# How to Run CRITICAL FIX #3 Tests

## Quick Start (The Right Way)

```bash
php scripts/test-critical-fix-3.php
```

That's it! This will:
1. ✅ Bootstrap the application properly
2. ✅ Load all configuration
3. ✅ Connect to database
4. ✅ Run all 5 tests
5. ✅ Display results

---

## Expected Output

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

---

## If You Got an Error

### Error: "Database connection failed"

**Solution**: You're probably running it wrong. Use:
```bash
php scripts/test-critical-fix-3.php
```

NOT:
```bash
php tests/RaceConditionTestSuite.php  # ❌ Wrong - will fail
```

### Error: "MySQL Server has gone away"

**Solution**: Start MySQL first
```bash
# Windows: Start MySQL in XAMPP Control Panel
# Mac/Linux:
sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start
```

### Error: "Unknown database 'jira_clone_system'"

**Solution**: Apply the migration first
```bash
php scripts/run-migrations.php
```

---

## Pre-Test Checklist

Before running tests, verify:

### ✅ Is MySQL Running?
```bash
mysql -u root -e "SELECT 1;"
# Should say: 1
```

### ✅ Does Database Exist?
```bash
mysql -u root -e "SHOW DATABASES LIKE '%jira%';"
# Should list: jira_clone_system
```

### ✅ Has Migration Been Applied?
```bash
mysql jira_clone_system -e "SHOW TABLES LIKE '%dispatch%';"
# Should list: notification_dispatch_log
```

If any check fails, fix it:
```bash
# Start MySQL (Windows: use XAMPP Control Panel)

# Create/update database
php scripts/migrate-database.php

# Now run tests
php scripts/test-critical-fix-3.php
```

---

## Full Deployment Sequence

### Step 1: Apply Database Migration
```bash
php scripts/migrate-database.php
```

### Step 2: Run Tests
```bash
php scripts/test-critical-fix-3.php
```

### Step 3: Verify Results
All 5 tests should PASS

### Step 4: Check Database
```bash
mysql jira_clone_system -e "SELECT * FROM notification_dispatch_log LIMIT 1\G"
```

---

## Test Details

### What Each Test Does

| # | Test | Verifies |
|---|------|----------|
| 1 | Normal Dispatch | Single dispatch succeeds, dispatch_id set, log created |
| 2 | Duplicate Prevention | Second dispatch skipped, no new notifications |
| 3 | Atomic Transaction | All notifications have same dispatch_id |
| 4 | Dispatch Log | Correct table structure, metadata fields |
| 5 | Error Handling | Error messages captured, retry support |

### Test Execution Time

- Normal (fast): 1-2 seconds
- With logging: 2-5 seconds
- With slow MySQL: 5-10 seconds

**If taking >30 seconds**: MySQL might be slow, check with:
```bash
mysql -u root -e "SHOW STATUS WHERE variable_name = 'Threads_connected';"
```

---

## Files Involved

```
✅ scripts/test-critical-fix-3.php         ← Run this file
├─ bootstrap/app.php                       ← Loads config
├─ config/config.php                       ← Database config
├─ src/Core/Database.php                   ← Database class
├─ src/Services/NotificationService.php    ← Code being tested
└─ tests/RaceConditionTestSuite.php        ← Tests
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "Database connection failed" | `php scripts/test-critical-fix-3.php` (use wrapper, not direct) |
| "Unknown database" | `php scripts/migrate-database.php` |
| "MySQL has gone away" | Start MySQL service |
| Tests timeout | Check if MySQL is responsive |
| "Table doesn't exist" | Migration not applied |

---

## After Tests Pass ✅

Once all tests pass:

1. **Deploy code** - Your code changes are ready for production
2. **Monitor logs** - Watch `storage/logs/notifications.log`
3. **Manual test** - Add comment to issue, check notifications
4. **Verify dispatch_log** - Query table to confirm behavior

---

## One More Time: The Command

```bash
php scripts/test-critical-fix-3.php
```

**That's all you need.**

No need to:
- Run `php tests/RaceConditionTestSuite.php` ❌
- Bootstrap manually ❌
- Set environment variables ❌
- Create config files ❌

Just run the safe wrapper script above. ✅

---

## Still Having Issues?

See **CRITICAL_FIX_3_TROUBLESHOOTING.md** for detailed debugging.
