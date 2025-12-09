# Parameter Binding Fix - Corrected

**Date**: December 6, 2025  
**Status**: ✅ CORRECTED

## Issue Found

After initial fixes, discovered that `Database::delete()` and `Database::update()` methods have special handling - they expect **positional parameters** (`?`) in the WHERE clause and convert them internally to named parameters.

## Root Cause

The Database class has logic to auto-convert positional `?` to named `:where_0`, `:where_1`, etc. in the WHERE clause. Using named parameters directly bypassed this conversion and caused parameter binding errors.

## Understanding the Database Class

### Methods That Need Positional Parameters in WHERE:
- `Database::update()` - WHERE clause must use `?`
- `Database::delete()` - WHERE clause must use `?`

### Methods That Need Named Parameters:
- `Database::select()` - Use `:name` in SQL
- `Database::selectOne()` - Use `:name` in SQL
- `Database::selectValue()` - Use `:name` in SQL
- `Database::query()` - Use `:name` in SQL
- `Database::insert()` - Auto-generates `:column` placeholders

## Corrections Applied

### Pattern for DELETE:
```php
// Correct
Database::delete('table', 'id = ?', [$id])

// Wrong (what we initially changed to)
Database::delete('table', 'id = :id', [':id' => $id])
```

### Pattern for UPDATE:
```php
// Correct
Database::update('table', $data, 'id = ?', [$id])

// Wrong (what we initially changed to)
Database::update('table', $data, 'id = :id', [':id' => $id])
```

### Pattern for SELECT (Correct):
```php
// Correct
Database::select("SELECT * FROM table WHERE id = :id", [':id' => $id])
```

## Files Corrected

### 1. src/Controllers/CommentController.php
- Line 86: `UPDATE issues` - Changed back to positional `?`
- Line 138: `UPDATE comments` - Changed back to positional `?`
- Line 193: `DELETE comments` - Already fixed to positional `?`

### 2. src/Services/IssueService.php
- Line 361: `UPDATE issues` - Changed back to positional `?`
- Line 391: `DELETE issues` - Changed back to positional `?`
- Line 418: `UPDATE issues` - Changed back to positional `?`
- Line 437: `UPDATE issues` - Changed back to positional `?`
- Line 564: `DELETE issue_links` - Changed back to positional `?`
- Line 631: `UPDATE issues` - Changed back to positional `?`
- Line 720: `DELETE issue_labels` - Changed back to positional `?`
- Line 746: `DELETE issue_components` - Changed back to positional `?`
- Line 758: `DELETE issue_versions` - Changed back to positional `?`

## Remaining Named Parameter Changes (Correct)

All `SELECT` queries remain with named parameters (`:name` style):
- `Database::select()`
- `Database::selectOne()`
- `Database::selectValue()`

## Testing

### Step 1: Clear Browser Cache
Press: `Ctrl + Shift + Delete` or `Ctrl + F5`

### Step 2: Test Adding Comment
1. Navigate to any issue
2. Add a comment
3. **Expected**: No error, comment appears

### Step 3: Verify Success
- ✅ No `SQLSTATE[HY093]` error
- ✅ No "Failed to add comment" error
- ✅ Comment appears in list

## Summary

**Total Corrections**: 10 methods reverted to positional `?` for WHERE clauses

All other SELECT queries remain with proper named parameter syntax (`:param`).

## Status

✅ **Code**: CORRECTED  
✅ **Ready**: FOR TESTING  

Clear your cache and test adding a comment to verify the fix.
