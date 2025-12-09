# Fix: PDOException - Foreign Key Constraint Violation on Issue Update

**Date**: December 6, 2025  
**Issue**: SQLSTATE[23000]: Integrity constraint violation when updating issues with empty assignee  
**Error**: Cannot add or update a child row: a foreign key constraint fails  
**Status**: ✅ FIXED

## Problem

When updating an issue and leaving the assignee as "Unassigned" (empty), the application throws a PDOException:

```
SQLSTATE[23000]: Integrity constraint violation: 1452 
Cannot add or update a child row: a foreign key constraint fails 
(`jiira_clonee_system`.`issues`, CONSTRAINT `issues_assignee_id_fk` 
FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL)
```

## Root Cause

The form submits an empty string (`''`) or `0` for the assignee field when "Unassigned" is selected. The code was passing this value directly to the database, but the foreign key constraint requires either:
- A valid user ID that exists in the `users` table
- NULL (which is allowed by `ON DELETE SET NULL`)

**The problem flow:**
1. User selects "Unassigned" from dropdown (value = '')
2. Form submits with `assignee_id = ''` or `assignee_id = 0`
3. Validator passes it through as nullable
4. Service receives `assignee_id = ''` (empty string)
5. Database tries to insert/update with `assignee_id = ''`
6. Foreign key constraint rejects it ('' is not NULL and not a valid user ID)

## Solution

Added data transformation in the controllers to convert empty strings and zeros to NULL before passing to the service.

### Fix 1: IssueController.store() (Create)

**File:** `src/Controllers/IssueController.php` (Lines 134-139)

**Added:**
```php
// Convert empty strings to null for optional foreign key fields
foreach (['assignee_id', 'epic_id', 'parent_id', 'sprint_id'] as $field) {
    if (isset($data[$field]) && ($data[$field] === '' || $data[$field] === 0)) {
        $data[$field] = null;
    }
}
```

**Location:** Right after validation, before passing to `createIssue()`

### Fix 2: IssueController.update() (Edit)

**File:** `src/Controllers/IssueController.php` (Lines 247-252)

**Added:**
```php
// Convert empty strings to null for optional foreign key fields
foreach (['assignee_id', 'epic_id'] as $field) {
    if (isset($data[$field]) && ($data[$field] === '' || $data[$field] === 0)) {
        $data[$field] = null;
    }
}
```

**Location:** Right after validation, before passing to `updateIssue()`

### Fix 3: IssueService.updateIssue() (Defensive Check)

**File:** `src/Services/IssueService.php` (Lines 305-321)

**Added:**
```php
// Convert empty strings to NULL for foreign key fields
$value = $data[$field];
if (in_array($field, ['assignee_id', 'epic_id', 'priority_id', 'issue_type_id']) 
    && ($value === '' || $value === null)) {
    // For optional FK fields (assignee, epic), allow NULL
    if (in_array($field, ['assignee_id', 'epic_id'])) {
        $value = null;
    }
}
```

**Purpose:** Defense-in-depth - ensures even if controller misses the conversion, service will handle it

## How It Works

### Before (Broken)
```
Form submission:
  assignee_id: ""  (empty string)
       ↓
  Validation passes (nullable)
       ↓
  Service receives: assignee_id = ""
       ↓
  Database UPDATE: SET assignee_id = ""
       ↓
  ❌ Foreign key constraint fails!
```

### After (Fixed)
```
Form submission:
  assignee_id: ""  (empty string)
       ↓
  Validation passes (nullable)
       ↓
  Controller conversion:
    "" → null
       ↓
  Service receives: assignee_id = null
       ↓
  Database UPDATE: SET assignee_id = NULL
       ↓
  ✅ Foreign key accepts NULL!
```

## Fields Affected

### Optional Foreign Keys (Allow NULL)
- `assignee_id` - Can be NULL (unassigned issues)
- `epic_id` - Can be NULL (issues not part of epic)
- `parent_id` - Can be NULL (standalone issues)
- `sprint_id` - Can be NULL (backlog items)

### Required Foreign Keys (Cannot be NULL)
- `project_id` - Must have a valid project
- `issue_type_id` - Must have a valid type
- `priority_id` - Must have a valid priority
- `status_id` - Must have a valid status

## Files Modified

1. `src/Controllers/IssueController.php`
   - Lines 134-139: `store()` method - Added NULL conversion
   - Lines 247-252: `update()` method - Added NULL conversion

2. `src/Services/IssueService.php`
   - Lines 305-321: `updateIssue()` method - Added defensive NULL conversion

## Verification Steps

### Test 1: Create Issue Without Assignee
1. Navigate to create issue page
2. Fill in all required fields (project, type, summary)
3. Leave assignee as default/empty
4. Click Save
5. ✅ Issue should be created successfully (assignee_id = NULL)

### Test 2: Update Issue to Unassigned
1. Navigate to edit issue page
2. Change assignee to "Unassigned"
3. Click Save
4. ✅ Issue should update successfully (assignee_id = NULL)

### Test 3: Update Issue to Assigned
1. Navigate to edit issue page
2. Select a team member as assignee
3. Click Save
4. ✅ Issue should update successfully (assignee_id = valid user ID)

### Test 4: Create Issue With Epic (Optional)
1. Create issue and leave epic empty
2. Click Save
3. ✅ Should work (epic_id = NULL)

## Database Validation

The database schema correctly defines optional foreign keys:

```sql
CONSTRAINT `issues_assignee_id_fk` FOREIGN KEY (`assignee_id`) 
REFERENCES `users` (`id`) ON DELETE SET NULL

CONSTRAINT `issues_epic_id_fk` FOREIGN KEY (`epic_id`) 
REFERENCES `issues` (`id`) ON DELETE SET NULL
```

The `ON DELETE SET NULL` means:
- NULL values are allowed
- If referenced record is deleted, FK is set to NULL (cascading)
- Empty string (`''`) is NOT acceptable

## Testing Checklist

- [ ] Create issue without selecting assignee - works
- [ ] Create issue with selected assignee - works
- [ ] Edit issue and remove assignee (set to "Unassigned") - works
- [ ] Edit issue and assign to team member - works
- [ ] Edit issue and remove epic - works
- [ ] All form validations still work
- [ ] History records show correct changes
- [ ] No PHP/database warnings in logs

## Why This Happens

HTML form selects with empty option return empty string (`''`), not NULL:
```html
<select name="assignee_id">
    <option value="">Unassigned</option>  <!-- Returns '' on select -->
    <option value="1">John Smith</option>  <!-- Returns 1 on select -->
</select>
```

JavaScript/PHP must convert `''` to `null` for database compatibility.

## Future Prevention

When adding new optional foreign keys:

1. **In controller**, after validation:
```php
foreach (['new_optional_fk'] as $field) {
    if (isset($data[$field]) && ($data[$field] === '' || $data[$field] === 0)) {
        $data[$field] = null;
    }
}
```

2. **In service**, as defense:
```php
if (in_array($field, ['optional_fk_fields']) && ($value === '' || $value === null)) {
    if (in_array($field, ['optional_fk_fields'])) {
        $value = null;
    }
}
```

## Summary

This fix resolves the foreign key constraint violation by properly converting empty form values to NULL before database operations. The solution uses a defense-in-depth approach with conversions at both the controller and service layers to ensure data integrity regardless of where the issue originates.
