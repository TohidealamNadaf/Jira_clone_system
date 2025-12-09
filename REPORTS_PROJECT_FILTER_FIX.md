# Reports Project Filter Fix

## Problem
The "All Projects" dropdown on the reports page (`/reports`) was not working when a project was selected. The filter was not being propagated to the individual report pages below.

### Root Cause
**Parameter Mismatch**: 
- The **index.php** view was using `project` as the query parameter in the JavaScript
- The controller expected `project_id` as the parameter name
- Additionally, the dropdown was using project `key` instead of project `id` as the value

## Solution Implemented

### 1. Fixed View (views/reports/index.php)
**Changes:**
- Changed dropdown value from `$proj['key']` to `$proj['id']`
- Updated JavaScript to use `project_id` instead of `project` as the query parameter
- Added comparison using `0` instead of empty string for consistency

```php
// Before
<option value="<?= $proj['key'] ?>" <?= ($selectedProject ?? '') === $proj['key'] ? 'selected' : '' ?>>

// After
<option value="<?= $proj['id'] ?>" <?= ($selectedProject ?? 0) == $proj['id'] ? 'selected' : '' ?>>
```

```javascript
// Before
url.searchParams.set('project', project);
url.searchParams.delete('project');

// After
url.searchParams.set('project_id', this.value);
url.searchParams.delete('project_id');
```

### 2. Updated Controller (src/Controllers/ReportController.php)
**Changes:**
- Added `$projectId` parameter handling from request
- Applied project filter to all queries in the `index()` method:
  - `$boards` query - filters by project_id
  - `$activeSprints` query - filters by project_id
  - `$totalIssues` stat calculation
  - `$completedIssues` stat calculation
  - `$inProgressIssues` stat calculation
  - `$avgVelocity` calculation - filters closed sprints by project
- Passes `$selectedProject` to view for pre-selecting the dropdown

### 3. Modern UI Redesign
**Improvements to match Jira design:**
- Updated stat cards with Jira-like styling (#DFE1E6 borders, #161B22 text)
- Professional icons with Jira color palette
- Improved typography (32px h1, 36px metric values, 12px uppercase labels)
- Better spacing and visual hierarchy
- Consistent shadow system: `0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13)`
- Added "Filter by Project:" label for clarity

## File Changes
- `views/reports/index.php` - Fixed dropdown parameter, improved UI design
- `src/Controllers/ReportController.php` - Added project filtering to all stats

## Testing
After the fix, when you:
1. Navigate to `/reports`
2. Click on "All Projects" dropdown
3. Select a specific project (e.g., "Baramati Project")
4. The stats cards above and all report links below should be filtered to show only data from that project

## Consistency
This fix ensures the reports page follows the same pattern as individual report pages like:
- `/reports/workload` - Uses `project_id` parameter
- `/reports/priority-breakdown` - Uses `project_id` parameter
- `/reports/resolution-time` - Uses `project_id` parameter

All now use consistent parameter naming and value types.
