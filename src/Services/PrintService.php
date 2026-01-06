<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * PrintService - Handles all printing and report generation
 * Integrates with KoolReport for professional report rendering
 */
class PrintService
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Get board data for printing
     * 
     * @param int $projectId
     * @param int $sprintId|null
     * @return array
     */
    public function getBoardDataForPrint(int $projectId, ?int $sprintId = null): array
    {
        // Get project
        $project = $this->database->selectOne(
            'SELECT id, name, key FROM projects WHERE id = ?',
            [$projectId]
        );

        if (!$project) {
            return [];
        }

        // Get statuses
        $statuses = $this->database->select(
            'SELECT id, name FROM statuses ORDER BY position',
            []
        );

        // Build query for issues
        $query = 'SELECT 
            i.id, 
            i.key, 
            i.summary, 
            i.description,
            i.issue_type_id,
            i.status_id, 
            i.priority_id,
            i.assignee_id,
            u.first_name as assignee_name,
            it.name as issue_type,
            it.icon as issue_icon,
            s.name as status_name,
            p.name as priority_name
        FROM issues i
        LEFT JOIN users u ON i.assignee_id = u.id
        LEFT JOIN issue_types it ON i.issue_type_id = it.id
        LEFT JOIN statuses s ON i.status_id = s.id
        LEFT JOIN priorities p ON i.priority_id = p.id
        WHERE i.project_id = ?';

        $params = [$projectId];

        // Add sprint filter if provided
        if ($sprintId) {
            $query .= ' AND i.sprint_id = ?';
            $params[] = $sprintId;
        }

        $query .= ' ORDER BY i.key DESC';

        $issues = $this->database->select($query, $params);

        return [
            'project' => $project,
            'statuses' => $statuses,
            'issues' => $issues,
            'total_issues' => count($issues),
            'timestamp' => date('Y-m-d H:i:s'),
            'generated_by' => $_SESSION['user']['email'] ?? 'System'
        ];
    }

    /**
     * Get project report data for printing
     * 
     * @param int $projectId
     * @return array
     */
    public function getProjectReportData(int $projectId): array
    {
        $project = $this->database->selectOne(
            'SELECT id, name, key FROM projects WHERE id = ?',
            [$projectId]
        );

        if (!$project) {
            return [];
        }

        // Get issue statistics
        $stats = $this->database->selectOne(
            'SELECT 
                COUNT(*) as total_issues,
                SUM(CASE WHEN status_id IN (SELECT id FROM statuses WHERE name = "Closed") THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN status_id IN (SELECT id FROM statuses WHERE name = "Open") THEN 1 ELSE 0 END) as open
            FROM issues WHERE project_id = ?',
            [$projectId]
        );

        // Get issues by priority
        $byPriority = $this->database->select(
            'SELECT 
                p.name, 
                COUNT(i.id) as count
            FROM issues i
            LEFT JOIN priorities p ON i.priority_id = p.id
            WHERE i.project_id = ?
            GROUP BY p.name
            ORDER BY COUNT(i.id) DESC',
            [$projectId]
        );

        // Get issues by type
        $byType = $this->database->select(
            'SELECT 
                it.name, 
                COUNT(i.id) as count
            FROM issues i
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            WHERE i.project_id = ?
            GROUP BY it.name
            ORDER BY COUNT(i.id) DESC',
            [$projectId]
        );

        // Get team members
        $members = $this->database->select(
            'SELECT DISTINCT 
                u.id,
                u.first_name,
                u.last_name,
                COUNT(i.id) as assigned_issues
            FROM project_members pm
            LEFT JOIN users u ON pm.user_id = u.id
            LEFT JOIN issues i ON u.id = i.assignee_id AND i.project_id = pm.project_id
            WHERE pm.project_id = ?
            GROUP BY u.id, u.first_name, u.last_name
            ORDER BY COUNT(i.id) DESC',
            [$projectId]
        );

        return [
            'project' => $project,
            'statistics' => $stats,
            'by_priority' => $byPriority,
            'by_type' => $byType,
            'team_members' => $members,
            'timestamp' => date('Y-m-d H:i:s'),
            'generated_by' => $_SESSION['user']['email'] ?? 'System'
        ];
    }

    /**
     * Get sprint data for printing
     * 
     * @param int $sprintId
     * @return array
     */
    public function getSprintReportData(int $sprintId): array
    {
        $sprint = $this->database->selectOne(
            'SELECT id, name, project_id, start_date, end_date, goal FROM sprints WHERE id = ?',
            [$sprintId]
        );

        if (!$sprint) {
            return [];
        }

        // Get sprint issues
        $issues = $this->database->select(
            'SELECT 
                i.id, 
                i.key, 
                i.summary,
                i.issue_type_id,
                i.status_id,
                i.priority_id,
                i.assignee_id,
                i.story_points,
                u.first_name as assignee_name,
                it.name as issue_type,
                s.name as status_name,
                p.name as priority_name
            FROM issues i
            LEFT JOIN users u ON i.assignee_id = u.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            LEFT JOIN statuses s ON i.status_id = s.id
            LEFT JOIN priorities p ON i.priority_id = p.id
            WHERE i.sprint_id = ?
            ORDER BY i.key',
            [$sprintId]
        );

        // Calculate sprint metrics
        $metrics = $this->calculateSprintMetrics($sprintId, $issues);

        return [
            'sprint' => $sprint,
            'issues' => $issues,
            'metrics' => $metrics,
            'timestamp' => date('Y-m-d H:i:s'),
            'generated_by' => $_SESSION['user']['email'] ?? 'System'
        ];
    }

    /**
     * Calculate sprint metrics
     * 
     * @param int $sprintId
     * @param array $issues
     * @return array
     */
    private function calculateSprintMetrics(int $sprintId, array $issues): array
    {
        $total_issues = count($issues);
        $completed = 0;
        $total_story_points = 0;
        $completed_story_points = 0;

        foreach ($issues as $issue) {
            if ($issue['status_name'] === 'Closed') {
                $completed++;
            }
            if ($issue['story_points']) {
                $total_story_points += $issue['story_points'];
                if ($issue['status_name'] === 'Closed') {
                    $completed_story_points += $issue['story_points'];
                }
            }
        }

        return [
            'total_issues' => $total_issues,
            'completed_issues' => $completed,
            'completion_percentage' => $total_issues > 0 ? round(($completed / $total_issues) * 100, 1) : 0,
            'total_story_points' => $total_story_points,
            'completed_story_points' => $completed_story_points,
            'remaining_story_points' => $total_story_points - $completed_story_points,
            'velocity' => $total_story_points > 0 ? round(($completed_story_points / $total_story_points) * 100, 1) : 0
        ];
    }

    /**
     * Export data to PDF using KoolReport
     * 
     * @param string $reportType (board|project|sprint)
     * @param array $data
     * @return string HTML for printing
     */
    public function generateHtmlReport(string $reportType, array $data): string
    {
        return match ($reportType) {
            'board' => $this->generateBoardReport($data),
            'project' => $this->generateProjectReport($data),
            'sprint' => $this->generateSprintReport($data),
            default => '<p>Unknown report type</p>'
        };
    }

    /**
     * Generate board HTML for printing
     * 
     * @param array $data
     * @return string
     */
    private function generateBoardReport(array $data): string
    {
        $html = '<html>
        <head>
            <meta charset="UTF-8">
            <title>Board Report - ' . e($data['project']['name']) . '</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif; color: #333; }
                .print-header { background: #8b1956; color: white; padding: 20px; margin-bottom: 20px; }
                .print-header h1 { margin: 0 0 5px 0; font-size: 28px; }
                .print-header p { margin: 0; font-size: 14px; opacity: 0.9; }
                .page { page-break-after: always; padding: 20px; }
                .board-section { margin: 20px 0; }
                .board-column { float: left; width: 25%; padding: 10px; background: #f5f5f5; margin: 0 0.5%; border-radius: 4px; }
                .column-title { font-weight: bold; font-size: 16px; margin-bottom: 10px; color: #8b1956; }
                .issue-card { background: white; padding: 10px; margin: 8px 0; border-left: 3px solid #8b1956; border-radius: 3px; }
                .issue-key { font-weight: bold; font-size: 12px; color: #8b1956; }
                .issue-summary { font-size: 13px; margin: 5px 0; }
                .issue-meta { font-size: 11px; color: #666; }
                .print-footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: right; }
                @media print {
                    body { margin: 0; padding: 0; }
                    .page { page-break-after: always; padding: 40px 20px; }
                }
            </style>
        </head>
        <body>';

        $html .= '<div class="print-header">
            <h1>' . e($data['project']['name']) . ' - Board Report</h1>
            <p>Project Key: ' . e($data['project']['key']) . ' | Generated: ' . $data['timestamp'] . '</p>
        </div>';

        $html .= '<div class="page"><div class="board-section">';

        // Output each status column
        foreach ($data['statuses'] as $status) {
            $html .= '<div class="board-column">';
            $html .= '<div class="column-title">' . e($status['name']) . '</div>';

            // Filter issues by status
            $statusIssues = array_filter($data['issues'], function ($issue) use ($status) {
                return $issue['status_id'] == $status['id'];
            });

            foreach ($statusIssues as $issue) {
                $html .= '<div class="issue-card">';
                $html .= '<div class="issue-key">' . e($issue['key']) . '</div>';
                $html .= '<div class="issue-summary">' . e($issue['summary']) . '</div>';
                $html .= '<div class="issue-meta">';
                $html .= 'Type: ' . e($issue['issue_type'] ?? 'Unknown');
                if ($issue['assignee_name']) {
                    $html .= ' | Assigned: ' . e($issue['assignee_name']);
                }
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '</div>';
        }

        $html .= '</div></div>';

        $html .= '<div class="print-footer">
            <p>Generated by: ' . e($data['generated_by']) . ' | Total Issues: ' . $data['total_issues'] . '</p>
        </div>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Generate project report HTML for printing
     * 
     * @param array $data
     * @return string
     */
    private function generateProjectReport(array $data): string
    {
        $html = '<html>
        <head>
            <meta charset="UTF-8">
            <title>Project Report - ' . e($data['project']['name']) . '</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif; color: #333; }
                .print-header { background: #8b1956; color: white; padding: 20px; margin-bottom: 20px; }
                .print-header h1 { margin: 0 0 5px 0; font-size: 28px; }
                .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0; }
                .stat-box { background: #f5f5f5; padding: 15px; border-radius: 4px; text-align: center; }
                .stat-value { font-size: 28px; font-weight: bold; color: #8b1956; }
                .stat-label { font-size: 13px; color: #666; margin-top: 5px; }
                .section { margin: 30px 0; }
                .section-title { font-size: 18px; font-weight: bold; color: #8b1956; margin: 20px 0 10px 0; border-bottom: 2px solid #8b1956; padding-bottom: 10px; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background: #f5f5f5; font-weight: bold; }
                .print-footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: right; }
                @media print {
                    body { margin: 0; padding: 0; }
                }
            </style>
        </head>
        <body>';

        $html .= '<div class="print-header">
            <h1>' . e($data['project']['name']) . ' - Project Report</h1>
            <p>Project Key: ' . e($data['project']['key']) . ' | Generated: ' . $data['timestamp'] . '</p>
        </div>';

        // Statistics boxes
        $html .= '<div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">' . ($data['statistics']['total_issues'] ?? 0) . '</div>
                <div class="stat-label">Total Issues</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">' . ($data['statistics']['resolved'] ?? 0) . '</div>
                <div class="stat-label">Resolved</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">' . ($data['statistics']['open'] ?? 0) . '</div>
                <div class="stat-label">Open</div>
            </div>
        </div>';

        // Priority breakdown
        if (!empty($data['by_priority'])) {
            $html .= '<div class="section">
                <h2 class="section-title">Issues by Priority</h2>
                <table>
                    <thead><tr><th>Priority</th><th>Count</th></tr></thead>
                    <tbody>';
            foreach ($data['by_priority'] as $row) {
                $html .= '<tr><td>' . e($row['name'] ?? 'Unknown') . '</td><td>' . $row['count'] . '</td></tr>';
            }
            $html .= '</tbody></table></div>';
        }

        // Type breakdown
        if (!empty($data['by_type'])) {
            $html .= '<div class="section">
                <h2 class="section-title">Issues by Type</h2>
                <table>
                    <thead><tr><th>Type</th><th>Count</th></tr></thead>
                    <tbody>';
            foreach ($data['by_type'] as $row) {
                $html .= '<tr><td>' . e($row['name'] ?? 'Unknown') . '</td><td>' . $row['count'] . '</td></tr>';
            }
            $html .= '</tbody></table></div>';
        }

        // Team members
        if (!empty($data['team_members'])) {
            $html .= '<div class="section">
                <h2 class="section-title">Team Members</h2>
                <table>
                    <thead><tr><th>Name</th><th>Assigned Issues</th></tr></thead>
                    <tbody>';
            foreach ($data['team_members'] as $member) {
                $html .= '<tr><td>' . e(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')) . '</td><td>' . ($member['assigned_issues'] ?? 0) . '</td></tr>';
            }
            $html .= '</tbody></table></div>';
        }

        $html .= '<div class="print-footer">
            <p>Generated by: ' . e($data['generated_by']) . '</p>
        </div>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Generate sprint report HTML for printing
     * 
     * @param array $data
     * @return string
     */
    private function generateSprintReport(array $data): string
    {
        $html = '<html>
        <head>
            <meta charset="UTF-8">
            <title>Sprint Report - ' . e($data['sprint']['name']) . '</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif; color: #333; }
                .print-header { background: #8b1956; color: white; padding: 20px; margin-bottom: 20px; }
                .print-header h1 { margin: 0 0 5px 0; font-size: 28px; }
                .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0; }
                .stat-box { background: #f5f5f5; padding: 15px; border-radius: 4px; text-align: center; }
                .stat-value { font-size: 24px; font-weight: bold; color: #8b1956; }
                .stat-label { font-size: 12px; color: #666; margin-top: 5px; }
                .issues-table { margin: 20px 0; }
                .section-title { font-size: 18px; font-weight: bold; color: #8b1956; margin: 20px 0 10px 0; border-bottom: 2px solid #8b1956; padding-bottom: 10px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background: #f5f5f5; font-weight: bold; font-size: 13px; }
                td { font-size: 13px; }
                .print-footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: right; }
                @media print { body { margin: 0; padding: 0; } }
            </style>
        </head>
        <body>';

        $html .= '<div class="print-header">
            <h1>' . e($data['sprint']['name']) . ' - Sprint Report</h1>
            <p>Goal: ' . e($data['sprint']['goal'] ?? 'No goal defined') . ' | Period: ' . $data['sprint']['start_date'] . ' to ' . $data['sprint']['end_date'] . '</p>
        </div>';

        // Metrics
        $m = $data['metrics'];
        $html .= '<div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">' . $m['total_issues'] . '</div>
                <div class="stat-label">Total Issues</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">' . $m['completed_issues'] . '</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">' . $m['completion_percentage'] . '%</div>
                <div class="stat-label">Progress</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">' . $m['completed_story_points'] . '/' . $m['total_story_points'] . '</div>
                <div class="stat-label">Story Points</div>
            </div>
        </div>';

        // Issues
        if (!empty($data['issues'])) {
            $html .= '<div class="issues-table">
                <h2 class="section-title">Sprint Issues</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Summary</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Assigned</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($data['issues'] as $issue) {
                $html .= '<tr>
                    <td><strong>' . e($issue['key']) . '</strong></td>
                    <td>' . e($issue['summary']) . '</td>
                    <td>' . e($issue['issue_type'] ?? '') . '</td>
                    <td>' . e($issue['status_name'] ?? '') . '</td>
                    <td>' . e($issue['assignee_name'] ?? 'Unassigned') . '</td>
                    <td>' . ($issue['story_points'] ?? '-') . '</td>
                </tr>';
            }
            $html .= '</tbody></table></div>';
        }

        $html .= '<div class="print-footer">
            <p>Generated by: ' . e($data['generated_by']) . ' on ' . $data['timestamp'] . '</p>
        </div>';

        $html .= '</body></html>';

        return $html;
    }
}
