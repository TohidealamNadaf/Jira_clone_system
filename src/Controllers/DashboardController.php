<?php
/**
 * Dashboard Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index(Request $request): string
    {
        $userId = $this->userId();

        // Get assigned issues (expanded limit for filtering)
        $assignedIssues = Database::select(
            "SELECT i.*, p.key as project_key, p.name as project_name,
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                    s.name as status_name, s.color as status_color, s.category as status_category,
                    pr.name as priority_name, pr.color as priority_color
             FROM issues i
             JOIN projects p ON i.project_id = p.id
             JOIN issue_types it ON i.issue_type_id = it.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_priorities pr ON i.priority_id = pr.id
             WHERE i.assignee_id = ? AND s.category != 'done'
             ORDER BY pr.sort_order ASC, i.updated_at DESC
             LIMIT 15",
            [$userId]
        );

        // Get reported issues
        $reportedIssues = Database::select(
            "SELECT i.*, p.key as project_key, p.name as project_name,
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                    s.name as status_name, s.color as status_color,
                    u.display_name as assignee_name
             FROM issues i
             JOIN projects p ON i.project_id = p.id
             JOIN issue_types it ON i.issue_type_id = it.id
             JOIN statuses s ON i.status_id = s.id
             LEFT JOIN users u ON i.assignee_id = u.id
             WHERE i.reporter_id = ? AND s.category != 'done'
             ORDER BY i.updated_at DESC
             LIMIT 10",
            [$userId]
        );

        // Get watched issues
        $watchedIssues = Database::select(
            "SELECT i.*, p.key as project_key, p.name as project_name,
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                    s.name as status_name, s.color as status_color, s.category as status_category,
                    pr.name as priority_name, pr.color as priority_color,
                    u.display_name as assignee_name
             FROM issue_watchers w
             JOIN issues i ON w.issue_id = i.id
             JOIN projects p ON i.project_id = p.id
             JOIN issue_types it ON i.issue_type_id = it.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_priorities pr ON i.priority_id = pr.id
             LEFT JOIN users u ON i.assignee_id = u.id
             WHERE w.user_id = ? AND s.category != 'done'
             ORDER BY i.updated_at DESC
             LIMIT 10",
            [$userId]
        );

        // Get recent activity with more details
        $recentActivity = Database::select(
            "SELECT ih.*, i.issue_key, i.summary, u.display_name as user_name, u.avatar as user_avatar
             FROM issue_history ih
             JOIN issues i ON ih.issue_id = i.id
             LEFT JOIN users u ON ih.user_id = u.id
             WHERE i.assignee_id = ? OR i.reporter_id = ? OR i.id IN (
                 SELECT issue_id FROM issue_watchers WHERE user_id = ?
             )
             ORDER BY ih.created_at DESC
             LIMIT 20",
            [$userId, $userId, $userId]
        );

        // Get user's projects
        $projects = Database::select(
            "SELECT p.*, 
                    (SELECT COUNT(*) FROM issues WHERE project_id = p.id AND status_id IN 
                        (SELECT id FROM statuses WHERE category != 'done')) as open_issues,
                    (SELECT COUNT(*) FROM issues WHERE project_id = p.id AND status_id IN 
                        (SELECT id FROM statuses WHERE category = 'done')) as done_issues
             FROM projects p
             WHERE p.id IN (
                 SELECT project_id FROM project_members WHERE user_id = ?
             ) AND p.is_archived = 0
             ORDER BY p.name ASC
             LIMIT 10",
            [$userId]
        );

        // Get statistics
        $stats = [
            'assigned_count' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.assignee_id = ? AND s.category != 'done'",
                [$userId]
            ),
            'reported_count' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.reporter_id = ? AND s.category != 'done'",
                [$userId]
            ),
            'due_soon' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.assignee_id = ? AND i.due_date IS NOT NULL 
                 AND i.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                 AND s.category != 'done'",
                [$userId]
            ),
            'overdue' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.assignee_id = ? AND i.due_date < CURDATE() AND s.category != 'done'",
                [$userId]
            ),
            'in_progress' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.assignee_id = ? AND s.category = 'in_progress'",
                [$userId]
            ),
            'completed_this_week' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.assignee_id = ? AND s.category = 'done'
                 AND i.updated_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)",
                [$userId]
            ),
        ];

        // Workload by project
        $workloadByProject = Database::select(
            "SELECT p.id, p.key, p.name, COUNT(*) as issue_count
             FROM issues i
             JOIN projects p ON i.project_id = p.id
             JOIN statuses s ON i.status_id = s.id
             WHERE i.assignee_id = ? AND s.category != 'done'
             GROUP BY p.id, p.key, p.name
             ORDER BY issue_count DESC
             LIMIT 6",
            [$userId]
        );

        // Status distribution for assigned issues
        $statusDistribution = Database::select(
            "SELECT s.id, s.name, s.color, s.category, COUNT(*) as issue_count
             FROM issues i
             JOIN statuses s ON i.status_id = s.id
             WHERE i.assignee_id = ?
             GROUP BY s.id, s.name, s.color, s.category
             ORDER BY s.sort_order ASC",
            [$userId]
        );

        // Priority distribution
        $priorityDistribution = Database::select(
            "SELECT pr.id, pr.name, pr.color, COUNT(*) as issue_count
             FROM issues i
             JOIN issue_priorities pr ON i.priority_id = pr.id
             JOIN statuses s ON i.status_id = s.id
             WHERE i.assignee_id = ? AND s.category != 'done'
             GROUP BY pr.id, pr.name, pr.color
             ORDER BY pr.sort_order ASC",
            [$userId]
        );

        // Get active sprints for user's projects with burndown data
        $activeSprints = Database::select(
            "SELECT sp.*, b.name as board_name, p.name as project_name, p.key as project_key,
                    (SELECT COUNT(*) FROM issues WHERE sprint_id = sp.id) as issue_count,
                    (SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id 
                     WHERE i.sprint_id = sp.id AND s.category = 'done') as done_count,
                    (SELECT SUM(COALESCE(story_points, 0)) FROM issues WHERE sprint_id = sp.id) as total_points,
                    (SELECT SUM(COALESCE(story_points, 0)) FROM issues i JOIN statuses s ON i.status_id = s.id 
                     WHERE i.sprint_id = sp.id AND s.category = 'done') as done_points
             FROM sprints sp
             JOIN boards b ON sp.board_id = b.id
             JOIN projects p ON b.project_id = p.id
             WHERE sp.status = 'active' AND p.id IN (
                 SELECT project_id FROM project_members WHERE user_id = ?
             )
             ORDER BY sp.end_date ASC
             LIMIT 5",
            [$userId]
        );

        // Calculate sprint health metrics
        foreach ($activeSprints as &$sprint) {
            $start = new \DateTime($sprint['start_date'] ?? 'now');
            $end = new \DateTime($sprint['end_date'] ?? 'now');
            $now = new \DateTime();

            $daysTotal = max(1, (int)$start->diff($end)->format('%a'));
            $daysElapsed = min($daysTotal, max(0, (int)$start->diff(min($now, $end))->format('%a')));

            $sprint['days_total'] = $daysTotal;
            $sprint['days_elapsed'] = $daysElapsed;
            $sprint['days_remaining'] = max(0, $daysTotal - $daysElapsed);

            $sprint['progress_percent'] = $sprint['issue_count'] > 0
                ? round(($sprint['done_count'] / $sprint['issue_count']) * 100)
                : 0;

            $sprint['ideal_percent'] = round(($daysElapsed / $daysTotal) * 100);

            // Determine health status
            if ($sprint['progress_percent'] >= $sprint['ideal_percent'] - 5) {
                $sprint['health'] = 'on-track';
                $sprint['health_label'] = 'On track';
            } elseif ($sprint['progress_percent'] >= $sprint['ideal_percent'] - 20) {
                $sprint['health'] = 'at-risk';
                $sprint['health_label'] = 'At risk';
            } else {
                $sprint['health'] = 'behind';
                $sprint['health_label'] = 'Behind';
            }
        }
        unset($sprint);

        // Get notifications
        $notifications = Database::select(
            "SELECT * FROM notifications
             WHERE user_id = ? AND read_at IS NULL
             ORDER BY created_at DESC
             LIMIT 10",
            [$userId]
        );

        // Recent comments on watched/assigned issues
        $recentComments = Database::select(
            "SELECT c.*, i.issue_key, i.summary, u.display_name as author_name, u.avatar as author_avatar
             FROM comments c
             JOIN issues i ON c.issue_id = i.id
             JOIN users u ON c.user_id = u.id
             WHERE (i.assignee_id = ? OR i.reporter_id = ? OR i.id IN (
                 SELECT issue_id FROM issue_watchers WHERE user_id = ?
             ))
             ORDER BY c.created_at DESC
             LIMIT 5",
            [$userId, $userId, $userId]
        );

        // Get ALL projects for quick create modal
        $allProjectsForModal = Database::select(
            "SELECT p.* FROM projects p ORDER BY p.name ASC"
        );

        // Get saved filters
        $savedFilters = Database::select(
            "SELECT * FROM saved_filters
             WHERE user_id = ? OR share_type = 'global'
             ORDER BY name ASC
             LIMIT 10",
            [$userId]
        );

        // Get current user info
        $currentUser = Database::selectOne(
            "SELECT id, email, display_name as name, avatar FROM users WHERE id = ?",
            [$userId]
        );

        return $this->view('dashboard.index', [
            'currentUser' => $currentUser ?? ['name' => 'User', 'avatar' => null],
            'assignedIssues' => $assignedIssues,
            'reportedIssues' => $reportedIssues,
            'watchedIssues' => $watchedIssues,
            'recentActivity' => $recentActivity,
            'recentComments' => $recentComments,
            'projects' => $projects,
            'allProjectsForModal' => $allProjectsForModal,
            'stats' => $stats,
            'workloadByProject' => $workloadByProject,
            'statusDistribution' => $statusDistribution,
            'priorityDistribution' => $priorityDistribution,
            'activeSprints' => $activeSprints,
            'notifications' => $notifications,
            'savedFilters' => $savedFilters,
        ]);
    }

    /**
     * Show API documentation
     */
    public function apiDocs(Request $request): string
    {
        return $this->view('api.docs');
    }
}
