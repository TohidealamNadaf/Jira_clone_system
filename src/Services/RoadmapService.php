<?php
/**
 * Roadmap Service - Handles roadmap data and logic
 * Provides methods to fetch epics, versions, and timeline data
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class RoadmapService
{
    public function __construct()
    {
    }
    
    /**
     * Get roadmap data for a project
     */
    public function getProjectRoadmap(string $projectKey): array
    {
        return [
            'epics' => $this->getEpics($projectKey),
            'versions' => $this->getVersions($projectKey),
            'timeline' => $this->getTimelineRange($projectKey)
        ];
    }
    
    /**
     * Get all epics for a project
     */
    public function getEpics(string $projectKey): array
    {
        $sql = "
            SELECT 
                i.id,
                i.`key`,
                i.summary,
                i.description,
                i.start_date,
                i.end_date,
                i.due_date,
                i.priority,
                i.status_id,
                s.name as status_name,
                s.color as status_color,
                it.name as issue_type_name,
                COUNT(DISTINCT ic.id) as issue_count,
                SUM(CASE WHEN ic.status_id IN (SELECT id FROM statuses WHERE name = 'Closed') THEN 1 ELSE 0 END) as completed_count
            FROM issues i
            LEFT JOIN statuses s ON i.status_id = s.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            LEFT JOIN issues ic ON ic.epic_id = i.id
            LEFT JOIN projects p ON i.project_id = p.id
            WHERE 
                p.`key` = :projectKey
                AND it.name = 'Epic'
            GROUP BY i.id
            ORDER BY i.start_date ASC
        ";
        
        $epics = Database::select($sql, [':projectKey' => $projectKey]);
        
        return array_map(function($epic) {
            return array_merge($epic, [
                'progress' => $epic['issue_count'] > 0 
                    ? round(($epic['completed_count'] / $epic['issue_count']) * 100) 
                    : 0
            ]);
        }, $epics);
    }
    
    /**
     * Get all versions for a project
     */
    public function getVersions(string $projectKey): array
    {
        $sql = "
            SELECT 
                v.id,
                v.name,
                v.description,
                v.start_date,
                v.release_date,
                v.status,
                COUNT(DISTINCT i.id) as issue_count,
                SUM(CASE WHEN i.status_id IN (SELECT id FROM statuses WHERE name = 'Closed') THEN 1 ELSE 0 END) as completed_count
            FROM versions v
            LEFT JOIN issues i ON i.version_id = v.id
            LEFT JOIN projects p ON v.project_id = p.id
            WHERE p.`key` = :projectKey
            GROUP BY v.id
            ORDER BY v.release_date ASC
        ";
        
        $versions = Database::select($sql, [':projectKey' => $projectKey]);
        
        return array_map(function($version) {
            return array_merge($version, [
                'progress' => $version['issue_count'] > 0 
                    ? round(($version['completed_count'] / $version['issue_count']) * 100) 
                    : 0
            ]);
        }, $versions);
    }
    
    /**
     * Get issues for a specific epic
     */
    public function getEpicIssues(int $epicId): array
    {
        $sql = "
            SELECT 
                i.id,
                i.`key`,
                i.summary,
                i.due_date,
                i.start_date,
                i.end_date,
                i.status_id,
                s.name as status_name,
                it.name as issue_type_name
            FROM issues i
            LEFT JOIN statuses s ON i.status_id = s.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            WHERE i.epic_id = :epicId
            ORDER BY i.due_date ASC
        ";
        
        return Database::select($sql, [':epicId' => $epicId]);
    }
    
    /**
     * Get issues for a specific version
     */
    public function getVersionIssues(int $versionId): array
    {
        $sql = "
            SELECT 
                i.id,
                i.`key`,
                i.summary,
                i.due_date,
                i.start_date,
                i.end_date,
                i.status_id,
                s.name as status_name,
                it.name as issue_type_name
            FROM issues i
            LEFT JOIN statuses s ON i.status_id = s.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            WHERE i.version_id = :versionId
            ORDER BY i.due_date ASC
        ";
        
        return Database::select($sql, [':versionId' => $versionId]);
    }
    
    /**
     * Get timeline range for a project's roadmap
     */
    public function getTimelineRange(string $projectKey): array
    {
        $sql = "
            SELECT 
                MIN(COALESCE(i.start_date, i.due_date, i.created_at)) as earliest_date,
                MAX(COALESCE(i.end_date, i.due_date, i.updated_at)) as latest_date,
                MIN(COALESCE(v.start_date, v.release_date)) as earliest_version_date,
                MAX(v.release_date) as latest_version_date
            FROM projects p
            LEFT JOIN issues i ON i.project_id = p.id
            LEFT JOIN versions v ON v.project_id = p.id
            WHERE p.`key` = :projectKey
        ";
        
        $result = Database::selectOne($sql, [':projectKey' => $projectKey]);
        
        if (!$result) {
            return [
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+90 days'))
            ];
        }
        
        $earliest = $result['earliest_date'] 
            ?? $result['earliest_version_date'] 
            ?? date('Y-m-d');
        $latest = $result['latest_date'] 
            ?? $result['latest_version_date'] 
            ?? date('Y-m-d', strtotime('+90 days'));
        
        return [
            'start_date' => $earliest,
            'end_date' => $latest
        ];
    }
    
    /**
     * Get all projects for roadmap selection
     */
    public function getProjects(): array
    {
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.`key`,
                COUNT(DISTINCT i.id) as issue_count,
                COUNT(DISTINCT v.id) as version_count
            FROM projects p
            LEFT JOIN issues i ON i.project_id = p.id
            LEFT JOIN versions v ON v.project_id = p.id
            GROUP BY p.id
            ORDER BY p.name ASC
        ";
        
        return Database::select($sql);
    }
    
    /**
     * Get roadmap statistics for a project
     */
    public function getProjectStats(string $projectKey): array
    {
        $sql = "
            SELECT 
                COUNT(DISTINCT CASE WHEN it.name = 'Epic' THEN i.id END) as epic_count,
                COUNT(DISTINCT v.id) as version_count,
                COUNT(DISTINCT i.id) as total_issues,
                COUNT(DISTINCT CASE WHEN i.status_id IN (SELECT id FROM statuses WHERE name = 'Closed') THEN i.id END) as completed_issues
            FROM projects p
            LEFT JOIN issues i ON i.project_id = p.id
            LEFT JOIN issue_types it ON i.issue_type_id = it.id
            LEFT JOIN versions v ON v.project_id = p.id
            WHERE p.`key` = :projectKey
        ";
        
        return Database::selectOne($sql, [':projectKey' => $projectKey]) ?: [
            'epic_count' => 0,
            'version_count' => 0,
            'total_issues' => 0,
            'completed_issues' => 0
        ];
    }
}
