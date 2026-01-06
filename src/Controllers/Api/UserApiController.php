<?php
/**
 * User API Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;

class UserApiController extends Controller
{
    private function apiUser(): array
    {
        return $GLOBALS['api_user'] ?? [];
    }

    private function apiUserId(): int
    {
        return (int) ($this->apiUser()['id'] ?? 0);
    }

    public function me(Request $request): never
    {
        $userId = $this->apiUserId();

        $user = Database::selectOne(
            "SELECT id, email, first_name, last_name, display_name, avatar, timezone, 
                    locale, job_title, department, is_admin, is_active, created_at, last_login_at
             FROM users WHERE id = ?",
            [$userId]
        );

        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
        }

        $roles = Database::select(
            "SELECT r.id, r.name, r.slug, ur.project_id
             FROM user_roles ur
             JOIN roles r ON ur.role_id = r.id
             WHERE ur.user_id = ?",
            [$userId]
        );

        $user['roles'] = $roles;

        $this->json($user);
    }

    public function updateMe(Request $request): never
    {
        $userId = $this->apiUserId();

        $data = $request->validate([
            'first_name' => 'nullable|max:100',
            'last_name' => 'nullable|max:100',
            'display_name' => 'nullable|max:255',
            'timezone' => 'nullable|max:50',
            'locale' => 'nullable|max:10',
            'job_title' => 'nullable|max:100',
            'department' => 'nullable|max:100',
        ]);

        $updateData = array_filter($data, fn($v) => $v !== null);

        if (!empty($updateData)) {
            Database::update('users', $updateData, 'id = ?', [$userId]);
        }

        $user = Database::selectOne(
            "SELECT id, email, first_name, last_name, display_name, avatar, timezone, 
                    locale, job_title, department, is_admin, is_active
             FROM users WHERE id = ?",
            [$userId]
        );

        $this->json(['success' => true, 'user' => $user]);
    }

    public function index(Request $request): never
    {
        $search = $request->input('search');
        $page = (int) ($request->input('page') ?? 1);
        $perPage = (int) ($request->input('per_page') ?? 25);
        $isActive = $request->input('is_active');

        $where = ['1 = 1'];
        $params = [];

        if ($search) {
            $where[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR display_name LIKE ?)";
            $searchTerm = "%$search%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if ($isActive !== null) {
            $where[] = "is_active = ?";
            $params[] = $isActive === '1' ? 1 : 0;
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM users WHERE $whereClause",
            $params
        );

        $users = Database::select(
            "SELECT id, email, first_name, last_name, display_name, avatar, 
                    job_title, department, is_active, created_at
             FROM users 
             WHERE $whereClause 
             ORDER BY display_name ASC, first_name ASC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        $this->json([
            'items' => $users,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }

    public function show(Request $request): never
    {
        $userId = (int) $request->param('id');

        $user = Database::selectOne(
            "SELECT id, email, first_name, last_name, display_name, avatar, 
                    job_title, department, is_active, created_at
             FROM users WHERE id = ?",
            [$userId]
        );

        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
        }

        $this->json($user);
    }

    public function search(Request $request): never
    {
        $query = $request->input('q', '');
        $projectId = $request->input('project_id');
        $limit = min((int) ($request->input('limit') ?? 10), 50);

        if (strlen($query) < 2) {
            $this->json([]);
        }

        $searchTerm = "%$query%";

        $sql = "SELECT id, email, first_name, last_name, display_name, avatar
                FROM users 
                WHERE is_active = 1 
                AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR display_name LIKE ?)";
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];

        if ($projectId) {
            $sql .= " AND id IN (SELECT user_id FROM project_members WHERE project_id = ?)";
            $params[] = $projectId;
        }

        $sql .= " ORDER BY display_name ASC, first_name ASC LIMIT $limit";

        $users = Database::select($sql, $params);

        $this->json($users);
    }

    public function active(Request $request): never
    {
        $users = Database::select(
            "SELECT id, email, first_name, last_name, display_name, avatar
             FROM users 
             WHERE is_active = 1
             ORDER BY display_name ASC, first_name ASC",
            []
        );

        // Add 'name' field for frontend compatibility
        foreach ($users as &$user) {
            $user['name'] = $user['display_name'] ?? trim($user['first_name'] . ' ' . $user['last_name']);
        }

        $this->json($users);
    }

    public function notifications(Request $request): never
    {
        $userId = $this->apiUserId();
        $unreadOnly = $request->input('unread') === '1';
        $page = (int) ($request->input('page') ?? 1);
        $perPage = (int) ($request->input('per_page') ?? 25);

        $where = ['user_id = ?'];
        $params = [$userId];

        if ($unreadOnly) {
            $where[] = 'read_at IS NULL';
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM notifications WHERE $whereClause",
            $params
        );

        $notifications = Database::select(
            "SELECT * FROM notifications 
             WHERE $whereClause 
             ORDER BY created_at DESC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        $unreadCount = (int) Database::selectValue(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND read_at IS NULL",
            [$userId]
        );

        $this->json([
            'items' => $notifications,
            'total' => $total,
            'unread_count' => $unreadCount,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ]);
    }

    public function markRead(Request $request): never
    {
        $userId = $this->apiUserId();

        $data = $request->validate([
            'notification_ids' => 'nullable|array',
            'mark_all' => 'nullable|boolean',
        ]);

        if (!empty($data['mark_all'])) {
            Database::update(
                'notifications',
                ['read_at' => date('Y-m-d H:i:s')],
                'user_id = ? AND read_at IS NULL',
                [$userId]
            );
        } elseif (!empty($data['notification_ids'])) {
            $ids = $data['notification_ids'];
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            Database::query(
                "UPDATE notifications SET read_at = ? WHERE id IN ($placeholders) AND user_id = ?",
                array_merge([date('Y-m-d H:i:s')], $ids, [$userId])
            );
        }

        $unreadCount = (int) Database::selectValue(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND read_at IS NULL",
            [$userId]
        );

        $this->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }
}
