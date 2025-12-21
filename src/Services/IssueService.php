<?php
/**
 * Issue Service
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Services\NotificationService;

class IssueService
{
    public function getIssues(array $filters = [], string $orderBy = 'created_at', string $order = 'DESC', int $page = 1, int $perPage = 25): array
    {
        $where = ['1 = 1'];
        $params = [];

        if (!empty($filters['project_id'])) {
            $where[] = "i.project_id = :project_id";
            $params['project_id'] = $filters['project_id'];
        }

        if (!empty($filters['issue_type_id'])) {
            $where[] = "i.issue_type_id = :issue_type_id";
            $params['issue_type_id'] = $filters['issue_type_id'];
        }

        if (!empty($filters['status_id'])) {
            if (is_array($filters['status_id'])) {
                $placeholders = [];
                foreach ($filters['status_id'] as $idx => $statusId) {
                    $key = "status_id_$idx";
                    $placeholders[] = ":$key";
                    $params[$key] = $statusId;
                }
                $where[] = "i.status_id IN (" . implode(', ', $placeholders) . ")";
            } else {
                $where[] = "i.status_id = :status_id";
                $params['status_id'] = $filters['status_id'];
            }
        }

        if (!empty($filters['priority_id'])) {
            $where[] = "i.priority_id = :priority_id";
            $params['priority_id'] = $filters['priority_id'];
        }

        if (!empty($filters['assignee_id'])) {
            if ($filters['assignee_id'] === 'unassigned') {
                $where[] = "i.assignee_id IS NULL";
            } else {
                $where[] = "i.assignee_id = :assignee_id";
                $params['assignee_id'] = $filters['assignee_id'];
            }
        }

        if (!empty($filters['reporter_id'])) {
            $where[] = "i.reporter_id = :reporter_id";
            $params['reporter_id'] = $filters['reporter_id'];
        }

        if (!empty($filters['sprint_id'])) {
            if ($filters['sprint_id'] === 'backlog') {
                $where[] = "i.sprint_id IS NULL";
            } else {
                $where[] = "i.sprint_id = :sprint_id";
                $params['sprint_id'] = $filters['sprint_id'];
            }
        }

        if (!empty($filters['epic_id'])) {
            $where[] = "i.epic_id = :epic_id";
            $params['epic_id'] = $filters['epic_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(i.summary LIKE :search OR i.issue_key LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['labels'])) {
            $where[] = "EXISTS (SELECT 1 FROM issue_labels il 
                        JOIN labels l ON il.label_id = l.id 
                        WHERE il.issue_id = i.id AND l.name IN (" . 
                        implode(',', array_map(fn($i) => ":label_$i", range(0, count($filters['labels']) - 1))) . 
                        "))";
            foreach ($filters['labels'] as $idx => $label) {
                $params["label_$idx"] = $label;
            }
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $allowedOrderBy = ['created_at', 'updated_at', 'priority_id', 'due_date', 'issue_key', 'summary'];
        $orderBy = in_array($orderBy, $allowedOrderBy) ? $orderBy : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issues i WHERE $whereClause",
            $params
        );

        $issues = Database::select(
            "SELECT i.*, 
                    p.key as project_key, p.name as project_name,
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                    s.name as status_name, s.category as status_category, s.color as status_color,
                    ip.name as priority_name, ip.icon as priority_icon, ip.color as priority_color,
                    reporter.display_name as reporter_name, reporter.avatar as reporter_avatar,
                    assignee.display_name as assignee_name, assignee.avatar as assignee_avatar
             FROM issues i
             JOIN projects p ON i.project_id = p.id
             JOIN issue_types it ON i.issue_type_id = it.id
             JOIN statuses s ON i.status_id = s.id
             LEFT JOIN issue_priorities ip ON i.priority_id = ip.id
             LEFT JOIN users reporter ON i.reporter_id = reporter.id
             LEFT JOIN users assignee ON i.assignee_id = assignee.id
             WHERE $whereClause
             ORDER BY i.$orderBy $order
             LIMIT $perPage OFFSET $offset",
            $params
        );

        return [
            'data' => $issues,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function getIssueByKey(string $issueKey): ?array
    {
        $issue = Database::selectOne(
            "SELECT i.*, 
                    p.key as project_key, p.name as project_name,
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color, it.is_subtask,
                    s.name as status_name, s.category as status_category, s.color as status_color,
                    ip.name as priority_name, ip.icon as priority_icon, ip.color as priority_color,
                    reporter.display_name as reporter_name, reporter.avatar as reporter_avatar, reporter.email as reporter_email,
                    assignee.display_name as assignee_name, assignee.avatar as assignee_avatar, assignee.email as assignee_email,
                    parent.issue_key as parent_key, parent.summary as parent_summary,
                    epic.issue_key as epic_key, epic.summary as epic_summary,
                    sp.name as sprint_name, sp.status as sprint_status
             FROM issues i
             JOIN projects p ON i.project_id = p.id
             JOIN issue_types it ON i.issue_type_id = it.id
             JOIN statuses s ON i.status_id = s.id
             LEFT JOIN issue_priorities ip ON i.priority_id = ip.id
             LEFT JOIN users reporter ON i.reporter_id = reporter.id
             LEFT JOIN users assignee ON i.assignee_id = assignee.id
             LEFT JOIN issues parent ON i.parent_id = parent.id
             LEFT JOIN issues epic ON i.epic_id = epic.id
             LEFT JOIN sprints sp ON i.sprint_id = sp.id
             WHERE i.issue_key = :issue_key",
            ['issue_key' => $issueKey]
        );

        if (!$issue) {
            return null;
        }

        $issue['labels'] = Database::select(
            "SELECT l.* FROM labels l
             JOIN issue_labels il ON l.id = il.label_id
             WHERE il.issue_id = :issue_id",
            ['issue_id' => $issue['id']]
        );

        $issue['components'] = Database::select(
            "SELECT c.* FROM components c
             JOIN issue_components ic ON c.id = ic.component_id
             WHERE ic.issue_id = :issue_id",
            ['issue_id' => $issue['id']]
        );

        $issue['fix_versions'] = Database::select(
            "SELECT v.* FROM versions v
             JOIN issue_versions iv ON v.id = iv.version_id
             WHERE iv.issue_id = :issue_id AND iv.type = 'fix'",
            ['issue_id' => $issue['id']]
        );

        $issue['affects_versions'] = Database::select(
            "SELECT v.* FROM versions v
             JOIN issue_versions iv ON v.id = iv.version_id
             WHERE iv.issue_id = :issue_id AND iv.type = 'affects'",
            ['issue_id' => $issue['id']]
        );

        $issue['watchers_count'] = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issue_watchers WHERE issue_id = :issue_id",
            ['issue_id' => $issue['id']]
        );

        $issue['votes_count'] = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issue_votes WHERE issue_id = :issue_id",
            ['issue_id' => $issue['id']]
        );

        // Load comments using raw PDO to avoid parameter binding issues
        try {
            $pdo = Database::getConnection();
            $sql = "SELECT c.*, u.display_name as author_name, u.avatar as author_avatar, u.id as author_id,
                           u.first_name, u.last_name, u.email
                    FROM comments c
                    JOIN users u ON c.user_id = u.id
                    WHERE c.issue_id = " . (int)$issue['id'] . "
                    ORDER BY c.created_at DESC";
            
            $stmt = $pdo->query($sql);
            $issue['comments'] = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            // If comment loading fails, just use empty array
            error_log('Failed to load comments: ' . $e->getMessage());
            $issue['comments'] = [];
        }

        // Transform flat comments into nested structure for view compatibility
        $issue['comments'] = array_map(function ($comment) {
            return [
                'id' => $comment['id'],
                'body' => $comment['body'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'user_id' => $comment['user_id'],
                'user' => [
                    'id' => $comment['author_id'],
                    'display_name' => $comment['author_name'],
                    'first_name' => $comment['first_name'],
                    'last_name' => $comment['last_name'],
                    'avatar' => $comment['author_avatar'],
                    'email' => $comment['email'],
                ]
            ];
        }, $issue['comments']);

        return $issue;
    }

    public function getIssueById(int $id): ?array
    {
        $issueKey = Database::selectValue("SELECT `issue_key` FROM issues WHERE id = ?", [$id]);
        if ($issueKey) {
            return $this->getIssueByKey($issueKey);
        }
        return null;
    }



    public function updateIssue(int $issueId, array $data, int $userId): array
    {
        $issue = $this->getIssueById($issueId);
        if (!$issue) {
            throw new \InvalidArgumentException('Issue not found');
        }

        return Database::transaction(function () use ($issueId, $issue, $data, $userId) {
            $updateFields = ['summary', 'description', 'priority_id', 'issue_type_id', 
                            'assignee_id', 'epic_id', 'story_points', 'original_estimate',
                            'remaining_estimate', 'environment', 'due_date'];

            $updateData = [];
            foreach ($updateFields as $field) {
                if (array_key_exists($field, $data)) {
                    // Convert empty strings to NULL for foreign key fields
                    $value = $data[$field];
                    if (in_array($field, ['assignee_id', 'epic_id', 'priority_id', 'issue_type_id']) 
                        && ($value === '' || $value === null)) {
                        // For optional FK fields (assignee, epic), allow NULL
                        if (in_array($field, ['assignee_id', 'epic_id'])) {
                            $value = null;
                        }
                    }
                    
                    $updateData[$field] = $value;
                    
                    if ($issue[$field] != $value) {
                        $this->recordHistory($issueId, $userId, $field, $issue[$field], $value);
                    }
                }
            }

            if (!empty($updateData)) {
                Database::update('issues', $updateData, 'id = ?', [$issueId]);
            }

            if (array_key_exists('labels', $data)) {
                $this->syncLabels($issueId, $data['labels'] ?? [], $issue['project_id']);
            }

            if (array_key_exists('components', $data)) {
                $this->syncComponents($issueId, $data['components'] ?? []);
            }

            if (array_key_exists('fix_versions', $data)) {
                $this->syncVersions($issueId, $data['fix_versions'] ?? [], 'fix');
            }

            $this->logAudit('issue_updated', 'issue', $issueId, $issue, $data, $userId);

            return $this->getIssueById($issueId);
        });
    }

    public function deleteIssue(int $issueId, int $userId): bool
    {
        $issue = $this->getIssueById($issueId);
        if (!$issue) {
            throw new \InvalidArgumentException('Issue not found');
        }

        $this->logAudit('issue_deleted', 'issue', $issueId, $issue, null, $userId);

        $deleted = Database::delete('issues', 'id = ?', [$issueId]) > 0;
        
        if ($deleted) {
            // Decrement the issue count in the project
            Database::query(
                "UPDATE projects SET issue_count = issue_count - 1 WHERE id = ?",
                [$issue['project_id']]
            );
        }
        
        return $deleted;
    }

    public function transitionIssue(int $issueId, int $targetStatusId, int $userId): array
    {
        $issue = $this->getIssueById($issueId);
        if (!$issue) {
            throw new \InvalidArgumentException('Issue not found');
        }

        if (!$this->isTransitionAllowed($issue['status_id'], $targetStatusId, $issue['project_id'])) {
            throw new \InvalidArgumentException('This transition is not allowed');
        }

        $targetStatus = Database::selectOne("SELECT * FROM statuses WHERE id = ?", [$targetStatusId]);
        if (!$targetStatus) {
            throw new \InvalidArgumentException('Target status not found');
        }

        $updateData = ['status_id' => $targetStatusId];
        
        if ($targetStatus['category'] === 'done' && !$issue['resolved_at']) {
            $updateData['resolved_at'] = date('Y-m-d H:i:s');
        } elseif ($targetStatus['category'] !== 'done' && $issue['resolved_at']) {
            $updateData['resolved_at'] = null;
        }

        Database::update('issues', $updateData, 'id = ?', [$issueId]);

        $this->recordHistory($issueId, $userId, 'status', $issue['status_name'], $targetStatus['name']);
        $this->logAudit('issue_transitioned', 'issue', $issueId, 
            ['status_id' => $issue['status_id']], 
            ['status_id' => $targetStatusId], 
            $userId
        );

        return $this->getIssueById($issueId);
    }

    public function assignIssue(int $issueId, ?int $assigneeId, int $userId): array
    {
        $issue = $this->getIssueById($issueId);
        if (!$issue) {
            throw new \InvalidArgumentException('Issue not found');
        }

        Database::update('issues', ['assignee_id' => $assigneeId], 'id = ?', [$issueId]);

        $oldAssignee = $issue['assignee_name'] ?? 'Unassigned';
        $newAssignee = $assigneeId 
            ? Database::selectValue("SELECT display_name FROM users WHERE id = ?", [$assigneeId]) ?? 'Unknown'
            : 'Unassigned';

        $this->recordHistory($issueId, $userId, 'assignee', $oldAssignee, $newAssignee);
        $this->logAudit('issue_assigned', 'issue', $issueId, 
            ['assignee_id' => $issue['assignee_id']], 
            ['assignee_id' => $assigneeId], 
            $userId
        );

        if ($assigneeId) {
            $this->watchIssue($issueId, $assigneeId);
        }

        return $this->getIssueById($issueId);
    }

    public function watchIssue(int $issueId, int $userId): bool
    {
        $existing = Database::selectOne(
            "SELECT 1 FROM issue_watchers WHERE issue_id = ? AND user_id = ?",
            [$issueId, $userId]
        );

        if (!$existing) {
            Database::insert('issue_watchers', [
                'issue_id' => $issueId,
                'user_id' => $userId,
            ]);
            return true;
        }

        return false;
    }

    public function unwatchIssue(int $issueId, int $userId): bool
    {
        return Database::delete(
            'issue_watchers',
            'issue_id = ? AND user_id = ?',
            [$issueId, $userId]
        ) > 0;
    }

    public function isWatching(int $issueId, int $userId): bool
    {
        return Database::selectOne(
            "SELECT 1 FROM issue_watchers WHERE issue_id = ? AND user_id = ?",
            [$issueId, $userId]
        ) !== null;
    }

    public function voteIssue(int $issueId, int $userId): bool
    {
        $existing = Database::selectOne(
            "SELECT 1 FROM issue_votes WHERE issue_id = ? AND user_id = ?",
            [$issueId, $userId]
        );

        if (!$existing) {
            Database::insert('issue_votes', [
                'issue_id' => $issueId,
                'user_id' => $userId,
            ]);
            return true;
        }

        return false;
    }

    public function unvoteIssue(int $issueId, int $userId): bool
    {
        return Database::delete(
            'issue_votes',
            'issue_id = ? AND user_id = ?',
            [$issueId, $userId]
        ) > 0;
    }

    public function hasVoted(int $issueId, int $userId): bool
    {
        return Database::selectOne(
            "SELECT 1 FROM issue_votes WHERE issue_id = ? AND user_id = ?",
            [$issueId, $userId]
        ) !== null;
    }

    public function linkIssues(int $sourceIssueId, int $targetIssueId, int $linkTypeId, int $userId): array
    {
        $existing = Database::selectOne(
            "SELECT 1 FROM issue_links 
             WHERE source_issue_id = ? AND target_issue_id = ? AND link_type_id = ?",
            [$sourceIssueId, $targetIssueId, $linkTypeId]
        );

        if ($existing) {
            throw new \InvalidArgumentException('Link already exists');
        }

        Database::insert('issue_links', [
            'source_issue_id' => $sourceIssueId,
            'target_issue_id' => $targetIssueId,
            'link_type_id' => $linkTypeId,
            'created_by' => $userId,
        ]);

        $this->logAudit('issue_linked', 'issue', $sourceIssueId, null, [
            'target_issue_id' => $targetIssueId,
            'link_type_id' => $linkTypeId,
        ], $userId);

        return $this->getIssueLinks($sourceIssueId);
    }

    public function unlinkIssues(int $linkId, int $userId): bool
    {
        $link = Database::selectOne("SELECT * FROM issue_links WHERE id = ?", [$linkId]);
        if (!$link) {
            throw new \InvalidArgumentException('Link not found');
        }

        $this->logAudit('issue_unlinked', 'issue', $link['source_issue_id'], $link, null, $userId);

        return Database::delete('issue_links', 'id = ?', [$linkId]) > 0;
    }

    public function getIssueLinks(int $issueId): array
    {
        $outward = Database::select(
            "SELECT il.*, ilt.name as link_type, ilt.outward_description as description,
                    i.issue_key, i.summary, s.name as status_name, s.color as status_color,
                    it.name as issue_type_name, it.icon as issue_type_icon
             FROM issue_links il
             JOIN issue_link_types ilt ON il.link_type_id = ilt.id
             JOIN issues i ON il.target_issue_id = i.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_types it ON i.issue_type_id = it.id
             WHERE il.source_issue_id = ?",
            [$issueId]
        );

        $inward = Database::select(
            "SELECT il.*, ilt.name as link_type, ilt.inward_description as description,
                    i.issue_key, i.summary, s.name as status_name, s.color as status_color,
                    it.name as issue_type_name, it.icon as issue_type_icon
             FROM issue_links il
             JOIN issue_link_types ilt ON il.link_type_id = ilt.id
             JOIN issues i ON il.source_issue_id = i.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_types it ON i.issue_type_id = it.id
             WHERE il.target_issue_id = ?",
            [$issueId]
        );

        return ['outward' => $outward, 'inward' => $inward];
    }

    public function getIssueHistory(int $issueId): array
    {
        return Database::select(
            "SELECT ih.*, u.display_name as user_name, u.avatar as user_avatar
             FROM issue_history ih
             LEFT JOIN users u ON ih.user_id = u.id
             WHERE ih.issue_id = ?
             ORDER BY ih.created_at DESC",
            [$issueId]
        );
    }

    public function logWork(int $issueId, int $userId, int $timeSpent, string $startedAt, ?string $description = null): array
    {
        $issue = $this->getIssueById($issueId);
        if (!$issue) {
            throw new \InvalidArgumentException('Issue not found');
        }

        Database::insert('worklogs', [
            'issue_id' => $issueId,
            'user_id' => $userId,
            'time_spent' => $timeSpent,
            'started_at' => $startedAt,
            'description' => $description,
        ]);

        $newTimeSpent = ($issue['time_spent'] ?? 0) + $timeSpent;
        $newRemaining = max(0, ($issue['remaining_estimate'] ?? 0) - $timeSpent);

        Database::update('issues', [
            'time_spent' => $newTimeSpent,
            'remaining_estimate' => $newRemaining,
        ], 'id = ?', [$issueId]);

        $this->recordHistory($issueId, $userId, 'time_spent', $issue['time_spent'], $newTimeSpent);

        return $this->getIssueById($issueId);
    }

    public function calculateNextIssueKey(int $projectId): string
    {
        $project = Database::selectOne("SELECT `key` FROM projects WHERE id = ?", [$projectId]);
        if (!$project) {
            throw new \InvalidArgumentException('Project not found');
        }

        // Get the highest issue number for this project to ensure uniqueness
        $lastIssue = Database::selectOne(
            "SELECT MAX(CAST(SUBSTRING(issue_key, LENGTH(?) + 2) AS UNSIGNED)) as max_number 
             FROM issues WHERE project_id = ?",
            [$project['key'], $projectId]
        );
        
        $nextNumber = ($lastIssue['max_number'] ?? 0) + 1;
        return $project['key'] . '-' . $nextNumber;
    }

    private function getNextIssueNumber(int $projectId): int
    {
        $project = Database::selectOne("SELECT issue_count FROM projects WHERE id = ?", [$projectId]);
        return ($project['issue_count'] ?? 0) + 1;
    }

    private function getInitialStatus(int $projectId): ?array
    {
        return Database::selectOne(
            "SELECT s.* FROM statuses s
             JOIN workflow_statuses ws ON s.id = ws.status_id
             JOIN workflows w ON ws.workflow_id = w.id
             WHERE w.is_default = 1 AND ws.is_initial = 1
             LIMIT 1"
        );
    }

    private function getDefaultPriority(): int
    {
        $priority = Database::selectOne("SELECT id FROM issue_priorities WHERE is_default = 1 LIMIT 1");
        return $priority ? $priority['id'] : 1;
    }

    private function getDefaultAssignee(int $projectId): ?int
    {
        $project = Database::selectOne(
            "SELECT default_assignee, lead_id FROM projects WHERE id = ?",
            [$projectId]
        );

        if ($project && $project['default_assignee'] === 'project_lead') {
            return $project['lead_id'];
        }

        return null;
    }

    private function isTransitionAllowed(int $fromStatusId, int $toStatusId, int $projectId): bool
    {
        // Get the default workflow
        $defaultWorkflow = Database::selectOne(
            "SELECT id FROM workflows WHERE is_default = 1"
        );

        if (!$defaultWorkflow) {
            // No default workflow - allow any transition
            return true;
        }

        // Check if ANY workflow transitions are configured
        $transitionCount = Database::selectOne(
            "SELECT COUNT(*) as count FROM workflow_transitions WHERE workflow_id = ?",
            [$defaultWorkflow['id']]
        );

        // If NO transitions configured at all, allow all transitions (setup phase)
        if ($transitionCount['count'] == 0) {
            return true;
        }

        // Transitions ARE configured, so check if this specific transition exists
        $transition = Database::selectOne(
            "SELECT 1 FROM workflow_transitions
             WHERE workflow_id = ?
             AND (from_status_id = ? OR from_status_id IS NULL)
             AND to_status_id = ?",
            [$defaultWorkflow['id'], $fromStatusId, $toStatusId]
        );

        return $transition !== null;
    }

    public function getAvailableTransitions(int $issueId): array
    {
        $issue = Database::selectOne("SELECT status_id, project_id FROM issues WHERE id = ?", [$issueId]);
        if (!$issue) {
            return [];
        }

        return Database::select(
            "SELECT DISTINCT s.id as status_id, s.name as status_name, s.color
             FROM workflow_transitions wt
             JOIN workflows w ON wt.workflow_id = w.id
             JOIN statuses s ON wt.to_status_id = s.id
             WHERE w.is_default = 1 
             AND (wt.from_status_id = ? OR wt.from_status_id IS NULL)
             AND wt.to_status_id != ?",
            [$issue['status_id'], $issue['status_id']]
        );
    }

    private function syncLabels(int $issueId, array $labels, int $projectId): void
    {
        Database::delete('issue_labels', 'issue_id = ?', [$issueId]);

        foreach ($labels as $labelName) {
            $label = Database::selectOne(
                "SELECT id FROM labels WHERE (project_id = ? OR project_id IS NULL) AND name = ?",
                [$projectId, $labelName]
            );

            if (!$label) {
                $labelId = Database::insert('labels', [
                    'project_id' => $projectId,
                    'name' => $labelName,
                ]);
            } else {
                $labelId = $label['id'];
            }

            Database::insert('issue_labels', [
                'issue_id' => $issueId,
                'label_id' => $labelId,
            ]);
        }
    }

    private function syncComponents(int $issueId, array $componentIds): void
    {
        Database::delete('issue_components', 'issue_id = ?', [$issueId]);

        foreach ($componentIds as $componentId) {
            Database::insert('issue_components', [
                'issue_id' => $issueId,
                'component_id' => $componentId,
            ]);
        }
    }

    private function syncVersions(int $issueId, array $versionIds, string $type): void
    {
        Database::delete('issue_versions', 'issue_id = ? AND type = ?', [$issueId, $type]);

        foreach ($versionIds as $versionId) {
            Database::insert('issue_versions', [
                'issue_id' => $issueId,
                'version_id' => $versionId,
                'type' => $type,
            ]);
        }
    }

    /**
     * Sync fix versions for an issue
     */
    private function syncFixVersions(int $issueId, array $versions): void
    {
        Database::delete('issue_fix_versions', 'issue_id = ?', [$issueId]);

        foreach ($versions as $versionId) {
            Database::insert('issue_fix_versions', [
                'issue_id' => $issueId,
                'version_id' => $versionId,
            ]);
        }
    }

    private function validateIssueData(array $data, bool $isCreate): void
    {
        if ($isCreate) {
            if (empty($data['project_id'])) {
                throw new \InvalidArgumentException('Project is required');
            }
            if (empty($data['issue_type_id'])) {
                throw new \InvalidArgumentException('Issue type is required');
            }
            if (empty($data['summary'])) {
                throw new \InvalidArgumentException('Summary is required');
            }
        }

        if (isset($data['summary']) && strlen($data['summary']) > 500) {
            throw new \InvalidArgumentException('Summary must not exceed 500 characters');
        }
    }

    private function recordHistory(int $issueId, int $userId, string $field, mixed $oldValue, mixed $newValue): void
    {
        Database::insert('issue_history', [
            'issue_id' => $issueId,
            'user_id' => $userId,
            'field' => $field,
            'old_value' => is_array($oldValue) ? json_encode($oldValue) : (string) $oldValue,
            'new_value' => is_array($newValue) ? json_encode($newValue) : (string) $newValue,
        ]);
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

    // Helper methods for dropdown data
    public function getPriorities(): array
    {
        return Database::select(
            "SELECT * FROM issue_priorities ORDER BY sort_order ASC"
        );
    }

    public function getIssueTypes(): array
    {
        return Database::select(
            "SELECT * FROM issue_types WHERE is_subtask = 0 ORDER BY sort_order ASC"
        );
    }

    public function getStatuses(): array
    {
        return Database::select(
            "SELECT * FROM statuses ORDER BY sort_order ASC"
        );
    }

    public function getProjects(): array
    {
        return Database::select(
            "SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name ASC"
        );
    }

    public function getProjectWithDetails(int $projectId): ?array
    {
        $project = Database::selectOne("SELECT * FROM projects WHERE id = ?", [$projectId]);
        if (!$project) {
            return null;
        }

        $project['issue_types'] = Database::select(
            "SELECT it.* FROM issue_types it
             WHERE it.is_subtask = 0
             ORDER BY it.sort_order ASC"
        );

        $project['members'] = Database::select(
            "SELECT DISTINCT u.id, u.display_name, u.avatar FROM users u
             JOIN project_members pm ON u.id = pm.user_id
             WHERE pm.project_id = ?
             ORDER BY u.display_name ASC",
            [$projectId]
        );

        $project['labels'] = Database::select(
            "SELECT * FROM labels WHERE project_id = ? OR project_id IS NULL ORDER BY name ASC",
            [$projectId]
        );

        $project['components'] = Database::select(
            "SELECT * FROM components WHERE project_id = ? ORDER BY name ASC",
            [$projectId]
        );

        $project['versions'] = Database::select(
            "SELECT * FROM versions WHERE project_id = ? AND is_archived = 0 ORDER BY release_date DESC",
            [$projectId]
        );

        return $project;
    }

    /**
     * Get all comments for an issue
     */
    public function getComments(int $issueId): array
    {
        $comments = Database::select(
            "SELECT c.id, c.issue_id, c.user_id, c.body, c.created_at, c.updated_at,
                    u.id as author_id, u.display_name as author_name, u.first_name,
                    u.last_name, u.avatar as author_avatar, u.email
             FROM comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.issue_id = ?
             ORDER BY c.created_at DESC",
            [$issueId]
        );

        return array_map(function ($comment) {
            return [
                'id' => $comment['id'],
                'body' => $comment['body'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'user_id' => $comment['user_id'],
                'user' => [
                    'id' => $comment['author_id'],
                    'display_name' => $comment['author_name'],
                    'first_name' => $comment['first_name'],
                    'last_name' => $comment['last_name'],
                    'avatar' => $comment['author_avatar'],
                    'email' => $comment['email'],
                ]
            ];
        }, $comments);
    }

    /**
     * Add a comment to an issue
     */
    public function addComment(int $issueId, string $body, int $userId): array
    {
        if (empty(trim($body))) {
            throw new \InvalidArgumentException('Comment body cannot be empty');
        }

        if (strlen($body) > 50000) {
            throw new \InvalidArgumentException('Comment body is too long (max 50000 characters)');
        }

        $issue = $this->getIssueById($issueId);
        if (!$issue) {
            throw new \InvalidArgumentException('Issue not found');
        }

        $commentId = Database::insert('comments', [
            'issue_id' => $issueId,
            'user_id' => $userId,
            'body' => $body,
        ]);

        // Update issue's updated_at timestamp
        Database::update('issues', [
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$issueId]);

        // Log audit
        $this->logAudit('comment_added', 'issue', $issueId, null, [
            'comment_id' => $commentId,
            'body_length' => strlen($body),
        ], $userId);

        // Dispatch notification for comment (works for both web form and API endpoints)
        // Uses dispatchCommentAdded to notify both assignee and watchers
        NotificationService::dispatchCommentAdded($issueId, $userId, $commentId);

        // Retrieve the full comment with user info
        $comments = $this->getComments($issueId);
        foreach ($comments as $comment) {
            if ($comment['id'] == $commentId) {
                return $comment;
            }
        }

        // Fallback if not found
        return [
            'id' => $commentId,
            'body' => $body,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'user_id' => $userId,
            'user' => Database::selectOne(
                "SELECT id, display_name, first_name, last_name, avatar, email FROM users WHERE id = ?",
                [$userId]
            ) ?: ['display_name' => 'Unknown User'],
        ];
    }

    /**
     * Update a comment
     */
    public function updateComment(int $commentId, string $body, int $userId): array
    {
        if (empty(trim($body))) {
            throw new \InvalidArgumentException('Comment body cannot be empty');
        }

        if (strlen($body) > 50000) {
            throw new \InvalidArgumentException('Comment body is too long (max 50000 characters)');
        }

        $comment = Database::selectOne(
            "SELECT c.*, i.id as issue_id FROM comments c 
             JOIN issues i ON c.issue_id = i.id
             WHERE c.id = ?",
            [$commentId]
        );

        if (!$comment) {
            throw new \InvalidArgumentException('Comment not found');
        }

        // Verify user can edit (author or admin with permission)
        if ($comment['user_id'] !== $userId) {
            // Note: This is a simplified check. In real app, check roles/permissions
            throw new \InvalidArgumentException('You can only edit your own comments');
        }

        $oldBody = $comment['body'];

        Database::update('comments', [
            'body' => $body,
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$commentId]);

        // Log audit
        $this->logAudit('comment_updated', 'issue', $comment['issue_id'], [
            'body' => $oldBody,
        ], [
            'body' => $body,
        ], $userId);

        // Retrieve updated comment
        $updated = Database::selectOne(
            "SELECT c.id, c.issue_id, c.user_id, c.body, c.created_at, c.updated_at,
                    u.id as author_id, u.display_name as author_name, u.first_name,
                    u.last_name, u.avatar as author_avatar, u.email
             FROM comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.id = ?",
            [$commentId]
        );

        return [
            'id' => $updated['id'],
            'body' => $updated['body'],
            'created_at' => $updated['created_at'],
            'updated_at' => $updated['updated_at'],
            'user_id' => $updated['user_id'],
            'user' => [
                'id' => $updated['author_id'],
                'display_name' => $updated['author_name'],
                'first_name' => $updated['first_name'],
                'last_name' => $updated['last_name'],
                'avatar' => $updated['author_avatar'],
                'email' => $updated['email'],
            ]
        ];
    }

    /**
     * Delete a comment
     */
    public function deleteComment(int $commentId, int $userId): bool
    {
        $comment = Database::selectOne(
            "SELECT c.*, i.id as issue_id FROM comments c 
             JOIN issues i ON c.issue_id = i.id
             WHERE c.id = ?",
            [$commentId]
        );

        if (!$comment) {
            throw new \InvalidArgumentException('Comment not found');
        }

        // Verify user can delete (author or admin with permission)
        if ($comment['user_id'] !== $userId) {
            // Note: This is a simplified check. In real app, check roles/permissions
            throw new \InvalidArgumentException('You can only delete your own comments');
        }

        $deleted = Database::delete('comments', 'id = ?', [$commentId]) > 0;

        if ($deleted) {
            // Log audit
            $this->logAudit('comment_deleted', 'issue', $comment['issue_id'], [
                'comment_id' => $commentId,
                'body_length' => strlen($comment['body']),
            ], null, $userId);
        }

        return $deleted;
    }

    /**
     * ✅ NEW: Store attachment for an issue (used by Quick Create Modal)
     * 
     * @param int $issueId Issue ID
     * @param array $file File from $_FILES array with structure: ['name' => 'test.pdf', 'tmp_name' => '/path', 'type' => 'application/pdf', 'size' => 1024, 'error' => 0]
     * @param int $userId User ID of uploader
     * @return array|null Attachment record or null if failed
     */
    public function storeAttachment(int $issueId, array $file, int $userId): ?array
    {
        // Validate file array structure
        if (!isset($file['name'], $file['tmp_name'], $file['type'], $file['size'], $file['error'])) {
            return null;
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Validate file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            return null;
        }

        // Validate MIME type (whitelist common file types)
        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/zip',
            'video/quicktime',
            'video/mp4',
            'video/webm',
            'image/webp',
        ];

        if (!in_array($file['type'], $allowedMimes, true)) {
            return null;
        }

        try {
            // Generate unique filename to prevent collisions
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $uniqueName = uniqid('attachment_', true) . '.' . $extension;
            $uploadDir = public_path('/uploads/attachments');

            // Ensure upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destinationPath = $uploadDir . '/' . $uniqueName;

            // Move uploaded file to destination
            if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
                return null;
            }

            // Get issue to update timestamp
            $issue = Database::selectOne('SELECT id FROM issues WHERE id = ?', [$issueId]);
            if (!$issue) {
                @unlink($destinationPath);  // Clean up if issue doesn't exist
                return null;
            }

            // Store attachment metadata in database
            $attachmentId = Database::insert('issue_attachments', [
                'issue_id' => $issueId,
                'author_id' => $userId,
                'filename' => $uniqueName,
                'original_name' => $file['name'],
                'mime_type' => $file['type'],
                'file_size' => (int) $file['size'],
                'file_path' => '/uploads/attachments/' . $uniqueName,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if (!$attachmentId) {
                @unlink($destinationPath);  // Clean up if DB insert fails
                return null;
            }

            // Update issue timestamp
            Database::update('issues', [
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$issueId]);

            // Log in issue history
            Database::insert('issue_history', [
                'issue_id' => $issueId,
                'user_id' => $userId,
                'field' => 'attachment',
                'new_value' => $file['name'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Return attachment record
            return Database::selectOne(
                'SELECT a.*, u.display_name as author_name 
                 FROM issue_attachments a
                 JOIN users u ON a.author_id = u.id
                 WHERE a.id = ?',
                [$attachmentId]
            );
        } catch (\Exception $e) {
            // Log error
            error_log("Attachment storage failed for issue {$issueId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new issue
     */
    public function createIssue(array $data, int $userId): array
    {
        // Validate required fields
        if (empty($data['project_id'])) {
            throw new \InvalidArgumentException('Project ID is required');
        }
        if (empty($data['issue_type_id'])) {
            throw new \InvalidArgumentException('Issue type is required');
        }
        if (empty($data['summary'])) {
            throw new \InvalidArgumentException('Summary is required');
        }

        // Validate project exists
        $project = Database::selectOne("SELECT * FROM projects WHERE id = ?", [$data['project_id']]);
        if (!$project) {
            throw new \InvalidArgumentException('Invalid project');
        }

        // Validate issue type exists
        $issueType = Database::selectOne("SELECT * FROM issue_types WHERE id = ?", [$data['issue_type_id']]);
        if (!$issueType) {
            throw new \InvalidArgumentException('Invalid issue type');
        }

        // Get next issue number for the project
        $nextNumber = Database::selectOne(
            "SELECT MAX(issue_number) + 1 as next_number FROM issues WHERE project_id = ?",
            [$data['project_id']]
        );
        $issueNumber = $nextNumber['next_number'] ?? 1;

        // Generate issue key
        $issueKey = $project['key'] . '-' . $issueNumber;

        // Get default status for new issues (To Do)
        $defaultStatus = Database::selectOne(
            "SELECT * FROM statuses WHERE name = 'To Do' LIMIT 1"
        );
        $statusId = $defaultStatus['id'] ?? 1;

        // Get default priority if not provided
        $priorityId = $data['priority_id'] ?? null;
        if (!$priorityId) {
            $defaultPriority = Database::selectOne(
                "SELECT * FROM issue_priorities WHERE is_default = 1 LIMIT 1"
            );
            $priorityId = $defaultPriority['id'] ?? null;
        }

        try {
            // Insert issue
            $issueId = Database::insert('issues', [
                'project_id' => $data['project_id'],
                'issue_type_id' => $data['issue_type_id'],
                'status_id' => $statusId,
                'priority_id' => $priorityId,
                'issue_key' => $issueKey,
                'issue_number' => $issueNumber,
                'summary' => $data['summary'],
                'description' => $data['description'] ?? null,
                'reporter_id' => $userId,
                'assignee_id' => $data['assignee_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'epic_id' => $data['epic_id'] ?? null,
                'sprint_id' => $data['sprint_id'] ?? null,
                'story_points' => $data['story_points'] ?? null,
                'original_estimate' => $data['original_estimate'] ?? null,
                'remaining_estimate' => $data['original_estimate'] ?? null, // Initially set to original
                'time_spent' => 0,
                'environment' => $data['environment'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if (!$issueId) {
                throw new \Exception('Failed to create issue');
            }

            // Handle labels if provided
            if (!empty($data['labels']) && is_array($data['labels'])) {
                $this->syncLabels($issueId, $data['labels'], $data['project_id']);
            }

            // Handle components if provided
            if (!empty($data['components']) && is_array($data['components'])) {
                $this->syncComponents($issueId, $data['components']);
            }

            // Handle fix versions if provided
            if (!empty($data['fix_versions']) && is_array($data['fix_versions'])) {
                $this->syncFixVersions($issueId, $data['fix_versions']);
            }

            // Add to sprint if specified
            if (!empty($data['sprint_id'])) {
                Database::insert('sprint_issues', [
                    'sprint_id' => $data['sprint_id'],
                    'issue_id' => $issueId,
                    'added_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Log creation in audit trail
            $this->logAudit('issue_created', 'issue', $issueId, null, [
                'project_id' => $data['project_id'],
                'issue_key' => $issueKey,
                'summary' => $data['summary']
            ], $userId);

            // Get complete issue data
            $issue = Database::selectOne(
                "SELECT i.*, 
                        p.name as project_name, p.key as project_key,
                        it.name as issue_type_name, it.icon as issue_type_icon,
                        s.name as status_name, s.color as status_color,
                        pr.name as priority_name, pr.color as priority_color,
                        reporter.display_name as reporter_name, reporter.avatar as reporter_avatar,
                        assignee.display_name as assignee_name, assignee.avatar as assignee_avatar
                 FROM issues i
                 JOIN projects p ON i.project_id = p.id
                 JOIN issue_types it ON i.issue_type_id = it.id
                 JOIN statuses s ON i.status_id = s.id
                 LEFT JOIN issue_priorities pr ON i.priority_id = pr.id
                 LEFT JOIN users reporter ON i.reporter_id = reporter.id
                 LEFT JOIN users assignee ON i.assignee_id = assignee.id
                 WHERE i.id = ?",
                [$issueId]
            );

            return $issue;

        } catch (\Exception $e) {
            error_log("Issue creation failed: " . $e->getMessage());
            throw new \Exception('Failed to create issue: ' . $e->getMessage());
        }
    }
}
