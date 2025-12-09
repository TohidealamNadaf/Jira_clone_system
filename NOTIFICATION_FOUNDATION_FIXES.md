# Notification Foundation - Implementation Fixes

**Status**: Action Guide | **Priority**: High | **Estimated Time**: 5 hours

---

## Quick Fix Summary

| Issue | Fix | Time | Priority |
|---|---|---|---|
| Missing `notification_preferences` table | Create table & migrate | 15 min | 🔴 Critical |
| Missing `notifications_archive` table | Create table & migrate | 15 min | 🔴 Critical |
| No user preference UI | Build profile page | 2 hrs | 🔴 Critical |
| Incomplete controller integration | Wire up notifications | 1.5 hrs | 🔴 Critical |
| Missing service methods | Add 3 new methods | 1 hr | 🟡 Important |
| Rate limiting not enforced | Add spam protection | 30 min | 🟡 Important |

---

## STEP 1: Create Missing Database Tables (30 minutes)

### Step 1.1: Create `notification_preferences` Table

**File**: `database/schema.sql`

Add this to your schema file (before closing):

```sql
-- =====================================================
-- NOTIFICATION PREFERENCES (User Settings)
-- =====================================================

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

**Then run in MySQL**:
```bash
php scripts/verify-and-seed.php
```

Or execute directly in MySQL:
```sql
USE jiira_clonee_system;

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

**Verify**:
```sql
DESCRIBE notification_preferences;
```

Expected output: 7 columns (id, user_id, event_type, in_app, email, push, created_at, updated_at)

---

### Step 1.2: Create `notifications_archive` Table

**File**: `database/schema.sql`

Add this right after the `notification_preferences` table:

```sql
-- =====================================================
-- NOTIFICATION ARCHIVE (Data Retention)
-- =====================================================

CREATE TABLE `notifications_archive` (
    `id` BIGINT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `type` VARCHAR(100) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT DEFAULT NULL,
    `action_url` VARCHAR(500) DEFAULT NULL,
    `actor_user_id` INT UNSIGNED DEFAULT NULL,
    `related_issue_id` INT UNSIGNED DEFAULT NULL,
    `related_project_id` INT UNSIGNED DEFAULT NULL,
    `priority` VARCHAR(20) DEFAULT 'normal',
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `archived_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `notifications_archive_user_id_idx` (`user_id`),
    KEY `notifications_archive_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Execute in MySQL**:
```sql
CREATE TABLE `notifications_archive` (
    `id` BIGINT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `type` VARCHAR(100) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT DEFAULT NULL,
    `action_url` VARCHAR(500) DEFAULT NULL,
    `actor_user_id` INT UNSIGNED DEFAULT NULL,
    `related_issue_id` INT UNSIGNED DEFAULT NULL,
    `related_project_id` INT UNSIGNED DEFAULT NULL,
    `priority` VARCHAR(20) DEFAULT 'normal',
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `archived_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `notifications_archive_user_id_idx` (`user_id`),
    KEY `notifications_archive_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Verify**:
```sql
DESCRIBE notifications_archive;
```

---

## STEP 2: Build User Profile Notification Settings Page (2 hours)

### Step 2.1: Create View File

**File**: `views/profile/notifications.php`

```php
<?php
declare(strict_types=1);

use App\Core\View;

View::extends('layouts.app');
View::section('content');
?>

<div class="profile-settings-container">
    <div class="settings-header">
        <h1>Notification Settings</h1>
        <p class="subtitle">Choose how you want to receive notifications</p>
    </div>

    <div class="settings-content">
        <!-- Preferences Form -->
        <form id="notificationPreferencesForm" class="preferences-form">
            <div class="form-section">
                <h3>Event Notifications</h3>
                <p class="section-description">Customize how you're notified for different events</p>

                <div class="preferences-grid">
                    <!-- Issue Created -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Issue Created</h4>
                            <p class="preference-description">New issues in your projects</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_created_in_app" class="channel-input" checked>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_created_email" class="channel-input" checked>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_created_push" class="channel-input">
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Assigned -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Issue Assigned</h4>
                            <p class="preference-description">When you're assigned an issue</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_assigned_in_app" class="channel-input" checked>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_assigned_email" class="channel-input" checked>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_assigned_push" class="channel-input">
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Commented -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Issue Commented</h4>
                            <p class="preference-description">New comments on your issues</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_commented_in_app" class="channel-input" checked>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_commented_email" class="channel-input" checked>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_commented_push" class="channel-input">
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Status Changed -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Status Changed</h4>
                            <p class="preference-description">When issue status changes</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_status_changed_in_app" class="channel-input" checked>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_status_changed_email" class="channel-input">
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_status_changed_push" class="channel-input">
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Mentioned -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Issue Mentioned</h4>
                            <p class="preference-description">When you're mentioned in an issue</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_mentioned_in_app" class="channel-input" checked>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_mentioned_email" class="channel-input" checked>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_mentioned_push" class="channel-input">
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- More event types... -->
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle"></i> Save Preferences
                </button>
                <button type="button" id="resetBtn" class="btn btn-secondary btn-lg">
                    Reset to Defaults
                </button>
            </div>

            <!-- Success/Error Messages -->
            <div id="successMessage" class="alert alert-success d-none">
                <i class="bi bi-check-circle"></i> Notification preferences updated successfully!
            </div>
            <div id="errorMessage" class="alert alert-danger d-none">
                <i class="bi bi-exclamation-circle"></i> Error updating preferences
            </div>
        </form>
    </div>
</div>

<style>
.profile-settings-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 20px;
}

.settings-header {
    margin-bottom: 40px;
}

.settings-header h1 {
    font-size: 28px;
    font-weight: 600;
    margin: 0 0 8px;
    color: #161b22;
}

.subtitle {
    margin: 0;
    color: #656d76;
    font-size: 14px;
}

.settings-content {
    background: white;
    border: 1px solid #d0d7de;
    border-radius: 12px;
    padding: 32px;
}

.form-section {
    margin-bottom: 32px;
}

.form-section h3 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px;
    color: #161b22;
}

.section-description {
    margin: 0 0 16px;
    font-size: 13px;
    color: #656d76;
}

.preferences-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.preference-card {
    border: 1px solid #d0d7de;
    border-radius: 8px;
    padding: 16px;
    background: #f6f8fa;
    transition: all 0.2s ease;
}

.preference-card:hover {
    border-color: #b6e3ff;
    background: #f0f7ff;
}

.preference-header {
    margin-bottom: 12px;
}

.preference-header h4 {
    margin: 0 0 4px;
    font-size: 14px;
    font-weight: 600;
    color: #161b22;
}

.preference-description {
    margin: 0;
    font-size: 12px;
    color: #656d76;
}

.preference-channels {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.channel-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 6px;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.channel-checkbox:hover {
    background: rgba(0, 82, 204, 0.1);
}

.channel-input {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #0052cc;
}

.channel-icon {
    font-size: 14px;
}

.channel-name {
    font-size: 13px;
    color: #161b22;
    flex: 1;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #d0d7de;
}

.btn {
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #0052cc;
    color: white;
}

.btn-primary:hover {
    background: #003d82;
}

.btn-secondary {
    background: #e1e4e8;
    color: #161b22;
}

.btn-secondary:hover {
    background: #d0d7de;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 15px;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-top: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.alert-success {
    background: #dffcf0;
    color: #216e4e;
    border: 1px solid #9ae8c8;
}

.alert-danger {
    background: #ffeceb;
    color: #ae2a19;
    border: 1px solid #ffcccc;
}

.d-none {
    display: none !important;
}

@media (max-width: 768px) {
    .preferences-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const form = document.getElementById('notificationPreferencesForm');
    const resetBtn = document.getElementById('resetBtn');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');

    // Load current preferences
    await loadPreferences();

    // Form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        await savePreferences();
    });

    // Reset button
    resetBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        if (confirm('Reset to default preferences?')) {
            await resetPreferences();
        }
    });

    async function loadPreferences() {
        try {
            const response = await fetch('/api/v1/notifications/preferences');
            const data = await response.json();

            if (response.ok && data.data) {
                // Populate form with current preferences
                data.data.forEach(pref => {
                    if (pref.in_app) {
                        document.querySelector(`[name="${pref.event_type}_in_app"]`).checked = true;
                    }
                    if (pref.email) {
                        document.querySelector(`[name="${pref.event_type}_email"]`).checked = true;
                    }
                    if (pref.push) {
                        document.querySelector(`[name="${pref.event_type}_push"]`).checked = true;
                    }
                });
            }
        } catch (error) {
            console.error('Error loading preferences:', error);
        }
    }

    async function savePreferences() {
        const eventTypes = [
            'issue_created', 'issue_assigned', 'issue_commented',
            'issue_status_changed', 'issue_mentioned', 'issue_watched'
        ];

        try {
            successMsg.classList.add('d-none');
            errorMsg.classList.add('d-none');

            for (const eventType of eventTypes) {
                const inApp = form.querySelector(`[name="${eventType}_in_app"]`).checked;
                const email = form.querySelector(`[name="${eventType}_email"]`).checked;
                const push = form.querySelector(`[name="${eventType}_push"]`).checked;

                const response = await fetch('/api/v1/notifications/preferences', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        event_type: eventType,
                        in_app: inApp,
                        email: email,
                        push: push
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to save preference');
                }
            }

            successMsg.classList.remove('d-none');
            setTimeout(() => successMsg.classList.add('d-none'), 5000);
        } catch (error) {
            console.error('Error saving preferences:', error);
            errorMsg.classList.remove('d-none');
            setTimeout(() => errorMsg.classList.add('d-none'), 5000);
        }
    }

    async function resetPreferences() {
        const eventTypes = [
            'issue_created', 'issue_assigned', 'issue_commented',
            'issue_status_changed', 'issue_mentioned', 'issue_watched'
        ];

        const defaults = {
            issue_created: { in_app: true, email: true, push: false },
            issue_assigned: { in_app: true, email: true, push: false },
            issue_commented: { in_app: true, email: true, push: false },
            issue_status_changed: { in_app: true, email: false, push: false },
            issue_mentioned: { in_app: true, email: true, push: false },
            issue_watched: { in_app: true, email: false, push: false }
        };

        try {
            successMsg.classList.add('d-none');
            errorMsg.classList.add('d-none');

            for (const eventType of eventTypes) {
                const prefs = defaults[eventType];
                const response = await fetch('/api/v1/notifications/preferences', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        event_type: eventType,
                        in_app: prefs.in_app,
                        email: prefs.email,
                        push: prefs.push
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to reset preference');
                }

                // Update UI
                form.querySelector(`[name="${eventType}_in_app"]`).checked = prefs.in_app;
                form.querySelector(`[name="${eventType}_email"]`).checked = prefs.email;
                form.querySelector(`[name="${eventType}_push"]`).checked = prefs.push;
            }

            successMsg.classList.remove('d-none');
            setTimeout(() => successMsg.classList.add('d-none'), 5000);
        } catch (error) {
            console.error('Error resetting preferences:', error);
            errorMsg.classList.remove('d-none');
            setTimeout(() => errorMsg.classList.add('d-none'), 5000);
        }
    }
});
</script>

<?php View::endSection(); ?>
```

### Step 2.2: Update UserController

**File**: `src/Controllers/UserController.php`

Find the `profileNotifications()` method and update it:

```php
/**
 * GET /profile/notifications - Notification preferences page
 */
public function profileNotifications(Request $request): string
{
    $user = $request->user();
    if (!$user) {
        return $this->redirect('/login');
    }

    $preferences = NotificationService::getPreferences($user['id']);

    return $this->view('profile.notifications', [
        'preferences' => $preferences,
    ]);
}
```

**If the method doesn't exist**, add it to the UserController class:

```php
<?php declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\NotificationService;

class UserController extends Controller
{
    // ... existing methods ...

    /**
     * GET /profile/notifications - Notification preferences page
     */
    public function profileNotifications(Request $request): string
    {
        $user = $request->user();
        if (!$user) {
            return $this->redirect('/login');
        }

        $preferences = NotificationService::getPreferences($user['id']);

        return $this->view('profile.notifications', [
            'preferences' => $preferences,
        ]);
    }

    /**
     * PUT /profile/notifications - Update notification preferences
     */
    public function updateNotificationSettings(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }

        // Handle form submission or API request
        // This is handled by NotificationController now
        $this->redirect('/profile/notifications');
    }
}
```

Add the import at the top:
```php
use App\Services\NotificationService;
```

---

## STEP 3: Complete Controller Integration (1.5 hours)

### Step 3.1: Add Comment Notification Integration

**File**: `src/Controllers/CommentController.php`

Find the `store()` method and add notification dispatch:

```php
use App\Services\NotificationService;  // Add this import

public function store(Request $request): void
{
    // ... existing code ...

    // After comment is created, dispatch notification
    $comment = CommentService::create([
        'issue_id' => $issueId,
        'user_id' => $userId,
        'body' => $request->input('body')
    ]);

    // ADD THIS LINE:
    NotificationService::dispatchIssueCommented($issueId, $userId, $comment['id']);

    // ... rest of code ...
}
```

### Step 3.2: Add Status Change Notification Integration

**File**: `src/Controllers/IssueController.php`

Find the `transition()` method and add notification dispatch:

```php
use App\Services\NotificationService;  // Add this import if not present

public function transition(Request $request): void
{
    // ... existing code ...

    $newStatus = $request->input('status');
    $issueId = (int) $request->param('issueId');
    $userId = $this->userId();

    // Update status
    IssueService::updateStatus($issueId, $newStatus);

    // ADD THIS LINE:
    NotificationService::dispatchIssueStatusChanged($issueId, $newStatus, $userId);

    // ... rest of code ...
}
```

---

## STEP 4: Add Missing Service Methods (1 hour)

### Step 4.1: Add to NotificationService.php

**File**: `src/Services/NotificationService.php`

Add these three methods to the NotificationService class:

```php
/**
 * Dispatch notification when user is mentioned in an issue
 */
public static function dispatchIssueMentioned(
    int $issueId,
    int $mentionedUserId,
    int $mentionerUserId
): void {
    $issue = Database::selectOne(
        'SELECT id, key, title, project_id FROM issues WHERE id = ?',
        [$issueId]
    );

    if (!$issue) return;

    if (self::shouldNotify($mentionedUserId, 'issue_mentioned')) {
        self::create(
            userId: $mentionedUserId,
            type: 'issue_mentioned',
            title: 'You were mentioned',
            message: "You were mentioned in {$issue['key']}",
            actionUrl: "/issues/{$issue['key']}",
            actorUserId: $mentionerUserId,
            relatedIssueId: $issueId,
            relatedProjectId: $issue['project_id'],
            priority: 'high'
        );
    }
}

/**
 * Dispatch notification when issue is watched by user
 */
public static function dispatchIssueWatched(
    int $issueId,
    int $watcherUserId
): void {
    $issue = Database::selectOne(
        'SELECT id, key, title, project_id FROM issues WHERE id = ?',
        [$issueId]
    );

    if (!$issue) return;

    if (self::shouldNotify($watcherUserId, 'issue_watched')) {
        self::create(
            userId: $watcherUserId,
            type: 'issue_watched',
            title: 'You started watching',
            message: "You are now watching {$issue['key']}",
            actionUrl: "/issues/{$issue['key']}",
            relatedIssueId: $issueId,
            relatedProjectId: $issue['project_id'],
            priority: 'normal'
        );
    }
}

/**
 * Dispatch notification when user is added to project
 */
public static function dispatchProjectMemberAdded(
    int $projectId,
    int $newMemberId,
    int $addedByUserId
): void {
    $project = Database::selectOne(
        'SELECT id, key, name FROM projects WHERE id = ?',
        [$projectId]
    );

    if (!$project) return;

    if (self::shouldNotify($newMemberId, 'project_member_added')) {
        self::create(
            userId: $newMemberId,
            type: 'project_member_added',
            title: 'Added to project',
            message: "You were added to project {$project['name']}",
            actionUrl: "/projects/{$project['key']}",
            actorUserId: $addedByUserId,
            relatedProjectId: $projectId,
            priority: 'normal'
        );
    }
}
```

---

## STEP 5: Implement Rate Limiting (30 minutes)

### Step 5.1: Add Rate Limit Check to NotificationService

**File**: `src/Services/NotificationService.php`

Update the `create()` method:

```php
/**
 * Create a notification record with rate limiting
 * Prevents spam: max 10 notifications per minute per user
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
    // Rate limit check: max 10 notifications per minute per user
    $recentCount = Database::selectOne(
        'SELECT COUNT(*) as count FROM notifications 
         WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)',
        [$userId]
    );

    if ($recentCount && $recentCount['count'] >= 10) {
        throw new \Exception("Notification rate limit exceeded for user {$userId}");
    }

    // Use parameterized insert for security
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

    return $id;
}
```

---

## STEP 6: Verify Everything Works

### Test Checklist

```bash
# 1. Create the notification_preferences table
mysql -u root < migration_preferences.sql

# 2. Create the notifications_archive table
mysql -u root < migration_archive.sql

# 3. Test database
mysql -u root
USE jiira_clonee_system;
DESCRIBE notification_preferences;
DESCRIBE notifications_archive;
SHOW KEYS FROM notifications_archive;

# 4. Test API endpoints
curl http://localhost/jira_clone_system/api/v1/notifications

# 5. Test web page
curl http://localhost/jira_clone_system/notifications

# 6. Test profile settings
curl http://localhost/jira_clone_system/profile/notifications
```

### Manual Testing

1. **Log in as a user**
2. **Navigate to `/profile/notifications`**
3. **Verify you see all 6 event types with channel options**
4. **Toggle some preferences**
5. **Click "Save Preferences"**
6. **Check database**: 
   ```sql
   SELECT * FROM notification_preferences WHERE user_id = 1;
   ```
7. **Create an issue** - you should see a notification in `/notifications`
8. **Assign it to yourself** - check for notification
9. **Add a comment** - check for notification

---

## Quick Reference: Files Modified

| File | Changes | Lines |
|---|---|---|
| `database/schema.sql` | Add 2 tables | ~50 lines |
| `views/profile/notifications.php` | NEW FILE | 400 lines |
| `src/Controllers/UserController.php` | Add method | 20 lines |
| `src/Controllers/CommentController.php` | Add dispatch call | 1 line |
| `src/Controllers/IssueController.php` | Add dispatch call | 1 line |
| `src/Services/NotificationService.php` | Add 4 methods, update 1 | 100 lines |

**Total Time**: ~5 hours
**Affected Files**: 6
**New Code Lines**: ~575

---

## Deployment Checklist

- [ ] Create `notification_preferences` table
- [ ] Create `notifications_archive` table  
- [ ] Verify foreign keys
- [ ] Create `views/profile/notifications.php`
- [ ] Update `UserController.php`
- [ ] Update `CommentController.php`
- [ ] Update `IssueController.php`
- [ ] Update `NotificationService.php`
- [ ] Test all API endpoints
- [ ] Test web pages
- [ ] Test preference saving
- [ ] Test notifications trigger
- [ ] Load testing (100+ notifications)
- [ ] Deploy to production

---

**Next Step**: Execute STEP 1 immediately. Tables must exist before any preference operations.
