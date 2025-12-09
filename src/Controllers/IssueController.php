<?php
/**
 * Issue Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\IssueService;
use App\Services\ProjectService;
use App\Services\NotificationService;

class IssueController extends Controller
{
    private IssueService $issueService;
    private ProjectService $projectService;

    public function __construct()
    {
        $this->issueService = new IssueService();
        $this->projectService = new ProjectService();
    }

    public function index(Request $request): string
    {
        // Get project key from route parameter
        $projectKey = $request->param('key');
        $project = null;
        
        if ($projectKey) {
            $project = $this->projectService->getProjectByKey($projectKey);
            if (!$project) {
                abort(404, 'Project not found');
            }
            // Filter by this project
        }
        
        $filters = [
            'project_id' => $project['id'] ?? $request->input('project_id'),
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

        // Get issue types, statuses, and priorities for filters
        $issueTypes = $this->issueService->getIssueTypes();
        $statuses = $this->issueService->getStatuses();
        $priorities = $this->issueService->getPriorities();
        
        // Get project members if project is selected
        $projectMembers = [];
        if ($project) {
            $projectMembers = $this->projectService->getProjectMembers($project['id']);
        }

        if ($request->wantsJson()) {
            // Sanitize issues to remove PII before returning JSON
            $issues['data'] = sanitize_issues_for_json($issues['data']);
            $this->json($issues);
        }

        return $this->view('issues.index', [
            'issues' => $issues,
            'filters' => $filters,
            'project' => $project,
            'issueTypes' => $issueTypes,
            'statuses' => $statuses,
            'priorities' => $priorities,
            'projectMembers' => $projectMembers,
        ]);
    }

    public function create(Request $request): string
    {
        $projectKey = $request->param('key') ?? $request->input('project');
        $project = null;

        if ($projectKey) {
            $project = $this->projectService->getProjectByKey($projectKey);
            if ($project) {
                $this->authorize('issues.create', $project['id']);
                // Load full project details including related data
                $project = $this->issueService->getProjectWithDetails($project['id']);
            }
        }

        return $this->view('issues.create', [
            'project' => $project,
            'projects' => $this->issueService->getProjects(),
            'issueTypes' => $this->issueService->getIssueTypes(),
            'priorities' => $this->issueService->getPriorities(),
        ]);
    }

    public function store(Request $request): void
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

        // Convert empty strings to null for optional foreign key fields
        foreach (['assignee_id', 'epic_id', 'parent_id', 'sprint_id'] as $field) {
            if (isset($data[$field]) && ($data[$field] === '' || $data[$field] === 0)) {
                $data[$field] = null;
            }
        }

        $this->authorize('issues.create', (int) $data['project_id']);

        try {
            $issue = $this->issueService->createIssue($data, $this->userId());

            // Dispatch notification for issue creation
            NotificationService::dispatchIssueCreated($issue['id'], $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'issue' => sanitize_issue_for_json($issue)], 201);
            }

            $this->redirectWith(
                url("/issue/{$issue['issue_key']}"),
                'success',
                "Issue {$issue['issue_key']} created successfully."
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            Session::flash('_old_input', $data);
            $this->back();
        }
    }

    public function show(Request $request): string
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $transitions = $this->issueService->getAvailableTransitions($issue['id']);
        $links = $this->issueService->getIssueLinks($issue['id']);
        $history = $this->issueService->getIssueHistory($issue['id']);
        
        $isWatching = $this->issueService->isWatching($issue['id'], $this->userId());
        $hasVoted = $this->issueService->hasVoted($issue['id'], $this->userId());

        if ($request->wantsJson()) {
            $this->json([
                'issue' => sanitize_issue_for_json($issue),
                'transitions' => $transitions,
                'links' => $links,
                'history' => $history,
                'is_watching' => $isWatching,
                'has_voted' => $hasVoted,
            ]);
        }

        return $this->view('issues.show', [
            'issue' => $issue,
            'transitions' => $transitions,
            'links' => $links,
            'history' => $history,
            'isWatching' => $isWatching,
            'hasVoted' => $hasVoted,
        ]);
    }

    public function edit(Request $request): string
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $this->authorize('issues.edit', $issue['project_id']);

        $project = $this->issueService->getProjectWithDetails($issue['project_id']);
        
        return $this->view('issues.edit', [
            'issue' => $issue,
            'project' => $project,
            'issueTypes' => $this->issueService->getIssueTypes(),
            'priorities' => $this->issueService->getPriorities(),
            'projectMembers' => $this->projectService->getProjectMembers($issue['project_id']),
            'labels' => $project['labels'] ?? [],
        ]);
    }

    public function update(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $this->authorize('issues.edit', $issue['project_id']);

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

        // Convert empty strings to null for optional foreign key fields
        foreach (['assignee_id', 'epic_id'] as $field) {
            if (isset($data[$field]) && ($data[$field] === '' || $data[$field] === 0)) {
                $data[$field] = null;
            }
        }

        try {
            $updated = $this->issueService->updateIssue($issue['id'], $data, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'issue' => $updated]);
            }

            $this->redirectWith(
                url("/issue/{$updated['issue_key']}"),
                'success',
                'Issue updated successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->redirect(url("/issue/{$issueKey}/edit"));
        }
    }

    public function destroy(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $this->authorize('issues.delete', $issue['project_id']);

        try {
            $this->issueService->deleteIssue($issue['id'], $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/projects/{$issue['project_key']}"),
                'success',
                'Issue deleted successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 500);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'error',
                'Failed to delete issue.'
            );
        }
    }

    public function transition(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $this->authorize('issues.transition', $issue['project_id']);

        $data = $request->validate([
            'status_id' => 'required|integer',
        ]);

        try {
            $updated = $this->issueService->transitionIssue(
                $issue['id'],
                (int) $data['status_id'],
                $this->userId()
            );

            // Dispatch notification for status change
            $newStatus = $updated['status'] ?? 'Unknown';
            NotificationService::dispatchStatusChanged(
                $issue['id'],
                $newStatus,
                $this->userId()
            );

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'issue' => $updated]);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'success',
                'Issue transitioned successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function assign(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $this->authorize('issues.assign', $issue['project_id']);

        $data = $request->validate([
            'assignee_id' => 'nullable|integer',
        ]);

        try {
            $previousAssigneeId = $issue['assignee_id'] ?? null;
            $newAssigneeId = $data['assignee_id'] ? (int) $data['assignee_id'] : null;

            $updated = $this->issueService->assignIssue(
                $issue['id'],
                $newAssigneeId,
                $this->userId()
            );

            // Dispatch notification for issue assignment
            if ($newAssigneeId) {
                NotificationService::dispatchIssueAssigned(
                    $issue['id'],
                    $newAssigneeId,
                    $previousAssigneeId
                );
            }

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'issue' => $updated]);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'success',
                'Issue assigned successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function watch(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $action = $request->input('action', 'watch');

        if ($action === 'unwatch') {
            $this->issueService->unwatchIssue($issue['id'], $this->userId());
            $message = 'You are no longer watching this issue.';
        } else {
            $this->issueService->watchIssue($issue['id'], $this->userId());
            $message = 'You are now watching this issue.';
        }

        if ($request->wantsJson()) {
            $this->json([
                'success' => true,
                'watching' => $action !== 'unwatch',
            ]);
        }

        $this->redirectWith(url("/issue/{$issueKey}"), 'success', $message);
    }

    public function vote(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        if ($issue['reporter_id'] === $this->userId()) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'You cannot vote for your own issue'], 422);
            }
            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'error',
                'You cannot vote for your own issue.'
            );
        }

        $action = $request->input('action', 'vote');

        if ($action === 'unvote') {
            $this->issueService->unvoteIssue($issue['id'], $this->userId());
            $message = 'Your vote has been removed.';
        } else {
            $this->issueService->voteIssue($issue['id'], $this->userId());
            $message = 'Your vote has been added.';
        }

        if ($request->wantsJson()) {
            $this->json([
                'success' => true,
                'voted' => $action !== 'unvote',
            ]);
        }

        $this->redirectWith(url("/issue/{$issueKey}"), 'success', $message);
    }

    public function link(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $this->authorize('issues.link', $issue['project_id']);

        $data = $request->validate([
            'target_issue_key' => 'required|string',
            'link_type_id' => 'required|integer',
        ]);

        $targetIssue = $this->issueService->getIssueByKey($data['target_issue_key']);
        if (!$targetIssue) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Target issue not found'], 404);
            }
            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'error',
                'Target issue not found.'
            );
        }

        try {
            $links = $this->issueService->linkIssues(
                $issue['id'],
                $targetIssue['id'],
                (int) $data['link_type_id'],
                $this->userId()
            );

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'links' => $links]);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'success',
                'Issues linked successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function unlink(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $linkId = (int) $request->param('linkId');

        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $this->authorize('issues.link', $issue['project_id']);

        try {
            $this->issueService->unlinkIssues($linkId, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'success',
                'Link removed successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function logWork(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $this->authorize('issues.log_work', $issue['project_id']);

        $data = $request->validate([
            'time_spent' => 'required|integer|min:1',
            'started_at' => 'required|date',
            'description' => 'nullable|max:5000',
        ]);

        try {
            $updated = $this->issueService->logWork(
                $issue['id'],
                $this->userId(),
                (int) $data['time_spent'],
                $data['started_at'],
                $data['description'] ?? null
            );

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'issue' => $updated]);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'success',
                'Work logged successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/issue/{$issueKey}"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function history(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $history = $this->issueService->getIssueHistory($issue['id']);
        $this->json($history);
    }

    public function transitions(Request $request): void
    {
        $issueKey = $request->param('issueKey');
        $issue = $this->issueService->getIssueByKey($issueKey);

        if (!$issue) {
            abort(404, 'Issue not found');
        }

        $transitions = $this->issueService->getAvailableTransitions($issue['id']);
        $this->json($transitions);
    }
}
