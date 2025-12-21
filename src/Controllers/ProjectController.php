<?php
/**
 * Project Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Services\ProjectService;
use App\Services\ActivityService;

class ProjectController extends Controller
{
    private ProjectService $projectService;
    private ActivityService $activityService;

    public function __construct()
    {
        $this->projectService = new ProjectService();
        $this->activityService = new ActivityService();
    }

    public function index(Request $request): string
    {
        $filters = [
            'search' => $request->input('search'),
            'category' => $request->input('category'),
            'status' => $request->input('status'),
        ];

        $page = (int) ($request->input('page') ?? 1);
        $perPage = (int) ($request->input('per_page') ?? 25);
        $projects = $this->projectService->getAllProjects(array_filter($filters), $page, $perPage);

        // Get categories for filter dropdown
        $categories = Database::select("SELECT * FROM project_categories ORDER BY name ASC");

        if ($request->wantsJson()) {
            $this->json($projects);
        }

        return $this->view('projects.index', [
            'projects' => $projects,
            'filters' => $filters,
            'categories' => $categories,
        ]);
    }



    public function create(Request $request): string
    {
        $this->authorize('projects.create');

        $categories = Database::select("SELECT * FROM project_categories ORDER BY name ASC");
        $users = Database::select("SELECT id, display_name FROM users WHERE is_active = 1 ORDER BY display_name ASC");

        return $this->view('projects.create', [
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    public function store(Request $request): void
    {
        $this->authorize('projects.create');

        $data = $request->validate([
            'key' => 'required|alpha_num|min:2|max:10',
            'name' => 'required|max:255',
            'description' => 'nullable|max:5000',
            'lead_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'default_assignee' => 'nullable|in:project_lead,unassigned',
        ]);

        try {
            $project = $this->projectService->createProject($data, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'project' => $project], 201);
            }

            $this->redirectWith(
                url("/projects/{$project['key']}"),
                'success',
                'Project created successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            Session::flash('_old_input', $data);
            $this->redirect(url('/projects/create'));
        }
    }

    public function show(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }



        if ($request->wantsJson()) {
            $this->json($project);
        }

        // Get project statistics
        $stats = [
            'total_issues' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues WHERE project_id = ?",
                [$project['id']]
            ),
            'open_issues' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i 
                 JOIN statuses s ON i.status_id = s.id 
                 WHERE i.project_id = ? AND s.category = 'todo'",
                [$project['id']]
            ),
            'in_progress' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i 
                 JOIN statuses s ON i.status_id = s.id 
                 WHERE i.project_id = ? AND s.category = 'in_progress'",
                [$project['id']]
            ),
            'done_issues' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i 
                 JOIN statuses s ON i.status_id = s.id 
                 WHERE i.project_id = ? AND s.category = 'done'",
                [$project['id']]
            ),
        ];

        // Get recent issues
        $recentIssues = Database::select(
            "SELECT i.*, 
                    it.name as type_name, it.icon as type_icon, it.color as type_color,
                    s.name as status_name, s.color as status_color,
                    a.display_name as assignee_name, a.avatar as assignee_avatar
             FROM issues i
             LEFT JOIN issue_types it ON i.issue_type_id = it.id
             LEFT JOIN statuses s ON i.status_id = s.id
             LEFT JOIN users a ON i.assignee_id = a.id
             WHERE i.project_id = ?
             ORDER BY i.updated_at DESC
             LIMIT 10",
            [$project['id']]
        );

        // Transform recent issues for view
        $recentIssues = array_map(function ($issue) {
            return [
                'issue_key' => $issue['issue_key'],
                'summary' => $issue['summary'],
                'updated_at' => $issue['updated_at'],
                'type' => [
                    'name' => $issue['type_name'] ?? 'Task',
                    'icon' => $issue['type_icon'] ?? 'circle',
                    'color' => $issue['type_color'] ?? 'secondary'
                ],
                'status' => [
                    'name' => $issue['status_name'] ?? 'Open',
                    'color' => $issue['status_color'] ?? 'secondary'
                ],
                'assignee' => $issue['assignee_name'] ? [
                    'display_name' => $issue['assignee_name'],
                    'avatar' => $issue['assignee_avatar']
                ] : null
            ];
        }, $recentIssues);

        // Get project members
        $members = $this->projectService->getProjectMembers($project['id']);

        // Get recent activity
        $activities = $this->activityService->getProjectActivities($project['id'], 10);

        return $this->view('projects.show', [
            'project' => $project,
            'stats' => $stats,
            'recentIssues' => $recentIssues,
            'members' => $members,
            'activities' => $activities,
        ]);
    }

    public function activity(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        // Get all activities for this project
        $activities = $this->activityService->getProjectActivities($project['id'], 100);

        return $this->view('projects.activity', [
            'project' => $project,
            'activities' => $activities,
        ]);
    }

    public function backlog(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        // Get all issues not in any sprint (backlog items)
        $backlogIssues = Database::select(
            "SELECT i.*, 
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                    s.name as status_name, s.color as status_color,
                    ip.name as priority_name, ip.color as priority_color,
                    a.display_name as assignee_name, a.avatar as assignee_avatar
             FROM issues i
             LEFT JOIN issue_types it ON i.issue_type_id = it.id
             LEFT JOIN statuses s ON i.status_id = s.id
             LEFT JOIN issue_priorities ip ON i.priority_id = ip.id
             LEFT JOIN users a ON i.assignee_id = a.id
             WHERE i.project_id = ? AND (i.sprint_id IS NULL OR i.sprint_id = 0)
             ORDER BY i.issue_number ASC",
            [$project['id']]
        );

        return $this->view('projects.backlog', [
            'project' => $project,
            'backlogIssues' => $backlogIssues,
        ]);
    }

    public function sprints(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        // Get all sprints for this project (through boards)
        $sprints = Database::select(
            "SELECT s.* FROM sprints s 
             INNER JOIN boards b ON s.board_id = b.id 
             WHERE b.project_id = ? 
             ORDER BY s.status, s.start_date DESC",
            [$project['id']]
        );

        return $this->view('projects.sprints', [
            'project' => $project,
            'sprints' => $sprints,
        ]);
    }

    public function board(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        // Get statuses
        $statuses = Database::select(
            "SELECT * FROM statuses ORDER BY sort_order ASC"
        );

        // Get issues grouped by status
        $issues = Database::select(
            "SELECT i.*, 
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                    s.name as status_name, s.id as status_id, s.color as status_color,
                    ip.name as priority_name, ip.color as priority_color,
                    a.display_name as assignee_name, a.avatar as assignee_avatar
             FROM issues i
             LEFT JOIN issue_types it ON i.issue_type_id = it.id
             LEFT JOIN statuses s ON i.status_id = s.id
             LEFT JOIN issue_priorities ip ON i.priority_id = ip.id
             LEFT JOIN users a ON i.assignee_id = a.id
             WHERE i.project_id = ?
             ORDER BY s.sort_order ASC, i.issue_number ASC",
            [$project['id']]
        );

        return $this->view('projects.board', [
            'project' => $project,
            'statuses' => $statuses,
            'issues' => $issues,
        ]);
    }

    public function reports(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        // Basic stats for reports
        $stats = [
            'total_issues' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues WHERE project_id = ?",
                [$project['id']]
            ),
            'resolved_issues' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i 
                 JOIN statuses s ON i.status_id = s.id 
                 WHERE i.project_id = ? AND s.category = 'done'",
                [$project['id']]
            ),
            'open_issues' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i 
                 JOIN statuses s ON i.status_id = s.id 
                 WHERE i.project_id = ? AND s.category = 'todo'",
                [$project['id']]
            ),
        ];

        return $this->view('projects.reports', [
            'project' => $project,
            'stats' => $stats,
        ]);
    }

    public function edit(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.edit', $project['id']);

        return $this->view('projects.edit', [
            'project' => $project,
        ]);
    }

    public function update(Request $request): void
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.edit', $project['id']);

        $data = $request->validate([
            'name' => 'nullable|max:255',
            'key' => 'nullable|alpha_num|min:2|max:10',
            'description' => 'nullable|max:5000',
            'lead_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'default_assignee' => 'nullable|in:project_lead,unassigned',
            'is_archived' => 'nullable|boolean',
        ]);

        try {
            $updated = $this->projectService->updateProject($project['id'], $data, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'project' => $updated]);
            }

            $this->redirectWith(
                url("/projects/{$updated['key']}"),
                'success',
                'Project updated successfully.'
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->redirect(url("/projects/{$key}/edit"));
        }
    }

    public function destroy(Request $request): void
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.delete', $project['id']);

        try {
            $this->projectService->deleteProject($project['id'], $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/projects'), 'success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 500);
            }

            $this->redirectWith(url("/projects/{$key}"), 'error', 'Failed to delete project.');
        }
    }

    public function settings(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.admin', $project['id']);

        $components = $this->projectService->getComponents($project['id']);
        $versions = $this->projectService->getVersions($project['id']);
        $categories = Database::select("SELECT * FROM project_categories ORDER BY name ASC");
        $users = Database::select("SELECT id, display_name FROM users WHERE is_active = 1 ORDER BY display_name ASC");

        return $this->view('projects.settings', [
            'project' => $project,
            'components' => $components,
            'versions' => $versions,
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    public function members(Request $request): string
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.view_members', $project['id']);

        $members = $this->projectService->getProjectMembers($project['id']);
        $availableUsers = $this->projectService->getAvailableUsers($project['id']);
        $availableRoles = $this->projectService->getAvailableRoles();

        if ($request->wantsJson()) {
            $this->json($members);
        }

        return $this->view('projects.members', [
            'project' => $project,
            'members' => $members,
            'availableUsers' => $availableUsers,
            'availableRoles' => $availableRoles,
        ]);
    }

    public function addMember(Request $request): void
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.manage_members', $project['id']);

        $data = $request->validate([
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        try {
            $this->projectService->addProjectMember(
                $project['id'],
                (int) $data['user_id'],
                (int) $data['role_id'],
                $this->userId()
            );

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/projects/{$key}/members"),
                'success',
                'Member added successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/projects/{$key}/members"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function updateMember(Request $request): void
    {
        $key = $request->param('key');
        $userId = (int) $request->param('userId');
        
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.manage_members', $project['id']);

        $data = $request->validate([
            'role_id' => 'required|integer',
        ]);

        try {
            $this->projectService->addProjectMember(
                $project['id'],
                $userId,
                (int) $data['role_id'],
                $this->userId()
            );

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/projects/{$key}/members"),
                'success',
                'Member role updated successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/projects/{$key}/members"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function removeMember(Request $request): void
    {
        $key = $request->param('key');
        $userId = (int) $request->param('userId');
        
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.manage_members', $project['id']);

        try {
            $this->projectService->removeProjectMember($project['id'], $userId, $this->userId());

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(
                url("/projects/{$key}/members"),
                'success',
                'Member removed successfully.'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(
                url("/projects/{$key}/members"),
                'error',
                $e->getMessage()
            );
        }
    }

    public function components(Request $request): void
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $components = $this->projectService->getComponents($project['id']);
        $this->json($components);
    }

    public function storeComponent(Request $request): void
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.admin', $project['id']);

        $data = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
            'lead_id' => 'nullable|integer',
            'default_assignee_id' => 'nullable|integer',
        ]);

        try {
            $component = $this->projectService->createComponent($project['id'], $data, $this->userId());
            $this->json(['success' => true, 'component' => $component], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function versions(Request $request): void
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $versions = $this->projectService->getVersions($project['id']);
        $this->json($versions);
    }

    public function storeVersion(Request $request): void
    {
        $key = $request->param('key');
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.admin', $project['id']);

        $data = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
            'start_date' => 'nullable|date',
            'release_date' => 'nullable|date',
        ]);

        try {
            $version = $this->projectService->createVersion($project['id'], $data, $this->userId());
            $this->json(['success' => true, 'version' => $version], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function releaseVersion(Request $request): void
    {
        $key = $request->param('key');
        $versionId = (int) $request->param('versionId');
        
        $project = $this->projectService->getProjectByKey($key);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $this->authorize('projects.admin', $project['id']);

        try {
            $version = $this->projectService->releaseVersion($versionId, $this->userId());
            $this->json(['success' => true, 'version' => $version]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * API endpoint for getting projects (for AJAX requests from web views)
     * Used by time tracking dashboard project selector and other UI components
     */
    public function apiProjects(): never
    {
        try {
            $user = Session::user();
            
            if (!$user || !isset($user['id'])) {
                $this->json(['error' => 'Unauthorized'], 401);
            }

            // Get all projects the user has access to
            $projects = Database::select(
                "SELECT p.id, p.`key`, p.name FROM projects p 
                 WHERE p.is_archived = 0 
                 ORDER BY p.name ASC"
            );

            // Return array format for easy parsing
            $this->json([
                'success' => true,
                'data' => $projects,
                'count' => count($projects)
            ], 200);
        } catch (\Exception $e) {
            error_log('[API-PROJECTS] Error: ' . $e->getMessage());
            $this->json([
                'error' => 'Failed to load projects',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get projects list for quick create modal
     */
    public function quickCreateList(Request $request): void
    {
        try {
            // Get projects user has access to
            $projects = $this->projectService->getUserProjects($this->userId());
            
            // Return JSON response
            $this->json($projects);
            
        } catch (\Exception $e) {
            error_log('[QUICK-CREATE-LIST] Error: ' . $e->getMessage());
            $this->json([
                'error' => 'Failed to load projects',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
