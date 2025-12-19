<?php
/**
 * Authentication Service
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Session;
use App\Core\Cache;
use App\Core\JWT;

class AuthService
{
    /**
     * Attempt to authenticate a user
     */
    public function attempt(string $email, string $password, bool $remember = false): bool
    {
        // Check rate limiting
        if ($this->isLockedOut($email)) {
            return false;
        }

        $user = Database::selectOne(
            "SELECT * FROM users WHERE email = ? AND is_active = 1",
            [$email]
        );

        if (!$user) {
            $this->incrementFailedAttempts($email);
            return false;
        }

        // Check if account is locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return false;
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            $this->incrementFailedAttempts($email);
            $this->recordFailedLogin($user['id']);
            return false;
        }

        // Login successful
        $this->login($user);
        $this->clearFailedAttempts($email);

        return true;
    }

    /**
     * Log in a user
     */
    public function login(array $user): void
    {
        // Remove sensitive data
        unset($user['password_hash']);

        // Get user roles and permissions
        $user['roles'] = $this->getUserRoles($user['id']);
        $user['permissions'] = $this->getUserPermissions($user['id']);

        // Store in session
        Session::setUser($user);

        // Update last login
        Database::update('users', [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => client_ip(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ], 'id = ?', [$user['id']]);

        // Create session record
        $this->createSessionRecord($user['id']);

        // Log audit
        $this->logAudit('login', 'user', $user['id'], null, ['ip' => client_ip()]);
    }

    /**
     * Log out the current user
     */
    public function logout(): void
    {
        $user = Session::user();
        
        if ($user) {
            // Remove session record
            $this->removeSessionRecord();
            
            // Log audit
            $this->logAudit('logout', 'user', $user['id']);
        }

        Session::logout();
    }

    /**
     * Get current authenticated user
     */
    public function user(): ?array
    {
        return Session::user();
    }

    /**
     * Check if user has a permission
     */
    public function can(string $permission, ?int $projectId = null): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        // Admins have all permissions
        if ($user['is_admin'] ?? false) {
            return true;
        }

        // Check global permissions
        if (in_array($permission, $user['permissions']['global'] ?? [])) {
            return true;
        }

        // Check project-specific permissions
        if ($projectId !== null && isset($user['permissions']['projects'][$projectId])) {
            if (in_array($permission, $user['permissions']['projects'][$projectId])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function canAny(array $permissions, ?int $projectId = null): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($permission, $projectId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has role
     */
    public function hasRole(string $roleSlug, ?int $projectId = null): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        if ($projectId === null) {
            return in_array($roleSlug, $user['roles']['global'] ?? []);
        }

        return in_array($roleSlug, $user['roles']['projects'][$projectId] ?? []);
    }

    /**
     * Get user roles
     */
    private function getUserRoles(int $userId): array
    {
        $roles = Database::select(
            "SELECT r.slug, ur.project_id 
             FROM user_roles ur
             JOIN roles r ON ur.role_id = r.id
             WHERE ur.user_id = ?",
            [$userId]
        );

        $result = ['global' => [], 'projects' => []];
        
        foreach ($roles as $role) {
            if ($role['project_id'] === null) {
                $result['global'][] = $role['slug'];
            } else {
                $result['projects'][$role['project_id']][] = $role['slug'];
            }
        }

        return $result;
    }

    /**
     * Get user permissions
     */
    private function getUserPermissions(int $userId): array
    {
        // Get global permissions
        $globalPerms = Database::select(
            "SELECT DISTINCT p.slug
             FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             JOIN user_roles ur ON rp.role_id = ur.role_id
             WHERE ur.user_id = ? AND ur.project_id IS NULL",
            [$userId]
        );

        // Get project permissions
        $projectPerms = Database::select(
            "SELECT DISTINCT p.slug, ur.project_id
             FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             JOIN user_roles ur ON rp.role_id = ur.role_id
             WHERE ur.user_id = ? AND ur.project_id IS NOT NULL",
            [$userId]
        );

        // Also get permissions from project_members table
        $memberPerms = Database::select(
            "SELECT DISTINCT p.slug, pm.project_id
             FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             JOIN project_members pm ON rp.role_id = pm.role_id
             WHERE pm.user_id = ?",
            [$userId]
        );

        $result = [
            'global' => array_column($globalPerms, 'slug'),
            'projects' => [],
        ];

        foreach ($projectPerms as $perm) {
            $result['projects'][$perm['project_id']][] = $perm['slug'];
        }

        foreach ($memberPerms as $perm) {
            if (!isset($result['projects'][$perm['project_id']])) {
                $result['projects'][$perm['project_id']] = [];
            }
            if (!in_array($perm['slug'], $result['projects'][$perm['project_id']])) {
                $result['projects'][$perm['project_id']][] = $perm['slug'];
            }
        }

        return $result;
    }

    /**
     * Check if email is locked out
     */
    private function isLockedOut(string $email): bool
    {
        $cache = new Cache();
        $attempts = (int) $cache->get("login_attempts_$email", 0);
        return $attempts >= config('security.max_login_attempts', 5);
    }

    /**
     * Increment failed login attempts
     */
    private function incrementFailedAttempts(string $email): void
    {
        $cache = new Cache();
        $key = "login_attempts_$email";
        $attempts = (int) $cache->get($key, 0) + 1;
        $cache->put($key, $attempts, config('security.lockout_duration', 900));
    }

    /**
     * Clear failed login attempts
     */
    private function clearFailedAttempts(string $email): void
    {
        $cache = new Cache();
        $cache->forget("login_attempts_$email");
    }

    /**
     * Record failed login in database
     */
    private function recordFailedLogin(int $userId): void
    {
        $user = Database::selectOne("SELECT failed_login_attempts FROM users WHERE id = ?", [$userId]);
        $attempts = ($user['failed_login_attempts'] ?? 0) + 1;
        $maxAttempts = config('security.max_login_attempts', 5);

        $data = ['failed_login_attempts' => $attempts];
        
        if ($attempts >= $maxAttempts) {
            $data['locked_until'] = date('Y-m-d H:i:s', time() + config('security.lockout_duration', 900));
        }

        Database::update('users', $data, 'id = ?', [$userId]);
    }

    /**
     * Create session record
     */
    private function createSessionRecord(int $userId): void
    {
        Database::insert('user_sessions', [
            'id' => Session::id(),
            'user_id' => $userId,
            'ip_address' => client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    }

    /**
     * Remove session record
     */
    private function removeSessionRecord(): void
    {
        Database::delete('user_sessions', 'id = ?', [Session::id()]);
    }

    /**
     * Generate password reset token
     */
    public function createPasswordResetToken(string $email): ?string
    {
        $user = Database::selectOne("SELECT id FROM users WHERE email = ? AND is_active = 1", [$email]);
        
        if (!$user) {
            return null;
        }

        // Delete existing tokens
        Database::delete('password_resets', 'user_id = ?', [$user['id']]);

        // Create new token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

        Database::insert('password_resets', [
            'user_id' => $user['id'],
            'token' => hash('sha256', $token),
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $password): bool
    {
        $reset = Database::selectOne(
            "SELECT * FROM password_resets 
             WHERE token = ? AND expires_at > NOW() AND used_at IS NULL",
            [hash('sha256', $token)]
        );

        if (!$reset) {
            return false;
        }

        // Update password
        Database::update('users', [
            'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
        ], 'id = ?', [$reset['user_id']]);

        // Mark token as used
        Database::update('password_resets', [
            'used_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$reset['id']]);

        // Log audit
        $this->logAudit('password_reset', 'user', $reset['user_id']);

        return true;
    }

    /**
     * Create JWT token for API
     */
    public function createApiToken(int $userId): string
    {
        return JWT::encode([
            'sub' => $userId,
            'type' => 'access',
        ]);
    }

    /**
     * Log audit entry
     */
    private function logAudit(string $action, string $entityType, ?int $entityId, ?array $oldValues = null, ?array $newValues = null): void
    {
        Database::insert('audit_logs', [
            'user_id' => $entityId,
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
