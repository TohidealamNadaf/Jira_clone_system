<?php
/**
 * Project Service
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class ProjectService
{
    public function getAllProjects(array $filters = [], int $page = 1, int $perPage = 25): array
    {
        $where = ['1 = 1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE :search OR p.key LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Handle status filter (active/archived)
        if (!empty($filters['status'])) {
            $where[] = "p.is_archived = :is_archived";
            $params['is_archived'] = $filters['status'] === 'archived' ? 1 : 0;
        }

        // Handle category filter (support both 'category' and 'category_id')
        $categoryId = $filters['category'] ?? $filters['category_id'] ?? null;
        if (!empty($categoryId)) {
            $where[] = "p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        if (!empty($filters['lead_id'])) {
            $where[] = "p.lead_id = :lead_id";
            $params['lead_id'] = $filters['lead_id'];
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM projects p WHERE $whereClause",
            $params
        );

        $projects = Database::select(
            "SELECT p.id, p.`key`, p.name, p.description, p.lead_id, p.category_id, p.default_assignee, p.avatar, p.is_archived, p.issue_count, p.created_by, p.created_at, p.updated_at,
                    u.display_name as lead_name,
                    pc.name as category_name,
                    COALESCE(issue_counts.total_issues, 0) as issue_count,
                    COALESCE(member_counts.total_members, 0) as member_count
             FROM projects p
             LEFT JOIN users u ON p.lead_id = u.id
             LEFT JOIN project_categories pc ON p.category_id = pc.id
             LEFT JOIN (
                 SELECT project_id, COUNT(*) as total_issues
                 FROM issues
                 GROUP BY project_id
             ) issue_counts ON p.id = issue_counts.project_id
             LEFT JOIN (
                 SELECT project_id, COUNT(*) as total_members
                 FROM project_members pm
                 JOIN users u2 ON pm.user_id = u2.id
                 WHERE u2.is_active = 1
                 GROUP BY project_id
             ) member_counts ON p.id = member_counts.project_id
             WHERE $whereClause
             ORDER BY p.name ASC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        return [
            'items' => $projects,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    public function getProjectByKey(string $key): ?array
    {
        $project = Database::selectOne(
            "SELECT p.id, p.`key`, p.name, p.description, p.lead_id, p.category_id, p.default_assignee, p.avatar, p.is_archived, p.issue_count, p.created_by, p.created_at, p.updated_at,
                    u.display_name as lead_name,
                    pc.name as category_name,
                    creator.display_name as created_by_name
             FROM projects p
             LEFT JOIN users u ON p.lead_id = u.id
             LEFT JOIN users creator ON p.created_by = creator.id
             LEFT JOIN project_categories pc ON p.category_id = pc.id
             WHERE p.`key` = ?",
            [$key]
        );

        return $project ?: null;
    }

    public function getProjectById(int $id): ?array
    {
        $project = Database::selectOne(
            "SELECT p.id, p.`key`, p.name, p.description, p.lead_id, p.category_id, p.default_assignee, p.avatar, p.is_archived, p.issue_count, p.created_by, p.created_at, p.updated_at, p.budget, p.budget_currency,
                    u.display_name as lead_name,
                    pc.name as category_name
             FROM projects p
             LEFT JOIN users u ON p.lead_id = u.id
             LEFT JOIN project_categories pc ON p.category_id = pc.id
             WHERE p.id = ?",
            [$id]
        );

        return $project ?: null;
    }

    /**
     * Get all projects a user has access to
     * (member of project or is admin)
     */
    public function getUserProjects(int $userId, bool $includeArchived = false): array
    {
        $archivedClause = $includeArchived ? '' : 'AND p.is_archived = 0';

        return Database::select(
            "SELECT DISTINCT p.id, p.`key`, p.name, p.description, p.lead_id, p.category_id, 
                    p.default_assignee, p.avatar, p.is_archived, p.issue_count, p.budget, p.budget_currency,
                    p.created_at, p.updated_at,
                    u.display_name as lead_name,
                    pc.name as category_name,
                    pm.role_id,
                    r.name as role_name,
                    COALESCE(pm.created_at, p.created_at) as joined_at,
                    0 as is_primary
             FROM projects p
             LEFT JOIN users u ON p.lead_id = u.id
             LEFT JOIN project_categories pc ON p.category_id = pc.id
             LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = ?
             LEFT JOIN roles r ON pm.role_id = r.id
             WHERE (pm.user_id = ? OR p.lead_id = ? OR ? = 1)
             $archivedClause
             ORDER BY p.name ASC",
            [$userId, $userId, $userId, 0]  // Last param for future admin check
        );
    }

    public function createProject(array $data, int $userId): array
    {
        $this->validateProjectData($data, true);

        if ($this->projectKeyExists($data['key'])) {
            throw new \InvalidArgumentException('Project key already exists');
        }

        return Database::transaction(function () use ($data, $userId) {
            // Only set lead_id if it's explicitly provided and the user exists
            $leadId = null;
            if (!empty($data['lead_id'])) {
                $leadUserExists = Database::selectOne(
                    "SELECT 1 FROM `users` WHERE `id` = ?",
                    [$data['lead_id']]
                );
                $leadId = $leadUserExists ? $data['lead_id'] : null;
            }

            // Only set category_id if it's explicitly provided and the category exists
            $categoryId = null;
            if (!empty($data['category_id'])) {
                $categoryExists = Database::selectOne(
                    "SELECT 1 FROM `project_categories` WHERE `id` = ?",
                    [$data['category_id']]
                );
                $categoryId = $categoryExists ? $data['category_id'] : null;
            }

            $projectId = Database::insert('projects', [
                'key' => strtoupper($data['key']),
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'lead_id' => $leadId,
                'category_id' => $categoryId,
                'default_assignee' => $data['default_assignee'] ?? 'unassigned',
                'avatar' => $data['avatar'] ?? null,
                'created_by' => $userId,
            ]);

            $defaultRole = Database::selectOne(
                "SELECT id FROM roles WHERE slug = 'project-admin' LIMIT 1"
            );

            if ($defaultRole) {
                Database::insert('project_members', [
                    'project_id' => $projectId,
                    'user_id' => $userId,
                    'role_id' => $defaultRole['id'],
                ]);
            }

            $this->logAudit('project_created', 'project', $projectId, null, $data, $userId);

            return $this->getProjectById($projectId);
        });
    }

    public function updateProject(int $projectId, array $data, int $userId): array
    {
        $project = $this->getProjectById($projectId);
        if (!$project) {
            throw new \InvalidArgumentException('Project not found');
        }

        $this->validateProjectData($data, false);

        if (isset($data['key']) && $data['key'] !== $project['key']) {
            if ($this->projectKeyExists($data['key'])) {
                throw new \InvalidArgumentException('Project key already exists');
            }
        }

        $updateData = array_filter([
            'name' => $data['name'] ?? null,
            'key' => isset($data['key']) ? strtoupper($data['key']) : null,
            'description' => $data['description'] ?? null,
            'lead_id' => $data['lead_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'default_assignee' => $data['default_assignee'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'is_archived' => $data['is_archived'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($updateData)) {
            Database::update('projects', $updateData, 'id = ?', [$projectId]);
            $this->logAudit('project_updated', 'project', $projectId, $project, $updateData, $userId);
        }

        return $this->getProjectById($projectId);
    }

    public function deleteProject(int $projectId, int $userId): bool
    {
        $project = $this->getProjectById($projectId);
        if (!$project) {
            throw new \InvalidArgumentException('Project not found');
        }

        $this->logAudit('project_deleted', 'project', $projectId, $project, null, $userId);

        return Database::delete('projects', 'id = ?', [$projectId]) > 0;
    }

    public function getProjectMembers(int $projectId): array
    {
        return Database::select(
            "SELECT pm.user_id, pm.project_id, pm.role_id, pm.created_at as joined_at,
                    u.email, u.display_name, u.avatar, u.first_name, u.last_name,
                    r.name as role_name, r.slug as role_slug,
                    COALESCE(r.slug, 'viewer') as role,
                    (SELECT COUNT(*) FROM issues WHERE assignee_id = pm.user_id AND project_id = ? AND status_id != 5) as assigned_issues_count
             FROM project_members pm
             JOIN users u ON pm.user_id = u.id
             JOIN roles r ON pm.role_id = r.id
             WHERE pm.project_id = ?
             ORDER BY u.display_name ASC",
            [$projectId, $projectId]
        );
    }

    public function addProjectMember(int $projectId, int $userId, int $roleId, int $actorId): bool
    {
        $existing = Database::selectOne(
            "SELECT 1 FROM project_members WHERE project_id = ? AND user_id = ?",
            [$projectId, $userId]
        );

        if ($existing) {
            Database::update(
                'project_members',
                ['role_id' => $roleId],
                'project_id = ? AND user_id = ?',
                [$projectId, $userId]
            );
        } else {
            Database::insert('project_members', [
                'project_id' => $projectId,
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        }

        $this->logAudit('member_added', 'project', $projectId, null, [
            'user_id' => $userId,
            'role_id' => $roleId,
        ], $actorId);

        return true;
    }

    public function removeProjectMember(int $projectId, int $userId, int $actorId): bool
    {
        $result = Database::delete(
            'project_members',
            'project_id = ? AND user_id = ?',
            [$projectId, $userId]
        );

        if ($result > 0) {
            $this->logAudit('member_removed', 'project', $projectId, ['user_id' => $userId], null, $actorId);
        }

        return $result > 0;
    }

    public function getAvailableUsers(int $projectId): array
    {
        return Database::select(
            "SELECT u.id, u.email, u.display_name, u.avatar, u.first_name, u.last_name
             FROM users u
             WHERE u.is_active = 1
             AND u.id NOT IN (
                 SELECT user_id FROM project_members WHERE project_id = ?
             )
             ORDER BY u.display_name ASC",
            [$projectId]
        );
    }

    public function getAvailableRoles(): array
    {
        return Database::select(
            "SELECT id, name, slug, description
             FROM roles
             ORDER BY name ASC"
        );
    }

    public function getComponents(int $projectId): array
    {
        return Database::select(
            "SELECT c.*, u.display_name as lead_name, da.display_name as default_assignee_name
             FROM components c
             LEFT JOIN users u ON c.lead_id = u.id
             LEFT JOIN users da ON c.default_assignee_id = da.id
             WHERE c.project_id = ?
             ORDER BY c.name ASC",
            [$projectId]
        );
    }

    public function createComponent(int $projectId, array $data, int $userId): array
    {
        $this->validateComponentData($data);

        $componentId = Database::insert('components', [
            'project_id' => $projectId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'lead_id' => $data['lead_id'] ?? null,
            'default_assignee_id' => $data['default_assignee_id'] ?? null,
        ]);

        $this->logAudit('component_created', 'component', $componentId, null, $data, $userId);

        return Database::selectOne("SELECT * FROM components WHERE id = ?", [$componentId]);
    }

    public function updateComponent(int $componentId, array $data, int $userId): array
    {
        $component = Database::selectOne("SELECT * FROM components WHERE id = ?", [$componentId]);
        if (!$component) {
            throw new \InvalidArgumentException('Component not found');
        }

        $updateData = array_filter([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'lead_id' => $data['lead_id'] ?? null,
            'default_assignee_id' => $data['default_assignee_id'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($updateData)) {
            Database::update('components', $updateData, 'id = ?', [$componentId]);
            $this->logAudit('component_updated', 'component', $componentId, $component, $updateData, $userId);
        }

        return Database::selectOne("SELECT * FROM components WHERE id = ?", [$componentId]);
    }

    public function deleteComponent(int $componentId, int $userId): bool
    {
        $component = Database::selectOne("SELECT * FROM components WHERE id = ?", [$componentId]);
        if (!$component) {
            throw new \InvalidArgumentException('Component not found');
        }

        $this->logAudit('component_deleted', 'component', $componentId, $component, null, $userId);

        return Database::delete('components', 'id = ?', [$componentId]) > 0;
    }

    public function getVersions(int $projectId): array
    {
        return Database::select(
            "SELECT * FROM versions WHERE project_id = ? ORDER BY sort_order ASC, release_date DESC",
            [$projectId]
        );
    }

    public function createVersion(int $projectId, array $data, int $userId): array
    {
        $this->validateVersionData($data);

        $maxOrder = (int) Database::selectValue(
            "SELECT MAX(sort_order) FROM versions WHERE project_id = ?",
            [$projectId]
        );

        $versionId = Database::insert('versions', [
            'project_id' => $projectId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'release_date' => $data['release_date'] ?? null,
            'sort_order' => $maxOrder + 1,
        ]);

        $this->logAudit('version_created', 'version', $versionId, null, $data, $userId);

        return Database::selectOne("SELECT * FROM versions WHERE id = ?", [$versionId]);
    }

    public function updateVersion(int $versionId, array $data, int $userId): array
    {
        $version = Database::selectOne("SELECT * FROM versions WHERE id = ?", [$versionId]);
        if (!$version) {
            throw new \InvalidArgumentException('Version not found');
        }

        $updateData = array_filter([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'release_date' => $data['release_date'] ?? null,
            'is_archived' => $data['is_archived'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($updateData)) {
            Database::update('versions', $updateData, 'id = ?', [$versionId]);
            $this->logAudit('version_updated', 'version', $versionId, $version, $updateData, $userId);
        }

        return Database::selectOne("SELECT * FROM versions WHERE id = ?", [$versionId]);
    }

    public function releaseVersion(int $versionId, int $userId): array
    {
        $version = Database::selectOne("SELECT * FROM versions WHERE id = ?", [$versionId]);
        if (!$version) {
            throw new \InvalidArgumentException('Version not found');
        }

        Database::update('versions', [
            'released_at' => date('Y-m-d H:i:s'),
            'release_date' => date('Y-m-d'),
        ], 'id = ?', [$versionId]);

        $this->logAudit('version_released', 'version', $versionId, $version, [
            'released_at' => date('Y-m-d H:i:s'),
        ], $userId);

        return Database::selectOne("SELECT * FROM versions WHERE id = ?", [$versionId]);
    }

    public function deleteVersion(int $versionId, int $userId): bool
    {
        $version = Database::selectOne("SELECT * FROM versions WHERE id = ?", [$versionId]);
        if (!$version) {
            throw new \InvalidArgumentException('Version not found');
        }

        $this->logAudit('version_deleted', 'version', $versionId, $version, null, $userId);

        return Database::delete('versions', 'id = ?', [$versionId]) > 0;
    }

    private function projectKeyExists(string $key): bool
    {
        return Database::selectOne(
            "SELECT 1 FROM projects WHERE `key` = ?",
            [strtoupper($key)]
        ) !== null;
    }

    private function validateProjectData(array $data, bool $isCreate): void
    {
        if ($isCreate) {
            if (empty($data['key'])) {
                throw new \InvalidArgumentException('Project key is required');
            }
            if (empty($data['name'])) {
                throw new \InvalidArgumentException('Project name is required');
            }
        }

        if (isset($data['key']) && !preg_match('/^[A-Z][A-Z0-9]{1,9}$/i', $data['key'])) {
            throw new \InvalidArgumentException('Project key must be 2-10 alphanumeric characters starting with a letter');
        }
    }

    private function validateComponentData(array $data): void
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Component name is required');
        }
    }

    private function validateVersionData(array $data): void
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Version name is required');
        }
    }

    /**
     * Get project budget
     * 
     * @param int $projectId
     * @return array|null
     */
    public function getProjectBudget(int $projectId): ?array
    {
        return Database::selectOne(
            "SELECT budget, budget_currency FROM projects WHERE id = ?",
            [$projectId]
        );
    }

    /**
     * Set project budget
     * 
     * @param int $projectId
     * @param float $budget
     * @param string $currency
     * @return bool
     */
    public function setProjectBudget(int $projectId, float $budget, string $currency = 'USD'): bool
    {
        $rowsAffected = Database::update(
            'projects',
            [
                'budget' => $budget,
                'budget_currency' => $currency
            ],
            'id = ?',
            [$projectId]
        );

        return $rowsAffected > 0;
    }

    /**
     * Get budget remaining for project
     * 
     * @param int $projectId
     * @return array
     */
    public function getBudgetStatus(int $projectId): array
    {
        $project = $this->getProjectById($projectId);
        if (!$project) {
            return [
                'budget' => 0.00,
                'spent' => 0.00,
                'remaining' => 0.00,
                'percentage_used' => 0,
                'currency' => 'USD'
            ];
        }

        $totalBudget = (float)($project['budget'] ?? 0);
        $currency = $project['budget_currency'] ?? 'USD';

        // Get total spent from time tracking
        $spent = 0.0;
        try {
            $result = Database::selectOne(
                "SELECT COALESCE(SUM(total_cost), 0) as total_spent 
                 FROM issue_time_logs 
                 WHERE project_id = ? AND status = 'stopped'",
                [$projectId]
            );
            $spent = (float)($result['total_spent'] ?? 0);
        } catch (\Exception $e) {
            // Table might not exist yet
            $spent = 0.0;
        }

        $remaining = $totalBudget - $spent;
        $percentageUsed = $totalBudget > 0 ? round(($spent / $totalBudget) * 100, 2) : 0;

        return [
            'budget' => $totalBudget,
            'spent' => $spent,
            'remaining' => max(0, $remaining),
            'percentage_used' => min(100, $percentageUsed),
            'currency' => $currency,
            'is_exceeded' => $spent > $totalBudget
        ];
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
