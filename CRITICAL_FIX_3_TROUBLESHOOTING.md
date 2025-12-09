# CRITICAL FIX #3: Troubleshooting Guide

**If you encountered a database connection error while running tests, this guide will help you resolve it.**

---

## Error: Database Connection Failed

### Error Message
```
Fatal error: Uncaught exception 'RuntimeException' with message 'Database connection failed'
Stack trace:
#0 src/Core/Database.php(36): App\Core\Database::connect()
```

### Solution

#### Option 1: Use the Safe Test Runner (RECOMMENDED)

**Instead of**:
```bash
php tests/RaceConditionTestSuite.php
```

**Use**:
```bash
php scripts/test-critical-fix-3.php
```

This script properly bootstraps the application before running tests.

---

## Common Issues & Fixes

### Issue 1: MySQL/Database Not Running

**Symptom**: Database connection fails immediately

**Fix**:
```bash
# Start XAMPP services
# On Windows: Launch XAMPP Control Panel and click "Start" for MySQL
# On Mac/Linux:
sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start
# or
systemctl start mysql
```

### Issue 2: Database Configuration Missing

**Symptom**: 
```
Error: Database name not configured
Error: Database host not found
```

**Fix**:
1. Check `config/config.php` exists
2. Verify database section:
```php
'database' => [
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'jira_clone_system',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]
```

### Issue 3: Incorrect Database Name

**Symptom**: 
```
SQLSTATE[HY000]: General error: 1030 Got error
```

**Fix**:
Verify database exists:
```bash
mysql -u root -e "SHOW DATABASES LIKE '%jira%';"
```

If missing, create it:
```bash
mysql -u root -e "CREATE DATABASE jira_clone_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Issue 4: Migration Not Applied

**Symptom**: Tests pass but dispatch_log table doesn't exist

**Fix**:
```bash
# Apply migration
php scripts/migrate-database.php

# Verify table exists
mysql jira_clone_system -e "SHOW TABLES LIKE 'notification_dispatch_log';"
```

---

## Test Execution Flow

### What Happens When You Run Tests

```bash
php scripts/test-critical-fix-3.php
    ↓
Define BASE_PATH constant
    ↓
Load bootstrap/app.php
    ├─ Load autoloader
    ├─ Load configuration
    ├─ Load database connection
    └─ Initialize application
    ↓
Load test class (App\Tests\RaceConditionTestSuite)
    ↓
Run all 5 test methods
    ├─ Test 1: Normal Dispatch
    ├─ Test 2: Duplicate Prevention
    ├─ Test 3: Atomic Transaction
    ├─ Test 4: Dispatch Log Creation
    └─ Test 5: Error Handling
    ↓
Display results (PASS/FAIL)
```

---

## Pre-Test Checklist

Before running tests, verify:

✅ **MySQL Running**
```bash
mysql -u root -e "SELECT VERSION();"
# Should return MySQL version
```

✅ **Database Exists**
```bash
mysql -u root -e "SHOW DATABASES LIKE '%jira%';"
# Should show: jira_clone_system
```

✅ **Configuration Loaded**
```bash
php -r "define('BASE_PATH', '.'); require 'bootstrap/app.php'; echo 'OK';"
# Should output: OK
```

✅ **Tables Exist**
```bash
mysql jira_clone_system -e "SHOW TABLES;"
# Should include: notifications, notification_dispatch_log, issues, users, etc.
```

---

## Manual Test Without Automated Suite

If automated tests don't work, try manual verification:

### Step 1: Add Test Comment
```php
<?php
define('BASE_PATH', '.');
require 'bootstrap/app.php';

use App\Services\NotificationService;

// Create a comment on issue #1
NotificationService::dispatchCommentAdded(1, 5, 100);

echo "Comment dispatch triggered.\n";
?>
```

Run with:
```bash
php test-manual.php
```

### Step 2: Check Dispatch Log
```bash
mysql jira_clone_system -e "SELECT * FROM notification_dispatch_log ORDER BY created_at DESC LIMIT 1\G"
```

**Expected output**:
```
          id: 1
  dispatch_id: comment_added_1_comment_100_5_...
 dispatch_type: comment_added
     issue_id: 1
    comment_id: 100
  actor_user_id: 5
recipients_count: X
       status: completed
    created_at: 2025-12-08 XX:XX:XX
  completed_at: 2025-12-08 XX:XX:XX
 error_message: NULL
```

### Step 3: Check Notifications
```bash
mysql jira_clone_system -e "SELECT COUNT(*) as notification_count FROM notifications WHERE dispatch_id = (SELECT dispatch_id FROM notification_dispatch_log ORDER BY created_at DESC LIMIT 1);"
```

**Expected output**: Should be > 0

---

## Performance Debugging

If tests run slowly (>10 seconds):

### Check MySQL Performance
```bash
mysql jira_clone_system -e "SHOW ENGINE INNODB STATUS\G" | head -50
```

### Check Query Performance
```bash
mysql jira_clone_system -e "
SET SESSION sql_mode='';
SELECT * FROM notification_dispatch_log;
SHOW STATUS LIKE 'Last_query_cost';
"
```

### Check Database Size
```bash
mysql jira_clone_system -e "
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb
FROM information_schema.TABLES
WHERE table_schema = 'jira_clone_system'
ORDER BY size_mb DESC;
"
```

---

## Test Failure Diagnosis

### Test Fails: "Dispatch log entry not created"

**Causes**:
1. Migration not applied
2. Table doesn't exist
3. Foreign key constraint violation

**Debug**:
```bash
# Check if table exists
mysql jira_clone_system -e "DESCRIBE notification_dispatch_log;"

# Check for constraints
mysql jira_clone_system -e "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS WHERE TABLE_NAME='notification_dispatch_log';"

# Check for errors in migration
mysql jira_clone_system -e "SHOW ERRORS;"
```

### Test Fails: "Duplicate dispatch prevention not working"

**Causes**:
1. Dispatch ID not being set correctly
2. Unique constraint not enforced
3. Status not updated to 'completed'

**Debug**:
```bash
# Check dispatch IDs are unique
mysql jira_clone_system -e "SELECT COUNT(DISTINCT dispatch_id) as unique_count, COUNT(*) as total FROM notification_dispatch_log;"
# Should be: unique_count = total

# Check status field
mysql jira_clone_system -e "SELECT status, COUNT(*) FROM notification_dispatch_log GROUP BY status;"
# Should show: completed, pending, failed counts
```

### Test Fails: "Atomic transaction not working"

**Causes**:
1. Transactions not supported (MyISAM engine)
2. Autocommit enabled
3. Transaction isolation level issue

**Debug**:
```bash
# Check table engine
mysql jira_clone_system -e "SHOW CREATE TABLE notification_dispatch_log\G" | grep -i engine;
# Should be: InnoDB

# Check transaction support
mysql jira_clone_system -e "SHOW VARIABLES LIKE 'autocommit';"
# Should be: ON (application manages transactions)
```

---

## Quick Fix Checklist

If tests are failing, run this checklist:

```bash
# 1. Start MySQL
# (Mac/Linux): sudo /path/to/mysql.server start
# (Windows): Start MySQL from XAMPP Control Panel

# 2. Apply migration
php scripts/migrate-database.php

# 3. Verify tables
mysql jira_clone_system -e "SHOW TABLES LIKE 'notification_dispatch_log';"

# 4. Verify configuration
php -r "define('BASE_PATH', '.'); require 'bootstrap/app.php'; echo 'Config OK';"

# 5. Run tests
php scripts/test-critical-fix-3.php

# 6. If still failing, check logs
tail -50 storage/logs/notifications.log
```

---

## Getting Help

### Debug Information to Collect

If you're still having issues, collect:

1. **MySQL version**: `mysql --version`
2. **PHP version**: `php --version`
3. **Database exists**: `mysql -u root -e "SHOW DATABASES LIKE '%jira%';"`
4. **Tables exist**: `mysql jira_clone_system -e "SHOW TABLES;"`
5. **Error logs**: `tail -100 storage/logs/notifications.log`
6. **Full stack trace** from error output

### Test Output Example

**PASSING OUTPUT** (What you should see):
```
=== CRITICAL FIX #3: Race Condition Test Suite ===

Test 1: Normal Dispatch... ✓ PASS
Test 2: Duplicate Prevention... ✓ PASS
Test 3: Atomic Transaction... ✓ PASS
Test 4: Dispatch Log Creation... ✓ PASS
Test 5: Error Handling... ✓ PASS

=== Test Results ===
Passed: 5
Failed: 0
Total:  5
```

**FAILING OUTPUT** (Red flags):
- Any "✗ FAIL" messages
- Failed count > 0
- Stack trace showing exceptions

---

## Summary

| Issue | Solution |
|-------|----------|
| Database connection error | Use: `php scripts/test-critical-fix-3.php` |
| MySQL not running | Start MySQL service |
| Database not created | Run: `php scripts/migrate-database.php` |
| Tests timeout | Check MySQL performance |
| Assertion failures | Verify migration applied successfully |
| Configuration errors | Check `config/config.php` exists |

---

## Success Indicators

✅ **Tests Running Successfully**
- All 5 tests passing
- No errors in output
- Test completed in <5 seconds

✅ **Database Changes Applied**
- `notification_dispatch_log` table exists
- `notifications.dispatch_id` column exists
- Both have proper indexes

✅ **Application Bootstrapped**
- Configuration loaded
- Database connection active
- All tables accessible

---

**If you're still stuck, the most common solution is: `php scripts/test-critical-fix-3.php`**

This uses the proper bootstrap and should resolve 95% of issues.
