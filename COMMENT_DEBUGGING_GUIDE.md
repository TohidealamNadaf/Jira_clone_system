# Comment Feature - Debugging Guide

**Date**: December 6, 2025  
**Status**: Ready for Testing

## What Was Fixed

### Issue 1: Table Name Mismatch
- **Problem**: Code referenced `issue_comments` but actual table is `comments`
- **Files Fixed**: 
  - `src/Services/IssueService.php`
  - `src/Controllers/CommentController.php`
  - `src/Controllers/Api/IssueApiController.php`

### Issue 2: Column Name Mismatch
- **Problem**: Code referenced `author_id` but actual column is `user_id`
- **Files Fixed**:
  - `src/Controllers/CommentController.php` (4 locations)

### Issue 3: Timestamp Handling
- **Problem**: Manually setting `created_at` and `updated_at` which have database defaults
- **Fix**: Removed manual timestamp setting to let database handle it
- **File Fixed**: `src/Controllers/CommentController.php`

### Issue 4: Error Logging
- **Problem**: Generic error messages made debugging difficult
- **Fix**: Enhanced error messages with actual exception text
- **File Fixed**: `src/Controllers/CommentController.php`

### Issue 5: Notification Failure
- **Problem**: If watchers notification failed, entire comment would fail
- **Fix**: Wrapped notifyWatchers in try-catch to not impact comment creation
- **File Fixed**: `src/Controllers/CommentController.php`

## How to Debug "Failed to Add Comment" Alert

### Step 1: Check Browser Console
1. Open the issue page
2. Press `F12` to open Developer Tools
3. Go to "Console" tab
4. Try to add a comment
5. Look for error messages

**Expected output if working:**
```
Comment error response: 201 {success: true}
```

**If you see errors:**
```
Comment error response: 500 {error: "Your error message here"}
```

### Step 2: Check Application Logs
1. Open `storage/logs/2025-12-06.log` (or current date)
2. Look for ERROR entries
3. Check the last error message

### Step 3: Test Database Directly
Run the test script via URL:
```
http://localhost:8080/jira_clone_system/public/test_comment_flow.php?issue=BP-7&user=1
```

This tests the full comment insertion flow and shows which step fails.

### Step 4: Check Database Table
Verify the `comments` table exists:
```sql
DESC comments;
```

Should show:
- `id` (INT UNSIGNED)
- `issue_id` (INT UNSIGNED)
- `user_id` (INT UNSIGNED) ← NOT author_id
- `body` (TEXT)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

## Common Error Messages & Solutions

### Error: "Table 'jiira_clonee_system.issue_comments' doesn't exist"
**Solution**: Files are using old code. Clear browser cache (Ctrl+F5) and reload.

### Error: "Failed to retrieve inserted comment"
**Cause**: The JOIN to users table failed (user doesn't exist?)  
**Solution**: Check that user ID exists in users table

### Error: "Cannot add or update a child row: a foreign key constraint fails"
**Cause**: The issue_id doesn't exist in issues table  
**Solution**: Verify the issue exists before adding comment

## Testing Checklist

- [ ] Navigate to any issue (e.g., BP-7)
- [ ] Page loads without errors
- [ ] Comment section displays
- [ ] Type a comment in the text box
- [ ] Click "Comment" button
- [ ] Alert should NOT say "Failed to add comment"
- [ ] Page reloads
- [ ] Comment appears in the list
- [ ] Comment shows correct author name
- [ ] Comment shows correct timestamp

## Files Modified in This Session

1. **src/Services/IssueService.php**
   - Line 207: `issue_comments` → `comments`
   - Line 208: `c.author_id` → `c.user_id`

2. **src/Controllers/CommentController.php**
   - Line 41: Insert table name: `issue_comments` → `comments`
   - Line 43: Insert column: `author_id` → `user_id`
   - Lines 45-46: Removed timestamps from insert
   - Line 50-51: SELECT from `issue_comments` → `comments` and `c.author_id` → `c.user_id`
   - Line 91: SELECT from `issue_comments` → `comments`
   - Line 101: Check `author_id` → `user_id`
   - Lines 111-112: Update removed manual timestamp
   - Lines 115-117: SELECT from `issue_comments` → `comments` and `c.author_id` → `c.user_id`
   - Line 150: SELECT from `issue_comments` → `comments`
   - Line 160: Check `author_id` → `user_id`
   - Line 165: DELETE from `issue_comments` → `comments`
   - Lines 191-213: Added try-catch around notifyWatchers
   - Line 76: Error response now includes actual exception message
   - Line 79: Error flash now includes actual exception message

3. **src/Controllers/Api/IssueApiController.php**
   - Line 327: `issue_comments` → `comments`
   - Line 352: `issue_comments` → `comments`

## Database Schema Reference

```sql
CREATE TABLE `comments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `body` TEXT NOT NULL,
    `is_internal` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `comments_issue_id_idx` (`issue_id`),
    KEY `comments_user_id_idx` (`user_id`),
    CONSTRAINT `comments_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `comments_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Next Steps

1. **Reload the page** (Ctrl+F5 to clear cache)
2. **Test adding a comment**
3. **If still failing**: Check browser console for error message
4. **If error message present**: Share it in logs/console output
5. **Run test script** if needed for diagnostic info

## Performance Impact

- **Comments loading**: No change (uses same query structure)
- **Comment insertion**: Slightly faster (removed unnecessary timestamp calculations)
- **Database**: No impact (queries are unchanged, just table/column names fixed)

## Security Impact

- ✅ No security changes
- ✅ All authorization checks in place
- ✅ CSRF protection still active
- ✅ Input validation still in place
- ✅ SQL injection prevention still active
