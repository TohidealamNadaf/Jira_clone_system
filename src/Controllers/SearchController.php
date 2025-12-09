<?php
/**
 * Search Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class SearchController extends Controller
{
    public function index(Request $request): string
    {
        $query = $request->input('q', '');
        $sort = $request->input('sort', 'updated_desc');
        $view = $request->input('view', 'list');
        $page = (int) ($request->input('page') ?? 1);
        $perPage = 25;

        // Collect filters
        $filters = [
            'project' => $request->input('project', ''),
            'type' => $request->input('type', []),
            'status' => $request->input('status', []),
            'priority' => $request->input('priority', []),
            'assignee' => $request->input('assignee', ''),
            'reporter' => $request->input('reporter', ''),
            'created' => $request->input('created', ''),
        ];

        $issues = [];
        $totalResults = 0;
        $totalPages = 1;
        $currentPage = $page;

        // Get all data for filters
        $projects = Database::select(
            "SELECT DISTINCT p.id, p.key, p.name FROM projects p ORDER BY p.name"
        );
        
        $issueTypes = Database::select(
            "SELECT DISTINCT it.id, it.name, it.icon, it.color FROM issue_types it ORDER BY it.name"
        );
        
        $statuses = Database::select(
            "SELECT DISTINCT s.id, s.name, s.color FROM statuses s ORDER BY s.name"
        );
        
        $priorities = Database::select(
            "SELECT id, name, color FROM issue_priorities ORDER BY id"
        );
        
        $users = Database::select(
            "SELECT id, display_name FROM users ORDER BY display_name"
        );

        // Perform search with filters - show all issues by default
        $result = $this->performSearchWithFilters($query, $filters, $sort, $page, $perPage);
        $issues = $result['issues'];
        $totalResults = $result['total'];
        $totalPages = (int) ceil($totalResults / $perPage);
        $currentPage = $page;

        $savedFilters = $this->getUserFilters();

        if ($request->wantsJson()) {
            $this->json([
                'issues' => $issues,
                'query' => $query,
                'filters' => $filters,
                'total' => $totalResults,
                'page' => $page,
            ]);
        }

        return $this->view('search.index', [
            'issues' => $issues,
            'query' => $query,
            'sort' => $sort,
            'view' => $view,
            'filters' => $filters,
            'projects' => $projects,
            'issueTypes' => $issueTypes,
            'statuses' => $statuses,
            'priorities' => $priorities,
            'users' => $users,
            'totalResults' => $totalResults,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'savedFilters' => $savedFilters,
        ]);
    }

    public function quick(Request $request): void
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            $this->json(['results' => []]);
        }

        $results = [
            'issues' => [],
            'projects' => [],
        ];

        if (preg_match('/^[A-Z]+-\d+$/i', $query)) {
            $issue = Database::selectOne(
                "SELECT i.id, i.issue_key, i.summary, s.name as status_name, s.color as status_color
                 FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.issue_key = ?",
                [strtoupper($query)]
            );
            if ($issue) {
                $results['issues'][] = $issue;
            }
        } else {
            $results['issues'] = Database::select(
                "SELECT i.id, i.issue_key, i.summary, s.name as status_name, s.color as status_color
                 FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.issue_key LIKE ? OR i.summary LIKE ?
                 ORDER BY i.updated_at DESC
                 LIMIT 5",
                ["%{$query}%", "%{$query}%"]
            );

            $results['projects'] = Database::select(
                "SELECT id, key, name
                 FROM projects
                 WHERE key LIKE ? OR name LIKE ?
                 ORDER BY name
                 LIMIT 3",
                ["%{$query}%", "%{$query}%"]
            );
        }

        $this->json($results);
    }

    public function advanced(Request $request): string
    {
        $jql = $request->input('jql', '');
        $page = (int) ($request->input('page') ?? 1);
        $perPage = 50;

        $results = [];
        $error = null;

        if (!empty($jql)) {
            try {
                $results = $this->parseAndExecuteJQL($jql, $page, $perPage);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        if ($request->wantsJson()) {
            if ($error) {
                $this->json(['error' => $error], 422);
            }
            $this->json($results);
        }

        return $this->view('search.advanced', [
            'jql' => $jql,
            'results' => $results,
            'error' => $error,
        ]);
    }

    public function filters(Request $request): string
    {
        $filters = $this->getUserFilters();
        $sharedFilters = Database::select(
            "SELECT f.*, u.display_name as owner_name
             FROM saved_filters f
             JOIN users u ON f.user_id = u.id
             WHERE (f.share_type = 'global' OR f.share_type = 'project') AND f.user_id != ?
             ORDER BY f.name",
            [$this->userId()]
        );

        if ($request->wantsJson()) {
            $this->json([
                'my_filters' => $filters,
                'shared_filters' => $sharedFilters,
            ]);
        }

        return $this->view('search.filters', [
            'myFilters' => $filters,
            'sharedFilters' => $sharedFilters,
        ]);
    }

    public function saveFilter(Request $request): void
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'jql' => 'required|max:5000',
            'share_type' => 'nullable|in:private,project,global',
        ]);

        try {
            $existingFilter = Database::selectOne(
                "SELECT id FROM saved_filters WHERE name = ? AND user_id = ?",
                [$data['name'], $this->userId()]
            );

            if ($existingFilter) {
                throw new \InvalidArgumentException('A filter with this name already exists.');
            }

            $filterId = Database::insert('saved_filters', [
                'user_id' => $this->userId(),
                'name' => $data['name'],
                'jql' => $data['jql'],
                'share_type' => $data['share_type'] ?? 'private',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $filter = Database::selectOne(
                "SELECT * FROM saved_filters WHERE id = ?",
                [$filterId]
            );

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'filter' => $filter], 201);
            }

            $this->redirectWith(url('/filters'), 'success', 'Filter saved successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            Session::flash('_old_input', $data);
            $this->redirect(url('/filters'));
        }
    }

    public function updateFilter(Request $request): void
    {
        $filterId = (int) $request->param('id');

        $filter = Database::selectOne(
            "SELECT * FROM saved_filters WHERE id = ? AND user_id = ?",
            [$filterId, $this->userId()]
        );

        if (!$filter) {
            abort(404, 'Filter not found');
        }

        $data = $request->validate([
            'name' => 'nullable|max:100',
            'jql' => 'nullable|max:5000',
            'share_type' => 'nullable|in:private,project,global',
        ]);

        try {
            if (isset($data['name']) && $data['name'] !== $filter['name']) {
                $existing = Database::selectOne(
                    "SELECT id FROM saved_filters WHERE name = ? AND user_id = ? AND id != ?",
                    [$data['name'], $this->userId(), $filterId]
                );
                if ($existing) {
                    throw new \InvalidArgumentException('A filter with this name already exists.');
                }
            }

            $updateData = array_filter([
                'name' => $data['name'] ?? null,
                'jql' => $data['jql'] ?? null,
                'share_type' => $data['share_type'] ?? null,
            ], fn($v) => $v !== null);

            $updateData['updated_at'] = date('Y-m-d H:i:s');

            Database::update('saved_filters', $updateData, 'id = :id', ['id' => $filterId]);

            $updated = Database::selectOne(
                "SELECT * FROM saved_filters WHERE id = ?",
                [$filterId]
            );

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'filter' => $updated]);
            }

            $this->redirectWith(url('/filters'), 'success', 'Filter updated successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(url('/filters'), 'error', $e->getMessage());
        }
    }

    public function deleteFilter(Request $request): void
    {
        $filterId = (int) $request->param('id');

        $filter = Database::selectOne(
            "SELECT * FROM saved_filters WHERE id = ? AND user_id = ?",
            [$filterId, $this->userId()]
        );

        if (!$filter) {
            abort(404, 'Filter not found');
        }

        try {
            Database::delete('saved_filters', 'id = :id', ['id' => $filterId]);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/filters'), 'success', 'Filter deleted successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to delete filter.'], 500);
            }

            $this->redirectWith(url('/filters'), 'error', 'Failed to delete filter.');
        }
    }

    private function performSearchWithFilters(string $query, array $filters, string $sort, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];

        // Text search
        if (!empty($query)) {
            $conditions[] = "(i.issue_key LIKE ? OR i.summary LIKE ? OR i.description LIKE ?)";
            $params[] = "%{$query}%";
            $params[] = "%{$query}%";
            $params[] = "%{$query}%";
        }

        // Project filter
        if (!empty($filters['project'])) {
            $conditions[] = "p.key = ?";
            $params[] = $filters['project'];
        }

        // Type filter
        if (!empty($filters['type'])) {
            $placeholders = implode(',', array_fill(0, count($filters['type']), '?'));
            $conditions[] = "i.issue_type_id IN ({$placeholders})";
            $params = array_merge($params, $filters['type']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $placeholders = implode(',', array_fill(0, count($filters['status']), '?'));
            $conditions[] = "i.status_id IN ({$placeholders})";
            $params = array_merge($params, $filters['status']);
        }

        // Priority filter
        if (!empty($filters['priority'])) {
            $placeholders = implode(',', array_fill(0, count($filters['priority']), '?'));
            $conditions[] = "i.priority_id IN ({$placeholders})";
            $params = array_merge($params, $filters['priority']);
        }

        // Assignee filter
        if (!empty($filters['assignee'])) {
            if ($filters['assignee'] === 'currentUser()') {
                $conditions[] = "i.assignee_id = ?";
                $params[] = $this->userId();
            } elseif ($filters['assignee'] === 'unassigned') {
                $conditions[] = "i.assignee_id IS NULL";
            } else {
                $conditions[] = "i.assignee_id = ?";
                $params[] = $filters['assignee'];
            }
        }

        // Reporter filter
        if (!empty($filters['reporter'])) {
            if ($filters['reporter'] === 'currentUser()') {
                $conditions[] = "i.reporter_id = ?";
                $params[] = $this->userId();
            } else {
                $conditions[] = "i.reporter_id = ?";
                $params[] = $filters['reporter'];
            }
        }

        // Created filter
        if (!empty($filters['created'])) {
            switch ($filters['created']) {
                case 'today':
                    $conditions[] = "DATE(i.created_at) = CURDATE()";
                    break;
                case 'week':
                    $conditions[] = "i.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    break;
                case 'month':
                    $conditions[] = "i.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    break;
            }
        }

        // Sort mapping
        $sortMap = [
            'updated_desc' => 'i.updated_at DESC',
            'updated_asc' => 'i.updated_at ASC',
            'created_desc' => 'i.created_at DESC',
            'created_asc' => 'i.created_at ASC',
            'priority_desc' => 'ip.id DESC',
            'priority_asc' => 'ip.id ASC',
        ];
        $orderBy = $sortMap[$sort] ?? 'i.updated_at DESC';

        $whereClause = empty($conditions) ? '1=1' : implode(' AND ', $conditions);

        // Get total count
        $countSql = "SELECT COUNT(*) FROM issues i
                     JOIN projects p ON i.project_id = p.id
                     JOIN statuses s ON i.status_id = s.id
                     JOIN issue_types t ON i.issue_type_id = t.id
                     LEFT JOIN issue_priorities ip ON i.priority_id = ip.id
                     LEFT JOIN users u ON i.assignee_id = u.id
                     WHERE {$whereClause}";
        
        $total = (int) Database::selectValue($countSql, $params);

        // Get issues
        $sql = "SELECT i.id, i.issue_key, i.summary, i.description, i.created_at, i.updated_at,
                       s.name as status_name, s.color as status_color,
                       t.name as issue_type_name, t.icon as issue_type_icon, t.color as issue_type_color,
                       ip.name as priority_name, ip.color as priority_color,
                       p.name as project_name, p.key as project_key,
                       u.display_name as assignee_name
                FROM issues i
                JOIN projects p ON i.project_id = p.id
                JOIN statuses s ON i.status_id = s.id
                JOIN issue_types t ON i.issue_type_id = t.id
                LEFT JOIN issue_priorities ip ON i.priority_id = ip.id
                LEFT JOIN users u ON i.assignee_id = u.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?";
        
        $issues = Database::select($sql, array_merge($params, [$perPage, $offset]));

        return [
            'issues' => $issues,
            'total' => $total,
        ];
    }

    private function performSearch(string $query, string $type, ?string $projectId, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $conditions = [];

        if ($type === 'all' || $type === 'issues') {
            $issueConditions = ["(i.issue_key LIKE ? OR i.summary LIKE ? OR i.description LIKE ?)"];
            $params = array_merge($params, ["%{$query}%", "%{$query}%", "%{$query}%"]);

            if ($projectId) {
                $issueConditions[] = "i.project_id = ?";
                $params[] = $projectId;
            }

            $issues = Database::select(
                "SELECT i.*, s.name as status_name, s.color as status_color,
                        t.name as type_name, t.icon as type_icon,
                        p.name as priority_name, p.color as priority_color,
                        u.display_name as assignee_name
                 FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 JOIN issue_types t ON i.issue_type_id = t.id
                 LEFT JOIN issue_priorities p ON i.priority_id = p.id
                 LEFT JOIN users u ON i.assignee_id = u.id
                 WHERE " . implode(' AND ', $issueConditions) . "
                 ORDER BY i.updated_at DESC
                 LIMIT ? OFFSET ?",
                array_merge($params, [$perPage, $offset])
            );

            return [
                'issues' => $issues,
                'total' => $this->countSearchResults($query, $type, $projectId),
                'page' => $page,
                'per_page' => $perPage,
            ];
        }

        return ['issues' => [], 'total' => 0, 'page' => $page, 'per_page' => $perPage];
    }

    private function countSearchResults(string $query, string $type, ?string $projectId): int
    {
        $conditions = ["(i.issue_key LIKE ? OR i.summary LIKE ? OR i.description LIKE ?)"];
        $params = ["%{$query}%", "%{$query}%", "%{$query}%"];

        if ($projectId) {
            $conditions[] = "i.project_id = ?";
            $params[] = $projectId;
        }

        return (int) Database::selectValue(
            "SELECT COUNT(*) FROM issues i WHERE " . implode(' AND ', $conditions),
            $params
        );
    }

    private function parseAndExecuteJQL(string $jql, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];
        $orderBy = 'i.created_at DESC';

        $jql = trim($jql);
        
        if (preg_match('/ORDER BY (.+)$/i', $jql, $matches)) {
            $orderClause = trim($matches[1]);
            $jql = preg_replace('/ORDER BY .+$/i', '', $jql);
            
            $orderMap = [
                'created' => 'i.created_at',
                'updated' => 'i.updated_at',
                'priority' => 'p.order_num',
                'status' => 's.name',
                'summary' => 'i.summary',
            ];
            
            if (preg_match('/(\w+)\s*(ASC|DESC)?/i', $orderClause, $orderMatch)) {
                $field = strtolower($orderMatch[1]);
                $direction = strtoupper($orderMatch[2] ?? 'DESC');
                if (isset($orderMap[$field])) {
                    $orderBy = $orderMap[$field] . ' ' . $direction;
                }
            }
        }

        if (preg_match('/project\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $matches)) {
            $conditions[] = "pr.key = ?";
            $params[] = strtoupper(trim($matches[1]));
        }

        if (preg_match('/status\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $matches)) {
            $conditions[] = "s.name = ?";
            $params[] = trim($matches[1]);
        }

        if (preg_match('/assignee\s*=\s*currentUser\(\)/i', $jql)) {
            $conditions[] = "i.assignee_id = ?";
            $params[] = $this->userId();
        } elseif (preg_match('/assignee\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $matches)) {
            $conditions[] = "u.display_name LIKE ?";
            $params[] = '%' . trim($matches[1]) . '%';
        }

        if (preg_match('/reporter\s*=\s*currentUser\(\)/i', $jql)) {
            $conditions[] = "i.reporter_id = ?";
            $params[] = $this->userId();
        }

        if (preg_match('/type\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $matches)) {
            $conditions[] = "t.name = ?";
            $params[] = trim($matches[1]);
        }

        if (preg_match('/priority\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $matches)) {
            $conditions[] = "p.name = ?";
            $params[] = trim($matches[1]);
        }

        if (preg_match('/text\s*~\s*["\']([^"\']+)["\']/i', $jql, $matches)) {
            $searchTerm = '%' . trim($matches[1]) . '%';
            $conditions[] = "(i.summary LIKE ? OR i.description LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = empty($conditions) ? '1=1' : implode(' AND ', $conditions);

        $total = (int) Database::selectValue(
            "SELECT COUNT(*)
             FROM issues i
             JOIN projects pr ON i.project_id = pr.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_types t ON i.issue_type_id = t.id
             LEFT JOIN issue_priorities p ON i.priority_id = p.id
             LEFT JOIN users u ON i.assignee_id = u.id
             WHERE $whereClause",
            $params
        );

        $issues = Database::select(
            "SELECT i.*, pr.key as project_key, pr.name as project_name,
                    s.name as status_name, s.color as status_color,
                    t.name as type_name, t.icon as type_icon,
                    p.name as priority_name, p.color as priority_color,
                    u.display_name as assignee_name
             FROM issues i
             JOIN projects pr ON i.project_id = pr.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_types t ON i.issue_type_id = t.id
             LEFT JOIN issue_priorities p ON i.priority_id = p.id
             LEFT JOIN users u ON i.assignee_id = u.id
             WHERE $whereClause
             ORDER BY $orderBy
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return [
            'issues' => $issues,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    private function getUserFilters(): array
    {
        return Database::select(
            "SELECT * FROM saved_filters WHERE user_id = ? ORDER BY name",
            [$this->userId()]
        );
    }
}
