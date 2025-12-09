# Notifications System Testing Guide

## ✅ Quick Test (No Login Required)

Visit this URL to verify the database schema is correct:

```
http://localhost:8080/jira_clone_system/public/test-notifications-simple.php
```

**Expected Result**: Green checkmarks for all tests, showing:
- ✓ Database connected
- ✓ notifications table exists
- ✓ All 13 columns present
- ✓ Indexes created
- ✓ Query works

---

## ✅ Create Test Notification (Login Required)

### Step 1: Log In
Visit and log in with your credentials:
```
http://localhost:8080/jira_clone_system/public/
```

### Step 2: Create Test Notification
Then visit:
```
http://localhost:8080/jira_clone_system/public/create-test-notification.php
```

**Expected Result**: 
- ✓ "SUCCESS!" message
- Notification created with ID shown
- Stats showing total and unread counts

---

## ✅ View Notifications (Login Required)

Visit the main notifications page:
```
http://localhost:8080/jira_clone_system/public/notifications
```

**Expected Result**:
- Page loads without errors
- Shows your notifications
- Test notification appears (if you created one)
- You can mark as read/unread

---

## ✅ Full Function Test (Login Required)

Visit this page while logged in:
```
http://localhost:8080/jira_clone_system/public/test-notifications-page.php
```

**Expected Result**:
- Shows your email and user ID
- Tests all NotificationService methods:
  - ✓ getAll() - All notifications
  - ✓ getCount() - Total count
  - ✓ getUnreadCount() - Unread count
  - ✓ getUnread() - Unread notifications
  - ✓ getStats() - Statistics

---

## 🔍 Manual Database Check

Run this command in your terminal:

```bash
php -r "$mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system'); $result = $mysqli->query('DESC notifications'); while ($row = $result->fetch_assoc()) { echo $row['Field'] . ': ' . $row['Type'] . PHP_EOL; } $mysqli->close();"
```

**Expected Output** (13 columns):
```
id: bigint(20) unsigned
user_id: int(10) unsigned
type: varchar(100)
title: varchar(255)          ← NEW
message: text                ← NEW
action_url: varchar(500)     ← NEW
actor_user_id: int(10)       ← NEW
related_issue_id: int(10)    ← NEW
related_project_id: int(10)  ← NEW
priority: varchar(20)        ← NEW
is_read: tinyint(1)          ← NEW
read_at: timestamp
created_at: timestamp
```

---

## 📋 Testing Checklist

- [ ] Run simple schema test (no login needed)
- [ ] Log in to your account
- [ ] Create test notification
- [ ] View notifications page
- [ ] Run full function test while logged in
- [ ] Check database columns manually

---

## ❌ Troubleshooting

### Error: "Unknown column 'title'"
- The schema fix didn't apply
- Run: `php run_fix_notifications.php` again
- Check database columns with manual check above

### Error: "Call to private Application::__construct()"
- Use `test-notifications-page.php` (fixed version)
- Or use `test-notifications-simple.php` (no login needed)

### Error: "You need to be logged in"
- Log in first at main page
- Then revisit the test page
- Cookie/session must be active

### Page loads but no notifications appear
- That's normal - you haven't created any yet
- Use create-test-notification.php to add one
- Or create real issues/comments in projects

---

## ✨ Success Signs

✅ Schema test page shows all green checkmarks  
✅ Can create test notifications  
✅ Notifications page loads without errors  
✅ Stats show correct counts  
✅ Can view notifications in database

---

## 📊 Database Verification Commands

Check notification count:
```bash
php -r "$mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system'); $result = $mysqli->query('SELECT COUNT(*) as count FROM notifications'); $row = $result->fetch_assoc(); echo 'Total notifications: ' . $row['count'] . PHP_EOL; $mysqli->close();"
```

Check column exists:
```bash
php -r "$mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system'); $result = $mysqli->query('SELECT title FROM notifications LIMIT 1'); if ($result) { echo 'title column exists!'; } else { echo 'title column NOT found'; } $mysqli->close();"
```

Check all indexes:
```bash
php -r "$mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system'); $result = $mysqli->query('SHOW INDEX FROM notifications'); while ($row = $result->fetch_assoc()) { echo $row['Key_name'] . ' on ' . $row['Column_name'] . PHP_EOL; } $mysqli->close();"
```

---

**Status**: 🚀 Notifications system is ready to use!
