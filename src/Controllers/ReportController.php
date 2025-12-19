<?php
/**
 * Report Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;

class ReportController extends Controller
{
    public function index(Request $request): string
    {
        $projectId = (int) $request->input('project_id', 0);
        
        $projects = Database::select(
            "SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name"
        );

        $boards = Database::select(
            "SELECT b.id, b.name, p.`key` as project_key
             FROM boards b
             JOIN projects p ON b.project_id = p.id
             WHERE p.is_archived = 0" . ($projectId ? " AND p.id = ?" : "") . "
             ORDER BY b.name",
            $projectId ? [$projectId] : []
        );

        $activeSprints = Database::select(
            "SELECT s.id, s.name, b.name as board_name, b.project_id
             FROM sprints s
             JOIN boards b ON s.board_id = b.id
             WHERE s.status = 'active'" . ($projectId ? " AND b.project_id = ?" : "") . "
             ORDER BY s.name",
            $projectId ? [$projectId] : []
        );

        // Calculate stats
        $statsQuery = "SELECT COUNT(*) FROM issues";
        $statsParams = [];
        
        if ($projectId) {
            $statsQuery .= " WHERE project_id = ?";
            $statsParams = [$projectId];
        }
        
        $totalIssues = (int) Database::selectValue($statsQuery, $statsParams);

        $completedStatuses = Database::select(
            "SELECT id FROM statuses WHERE category = 'done'"
        );
        $completedStatusIds = array_column($completedStatuses, 'id');
        
        $completedIssues = 0;
        if (!empty($completedStatusIds)) {
            $placeholders = implode(',', array_fill(0, count($completedStatusIds), '?'));
            $completedQuery = "SELECT COUNT(*) FROM issues WHERE status_id IN ($placeholders)";
            $completedIssueParams = $completedStatusIds;
            
            if ($projectId) {
                $completedQuery .= " AND project_id = ?";
                $completedIssueParams[] = $projectId;
            }
            
            $completedIssues = (int) Database::selectValue($completedQuery, $completedIssueParams);
        }

        $inProgressStatuses = Database::select(
            "SELECT id FROM statuses WHERE category = 'in_progress'"
        );
        $inProgressStatusIds = array_column($inProgressStatuses, 'id');
        
        $inProgressIssues = 0;
        if (!empty($inProgressStatusIds)) {
            $placeholders = implode(',', array_fill(0, count($inProgressStatusIds), '?'));
            $inProgressQuery = "SELECT COUNT(*) FROM issues WHERE status_id IN ($placeholders)";
            $inProgressParams = $inProgressStatusIds;
            
            if ($projectId) {
                $inProgressQuery .= " AND project_id = ?";
                $inProgressParams[] = $projectId;
            }
            
            $inProgressIssues = (int) Database::selectValue($inProgressQuery, $inProgressParams);
        }

        // Calculate average velocity from closed sprints
        $avgVelocity = 0;
        $closedSprintsQuery = "SELECT s.id FROM sprints s";
        $closedSprintsParams = [];
        
        if ($projectId) {
            $closedSprintsQuery .= " JOIN boards b ON s.board_id = b.id WHERE b.project_id = ? AND s.status = 'closed'";
            $closedSprintsParams = [$projectId];
        } else {
            $closedSprintsQuery .= " WHERE s.status = 'closed'";
        }
        
        $closedSprintsQuery .= " ORDER BY s.end_date DESC LIMIT 10";
        
        $closedSprints = Database::select($closedSprintsQuery, $closedSprintsParams);
        
        if (!empty($closedSprints)) {
            $totalVelocity = 0;
            foreach ($closedSprints as $sprint) {
                $completedPoints = (float) Database::selectValue(
                    "SELECT COALESCE(SUM(story_points), 0) FROM issues 
                     WHERE sprint_id = ? AND status_id IN (SELECT id FROM statuses WHERE category = 'done')",
                    [$sprint['id']]
                );
                $totalVelocity += $completedPoints;
            }
            $avgVelocity = round($totalVelocity / count($closedSprints), 1);
        }

        $stats = [
            'total_issues' => $totalIssues,
            'completed_issues' => $completedIssues,
            'in_progress' => $inProgressIssues,
            'avg_velocity' => $avgVelocity,
        ];

        if ($request->wantsJson()) {
            $this->json([
                'projects' => $projects,
                'boards' => $boards,
                'active_sprints' => $activeSprints,
                'stats' => $stats,
            ]);
        }

        return $this->view('reports.index', [
            'projects' => $projects,
            'boards' => $boards,
            'activeSprints' => $activeSprints,
            'stats' => $stats,
            'selectedProject' => $projectId,
        ]);
    }

    public function sprint(Request $request): string
    {
        $sprintId = (int) $request->input('sprintId', 0);

        $sprints = Database::select(
            "SELECT s.id, s.name, s.status, s.start_date, s.end_date, b.id as board_id, b.name as board_name, p.id as project_id, p.`key` as project_key
             FROM sprints s
             JOIN boards b ON s.board_id = b.id
             JOIN projects p ON b.project_id = p.id
             ORDER BY s.end_date DESC
             LIMIT 50"
        );

        $sprintData = null;
        
        // If no sprint selected and there are sprints, auto-select the first one
        if ($sprintId === 0 && !empty($sprints)) {
            $sprintId = (int) $sprints[0]['id'];
        }

        if ($sprintId > 0) {
            $sprintData = Database::selectOne(
                "SELECT s.*, b.id as board_id, b.name as board_name, p.id as project_id, p.`key` as project_key
                 FROM sprints s
                 JOIN boards b ON s.board_id = b.id
                 JOIN projects p ON b.project_id = p.id
                 WHERE s.id = ?",
                [$sprintId]
            );

            if ($sprintData) {
                // Get sprint metrics
                $totalIssues = (int) Database::selectValue(
                    "SELECT COUNT(*) FROM issues WHERE sprint_id = ?",
                    [$sprintId]
                );

                $completedStatuses = Database::select(
                    "SELECT id FROM statuses WHERE category = 'done'"
                );
                $completedStatusIds = array_column($completedStatuses, 'id');

                $completedIssues = 0;
                if (!empty($completedStatusIds)) {
                    $placeholders = implode(',', array_fill(0, count($completedStatusIds), '?'));
                    $completedIssues = (int) Database::selectValue(
                        "SELECT COUNT(*) FROM issues WHERE sprint_id = ? AND status_id IN ($placeholders)",
                        array_merge([$sprintId], $completedStatusIds)
                    );
                }

                $totalPoints = (float) Database::selectValue(
                    "SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ?",
                    [$sprintId]
                );

                $completedPoints = 0;
                if (!empty($completedStatusIds)) {
                    $placeholders = implode(',', array_fill(0, count($completedStatusIds), '?'));
                    $completedPoints = (float) Database::selectValue(
                        "SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ? AND status_id IN ($placeholders)",
                        array_merge([$sprintId], $completedStatusIds)
                    );
                }

                $sprintData['total_issues'] = $totalIssues;
                $sprintData['completed_issues'] = $completedIssues;
                $sprintData['completion_percentage'] = $totalIssues > 0 ? round(($completedIssues / $totalIssues) * 100) : 0;
                $sprintData['total_points'] = $totalPoints;
                $sprintData['completed_points'] = $completedPoints;
                $sprintData['points_completion_percentage'] = $totalPoints > 0 ? round(($completedPoints / $totalPoints) * 100) : 0;
            }
        }

        if ($request->wantsJson()) {
            $this->json([
                'sprints' => $sprints,
                'sprint' => $sprintData,
            ]);
        }

        return $this->view('reports.sprint', [
            'sprints' => $sprints,
            'sprint' => $sprintData,
            'selectedSprint' => $sprintId,
        ]);
    }

    public function stats(Request $request): void
    {
        // Calculate stats
        $totalIssues = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issues"
        );

        $completedStatuses = Database::select(
            "SELECT id FROM statuses WHERE category = 'done'"
        );
        $completedStatusIds = array_column($completedStatuses, 'id');
        
        $completedIssues = 0;
        if (!empty($completedStatusIds)) {
            $placeholders = implode(',', array_fill(0, count($completedStatusIds), '?'));
            $completedIssues = (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues WHERE status_id IN ($placeholders)",
                $completedStatusIds
            );
        }

        $inProgressStatuses = Database::select(
            "SELECT id FROM statuses WHERE category = 'in_progress'"
        );
        $inProgressStatusIds = array_column($inProgressStatuses, 'id');
        
        $inProgressIssues = 0;
        if (!empty($inProgressStatusIds)) {
            $placeholders = implode(',', array_fill(0, count($inProgressStatusIds), '?'));
            $inProgressIssues = (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues WHERE status_id IN ($placeholders)",
                $inProgressStatusIds
            );
        }

        // Calculate average velocity from closed sprints
        $avgVelocity = 0;
        $closedSprints = Database::select(
            "SELECT id FROM sprints WHERE status = 'closed' ORDER BY end_date DESC LIMIT 10"
        );
        
        if (!empty($closedSprints)) {
            $totalVelocity = 0;
            foreach ($closedSprints as $sprint) {
                $completedPoints = (float) Database::selectValue(
                    "SELECT COALESCE(SUM(story_points), 0) FROM issues 
                     WHERE sprint_id = ? AND status_id IN (SELECT id FROM statuses WHERE category = 'done')",
                    [$sprint['id']]
                );
                $totalVelocity += $completedPoints;
            }
            $avgVelocity = round($totalVelocity / count($closedSprints), 1);
        }

        $this->json([
            'total_issues' => $totalIssues,
            'completed_issues' => $completedIssues,
            'in_progress' => $inProgressIssues,
            'avg_velocity' => $avgVelocity,
        ]);
    }

    public function burndown(Request $request): string
    {
        $sprintId = (int) ($request->param('sprintId') ?? 0);

        // Get all projects for selector
        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");
        
        // If no sprint ID provided, show selector with first active sprint
        if ($sprintId === 0) {
            $sprints = Database::select(
                "SELECT s.id, s.name, b.name as board_name, p.`key` as project_key
                 FROM sprints s
                 JOIN boards b ON s.board_id = b.id
                 JOIN projects p ON b.project_id = p.id
                 WHERE s.status = 'active'
                 ORDER BY s.created_at DESC
                 LIMIT 20"
            );
            
            if (!empty($sprints)) {
                $sprintId = (int) $sprints[0]['id'];
            } else {
                // No active sprints, show message
                return $this->view('reports.burndown', [
                    'sprint' => null,
                    'sprintData' => [],
                    'burndownData' => json_encode([]),
                    'idealBurndown' => json_encode([]),
                    'projects' => $projects,
                    'sprints' => [],
                    'selectedSprint' => 0,
                    'sprintIssues' => [],
                    'error' => 'No active sprints found. Please create or activate a sprint first.',
                ]);
            }
        }

        $sprint = Database::selectOne(
            "SELECT s.*, b.project_id
             FROM sprints s
             JOIN boards b ON s.board_id = b.id
             WHERE s.id = ?",
            [$sprintId]
        );

        if (!$sprint) {
            abort(404, 'Sprint not found');
        }

        $startDate = new \DateTime($sprint['start_date']);
        $endDate = new \DateTime($sprint['end_date']);
        $today = new \DateTime();

        $totalPoints = (float) Database::selectValue(
            "SELECT COALESCE(SUM(story_points), 0)
             FROM issues
             WHERE sprint_id = ?",
            [$sprintId]
        );

        $completedStatuses = Database::select(
            "SELECT id FROM statuses WHERE category = 'done'"
        );
        $completedStatusIds = array_column($completedStatuses, 'id');

        $burndownData = [];
        $idealBurndown = [];
        $current = clone $startDate;
        $sprintDays = $startDate->diff($endDate)->days + 1;
        $dailyBurn = $totalPoints / max(1, $sprintDays);
        $dayIndex = 0;

        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            
            $idealBurndown[] = [
                'date' => $dateStr,
                'points' => max(0, $totalPoints - ($dailyBurn * $dayIndex)),
            ];

            if ($current <= $today) {
                $completedPoints = 0;
                if (!empty($completedStatusIds)) {
                    $placeholders = implode(',', array_fill(0, count($completedStatusIds), '?'));
                    $completedPoints = (float) Database::selectValue(
                        "SELECT COALESCE(SUM(story_points), 0)
                         FROM issues
                         WHERE sprint_id = ?
                         AND status_id IN ($placeholders)
                         AND updated_at <= ?",
                        array_merge([$sprintId], $completedStatusIds, [$dateStr . ' 23:59:59'])
                    );
                }

                $burndownData[] = [
                    'date' => $dateStr,
                    'remaining' => $totalPoints - $completedPoints,
                    'completed' => $completedPoints,
                ];
            }

            $current->modify('+1 day');
            $dayIndex++;
        }

        // Get sprints for the selector
        $sprints = Database::select(
            "SELECT id, name FROM sprints WHERE board_id IN 
             (SELECT id FROM boards WHERE project_id = ?) 
             ORDER BY name",
            [$sprint['project_id']]
        );

        // Get sprint issues
        $sprintIssues = Database::select(
            "SELECT i.id, i.issue_key, i.summary, i.story_points, 
                    s.id as status_id, s.name as status_name, s.color as status_color,
                    CASE WHEN s.category = 'done' THEN 'done' 
                         WHEN s.category = 'in_progress' THEN 'progress'
                         ELSE 'todo' END as status_category,
                    t.icon as issue_type_icon, t.color as issue_type_color,
                    u.display_name as assignee_name
             FROM issues i
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_types t ON i.issue_type_id = t.id
             LEFT JOIN users u ON i.assignee_id = u.id
             WHERE i.sprint_id = ?
             ORDER BY i.issue_key",
            [$sprintId]
        );

        $completedPoints = 0;
        if (!empty($burndownData)) {
            $lastData = end($burndownData);
            $completedPoints = $lastData['completed'] ?? 0;
        }

        if ($request->wantsJson()) {
            $this->json([
                'sprint' => $sprint,
                'total_points' => $totalPoints,
                'burndown' => $burndownData,
                'ideal' => $idealBurndown,
            ]);
        }

        return $this->view('reports.burndown', [
            'sprint' => $sprint,
            'sprintData' => [
                'total_points' => $totalPoints,
                'completed_points' => $completedPoints,
                'remaining_points' => $totalPoints - $completedPoints,
                'days_remaining' => max(0, (new \DateTime($sprint['end_date']))->diff(new \DateTime())->days),
            ],
            'burndownData' => json_encode($burndownData),
            'idealBurndown' => json_encode($idealBurndown),
            'projects' => $projects,
            'sprints' => $sprints,
            'sprintIssues' => $sprintIssues,
            'selectedSprint' => $sprintId,
        ]);
    }

    public function velocity(Request $request): string
    {
        $boardId = (int) $request->param('boardId');
        $sprintCount = min(20, max(5, (int) ($request->input('count') ?? 10)));

        $board = Database::selectOne(
            "SELECT b.*, p.`key` as project_key
             FROM boards b
             JOIN projects p ON b.project_id = p.id
             WHERE b.id = ?",
            [$boardId]
        );

        if (!$board) {
            abort(404, 'Board not found');
        }

        $completedStatuses = Database::select(
            "SELECT id FROM statuses WHERE category = 'done'"
        );
        $completedStatusIds = array_column($completedStatuses, 'id');

        $sprints = Database::select(
            "SELECT id, name, start_date, end_date
             FROM sprints
             WHERE board_id = ? AND status = 'closed'
             ORDER BY end_date DESC
             LIMIT ?",
            [$boardId, $sprintCount]
        );

        $velocityData = [];
        foreach (array_reverse($sprints) as $sprint) {
            $committed = (float) Database::selectValue(
                "SELECT COALESCE(SUM(story_points), 0)
                 FROM issues
                 WHERE sprint_id = ?",
                [$sprint['id']]
            );

            $completed = 0;
            if (!empty($completedStatusIds)) {
                $placeholders = implode(',', array_fill(0, count($completedStatusIds), '?'));
                $completed = (float) Database::selectValue(
                    "SELECT COALESCE(SUM(story_points), 0)
                     FROM issues
                     WHERE sprint_id = ? AND status_id IN ($placeholders)",
                    array_merge([$sprint['id']], $completedStatusIds)
                );
            }

            $velocityData[] = [
                'sprint_id' => $sprint['id'],
                'sprint_name' => $sprint['name'],
                'committed' => $committed,
                'completed' => $completed,
                'start_date' => $sprint['start_date'],
                'end_date' => $sprint['end_date'],
            ];
        }

        $averageVelocity = count($velocityData) > 0
            ? array_sum(array_column($velocityData, 'completed')) / count($velocityData)
            : 0;

        if ($request->wantsJson()) {
            $this->json([
                'board' => $board,
                'velocity' => $velocityData,
                'average_velocity' => round($averageVelocity, 1),
                'sprint_count' => count($velocityData),
            ]);
        }

        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");
        $boards = Database::select(
            "SELECT b.id, b.name FROM boards b 
             WHERE b.project_id = ? 
             ORDER BY b.name",
            [$board['project_id']]
        );

        return $this->view('reports.velocity', [
            'board' => $board,
            'velocityData' => json_encode($velocityData),
            'averageVelocity' => round($averageVelocity, 1),
            'projects' => $projects,
            'boards' => $boards,
            'selectedBoard' => $boardId,
        ]);
    }

    public function cumulativeFlowSelector(Request $request): string
    {
        $boardId = (int) $request->input('boardId', 0);

        $boards = Database::select(
            "SELECT b.id, b.name, p.id as project_id, p.`key` as project_key, p.name as project_name
             FROM boards b
             JOIN projects p ON b.project_id = p.id
             WHERE p.is_archived = 0
             ORDER BY b.name"
        );

        // Always show the selector page, don't auto-redirect
        return $this->view('reports.cumulative-flow-selector', [
            'boards' => $boards,
            'selectedBoard' => $boardId,
        ]);
    }

    public function cumulativeFlow(Request $request): string
    {
        $boardId = (int) $request->param('boardId');
        $days = min(90, max(7, (int) ($request->input('days') ?? 30)));

        $board = Database::selectOne(
            "SELECT b.*, p.id as project_id, p.`key` as project_key
             FROM boards b
             JOIN projects p ON b.project_id = p.id
             WHERE b.id = ?",
            [$boardId]
        );

        if (!$board) {
            abort(404, 'Board not found');
        }

        $statuses = Database::select(
            "SELECT id, name, color, category
             FROM statuses
             ORDER BY sort_order"
        );

        $startDate = (new \DateTime())->modify("-{$days} days");
        $endDate = new \DateTime();

        $flowData = [];
        $current = clone $startDate;

        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $dayData = ['date' => $dateStr];

            foreach ($statuses as $status) {
                $count = (int) Database::selectValue(
                    "SELECT COUNT(*)
                     FROM issues i
                     JOIN issue_history h ON i.id = h.issue_id
                     WHERE i.project_id = ?
                     AND h.field = 'status_id'
                     AND h.new_value = ?
                     AND DATE(h.created_at) <= ?
                     AND (
                         NOT EXISTS (
                             SELECT 1 FROM issue_history h2
                             WHERE h2.issue_id = i.id
                             AND h2.field = 'status_id'
                             AND h2.created_at > h.created_at
                             AND DATE(h2.created_at) <= ?
                         )
                     )",
                    [$board['project_id'], $status['id'], $dateStr, $dateStr]
                );

                if ($count === 0) {
                    $count = (int) Database::selectValue(
                        "SELECT COUNT(*)
                         FROM issues
                         WHERE project_id = ?
                         AND status_id = ?
                         AND DATE(created_at) <= ?",
                        [$board['project_id'], $status['id'], $dateStr]
                    );
                }

                $dayData[$status['name']] = $count;
            }

            $flowData[] = $dayData;
            $current->modify('+1 day');
        }

        if ($request->wantsJson()) {
            $this->json([
                'board' => $board,
                'statuses' => $statuses,
                'flow_data' => $flowData,
                'days' => $days,
            ]);
        }

        return $this->view('reports.cumulative-flow', [
            'board' => $board,
            'statuses' => $statuses,
            'flowData' => $flowData,
            'days' => $days,
        ]);
    }

    public function workload(Request $request): string
    {
        $projectId = (int) $request->input('project_id', 0);
        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");

        $conditions = ["i.status_id NOT IN (SELECT id FROM statuses WHERE category = 'done')"];
        $params = [];

        if ($projectId) {
            $conditions[] = "i.project_id = ?";
            $params[] = $projectId;
        }

        $whereClause = implode(' AND ', $conditions);

        $workloadData = Database::select(
            "SELECT 
                u.id as user_id,
                u.display_name,
                u.avatar,
                COUNT(i.id) as issue_count,
                COALESCE(SUM(i.story_points), 0) as total_points,
                COALESCE(SUM(i.remaining_estimate), 0) as remaining_estimate,
                SUM(CASE WHEN p.name = 'Highest' OR p.name = 'High' THEN 1 ELSE 0 END) as high_priority_count
             FROM users u
             LEFT JOIN issues i ON u.id = i.assignee_id AND $whereClause
             LEFT JOIN issue_priorities p ON i.priority_id = p.id
             WHERE u.is_active = 1
             GROUP BY u.id, u.display_name, u.avatar
             HAVING issue_count > 0
             ORDER BY total_points DESC",
            $params
        );

        $unassigned = Database::selectOne(
            "SELECT 
                COUNT(i.id) as issue_count,
                COALESCE(SUM(i.story_points), 0) as total_points,
                COALESCE(SUM(i.remaining_estimate), 0) as remaining_estimate
             FROM issues i
             WHERE i.assignee_id IS NULL AND $whereClause",
            $params
        );

        $averageLoad = count($workloadData) > 0
            ? array_sum(array_column($workloadData, 'total_points')) / count($workloadData)
            : 0;

        if ($request->wantsJson()) {
            $this->json([
                'workload' => $workloadData,
                'unassigned' => $unassigned,
                'average_load' => round($averageLoad, 1),
                'team_size' => count($workloadData),
            ]);
        }

        return $this->view('reports.workload', [
            'workload' => $workloadData,
            'unassigned' => $unassigned,
            'averageLoad' => $averageLoad,
            'projects' => $projects,
            'selectedProject' => $projectId,
        ]);
    }

    public function createdVsResolved(Request $request): string
    {
        $days = min(180, max(7, (int) ($request->input('days') ?? 30)));
        $projectId = (int) $request->input('project_id', 0);

        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");

        $startDate = (new \DateTime())->modify("-{$days} days");
        $endDate = new \DateTime();

        $resolvedStatuses = Database::select(
            "SELECT id FROM statuses WHERE category = 'done'"
        );
        $resolvedStatusIds = array_column($resolvedStatuses, 'id');

        $reportData = [];
        $current = clone $startDate;

        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            
            $created = (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues WHERE DATE(created_at) = ? " . 
                ($projectId ? "AND project_id = ?" : ""),
                $projectId ? [$dateStr, $projectId] : [$dateStr]
            );

            $resolved = 0;
            if (!empty($resolvedStatusIds)) {
                $placeholders = implode(',', array_fill(0, count($resolvedStatusIds), '?'));
                $resolved = (int) Database::selectValue(
                    "SELECT COUNT(*) FROM issues WHERE status_id IN ($placeholders) 
                     AND DATE(updated_at) = ? " . 
                    ($projectId ? "AND project_id = ?" : ""),
                    $projectId ? [...$resolvedStatusIds, $dateStr, $projectId] : [...$resolvedStatusIds, $dateStr]
                );
            }

            $reportData[] = [
                'date' => $dateStr,
                'created' => $created,
                'resolved' => $resolved,
            ];

            $current->modify('+1 day');
        }

        if ($request->wantsJson()) {
            $this->json([
                'data' => $reportData,
                'projects' => $projects,
                'days' => $days,
            ]);
        }

        return $this->view('reports.created-vs-resolved', [
            'reportData' => json_encode($reportData),
            'projects' => $projects,
            'selectedProject' => $projectId,
            'days' => $days,
        ]);
    }

    public function resolutionTime(Request $request): string
    {
        $projectId = (int) $request->input('project_id', 0);
        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");

        $resolvedStatuses = Database::select("SELECT id FROM statuses WHERE category = 'done'");
        $resolvedStatusIds = array_column($resolvedStatuses, 'id');

        $query = "SELECT 
            i.id,
            i.issue_key,
            i.summary,
            i.created_at,
            i.updated_at,
            TIMESTAMPDIFF(HOUR, i.created_at, i.updated_at) as resolution_hours
         FROM issues i
         WHERE i.status_id IN (" . implode(',', array_fill(0, count($resolvedStatusIds), '?')) . ")";
        $params = $resolvedStatusIds;

        if ($projectId) {
            $query .= " AND i.project_id = ?";
            $params[] = $projectId;
        }

        $query .= " ORDER BY i.updated_at DESC LIMIT 100";
        
        $data = Database::select($query, $params);

        $avgResolutionHours = 0;
        if (!empty($data)) {
            $avgResolutionHours = array_sum(array_column($data, 'resolution_hours')) / count($data);
        }

        return $this->view('reports.resolution-time', [
            'data' => $data,
            'projects' => $projects,
            'selectedProject' => $projectId,
            'avgResolutionHours' => round($avgResolutionHours, 1),
        ]);
    }

    public function priorityBreakdown(Request $request): string
    {
        $projectId = (int) $request->input('project_id', 0);
        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");

        $query = "SELECT 
            p.id,
            p.name,
            COUNT(i.id) as count,
            SUM(CASE WHEN i.status_id IN (SELECT id FROM statuses WHERE category = 'done') THEN 1 ELSE 0 END) as completed
         FROM issue_priorities p
         LEFT JOIN issues i ON p.id = i.priority_id";
        
        $params = [];
        if ($projectId) {
            $query .= " AND i.project_id = ?";
            $params[] = $projectId;
        }

        $query .= " GROUP BY p.id, p.name ORDER BY p.sort_order";

        $data = Database::select($query, $params);

        return $this->view('reports.priority-breakdown', [
            'data' => $data,
            'projects' => $projects,
            'selectedProject' => $projectId,
        ]);
    }

    public function timeLogged(Request $request): string
    {
        $projectId = (int) $request->input('project_id', 0);
        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");

        $query = "SELECT 
            u.id,
            u.display_name,
            u.avatar,
            COUNT(wl.id) as worklog_count,
            SUM(wl.time_spent) as total_time_spent
         FROM users u
         LEFT JOIN worklogs wl ON u.id = wl.user_id
         LEFT JOIN issues i ON wl.issue_id = i.id";
         
         $params = [];
         if ($projectId) {
             $query .= " AND i.project_id = ?";
             $params[] = $projectId;
         }

         $query .= " WHERE u.is_active = 1
         GROUP BY u.id, u.display_name, u.avatar
         HAVING worklog_count > 0
         ORDER BY total_time_spent DESC";

        $data = Database::select($query, $params);

        return $this->view('reports.time-logged', [
            'data' => $data,
            'projects' => $projects,
            'selectedProject' => $projectId,
        ]);
    }

    public function estimateAccuracy(Request $request): string
    {
        $projectId = (int) $request->input('project_id', 0);
        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");

        $resolvedStatuses = Database::select("SELECT id FROM statuses WHERE category = 'done'");
        $resolvedStatusIds = array_column($resolvedStatuses, 'id');

        $query = "SELECT 
            i.id,
            i.issue_key,
            i.summary,
            i.original_estimate,
            COALESCE(SUM(wl.time_spent), 0) as actual_time_spent,
            CASE 
                WHEN i.original_estimate = 0 THEN NULL
                ELSE ROUND((COALESCE(SUM(wl.time_spent), 0) / i.original_estimate) * 100, 1)
            END as accuracy_percentage
         FROM issues i
         LEFT JOIN worklogs wl ON i.id = wl.issue_id
         WHERE i.status_id IN (" . implode(',', array_fill(0, count($resolvedStatusIds), '?')) . ")
         AND i.original_estimate > 0";
         
         $params = $resolvedStatusIds;
         if ($projectId) {
             $query .= " AND i.project_id = ?";
             $params[] = $projectId;
         }

         $query .= " GROUP BY i.id, i.issue_key, i.summary, i.original_estimate
         ORDER BY i.updated_at DESC LIMIT 100";

        $data = Database::select($query, $params);

        return $this->view('reports.estimate-accuracy', [
            'data' => $data,
            'projects' => $projects,
            'selectedProject' => $projectId,
        ]);
    }

    public function versionProgress(Request $request): string
    {
        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");

        $query = "SELECT 
            v.id,
            v.name,
            v.release_date,
            COUNT(i.id) as total_issues,
            SUM(CASE WHEN i.status_id IN (SELECT id FROM statuses WHERE category = 'done') THEN 1 ELSE 0 END) as completed_issues,
            p.name as project_name
         FROM versions v
         LEFT JOIN issue_versions iv ON v.id = iv.version_id
         LEFT JOIN issues i ON iv.issue_id = i.id
         LEFT JOIN projects p ON v.project_id = p.id
         WHERE v.is_archived = 0
         GROUP BY v.id, v.name, v.release_date, p.name
         ORDER BY v.release_date DESC";

        $data = Database::select($query);

        return $this->view('reports.version-progress', [
            'data' => $data,
            'projects' => $projects,
        ]);
    }

    public function releaseBurndown(Request $request): string
    {
        $versionId = (int) $request->input('version_id', 0);
        $projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");
        $versions = Database::select("SELECT id, name FROM versions WHERE is_archived = 0 ORDER BY release_date DESC");

        $versionData = null;
        $burndownData = [];

        if ($versionId > 0) {
            $versionData = Database::selectOne(
                "SELECT v.*, p.name as project_name FROM versions v 
                 JOIN projects p ON v.project_id = p.id 
                 WHERE v.id = ?",
                [$versionId]
            );

            if ($versionData) {
                $issues = Database::select(
                    "SELECT i.id, i.created_at, i.updated_at, i.status_id 
                     FROM issues i
                     JOIN issue_versions iv ON i.id = iv.issue_id
                     WHERE iv.version_id = ? ORDER BY i.created_at",
                    [$versionId]
                );

                $resolvedStatuses = Database::select("SELECT id FROM statuses WHERE category = 'done'");
                $resolvedStatusIds = array_column($resolvedStatuses, 'id');

                foreach ($issues as $issue) {
                    $burndownData[] = [
                        'issue_id' => $issue['id'],
                        'created_at' => $issue['created_at'],
                        'completed_at' => in_array($issue['status_id'], $resolvedStatusIds) ? $issue['updated_at'] : null,
                    ];
                }
            }
        }

        return $this->view('reports.release-burndown', [
            'version' => $versionData,
            'versions' => $versions,
            'burndownData' => json_encode($burndownData),
            'projects' => $projects,
            'selectedVersion' => $versionId,
        ]);
    }
}
