<?php
/**
 * User Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Services\NotificationService;

class UserController extends Controller
{
    public function profile(Request $request): string
    {
        $user = $this->user();
        $userId = $this->userId();

        // Get activity stats
        $stats = [
            'issues_assigned' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues WHERE assignee_id = ?",
                [$userId]
            ),
            'issues_completed' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM issues i 
                 JOIN statuses s ON i.status_id = s.id 
                 WHERE i.assignee_id = ? AND s.category = 'done'",
                [$userId]
            ),
            'comments_made' => (int) Database::selectValue(
                "SELECT COUNT(*) FROM comments WHERE user_id = ?",
                [$userId]
            ),
            'projects_count' => (int) Database::selectValue(
                "SELECT COUNT(DISTINCT pm.project_id) FROM project_members pm WHERE pm.user_id = ?",
                [$userId]
            ),
        ];

        $timezones = timezone_identifiers_list();

        if ($request->wantsJson()) {
            $this->json($user);
        }

        return $this->view('profile.index', [
            'user' => $user,
            'stats' => $stats,
            'timezones' => $timezones,
        ]);
    }

    public function updateProfile(Request $request): void
    {
        $data = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:255',
            'timezone' => 'nullable|max:50',
            'locale' => 'nullable|max:10',
        ]);

        try {
            $existingUser = Database::selectOne(
                "SELECT id FROM users WHERE email = ? AND id != ?",
                [$data['email'], $this->userId()]
            );

            if ($existingUser) {
                throw new \InvalidArgumentException('Email is already in use.');
            }

            Database::update('users', [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'timezone' => $data['timezone'] ?? 'UTC',
                'locale' => $data['locale'] ?? 'en',
            ], 'id = ?', [$this->userId()]);

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'message' => 'Profile updated successfully.']);
            }

            $this->redirectWith(url('/profile'), 'success', 'Profile updated successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            Session::flash('_old_input', $data);
            $this->redirect(url('/profile'));
        }
    }

    public function updatePassword(Request $request): void
    {
        $data = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        try {
            $user = Database::selectOne(
                "SELECT password_hash FROM users WHERE id = ?",
                [$this->userId()]
            );

            if (!password_verify($data['current_password'], $user['password_hash'])) {
                throw new \InvalidArgumentException('Current password is incorrect.');
            }

            Database::update('users', [
                'password_hash' => password_hash($data['new_password'], PASSWORD_ARGON2ID),
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$this->userId()]);

            if ($request->wantsJson()) {
                $this->json(['success' => true, 'message' => 'Password updated successfully.']);
            }

            $this->redirectWith(url('/profile'), 'success', 'Password updated successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            }

            Session::flash('error', $e->getMessage());
            $this->redirect(url('/profile'));
        }
    }

    public function updateAvatar(Request $request): void
    {
        try {
            if (!$request->hasFile('avatar')) {
                throw new \InvalidArgumentException('No file was uploaded.');
            }

            $file = $request->file('avatar');
            
            // Validate file
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($file['type'], $allowedMimes)) {
                throw new \InvalidArgumentException('Only JPEG, PNG, and GIF images are allowed.');
            }
            
            if ($file['size'] > $maxSize) {
                throw new \InvalidArgumentException('File size must not exceed 2MB.');
            }
            
            // Create unique filename
            $filename = 'avatar_' . $this->userId() . '_' . time() . '.png';
            $uploadDir = BASE_PATH . '/public/uploads/avatars/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $filepath = $uploadDir . $filename;
            
            // Move the cropped image (already processed by frontend)
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new \Exception('Failed to upload file.');
            }
            
            // Ensure proper permissions
            chmod($filepath, 0644);
            
            // Update user avatar with full URL
            $avatarUrl = url('/uploads/avatars/' . $filename);
            Database::update('users', [
                'avatar' => $avatarUrl,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$this->userId()]);
            
            // Verify the update worked
            $updatedUser = Database::selectOne('SELECT * FROM users WHERE id = ?', [$this->userId()]);
            
            if (!$updatedUser || !$updatedUser['avatar']) {
                throw new \Exception('Avatar was not saved to database. Value: ' . ($updatedUser['avatar'] ?? 'NULL'));
            }
            
            // Refresh user data in session
            Session::set('_user', $updatedUser);
            
            // Always return JSON for AJAX request
            $this->json([
                'success' => true, 
                'avatar_url' => $avatarUrl,
                'db_avatar' => $updatedUser['avatar'],
                'message' => 'Avatar updated successfully.'
            ]);
        } catch (\Exception $e) {
            // Return error as JSON
            $this->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function tokens(Request $request): string
    {
        $tokens = Database::select(
            "SELECT id, name, last_used_at, created_at, expires_at 
             FROM personal_access_tokens 
             WHERE user_id = ? 
             ORDER BY created_at DESC",
            [$this->userId()]
        );

        if ($request->wantsJson()) {
            $this->json($tokens);
        }

        return $this->view('profile.tokens', [
            'tokens' => $tokens,
        ]);
    }

    public function createToken(Request $request): void
    {
        $data = $request->validate([
            'name' => 'required|max:100',
        ]);

        try {
            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);

            Database::insert('personal_access_tokens', [
                'user_id' => $this->userId(),
                'name' => $data['name'],
                'token_hash' => $hashedToken,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($request->wantsJson()) {
                $this->json([
                    'success' => true,
                    'token' => $token,
                    'message' => 'Token created. Please copy it now as it will not be shown again.',
                ]);
            }

            Session::flash('success', 'Token created successfully.');
            Session::flash('new_token', $token);
            $this->redirect(url('/profile/tokens'));
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to create token.'], 500);
            }

            $this->redirectWith(url('/profile/tokens'), 'error', 'Failed to create token.');
        }
    }

    public function revokeToken(Request $request): void
    {
        $tokenId = (int) $request->param('id');

        try {
            $token = Database::selectOne(
                "SELECT id FROM personal_access_tokens WHERE id = ? AND user_id = ?",
                [$tokenId, $this->userId()]
            );

            if (!$token) {
                abort(404, 'Token not found');
            }

            Database::delete('personal_access_tokens', 'id = ?', [$tokenId]);

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/profile/tokens'), 'success', 'Token revoked successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to revoke token.'], 500);
            }

            $this->redirectWith(url('/profile/tokens'), 'error', 'Failed to revoke token.');
        }
    }

    public function notifications(Request $request): string
    {
        $page = (int) ($request->input('page') ?? 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $notifications = Database::select(
            "SELECT * FROM notifications
             WHERE user_id = ?
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?",
            [$this->userId(), $perPage, $offset]
        );

        // Parse JSON data for each notification
        foreach ($notifications as &$notification) {
            if (!empty($notification['data'])) {
                $notification['data'] = json_decode($notification['data'], true) ?? [];
            } else {
                $notification['data'] = [];
            }
        }

        $unreadCount = (int) Database::selectValue(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND read_at IS NULL",
            [$this->userId()]
        );

        $total = (int) Database::selectValue(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ?",
            [$this->userId()]
        );

        if ($request->wantsJson()) {
            $this->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);
        }

        return $this->view('user.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    public function markNotificationsRead(Request $request): void
    {
        $ids = $request->input('ids');

        try {
            if ($ids && is_array($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                Database::query(
                    "UPDATE notifications SET read_at = ? WHERE id IN ($placeholders) AND user_id = ?",
                    array_merge([date('Y-m-d H:i:s')], $ids, [$this->userId()])
                );
            } else {
                Database::query(
                    "UPDATE notifications SET read_at = ? WHERE user_id = ? AND read_at IS NULL",
                    [date('Y-m-d H:i:s'), $this->userId()]
                );
            }

            if ($request->wantsJson()) {
                $this->json(['success' => true]);
            }

            $this->redirectWith(url('/notifications'), 'success', 'Notifications marked as read.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                $this->json(['error' => 'Failed to update notifications.'], 500);
            }

            $this->redirectWith(url('/notifications'), 'error', 'Failed to update notifications.');
        }
    }

    public function profileNotifications(Request $request): string
    {
        $user = $this->user();
        $userId = $this->userId();
        
        // Get user's notification preferences
        $preferencesList = NotificationService::getPreferences($userId);
        
        // Convert list to associative array keyed by event type
        $preferences = [];
        foreach ($preferencesList as $pref) {
            $preferences[$pref['event_type']] = [
                'in_app' => (bool) $pref['in_app'],
                'email' => (bool) $pref['email'],
                'push' => (bool) $pref['push'],
            ];
        }
        
        return $this->view('profile.notifications', [
            'user' => $user,
            'preferences' => $preferences,
        ]);
    }

    public function updateNotificationSettings(Request $request): void
    {
        $this->redirectWith(url('/profile/notifications'), 'success', 'Notification settings updated.');
    }

    public function security(Request $request): string
    {
        $user = $this->user();
        
        // Get recent login activity from audit logs
        $loginActivity = Database::select(
            "SELECT * FROM audit_logs 
             WHERE user_id = ? AND action LIKE '%login%'
             ORDER BY created_at DESC
             LIMIT 10",
            [$this->userId()]
        );
        
        return $this->view('profile.security', [
            'user' => $user,
            'loginActivity' => $loginActivity,
        ]);
    }

    /**
     * Get active users for dropdowns and quick create modal
     * Used by Quick Create Modal and other UI components
     */
    public function activeUsers(Request $request): void
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
}
