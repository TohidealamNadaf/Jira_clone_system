# Notification Foundation - Quick Fix Checklist

**Time Required**: 4.5 hours | **Complexity**: Medium | **Risk**: Low

---

## 🚀 5-Step Fix Plan

### ✅ Step 1: Create Tables (30 min)

```sql
-- Run these in MySQL one by one

USE jiira_clonee_system;

-- Table 1: User preferences
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

-- Table 2: Archive for old notifications
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

-- Verify
DESCRIBE notification_preferences;
DESCRIBE notifications_archive;
```

**✓ Done?** Check: `SHOW TABLES LIKE 'notification%';`

---

### ✅ Step 2: Build Profile Settings Page (2 hours)

**File**: Create `views/profile/notifications.php`

Copy the complete file from: `NOTIFICATION_FOUNDATION_FIXES.md` → Step 2.1

Or create with:
```bash
# Windows
copy "c:\xampp\htdocs\jira_clone_system\NOTIFICATION_FOUNDATION_FIXES.md" "c:\xampp\htdocs\jira_clone_system\views\profile\notifications.php"

# Or manually create and paste content
```

**✓ Done?** File should be 400+ lines and have form elements for each event type

---

### ✅ Step 3: Update Controllers (1.5 hours)

#### A. Update UserController

**File**: `src/Controllers/UserController.php`

Add method (around line 200-250):

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

Add import at top:
```php
use App\Services\NotificationService;
```

**✓ Done?** `http://localhost/jira_clone_system/profile/notifications` should load page

---

#### B. Update CommentController

**File**: `src/Controllers/CommentController.php`

Find `store()` method, add ONE LINE after comment is created:

```php
// AFTER: $comment = CommentService::create(...)
NotificationService::dispatchIssueCommented($issueId, $userId, $comment['id']);
```

Add import:
```php
use App\Services\NotificationService;
```

**✓ Done?** Comments now trigger notifications

---

#### C. Update IssueController

**File**: `src/Controllers/IssueController.php`

Find `transition()` method, add ONE LINE after status update:

```php
// AFTER: IssueService::updateStatus(...)
NotificationService::dispatchIssueStatusChanged($issueId, $newStatus, $userId);
```

Add import if missing:
```php
use App\Services\NotificationService;
```

**✓ Done?** Status changes now trigger notifications

---

### ✅ Step 4: Add Service Methods (1 hour)

**File**: `src/Services/NotificationService.php`

Add these 3 methods at the end of the class (before closing brace):

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

**✓ Done?** All 3 methods added to NotificationService class

---

### ✅ Step 5: Test Everything (30 min)

#### Quick Tests

```bash
# 1. Test MySQL tables exist
mysql -u root -e "USE jiira_clonee_system; DESCRIBE notification_preferences;"
mysql -u root -e "USE jiira_clonee_system; DESCRIBE notifications_archive;"

# 2. Test web page loads
curl http://localhost/jira_clone_system/profile/notifications

# 3. Test API endpoint
curl http://localhost/jira_clone_system/api/v1/notifications
```

#### Manual Testing (in browser)

1. **Log in** as any user
2. **Navigate** to `/profile/notifications`
3. **Verify** you see checkboxes for:
   - Issue Created
   - Issue Assigned
   - Issue Commented
   - Issue Status Changed
   - Issue Mentioned
   - Issue Watched

4. **Toggle** some preferences
5. **Click** "Save Preferences"
6. **Verify** green success message appears

7. **Create new issue** → should see notification in `/notifications`
8. **Assign to yourself** → should see notification
9. **Add comment** → should see notification

**✓ All working?** System is ready to use!

---

## 📋 Checklist Format

```
STEP 1: Tables Created
☐ notification_preferences table created
☐ notifications_archive table created
☐ Foreign keys working
☐ Can describe both tables in MySQL

STEP 2: Profile Page Built
☐ views/profile/notifications.php exists
☐ File is 400+ lines
☐ Has all event type cards
☐ Page loads without errors

STEP 3: Controllers Updated
☐ UserController.profileNotifications() method added
☐ CommentController has NotificationService import
☐ CommentController.store() calls dispatchIssueCommented()
☐ IssueController.transition() calls dispatchIssueStatusChanged()

STEP 4: Service Methods Added
☐ dispatchIssueMentioned() added
☐ dispatchIssueWatched() added
☐ dispatchProjectMemberAdded() added

STEP 5: Everything Tested
☐ Tables exist in MySQL
☐ Profile page loads
☐ API endpoints work
☐ Creating issue triggers notification
☐ Assigning issue triggers notification
☐ Commenting triggers notification
☐ Saving preferences works
☐ Notifications appear in /notifications page
```

---

## 🆘 Troubleshooting

### Error: "UNIQUE constraint failed"
```
Problem: notification_preferences table already exists
Solution: Check if table exists with: SHOW TABLES LIKE 'notification%';
If it exists, skip table creation
```

### Error: "Method not found" in controller
```
Problem: Method not added correctly
Solution: 
1. Check file exists at exact location
2. Check method has `public function` keyword
3. Check closing braces match
4. Restart PHP server
```

### Error: "View not found"
```
Problem: View file missing or wrong path
Solution:
1. File must be: views/profile/notifications.php
2. Check file extension is .php
3. Check file content starts with <?php
4. Run php scripts/verify-and-seed.php
```

### API returns empty array
```
Problem: No notifications created yet
Solution: Create an issue first, then check API
```

### Preferences not saving
```
Problem: notification_preferences table doesn't exist
Solution: Run Step 1 MySQL commands again
```

---

## ⏱️ Time Breakdown

| Step | Task | Time | Done? |
|---|---|---|---|
| 1 | Create tables | 30 min | ☐ |
| 2 | Build UI | 2 hrs | ☐ |
| 3 | Update controllers | 1.5 hrs | ☐ |
| 4 | Add service methods | 1 hr | ☐ |
| 5 | Test | 30 min | ☐ |
| **Total** | | **5 hrs** | |

---

## 🎯 What You'll Have After This

✅ Users can see all notifications
✅ Users can manage preferences
✅ Comments trigger notifications
✅ Status changes trigger notifications
✅ Professional notification center
✅ Enterprise-ready system
✅ Production deployable

---

## Next Week (Optional)

Once this is done and working, consider adding:
- Email notifications (4 hours)
- Push notifications (4 hours)
- Notification analytics (3 hours)

But the system is **fully functional** without these.

---

## Questions?

Refer to full documentation:
- `NOTIFICATION_FOUNDATION_AUDIT.md` - Detailed findings
- `NOTIFICATION_FOUNDATION_FIXES.md` - Step-by-step guide
- `NOTIFICATION_ENTERPRISE_SUMMARY.md` - Executive summary

---

**Start with Step 1. Execute in order. You got this!** ✅
