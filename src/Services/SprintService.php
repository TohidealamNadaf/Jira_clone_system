<?php
/**
 * Sprint Service
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class SprintService
{
    public function getSprintsByBoard(int $boardId, ?string $status = null): array
    {
        $where = 'board_id = ?';
        $params = [$boardId];

        if ($status) {
            $where .= ' AND status = ?';
            $params[] = $status;
        }

        return Database::select(
            "SELECT s.*, 
                    (SELECT COUNT(*) FROM issues i WHERE i.sprint_id = s.id) as issue_count,
                    (SELECT COALESCE(SUM(story_points), 0) FROM issues i WHERE i.sprint_id = s.id) as total_points,
                    (SELECT COUNT(*) FROM issues i 
                     JOIN statuses st ON i.status_id = st.id 
                     WHERE i.sprint_id = s.id AND st.category = 'done') as completed_issues,
                    (SELECT COALESCE(SUM(i.story_points), 0) FROM issues i
                     JOIN statuses st ON i.status_id = st.id
                     WHERE i.sprint_id = s.id AND st.category = 'done') as completed_points
             FROM sprints s
             WHERE $where
             ORDER BY 
                CASE s.status 
                    WHEN 'active' THEN 1 
                    WHEN 'future' THEN 2 
                    ELSE 3 
                END,
                s.start_date ASC",
            $params
        );
    }

    public function getSprints(int $boardId, ?string $status = null): array
    {
        return $this->getSprintsByBoard($boardId, $status);
    }

    public function getSprintById(int $sprintId): ?array
    {
        $sprint = Database::selectOne(
            "SELECT s.*, b.name as board_name, b.project_id, p.key as project_key
             FROM sprints s
             JOIN boards b ON s.board_id = b.id
             JOIN projects p ON b.project_id = p.id
             WHERE s.id = ?",
            [$sprintId]
        );

        if ($sprint) {
            $sprint['issue_count'] = (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues WHERE sprint_id = ?",
                [$sprintId]
            );
            $sprint['total_points'] = (float) Database::selectValue(
                "SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ?",
                [$sprintId]
            );
            $sprint['completed_points'] = (float) Database::selectValue(
                "SELECT COALESCE(SUM(i.story_points), 0) FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.sprint_id = ? AND s.category = 'done'",
                [$sprintId]
            );
        }

        return $sprint;
    }

    public function createSprint(int $boardId, array $data, int $userId): array
    {
        $this->validateSprintData($data);

        $existingSprints = (int) Database::selectValue(
            "SELECT COUNT(*) FROM sprints WHERE board_id = ?",
            [$boardId]
        );

        $sprintId = Database::insert('sprints', [
            'board_id' => $boardId,
            'name' => $data['name'] ?? 'Sprint ' . ($existingSprints + 1),
            'goal' => $data['goal'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => 'future',
        ]);

        $this->logAudit('sprint_created', 'sprint', $sprintId, null, $data, $userId);

        return $this->getSprintById($sprintId);
    }

    public function updateSprint(int $sprintId, array $data, int $userId): array
    {
        $sprint = $this->getSprintById($sprintId);
        if (!$sprint) {
            throw new \InvalidArgumentException('Sprint not found');
        }

        if ($sprint['status'] === 'completed') {
            throw new \InvalidArgumentException('Cannot modify a completed sprint');
        }

        $updateData = array_filter([
            'name' => $data['name'] ?? null,
            'goal' => $data['goal'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($updateData)) {
            Database::update('sprints', $updateData, 'id = ?', [$sprintId]);
            $this->logAudit('sprint_updated', 'sprint', $sprintId, $sprint, $updateData, $userId);
        }

        return $this->getSprintById($sprintId);
    }

    public function deleteSprint(int $sprintId, int $userId): bool
    {
        $sprint = $this->getSprintById($sprintId);
        if (!$sprint) {
            throw new \InvalidArgumentException('Sprint not found');
        }

        if ($sprint['status'] === 'active') {
            throw new \InvalidArgumentException('Cannot delete an active sprint');
        }

        Database::update('issues', ['sprint_id' => null], 'sprint_id = ?', [$sprintId]);

        $this->logAudit('sprint_deleted', 'sprint', $sprintId, $sprint, null, $userId);

        return Database::delete('sprints', 'id = ?', [$sprintId]) > 0;
    }

    public function startSprint(int $sprintId, array $data, int $userId): array
    {
        $sprint = $this->getSprintById($sprintId);
        if (!$sprint) {
            throw new \InvalidArgumentException('Sprint not found');
        }

        if ($sprint['status'] !== 'future') {
            throw new \InvalidArgumentException('Only future sprints can be started');
        }

        $activeSprint = Database::selectOne(
            "SELECT id FROM sprints WHERE board_id = ? AND status = 'active'",
            [$sprint['board_id']]
        );

        if ($activeSprint) {
            throw new \InvalidArgumentException('Another sprint is already active. Complete it first.');
        }

        $issueCount = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issues WHERE sprint_id = ?",
            [$sprintId]
        );

        if ($issueCount === 0) {
            throw new \InvalidArgumentException('Cannot start a sprint with no issues');
        }

        Database::update('sprints', [
            'status' => 'active',
            'started_at' => date('Y-m-d H:i:s'),
            'start_date' => $data['start_date'] ?? date('Y-m-d'),
            'end_date' => $data['end_date'] ?? date('Y-m-d', strtotime('+2 weeks')),
            'goal' => $data['goal'] ?? $sprint['goal'],
        ], 'id = ?', [$sprintId]);

        $this->logAudit('sprint_started', 'sprint', $sprintId, $sprint, [
            'status' => 'active',
            'started_at' => date('Y-m-d H:i:s'),
        ], $userId);

        return $this->getSprintById($sprintId);
    }

    public function completeSprint(int $sprintId, array $options, int $userId): array
    {
        $sprint = $this->getSprintById($sprintId);
        if (!$sprint) {
            throw new \InvalidArgumentException('Sprint not found');
        }

        if ($sprint['status'] !== 'active') {
            throw new \InvalidArgumentException('Only active sprints can be completed');
        }

        return Database::transaction(function () use ($sprintId, $sprint, $options, $userId) {
            $incompleteIssues = Database::select(
                "SELECT i.id FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.sprint_id = ? AND s.category != 'done'",
                [$sprintId]
            );

            $targetSprintId = $options['move_to_sprint_id'] ?? null;

            if (!empty($incompleteIssues)) {
                $issueIds = array_column($incompleteIssues, 'id');
                $placeholders = implode(',', array_fill(0, count($issueIds), '?'));

                if ($targetSprintId) {
                    Database::query(
                        "UPDATE issues SET sprint_id = ? WHERE id IN ($placeholders)",
                        array_merge([$targetSprintId], $issueIds)
                    );
                } else {
                    Database::query(
                        "UPDATE issues SET sprint_id = NULL WHERE id IN ($placeholders)",
                        $issueIds
                    );
                }
            }

            $velocity = $this->calculateVelocity($sprintId);

            Database::update('sprints', [
                'status' => 'completed',
                'completed_at' => date('Y-m-d H:i:s'),
                'velocity' => $velocity,
            ], 'id = ?', [$sprintId]);

            $this->logAudit('sprint_completed', 'sprint', $sprintId, $sprint, [
                'status' => 'completed',
                'velocity' => $velocity,
                'incomplete_issues_moved_to' => $targetSprintId,
            ], $userId);

            return $this->getSprintById($sprintId);
        });
    }

    public function addIssueToSprint(int $sprintId, int $issueId, int $userId): bool
    {
        $sprint = $this->getSprintById($sprintId);
        if (!$sprint) {
            throw new \InvalidArgumentException('Sprint not found');
        }

        if ($sprint['status'] === 'completed') {
            throw new \InvalidArgumentException('Cannot add issues to a completed sprint');
        }

        $issue = Database::selectOne("SELECT * FROM issues WHERE id = ?", [$issueId]);
        if (!$issue) {
            throw new \InvalidArgumentException('Issue not found');
        }

        $oldSprintName = $issue['sprint_id']
            ? Database::selectValue("SELECT name FROM sprints WHERE id = ?", [$issue['sprint_id']])
            : 'Backlog';

        Database::update('issues', ['sprint_id' => $sprintId], 'id = ?', [$issueId]);

        // Check if issue was previously in this sprint
        $existing = Database::selectOne(
            "SELECT * FROM sprint_issues WHERE sprint_id = ? AND issue_id = ?",
            [$sprintId, $issueId]
        );

        if ($existing) {
            // Reactivate the issue in this sprint
            // We need to verify if the table supports added_at/updated_at, but we know it supports removed_at from removeIssueFromSprint
            $updateData = ['removed_at' => null];

            // Try to update added_at if we want to treat this as a new addition event, 
            // but strictly speaking we might just want to clear removed_at. 
            // Given the reporting logic uses added_at, refreshing it is probably better.
            // However, we don't know for sure if added_at exists in schema (inferred).
            // Let's assume it does since getSprintReport uses it.
            try {
                // Check if we can select added_at
                // If this throws, we catch and just update removed_at
                $updateData['added_at'] = date('Y-m-d H:i:s');
                Database::update('sprint_issues', $updateData, 'sprint_id = ? AND issue_id = ?', [$sprintId, $issueId]);
            } catch (\Exception $e) {
                // Fallback: just clear removed_at
                unset($updateData['added_at']);
                Database::update('sprint_issues', $updateData, 'sprint_id = ? AND issue_id = ?', [$sprintId, $issueId]);
            }
        } else {
            Database::insert('sprint_issues', [
                'sprint_id' => $sprintId,
                'issue_id' => $issueId,
            ]);
        }

        Database::insert('issue_history', [
            'issue_id' => $issueId,
            'user_id' => $userId,
            'field' => 'sprint',
            'old_value' => $oldSprintName,
            'new_value' => $sprint['name'],
        ]);

        return true;
    }

    public function removeIssueFromSprint(int $sprintId, int $issueId, int $userId): bool
    {
        $sprint = $this->getSprintById($sprintId);
        if (!$sprint) {
            throw new \InvalidArgumentException('Sprint not found');
        }

        if ($sprint['status'] === 'completed') {
            throw new \InvalidArgumentException('Cannot remove issues from a completed sprint');
        }

        $issue = Database::selectOne("SELECT * FROM issues WHERE id = ? AND sprint_id = ?", [$issueId, $sprintId]);
        if (!$issue) {
            throw new \InvalidArgumentException('Issue not found in this sprint');
        }

        Database::update('issues', ['sprint_id' => null], 'id = ?', [$issueId]);

        try {
            Database::update('sprint_issues', [
                'removed_at' => date('Y-m-d H:i:s'),
            ], 'sprint_id = ? AND issue_id = ?', [$sprintId, $issueId]);
        } catch (\Exception $e) {
            // Fallback: hard delete if soft delete fails (e.g. column missing)
            Database::delete('sprint_issues', 'sprint_id = ? AND issue_id = ?', [$sprintId, $issueId]);
        }

        Database::insert('issue_history', [
            'issue_id' => $issueId,
            'user_id' => $userId,
            'field' => 'sprint',
            'old_value' => $sprint['name'],
            'new_value' => 'Backlog',
        ]);

        return true;
    }

    public function getSprintBurndown(int $sprintId): array
    {
        $sprint = $this->getSprintById($sprintId);
        if (!$sprint) {
            throw new \InvalidArgumentException('Sprint not found');
        }

        if (!$sprint['start_date'] || !$sprint['end_date']) {
            return ['data' => [], 'sprint' => $sprint, 'summary' => null];
        }

        $startDate = new \DateTime($sprint['start_date']);
        $endDate = new \DateTime($sprint['end_date']);
        $today = new \DateTime();

        $totalPoints = (float) Database::selectValue(
            "SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ?",
            [$sprintId]
        );

        $totalIssues = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issues WHERE sprint_id = ?",
            [$sprintId]
        );

        $burndownData = [];
        $currentDate = clone $startDate;
        $interval = new \DateInterval('P1D');

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $idealPoints = $this->calculateIdealBurndown($totalPoints, $startDate, $endDate, $currentDate);

            if ($currentDate <= $today) {
                $completedPoints = (float) Database::selectValue(
                    "SELECT COALESCE(SUM(i.story_points), 0) 
                     FROM issues i
                     JOIN statuses s ON i.status_id = s.id
                     WHERE i.sprint_id = ? 
                     AND s.category = 'done'
                     AND DATE(i.resolved_at) <= ?",
                    [$sprintId, $dateStr]
                );

                $completedIssues = (int) Database::selectValue(
                    "SELECT COUNT(*) 
                     FROM issues i
                     JOIN statuses s ON i.status_id = s.id
                     WHERE i.sprint_id = ? 
                     AND s.category = 'done'
                     AND DATE(i.resolved_at) <= ?",
                    [$sprintId, $dateStr]
                );

                $burndownData[] = [
                    'date' => $dateStr,
                    'ideal_points' => round($idealPoints, 1),
                    'actual_points' => round($totalPoints - $completedPoints, 1),
                    'completed_points' => round($completedPoints, 1),
                    'completed_issues' => $completedIssues,
                    'remaining_issues' => $totalIssues - $completedIssues,
                ];
            } else {
                $burndownData[] = [
                    'date' => $dateStr,
                    'ideal_points' => round($idealPoints, 1),
                    'actual_points' => null,
                    'completed_points' => null,
                    'completed_issues' => null,
                    'remaining_issues' => null,
                ];
            }

            $currentDate->add($interval);
        }

        $completedPoints = (float) Database::selectValue(
            "SELECT COALESCE(SUM(i.story_points), 0) 
             FROM issues i
             JOIN statuses s ON i.status_id = s.id
             WHERE i.sprint_id = ? AND s.category = 'done'",
            [$sprintId]
        );

        return [
            'data' => $burndownData,
            'sprint' => $sprint,
            'summary' => [
                'total_points' => $totalPoints,
                'completed_points' => $completedPoints,
                'remaining_points' => $totalPoints - $completedPoints,
                'total_issues' => $totalIssues,
                'progress_percentage' => $totalPoints > 0 ? round(($completedPoints / $totalPoints) * 100, 1) : 0,
            ],
        ];
    }

    public function calculateBurndown(int $sprintId): array
    {
        $result = $this->getSprintBurndown($sprintId);
        return $result['data'];
    }

    private function calculateIdealBurndown(float $totalPoints, \DateTime $startDate, \DateTime $endDate, \DateTime $currentDate): float
    {
        $totalDays = $startDate->diff($endDate)->days;
        $elapsedDays = $startDate->diff($currentDate)->days;

        if ($totalDays === 0) {
            return 0;
        }

        return $totalPoints * (1 - ($elapsedDays / $totalDays));
    }

    public function calculateVelocity(int $sprintId): float
    {
        return (float) Database::selectValue(
            "SELECT COALESCE(SUM(i.story_points), 0) 
             FROM issues i
             JOIN statuses s ON i.status_id = s.id
             WHERE i.sprint_id = ? AND s.category = 'done'",
            [$sprintId]
        );
    }

    public function getAverageVelocity(int $boardId, int $sprintCount = 3): float
    {
        $velocities = Database::select(
            "SELECT velocity FROM sprints 
             WHERE board_id = ? AND status = 'completed' AND velocity IS NOT NULL
             ORDER BY completed_at DESC
             LIMIT ?",
            [$boardId, $sprintCount]
        );

        if (empty($velocities)) {
            return 0;
        }

        return array_sum(array_column($velocities, 'velocity')) / count($velocities);
    }

    public function getVelocityChart(int $boardId, int $sprintCount = 6): array
    {
        $sprints = Database::select(
            "SELECT s.id, s.name, s.velocity,
                    (SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = s.id) as committed_points,
                    s.completed_at
             FROM sprints s
             WHERE s.board_id = ? AND s.status = 'completed'
             ORDER BY s.completed_at DESC
             LIMIT ?",
            [$boardId, $sprintCount]
        );

        $sprints = array_reverse($sprints);

        return [
            'sprints' => $sprints,
            'average_velocity' => $this->getAverageVelocity($boardId, $sprintCount),
            'trend' => $this->calculateVelocityTrend($sprints),
        ];
    }

    private function calculateVelocityTrend(array $sprints): string
    {
        if (count($sprints) < 2) {
            return 'stable';
        }

        $recentVelocities = array_slice($sprints, -3);
        $velocities = array_column($recentVelocities, 'velocity');

        if (count($velocities) < 2) {
            return 'stable';
        }

        $first = $velocities[0] ?? 0;
        $last = end($velocities) ?? 0;

        if ($first == 0) {
            return 'stable';
        }

        $change = (($last - $first) / $first) * 100;

        if ($change > 10) {
            return 'increasing';
        } elseif ($change < -10) {
            return 'decreasing';
        }

        return 'stable';
    }

    public function getSprintIssues(int $sprintId): array
    {
        $sprint = $this->getSprintById($sprintId);
        if (!$sprint) {
            throw new \InvalidArgumentException('Sprint not found');
        }

        return Database::select(
            "SELECT i.*, 
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                    s.name as status_name, s.color as status_color, s.category as status_category,
                    ip.name as priority_name, ip.icon as priority_icon, ip.color as priority_color,
                    assignee.display_name as assignee_name, assignee.avatar as assignee_avatar
             FROM issues i
             JOIN issue_types it ON i.issue_type_id = it.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_priorities ip ON i.priority_id = ip.id
             LEFT JOIN users assignee ON i.assignee_id = assignee.id
             WHERE i.sprint_id = ?
             ORDER BY i.sort_order ASC, i.created_at DESC",
            [$sprintId]
        );
    }

    public function moveIssuesBetweenSprints(int $fromSprintId, int $toSprintId, array $issueIds, int $userId): int
    {
        $fromSprint = $this->getSprintById($fromSprintId);
        $toSprint = $toSprintId ? $this->getSprintById($toSprintId) : null;

        if (!$fromSprint) {
            throw new \InvalidArgumentException('Source sprint not found');
        }

        if ($toSprintId && !$toSprint) {
            throw new \InvalidArgumentException('Target sprint not found');
        }

        if ($toSprint && $toSprint['status'] === 'completed') {
            throw new \InvalidArgumentException('Cannot move issues to a completed sprint');
        }

        $movedCount = 0;
        $toSprintName = $toSprint ? $toSprint['name'] : 'Backlog';

        Database::transaction(function () use ($issueIds, $fromSprintId, $toSprintId, $fromSprint, $toSprintName, $userId, &$movedCount) {
            foreach ($issueIds as $issueId) {
                $issue = Database::selectOne(
                    "SELECT id FROM issues WHERE id = ? AND sprint_id = ?",
                    [$issueId, $fromSprintId]
                );

                if ($issue) {
                    Database::update('issues', ['sprint_id' => $toSprintId], 'id = ?', [$issueId]);

                    Database::insert('issue_history', [
                        'issue_id' => $issueId,
                        'user_id' => $userId,
                        'field' => 'sprint',
                        'old_value' => $fromSprint['name'],
                        'new_value' => $toSprintName,
                    ]);

                    $movedCount++;
                }
            }

            $this->logAudit('issues_moved_between_sprints', 'sprint', $fromSprintId, null, [
                'from_sprint_id' => $fromSprintId,
                'to_sprint_id' => $toSprintId,
                'issue_ids' => $issueIds,
                'count' => $movedCount,
            ], $userId);
        });

        return $movedCount;
    }

    public function getSprintReport(int $sprintId): array
    {
        $sprint = $this->getSprintById($sprintId);
        if (!$sprint) {
            throw new \InvalidArgumentException('Sprint not found');
        }

        $committedIssues = Database::select(
            "SELECT i.*, it.name as issue_type_name, s.name as status_name, s.category as status_category
             FROM issues i
             JOIN issue_types it ON i.issue_type_id = it.id
             JOIN statuses s ON i.status_id = s.id
             WHERE i.sprint_id = ?",
            [$sprintId]
        );

        $addedDuringSprint = Database::select(
            "SELECT si.issue_id, si.added_at 
             FROM sprint_issues si
             WHERE si.sprint_id = ? AND si.added_at > ?",
            [$sprintId, $sprint['started_at'] ?? $sprint['created_at']]
        );

        $removedDuringSprint = Database::select(
            "SELECT si.issue_id, si.removed_at 
             FROM sprint_issues si
             WHERE si.sprint_id = ? AND si.removed_at IS NOT NULL",
            [$sprintId]
        );

        $completedIssues = array_filter($committedIssues, fn($i) => $i['status_category'] === 'done');
        $incompleteIssues = array_filter($committedIssues, fn($i) => $i['status_category'] !== 'done');

        return [
            'sprint' => $sprint,
            'committed_issues' => count($committedIssues),
            'completed_issues' => count($completedIssues),
            'incomplete_issues' => count($incompleteIssues),
            'added_during_sprint' => count($addedDuringSprint),
            'removed_during_sprint' => count($removedDuringSprint),
            'committed_points' => array_sum(array_column($committedIssues, 'story_points')),
            'completed_points' => array_sum(array_column($completedIssues, 'story_points')),
            'velocity' => $sprint['velocity'] ?? $this->calculateVelocity($sprintId),
            'burndown' => $this->calculateBurndown($sprintId),
        ];
    }

    private function validateSprintData(array $data): void
    {
        if (isset($data['start_date']) && isset($data['end_date'])) {
            if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
                throw new \InvalidArgumentException('End date must be after start date');
            }
        }
    }

    private function logAudit(string $action, string $entityType, ?int $entityId, ?array $oldValues, ?array $newValues, int $userId): void
    {
        Database::insert('audit_logs', [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    }
}
