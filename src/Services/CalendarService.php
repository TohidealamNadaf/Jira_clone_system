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
                i.project_id,
                proj.name as project_name,
                proj.key as project_key,
                it.name as issue_type
            FROM issues i
            JOIN projects proj ON i.project_id = proj.id
            JOIN statuses s ON i.status_id = s.id
            JOIN issue_priorities ip ON i.priority_id = ip.id
            JOIN issue_types it ON i.issue_type_id = it.id
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
        // Priority: start_date/end_date -> due_date (as single day)

        $start = $issue['start_date'] ?? $issue['due_date'];
        $end = $issue['end_date'] ?? $issue['due_date'];

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
                'status' => $issue['status_name'],
                'statusColor' => $issue['status_color'] ?? '#ccc',
                'priority' => $issue['priority_name'],
                'issueType' => $issue['issue_type'],
                'description' => mb_substr(strip_tags($issue['description'] ?? ''), 0, 100) . '...'
            ]
        ];
    }

    /**
     * Get upcoming issues (dashboard/widget usage)
     */
    public function getUpcomingIssues(int $limit = 5): array
    {
        $sql = "
            SELECT i.*, p.key as project_key
            FROM issues i
            JOIN projects p ON i.project_id = p.id
            WHERE i.due_date >= CURDATE()
            ORDER BY i.due_date ASC
            LIMIT :limit
        ";

        return Database::select($sql, ['limit' => $limit]);
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
}
