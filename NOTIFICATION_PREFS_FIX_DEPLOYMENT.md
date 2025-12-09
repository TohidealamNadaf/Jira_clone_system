# Notification Preferences Fix - Deployment Guide

**Date**: December 8, 2025  
**Status**: Ready for Deployment  
**Severity**: CRITICAL - Production Blocking Bug

---

## Quick Summary

The notification preferences feature was completely broken due to a SQL parameter binding error. When users tried to save their notification settings, the API returned a 500 error.

**Fixed**: Changed the `Database::insertOrUpdate()` method to use positional parameters instead of named parameters, eliminating PDO binding conflicts.

**Time to Deploy**: < 2 minutes

---

## What Was Broken

Users navigating to their notification preferences at:
```
http://localhost:8080/jira_clone_system/public/profile/notifications
```

And attempting to save their preferences would see:
```
Error updating preferences
Failed to update preferences
SQLSTATE[HY093]: Invalid parameter number
```

**Impact**: 
- Users cannot customize notification behavior
- All users receive default notifications only
- Notification system is only partially functional

---

## The Fix

### File Changed
**`src/Core/Database.php`**

### Change Details

The `insertOrUpdate()` method at lines 215-244 was refactored:

**Before** (Using named parameters - BROKEN):
```php
$placeholders = array_map(fn($col) => ":$col", $columns);

$updateClauses[] = "`$col` = :{$col}";  // Parameter reused

$stmt = self::query($sql, $data);  // Associative array
```

**After** (Using positional parameters - FIXED):
```php
$placeholders = array_fill(0, count($columns), '?');

$updateClauses[] = "`$col` = VALUES(`$col`)";  // Using VALUES() function

$params = array_values($data);
$stmt = self::query($sql, $params);  // Ordered array
```

### Why This Works

1. **Positional Parameters**: Using `?` placeholders are bound in order, avoiding naming conflicts
2. **VALUES() Function**: MySQL's `VALUES(col)` references the insertion value, no parameter binding needed
3. **Compatibility**: Works with MySQL 5.7 through 8.0.21+
4. **Reliability**: PDO can reliably bind positional parameters without confusion

---

## Deployment Steps

### Step 1: Backup Current Files
```bash
# Optional but recommended
cp src/Core/Database.php src/Core/Database.php.backup
```

### Step 2: Deploy Fixed Code
Replace `src/Core/Database.php` with the fixed version.

**Checklist**:
- [ ] File backed up (optional)
- [ ] New version deployed
- [ ] File permissions correct (readable)
- [ ] No syntax errors in PHP

### Step 3: Verify Installation
Run the verification script:
```bash
php verify_notification_prefs_fixed.php
```

**Expected Output**:
```
╔═══════════════════════════════════════════════════════════════╗
║     NOTIFICATION PREFERENCES SQL FIX - VERIFICATION          ║
╚═══════════════════════════════════════════════════════════════╝

Test 1: Database Connection... ✓ PASS
Test 2: Check notification_preferences table... ✓ PASS
Test 3: Test insertOrUpdate with positional parameters... ✓ PASS
Test 4: Verify saved data... ✓ PASS

✓ ALL TESTS PASSED - System is production-ready!
```

### Step 4: Manual Testing
1. Open browser and navigate to:
   ```
   http://localhost:8080/jira_clone_system/public/profile/notifications
   ```

2. Modify a notification preference (check/uncheck a box)

3. Click "Save Preferences" button

4. Expected results:
   - ✓ No error displayed
   - ✓ Success message shown
   - ✓ Browser console clean (no 500 error)
   - ✓ Page refresh shows saved preferences persist

### Step 5: Database Verification
Query the database to confirm data is saved:
```sql
SELECT user_id, event_type, in_app, email, push 
FROM notification_preferences 
WHERE user_id = 1 
ORDER BY event_type;
```

**Expected columns**:
| user_id | event_type | in_app | email | push |
|---------|-----------|--------|-------|------|
| 1 | comment_reply | 1 | 1 | 0 |
| 1 | issue_assigned | 1 | 1 | 0 |
| 1 | issue_commented | 1 | 1 | 0 |
| ... | ... | ... | ... | ... |

---

## Rollback Plan

If issues occur after deployment:

```bash
# Restore backup
cp src/Core/Database.php.backup src/Core/Database.php

# Clear any caches (if applicable)
# Restart PHP-FPM or web server
```

---

## Testing Scenarios

### Scenario 1: Enable All Notifications
1. Navigate to `/profile/notifications`
2. Check all boxes for "in_app", "email", "push"
3. Click "Save Preferences"
4. Refresh page
5. **Expected**: All boxes remain checked

### Scenario 2: Disable Specific Channel
1. Navigate to `/profile/notifications`
2. Uncheck "push" notifications
3. Click "Save Preferences"
4. Refresh page
5. **Expected**: "in_app" and "email" checked, "push" unchecked

### Scenario 3: Bulk Update All Event Types
1. Navigate to `/profile/notifications`
2. Modify multiple event types
3. Click "Save Preferences"
4. Refresh page
5. **Expected**: All changes persist

### Scenario 4: Cross-User Isolation
1. Login as User A, modify preferences, save
2. Login as User B, verify User B's preferences different
3. **Expected**: Each user has independent preferences

---

## Monitoring After Deployment

### Watch These Logs
- **Application Error Log**: `storage/logs/notifications.log`
- **Security Log**: `storage/logs/security.log`
- **Web Server Error Log**: Apache/XAMPP error log

### Metrics to Monitor
- Number of preference update requests
- Success rate of preference updates
- Error rate (should be 0%)
- API response times (should be < 100ms)

### Error Message to Watch For
If you see this error, rollback immediately:
```
SQLSTATE[HY093]: Invalid parameter number
```

---

## Communication

### To Your Team
> "We've fixed a critical bug in the notification preferences feature. Users can now successfully configure which notifications they want to receive. The fix has been tested and verified."

### To Your Users (If Applicable)
> "The notification preferences feature is now working correctly. You can customize which types of notifications you receive and through which channels (in-app, email, push) by visiting your profile settings."

---

## Success Criteria

Deployment is successful when:
- ✅ No errors in application logs
- ✅ Verification script passes all tests
- ✅ Manual testing succeeds
- ✅ Users can save preferences without errors
- ✅ Preferences persist across page refreshes
- ✅ Different users have independent preferences

---

## Technical Details

For deeper understanding, see:
- `CRITICAL_FIX_NOTIFICATION_PREFERENCES_SAVE.md` - Executive summary
- `NOTIFICATION_PREFERENCES_SQL_FIX.md` - Detailed technical explanation
- `NOTIFICATIONS_SYSTEM_SPEC.md` - Overall system architecture

---

## Support

If you encounter issues:

1. **Check the error message** in `storage/logs/notifications.log`
2. **Run verification script** to identify the problem
3. **Review manual tests** to confirm expected behavior
4. **Rollback if necessary** using the backup

---

## Deployment Checklist

- [ ] Review changes in `src/Core/Database.php`
- [ ] Backup current version
- [ ] Deploy fixed version
- [ ] Verify file permissions
- [ ] Run verification script
- [ ] Perform manual testing
- [ ] Check database records
- [ ] Monitor error logs
- [ ] Confirm user reporting success
- [ ] Update release notes

---

**This fix is critical and required for the notification system to function properly.**

Deployment should be prioritized and completed at the earliest convenience.

---

**Last Updated**: December 8, 2025  
**Status**: Production Ready  
**Estimated Deployment Time**: 2 minutes
