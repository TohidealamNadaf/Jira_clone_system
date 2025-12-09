# Notifications System - Technical Specification

**Status**: Ready for Implementation | **Priority**: HIGH | **Updated**: December 2025

---

## Overview

The Notifications System will provide real-time awareness of issue activities across the platform. It will support multiple notification channels (in-app, email, push) with user-configurable preferences.

---

## Architecture

### Components

```
┌─────────────────────────────────────────────────────────┐
│         NOTIFICATION EVENTS (Triggers)                   │
│  Issue created, assigned, commented, status changed...   │
└────────────────┬────────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────────┐
│    NotificationDispatcher (Service Layer)                │
│  - Determines who should be notified                     │
│  - Creates notification records                          │
│  - Queues email/push deliveries                          │
└────────────────┬────────────────────────────────────────┘
                 │
        ┌────────┴─────────────┬──────────────┐
        │                      │              │
┌───────▼───────┐    ┌─────────▼──────┐    ┌──▼──────────────┐
│  In-App Queue │    │  Email Queue   │    │  Push Queue     │
│  (Database)   │    │  (Database)    │    │  (Database)     │
└───────┬───────┘    └─────────┬──────┘    └──┬──────────────┘
        │                      │               │
        │              (Cron Job Handler)      │
        │                      │               │
┌───────▼─────────────────────▼───────────────▼──────┐
│    Notification Center (UI)                        │
│    - Display in-app notifications                  │
│    - Mark as read/unread                           │
│    - View notification history                     │
└────────────────────────────────────────────────────┘
```

---

## Database Schema

### 1. `notifications` Table

```sql
CREATE TABLE `notifications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('issue_created', 'issue_assigned', 'issue_commented', 
                'issue_status_changed', 'issue_mentioned', 'issue_watched',
                'project_created', 'project_member_added', 'comment_reply',
                'custom') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT,
    `action_url` VARCHAR(500),
    `actor_user_id` INT UNSIGNED,
    `related_issue_id` INT UNSIGNED,
    `related_project_id` INT UNSIGNED,
    `priority` ENUM('high', 'normal', 'low') DEFAULT 'normal',
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `notifications_user_id_idx` (`user_id`, `is_read`, `created_at`),
    KEY `notifications_actor_user_id_idx` (`actor_user_id`),
    KEY `notifications_issue_id_idx` (`related_issue_id`),
    KEY `notifications_created_at_idx` (`created_at`),
    CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `notifications_actor_user_id_fk` FOREIGN KEY (`actor_user_id`) 
        REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `notifications_issue_id_fk` FOREIGN KEY (`related_issue_id`) 
        REFERENCES `issues` (`id`) ON DELETE SET NULL,
    CONSTRAINT `notifications_project_id_fk` FOREIGN KEY (`related_project_id`) 
        REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. `notification_preferences` Table

```sql
CREATE TABLE `notification_preferences` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `event_type` ENUM('issue_created', 'issue_assigned', 'issue_commented',
                      'issue_status_changed', 'issue_mentioned', 'issue_watched',
                      'project_created', 'project_member_added', 'comment_reply',
                      'all') NOT NULL,
    `in_app` TINYINT(1) DEFAULT 1,
    `email` TINYINT(1) DEFAULT 1,
    `push` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `notification_preferences_user_event_unique` (`user_id`, `event_type`),
    CONSTRAINT `notification_preferences_user_id_fk` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. `notification_deliveries` Table (Optional - for tracking)

```sql
CREATE TABLE `notification_deliveries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `notification_id` INT UNSIGNED NOT NULL,
    `channel` ENUM('in_app', 'email', 'push') NOT NULL,
    `status` ENUM('pending', 'sent', 'failed', 'bounced') DEFAULT 'pending',
    `sent_at` TIMESTAMP NULL DEFAULT NULL,
    `error_message` TEXT,
    `retry_count` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `notification_deliveries_notification_id_idx` (`notification_id`),
    KEY `notification_deliveries_status_idx` (`status`),
    CONSTRAINT `notification_deliveries_notification_id_fk` FOREIGN KEY (`notification_id`) 
        REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4. Migration SQL

```sql
-- Add column to track unread count (optional, for performance)
ALTER TABLE `users` ADD COLUMN `unread_notifications_count` INT UNSIGNED DEFAULT 0;

-- Index for fast unread count
ALTER TABLE `notifications` ADD INDEX `idx_user_unread` (`user_id`, `is_read`);
```

---

## Implementation Files

### Service Layer: `src/Services/NotificationService.php`

```php
<?php declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Cache;

class NotificationService
{
    /**
     * Dispatch notifications for various events
     */
    public static function dispatchIssueCreated(int $issueId, int $userId): void
    {
        // Get issue details
        $issue = Database::selectOne(
            'SELECT id, key, project_id, title FROM issues WHERE id = ?',
            [$issueId]
        );
        
        if (!$issue) return;
        
        // Get all project members
        $members = Database::select(
            'SELECT DISTINCT user_id FROM project_members WHERE project_id = ? AND user_id != ?',
            [$issue['project_id'], $userId]
        );
        
        foreach ($members as $member) {
            // Check preferences
            if (!self::shouldNotify($member['user_id'], 'issue_created')) {
                continue;
            }
            
            self::create(
                user_id: $member['user_id'],
                type: 'issue_created',
                title: 'Issue Created',
                message: "Issue {$issue['key']}: {$issue['title']}",
                action_url: "/issues/{$issue['key']}",
                actor_user_id: $userId,
                related_issue_id: $issueId,
                related_project_id: $issue['project_id'],
                priority: 'normal'
            );
        }
    }
    
    /**
     * Dispatch notification when user is assigned to issue
     */
    public static function dispatchIssueAssigned(
        int $issueId,
        int $assigneeId,
        int $previousAssigneeId = null
    ): void {
        $issue = Database::selectOne(
            'SELECT id, key, title FROM issues WHERE id = ?',
            [$issueId]
        );
        
        if (!$issue) return;
        
        if (self::shouldNotify($assigneeId, 'issue_assigned')) {
            self::create(
                user_id: $assigneeId,
                type: 'issue_assigned',
                title: 'Issue Assigned to You',
                message: "Issue {$issue['key']}: {$issue['title']}",
                action_url: "/issues/{$issue['key']}",
                related_issue_id: $issueId,
                priority: 'high'
            );
        }
    }
    
    /**
     * Dispatch notification when issue is commented on
     */
    public static function dispatchIssueCommented(
        int $issueId,
        int $commenterId,
        int $commentId
    ): void {
        $issue = Database::selectOne(
            'SELECT id, key, title, assignee_id FROM issues WHERE id = ?',
            [$issueId]
        );
        
        if (!$issue) return;
        
        // Notify assignee
        if ($issue['assignee_id'] && $issue['assignee_id'] !== $commenterId) {
            if (self::shouldNotify($issue['assignee_id'], 'issue_commented')) {
                self::create(
                    user_id: $issue['assignee_id'],
                    type: 'issue_commented',
                    title: 'New Comment on Your Issue',
                    message: "New comment on {$issue['key']}",
                    action_url: "/issues/{$issue['key']}#comment-{$commentId}",
                    actor_user_id: $commenterId,
                    related_issue_id: $issueId,
                    priority: 'normal'
                );
            }
        }
    }
    
    /**
     * Create a notification record
     */
    public static function create(
        int $userId,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionUrl = null,
        ?int $actorUserId = null,
        ?int $relatedIssueId = null,
        ?int $relatedProjectId = null,
        string $priority = 'normal'
    ): int {
        $id = Database::insert('notifications', [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'actor_user_id' => $actorUserId,
            'related_issue_id' => $relatedIssueId,
            'related_project_id' => $relatedProjectId,
            'priority' => $priority,
            'is_read' => 0,
        ]);
        
        // Update unread count cache
        Cache::increment("user:{$userId}:unread_notifications");
        
        return $id;
    }
    
    /**
     * Get unread notifications for user
     */
    public static function getUnread(int $userId, int $limit = 20): array
    {
        return Database::select(
            'SELECT * FROM notifications 
             WHERE user_id = ? AND is_read = 0 
             ORDER BY created_at DESC 
             LIMIT ?',
            [$userId, $limit]
        );
    }
    
    /**
     * Get all notifications with pagination
     */
    public static function getAll(int $userId, int $page = 1, int $perPage = 25): array
    {
        $offset = ($page - 1) * $perPage;
        
        return Database::select(
            'SELECT * FROM notifications 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ? OFFSET ?',
            [$userId, $perPage, $offset]
        );
    }
    
    /**
     * Mark notification as read
     */
    public static function markAsRead(int $notificationId, int $userId): bool
    {
        $result = Database::update(
            'notifications',
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            'id = ? AND user_id = ?',
            [$notificationId, $userId]
        );
        
        if ($result) {
            Cache::decrement("user:{$userId}:unread_notifications");
        }
        
        return (bool) $result;
    }
    
    /**
     * Mark all notifications as read
     */
    public static function markAllAsRead(int $userId): bool
    {
        $result = Database::update(
            'notifications',
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            'user_id = ? AND is_read = 0',
            [$userId]
        );
        
        Cache::delete("user:{$userId}:unread_notifications");
        
        return (bool) $result;
    }
    
    /**
     * Check if user has notification preference enabled
     */
    public static function shouldNotify(int $userId, string $eventType): bool
    {
        $preference = Database::selectOne(
            'SELECT in_app FROM notification_preferences WHERE user_id = ? AND event_type = ?',
            [$userId, $eventType]
        );
        
        // Default to enabled if no preference set
        return $preference ? (bool) $preference['in_app'] : true;
    }
    
    /**
     * Get user notification preferences
     */
    public static function getPreferences(int $userId): array
    {
        return Database::select(
            'SELECT * FROM notification_preferences WHERE user_id = ?',
            [$userId]
        );
    }
    
    /**
     * Update notification preference
     */
    public static function updatePreference(
        int $userId,
        string $eventType,
        bool $inApp = true,
        bool $email = true,
        bool $push = false
    ): bool {
        return (bool) Database::insertOrUpdate(
            'notification_preferences',
            [
                'user_id' => $userId,
                'event_type' => $eventType,
                'in_app' => (int) $inApp,
                'email' => (int) $email,
                'push' => (int) $push,
            ],
            ['user_id', 'event_type']
        );
    }
    
    /**
     * Delete notification
     */
    public static function delete(int $notificationId, int $userId): bool
    {
        return (bool) Database::delete(
            'notifications',
            'id = ? AND user_id = ?',
            [$notificationId, $userId]
        );
    }
    
    /**
     * Get unread count for user
     */
    public static function getUnreadCount(int $userId): int
    {
        $cached = Cache::get("user:{$userId}:unread_notifications");
        if ($cached !== null) {
            return (int) $cached;
        }
        
        $result = Database::selectOne(
            'SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0',
            [$userId]
        );
        
        Cache::set("user:{$userId}:unread_notifications", $result['count'], 3600);
        
        return $result['count'];
    }
}
```

### Controller: `src/Controllers/NotificationController.php`

```php
<?php declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    /**
     * GET /notifications - List all notifications
     */
    public function index(Request $request): string
    {
        $user = $request->user();
        if (!$user) {
            return $this->redirect('/login');
        }
        
        $page = (int) $request->query('page', 1);
        $notifications = NotificationService::getAll($user['id'], $page);
        $unreadCount = NotificationService::getUnreadCount($user['id']);
        
        return $this->view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'page' => $page,
        ]);
    }
    
    /**
     * GET /api/v1/notifications - Get notifications (API)
     */
    public function apiIndex(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $limit = (int) $request->query('limit', 20);
        $limit = min($limit, 100); // Max 100
        
        $notifications = NotificationService::getUnread($user['id'], $limit);
        
        $this->json([
            'data' => $notifications,
            'count' => count($notifications),
            'unread_count' => NotificationService::getUnreadCount($user['id']),
        ]);
    }
    
    /**
     * PATCH /api/v1/notifications/{id}/read - Mark as read
     */
    public function markAsRead(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $notificationId = (int) $request->param('id');
        
        if (NotificationService::markAsRead($notificationId, $user['id'])) {
            $this->json(['status' => 'success']);
        } else {
            $this->json(['error' => 'Not found'], 404);
        }
    }
    
    /**
     * PATCH /api/v1/notifications/read-all - Mark all as read
     */
    public function markAllAsRead(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        NotificationService::markAllAsRead($user['id']);
        $this->json(['status' => 'success']);
    }
    
    /**
     * DELETE /api/v1/notifications/{id} - Delete notification
     */
    public function delete(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $notificationId = (int) $request->param('id');
        
        if (NotificationService::delete($notificationId, $user['id'])) {
            $this->json(['status' => 'success']);
        } else {
            $this->json(['error' => 'Not found'], 404);
        }
    }
    
    /**
     * GET /api/v1/notifications/preferences - Get preferences
     */
    public function getPreferences(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $preferences = NotificationService::getPreferences($user['id']);
        $this->json(['data' => $preferences]);
    }
    
    /**
     * POST /api/v1/notifications/preferences - Update preferences
     */
    public function updatePreferences(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $eventType = $request->input('event_type');
        $inApp = (bool) $request->input('in_app', true);
        $email = (bool) $request->input('email', true);
        $push = (bool) $request->input('push', false);
        
        if (!$eventType) {
            $this->json(['error' => 'Missing event_type'], 400);
            return;
        }
        
        NotificationService::updatePreference($user['id'], $eventType, $inApp, $email, $push);
        $this->json(['status' => 'success']);
    }
}
```

### Routes: Add to `routes/web.php` and `routes/api.php`

```php
// Web routes (routes/web.php)
$router->get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

// API routes (routes/api.php)
$router->get('/notifications', [NotificationController::class, 'apiIndex'])->name('api.notifications.index');
$router->patch('/notifications/:id/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-read');
$router->patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-read');
$router->delete('/notifications/:id', [NotificationController::class, 'delete'])->name('api.notifications.delete');
$router->get('/notifications/preferences', [NotificationController::class, 'getPreferences'])->name('api.notifications.preferences');
$router->post('/notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('api.notifications.update-preferences');
```

---

## Integration Points

### 1. In IssueController

When creating an issue:
```php
$issueId = $issueService->create($data);
NotificationService::dispatchIssueCreated($issueId, $request->user()['id']);
```

When assigning an issue:
```php
$issueService->assign($issueId, $assigneeId);
NotificationService::dispatchIssueAssigned($issueId, $assigneeId, $previousAssigneeId);
```

### 2. In CommentController

```php
$comment = $commentService->create($issueId, $data);
NotificationService::dispatchIssueCommented($issueId, $userId, $comment['id']);
```

### 3. In Status Changes

```php
$issueService->transition($issueId, $newStatus);
NotificationService::dispatchIssueStatusChanged($issueId, $newStatus, $userId);
```

---

## UI Components

### Navbar Bell Icon

Add to `views/layouts/app.php`:

```php
<div class="notification-bell">
    <button type="button" class="btn btn-link" id="notificationBell" data-bs-toggle="dropdown">
        <i class="bi bi-bell"></i>
        <span class="badge badge-notification" id="unreadCount" style="display:none;">0</span>
    </button>
    
    <ul class="dropdown-menu dropdown-menu-end" style="max-width: 400px; max-height: 500px; overflow-y: auto;">
        <li><h6 class="dropdown-header">Notifications</h6></li>
        <li><hr class="dropdown-divider"></li>
        <div id="notificationList"></div>
        <li><hr class="dropdown-divider"></li>
        <li><a href="/notifications" class="dropdown-item text-center">View All</a></li>
    </ul>
</div>

<style>
.badge-notification {
    position: absolute;
    top: 5px;
    right: 0;
    background: #dc3545;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
}
</style>

<script>
// Load notifications
function loadNotifications() {
    fetch('/api/v1/notifications?limit=5')
        .then(r => r.json())
        .then(data => {
            const count = document.getElementById('unreadCount');
            const list = document.getElementById('notificationList');
            
            if (data.unread_count > 0) {
                count.textContent = data.unread_count;
                count.style.display = 'inline-block';
            } else {
                count.style.display = 'none';
            }
            
            if (data.data.length === 0) {
                list.innerHTML = '<li class="text-center text-muted p-3">No notifications</li>';
                return;
            }
            
            list.innerHTML = data.data.map(n => `
                <li>
                    <a href="${n.action_url}" class="dropdown-item">
                        <strong>${n.title}</strong>
                        <small class="d-block text-muted">${n.message}</small>
                    </a>
                </li>
            `).join('');
        });
}

document.getElementById('notificationBell').addEventListener('click', loadNotifications);

// Refresh every 30 seconds
setInterval(loadNotifications, 30000);
</script>
```

---

## Testing Checklist

- [ ] Create notifications when issue is created
- [ ] Create notifications when user is assigned
- [ ] Create notifications when someone comments
- [ ] Create notifications on status change
- [ ] Mark notification as read
- [ ] Mark all as read
- [ ] Delete notification
- [ ] Notification preferences update
- [ ] Unread count displays correctly
- [ ] Notification bell shows unread count
- [ ] API endpoints return correct data
- [ ] Permissions enforced (can't see other's notifications)
- [ ] Performance: unread count lookup is cached

---

## Next Steps

1. Create database tables (migrations)
2. Implement NotificationService
3. Implement NotificationController
4. Add API routes
5. Integrate with IssueController
6. Add UI components
7. Test thoroughly
8. Add email notifications (phase 2)
9. Add push notifications (phase 2)

Would you like me to proceed with implementation?
