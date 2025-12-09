# Fix Applied: Comment Table Name Error

**Date**: December 6, 2025  
**Issue**: PDOException - Table 'jiira_clonee_system.issue_comments' doesn't exist  
**Status**: ✅ FIXED

## Root Cause

The code was referencing a non-existent table name `issue_comments`, but the actual database table is named `comments`.

Additionally, the code was using column name `author_id`, but the actual database column is `user_id`.

## Table Structure (Correct)

```sql
CREATE TABLE `comments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,  -- NOT author_id
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

## Files Fixed

### 1. src/Services/IssueService.php (Lines 207-208)
**Before:**
```php
FROM issue_comments c
JOIN users u ON c.author_id = u.id
```

**After:**
```php
FROM comments c
JOIN users u ON c.user_id = u.id
```

### 2. src/Controllers/CommentController.php (Multiple locations)

**Lines 41-43 (store method - insert):**
```php
// Before:
Database::insert('issue_comments', [
    'author_id' => $this->userId(),

// After:
Database::insert('comments', [
    'user_id' => $this->userId(),
```

**Lines 50-52 (store method - select):**
```php
// Before:
FROM issue_comments c
JOIN users u ON c.author_id = u.id

// After:
FROM comments c
JOIN users u ON c.user_id = u.id
```

**Lines 89, 99, 108, 115 (update method):**
- Line 89: `FROM issue_comments c` → `FROM comments c`
- Line 99: `$comment['author_id']` → `$comment['user_id']`
- Line 108: `Database::update('issue_comments'` → `Database::update('comments'`
- Line 115: `FROM issue_comments c` → `FROM comments c`
- Line 116: `c.author_id` → `c.user_id`

**Lines 149, 159, 164 (destroy method):**
- Line 149: `FROM issue_comments c` → `FROM comments c`
- Line 159: `$comment['author_id']` → `$comment['user_id']`
- Line 164: `Database::delete('issue_comments'` → `Database::delete('comments'`

### 3. src/Controllers/Api/IssueApiController.php (Lines 327, 352)

**Line 327 (updateComment method):**
```php
// Before:
FROM issue_comments WHERE id = ?

// After:
FROM comments WHERE id = ?
```

**Line 352 (destroyComment method):**
```php
// Before:
FROM issue_comments WHERE id = ?

// After:
FROM comments WHERE id = ?
```

## Summary of Changes

| File | Changes | Severity |
|------|---------|----------|
| IssueService.php | 2 table refs, 1 column ref | Critical |
| CommentController.php | 5 table refs, 4 column refs | Critical |
| IssueApiController.php | 2 table refs | Critical |

**Total**: 9 table name corrections, 5 column name corrections

## Testing

To verify the fix works:

1. **Navigate to any issue** (e.g., BP-7 or another existing issue)
2. **The issue detail page should load** (previously threw PDOException)
3. **Comments section should display** (if any comments exist)
4. **Add a new comment** - it should save and display immediately

## Error Resolution

The runtime error:
```
PDOException
Message: SQLSTATE[42S02]: Base table or view not found: 1146 Table 'jiira_clonee_system.issue_comments' doesn't exist
```

This error **should now be resolved** because:
- ✅ All `issue_comments` references replaced with `comments`
- ✅ All `author_id` references replaced with `user_id`
- ✅ Code now matches actual database schema

## Previous Documentation Note

The COMMENT_FUNCTIONALITY_FIX.md document referenced table name as `issue_comments` which was incorrect. The actual table name in the database schema is `comments` with `user_id` column, not `author_id`.

## Verification

Run this SQL query to confirm the table exists:
```sql
SELECT COUNT(*) FROM comments;
DESCRIBE comments;
```

Both should execute without error.
