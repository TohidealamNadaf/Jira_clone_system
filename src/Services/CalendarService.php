<?php
/**
 * Calendar Service
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class CalendarService
{
    /**
     * Get events for a specific month
     */
    public function getMonthEvents(int $year, int $month): array
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        return $this->getEvents($startDate, $endDate);
    }

    /**
     * Get events for a date range
     */
    public function getDateRangeEvents(string $start, string $end): array
    {
        return $this->getEvents($start, $end);
    }

    /**
     * Get events for a project within a date range
     */
    public function getProjectDateRangeEvents(string $projectKey, string $start, string $end): array
    {
        return $this->getEvents($start, $end, ['project_key' => $projectKey]);
    }

    /**
     * Get project events for a specific month
     */
    public function getProjectEvents(string $projectKey, int $year, int $month): array
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        return $this->getEvents($startDate, $endDate, ['project_key' => $projectKey]);
    }

    /**
     * Core method to fetch events with filters
     */
    private function getEvents(string $start, string $end, array $filters = []): array
    {
        $sql = "
            SELECT DISTINCT
                i.id,
                i.issue_key as 'key',
                i.summary as title,
                i.description,
                i.start_date,
                i.end_date,
                i.due_date,
                i.priority_id,
                ip.name as priority_name,
                i.status_id,
                s.name as status_name,
                s.color as status_color,
                s.category as status_category,
                i.project_id,
                proj.name as project_name,
                proj.key as project_key,
                it.name as issue_type,
                i.issue_type_id,
                i.assignee_id,
                assignee.display_name as assignee_name,
                assignee.email as assignee_email,
                assignee.avatar as assignee_avatar,
                i.reporter_id,
                reporter.display_name as reporter_name,
                reporter.email as reporter_email,
                reporter.avatar as reporter_avatar,
                i.created_at,
                i.updated_at,
                i.story_points
            FROM issues i
            JOIN projects proj ON i.project_id = proj.id
            JOIN statuses s ON i.status_id = s.id
            JOIN issue_priorities ip ON i.priority_id = ip.id
            JOIN issue_types it ON i.issue_type_id = it.id
            LEFT JOIN users assignee ON i.assignee_id = assignee.id
            LEFT JOIN users reporter ON i.reporter_id = reporter.id
            WHERE
                (
                    (i.start_date BETWEEN :start1 AND :end1) OR
                    (i.end_date BETWEEN :start2 AND :end2) OR
                    (i.due_date BETWEEN :start3 AND :end3) OR
                    (i.start_date <= :start4 AND i.end_date >= :end4) -- Spanning events
                )
        ";

        $params = [
            'start1' => $start,
            'end1' => $end,
            'start2' => $start,
            'end2' => $end,
            'start3' => $start,
            'end3' => $end,
            'start4' => $end,
            'end4' => $start // Logic for spanning: start <= range_end AND end >= range_start
        ];

        // Apply filters
        if (!empty($filters['project_key'])) {
            $sql .= " AND proj.key = :project_key";
            $params['project_key'] = $filters['project_key'];
        }

        $sql .= " ORDER BY i.start_date ASC";

        $issues = Database::select($sql, $params);

        return array_map([$this, 'formatEvent'], $issues);
    }

    /**
     * Format issue as FullCalendar event
     */
    private function formatEvent(array $issue): array
    {
        // Determine start/end to use
        // Determine start/end to use
        // User Request: Always use Due Date for the badge position if available.
        // This prevents the event from spanning multiple days on the calendar.
        if (!empty($issue['due_date'])) {
            $start = $issue['due_date'];
            // Force single day for calendar view to avoid spanning "duplication" look
            $end = $issue['due_date'];
        } else {
            $start = $issue['start_date'];
            // Force single day
            $end = $issue['start_date'];
        }

        // Color mapping based on priority
        $colors = [
            'Highest' => '#d9534f', // Red
            'High' => '#f0ad4e',   // Orange
            'Medium' => '#5bc0de', // Blue
            'Low' => '#5cb85c',     // Green
            'Lowest' => '#5cb85c'   // Green
        ];

        $color = $colors[$issue['priority_name']] ?? '#777777';

        return [
            'id' => $issue['id'],
            'title' => $issue['key'] . ': ' . $issue['title'],
            'start' => $start,
            'end' => $end,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'allDay' => true, // Jira issues are usually date-based, not time-based
            'extendedProps' => [
                'key' => $issue['key'],
                'project' => $issue['project_name'],
                'projectKey' => $issue['project_key'],
                'projectId' => $issue['project_id'],
                'status' => $issue['status_name'],
                'statusColor' => $issue['status_color'] ?? '#ccc',
                'statusCategory' => $issue['status_category'], // Added for completion logic
                'priority' => $issue['priority_name'],
                'issueType' => $issue['issue_type'],
                'issueTypeId' => $issue['issue_type_id'],
                'description' => mb_substr(strip_tags($issue['description'] ?? ''), 0, 100) . '...',
                'assigneeId' => $issue['assignee_id'],
                'assigneeName' => $issue['assignee_name'],
                'assigneeEmail' => $issue['assignee_email'],
                'assigneeAvatar' => $issue['assignee_avatar'],
                'reporterId' => $issue['reporter_id'],
                'reporterName' => $issue['reporter_name'],
                'reporterEmail' => $issue['reporter_email'],
                'reporterAvatar' => $issue['reporter_avatar'],
                'created' => $issue['created_at'],
                'updated' => $issue['updated_at'],
                'storyPoints' => $issue['story_points']
            ]
        ];
    }

    /**
     * Get upcoming issues (dashboard/widget usage) - Returns formatted events
     */
    public function getUpcomingIssues(int $limit = 5): array
    {
        $sql = "
            SELECT
                i.id,
                i.issue_key as 'key',
                i.summary as title,
                i.description,
                i.start_date,
                i.end_date,
                i.due_date,
                i.priority_id,
                ip.name as priority_name,
                i.status_id,
                s.name as status_name,
                s.color as status_color,
                s.category as status_category,
                i.project_id,
                proj.name as project_name,
                proj.key as project_key,
                it.name as issue_type,
                i.issue_type_id,
                i.assignee_id,
                assignee.display_name as assignee_name,
                assignee.email as assignee_email,
                assignee.avatar as assignee_avatar,
                i.reporter_id,
                reporter.display_name as reporter_name,
                reporter.email as reporter_email,
                reporter.avatar as reporter_avatar,
                i.created_at,
                i.updated_at,
                i.story_points
            FROM issues i
            JOIN projects proj ON i.project_id = proj.id
            JOIN statuses s ON i.status_id = s.id
            JOIN issue_priorities ip ON i.priority_id = ip.id
            JOIN issue_types it ON i.issue_type_id = it.id
            LEFT JOIN users assignee ON i.assignee_id = assignee.id
            LEFT JOIN users reporter ON i.reporter_id = reporter.id
            WHERE i.due_date >= CURDATE()
            ORDER BY i.due_date ASC
            LIMIT :limit
        ";

        $issues = Database::select($sql, ['limit' => $limit]);

        return array_map([$this, 'formatEvent'], $issues);
    }

    /**
     * Get overdue issues
     */
    public function getOverdueIssues(int $limit = 5): array
    {
        $sql = "
            SELECT i.*, p.key as project_key
            FROM issues i
            JOIN projects p ON i.project_id = p.id
            WHERE i.due_date < CURDATE() 
            AND i.status_id NOT IN (SELECT id FROM statuses WHERE category = 'done')
            ORDER BY i.due_date ASC
            LIMIT :limit
        ";

        return Database::select($sql, ['limit' => $limit]);
    }

    /**
     * Get projects for filter dropdown
     */
    public function getProjectsForFilter(): array
    {
        return Database::select("SELECT id, `key`, name FROM projects ORDER BY name ASC");
    }

    /**
     * Get unscheduled issues (issues without start_date and due_date)
     */
    public function getUnscheduledIssues(): array
    {
        $sql = "
            SELECT
                i.id,
                i.issue_key as 'key',
                i.summary,
                i.description,
                i.priority_id,
                ip.name as priority_name,
                i.status_id,
                s.name as status_name,
                s.color as status_color,
                s.category as status_category,
                i.project_id,
                proj.name as project_name,
                proj.key as project_key,
                proj.key as project_key,
                it.name as issue_type,
                i.issue_type_id,
                it.icon as issue_type_icon,
                it.color as issue_type_color,
                i.assignee_id,
                assignee.display_name as assignee_name,
                assignee.email as assignee_email,
                assignee.avatar as assignee_avatar,
                i.reporter_id,
                reporter.display_name as reporter_name,
                reporter.email as reporter_email,
                reporter.avatar as reporter_avatar,
                i.created_at,
                i.updated_at,
                i.story_points
            FROM issues i
            JOIN projects proj ON i.project_id = proj.id
            JOIN statuses s ON i.status_id = s.id
            JOIN issue_priorities ip ON i.priority_id = ip.id
            JOIN issue_types it ON i.issue_type_id = it.id
            LEFT JOIN users assignee ON i.assignee_id = assignee.id
            LEFT JOIN users reporter ON i.reporter_id = reporter.id
            WHERE 
                i.start_date IS NULL
                AND i.due_date IS NULL
                AND s.category != 'done'
            ORDER BY 
                FIELD(ip.name, 'Urgent', 'High', 'Medium', 'Low'),
                i.created_at DESC
        ";

        $issues = Database::select($sql);

        // No labels column, skip parsing

        return $issues;
    }

    /**
     * Get statuses for filter dropdown
     */
    public function getStatusesForFilter(): array
    {
        return Database::select("SELECT id, name, category FROM statuses ORDER BY name ASC");
    }

    /**
     * Get priorities for filter dropdown
     */
    public function getPrioritiesForFilter(): array
    {
        return Database::select("SELECT id, name FROM issue_priorities ORDER BY name ASC");
    }

    /**
     * Get issue types for filter dropdown
     */
    public function getIssueTypesForFilter(): array
    {
        return Database::select("SELECT id, name FROM issue_types ORDER BY name ASC");
    }

    /**
     * API: Get users for assignee filter
     */
    public function getUsersForFilter(): array
    {
        $sql = "
            SELECT DISTINCT
                u.id,
                u.display_name,
                u.email,
                u.avatar
            FROM users u
            JOIN issues i ON (i.assignee_id = u.id OR i.reporter_id = u.id)
            WHERE u.is_active = 1
            ORDER BY u.display_name
        ";

        return Database::select($sql);
    }

    /**
     * Toggle watch status for an issue
     */
    public function toggleWatch(string $issueKey, int $userId): bool
    {
        $issue = Database::selectOne("SELECT id FROM issues WHERE issue_key = :key", ['key' => $issueKey]);
        if (!$issue) {
            return false;
        }

        $exists = Database::selectOne(
            "SELECT 1 FROM issue_watchers WHERE issue_id = :issue_id AND user_id = :user_id",
            ['issue_id' => $issue['id'], 'user_id' => $userId]
        );

        if ($exists) {
            Database::delete("issue_watchers", "issue_id = :issue_id AND user_id = :user_id", [$issue['id'], $userId]);
            return false; // Now not watching
        } else {
            Database::insert("issue_watchers", [
                'issue_id' => $issue['id'],
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return true; // Now watching
        }
    }

    /**
     * Get watch status
     */
    public function getWatchStatus(string $issueKey, int $userId): bool
    {
        $issue = Database::selectOne("SELECT id FROM issues WHERE issue_key = :key", ['key' => $issueKey]);
        if (!$issue) {
            return false;
        }

        $result = Database::selectOne(
            "SELECT 1 FROM issue_watchers WHERE issue_id = :issue_id AND user_id = :user_id",
            ['issue_id' => $issue['id'], 'user_id' => $userId]
        );

        return (bool) $result;
    }
}
