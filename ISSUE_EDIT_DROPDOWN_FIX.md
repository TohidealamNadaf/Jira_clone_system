# Fix: Empty Dropdowns on Issue Edit Page

**Date**: December 6, 2025  
**Issue**: Issue Type, Assignee, and Labels dropdowns are empty on the issue edit page  
**Status**: ✅ FIXED

## Problem

When navigating to edit an issue (`/issue/BP-7/edit`), the following dropdowns were empty:
- Issue Type - showed only placeholder "Select Type..."
- Assignee - showed only placeholder
- Labels - showed no options

## Root Cause

The `IssueController.edit()` method was not passing the required dropdown data to the view:

**Missing data variables:**
- `$issueTypes` - List of issue types
- `$projectMembers` - List of team members for assignee dropdown
- `$labels` - List of labels for the project

### What Was Passed

```php
return $this->view('issues.edit', [
    'issue' => $issue,
    'project' => $project,
    'priorities' => $this->issueService->getPriorities(), // ✓ Only this was passed
]);
```

### What the View Expected

The view (`views/issues/edit.php`) expected:
```php
// Line 34: Issue Types dropdown
<?php foreach ($issueTypes ?? [] as $type): ?>

// Line 74: Assignee dropdown  
<?php foreach ($projectMembers ?? [] as $member): ?>

// Line 86: Labels dropdown
<?php foreach ($labels ?? [] as $label): ?>
```

## Solution

Updated `IssueController.edit()` method to pass all required dropdown data:

### File: `src/Controllers/IssueController.php` (Lines 196-214)

**Before:**
```php
return $this->view('issues.edit', [
    'issue' => $issue,
    'project' => $project,
    'priorities' => $this->issueService->getPriorities(),
]);
```

**After:**
```php
return $this->view('issues.edit', [
    'issue' => $issue,
    'project' => $project,
    'issueTypes' => $this->issueService->getIssueTypes(),        // ✅ Added
    'priorities' => $this->issueService->getPriorities(),
    'projectMembers' => $this->projectService->getProjectMembers($issue['project_id']),  // ✅ Added
    'labels' => $project['labels'] ?? [],                        // ✅ Added
]);
```

### Bonus: Fixed Create Page Too

Also updated `IssueController.create()` to include `issueTypes`:

**Before:**
```php
return $this->view('issues.create', [
    'project' => $project,
    'projects' => $this->issueService->getProjects(),
    'priorities' => $this->issueService->getPriorities(),
]);
```

**After:**
```php
return $this->view('issues.create', [
    'project' => $project,
    'projects' => $this->issueService->getProjects(),
    'issueTypes' => $this->issueService->getIssueTypes(),        // ✅ Added
    'priorities' => $this->issueService->getPriorities(),
]);
```

## Data Sources

The fix pulls data from two sources:

### 1. IssueService Methods
```php
$this->issueService->getIssueTypes()      // Gets all non-subtask issue types
$this->issueService->getPriorities()      // Gets all priority levels
```

### 2. ProjectService
```php
$this->projectService->getProjectMembers($issue['project_id'])  // Gets project team members
```

### 3. Project Details
```php
$project['labels'] ?? []  // Labels are already loaded by getProjectWithDetails()
```

## Files Modified

1. `src/Controllers/IssueController.php`
   - Lines 106-110: `create()` method - Added `$issueTypes`
   - Lines 208-215: `edit()` method - Added `$issueTypes`, `$projectMembers`, `$labels`

## Verification Steps

### Test Issue Edit Page

1. **Navigate to edit page:**
   ```
   http://localhost:8080/jira_clone_system/public/issue/BP-7/edit
   ```

2. **Verify dropdowns are populated:**
   - Issue Type dropdown - Shows: Story, Task, Bug, Epic, Sub-task
   - Priority dropdown - Shows: Highest, High, Medium, Low, Lowest
   - Assignee dropdown - Shows: Team member names + "Unassigned"
   - Labels dropdown - Shows: Available labels for the project

3. **Test functionality:**
   - Select a different issue type - Should update
   - Select an assignee - Should show in team member list
   - Select labels - Should support multiple selection
   - Click save - Should persist changes

### Test Issue Create Page

1. **Navigate to create page:**
   ```
   http://localhost:8080/jira_clone_system/public/projects/BP/issues/create
   ```

2. **Verify Issue Type dropdown is populated**
   - Should show all available issue types

## Impact

- **Scope**: Issue creation and editing functionality
- **Affected Pages**:
  - `/issue/{key}/edit` - Issue edit page ✅ Fixed
  - `/projects/{key}/issues/create` - Issue create page ✅ Fixed

- **User Experience**:
  - Users can now properly select issue types when editing
  - Users can now assign issues to team members
  - Users can now add labels to issues
  - Form remains pre-filled with current values

## Database Verification

No database changes. All data is queried from existing tables:
- `issue_types` - Issue type definitions
- `issue_priorities` - Priority levels
- `project_members` - Team member assignments
- `labels` - Issue labels

## Testing Checklist

After deploying this fix:

- [ ] Navigate to issue edit page
- [ ] Verify Issue Type dropdown shows all types
- [ ] Verify Priority dropdown shows all priorities
- [ ] Verify Assignee dropdown shows team members
- [ ] Verify Labels dropdown shows project labels
- [ ] Change issue type and save
- [ ] Change assignee and save
- [ ] Add labels and save
- [ ] Verify edit page still displays existing values as pre-selected
- [ ] Test on create page - Issue Type dropdown works

## Summary

This was a straightforward case of missing data variables in the controller. The `edit()` method was loading the `$project` object which contains all the necessary data through `getProjectWithDetails()`, but wasn't explicitly passing `issueTypes`, `projectMembers`, and `labels` to the view.

The fix ensures all dropdown data is available, providing a complete editing experience for users.
