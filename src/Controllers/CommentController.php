<?php
/**
 * Comment Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Services\IssueService;
use App\Services\NotificationService;

class CommentController extends Controller
{
    private IssueService $issueService;

    public function __construct()
    {
        $this->issueService = new IssueService();
    }

    public function store(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $this->authorize('issues.comment', $issue['project_id']);

        $data = $request->validate([
            'body' => 'required|max:50000',
        ]);

        try {
            $userId = $this->userId();
            if (!$userId) {
                throw new \Exception('User is not authenticated');
            }

            $commentId = Database::insert('comments', [
                'issue_id' => $issue['id'],
                'user_id' => $userId,
                'body' => $data['body'],
            ]);

            if (!$commentId) {
                throw new \Exception('Failed to insert comment - no ID returned');
            }

            // Retrieve the inserted comment with author info using raw PDO
            try {
                $pdo = Database::getConnection();
                $sql = "SELECT c.id, c.issue_id, c.user_id, c.body, c.created_at, c.updated_at,
                               u.id as author_id, u.display_name as author_name, u.avatar as author_avatar
                        FROM comments c
                        INNER JOIN users u ON c.user_id = u.id
                        WHERE c.id = " . (int)$commentId;
                
                $stmt = $pdo->query($sql);
                $comment = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$comment) {
                    throw new \Exception('Failed to retrieve inserted comment');
                }
            } catch (\Exception $e) {
                // Log but don't fail - comment was inserted, just can't retrieve it
                error_log('Comment retrieval failed: ' . $e->getMessage());
                $comment = [
                    'id' => $commentId,
                    'issue_id' => $issue['id'],
                    'user_id' => $userId,
                    'body' => $data['body'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            Database::update('issues', [
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$issue['id']]);

            // Dispatch notification for comment
            NotificationService::dispatchIssueCommented($issue['id'], $userId, (int) $commentId);
            
            $this->notifyWatchers($issue, 'comment_added', $comment);

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'comment' => $comment], 201);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}#comment-{$commentId}"),
                'success',
                'Comment added successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 500);
            }

            Session::flash('error', 'Failed to add comment: ' . $e->getMessage());
            Session::flash('_old_input', $data);
            $this->redirect(url("/issue/{$issueKey}"));
        }
    }

    public function update(Request $request): void
    {
        $commentId = (int) $request->param('id');

        $comment = Database::selectOne(
            "SELECT c.*, i.issue_key, i.project_id
             FROM comments c
             JOIN issues i ON c.issue_id = i.id
             WHERE c.id = ?",
            [$commentId]
        );

        if (!$comment) {
            abort(404, 'Comment not found');
        }

        if ($comment['user_id'] !== $this->userId()) {
            $this->authorize('comments.edit_all', $comment['project_id']);
        }

        $data = $request->validate([
            'body' => 'required|max:50000',
        ]);

        try {
            Database::update('comments', [
                'body' => $data['body'],
            ], 'id = ?', [$commentId]);

            $updated = Database::selectOne(
                "SELECT c.*, u.display_name as author_name, u.avatar as author_avatar
                 FROM comments c
                 JOIN users u ON c.user_id = u.id
                 WHERE c.id = ?",
                [$commentId]
            );

            if ($request->wantsJson() || $request->isAjax()) {
                $this->json(['success' => true, 'comment' => $updated]);
            }

            $this->redirectWith(
                url("/issue/{$comment['issue_key']}#comment-{$commentId}"),
                'success',
                'Comment updated successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to update comment.'], 500);
            }

            $this->redirectWith(
                url("/issue/{$comment['issue_key']}"),
                'error',
                'Failed to update comment.'
            );
        }
    }

    public function destroy(Request $request): void
    {
        $commentId = (int) $request->param('id');

        $comment = Database::selectOne(
            "SELECT c.*, i.issue_key, i.project_id
             FROM comments c
             JOIN issues i ON c.issue_id = i.id
             WHERE c.id = ?",
            [$commentId]
        );

        if (!$comment) {
            abort(404, 'Comment not found');
        }

        if ($comment['user_id'] !== $this->userId()) {
            $this->authorize('comments.delete_all', $comment['project_id']);
        }

        try {
            Database::delete('comments', 'id = ?', [$commentId]);

            if ($request->wantsJson() || $request->isAjax()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/issue/{$comment['issue_key']}"),
                'success',
                'Comment deleted successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to delete comment.'], 500);
            }

            $this->redirectWith(
                url("/issue/{$comment['issue_key']}"),
                'error',
                'Failed to delete comment.'
            );
        }
    }

    private function notifyWatchers(array $issue, string $type, array $comment): void
    {
        try {
            $watchers = Database::select(
                "SELECT user_id FROM issue_watchers WHERE issue_id = :issue_id AND user_id != :user_id",
                ['issue_id' => $issue['id'], 'user_id' => $this->userId()]
            );

            foreach ($watchers as $watcher) {
                Database::insert('notifications', [
                    'user_id' => $watcher['user_id'],
                    'type' => $type,
                    'notifiable_type' => 'issue',
                    'notifiable_id' => $issue['id'],
                    'data' => json_encode([
                        'issue_key' => $issue['issue_key'],
                        'comment_id' => $comment['id'],
                        'actor_id' => $this->userId(),
                    ]),
                ]);
            }
        } catch (\Exception $e) {
            // Log notification error but don't fail the comment creation
            error_log('Failed to notify watchers: ' . $e->getMessage());
        }
    }
}
