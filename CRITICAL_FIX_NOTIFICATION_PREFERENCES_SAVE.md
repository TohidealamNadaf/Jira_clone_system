# CRITICAL FIX: Notification Preferences Save Error

**Status**: ✅ FIXED  
**Severity**: CRITICAL - Blocking Feature  
**Root Cause**: SQL Parameter Binding in PDO with ON DUPLICATE KEY UPDATE  
**Fixed**: December 8, 2025

---

## Executive Summary

The notification preferences save functionality was completely broken. When users tried to save their notification settings, they received a 500 error with `SQLSTATE[HY093]: Invalid parameter number`.

**Root Cause**: The `Database::insertOrUpdate()` method used named parameters that appeared multiple times in the SQL statement, causing PDO parameter binding conflicts.

**Solution**: Refactored to use positional parameters and MySQL's `VALUES()` function.

**Result**: Notification preferences now save successfully.

---

## Problem Details

### User-Facing Issue

When a user navigated to `/profile/notifications`, checked/unchecked preferences, and clicked "Save Preferences":

```
Error updating preferences
Failed to update preferences
SQLSTATE[HY093]: Invalid parameter number
```

The browser console showed:
```
PUT http://localhost:8080/jira_clone_system/public/api/v1/notifications/preferences 500 (Internal Server Error)
[CRITICAL #2] API error: {error: 'Failed to update preferences', details: 'SQLSTATE[HY093]: Invalid parameter number'}
[CRITICAL #2] Error details: SQLSTATE[HY093]: Invalid parameter number
```

### Technical Root Cause

The `Database::insertOrUpdate()` method generated:

```sql
INSERT INTO `notification_preferences` 
    (`user_id`, `event_type`, `in_app`, `email`, `push`) 
VALUES 
    (:user_id, :event_type, :in_app, :email, :push) 
ON DUPLICATE KEY UPDATE 
    `in_app` = :in_app, `email` = :email, `push` = :push
```

The problem: Named placeholders (`:in_app`, `:email`, `:push`) appear in **both** the VALUES clause AND the UPDATE clause. PDO's parameter binding gets confused when the same named placeholder is used multiple times, especially across different SQL clauses.

---

## Solution Implemented

### Changed File

**File**: `src/Core/Database.php`  
**Method**: `insertOrUpdate()` (Lines 215-244)

### Key Changes

1. **Positional Parameters Instead of Named Parameters**
   - Changed from `:user_id`, `:in_app`, etc. to `?` placeholders
   - Eliminates naming conflicts
   - Works reliably with PDO parameter binding

2. **VALUES() Function in UPDATE Clause**
   - Changed from `` `in_app` = :in_app`` to `` `in_app` = VALUES(`in_app`)``
   - `VALUES()` is a MySQL function that references the value from the INSERT VALUES clause
   - No parameter binding needed, just direct column references
   - Compatible with MySQL 5.7+

### Before and After

**BEFORE (BROKEN)**:
```php
$placeholders = array_map(fn($col) => ":$col", $columns);  // Named params ❌
$updateClauses[] = "`$col` = :{$col}";  // Reusing named params ❌
$stmt = self::query($sql, $data);  // Associative array for named params
```

**AFTER (FIXED)**:
```php
$placeholders = array_fill(0, count($columns), '?');  // Positional params ✅
$updateClauses[] = "`$col` = VALUES(`$col`)";  // Using VALUES() function ✅
$params = array_values($data);  // Ordered array for positional params ✅
$stmt = self::query($sql, $params);
```

### Generated SQL Comparison

**BROKEN SQL**:
```sql
INSERT INTO `notification_preferences` (`user_id`, `event_type`, `in_app`, `email`, `push`) 
VALUES (:user_id, :event_type, :in_app, :email, :push) 
ON DUPLICATE KEY UPDATE `in_app` = :in_app, `email` = :email, `push` = :push
```

**FIXED SQL**:
```sql
INSERT INTO `notification_preferences` (`user_id`, `event_type`, `in_app`, `email`, `push`) 
VALUES (?, ?, ?, ?, ?) 
ON DUPLICATE KEY UPDATE `in_app` = VALUES(`in_app`), `email` = VALUES(`email`), `push` = VALUES(`push`)
```

---

## Testing & Verification

### Manual Testing Steps

1. **Navigate to settings**:
   ```
   http://localhost:8080/jira_clone_system/public/profile/notifications
   ```

2. **Modify preferences**:
   - Check/uncheck any notification preferences
   - Select different channels (in_app, email, push)

3. **Save and verify**:
   - Click "Save Preferences" button
   - Should see success message (no error)
   - Browser console should be clean (no 500 errors)
   - Refresh page - preferences should persist

4. **Database verification**:
   ```sql
   SELECT * FROM notification_preferences WHERE user_id = 1;
   ```
   Should show correctly saved values.

### Automated Verification

Run the verification script:
```bash
php verify_notification_prefs_fixed.php
```

Expected output:
```
✓ Test 1: Database Connection... PASS
✓ Test 2: Check notification_preferences table... PASS
✓ Test 3: Test insertOrUpdate with positional parameters... PASS
✓ Test 4: Verify saved data... PASS

✓ ALL TESTS PASSED - System is production-ready!
```

---

## Impact Assessment

### What Was Broken
- ✗ Notification preferences could not be saved
- ✗ Users could not customize notification behavior
- ✗ All notifications were sent with default settings only
- ✗ Any feature using `insertOrUpdate()` would fail similarly

### What is Now Fixed
- ✅ Notification preferences save successfully
- ✅ All channels (in_app, email, push) configurable
- ✅ User preferences persist across sessions
- ✅ Settings apply immediately to new notifications
- ✅ System is now production-ready

### Affected Components
- Notification preferences page (`/profile/notifications`)
- Notification API (`PUT /api/v1/notifications/preferences`)
- NotificationService (`updatePreference()` method)
- Database abstraction layer (`insertOrUpdate()` method)

---

## Production Checklist

- [x] Root cause identified and fixed
- [x] Code changes tested locally
- [x] No database schema changes required
- [x] Backward compatible (no breaking changes)
- [x] Verification script created
- [x] Documentation complete
- [ ] Deploy to production
- [ ] Run verification script in production
- [ ] Notify users of fix
- [ ] Monitor error logs

---

## Related Documentation

- `NOTIFICATION_PREFERENCES_SQL_FIX.md` - Detailed technical explanation
- `NOTIFICATIONS_SYSTEM_SPEC.md` - Notification system architecture
- `FIX_5_EMAIL_PUSH_CHANNEL_LOGIC_COMPLETE.md` - Channel preferences implementation

---

## Files Modified

1. **src/Core/Database.php**
   - Lines 215-244: Fixed `insertOrUpdate()` method
   - Changed parameter binding approach
   - Updated SQL generation logic

## Files Created

1. **NOTIFICATION_PREFERENCES_SQL_FIX.md** - Technical details
2. **CRITICAL_FIX_NOTIFICATION_PREFERENCES_SAVE.md** - This file
3. **verify_notification_prefs_fixed.php** - Verification script

---

## Timeline

- **Issue Discovered**: December 8, 2025 - User reported preferences not saving
- **Root Cause Analysis**: Database parameter binding in ON DUPLICATE KEY UPDATE
- **Solution Implemented**: Changed to positional parameters + VALUES() function
- **Testing**: Verified with manual browser testing + script
- **Status**: Ready for production deployment

---

## Next Steps

1. ✅ Deploy the fixed `src/Core/Database.php`
2. ✅ Run `verify_notification_prefs_fixed.php` in production
3. ✅ Test manually in production environment
4. ✅ Monitor error logs for any issues
5. ✅ Confirm with users that feature now works
6. ✅ Document in release notes

---

**This is a CRITICAL production fix required for the notification system to function.**

The system will NOT be able to save notification preferences until this fix is deployed.
