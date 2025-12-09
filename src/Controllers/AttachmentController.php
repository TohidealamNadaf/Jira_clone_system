<?php
/**
 * Attachment Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Services\IssueService;

class AttachmentController extends Controller
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

        $this->authorize('issues.attach', $issue['project_id']);

        $file = $request->file('file');

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'No file uploaded or upload error.'], 422);
            }
            $this->redirectWith(url("/issue/{$issueKey}"), 'error', 'No file uploaded.');
        }

        try {
            $uploaded = $this->uploadFile($file, 'attachments');

            if (!$uploaded) {
                throw new \Exception('Failed to upload file.');
            }

            $attachmentId = Database::insert('issue_attachments', [
                'issue_id' => $issue['id'],
                'author_id' => $this->userId(),
                'filename' => $uploaded['filename'],
                'original_name' => $uploaded['original_name'],
                'mime_type' => $uploaded['mime_type'],
                'file_size' => $uploaded['size'],
                'file_path' => $uploaded['path'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $attachment = Database::selectOne(
                "SELECT a.*, u.display_name as author_name
                 FROM issue_attachments a
                 JOIN users u ON a.author_id = u.id
                 WHERE a.id = ?",
                [$attachmentId]
            );

            Database::update('issues', [
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', ['id' => $issue['id']]);

            Database::insert('issue_history', [
                'issue_id' => $issue['id'],
                'user_id' => $this->userId(),
                'field' => 'attachment',
                'new_value' => $uploaded['original_name'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'attachment' => $attachment], 201);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'success',
                'File uploaded successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 500);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'error',
                'Failed to upload file.'
            );
        }
    }

    public function download(Request $request): void
    {
        $attachmentId = (int) $request->param('id');

        $attachment = Database::selectOne(
            "SELECT a.*, i.project_id, i.issue_key
             FROM issue_attachments a
             JOIN issues i ON a.issue_id = i.id
             WHERE a.id = ?",
            [$attachmentId]
        );

        if (!$attachment) {
            abort(404, 'Attachment not found');
        }

        $filePath = public_path($attachment['file_path']);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        header('Content-Type: ' . $attachment['mime_type']);
        header('Content-Disposition: attachment; filename="' . $attachment['original_name'] . '"');
        header('Content-Length: ' . $attachment['file_size']);
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        readfile($filePath);
        exit;
    }

    public function destroy(Request $request): void
    {
        $attachmentId = (int) $request->param('id');

        $attachment = Database::selectOne(
            "SELECT a.*, i.project_id, i.issue_key
             FROM issue_attachments a
             JOIN issues i ON a.issue_id = i.id
             WHERE a.id = ?",
            [$attachmentId]
        );

        if (!$attachment) {
            abort(404, 'Attachment not found');
        }

        if ($attachment['author_id'] !== $this->userId()) {
            $this->authorize('attachments.delete_all', $attachment['project_id']);
        }

        try {
            $this->deleteFile($attachment['file_path']);

            Database::delete('issue_attachments', 'id = :id', ['id' => $attachmentId]);

            Database::insert('issue_history', [
                'issue_id' => $attachment['issue_id'],
                'user_id' => $this->userId(),
                'field' => 'attachment',
                'old_value' => $attachment['original_name'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/issue/{$attachment['issue_key']}"),
                'success',
                'Attachment deleted successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to delete attachment.'], 500);
            }

            $this->redirectWith(
                url("/issue/{$attachment['issue_key']}"),
                'error',
                'Failed to delete attachment.'
            );
        }
    }
}
