<?php
/**
 * Admin Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminController extends Controller
{
    public function index(Request $request): string
    {
        $stats = [
            'total_users' => (int) Database::selectValue("SELECT COUNT(*) FROM users"),
            'active_users' => (int) Database::selectValue("SELECT COUNT(*) FROM users WHERE is_active = 1"),
            'total_projects' => (int) Database::selectValue("SELECT COUNT(*) FROM projects WHERE is_archived = 0"),
            'total_issues' => (int) Database::selectValue("SELECT COUNT(*) FROM issues"),
            'open_issues' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i 
                 JOIN statuses s ON i.status_id = s.id 
                 WHERE s.category != 'done'"
            ),
            'storage_used' => '0 MB',
            'disk_usage' => '75%',
        ];

        $recentActivity = Database::select(
            "SELECT a.*, u.display_name as user_name
             FROM audit_logs a
             LEFT JOIN users u ON a.user_id = u.id
             ORDER BY a.created_at DESC
             LIMIT 10"
        );

        if ($request->wantsJson()) {
            $this->json([
                'stats' => $stats,
                'recent_activity' => $recentActivity,
            ]);
        }

        return $this->view('admin.index', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }

    public function users(Request $request): string
    {
        $this->authorize('admin.manage-users');
        
        $search = $request->input('search');
        $status = $request->input('status');
        $role = $request->input('role');
        $page = (int) ($request->input('page') ?? 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $conditions = [];
        $params = [];

        if ($search) {
            $conditions[] = "(u.display_name LIKE ? OR u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $params = array_merge($params, ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"]);
        }

        if ($status === 'active') {
            $conditions[] = "u.is_active = 1";
        } elseif ($status === 'inactive') {
            $conditions[] = "u.is_active = 0";
        } elseif ($status === 'pending') {
            $conditions[] = "u.email_verified_at IS NULL";
        }

        if ($role) {
            $conditions[] = "EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = u.id AND ur.role_id = ?)";
            $params[] = (int) $role;
        }

        $whereClause = empty($conditions) ? '1=1' : implode(' AND ', $conditions);

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM users u WHERE $whereClause",
            $params
        );

        $users = Database::select(
            "SELECT u.*, 
                    (SELECT GROUP_CONCAT(r.name) FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = u.id) as roles
             FROM users u
             WHERE $whereClause
             ORDER BY u.display_name
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        $pagination = [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
        
        // Get roles for filter dropdown
        $roles = Database::select("SELECT * FROM roles ORDER BY name");

        if ($request->wantsJson()) {
            $this->json([
                'users' => $users,
                'pagination' => $pagination,
            ]);
        }

        return $this->view('admin.users', [
            'users' => $users,
            'pagination' => $pagination,
            'search' => $search,
            'status' => $status,
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'role' => $role,
            ],
        ]);
    }

    public function createUser(Request $request): string
    {
        $this->authorize('admin.manage-users');
        
        $roles = Database::select("SELECT * FROM roles ORDER BY name");
        $timezones = timezone_identifiers_list();

        return $this->view('admin.user-form', [
            'roles' => $roles,
            'timezones' => $timezones,
        ]);
    }

    public function storeUser(Request $request): void
    {
        $this->authorize('admin.manage-users');
        
        $data = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:255',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|min:8',
            'role_id' => 'required|integer',
            'timezone' => 'nullable|max:50',
            'is_admin' => 'nullable|boolean',
        ]);

        try {
            // Check passwords match
            if ($data['password'] !== $data['password_confirmation']) {
                throw new \InvalidArgumentException('Passwords do not match.');
            }
            
            // Check for existing email
            $existing = Database::selectOne(
                "SELECT id FROM users WHERE email = ?",
                [$data['email']]
            );

            if ($existing) {
                throw new \InvalidArgumentException('Email already exists.');
            }

            // Create user
            $userId = Database::insert('users', [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password_hash' => password_hash($data['password'], PASSWORD_ARGON2ID),
                'timezone' => $data['timezone'] ?? 'UTC',
                'is_active' => 1,
                'is_admin' => (bool) ($data['is_admin'] ?? false),
            ]);

            // Assign role
            if ($data['role_id']) {
                Database::insert('user_roles', [
                    'user_id' => $userId,
                    'role_id' => (int) $data['role_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $this->logAudit('user_created', 'user', $userId, null, $data['email']);

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'user_id' => $userId], 201);
            }

            $this->redirectWith(url('/admin/users'), 'success', 'User created successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            Session::flash('_old_input', $data);
            $this->back();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to create user: ' . $e->getMessage()], 500);
            }

            Session::flash('error', 'Failed to create user: ' . $e->getMessage());
            $this->back();
        }
    }

    public function editUser(Request $request): string
    {
        $this->authorize('admin.manage-users');
        
        $userId = (int) $request->param('id');

        $user = Database::selectOne("SELECT * FROM users WHERE id = ?", [$userId]);

        if (!$user) {
            abort(404, 'User not found');
        }

        $roles = Database::select("SELECT * FROM roles ORDER BY name");
        $timezones = timezone_identifiers_list();

        return $this->view('admin.user-form', [
            'editUser' => $user,
            'roles' => $roles,
            'timezones' => $timezones,
            'isAdmin' => $user['is_admin'] ?? false,
        ]);
    }

    public function updateUser(Request $request): void
    {
        $this->authorize('admin.manage-users');
        
        $userId = (int) $request->param('id');

        $user = Database::selectOne("SELECT * FROM users WHERE id = ?", [$userId]);

        if (!$user) {
            abort(404, 'User not found');
        }

        // Prevent updating administrators
        if ($user['is_admin']) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Administrator users cannot be edited.'], 403);
            }
            $this->redirectWith(url('/admin/users'), 'error', 'Administrator users cannot be edited.');
        }

        $data = $request->validate([
            'first_name' => 'nullable|max:100',
            'last_name' => 'nullable|max:100',
            'email' => 'nullable|email|max:255',
            'display_name' => 'nullable|max:100',
            'password' => 'nullable|min:8',
            'password_confirmation' => 'nullable|min:8',
            'role_id' => 'nullable|integer',
            'job_title' => 'nullable|max:100',
            'department' => 'nullable|max:100',
            'location' => 'nullable|max:100',
            'timezone' => 'nullable|max:100',
            'is_admin' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive,pending',
        ]);

        try {
            // Check password confirmation if password is being changed
            if (!empty($data['password'])) {
                if ($data['password'] !== ($data['password_confirmation'] ?? '')) {
                    throw new \InvalidArgumentException('Passwords do not match.');
                }
            }
            
            // Check email uniqueness
            if (isset($data['email']) && $data['email'] !== $user['email']) {
                $existing = Database::selectOne(
                    "SELECT id FROM users WHERE email = ? AND id != ?",
                    [$data['email'], $userId]
                );
                if ($existing) {
                    throw new \InvalidArgumentException('Email already exists.');
                }
            }

            // Build update data
            $updateData = [];
            
            if (isset($data['first_name'])) $updateData['first_name'] = $data['first_name'];
            if (isset($data['last_name'])) $updateData['last_name'] = $data['last_name'];
            if (isset($data['email'])) $updateData['email'] = $data['email'];
            if (isset($data['display_name'])) $updateData['display_name'] = $data['display_name'];
            if (isset($data['job_title'])) $updateData['job_title'] = $data['job_title'];
            if (isset($data['department'])) $updateData['department'] = $data['department'];
            if (isset($data['location'])) $updateData['location'] = $data['location'];
            if (isset($data['timezone'])) $updateData['timezone'] = $data['timezone'];
            
            // Do NOT allow changing is_admin flag
            // is_admin can only be set during user creation via direct database modification
            // or by privileged system operations
            
            if (isset($data['status'])) {
                $updateData['is_active'] = $data['status'] === 'active' ? 1 : 0;
            }
            if (!empty($data['password'])) {
                $updateData['password_hash'] = password_hash($data['password'], PASSWORD_ARGON2ID);
            }

            $updateData['updated_at'] = date('Y-m-d H:i:s');

            if (!empty($updateData)) {
                Database::update('users', $updateData, 'id = :id', ['id' => $userId]);
            }

            // Update role
            if (isset($data['role_id'])) {
                Database::delete('user_roles', 'user_id = :user_id', ['user_id' => $userId]);
                if ($data['role_id']) {
                    Database::insert('user_roles', [
                        'user_id' => $userId,
                        'role_id' => (int) $data['role_id'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->logAudit('user_updated', 'user', $userId);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/users'), 'success', 'User updated successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->redirect(url("/admin/users/{$userId}/edit"));
        }
    }

    public function deleteUser(Request $request): void
    {
        $userId = (int) $request->param('id');

        if ($userId === $this->userId()) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'You cannot delete your own account.'], 422);
            }
            $this->redirectWith(url('/admin/users'), 'error', 'You cannot delete your own account.');
        }

        $user = Database::selectOne("SELECT * FROM users WHERE id = ?", [$userId]);

        if (!$user) {
            abort(404, 'User not found');
        }

        // Prevent deleting administrators
        if ($user['is_admin']) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Administrator users cannot be deleted.'], 403);
            }
            $this->redirectWith(url('/admin/users'), 'error', 'Administrator users cannot be deleted.');
        }

        try {
            Database::update('users', [
                'is_active' => false,
                'deleted_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', ['id' => $userId]);

            Database::delete('user_roles', 'user_id = :user_id', ['user_id' => $userId]);
            Database::delete('user_groups', 'user_id = :user_id', ['user_id' => $userId]);

            $this->logAudit('user_deleted', 'user', $userId, $user['username']);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/users'), 'success', 'User deleted successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to delete user.'], 500);
            }

            $this->redirectWith(url('/admin/users'), 'error', 'Failed to delete user.');
        }
    }

    public function deactivateUser(Request $request): void
    {
        $this->authorize('admin.manage-users');
        
        $userId = (int) $request->param('id');

        if ($userId === $this->userId()) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'You cannot deactivate your own account.'], 422);
            }
            $this->redirectWith(url('/admin/users'), 'error', 'You cannot deactivate your own account.');
        }

        $user = Database::selectOne("SELECT * FROM users WHERE id = ?", [$userId]);

        if (!$user) {
            abort(404, 'User not found');
        }

        // Prevent deactivating administrators
        if ($user['is_admin']) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Administrator users cannot be deactivated.'], 403);
            }
            $this->redirectWith(url('/admin/users'), 'error', 'Administrator users cannot be deactivated.');
        }

        try {
            Database::update('users', [
                'is_active' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$userId]);

            $this->logAudit('user_deactivated', 'user', $userId);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/users'), 'success', 'User deactivated successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to deactivate user.'], 500);
            }

            $this->redirectWith(url('/admin/users'), 'error', 'Failed to deactivate user.');
        }
    }

    public function activateUser(Request $request): void
    {
        $this->authorize('admin.manage-users');
        
        $userId = (int) $request->param('id');

        $user = Database::selectOne("SELECT * FROM users WHERE id = ?", [$userId]);

        if (!$user) {
            abort(404, 'User not found');
        }

        try {
            Database::update('users', [
                'is_active' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$userId]);

            $this->logAudit('user_activated', 'user', $userId);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/users'), 'success', 'User activated successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to activate user.'], 500);
            }

            $this->redirectWith(url('/admin/users'), 'error', 'Failed to activate user.');
        }
    }

    public function roles(Request $request): string
    {
        $roles = Database::select(
            "SELECT r.*, 
                    (SELECT COUNT(*) FROM user_roles ur WHERE ur.role_id = r.id) as user_count,
                    (SELECT COUNT(*) FROM role_permissions rp WHERE rp.role_id = r.id) as permission_count
             FROM roles r
             ORDER BY r.name"
        );

        if ($request->wantsJson()) {
            $this->json($roles);
        }

        return $this->view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function showRole(Request $request): string
    {
        $roleId = (int) $request->param('id');

        $role = Database::selectOne("SELECT * FROM roles WHERE id = ?", [$roleId]);

        if (!$role) {
            abort(404, 'Role not found');
        }

        $permissions = Database::select(
            "SELECT p.* FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?
             ORDER BY p.category, p.name",
            [$roleId]
        );

        $allPermissions = Database::select(
            "SELECT * FROM permissions ORDER BY category, name"
        );

        $users = Database::select(
            "SELECT u.id, u.display_name, u.email
             FROM users u
             JOIN user_roles ur ON u.id = ur.user_id
             WHERE ur.role_id = ?
             ORDER BY u.display_name",
            [$roleId]
        );

        if ($request->wantsJson()) {
            $this->json([
                'role' => $role,
                'permissions' => $permissions,
                'all_permissions' => $allPermissions,
                'users' => $users,
            ]);
        }

        return $this->view('admin.roles.show', [
            'role' => $role,
            'permissions' => $permissions,
            'allPermissions' => $allPermissions,
            'users' => $users,
        ]);
    }

    public function createRole(Request $request): string
    {
        $allPermissions = Database::select(
            "SELECT * FROM permissions ORDER BY category, name"
        );

        return $this->view('admin.roles.form', [
            'role' => null,
            'permissions' => [],
            'allPermissions' => $allPermissions,
        ]);
    }

    public function editRole(Request $request): string
    {
        $roleId = (int) $request->param('id');

        $role = Database::selectOne("SELECT * FROM roles WHERE id = ?", [$roleId]);

        if (!$role) {
            abort(404, 'Role not found');
        }

        // Allow viewing system roles, but pass flag to disable editing in view
        $isSystemRole = $role['is_system'] ?? false;

        $permissions = Database::select(
            "SELECT permission_id FROM role_permissions WHERE role_id = ?",
            [$roleId]
        );
        $permissionIds = array_column($permissions, 'permission_id');

        $allPermissions = Database::select(
            "SELECT * FROM permissions ORDER BY category, name"
        );

        return $this->view('admin.roles.form', [
            'role' => $role,
            'permissions' => $permissionIds,
            'allPermissions' => $allPermissions,
            'isSystemRole' => $isSystemRole,
        ]);
    }

    public function storeRole(Request $request): void
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:500',
            'permissions' => 'nullable|array',
        ]);

        try {
            $roleId = Database::insert('roles', [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_system' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $permissionId) {
                    Database::insert('role_permissions', [
                        'role_id' => $roleId,
                        'permission_id' => (int) $permissionId,
                    ]);
                }
            }

            $this->logAudit('role_created', 'role', $roleId);
            $this->redirectWith(url("/admin/roles/{$roleId}"), 'success', 'Role created successfully.');
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to create role: ' . $e->getMessage());
            $this->back();
        }
    }

    public function deleteRole(Request $request): void
    {
        $roleId = (int) $request->param('id');

        $role = Database::selectOne("SELECT * FROM roles WHERE id = ?", [$roleId]);

        if (!$role) {
            abort(404, 'Role not found');
        }

        if ($role['is_system'] ?? false) {
            $this->redirectWith(url('/admin/roles'), 'error', 'System roles cannot be deleted.');
            return;
        }

        try {
            Database::delete('role_permissions', 'role_id = :role_id', ['role_id' => $roleId]);
            Database::delete('user_roles', 'role_id = :role_id', ['role_id' => $roleId]);
            Database::delete('roles', 'id = :id', ['id' => $roleId]);

            $this->logAudit('role_deleted', 'role', $roleId);
            $this->redirectWith(url('/admin/roles'), 'success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            $this->redirectWith(url('/admin/roles'), 'error', 'Failed to delete role.');
        }
    }

    public function updateRole(Request $request): void
    {
        $roleId = (int) $request->param('id');

        $role = Database::selectOne("SELECT * FROM roles WHERE id = ?", [$roleId]);

        if (!$role) {
            abort(404, 'Role not found');
        }

        // Prevent updating system roles
        if ($role['is_system'] ?? false) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'System roles cannot be edited.'], 403);
            }
            $this->redirectWith(url('/admin/roles'), 'error', 'System roles cannot be edited.');
        }

        $data = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:500',
            'permissions' => 'nullable',
        ]);

        try {
            // Update role basic info
            Database::update('roles', [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$roleId]);

            // Update permissions
            Database::delete('role_permissions', 'role_id = ?', [$roleId]);
            
            $permissions = $request->input('permissions');
            if (!empty($permissions) && is_array($permissions)) {
                foreach ($permissions as $permissionId) {
                    Database::insert('role_permissions', [
                        'role_id' => $roleId,
                        'permission_id' => (int) $permissionId,
                    ]);
                }
            }

            $this->logAudit('role_updated', 'role', $roleId);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url("/admin/roles/{$roleId}"), 'success', 'Role updated successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to update role: ' . $e->getMessage()], 500);
            }

            $this->redirectWith(url("/admin/roles/{$roleId}/edit"), 'error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    public function groups(Request $request): string
    {
        $groups = Database::select(
            "SELECT g.*, 
                    (SELECT COUNT(*) FROM user_groups ug WHERE ug.group_id = g.id) as user_count
             FROM groups g
             ORDER BY g.name"
        );

        if ($request->wantsJson()) {
            $this->json($groups);
        }

        return $this->view('admin.groups.index', [
            'groups' => $groups,
        ]);
    }

    public function storeGroup(Request $request): void
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:500',
        ]);

        try {
            $existing = Database::selectOne(
                "SELECT id FROM groups WHERE name = ?",
                [$data['name']]
            );

            if ($existing) {
                throw new \InvalidArgumentException('Group name already exists.');
            }

            $groupId = Database::insert('groups', [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->logAudit('group_created', 'group', $groupId, null, $data['name']);

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'group_id' => $groupId], 201);
            }

            $this->redirectWith(url('/admin/groups'), 'success', 'Group created successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->redirect(url('/admin/groups'));
        }
    }

    public function updateGroup(Request $request): void
    {
        $groupId = (int) $request->param('id');

        $group = Database::selectOne("SELECT * FROM groups WHERE id = ?", [$groupId]);

        if (!$group) {
            abort(404, 'Group not found');
        }

        $data = $request->validate([
            'name' => 'nullable|max:100',
            'description' => 'nullable|max:500',
            'users' => 'nullable|array',
        ]);

        try {
            if (isset($data['name']) && $data['name'] !== $group['name']) {
                $existing = Database::selectOne(
                    "SELECT id FROM groups WHERE name = ? AND id != ?",
                    [$data['name'], $groupId]
                );
                if ($existing) {
                    throw new \InvalidArgumentException('Group name already exists.');
                }
            }

            Database::update('groups', array_filter([
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
            ], fn($v) => $v !== null), 'id = :id', ['id' => $groupId]);

            if (array_key_exists('users', $data)) {
                Database::delete('user_groups', 'group_id = :group_id', ['group_id' => $groupId]);
                if (!empty($data['users'])) {
                    foreach ($data['users'] as $userId) {
                        Database::insert('user_groups', [
                            'user_id' => (int) $userId,
                            'group_id' => $groupId,
                        ]);
                    }
                }
            }

            $this->logAudit('group_updated', 'group', $groupId);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/groups'), 'success', 'Group updated successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(url('/admin/groups'), 'error', $e->getMessage());
        }
    }

    public function deleteGroup(Request $request): void
    {
        $groupId = (int) $request->param('id');

        $group = Database::selectOne("SELECT * FROM groups WHERE id = ?", [$groupId]);

        if (!$group) {
            abort(404, 'Group not found');
        }

        try {
            Database::delete('user_groups', 'group_id = :group_id', ['group_id' => $groupId]);
            Database::delete('groups', 'id = :id', ['id' => $groupId]);

            $this->logAudit('group_deleted', 'group', $groupId, $group['name']);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/groups'), 'success', 'Group deleted successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to delete group.'], 500);
            }

            $this->redirectWith(url('/admin/groups'), 'error', 'Failed to delete group.');
        }
    }

    public function workflows(Request $request): string
    {
        $workflows = Database::select(
            "SELECT w.*, 
                    (SELECT COUNT(*) FROM project_workflows pw WHERE pw.workflow_id = w.id) as project_count
             FROM workflows w
             ORDER BY w.name"
        );

        if ($request->wantsJson()) {
            $this->json($workflows);
        }

        return $this->view('admin.workflows.index', [
            'workflows' => $workflows,
        ]);
    }

    public function showWorkflow(Request $request): string
    {
        $workflowId = (int) $request->param('id');

        $workflow = Database::selectOne("SELECT * FROM workflows WHERE id = ?", [$workflowId]);

        if (!$workflow) {
            abort(404, 'Workflow not found');
        }

        $statuses = Database::select(
            "SELECT s.* FROM statuses s
             JOIN workflow_statuses ws ON s.id = ws.status_id
             WHERE ws.workflow_id = ?
             ORDER BY ws.order_num",
            [$workflowId]
        );

        $transitions = Database::select(
            "SELECT t.*, 
                    fs.name as from_status_name,
                    ts.name as to_status_name
             FROM workflow_transitions t
             JOIN statuses fs ON t.from_status_id = fs.id
             JOIN statuses ts ON t.to_status_id = ts.id
             WHERE t.workflow_id = ?
             ORDER BY fs.name, ts.name",
            [$workflowId]
        );

        if ($request->wantsJson()) {
            $this->json([
                'workflow' => $workflow,
                'statuses' => $statuses,
                'transitions' => $transitions,
            ]);
        }

        return $this->view('admin.workflows.show', [
            'workflow' => $workflow,
            'statuses' => $statuses,
            'transitions' => $transitions,
        ]);
    }

    public function issueTypes(Request $request): string
    {
        $issueTypes = Database::select(
            "SELECT it.*, 
                    (SELECT COUNT(*) FROM issues i WHERE i.issue_type_id = it.id) as issue_count
             FROM issue_types it
             ORDER BY it.sort_order, it.name"
        );

        if ($request->wantsJson()) {
            $this->json($issueTypes);
        }

        return $this->view('admin.issue-types', [
            'issueTypes' => $issueTypes,
        ]);
    }

    public function storeIssueType(Request $request): void
    {
        $data = $request->validate([
            'name' => 'required|max:50',
            'description' => 'nullable|max:500',
            'icon' => 'nullable|max:50',
            'color' => 'nullable|max:7',
            'is_subtask' => 'nullable|boolean',
        ]);

        try {
            $existing = Database::selectOne(
                "SELECT id FROM issue_types WHERE name = ?",
                [$data['name']]
            );

            if ($existing) {
                throw new \InvalidArgumentException('Issue type name already exists.');
            }

            $maxSortOrder = (int) Database::selectValue("SELECT MAX(sort_order) FROM issue_types") ?? 0;

            Database::insert('issue_types', [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? 'circle',
                'color' => $data['color'] ?? '#4A90D9',
                'is_subtask' => (int) ($data['is_subtask'] ?? 0),
                'sort_order' => $maxSortOrder + 1,
            ]);

            $this->logAudit('issue_type_created', 'issue_type', null, null, $data['name']);

            if ($request->wantsJson()) {
                $this->json(['success' => true], 201);
            }

            $this->redirectWith(url('/admin/issue-types'), 'success', 'Issue type created successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->back();
        }
    }

    public function updateIssueType(Request $request): void
    {
        $typeId = (int) $request->param('id');

        $issueType = Database::selectOne("SELECT * FROM issue_types WHERE id = ?", [$typeId]);

        if (!$issueType) {
            abort(404, 'Issue type not found');
        }

        $data = $request->validate([
            'name' => 'nullable|max:50',
            'description' => 'nullable|max:500',
            'icon' => 'nullable|max:50',
            'color' => 'nullable|max:7',
            'is_subtask' => 'nullable|boolean',
        ]);

        try {
            if (isset($data['name']) && $data['name'] !== $issueType['name']) {
                $existing = Database::selectOne(
                    "SELECT id FROM issue_types WHERE name = ? AND id != ?",
                    [$data['name'], $typeId]
                );
                if ($existing) {
                    throw new \InvalidArgumentException('Issue type name already exists.');
                }
            }

            Database::update('issue_types', array_filter([
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? null,
                'color' => $data['color'] ?? null,
                'is_subtask' => isset($data['is_subtask']) ? (int) $data['is_subtask'] : null,
            ], fn($v) => $v !== null), 'id = ?', [$typeId]);

            $this->logAudit('issue_type_updated', 'issue_type', $typeId);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/issue-types'), 'success', 'Issue type updated successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->back();
        }
    }

    public function deleteIssueType(Request $request): void
    {
        $typeId = (int) $request->param('id');

        $issueType = Database::selectOne("SELECT * FROM issue_types WHERE id = ?", [$typeId]);

        if (!$issueType) {
            abort(404, 'Issue type not found');
        }

        try {
            $issueCount = (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues WHERE issue_type_id = ?",
                [$typeId]
            );

            if ($issueCount > 0) {
                throw new \InvalidArgumentException('Cannot delete issue type with associated issues.');
            }

            Database::delete('issue_types', 'id = ?', [$typeId]);

            $this->logAudit('issue_type_deleted', 'issue_type', $typeId, $issueType['name']);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/issue-types'), 'success', 'Issue type deleted successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(url('/admin/issue-types'), 'error', $e->getMessage());
        }
    }

    public function auditLog(Request $request): string
    {
        $page = (int) ($request->input('page') ?? 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $entityType = $request->input('entity_type');
        $action = $request->input('action');
        $userId = $request->input('user_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $conditions = [];
        $params = [];

        if ($entityType) {
            $conditions[] = "a.entity_type = ?";
            $params[] = $entityType;
        }

        if ($action) {
            $conditions[] = "a.action = ?";
            $params[] = $action;
        }

        if ($userId) {
            $conditions[] = "a.user_id = ?";
            $params[] = $userId;
        }

        if ($dateFrom) {
            $conditions[] = "DATE(a.created_at) >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $conditions[] = "DATE(a.created_at) <= ?";
            $params[] = $dateTo;
        }

        $whereClause = empty($conditions) ? '1=1' : implode(' AND ', $conditions);

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM audit_logs a WHERE $whereClause",
            $params
        );

        $logs = Database::select(
            "SELECT a.*, u.display_name as user_name
             FROM audit_logs a
             LEFT JOIN users u ON a.user_id = u.id
             WHERE $whereClause
             ORDER BY a.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        $pagination = [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];

        if ($request->wantsJson()) {
            $this->json([
                'logs' => $logs,
                'pagination' => $pagination,
            ]);
        }

        return $this->view('admin.audit-log', [
            'logs' => $logs,
            'pagination' => $pagination,
            'filters' => [
                'entity_type' => $entityType,
                'action' => $action,
                'user_id' => $userId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function settings(Request $request): string
    {
        $rows = Database::select("SELECT * FROM settings ORDER BY `key`");

        // Convert to key-value array for easy template access
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }

        if ($request->wantsJson()) {
            $this->json($settings);
        }

        // Get all available timezones
        $timezones = \DateTimeZone::listIdentifiers();

        return $this->view('admin.settings', [
            'settings' => $settings,
            'timezones' => $timezones,
        ]);
    }

    public function globalPermissions(Request $request): string
    {
        $permissions = Database::select(
            "SELECT * FROM permissions ORDER BY category, name"
        );

        $grouped = [];
        foreach ($permissions as $permission) {
            $category = $permission['category'] ?? 'Other';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $permission;
        }

        if ($request->wantsJson()) {
            $this->json([
                'permissions' => $permissions,
                'grouped' => $grouped,
            ]);
        }

        return $this->view('admin.global-permissions', [
            'permissions' => $permissions,
            'grouped' => $grouped,
        ]);
    }

    public function updateGlobalPermissions(Request $request): void
    {
        $data = $request->validate([
            'permissions' => 'nullable|array',
        ]);

        try {
            $permissions = $request->input('permissions', []);
            if (!is_array($permissions)) {
                $permissions = [];
            }

            foreach ($permissions as $permissionId => $permissionData) {
                if (!is_array($permissionData)) {
                    continue;
                }

                $updates = array_filter([
                    'description' => $permissionData['description'] ?? null,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], fn($v) => $v !== null);

                if (!empty($updates)) {
                    Database::update('permissions', $updates, 'id = :id', ['id' => (int) $permissionId]);
                }
            }

            $this->logAudit('global_permissions_updated', 'system', null);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/global-permissions'), 'success', 'Global permissions updated successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to update global permissions.'], 500);
            }

            $this->redirectWith(url('/admin/global-permissions'), 'error', 'Failed to update global permissions.');
        }
    }

    public function projects(Request $request): string
    {
        $page = (int) ($request->input('page') ?? 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $search = $request->input('search');

        $conditions = [];
        $params = [];

        if ($search) {
            $conditions[] = "(p.name LIKE ? OR p.key LIKE ?)";
            $params = ["%{$search}%", "%{$search}%"];
        }

        $whereClause = empty($conditions) ? '1=1' : implode(' AND ', $conditions);

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM projects p WHERE $whereClause",
            $params
        );

        $projects = Database::select(
            "SELECT p.*, 
                    (SELECT COUNT(*) FROM issues i WHERE i.project_id = p.id) as issue_count,
                    (SELECT COUNT(*) FROM project_members pm WHERE pm.project_id = p.id) as member_count
             FROM projects p
             WHERE $whereClause
             ORDER BY p.name
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        $pagination = [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];

        if ($request->wantsJson()) {
            $this->json([
                'projects' => $projects,
                'pagination' => $pagination,
            ]);
        }

        return $this->view('admin.projects', [
            'projects' => $projects,
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    public function projectCategories(Request $request): string
    {
        $categories = Database::select(
            "SELECT pc.*, 
                    (SELECT COUNT(*) FROM projects p WHERE p.category_id = pc.id) as project_count
             FROM project_categories pc
             ORDER BY pc.name"
        );

        if ($request->wantsJson()) {
            $this->json($categories);
        }

        return $this->view('admin.project-categories', [
            'categories' => $categories,
        ]);
    }

    public function storeProjectCategory(Request $request): void
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:500',
        ]);

        try {
            $existing = Database::selectOne(
                "SELECT id FROM project_categories WHERE name = ?",
                [$data['name']]
            );

            if ($existing) {
                throw new \InvalidArgumentException('Category name already exists.');
            }

            $categoryId = Database::insert('project_categories', [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            $this->logAudit('project_category_created', 'project_category', $categoryId, null, $data['name']);

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'category_id' => $categoryId], 201);
            }

            $this->redirectWith(url('/admin/project-categories'), 'success', 'Project category created successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->back();
        }
    }

    public function updateProjectCategory(Request $request): void
    {
        $categoryId = (int) $request->param('id');

        $category = Database::selectOne("SELECT * FROM project_categories WHERE id = ?", [$categoryId]);

        if (!$category) {
            abort(404, 'Project category not found');
        }

        $data = $request->validate([
            'name' => 'nullable|max:100',
            'description' => 'nullable|max:500',
        ]);

        try {
            if (isset($data['name']) && $data['name'] !== $category['name']) {
                $existing = Database::selectOne(
                    "SELECT id FROM project_categories WHERE name = ? AND id != ?",
                    [$data['name'], $categoryId]
                );
                if ($existing) {
                    throw new \InvalidArgumentException('Category name already exists.');
                }
            }

            Database::update('project_categories', array_filter([
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
            ], fn($v) => $v !== null), 'id = ?', [$categoryId]);

            $this->logAudit('project_category_updated', 'project_category', $categoryId);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/project-categories'), 'success', 'Project category updated successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->back();
        }
    }

    public function deleteProjectCategory(Request $request): void
    {
        $categoryId = (int) $request->param('id');

        $category = Database::selectOne("SELECT * FROM project_categories WHERE id = ?", [$categoryId]);

        if (!$category) {
            abort(404, 'Project category not found');
        }

        try {
            $projectCount = (int) Database::selectValue(
                "SELECT COUNT(*) FROM projects WHERE category_id = ?",
                [$categoryId]
            );

            if ($projectCount > 0) {
                throw new \InvalidArgumentException('Cannot delete category with associated projects.');
            }

            Database::delete('project_categories', 'id = ?', [$categoryId]);

            $this->logAudit('project_category_deleted', 'project_category', $categoryId, $category['name']);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/project-categories'), 'success', 'Project category deleted successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            $this->redirectWith(url('/admin/project-categories'), 'error', $e->getMessage());
        }
    }

    public function updateSettings(Request $request): void
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url',
            'default_timezone' => 'nullable|string',
            'default_language' => 'nullable|string',
            'date_format' => 'nullable|string',
            'logo' => 'nullable|file|image|max:2048',
            'favicon' => 'nullable|file|image|max:1024',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'default_theme' => 'nullable|in:light,dark,auto',
            'mail_driver' => 'nullable|in:smtp,sendmail,log',
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|numeric',
            'smtp_encryption' => 'nullable|in:tls,ssl,none',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
            'require_2fa' => 'nullable|boolean',
            'session_timeout' => 'nullable|numeric',
            'password_min_length' => 'nullable|numeric',
            'password_require_special' => 'nullable|boolean',
            'max_login_attempts' => 'nullable|numeric',
            'lockout_duration' => 'nullable|numeric',
            'slack_webhook' => 'nullable|string',
            'github_client_id' => 'nullable|string',
            'github_client_secret' => 'nullable|string',
            'notify_issue_assigned' => 'nullable|boolean',
            'notify_issue_updated' => 'nullable|boolean',
            'notify_comment_added' => 'nullable|boolean',
            'notify_mentioned' => 'nullable|boolean',
        ]);

        try {
            $keys = [
                'app_name', 'app_url', 'default_timezone', 'default_language', 'date_format',
                'primary_color', 'default_theme', 'mail_driver', 'smtp_host', 'smtp_port',
                'smtp_encryption', 'smtp_username', 'smtp_password', 'mail_from_address', 'mail_from_name',
                'require_2fa', 'session_timeout', 'password_min_length', 'password_require_special',
                'max_login_attempts', 'lockout_duration', 'slack_webhook', 'github_client_id',
                'github_client_secret', 'notify_issue_assigned', 'notify_issue_updated',
                'notify_comment_added', 'notify_mentioned'
            ];

            foreach ($keys as $key) {
                $value = $request->input($key);
                
                // Handle checkboxes - always save 0 or 1
                if (in_array($key, ['require_2fa', 'password_require_special', 'notify_issue_assigned', 'notify_issue_updated', 'notify_comment_added', 'notify_mentioned'])) {
                    $value = $request->has($key) ? '1' : '0';
                    
                    // Always save checkbox values (including 0)
                    $existing = Database::selectOne("SELECT id FROM settings WHERE `key` = ?", [$key]);
                    if ($existing) {
                        Database::update('settings', ['value' => $value], '`key` = ?', [$key]);
                    } else {
                        Database::insert('settings', ['key' => $key, 'value' => $value]);
                    }
                    continue;
                }

                if ($value !== null && $value !== '') {
                    $existing = Database::selectOne("SELECT id FROM settings WHERE `key` = ?", [$key]);
                    
                    if ($existing) {
                        Database::update('settings', ['value' => $value], '`key` = ?', [$key]);
                    } else {
                        Database::insert('settings', ['key' => $key, 'value' => $value]);
                    }
                }
            }

            $this->logAudit('settings_updated', 'system', null);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/admin/settings'), 'success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to update settings: ' . $e->getMessage()], 500);
            }

            $this->redirectWith(url('/admin/settings'), 'error', 'Failed to update settings.');
        }
    }

    public function testEmail(Request $request): void
    {
        try {
            $smtpHost = Database::selectValue("SELECT value FROM settings WHERE `key` = 'smtp_host'");
            $smtpPort = Database::selectValue("SELECT value FROM settings WHERE `key` = 'smtp_port'") ?? 587;
            $smtpUsername = Database::selectValue("SELECT value FROM settings WHERE `key` = 'smtp_username'");
            $smtpPassword = Database::selectValue("SELECT value FROM settings WHERE `key` = 'smtp_password'");
            $smtpEncryption = Database::selectValue("SELECT value FROM settings WHERE `key` = 'smtp_encryption'") ?? 'tls';
            $fromAddress = Database::selectValue("SELECT value FROM settings WHERE `key` = 'mail_from_address'");
            $fromName = Database::selectValue("SELECT value FROM settings WHERE `key` = 'mail_from_name'") ?? 'Jira Clone';
            $mailDriver = Database::selectValue("SELECT value FROM settings WHERE `key` = 'mail_driver'") ?? 'smtp';

            if ($mailDriver === 'log') {
                $logFile = storage_path('logs/email.log');
                $logMessage = sprintf(
                    "[%s] Test email to: %s from: %s <%s>\n",
                    date('Y-m-d H:i:s'),
                    $this->user()['email'],
                    $fromName,
                    $fromAddress ?: 'noreply@example.com'
                );
                file_put_contents($logFile, $logMessage, FILE_APPEND);
                
                $this->json(['success' => true, 'message' => 'Test email logged to storage/logs/email.log']);
                return;
            }

            if (!$smtpHost) {
                $this->json(['error' => 'SMTP host is not configured'], 400);
                return;
            }

            $user = $this->user();
            $to = $user['email'];
            $subject = 'Test Email from Jira Clone';
            $message = "This is a test email from your Jira Clone installation.\n\n";
            $message .= "If you received this email, your email settings are configured correctly.\n\n";
            $message .= "Sent at: " . date('Y-m-d H:i:s');

            $headers = [
                'From' => $fromName . ' <' . ($fromAddress ?: 'noreply@example.com') . '>',
                'Reply-To' => $fromAddress ?: 'noreply@example.com',
                'X-Mailer' => 'PHP/' . phpversion(),
                'Content-Type' => 'text/plain; charset=UTF-8',
            ];

            $headerString = '';
            foreach ($headers as $key => $value) {
                $headerString .= "$key: $value\r\n";
            }

            if ($mailDriver === 'sendmail' || empty($smtpHost)) {
                $result = mail($to, $subject, $message, $headerString);
            } else {
                $result = $this->sendSmtpEmail(
                    $smtpHost,
                    (int) $smtpPort,
                    $smtpUsername,
                    $smtpPassword,
                    $smtpEncryption,
                    $fromAddress ?: 'noreply@example.com',
                    $fromName,
                    $to,
                    $subject,
                    $message
                );
            }

            if ($result) {
                $this->logAudit('test_email_sent', 'system', null);
                $this->json(['success' => true, 'message' => 'Test email sent successfully to ' . $to]);
            } else {
                $this->json(['error' => 'Failed to send test email. Check your SMTP settings.'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to send test email: ' . $e->getMessage()], 500);
        }
    }

    private function sendSmtpEmail(
        string $host,
        int $port,
        ?string $username,
        ?string $password,
        string $encryption,
        string $from,
        string $fromName,
        string $to,
        string $subject,
        string $body
    ): bool {
        $socket = null;
        
        try {
            $protocol = $encryption === 'ssl' ? 'ssl://' : '';
            $socket = @fsockopen($protocol . $host, $port, $errno, $errstr, 30);
            
            if (!$socket) {
                throw new \Exception("Could not connect to SMTP server: $errstr ($errno)");
            }

            $this->smtpRead($socket);
            $this->smtpSend($socket, "EHLO " . gethostname());
            
            if ($encryption === 'tls' && $port !== 465) {
                $this->smtpSend($socket, "STARTTLS");
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->smtpSend($socket, "EHLO " . gethostname());
            }

            if ($username && $password) {
                $this->smtpSend($socket, "AUTH LOGIN");
                $this->smtpSend($socket, base64_encode($username));
                $this->smtpSend($socket, base64_encode($password));
            }

            $this->smtpSend($socket, "MAIL FROM:<$from>");
            $this->smtpSend($socket, "RCPT TO:<$to>");
            $this->smtpSend($socket, "DATA");

            $email = "From: $fromName <$from>\r\n";
            $email .= "To: $to\r\n";
            $email .= "Subject: $subject\r\n";
            $email .= "MIME-Version: 1.0\r\n";
            $email .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $email .= "\r\n";
            $email .= $body;
            $email .= "\r\n.";

            $this->smtpSend($socket, $email);
            $this->smtpSend($socket, "QUIT");

            fclose($socket);
            return true;
        } catch (\Exception $e) {
            if ($socket) {
                fclose($socket);
            }
            throw $e;
        }
    }

    private function smtpSend($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->smtpRead($socket);
    }

    private function smtpRead($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        
        $code = (int) substr($response, 0, 3);
        if ($code >= 400) {
            throw new \Exception("SMTP Error: $response");
        }
        
        return $response;
    }

    private function logAudit(
        string $action,
        string $entityType,
        ?int $entityId,
        ?string $oldValue = null,
        ?string $newValue = null
    ): void {
        Database::insert('audit_logs', [
            'user_id' => $this->userId(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValue ? json_encode(['value' => $oldValue]) : null,
            'new_values' => $newValue ? json_encode(['value' => $newValue]) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    }
}
