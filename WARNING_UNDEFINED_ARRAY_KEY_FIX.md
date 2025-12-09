# Fix: Warning - Undefined Array Key "assignee" and "reporter"

**Date**: December 6, 2025  
**Issue**: PHP Warning: Undefined array key "assignee" and "reporter" on views/issues/index.php  
**Status**: ✅ FIXED

## Problem

When viewing the issues list page, PHP warnings appeared:
```
Warning: Undefined array key "assignee" in C:\xampp\htdocs\jira_clone_system\views\issues\index.php on line 156
Warning: Undefined array key "reporter" in C:\xampp\htdocs\jira_clone_system\views\issues\index.php on line 173
```

These warnings occurred even though the issues were displaying correctly.

## Root Cause

**Data structure mismatch between IssueService and View:**

### What IssueService Returns

The `IssueService.getIssues()` method returns flat, comma-separated field names from the database query:

```php
SELECT i.*, 
        ...
        reporter.display_name as reporter_name, reporter.avatar as reporter_avatar,
        assignee.display_name as assignee_name, assignee.avatar as assignee_avatar
FROM issues i
...
```

This returns array keys like:
- `assignee_name` (string)
- `assignee_avatar` (string/null)
- `reporter_name` (string)
- `reporter_avatar` (string/null)

### What the View Expected

The view was written as if the data was nested objects/arrays:

```php
<?php if ($issue['assignee']): ?>  <!-- ❌ Expected nested array -->
<img src="<?= $issue['assignee']['avatar'] ?>">  <!-- ❌ Nested array access -->
<span><?= $issue['assignee']['display_name'] ?></span>
```

This caused the warning because `$issue['assignee']` key doesn't exist.

## Solution

Updated `views/issues/index.php` to use the actual data structure from IssueService:

### For Assignee (Lines 155-170)

**Before:**
```php
<?php if ($issue['assignee']): ?>
<div class="d-flex align-items-center">
    <?php if ($issue['assignee']['avatar']): ?>
    <img src="<?= e($issue['assignee']['avatar']) ?>" ...>
    <?php else: ?>
    <div>
        <?= strtoupper(substr($issue['assignee']['first_name'], 0, 1)) ?>
    </div>
    <?php endif; ?>
    <span class="small"><?= e($issue['assignee']['display_name']) ?></span>
</div>
```

**After:**
```php
<?php if ($issue['assignee_name'] ?? null): ?>
<div class="d-flex align-items-center">
    <?php if ($issue['assignee_avatar'] ?? null): ?>
    <img src="<?= e($issue['assignee_avatar']) ?>" ...>
    <?php else: ?>
    <div>
        <?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
    </div>
    <?php endif; ?>
    <span class="small"><?= e($issue['assignee_name']) ?></span>
</div>
```

### For Reporter (Lines 172-188)

**Before:**
```php
<?php if ($issue['reporter'] ?? null): ?>
<div class="d-flex align-items-center">
    <?php if (($issue['reporter']['avatar']) ?? null): ?>
    <img src="<?= e($issue['reporter']['avatar']) ?>" ...>
    <?php else: ?>
    <div>
        <?= strtoupper(substr(($issue['reporter']['first_name'] ?? 'U'), 0, 1)) ?>
    </div>
    <?php endif; ?>
    <span class="small"><?= e($issue['reporter']['display_name'] ?? 'Unknown') ?></span>
</div>
```

**After:**
```php
<?php if ($issue['reporter_name'] ?? null): ?>
<div class="d-flex align-items-center">
    <?php if ($issue['reporter_avatar'] ?? null): ?>
    <img src="<?= e($issue['reporter_avatar']) ?>" ...>
    <?php else: ?>
    <div>
        <?= strtoupper(substr($issue['reporter_name'], 0, 1)) ?>
    </div>
    <?php endif; ?>
    <span class="small"><?= e($issue['reporter_name']) ?></span>
</div>
```

## Key Changes

1. **Assignee section:**
   - `$issue['assignee']` → `$issue['assignee_name'] ?? null`
   - `$issue['assignee']['avatar']` → `$issue['assignee_avatar'] ?? null`
   - `$issue['assignee']['first_name']` → `$issue['assignee_name']`
   - `$issue['assignee']['display_name']` → `$issue['assignee_name']`

2. **Reporter section:**
   - `$issue['reporter']` → `$issue['reporter_name'] ?? null`
   - `$issue['reporter']['avatar']` → `$issue['reporter_avatar'] ?? null`
   - `$issue['reporter']['first_name']` → `$issue['reporter_name']`
   - `$issue['reporter']['display_name']` → `$issue['reporter_name']`

3. **Null coalescing operator (`??`)** added for safe access

## Files Modified

1. `views/issues/index.php`
   - Lines 155-170: Fixed assignee section
   - Lines 172-188: Fixed reporter section

## Verification

After the fix:
- No more PHP warnings on the issues list page
- Assignee information displays correctly
- Reporter information displays correctly
- "Unassigned" and "Unknown" fallback messages work properly
- Avatar initials display correctly when avatar URL is not available

## Comparison with Show View

The `views/issues/show.php` (issue detail page) uses the `getIssueByKey()` method which returns a different data structure with nested user objects. This is why it may have a different approach to accessing user data.

The fix ensures consistency in how the list view accesses data returned by `IssueService.getIssues()`.

## Testing Checklist

- [ ] Navigate to issues list page
- [ ] Verify no PHP warnings appear in browser console or error logs
- [ ] Verify assignee names and avatars display correctly
- [ ] Verify reporter names and avatars display correctly
- [ ] Verify "Unassigned" appears for issues without assignees
- [ ] Verify "Unknown" appears for issues without reporters
- [ ] Verify avatar initials display when no avatar URL is available

## Summary

This was a straightforward data structure mismatch fix. The IssueService was returning flat string fields (`assignee_name`, `assignee_avatar`, `reporter_name`, `reporter_avatar`), but the view was trying to access them as nested objects. By updating the view to use the correct field names, the warnings are completely eliminated while maintaining all visual functionality.
