# Notification Preferences - Debugging Guide

If you're still getting "Error updating preferences", follow this guide to identify the exact issue.

---

## Step 1: Check Browser Console for Error Details

1. Open your browser's Developer Tools (F12 or Right-click → Inspect)
2. Go to the **Console** tab
3. Go to `/profile/notifications`
4. Try to save preferences
5. Look for error messages in console - they will show the actual API error

**Expected console output on success:**
```
Preferences saved: {status: 'success', message: 'Preferences updated', updated_count: 9}
```

**If you see an error**, copy the full error message and check the sections below.

---

## Step 2: Verify Database Method Exists

Visit: `http://localhost/jira_clone_system/public/debug-notification-api.php`

This page will show:
- ✓/✗ If `Database::insertOrUpdate()` method exists
- ✓/✗ If `notification_preferences` table exists
- ✓/✗ If getPreferences() works
- ✓/✗ If updatePreference() works
- Actual test data showing before/after values

**All checks should show PASS**

---

## Step 3: Test Preferences Directly via API

Using curl or Postman:

```bash
# First, get your JWT token by logging in
# Then make this API call:

curl -X PUT http://localhost/jira_clone_system/public/api/v1/notifications/preferences \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "preferences": {
      "issue_created": {
        "in_app": true,
        "email": false,
        "push": true
      }
    }
  }'
```

**Expected response:**
```json
{
  "status": "success",
  "message": "Preferences updated",
  "updated_count": 1
}
```

**If you get an error**, the response will include:
```json
{
  "error": "Failed to update preferences",
  "details": "..."
}
```

The `details` field will show the actual error.

---

## Common Issues & Solutions

### Issue 1: "Method not found" or "Call to undefined method"

**Error message**: `Call to undefined method insertOrUpdate`

**Solution**: 
1. Verify the fix was applied: Check `src/Core/Database.php` line 215+
2. The `insertOrUpdate()` method should be there
3. If not, re-apply the fix from NOTIFICATION_PREFERENCES_FIX.md

```php
// Line 215 in src/Core/Database.php should have:
public static function insertOrUpdate(string $table, array $data, array $uniqueKeys = []): bool
```

### Issue 2: "Table not found" or "notification_preferences doesn't exist"

**Error message**: `Table 'jira_clone.notification_preferences' doesn't exist`

**Solution**:
1. Run the database migration:
   ```bash
   php scripts/verify-and-seed.php
   ```
2. Or manually run the SQL in `database/migrations/001_create_notifications_tables.sql`

**Verify the table exists**:
```sql
SHOW TABLES LIKE 'notification_preferences';
DESC notification_preferences;
```

### Issue 3: "Unauthorized" Error

**Error message**: `{"error": "Unauthorized"}`

**Solution**:
1. Make sure you're logged in
2. The CSRF token should be present in the page (`meta[name="csrf-token"]`)
3. Check that the Authorization header is being sent if using API directly

### Issue 4: "Updated count is 0"

**Error message**: `"updated_count": 0` in response, but no error

**Solution**:
1. This means the preferences are not being found/updated
2. Check if user preferences are initialized:
   ```sql
   SELECT COUNT(*) FROM notification_preferences WHERE user_id = 2;
   ```
3. If empty, preferences weren't initialized. Initialize them:
   ```sql
   INSERT INTO notification_preferences 
   (user_id, event_type, in_app, email, push)
   SELECT 2, 'issue_created', 1, 1, 0
   WHERE NOT EXISTS (
     SELECT 1 FROM notification_preferences 
     WHERE user_id = 2 AND event_type = 'issue_created'
   );
   ```

### Issue 5: Browser Shows Success but Database Not Updated

**Symptom**: Green success message, but preferences not saved in database

**Solution**:
1. Check if there's a transaction issue:
   ```sql
   SELECT * FROM notification_preferences WHERE user_id = 2;
   ```
2. Verify the unique index exists:
   ```sql
   SHOW INDEXES FROM notification_preferences;
   ```
   Should show: `notification_preferences_user_event_unique` on (user_id, event_type)

3. Test the SQL directly:
   ```sql
   INSERT INTO notification_preferences (user_id, event_type, in_app, email, push)
   VALUES (2, 'issue_created', 1, 0, 1)
   ON DUPLICATE KEY UPDATE in_app=1, email=0, push=1;
   
   SELECT * FROM notification_preferences WHERE user_id = 2 AND event_type = 'issue_created';
   ```

---

## Step 4: Manual Verification

Once you've solved the issue, verify manually:

### Database Check
```sql
-- Check that preferences are saved
SELECT * FROM notification_preferences WHERE user_id = 2 LIMIT 5;

-- Should show entries like:
-- id | user_id | event_type | in_app | email | push | created_at | updated_at
-- 1  |    2    | issue_created |   1   |   0   |  1   |  ...       | ... (recent)
```

### UI Check
1. Go to `/profile/notifications`
2. Modify some preferences
3. Click "Save Preferences"
4. Should see green success message
5. Refresh the page
6. Preferences should still be as you saved them

---

## Files Modified (December 8, 2025)

1. **src/Core/Database.php**
   - Added `insertOrUpdate()` method (lines 205-241)
   - Implements MySQL `INSERT ... ON DUPLICATE KEY UPDATE`

2. **src/Controllers/NotificationController.php**
   - Enhanced `updatePreferences()` with try-catch error handling
   - Better error messages in JSON response
   - Count updated preferences

3. **views/profile/notifications.php**
   - Enhanced JavaScript error handling
   - Console logging for debugging
   - Better error message parsing

---

## Quick Checklist

- [ ] `Database::insertOrUpdate()` method exists in `src/Core/Database.php`
- [ ] `notification_preferences` table exists
- [ ] User has preferences initialized in database
- [ ] No errors in browser console (check DevTools)
- [ ] API returns `"status": "success"`
- [ ] Database shows updated values after save
- [ ] Page refresh shows saved preferences

---

## Getting Help

If you still have issues after following this guide:

1. **Check server logs**: `storage/logs/`
2. **Run debug page**: Visit the debug-notification-api.php page
3. **Check browser console**: F12 → Console tab
4. **Test API directly**: Use curl with the example above
5. **Verify database**: Use the SQL queries in this guide

---

## References

- [NOTIFICATION_PREFERENCES_FIX.md](NOTIFICATION_PREFERENCES_FIX.md) - Technical details of the fix
- [NOTIFICATION_SYSTEM_COMPLETE.md](NOTIFICATION_SYSTEM_COMPLETE.md) - Full notification system docs
- [AGENTS.md](AGENTS.md) - Code standards and patterns
