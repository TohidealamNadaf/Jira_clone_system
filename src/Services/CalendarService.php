<?php
/**
 * Calendar Service - Handles calendar event data and logic
 * Provides methods to fetch and format issues for calendar views
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class CalendarService
{
    public function __construct()
    {
    }
    
    /**
     * Get all issues for a given month across all projects
     */
    public function getMonthEvents(int $year, int $month): array
    {
        $startOfMonth = "$year-" . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . "-01";
        $endOfMonth = date('Y-m-t', strtotime($startOfMonth));
        
        // Get issues with any date in this month, or use current date for issues without dates
        $sql = "
            SELECT 
                i.id,
                i.`key`,
                i.summary,
                i.description,
                i.due_date,
                COALESCE(i.start_date, DATE_SUB(COALESCE(i.due_date, CURDATE()), INTERVAL 7 DAY)) as start_date,
                COALESCE(i.end_date, i.due_date, CURDATE()) as end_date,
                i.priority,
                i.status_id,
                s.name as status_name,
                s.color as status_color,
                p.`key` as project_key,
                p.name as project_name,
                it.name as issue_type_name,
                it.icon as issue_type_icon
            FROM issues i
            LEFT JOIN statuses s ON i.status_id = s.id
            LEFT JOIN projects p ON i.project_id = p.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            LIMIT 100
        ";
        
        $issues = Database::select($sql);
        return $this->formatEventsForCalendar($issues);
    }
    
    /**
     * Get issues for a project in a given month
     */
    public function getProjectEvents(string $projectKey, int $year, int $month): array
    {
        $startOfMonth = "$year-" . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . "-01";
        $endOfMonth = date('Y-m-t', strtotime($startOfMonth));
        
        $sql = "
            SELECT 
                i.id,
                i.`key`,
                i.summary,
                i.description,
                i.due_date,
                COALESCE(i.start_date, DATE_SUB(COALESCE(i.due_date, CURDATE()), INTERVAL 7 DAY)) as start_date,
                COALESCE(i.end_date, i.due_date, CURDATE()) as end_date,
                i.priority,
                i.status_id,
                s.name as status_name,
                s.color as status_color,
                p.`key` as project_key,
                p.name as project_name,
                it.name as issue_type_name,
                it.icon as issue_type_icon
            FROM issues i
            LEFT JOIN statuses s ON i.status_id = s.id
            LEFT JOIN projects p ON i.project_id = p.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            WHERE p.`key` = :projectKey
            LIMIT 100
        ";
        
        $issues = Database::select($sql, [':projectKey' => $projectKey]);
        return $this->formatEventsForCalendar($issues);
    }
    
    /**
     * Get events across a date range for all projects
     */
    public function getDateRangeEvents(string $startDate, string $endDate): array
    {
        $sql = "
            SELECT 
                i.id,
                i.`key`,
                i.summary,
                i.description,
                i.due_date,
                COALESCE(i.start_date, DATE_SUB(COALESCE(i.due_date, CURDATE()), INTERVAL 7 DAY)) as start_date,
                COALESCE(i.end_date, i.due_date, CURDATE()) as end_date,
                i.priority,
                i.status_id,
                s.name as status_name,
                s.color as status_color,
                p.`key` as project_key,
                p.name as project_name,
                it.name as issue_type_name,
                it.icon as issue_type_icon
            FROM issues i
            LEFT JOIN statuses s ON i.status_id = s.id
            LEFT JOIN projects p ON i.project_id = p.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            WHERE 
                (i.due_date IS NOT NULL AND i.due_date BETWEEN :start AND :end)
                OR (i.start_date IS NOT NULL AND i.start_date BETWEEN :start AND :end)
                OR (i.end_date IS NOT NULL AND i.end_date BETWEEN :start AND :end)
            ORDER BY COALESCE(i.due_date, i.created_at) ASC
            LIMIT 100
        ";
        
        $issues = Database::select($sql, [
            ':start' => $startDate,
            ':end' => $endDate
        ]);
        
        return $this->formatEventsForCalendar($issues);
    }
    
    /**
     * Get events for a specific project within a date range
     */
    public function getProjectDateRangeEvents(string $projectKey, string $startDate, string $endDate): array
    {
        $sql = "
            SELECT 
                i.id,
                i.`key`,
                i.summary,
                i.description,
                i.due_date,
                COALESCE(i.start_date, DATE_SUB(COALESCE(i.due_date, CURDATE()), INTERVAL 7 DAY)) as start_date,
                COALESCE(i.end_date, i.due_date, CURDATE()) as end_date,
                i.priority,
                i.status_id,
                s.name as status_name,
                s.color as status_color,
                p.`key` as project_key,
                p.name as project_name,
                it.name as issue_type_name,
                it.icon as issue_type_icon
            FROM issues i
            LEFT JOIN statuses s ON i.status_id = s.id
            LEFT JOIN projects p ON i.project_id = p.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            WHERE 
                p.`key` = :projectKey
                AND (
                    (i.due_date IS NOT NULL AND i.due_date BETWEEN :start AND :end)
                    OR (i.start_date IS NOT NULL AND i.start_date BETWEEN :start AND :end)
                    OR (i.end_date IS NOT NULL AND i.end_date BETWEEN :start AND :end)
                )
            ORDER BY COALESCE(i.due_date, i.created_at) ASC
            LIMIT 100
        ";
        
        $issues = Database::select($sql, [
            ':projectKey' => $projectKey,
            ':start' => $startDate,
            ':end' => $endDate
        ]);
        
        return $this->formatEventsForCalendar($issues);
    }
    
    /**
     * Get upcoming issues (next 30 days)
     */
    public function getUpcomingIssues(): array
    {
        $endDate = date('Y-m-d', strtotime('+30 days'));
        $startDate = date('Y-m-d');
        
        return $this->getDateRangeEvents($startDate, $endDate);
    }
    
    /**
     * Get overdue issues
     */
    public function getOverdueIssues(): array
    {
        $sql = "
            SELECT 
                i.id,
                i.`key`,
                i.summary,
                i.due_date,
                i.status_id,
                s.name as status_name,
                p.name as project_name,
                it.name as issue_type_name
            FROM issues i
            LEFT JOIN statuses s ON i.status_id = s.id
            LEFT JOIN projects p ON i.project_id = p.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            WHERE 
                i.due_date < CURDATE()
                AND i.status_id != (SELECT id FROM statuses WHERE name = 'Closed' LIMIT 1)
                ORDER BY i.due_date ASC
                ";
                
                return Database::select($sql);
    }
    
    /**
     * Get issues by project for dropdown
     */
    public function getProjectsForFilter(): array
    {
        $sql = "SELECT id, name, `key` FROM projects ORDER BY name ASC";
        return Database::select($sql);
    }
    
    /**
     * Format issues into calendar event objects
     */
    private function formatEventsForCalendar(array $issues): array
    {
        $events = [];
        
        foreach ($issues as $issue) {
            $startDate = $issue['start_date'] ?? $issue['due_date'];
            $endDate = $issue['end_date'] ?? $issue['due_date'];
            
            // Color based on priority
            $color = $this->getPriorityColor($issue['priority']);
            
            $events[] = [
                'id' => $issue['id'],
                'title' => $issue['key'] . ': ' . $issue['summary'],
                'start' => $startDate,
                'end' => $endDate,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#fff',
                'extendedProps' => [
                    'key' => $issue['key'],
                    'summary' => $issue['summary'],
                    'priority' => $issue['priority'],
                    'status' => $issue['status_name'],
                    'statusColor' => $issue['status_color'],
                    'project' => $issue['project_name'],
                    'projectKey' => $issue['project_key'],
                    'issueType' => $issue['issue_type_name'],
                    'issueTypeIcon' => $issue['issue_type_icon'],
                    'dueDate' => $issue['due_date'],
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'description' => $issue['description']
                ]
            ];
        }
        
        return $events;
    }
    
    /**
     * Get color based on priority
     */
    private function getPriorityColor(string $priority): string
    {
        return match(strtolower($priority)) {
            'urgent' => '#d9534f',   // Red
            'high' => '#f0ad4e',     // Orange
            'medium' => '#5bc0de',   // Blue
            'low' => '#5cb85c',      // Green
            default => '#6c757d'     // Gray
        };
    }
}
