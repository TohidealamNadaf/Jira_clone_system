<?php
/**
 * Issue API Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;
use App\Services\IssueService;
use App\Services\ProjectService;

class IssueApiController extends Controller
{
    private IssueService $issueService;
    private ProjectService $projectService;

    public function __construct()
    {
        $this->issueService = new IssueService();
        $this->projectService = new ProjectService();
    }

    private function apiUser(): array
    {
        return $GLOBALS['api_user'] ?? [];
    }

    private function apiUserId(): int
    {
        return (int) ($this->apiUser()['id'] ?? 0);
    }

    public function index(Request $request): never
    {
        $filters = [
            'project_id' => $request->input('project_id'),
            'issue_type_id' => $request->input('issue_type_id'),
            'status_id' => $request->input('status_id'),
            'priority_id' => $request->input('priority_id'),
            'assignee_id' => $request->input('assignee_id'),
            'reporter_id' => $request->input('reporter_id'),
            'sprint_id' => $request->input('sprint_id'),
            'epic_id' => $request->input('epic_id'),
            'search' => $request->input('search'),
            'labels' => $request->input('labels') ? explode(',', $request->input('labels')) : null,
        ];

        $orderBy = $request->input('order_by', 'created_at');
        $order = $request->input('order', 'DESC');
        $page = (int) ($request->input('page') ?? 1);
        $perPage = (int) ($request->input('per_page') ?? 25);

        $issues = $this->issueService->getIssues(
            array_filter($filters, fn($v) => $v !== null && $v !== ''),
            $orderBy,
            $order,
            $page,
            $perPage
        );

        $this->json($issues);
    }

    public function store(Request $request): never
    {
        $data = $request->validate([
            'project_id' => 'required|integer',
            'issue_type_id' => 'required|integer',
            'summary' => 'required|max:500',
            'description' => 'nullable|max:50000',
            'priority_id' => 'nullable|integer',
            'assignee_id' => 'nullable|integer',
            'parent_id' => 'nullable|integer',
            'epic_id' => 'nullable|integer',
            'sprint_id' => 'nullable|integer',
            'story_points' => 'nullable|numeric|min:0|max:999',
            'original_estimate' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'labels' => 'nullable|array',
            'components' => 'nullable|array',
            'fix_versions' => 'nullable|array',
        ]);

        try {
            $issue = $this->issueService->createIssue($data, $this->apiUserId());
            $this->json(['success' => true, 'issue' => $issue], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $transitions = $this->issueService->getAvailableTransitions($issue['id']);
        $links = $this->issueService->getIssueLinks($issue['id']);
        $isWatching = $this->issueService->isWatching($issue['id'], $this->apiUserId());
        $hasVoted = $this->issueService->hasVoted($issue['id'], $this->apiUserId());

        $this->json([
            'issue' => $issue,
            'transitions' => $transitions,
            'links' => $links,
            'is_watching' => $isWatching,
            'has_voted' => $hasVoted,
        ]);
    }

    public function update(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $data = $request->validate([
            'summary' => 'nullable|max:500',
            'description' => 'nullable|max:50000',
            'issue_type_id' => 'nullable|integer',
            'priority_id' => 'nullable|integer',
            'assignee_id' => 'nullable|integer',
            'epic_id' => 'nullable|integer',
            'story_points' => 'nullable|numeric|min:0|max:999',
            'original_estimate' => 'nullable|integer|min:0',
            'remaining_estimate' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'environment' => 'nullable|max:10000',
            'labels' => 'nullable|array',
            'components' => 'nullable|array',
            'fix_versions' => 'nullable|array',
        ]);

        try {
            $updated = $this->issueService->updateIssue($issue['id'], $data, $this->apiUserId());
            $this->json(['success' => true, 'issue' => $updated]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        try {
            $this->issueService->deleteIssue($issue['id'], $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Issue deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function transition(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $data = $request->validate([
            'status_id' => 'required|integer',
        ]);

        try {
            $updated = $this->issueService->transitionIssue(
                $issue['id'],
                (int) $data['status_id'],
                $this->apiUserId()
            );
            $this->json(['success' => true, 'issue' => $updated]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function availableTransitions(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $transitions = $this->issueService->getAvailableTransitions($issue['id']);
        $this->json($transitions);
    }

    public function assign(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $data = $request->validate([
            'assignee_id' => 'nullable|integer',
        ]);

        try {
            $updated = $this->issueService->assignIssue(
                $issue['id'],
                $data['assignee_id'] ? (int) $data['assignee_id'] : null,
                $this->apiUserId()
            );
            $this->json(['success' => true, 'issue' => $updated]);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function watch(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $this->issueService->watchIssue($issue['id'], $this->apiUserId());
        $this->json(['success' => true, 'watching' => true]);
    }

    public function unwatch(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $this->issueService->unwatchIssue($issue['id'], $this->apiUserId());
        $this->json(['success' => true, 'watching' => false]);
    }

    public function vote(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        if ($issue['reporter_id'] === $this->apiUserId()) {
            $this->json(['error' => 'You cannot vote for your own issue'], 422);
        }

        $this->issueService->voteIssue($issue['id'], $this->apiUserId());
        $this->json(['success' => true, 'voted' => true]);
    }

    public function unvote(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $this->issueService->unvoteIssue($issue['id'], $this->apiUserId());
        $this->json(['success' => true, 'voted' => false]);
    }

    public function comments(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $comments = $this->issueService->getComments($issue['id']);
        $this->json($comments);
    }

    public function storeComment(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $data = $request->validate([
            'body' => 'required|max:50000',
        ]);

        try {
            $comment = $this->issueService->addComment($issue['id'], $data['body'], $this->apiUserId());
            $this->json(['success' => true, 'comment' => $comment], 201);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function updateComment(Request $request): never
    {
        $commentId = (int) $request->param('id');

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$comment) {
            $this->json(['error' => 'Comment not found'], 404);
        }

        if ($comment['user_id'] !== $this->apiUserId()) {
            $this->json(['error' => 'You can only edit your own comments'], 403);
        }

        $data = $request->validate([
            'body' => 'required|max:50000',
        ]);

        try {
            $updated = $this->issueService->updateComment($commentId, $data['body'], $this->apiUserId());
            $this->json(['success' => true, 'comment' => $updated]);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroyComment(Request $request): never
    {
        $commentId = (int) $request->param('id');

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$comment) {
            $this->json(['error' => 'Comment not found'], 404);
        }

        if ($comment['user_id'] !== $this->apiUserId()) {
            $this->json(['error' => 'You can only delete your own comments'], 403);
        }

        try {
            $this->issueService->deleteComment($commentId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Comment deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function attachments(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $attachments = $this->issueService->getAttachments($issue['id']);
        $this->json($attachments);
    }

    public function storeAttachment(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $file = $_FILES['file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'No file uploaded or upload error'], 400);
        }

        try {
            $attachment = $this->issueService->addAttachment($issue['id'], $file, $this->apiUserId());
            $this->json(['success' => true, 'attachment' => $attachment], 201);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroyAttachment(Request $request): never
    {
        $attachmentId = (int) $request->param('id');

        $attachment = Database::selectOne("SELECT * FROM issue_attachments WHERE id = ?", [$attachmentId]);
        if (!$attachment) {
            $this->json(['error' => 'Attachment not found'], 404);
        }

        try {
            $this->issueService->deleteAttachment($attachmentId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Attachment deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function worklogs(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $worklogs = $this->issueService->getWorklogs($issue['id']);
        $this->json($worklogs);
    }

    public function storeWorklog(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $data = $request->validate([
            'time_spent' => 'required|integer|min:1',
            'started_at' => 'required|date',
            'description' => 'nullable|max:5000',
        ]);

        try {
            $worklog = $this->issueService->logWork(
                $issue['id'],
                $this->apiUserId(),
                (int) $data['time_spent'],
                $data['started_at'],
                $data['description'] ?? null
            );
            $this->json(['success' => true, 'worklog' => $worklog], 201);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function updateWorklog(Request $request): never
    {
        $worklogId = (int) $request->param('id');

        $worklog = Database::selectOne("SELECT * FROM issue_worklogs WHERE id = ?", [$worklogId]);
        if (!$worklog) {
            $this->json(['error' => 'Worklog not found'], 404);
        }

        if ($worklog['user_id'] !== $this->apiUserId()) {
            $this->json(['error' => 'You can only edit your own worklogs'], 403);
        }

        $data = $request->validate([
            'time_spent' => 'nullable|integer|min:1',
            'started_at' => 'nullable|date',
            'description' => 'nullable|max:5000',
        ]);

        try {
            $updated = $this->issueService->updateWorklog($worklogId, $data, $this->apiUserId());
            $this->json(['success' => true, 'worklog' => $updated]);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroyWorklog(Request $request): never
    {
        $worklogId = (int) $request->param('id');

        $worklog = Database::selectOne("SELECT * FROM issue_worklogs WHERE id = ?", [$worklogId]);
        if (!$worklog) {
            $this->json(['error' => 'Worklog not found'], 404);
        }

        if ($worklog['user_id'] !== $this->apiUserId()) {
            $this->json(['error' => 'You can only delete your own worklogs'], 403);
        }

        try {
            $this->issueService->deleteWorklog($worklogId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Worklog deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function links(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $links = $this->issueService->getIssueLinks($issue['id']);
        $this->json($links);
    }

    public function storeLink(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $data = $request->validate([
            'target_issue_key' => 'required|string',
            'link_type_id' => 'required|integer',
        ]);

        $targetIssue = $this->issueService->getIssueByKey($data['target_issue_key']);
        if (!$targetIssue) {
            $this->json(['error' => 'Target issue not found'], 404);
        }

        try {
            $link = $this->issueService->linkIssues(
                $issue['id'],
                $targetIssue['id'],
                (int) $data['link_type_id'],
                $this->apiUserId()
            );
            $this->json(['success' => true, 'link' => $link], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroyLink(Request $request): never
    {
        $linkId = (int) $request->param('id');

        $link = Database::selectOne("SELECT * FROM issue_links WHERE id = ?", [$linkId]);
        if (!$link) {
            $this->json(['error' => 'Link not found'], 404);
        }

        try {
            $this->issueService->unlinkIssues($linkId, $this->apiUserId());
            $this->json(['success' => true, 'message' => 'Link removed successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function history(Request $request): never
    {
        $issueKey = $request->param('key');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            $this->json(['error' => 'Issue not found'], 404);
        }

        $history = $this->issueService->getIssueHistory($issue['id']);
        $this->json($history);
    }

    public function issueTypes(Request $request): never
    {
        $sql = "SELECT id, name, description, icon, color, is_subtask, is_default, sort_order 
                FROM issue_types 
                ORDER BY sort_order ASC, name ASC";

        $types = Database::select($sql);
        $this->json($types);
    }

    public function priorities(Request $request): never
    {
        $priorities = Database::select(
            "SELECT id, name, description, icon, color, sort_order, is_default FROM issue_priorities ORDER BY sort_order ASC"
        );
        $this->json($priorities);
    }

    public function statuses(Request $request): never
    {
        $sql = "SELECT id, name, description, category, color, sort_order 
                FROM statuses 
                ORDER BY sort_order ASC, name ASC";

        $statuses = Database::select($sql);
        $this->json($statuses);
    }

    public function labels(Request $request): never
    {
        $projectId = $request->input('project_id');
        $search = $request->input('search');
        
        $where = ['1 = 1'];
        $params = [];

        if ($projectId) {
            $where[] = "(project_id IS NULL OR project_id = ?)";
            $params[] = $projectId;
        }

        if ($search) {
            $where[] = "name LIKE ?";
            $params[] = "%$search%";
        }

        $whereClause = implode(' AND ', $where);
        $labels = Database::select(
            "SELECT * FROM labels WHERE $whereClause ORDER BY name ASC LIMIT 50",
            $params
        );
        $this->json($labels);
    }

    public function linkTypes(Request $request): never
    {
        $linkTypes = Database::select(
            "SELECT * FROM issue_link_types ORDER BY name ASC"
        );
        $this->json($linkTypes);
    }
}
