# Debug Velocity Endpoint Issue

## Problem Summary
User reports:
1. Export button not working (no console output)
2. Team Velocity Over Time chart not showing

## Root Cause Investigation

### Step 1: Verify the Endpoint is Being Called
**URL**: `localhost:8080/jira_clone_system/public/reports/velocity/2`
**Route**: `/reports/velocity/{boardId}`  (defined in routes/web.php line 137)
**Controller**: `ReportController::velocity()`

### Step 2: Check What Data is Being Passed

Looking at the velocity view:
```php
// Line 108
const velocityRaw = <?= $velocityData ?? '[]' ?>;
```

The controller passes:
```php
// Line 365
'velocityData' => json_encode($velocityData),
```

**This means**:
- If board 2 has closed sprints → `velocityData` will be JSON array with sprint data
- If board 2 has NO closed sprints → `velocityData` will be JSON empty array `[]`

### Step 3: Your Screenshot Shows

From the screenshot of `/reports/velocity/2`:
- **Average Velocity**: 0
- **Sprints Analyzed**: 0
- **Chart area**: Empty

**This means**: Board 2 has **zero closed sprints** with data

### Step 4: Why Export Button Doesn't Work

In this scenario:
1. `velocityRaw = []` (empty array)
2. `initChart()` runs and sets `velocityChart = null` (line 119-129 in fixed code)
3. User clicks Export
4. Check fails: `if (!velocityChart)` → shows alert "Chart not ready"
5. No console.log executes because listener never attached (original code issue)

## The Real Fix Needed

The code I provided SHOULD fix this, but you need to verify:

### Test 1: Check if script is even running

Open DevTools F12 → Console tab → Navigate to `/reports/velocity/2`

You should immediately see:
```
VELOCITY SCRIPT LOADED
=== VELOCITY CHART DEBUG ===
Raw velocity data: []
Data type: object
Is array: true
Length: 0
No velocity data available, showing empty state
```

**If you don't see this**, then the JavaScript isn't running at all.

### Test 2: Click Export button with DevTools open

You should see:
```
Export clicked
velocityChart state: null
Chart not initialized yet
```

**If you see nothing**, then the event listener wasn't attached.

## Solution to Get Chart to Show Data

Since board 2 has no closed sprints, create one:

```sql
-- Create a test sprint
INSERT INTO sprints (board_id, name, status, start_date, end_date, goal, created_at) 
VALUES (2, 'Test Sprint 1', 'closed', '2025-01-01', '2025-01-14', 'Test', NOW());

-- Note the sprint ID returned

-- Create some issues with story points (assumes project_id = 1, status_id for "done" = 3)
INSERT INTO issues (project_id, key, title, status_id, story_points, sprint_id, created_by, created_at)  
VALUES (?, 'TST-1', 'Test Issue 1', 3, 8, ?, 1, NOW());

-- Repeat for a few more issues
```

Then refresh the page at `/reports/velocity/2` and the chart should show.

## Alternative: Check if Data Exists

Run: `php test_velocity_chart.php`

This will show:
- Number of closed sprints for board 2
- Number of story points
- Velocity data that would be displayed

## Files Modified to Fix This Issue

**File**: `views/reports/velocity.php`

**Changes Made**:
1. Lines 119-227: Improved `initChart()` function
   - Added JSON logging of velocityRaw data
   - Added Chart.js library validation
   - Added comprehensive null checks
   - Added proper chart destruction
   - Added error stack traces

2. Lines 240-297: Improved event listener attachment  
   - Wrapped in functions: `attachExportListener()`, `attachBoardSelectListener()`
   - Added null checks for DOM elements
   - Added try-catch error handling
   - Supports both early and late page load

## Expected Behavior After Fix

### When Opening Page (no data):
```
Console shows:
✓ VELOCITY SCRIPT LOADED
✓ Raw velocity data: []
✓ No velocity data available, showing empty state
```

### When Clicking Export (with no chart):
```
Console shows:
✓ Export clicked
✓ velocityChart state: null
✓ Alert: "Chart is not ready..."
```

### When Closing Sprints (with data):
```
Console shows:
✓ VELOCITY SCRIPT LOADED
✓ Raw velocity data: [{...}, {...}]
✓ Chart prepared: {labels, committed, completed, avg}
✓ ✓ Chart created successfully

When clicking Export:
✓ Export clicked
✓ velocityChart state: Chart {...}
✓ ✓ Chart exported successfully
✓ File downloads: velocity-chart-2025-12-07.png
```

## Verification Checklist

- [ ] Page loads without JavaScript errors
- [ ] Console shows "VELOCITY SCRIPT LOADED"
- [ ] Console shows velocity data (empty or with sprints)
- [ ] Export button responsive (shows message or downloads file)
- [ ] Board selector changes page
- [ ] Chart appears when sprints exist with data

## If Still Not Working

1. **Check browser console** (F12 → Console) for any error messages
2. **Run test_velocity_chart.php** to verify database has data
3. **Check network tab** (F12 → Network) to see if chart.js CDN loads
4. **Hard refresh** (Ctrl+F5) to clear cached JavaScript
5. **Check PHP error logs** in storage/logs/
