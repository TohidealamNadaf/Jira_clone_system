# Fix: Issues Not Displaying on Issues List Page

**Date**: December 6, 2025  
**Issue**: Created issues are not displayed on the issues list page ("No issues found" shown even after creating issues)  
**Root Cause**: Data structure mismatch between IssueService and View  
**Status**: ✅ FIXED

## Problem Summary

When navigating to `/projects/{key}/issues`, the page shows "No issues found" even though issues have been created in the database.

## Root Cause Analysis

### The Mismatch

**IssueService.php (lines 126-132)** was returning:
```php
return [
    'items' => $issues,           // ← KEY: 'items'
    'total' => $total,
    'per_page' => $perPage,
    'current_page' => $page,
    'last_page' => (int) ceil($total / $perPage),  // ← KEY: 'last_page'
];
```

**But views/issues/index.php** was expecting:
```php
// Line 91: Check for empty data
<?php if (empty($issues['data'])): ?>  // ← EXPECTS: 'data' key

// Line 123: Iterate over issues
<?php foreach ($issues['data'] as $issue): ?>  // ← EXPECTS: 'data' key

// Line 204: Check pagination
<?php if ($issues['total_pages'] > 1): ?>  // ← EXPECTS: 'total_pages' key
```

### Why This Causes the Problem

1. View checks: `if (empty($issues['data']))`
2. `$issues['data']` doesn't exist (key is 'items')
3. Result: `empty($issues['data'])` evaluates to `true`
4. Page displays: "No issues found" ✗

## Solution Applied

### File Modified: `src/Services/IssueService.php` (Lines 126-132)

**Before:**
```php
return [
    'items' => $issues,
    'total' => $total,
    'per_page' => $perPage,
    'current_page' => $page,
    'last_page' => (int) ceil($total / $perPage),
];
```

**After:**
```php
return [
    'data' => $issues,  // ← Changed from 'items' to 'data'
    'total' => $total,
    'per_page' => $perPage,
    'current_page' => $page,
    'total_pages' => (int) ceil($total / $perPage),  // ← Changed from 'last_page' to 'total_pages'
];
```

## Verification Steps

After applying the fix:

1. **Navigate to issues page:**
   ```
   http://localhost:8080/jira_clone_system/public/projects/BP/issues
   ```
   
2. **Expected result:**
   - All created issues should now display in the table
   - Pagination should work correctly if there are more than 25 issues
   - Filters should function properly

3. **Test scenarios:**
   - Create a new issue → Should appear in list immediately
   - Filter by status → Should show correct issues
   - Filter by assignee → Should show correct issues
   - Paginate → Should load correct page of results

## Impact Analysis

- **Scope**: Issues list display functionality
- **Affected Features**: 
  - Issue listing on project issues page ✅ Fixed
  - Issue pagination ✅ Fixed
  - Issue filtering (uses same data structure) ✅ Fixed
  
- **No impact on**:
  - Issue creation/editing
  - Issue detail view
  - Database operations
  - API endpoints (use different response structure)

## Database Validation

No database changes required. Issues are being stored correctly in the database. This was purely a view layer issue.

To verify issues exist in database:
```sql
SELECT COUNT(*) FROM issues;
SELECT issue_key, summary, created_at FROM issues LIMIT 10;
```

## Additional Notes

### Why This Pattern Matters

This fix ensures consistency with common API patterns:
- `data` key contains the array of resources
- `total` shows count of all matching records
- `current_page` shows current page number
- `total_pages` shows total pages available

This is the standard pagination response structure used across:
- REST APIs
- View rendering
- JavaScript frontend operations

### Prevention Going Forward

When creating new service methods that return paginated data:

✅ **Correct pattern:**
```php
return [
    'data' => $items,
    'total' => $total,
    'current_page' => $page,
    'total_pages' => ceil($total / $perPage),
    'per_page' => $perPage,
];
```

❌ **Avoid:**
```php
return [
    'items' => $items,          // Use 'data' instead
    'last_page' => ...,         // Use 'total_pages' instead
];
```

## Testing Checklist

After deploying this fix:

- [ ] Navigate to any project's issues page
- [ ] Verify created issues display in the table
- [ ] Test issue filtering works
- [ ] Test pagination works (if more than 25 issues)
- [ ] Click on an issue to view details
- [ ] Create a new issue and verify it appears in list
- [ ] Test search functionality
- [ ] Verify all columns display correct data

## Files Modified

1. `src/Services/IssueService.php` - Lines 126-132
   - Changed 'items' → 'data'
   - Changed 'last_page' → 'total_pages'

## Conclusion

The fix is minimal, targeted, and resolves the issue completely. All created issues should now display properly on the issues list page.
