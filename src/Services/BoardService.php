<?php
/**
 * Board Service
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class BoardService
{
    public function getAllBoards(array $filters = [], int $page = 1, int $perPage = 25): array
    {
        $where = ['1 = 1'];
        $params = [];

        if (!empty($filters['project_id'])) {
            $where[] = "b.project_id = :project_id";
            $params['project_id'] = $filters['project_id'];
        }

        if (!empty($filters['type'])) {
            $where[] = "b.type = :type";
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['owner_id'])) {
            $where[] = "b.owner_id = :owner_id";
            $params['owner_id'] = $filters['owner_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = "b.name LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (isset($filters['is_private'])) {
            $where[] = "b.is_private = :is_private";
            $params['is_private'] = $filters['is_private'] ? 1 : 0;
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM boards b WHERE $whereClause",
            $params
        );

        $boards = Database::select(
            "SELECT b.*, 
                    p.key as project_key, p.name as project_name,
                    u.display_name as owner_name,
                    (SELECT COUNT(*) FROM sprints s WHERE s.board_id = b.id AND s.status = 'active') as active_sprints,
                    (SELECT COUNT(*) FROM sprints s WHERE s.board_id = b.id) as total_sprints
             FROM boards b
             JOIN projects p ON b.project_id = p.id
             LEFT JOIN users u ON b.owner_id = u.id
             WHERE $whereClause
             ORDER BY b.name ASC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        return [
            'items' => $boards,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    public function getBoards(int $projectId): array
    {
        return Database::select(
            "SELECT b.*, u.display_name as owner_name,
                    (SELECT COUNT(*) FROM sprints s WHERE s.board_id = b.id AND s.status = 'active') as active_sprints
             FROM boards b
             LEFT JOIN users u ON b.owner_id = u.id
             WHERE b.project_id = ?
             ORDER BY b.name ASC",
            [$projectId]
        );
    }

    public function getBoardById(int $boardId): ?array
    {
        $board = Database::selectOne(
            "SELECT b.*, p.key as project_key, p.name as project_name, u.display_name as owner_name
             FROM boards b
             JOIN projects p ON b.project_id = p.id
             LEFT JOIN users u ON b.owner_id = u.id
             WHERE b.id = ?",
            [$boardId]
        );

        if ($board) {
            $board['columns'] = $this->getBoardColumns($boardId);
        }

        return $board;
    }

    public function createBoard(int $projectId, array $data, int $userId): array
    {
        $this->validateBoardData($data);

        $boardId = Database::insert('boards', [
            'project_id' => $projectId,
            'name' => $data['name'],
            'type' => $data['type'] ?? 'scrum',
            'filter_jql' => $data['filter_jql'] ?? null,
            'is_private' => $data['is_private'] ?? false,
            'owner_id' => $userId,
        ]);

        $this->createDefaultColumns($boardId, $data['type'] ?? 'scrum');

        $this->logAudit('board_created', 'board', $boardId, null, $data, $userId);

        return $this->getBoardById($boardId);
    }

    public function updateBoard(int $boardId, array $data, int $userId): array
    {
        $board = $this->getBoardById($boardId);
        if (!$board) {
            throw new \InvalidArgumentException('Board not found');
        }

        $updateData = array_filter([
            'name' => $data['name'] ?? null,
            'filter_jql' => $data['filter_jql'] ?? null,
            'is_private' => $data['is_private'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($updateData)) {
            Database::update('boards', $updateData, 'id = ?', [$boardId]);
            $this->logAudit('board_updated', 'board', $boardId, $board, $updateData, $userId);
        }

        return $this->getBoardById($boardId);
    }

    public function deleteBoard(int $boardId, int $userId): bool
    {
        $board = $this->getBoardById($boardId);
        if (!$board) {
            throw new \InvalidArgumentException('Board not found');
        }

        $this->logAudit('board_deleted', 'board', $boardId, $board, null, $userId);

        return Database::delete('boards', 'id = ?', [$boardId]) > 0;
    }

    public function getBoardWithIssues(int $boardId, ?int $sprintId = null): array
    {
        $board = $this->getBoardById($boardId);
        if (!$board) {
            throw new \InvalidArgumentException('Board not found');
        }

        $columns = $board['columns'];

        foreach ($columns as &$column) {
            $statusIds = json_decode($column['status_ids'] ?? '[]', true);

            if (empty($statusIds)) {
                $column['issues'] = [];
                continue;
            }

            $placeholders = implode(',', array_fill(0, count($statusIds), '?'));
            $params = $statusIds;

            $sql = "SELECT i.*, 
                           it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                           ip.name as priority_name, ip.icon as priority_icon, ip.color as priority_color,
                           assignee.display_name as assignee_name, assignee.avatar as assignee_avatar
                    FROM issues i
                    JOIN issue_types it ON i.issue_type_id = it.id
                    JOIN issue_priorities ip ON i.priority_id = ip.id
                    LEFT JOIN users assignee ON i.assignee_id = assignee.id
                    WHERE i.project_id = ? 
                    AND i.status_id IN ($placeholders)";

            $params = array_merge([$board['project_id']], $statusIds);

            if ($sprintId) {
                $sql .= " AND i.sprint_id = ?";
                $params[] = $sprintId;
            } elseif ($board['type'] === 'scrum') {
                $activeSprint = Database::selectOne(
                    "SELECT id FROM sprints WHERE board_id = ? AND status = 'active' LIMIT 1",
                    [$boardId]
                );
                if ($activeSprint) {
                    $sql .= " AND i.sprint_id = ?";
                    $params[] = $activeSprint['id'];
                }
            }

            $sql .= " ORDER BY i.sort_order ASC, i.created_at DESC";

            $column['issues'] = Database::select($sql, $params);
        }

        $board['columns'] = $columns;

        if ($board['type'] === 'scrum') {
            $board['active_sprint'] = Database::selectOne(
                "SELECT * FROM sprints WHERE board_id = ? AND status = 'active' LIMIT 1",
                [$boardId]
            );
        }

        return $board;
    }

    public function getBacklogIssues(int $boardId, int $page = 1, int $perPage = 50): array
    {
        $board = $this->getBoardById($boardId);
        if (!$board) {
            throw new \InvalidArgumentException('Board not found');
        }

        $offset = ($page - 1) * $perPage;

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issues WHERE project_id = ? AND sprint_id IS NULL",
            [$board['project_id']]
        );

        $issues = Database::select(
            "SELECT i.*, 
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                    s.name as status_name, s.color as status_color,
                    ip.name as priority_name, ip.icon as priority_icon, ip.color as priority_color,
                    assignee.display_name as assignee_name, assignee.avatar as assignee_avatar,
                    epic.issue_key as epic_key, epic.summary as epic_summary
             FROM issues i
             JOIN issue_types it ON i.issue_type_id = it.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_priorities ip ON i.priority_id = ip.id
             LEFT JOIN users assignee ON i.assignee_id = assignee.id
             LEFT JOIN issues epic ON i.epic_id = epic.id
             WHERE i.project_id = ? AND i.sprint_id IS NULL
             ORDER BY i.sort_order ASC, i.created_at DESC
             LIMIT $perPage OFFSET $offset",
            [$board['project_id']]
        );

        return [
            'items' => $issues,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    public function moveIssue(int $issueId, int $targetStatusId, ?int $targetSprintId, int $userId): array
    {
        $issue = Database::selectOne("SELECT * FROM issues WHERE id = ?", [$issueId]);
        if (!$issue) {
            throw new \InvalidArgumentException('Issue not found');
        }

        $updateData = [];
        $historyRecords = [];

        if ($issue['status_id'] != $targetStatusId) {
            $oldStatus = Database::selectValue("SELECT name FROM statuses WHERE id = ?", [$issue['status_id']]);
            $newStatus = Database::selectValue("SELECT name FROM statuses WHERE id = ?", [$targetStatusId]);

            $updateData['status_id'] = $targetStatusId;
            $historyRecords[] = ['field' => 'status', 'old' => $oldStatus, 'new' => $newStatus];

            $statusCategory = Database::selectValue("SELECT category FROM statuses WHERE id = ?", [$targetStatusId]);
            if ($statusCategory === 'done' && !$issue['resolved_at']) {
                $updateData['resolved_at'] = date('Y-m-d H:i:s');
            } elseif ($statusCategory !== 'done' && $issue['resolved_at']) {
                $updateData['resolved_at'] = null;
            }
        }

        if ($issue['sprint_id'] != $targetSprintId) {
            $oldSprint = $issue['sprint_id']
                ? Database::selectValue("SELECT name FROM sprints WHERE id = ?", [$issue['sprint_id']])
                : 'Backlog';
            $newSprint = $targetSprintId
                ? Database::selectValue("SELECT name FROM sprints WHERE id = ?", [$targetSprintId])
                : 'Backlog';

            $updateData['sprint_id'] = $targetSprintId;
            $historyRecords[] = ['field' => 'sprint', 'old' => $oldSprint, 'new' => $newSprint];
        }

        if (!empty($updateData)) {
            Database::update('issues', $updateData, 'id = ?', [$issueId]);

            foreach ($historyRecords as $record) {
                Database::insert('issue_history', [
                    'issue_id' => $issueId,
                    'user_id' => $userId,
                    'field' => $record['field'],
                    'old_value' => $record['old'],
                    'new_value' => $record['new'],
                ]);
            }

            $this->logAudit('issue_moved', 'issue', $issueId, $issue, $updateData, $userId);
        }

        return Database::selectOne("SELECT * FROM issues WHERE id = ?", [$issueId]);
    }

    public function rankIssue(int $issueId, int $afterIssueId, int $userId): bool
    {
        $issue = Database::selectOne("SELECT * FROM issues WHERE id = ?", [$issueId]);
        $afterIssue = Database::selectOne("SELECT * FROM issues WHERE id = ?", [$afterIssueId]);

        if (!$issue || !$afterIssue) {
            throw new \InvalidArgumentException('Issue not found');
        }

        $newSortOrder = $afterIssue['sort_order'] + 1;

        Database::query(
            "UPDATE issues SET sort_order = sort_order + 1 
             WHERE project_id = ? AND sort_order >= ?",
            [$issue['project_id'], $newSortOrder]
        );

        Database::update('issues', ['sort_order' => $newSortOrder], 'id = ?', [$issueId]);

        return true;
    }

    public function getBoardColumns(int $boardId): array
    {
        return Database::select(
            "SELECT bc.*, 
                    (SELECT GROUP_CONCAT(s.name SEPARATOR ', ') 
                     FROM statuses s 
                     WHERE JSON_CONTAINS(bc.status_ids, CAST(s.id AS CHAR))) as status_names
             FROM board_columns bc 
             WHERE bc.board_id = ? 
             ORDER BY bc.sort_order ASC",
            [$boardId]
        );
    }

    public function updateColumnOrder(int $boardId, array $columnOrder, int $userId): array
    {
        $board = $this->getBoardById($boardId);
        if (!$board) {
            throw new \InvalidArgumentException('Board not found');
        }

        Database::transaction(function () use ($boardId, $columnOrder, $userId) {
            foreach ($columnOrder as $index => $columnId) {
                Database::update(
                    'board_columns',
                    ['sort_order' => $index],
                    'id = ? AND board_id = ?',
                    [$columnId, $boardId]
                );
            }

            $this->logAudit('board_columns_reordered', 'board', $boardId, null, [
                'column_order' => $columnOrder,
            ], $userId);
        });

        return $this->getBoardColumns($boardId);
    }

    public function createColumn(int $boardId, array $data, int $userId): array
    {
        $board = $this->getBoardById($boardId);
        if (!$board) {
            throw new \InvalidArgumentException('Board not found');
        }

        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Column name is required');
        }

        $maxOrder = (int) Database::selectValue(
            "SELECT MAX(sort_order) FROM board_columns WHERE board_id = ?",
            [$boardId]
        );

        $columnId = Database::insert('board_columns', [
            'board_id' => $boardId,
            'name' => $data['name'],
            'status_ids' => json_encode($data['status_ids'] ?? []),
            'wip_limit' => $data['wip_limit'] ?? null,
            'sort_order' => $maxOrder + 1,
        ]);

        $this->logAudit('board_column_created', 'board_column', $columnId, null, $data, $userId);

        return Database::selectOne("SELECT * FROM board_columns WHERE id = ?", [$columnId]);
    }

    public function updateColumn(int $columnId, array $data, int $userId): array
    {
        $column = Database::selectOne("SELECT * FROM board_columns WHERE id = ?", [$columnId]);
        if (!$column) {
            throw new \InvalidArgumentException('Column not found');
        }

        $updateData = array_filter([
            'name' => $data['name'] ?? null,
            'status_ids' => isset($data['status_ids']) ? json_encode($data['status_ids']) : null,
            'wip_limit' => $data['wip_limit'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($updateData)) {
            Database::update('board_columns', $updateData, 'id = ?', [$columnId]);
            $this->logAudit('board_column_updated', 'board_column', $columnId, $column, $updateData, $userId);
        }

        return Database::selectOne("SELECT * FROM board_columns WHERE id = ?", [$columnId]);
    }

    public function deleteColumn(int $columnId, int $userId): bool
    {
        $column = Database::selectOne("SELECT * FROM board_columns WHERE id = ?", [$columnId]);
        if (!$column) {
            throw new \InvalidArgumentException('Column not found');
        }

        $this->logAudit('board_column_deleted', 'board_column', $columnId, $column, null, $userId);

        return Database::delete('board_columns', 'id = ?', [$columnId]) > 0;
    }

    public function getBacklog(int $boardId, array $filters = [], int $page = 1, int $perPage = 50): array
    {
        $board = $this->getBoardById($boardId);
        if (!$board) {
            throw new \InvalidArgumentException('Board not found');
        }

        $where = ['i.project_id = ?', 'i.sprint_id IS NULL'];
        $params = [$board['project_id']];

        if (!empty($filters['search'])) {
            $where[] = "(i.summary LIKE ? OR i.issue_key LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['issue_type_id'])) {
            $where[] = "i.issue_type_id = ?";
            $params[] = $filters['issue_type_id'];
        }

        if (!empty($filters['priority_id'])) {
            $where[] = "i.priority_id = ?";
            $params[] = $filters['priority_id'];
        }

        if (!empty($filters['assignee_id'])) {
            if ($filters['assignee_id'] === 'unassigned') {
                $where[] = "i.assignee_id IS NULL";
            } else {
                $where[] = "i.assignee_id = ?";
                $params[] = $filters['assignee_id'];
            }
        }

        if (!empty($filters['epic_id'])) {
            $where[] = "i.epic_id = ?";
            $params[] = $filters['epic_id'];
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issues i WHERE $whereClause",
            $params
        );

        $issues = Database::select(
            "SELECT i.*, 
                    it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                    s.name as status_name, s.color as status_color, s.category as status_category,
                    ip.name as priority_name, ip.icon as priority_icon, ip.color as priority_color,
                    assignee.display_name as assignee_name, assignee.avatar as assignee_avatar,
                    epic.issue_key as epic_key, epic.summary as epic_summary
             FROM issues i
             JOIN issue_types it ON i.issue_type_id = it.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_priorities ip ON i.priority_id = ip.id
             LEFT JOIN users assignee ON i.assignee_id = assignee.id
             LEFT JOIN issues epic ON i.epic_id = epic.id
             WHERE $whereClause
             ORDER BY i.sort_order ASC, i.created_at DESC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        $totalPoints = (float) Database::selectValue(
            "SELECT COALESCE(SUM(story_points), 0) FROM issues i WHERE $whereClause",
            $params
        );

        return [
            'items' => $issues,
            'total' => $total,
            'total_points' => $totalPoints,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    public function getBoardIssues(int $boardId, ?int $sprintId = null, array $filters = []): array
    {
        $board = $this->getBoardById($boardId);
        if (!$board) {
            throw new \InvalidArgumentException('Board not found');
        }

        $where = ['i.project_id = ?'];
        $params = [$board['project_id']];

        if ($sprintId) {
            $where[] = "i.sprint_id = ?";
            $params[] = $sprintId;
        } elseif ($board['type'] === 'scrum') {
            $activeSprint = Database::selectOne(
                "SELECT id FROM sprints WHERE board_id = ? AND status = 'active' LIMIT 1",
                [$boardId]
            );
            if ($activeSprint) {
                $where[] = "i.sprint_id = ?";
                $params[] = $activeSprint['id'];
            } else {
                return ['columns' => [], 'issues' => [], 'sprint' => null];
            }
        }

        if (!empty($filters['assignee_id'])) {
            if ($filters['assignee_id'] === 'unassigned') {
                $where[] = "i.assignee_id IS NULL";
            } else {
                $where[] = "i.assignee_id = ?";
                $params[] = $filters['assignee_id'];
            }
        }

        if (!empty($filters['issue_type_id'])) {
            $where[] = "i.issue_type_id = ?";
            $params[] = $filters['issue_type_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(i.summary LIKE ? OR i.issue_key LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $where);

        $issues = Database::select(
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
             WHERE $whereClause
             ORDER BY i.sort_order ASC, i.created_at DESC",
            $params
        );

        $columns = $this->getBoardColumns($boardId);
        foreach ($columns as &$column) {
            $statusIds = json_decode($column['status_ids'] ?? '[]', true);
            $column['issues'] = array_values(array_filter($issues, fn($i) => in_array($i['status_id'], $statusIds)));
        }

        $sprint = null;
        if ($sprintId) {
            $sprint = Database::selectOne("SELECT * FROM sprints WHERE id = ?", [$sprintId]);
        } elseif ($board['type'] === 'scrum') {
            $sprint = Database::selectOne(
                "SELECT * FROM sprints WHERE board_id = ? AND status = 'active' LIMIT 1",
                [$boardId]
            );
        }

        return [
            'columns' => $columns,
            'issues' => $issues,
            'sprint' => $sprint,
        ];
    }

    public function rankIssues(array $issueIds, int $userId): bool
    {
        if (empty($issueIds)) {
            return true;
        }

        Database::transaction(function () use ($issueIds, $userId) {
            foreach ($issueIds as $index => $issueId) {
                Database::update(
                    'issues',
                    ['sort_order' => $index * 1000],
                    'id = ?',
                    [$issueId]
                );
            }

            $this->logAudit('issues_ranked', 'issue', null, null, [
                'issue_ids' => $issueIds,
            ], $userId);
        });

        return true;
    }

    public function moveIssuesToSprint(array $issueIds, ?int $sprintId, int $userId): int
    {
        if (empty($issueIds)) {
            return 0;
        }

        $movedCount = 0;

        Database::transaction(function () use ($issueIds, $sprintId, $userId, &$movedCount) {
            $sprintName = $sprintId
                ? Database::selectValue("SELECT name FROM sprints WHERE id = ?", [$sprintId])
                : 'Backlog';

            foreach ($issueIds as $issueId) {
                $issue = Database::selectOne("SELECT * FROM issues WHERE id = ?", [$issueId]);
                if (!$issue)
                    continue;

                $oldSprintName = $issue['sprint_id']
                    ? Database::selectValue("SELECT name FROM sprints WHERE id = ?", [$issue['sprint_id']])
                    : 'Backlog';

                if ($issue['sprint_id'] != $sprintId) {
                    Database::update('issues', ['sprint_id' => $sprintId], 'id = ?', [$issueId]);

                    Database::insert('issue_history', [
                        'issue_id' => $issueId,
                        'user_id' => $userId,
                        'field' => 'sprint',
                        'old_value' => $oldSprintName,
                        'new_value' => $sprintName,
                    ]);

                    $movedCount++;
                }
            }

            $this->logAudit('issues_moved_to_sprint', 'sprint', $sprintId, null, [
                'issue_ids' => $issueIds,
                'count' => $movedCount,
            ], $userId);
        });

        return $movedCount;
    }

    private function createDefaultColumns(int $boardId, string $type): void
    {
        $statuses = Database::select("SELECT id, name, category FROM statuses ORDER BY sort_order ASC");

        $columns = [
            'todo' => ['name' => 'To Do', 'statuses' => []],
            'in_progress' => ['name' => 'In Progress', 'statuses' => []],
            'done' => ['name' => 'Done', 'statuses' => []],
        ];

        foreach ($statuses as $status) {
            $columns[$status['category']]['statuses'][] = $status['id'];
        }

        $order = 0;
        foreach ($columns as $category => $column) {
            Database::insert('board_columns', [
                'board_id' => $boardId,
                'name' => $column['name'],
                'status_ids' => json_encode($column['statuses']),
                'sort_order' => $order++,
            ]);
        }
    }

    private function validateBoardData(array $data): void
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Board name is required');
        }

        if (isset($data['type']) && !in_array($data['type'], ['scrum', 'kanban'])) {
            throw new \InvalidArgumentException('Invalid board type');
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
