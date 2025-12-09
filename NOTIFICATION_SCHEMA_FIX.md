# Fix Applied: Notification Schema Mismatch

**Date**: December 6, 2025  
**Issue**: SQLSTATE[HY093]: Invalid parameter number  
**Status**: ✅ FIXED

## Root Cause

The `notifyWatchers()` method in CommentController was inserting data into the `notifications` table with the wrong column names and structure. This caused a parameter mismatch error in the Database class.

## Schema Mismatch Details

### What the Code Was Trying to Insert:
```php
Database::insert('notifications', [
    'user_id' => $watcher['user_id'],
    'type' => $type,
    'issue_id' => $issue['id'],           // ✗ DOESN'T EXIST
    'project_id' => $issue['project_id'], // ✗ DOESN'T EXIST
    'actor_id' => $this->userId(),        // ✗ DOESN'T EXIST
    'data' => json_encode([...]),
]);
```

### What the notifications Table Actually Has:
```sql
CREATE TABLE `notifications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `type` VARCHAR(100) NOT NULL,
    `notifiable_type` VARCHAR(50) NOT NULL,   -- ✓ REQUIRED
    `notifiable_id` INT UNSIGNED NOT NULL,    -- ✓ REQUIRED
    `data` JSON DEFAULT NULL,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## The Fix

**File**: `src/Controllers/CommentController.php` (Lines 189-214)

### Before (Wrong):
```php
Database::insert('notifications', [
    'user_id' => $watcher['user_id'],
    'type' => $type,
    'issue_id' => $issue['id'],              // Wrong column
    'project_id' => $issue['project_id'],    // Wrong column
    'actor_id' => $this->userId(),           // Wrong column
    'data' => json_encode([
        'issue_key' => $issue['issue_key'],
        'comment_id' => $comment['id'],
    ]),
]);
```

### After (Correct):
```php
Database::insert('notifications', [
    'user_id' => $watcher['user_id'],
    'type' => $type,
    'notifiable_type' => 'issue',     // ✓ Correct: 'issue' type
    'notifiable_id' => $issue['id'],  // ✓ Correct: the issue ID
    'data' => json_encode([
        'issue_key' => $issue['issue_key'],
        'comment_id' => $comment['id'],
        'actor_id' => $this->userId(),  // ✓ Moved to data JSON
    ]),
]);
```

## What Changed

| Item | Before | After |
|------|--------|-------|
| `issue_id` | Direct column | Removed |
| `project_id` | Direct column | Removed |
| `actor_id` | Direct column | Moved to `data` JSON |
| `notifiable_type` | Missing | Added: `'issue'` |
| `notifiable_id` | Missing | Added: `$issue['id']` |

## Why This Matters

The `notifications` table uses a **polymorphic relationship** pattern:
- `notifiable_type`: The type of thing being notified about ('issue', 'project', 'comment', etc.)
- `notifiable_id`: The ID of that thing

This is a common pattern for storing flexible notifications about different entity types.

## Related Metadata

Now stored in the `data` JSON field:
- `issue_key` - The issue key (e.g., 'BP-7')
- `comment_id` - The comment ID
- `actor_id` - Who made the change

These become queryable via JSON if needed: `SELECT * FROM notifications WHERE JSON_EXTRACT(data, '$.actor_id') = 1`

## Error Resolution

**Before**: `SQLSTATE[HY093]: Invalid parameter number`
- Caused by: Trying to insert into non-existent columns
- Result: Comment insert would fail with cryptic error

**After**: Comment insert succeeds, notification is created with proper schema

## Affected Functionality

### Comment Creation (store method)
- ✅ Comment inserted into `comments` table
- ✅ Notification sent to watchers (if any)
- ✅ Issue updated with new timestamp

### Error Handling
- ✅ If notification fails, comment still saves
- ✅ Error is logged but doesn't block the operation

## Testing

### To Verify the Fix:
1. Clear browser cache: `Ctrl + F5`
2. Navigate to issue: `BP-7`
3. Add a comment
4. **Should NOT see**: "Failed to add comment" alert
5. **Should see**: Comment appears on page

### Check Logs
Look in `storage/logs/2025-12-06.log`:
- Should NOT see: `SQLSTATE[HY093]`
- Should see (if watchers exist): `Failed to notify watchers` only if that operation fails

## Database Alignment Summary

The notification system now correctly aligns with:

```
notifications table structure:
├── id (auto-increment)
├── user_id (INT) - who gets notified
├── type (VARCHAR) - 'comment_added', 'issue_updated', etc.
├── notifiable_type (VARCHAR) - 'issue', 'project', 'comment'
├── notifiable_id (INT) - the ID of the thing being notified about
├── data (JSON) - additional context/metadata
├── read_at (TIMESTAMP, nullable)
└── created_at (TIMESTAMP, auto-set)
```

## Code Quality

✅ **Schema Alignment** - Code matches actual database schema  
✅ **Polymorphic Pattern** - Uses proper notifiable_type/notifiable_id pattern  
✅ **Error Resilience** - Notification failures don't block comments  
✅ **Metadata Storage** - Related data properly stored in JSON  

## Files Modified

- `src/Controllers/CommentController.php` (Lines 189-214, notifyWatchers method)

## Summary

Fixed a critical parameter mismatch in the notification system by aligning the insert operation with the actual notifications table schema. Comments can now be created without errors.
