<?php
/**
 * Roadmap Service - Manages roadmap items, dependencies, and timeline calculations
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class RoadmapService
{
    /**
     * Get all roadmap items for a project with full details
     */
    public function getProjectRoadmap(int $projectId, array $filters = []): array
    {
        $where = ['ri.project_id = ?'];
        $params = [$projectId];

        // Filter by status
        if (!empty($filters['status'])) {
            $where[] = 'ri.status = ?';
            $params[] = $filters['status'];
        }

        // Filter by type
        if (!empty($filters['type'])) {
            $where[] = 'ri.type = ?';
            $params[] = $filters['type'];
        }

        // Filter by owner
        if (!empty($filters['owner_id'])) {
            $where[] = 'ri.owner_id = ?';
            $params[] = (int) $filters['owner_id'];
        }

        // Filter by date range
        if (!empty($filters['start_date'])) {
            $where[] = 'ri.end_date >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'ri.start_date <= ?';
            $params[] = $filters['end_date'];
        }

        $whereClause = implode(' AND ', $where);

        // Get roadmap items
        $items = Database::select(
            "SELECT ri.*,
                    u.display_name as owner_name, u.avatar as owner_avatar,
                    creator.display_name as created_by_name,
                    COUNT(DISTINCT ris.sprint_id) as sprint_count,
                    COUNT(DISTINCT rdi.id) as dependency_count,
                    COUNT(DISTINCT roi.issue_id) as issue_count
             FROM roadmap_items ri
             LEFT JOIN users u ON ri.owner_id = u.id
             LEFT JOIN users creator ON ri.created_by = creator.id
             LEFT JOIN roadmap_item_sprints ris ON ri.id = ris.roadmap_item_id
             LEFT JOIN roadmap_dependencies rdi ON ri.id = rdi.item_id
             LEFT JOIN roadmap_item_issues roi ON ri.id = roi.roadmap_item_id
             WHERE $whereClause
             GROUP BY ri.id
             ORDER BY ri.start_date ASC, ri.sort_order ASC",
            $params
        );

        // Enrich items with related data
        $items = array_map(fn($item) => $this->enrichRoadmapItem($item), $items);

        return $items;
    }

    /**
     * Get single roadmap item with all related data
     */
    public function getRoadmapItem(int $itemId): ?array
    {
        $item = Database::selectOne(
            "SELECT ri.*,
                    u.display_name as owner_name, u.avatar as owner_avatar,
                    creator.display_name as created_by_name
             FROM roadmap_items ri
             LEFT JOIN users u ON ri.owner_id = u.id
             LEFT JOIN users creator ON ri.created_by = creator.id
             WHERE ri.id = ?",
            [$itemId]
        );

        if (!$item) {
            return null;
        }

        return $this->enrichRoadmapItem($item);
    }

    /**
     * Enrich roadmap item with related sprints, issues, and dependencies
     */
    private function enrichRoadmapItem(array $item): array
    {
        // Get linked sprints
        $sprints = Database::select(
            "SELECT s.id, s.name, s.status, s.start_date, s.end_date
             FROM sprints s
             INNER JOIN roadmap_item_sprints ris ON s.id = ris.sprint_id
             WHERE ris.roadmap_item_id = ?
             ORDER BY s.start_date ASC",
            [$item['id']]
        );

        // Get linked issues
        $issues = Database::select(
            "SELECT i.id, i.issue_key, i.summary, i.status_id, i.assignee_id,
                    it.name as type_name, it.icon as type_icon,
                    s.name as status_name, s.color as status_color,
                    u.display_name as assignee_name
             FROM issues i
             INNER JOIN roadmap_item_issues roi ON i.id = roi.issue_id
             LEFT JOIN issue_types it ON i.issue_type_id = it.id
             LEFT JOIN statuses s ON i.status_id = s.id
             LEFT JOIN users u ON i.assignee_id = u.id
             WHERE roi.roadmap_item_id = ?
             ORDER BY i.issue_key ASC",
            [$item['id']]
        );

        // Get dependencies (items this depends on)
        $dependencies = Database::select(
            "SELECT rd.*, ri.title as depends_on_title, ri.status as depends_on_status
             FROM roadmap_dependencies rd
             INNER JOIN roadmap_items ri ON rd.depends_on_item_id = ri.id
             WHERE rd.item_id = ?",
            [$item['id']]
        );

        // Calculate progress and cost
        $progress = $this->calculateItemProgress($item['id']);
        $cost = $this->calculateItemCost($item['id']);

        return array_merge($item, [
            'sprints' => $sprints,
            'issues' => $issues,
            'dependencies' => $dependencies,
            'progress' => $progress,
            'cost' => $cost,
        ]);
    }

    /**
     * Create new roadmap item
     */
    public function createRoadmapItem(int $projectId, array $data, int $userId): array
    {
        // Validate dates
        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            throw new \InvalidArgumentException('Start date must be before end date');
        }

        $insertData = [
            'project_id' => $projectId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? 'feature',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'] ?? 'planned',
            'priority' => $data['priority'] ?? 'medium',
            'progress_percentage' => isset($data['progress']) ? (int)$data['progress'] : 0,
            'owner_id' => $data['owner_id'] ?? null,
            'color' => $data['color'] ?? '#8b1956',
            'created_by' => $userId,
        ];

        Database::insert('roadmap_items', $insertData);

        $itemId = (int) Database::lastInsertId();

        // Link sprints if provided
        if (!empty($data['sprint_ids'])) {
            foreach ((array) $data['sprint_ids'] as $sprintId) {
                Database::insert('roadmap_item_sprints', [
                    'roadmap_item_id' => $itemId,
                    'sprint_id' => (int) $sprintId,
                ]);
            }
        }

        // Link issues if provided
        if (!empty($data['issue_ids'])) {
            foreach ((array) $data['issue_ids'] as $issueId) {
                Database::insert('roadmap_item_issues', [
                    'roadmap_item_id' => $itemId,
                    'issue_id' => (int) $issueId,
                ]);
            }
        }

        // Create dependencies if provided
        if (!empty($data['dependencies'])) {
            foreach ((array) $data['dependencies'] as $dependency) {
                Database::insert('roadmap_dependencies', [
                    'item_id' => $itemId,
                    'depends_on_item_id' => (int) $dependency['depends_on_item_id'],
                    'dependency_type' => $dependency['dependency_type'] ?? 'depends_on',
                ]);
            }
        }

        return $this->getRoadmapItem($itemId) ?? [];
    }

    /**
     * Update roadmap item
     */
    public function updateRoadmapItem(int $itemId, array $data, int $userId): array
    {
        // Validate dates if provided
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
                throw new \InvalidArgumentException('Start date must be before end date');
            }
        }

        $updateData = array_filter([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => $data['status'] ?? null,
            'priority' => $data['priority'] ?? null,
            'owner_id' => $data['owner_id'] ?? null,
            'color' => $data['color'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($updateData)) {
            Database::update('roadmap_items', $updateData, 'id = ?', [$itemId]);
        }

        // Update sprint links if provided
        if (isset($data['sprint_ids'])) {
            Database::delete('roadmap_item_sprints', 'roadmap_item_id = ?', [$itemId]);
            foreach ((array) $data['sprint_ids'] as $sprintId) {
                Database::insert('roadmap_item_sprints', [
                    'roadmap_item_id' => $itemId,
                    'sprint_id' => (int) $sprintId,
                ]);
            }
        }

        // Update issue links if provided
        if (isset($data['issue_ids'])) {
            Database::delete('roadmap_item_issues', 'roadmap_item_id = ?', [$itemId]);
            foreach ((array) $data['issue_ids'] as $issueId) {
                Database::insert('roadmap_item_issues', [
                    'roadmap_item_id' => $itemId,
                    'issue_id' => (int) $issueId,
                ]);
            }
        }

        return $this->getRoadmapItem($itemId) ?? [];
    }

    /**
     * Delete roadmap item (cascades to dependencies and relationships)
     */
    public function deleteRoadmapItem(int $itemId): bool
    {
        return Database::delete('roadmap_items', 'id = ?', [$itemId]) > 0;
    }

    /**
     * Calculate progress percentage for a roadmap item based on linked issues
     */
    private function calculateItemProgress(int $itemId): array
    {
        $stats = Database::selectOne(
            "SELECT COUNT(*) as total_issues,
                    SUM(CASE WHEN s.category = 'done' THEN 1 ELSE 0 END) as completed_issues
             FROM roadmap_item_issues roi
             INNER JOIN issues i ON roi.issue_id = i.id
             LEFT JOIN statuses s ON i.status_id = s.id
             WHERE roi.roadmap_item_id = ?",
            [$itemId]
        );

        $total = (int) ($stats['total_issues'] ?? 0);
        $completed = (int) ($stats['completed_issues'] ?? 0);
        $percentage = $total > 0 ? (int) (($completed / $total) * 100) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => $percentage,
        ];
    }

    /**
     * Calculate cost impact for a roadmap item based on time logs (worklogs)
     */
    private function calculateItemCost(int $itemId): array
    {
        try {
            // Get all issues linked to this roadmap item
            $timeLogs = Database::select(
                "SELECT w.time_spent, us.hourly_rate
                 FROM worklogs w
                 INNER JOIN roadmap_item_issues roi ON w.issue_id = roi.issue_id
                 LEFT JOIN user_settings us ON w.user_id = us.user_id
                 WHERE roi.roadmap_item_id = ?",
                [$itemId]
            );

            $totalMinutes = 0;
            $totalCost = 0.0;

            foreach ($timeLogs as $log) {
                // Convert time_spent (in seconds) to minutes
                $minutes = (int) $log['time_spent'] / 60;
                $totalMinutes += $minutes;
                if ($log['hourly_rate']) {
                    $hours = $minutes / 60;
                    $totalCost += $hours * (float) $log['hourly_rate'];
                }
            }

            return [
                'total_minutes' => $totalMinutes,
                'total_hours' => $totalMinutes / 60,
                'estimated_cost' => $totalCost,
            ];
        } catch (\Exception $e) {
            // Return empty cost if worklogs table has issues
            return [
                'total_minutes' => 0,
                'total_hours' => 0,
                'estimated_cost' => 0.0,
            ];
        }
    }

    /**
     * Check for delayed roadmap items based on dependencies
     */
    public function checkRiskStatus(int $projectId): array
    {
        $atRiskItems = [];

        // Get all roadmap items
        $items = $this->getProjectRoadmap($projectId);

        foreach ($items as $item) {
            $isAtRisk = false;

            // Check if any dependency is delayed
            foreach ($item['dependencies'] as $dependency) {
                if ($dependency['depends_on_status'] === 'delayed') {
                    $isAtRisk = true;
                    break;
                }
            }

            // Check if past due date
            if (strtotime($item['end_date']) < time() && $item['status'] !== 'completed') {
                $isAtRisk = true;
            }

            if ($isAtRisk && $item['status'] !== 'at_risk' && $item['status'] !== 'delayed') {
                $atRiskItems[] = $item;
            }
        }

        return $atRiskItems;
    }

    /**
     * Get project roadmap summary with metrics
     */
    public function getRoadmapSummary(int $projectId): array
    {
        $items = $this->getProjectRoadmap($projectId);

        $statusCounts = array_fill_keys(
            ['planned', 'in_progress', 'on_track', 'at_risk', 'delayed', 'completed'],
            0
        );

        $totalProgress = 0;
        $totalEstimatedHours = 0;
        $totalActualHours = 0;
        $totalIssues = 0;
        $completedIssues = 0;

        foreach ($items as $item) {
            $statusCounts[$item['status']] = ($statusCounts[$item['status']] ?? 0) + 1;
            $totalProgress += $item['progress_percentage'];
            $totalEstimatedHours += (float) $item['estimated_hours'];
            $totalActualHours += (float) $item['actual_hours'];
            $totalIssues += $item['progress']['total'];
            $completedIssues += $item['progress']['completed'];
        }

        $itemCount = count($items);
        $avgProgress = $itemCount > 0 ? (int) ($totalProgress / $itemCount) : 0;

        return [
            'total_items' => $itemCount,
            'status_counts' => $statusCounts,
            'average_progress' => $avgProgress,
            'total_estimated_hours' => $totalEstimatedHours,
            'total_actual_hours' => $totalActualHours,
            'total_issues' => $totalIssues,
            'completed_issues' => $completedIssues,
            'issue_completion_rate' => $totalIssues > 0 ? (int) (($completedIssues / $totalIssues) * 100) : 0,
            'at_risk_count' => $statusCounts['at_risk'] + $statusCounts['delayed'],
        ];
    }

    /**
     * Get timeline range for Gantt chart
     */
    public function getTimelineRange(int $projectId): array
    {
        $range = Database::selectOne(
            "SELECT MIN(start_date) as earliest_date, MAX(end_date) as latest_date
             FROM roadmap_items
             WHERE project_id = ?",
            [$projectId]
        );

        if (!$range || !$range['earliest_date']) {
            $today = new \DateTime();
            return [
                'start_date' => $today->format('Y-m-d'),
                'end_date' => $today->modify('+90 days')->format('Y-m-d'),
            ];
        }

        return [
            'start_date' => $range['earliest_date'],
            'end_date' => $range['latest_date'],
        ];
    }
}
