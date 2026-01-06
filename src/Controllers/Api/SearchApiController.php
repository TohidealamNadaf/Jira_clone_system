<?php
/**
 * Search API Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;

class SearchApiController extends Controller
{
    private function apiUser(): array
    {
        return $GLOBALS['api_user'] ?? [];
    }

    private function apiUserId(): int
    {
        return (int) ($this->apiUser()['id'] ?? 0);
    }

    public function search(Request $request): never
    {
        $query = $request->input('q', '');
        $type = $request->input('type');
        $projectId = $request->input('project_id');
        $limit = min((int) ($request->input('limit') ?? 20), 100);

        if (strlen($query) < 2) {
            $this->json(['results' => [], 'total' => 0]);
        }

        $results = [];
        $searchTerm = "%$query%";

        if (!$type || $type === 'issues') {
            $issueWhere = ["(i.summary LIKE ? OR i.issue_key LIKE ? OR i.description LIKE ?)"];
            $issueParams = [$searchTerm, $searchTerm, $searchTerm];

            if ($projectId) {
                $issueWhere[] = "i.project_id = ?";
                $issueParams[] = $projectId;
            }

            $issueWhereClause = implode(' AND ', $issueWhere);

            $issues = Database::select(
                "SELECT i.id, i.issue_key, i.summary, 
                        p.key as project_key, p.name as project_name,
                        it.name as issue_type_name, it.icon as issue_type_icon,
                        s.name as status_name, s.color as status_color
                 FROM issues i
                 JOIN projects p ON i.project_id = p.id
                 JOIN issue_types it ON i.issue_type_id = it.id
                 JOIN statuses s ON i.status_id = s.id
                 WHERE $issueWhereClause
                 ORDER BY i.updated_at DESC
                 LIMIT $limit",
                $issueParams
            );

            foreach ($issues as $issue) {
                $results[] = [
                    'type' => 'issue',
                    'id' => $issue['id'],
                    'key' => $issue['issue_key'],
                    'title' => $issue['summary'],
                    'subtitle' => "{$issue['project_name']} - {$issue['status_name']}",
                    'icon' => $issue['issue_type_icon'],
                    'url' => "/issue/{$issue['issue_key']}",
                    'data' => $issue,
                ];
            }
        }

        if (!$type || $type === 'projects') {
            $projects = Database::select(
                "SELECT id, `key`, name, description
                 FROM projects 
                 WHERE (name LIKE ? OR `key` LIKE ? OR description LIKE ?)
                 AND is_archived = 0
                 ORDER BY name ASC
                 LIMIT $limit",
                [$searchTerm, $searchTerm, $searchTerm]
            );

            foreach ($projects as $project) {
                $results[] = [
                    'type' => 'project',
                    'id' => $project['id'],
                    'key' => $project['key'],
                    'title' => $project['name'],
                    'subtitle' => $project['key'],
                    'url' => "/projects/{$project['key']}",
                    'data' => $project,
                ];
            }
        }

        if (!$type || $type === 'users') {
            $users = Database::select(
                "SELECT id, email, first_name, last_name, display_name, avatar
                 FROM users 
                 WHERE is_active = 1 
                 AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR display_name LIKE ?)
                 ORDER BY display_name ASC
                 LIMIT $limit",
                [$searchTerm, $searchTerm, $searchTerm, $searchTerm]
            );

            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'id' => $user['id'],
                    'title' => $user['display_name'] ?: "{$user['first_name']} {$user['last_name']}",
                    'subtitle' => $user['email'],
                    'avatar' => avatar($user['avatar']),
                    'data' => $user,
                ];
            }
        }

        $this->json([
            'results' => $results,
            'total' => count($results),
            'query' => $query,
        ]);
    }

    public function jql(Request $request): never
    {
        $jql = $request->input('jql', '');
        $page = (int) ($request->input('page') ?? 1);
        $perPage = (int) ($request->input('per_page') ?? 50);

        if (empty($jql)) {
            $this->json(['error' => 'JQL query is required'], 400);
        }

        try {
            $parsed = $this->parseJQL($jql);
            $offset = ($page - 1) * $perPage;

            $total = (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i 
                 JOIN projects p ON i.project_id = p.id
                 JOIN issue_types it ON i.issue_type_id = it.id
                 JOIN statuses s ON i.status_id = s.id
                 JOIN issue_priorities ip ON i.priority_id = ip.id
                 WHERE {$parsed['where']}",
                $parsed['params']
            );

            $issues = Database::select(
                "SELECT i.*, 
                        p.key as project_key, p.name as project_name,
                        it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
                        s.name as status_name, s.color as status_color, s.category as status_category,
                        ip.name as priority_name, ip.icon as priority_icon, ip.color as priority_color,
                        assignee.display_name as assignee_name, assignee.avatar as assignee_avatar,
                        reporter.display_name as reporter_name
                 FROM issues i
                 JOIN projects p ON i.project_id = p.id
                 JOIN issue_types it ON i.issue_type_id = it.id
                 JOIN statuses s ON i.status_id = s.id
                 JOIN issue_priorities ip ON i.priority_id = ip.id
                 LEFT JOIN users assignee ON i.assignee_id = assignee.id
                 LEFT JOIN users reporter ON i.reporter_id = reporter.id
                 WHERE {$parsed['where']}
                 ORDER BY {$parsed['orderBy']}
                 LIMIT $perPage OFFSET $offset",
                $parsed['params']
            );

            $this->json([
                'items' => $issues,
                'total' => $total,
                'jql' => $jql,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
            ]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Invalid JQL: ' . $e->getMessage()], 400);
        }
    }

    private function parseJQL(string $jql): array
    {
        $where = ['1 = 1'];
        $params = [];
        $orderBy = 'i.created_at DESC';

        $orderMatch = [];
        if (preg_match('/ORDER\s+BY\s+(.+)$/i', $jql, $orderMatch)) {
            $orderClause = trim($orderMatch[1]);
            $jql = preg_replace('/ORDER\s+BY\s+.+$/i', '', $jql);
            
            $orderMap = [
                'created' => 'i.created_at',
                'updated' => 'i.updated_at',
                'priority' => 'ip.sort_order',
                'status' => 's.sort_order',
                'summary' => 'i.summary',
                'key' => 'i.issue_key',
                'assignee' => 'assignee.display_name',
                'reporter' => 'reporter.display_name',
                'duedate' => 'i.due_date',
            ];

            if (preg_match('/(\w+)\s*(ASC|DESC)?/i', $orderClause, $om)) {
                $field = strtolower($om[1]);
                $direction = strtoupper($om[2] ?? 'ASC');
                if (isset($orderMap[$field])) {
                    $orderBy = "{$orderMap[$field]} $direction";
                }
            }
        }

        $jql = trim($jql);
        
        if (preg_match('/project\s*=\s*["\']?(\w+)["\']?/i', $jql, $m)) {
            $where[] = "p.key = ?";
            $params[] = $m[1];
        }

        if (preg_match('/status\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $m)) {
            $where[] = "s.name = ?";
            $params[] = trim($m[1]);
        }

        if (preg_match('/assignee\s*=\s*currentUser\(\)/i', $jql)) {
            $where[] = "i.assignee_id = ?";
            $params[] = $this->apiUserId();
        } elseif (preg_match('/assignee\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $m)) {
            $where[] = "(assignee.email = ? OR assignee.display_name = ?)";
            $params[] = trim($m[1]);
            $params[] = trim($m[1]);
        } elseif (preg_match('/assignee\s+IS\s+EMPTY/i', $jql)) {
            $where[] = "i.assignee_id IS NULL";
        }

        if (preg_match('/reporter\s*=\s*currentUser\(\)/i', $jql)) {
            $where[] = "i.reporter_id = ?";
            $params[] = $this->apiUserId();
        } elseif (preg_match('/reporter\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $m)) {
            $where[] = "(reporter.email = ? OR reporter.display_name = ?)";
            $params[] = trim($m[1]);
            $params[] = trim($m[1]);
        }

        if (preg_match('/priority\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $m)) {
            $where[] = "ip.name = ?";
            $params[] = trim($m[1]);
        }

        if (preg_match('/issuetype\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $m)) {
            $where[] = "it.name = ?";
            $params[] = trim($m[1]);
        }

        if (preg_match('/sprint\s*=\s*["\']?([^"\']+)["\']?/i', $jql, $m)) {
            $sprintName = trim($m[1]);
            if (strtolower($sprintName) === 'open') {
                $where[] = "i.sprint_id IN (SELECT id FROM sprints WHERE status IN ('active', 'future'))";
            } else {
                $where[] = "i.sprint_id IN (SELECT id FROM sprints WHERE name = ?)";
                $params[] = $sprintName;
            }
        }

        if (preg_match('/text\s*~\s*["\']?([^"\']+)["\']?/i', $jql, $m)) {
            $searchTerm = '%' . trim($m[1]) . '%';
            $where[] = "(i.summary LIKE ? OR i.description LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (preg_match('/created\s*>=?\s*["\']?(-?\d+[dwmy])["\']?/i', $jql, $m)) {
            $interval = $this->parseRelativeDate($m[1]);
            if ($interval) {
                $where[] = "i.created_at >= ?";
                $params[] = $interval;
            }
        }

        if (preg_match('/updated\s*>=?\s*["\']?(-?\d+[dwmy])["\']?/i', $jql, $m)) {
            $interval = $this->parseRelativeDate($m[1]);
            if ($interval) {
                $where[] = "i.updated_at >= ?";
                $params[] = $interval;
            }
        }

        if (preg_match('/resolution\s+IS\s+EMPTY/i', $jql)) {
            $where[] = "i.resolved_at IS NULL";
        } elseif (preg_match('/resolution\s+IS\s+NOT\s+EMPTY/i', $jql)) {
            $where[] = "i.resolved_at IS NOT NULL";
        }

        return [
            'where' => implode(' AND ', $where),
            'params' => $params,
            'orderBy' => $orderBy,
        ];
    }

    private function parseRelativeDate(string $value): ?string
    {
        if (preg_match('/(-?\d+)([dwmy])/i', $value, $m)) {
            $num = (int) $m[1];
            $unit = strtolower($m[2]);
            
            $unitMap = ['d' => 'day', 'w' => 'week', 'm' => 'month', 'y' => 'year'];
            $sqlUnit = $unitMap[$unit] ?? 'day';
            
            return date('Y-m-d H:i:s', strtotime("$num $sqlUnit"));
        }
        return null;
    }

    public function filters(Request $request): never
    {
        $userId = $this->apiUserId();

        $filters = Database::select(
            "SELECT * FROM saved_filters 
             WHERE user_id = ? OR share_type IN ('global', 'project')
             ORDER BY name ASC",
            [$userId]
        );

        $this->json($filters);
    }

    public function storeFilter(Request $request): never
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'jql' => 'required|max:5000',
            'share_type' => 'nullable|in:private,project,global',
        ]);

        $filterId = Database::insert('saved_filters', [
            'user_id' => $this->apiUserId(),
            'name' => $data['name'],
            'jql' => $data['jql'],
            'share_type' => $data['share_type'] ?? 'private',
        ]);

        $filter = Database::selectOne("SELECT * FROM saved_filters WHERE id = ?", [$filterId]);

        $this->json(['success' => true, 'filter' => $filter], 201);
    }

    public function showFilter(Request $request): never
    {
        $filterId = (int) $request->param('id');
        $userId = $this->apiUserId();

        $filter = Database::selectOne(
            "SELECT * FROM saved_filters WHERE id = ? AND (user_id = ? OR share_type IN ('global', 'project'))",
            [$filterId, $userId]
        );

        if (!$filter) {
            $this->json(['error' => 'Filter not found'], 404);
        }

        $this->json($filter);
    }

    public function updateFilter(Request $request): never
    {
        $filterId = (int) $request->param('id');
        $userId = $this->apiUserId();

        $filter = Database::selectOne(
            "SELECT * FROM saved_filters WHERE id = ? AND user_id = ?",
            [$filterId, $userId]
        );

        if (!$filter) {
            $this->json(['error' => 'Filter not found or not owned by you'], 404);
        }

        $data = $request->validate([
            'name' => 'nullable|max:255',
            'jql' => 'nullable|max:5000',
            'share_type' => 'nullable|in:private,project,global',
        ]);

        $updateData = array_filter($data, fn($v) => $v !== null);

        if (!empty($updateData)) {
            Database::update('saved_filters', $updateData, 'id = ?', [$filterId]);
        }

        $updated = Database::selectOne("SELECT * FROM saved_filters WHERE id = ?", [$filterId]);

        $this->json(['success' => true, 'filter' => $updated]);
    }

    public function destroyFilter(Request $request): never
    {
        $filterId = (int) $request->param('id');
        $userId = $this->apiUserId();

        $filter = Database::selectOne(
            "SELECT * FROM saved_filters WHERE id = ? AND user_id = ?",
            [$filterId, $userId]
        );

        if (!$filter) {
            $this->json(['error' => 'Filter not found or not owned by you'], 404);
        }

        Database::delete('saved_filters', 'id = ?', [$filterId]);

        $this->json(['success' => true, 'message' => 'Filter deleted successfully']);
    }
}
