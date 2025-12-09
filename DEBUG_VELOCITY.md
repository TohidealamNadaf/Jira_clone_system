# Velocity Chart - Debug Instructions

## Step 1: Verify the Fix is in Place

### Check Controller
File: `src/Controllers/ReportController.php`
Line 281 should read:
```php
public function velocity(Request $request): string
```

**NOT:**
```php
public function velocity(Request $request): void
```

If it still says `void`, the fix wasn't applied correctly.

### Check View File Exists
File: `views/reports/velocity.php`
Should be ~270 lines with JavaScript code at the bottom.

## Step 2: Run Direct Tests

### Test 1: Direct View Rendering
Access this URL in your browser:
```
http://localhost/jira_clone_system/public/test-velocity.php
```

**Expected Output:**
- "✓ View rendered successfully!"
- Full HTML page with velocity chart
- Console.log statements

**If you see error:**
- Note the exact error message
- Check file exists at correct location

### Test 2: Check Raw Data
Access this URL:
```
http://localhost/jira_clone_system/test_velocity_raw.php
```

**Expected Output:**
JSON like:
```json
{
  "board": {...},
  "sprint_count": 5,
  "velocity_data": [
    {"sprint_name": "Sprint 1", "committed": 25, "completed": 24, ...}
  ]
}
```

**If empty data:**
- Board has no closed sprints
- Need to create and close sprints first

### Test 3: Browser Console Check
1. Go to Reports → Velocity Chart
2. Press F12 to open DevTools
3. Go to Console tab
4. You should see: `VELOCITY SCRIPT LOADED`

**If you don't see it:**
- JavaScript isn't executing
- View might not be loading
- Check for any red error messages

## Step 3: Manual Verification

### Check Database
Run these SQL queries:

```sql
-- Check boards exist
SELECT * FROM boards LIMIT 1;

-- Check closed sprints exist
SELECT * FROM sprints WHERE status = 'closed' LIMIT 1;

-- Check issues with story points
SELECT * FROM issues WHERE story_points > 0 LIMIT 1;

-- Check done statuses
SELECT * FROM statuses WHERE category = 'done';
```

If any return empty results, you need to create test data.

### Create Test Data (if needed)
```sql
-- Create or use existing board
-- Create sprint:
INSERT INTO sprints (board_id, name, status, start_date, end_date, goal)
VALUES (1, 'Test Sprint', 'closed', NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'Test');

-- Add issue to sprint:
INSERT INTO issues (project_id, sprint_id, issue_key, summary, description, status_id, story_points)
VALUES (1, 1, 'TEST-1', 'Test Issue', 'Test', 2, 8);

-- Mark as done:
UPDATE issues SET status_id = (SELECT id FROM statuses WHERE category = 'done' LIMIT 1) WHERE id = ...;
```

## Step 4: Common Issues & Fixes

### Issue: "View not found"
**Cause:** File doesn't exist at correct location  
**Fix:** Check `views/reports/velocity.php` exists

### Issue: Blank page or no data
**Cause 1:** No closed sprints in database  
**Fix:** Create sprints and close them

**Cause 2:** Issues don't have story points  
**Fix:** Add story_points to issues

**Cause 3:** Issues not marked as "Done"  
**Fix:** Update issue status_id to done status

### Issue: "VELOCITY SCRIPT LOADED" doesn't appear
**Cause:** View isn't rendering  
**Fix:**
1. Run test-velocity.php directly
2. Check for PHP errors
3. Verify file syntax

### Issue: Chart appears but no bars
**Cause:** JavaScript error with data  
**Fix:**
1. Check browser console (F12)
2. Look for red errors
3. Check JSON data structure

## Step 5: If Still Not Working

### Get Help Info
Run this and note the output:

**Test 1:**
```
http://localhost/jira_clone_system/public/test-velocity.php
```
(Save output)

**Test 2:**
```
http://localhost/jira_clone_system/test_velocity_raw.php
```
(Save output)

**Test 3:**
Open browser DevTools (F12) → Console
Click on Reports → Velocity Chart
Copy any errors from console

## Checklist Before Reporting Issue

- [ ] `src/Controllers/ReportController.php` line 281 has `string` return type
- [ ] `views/reports/velocity.php` file exists and is ~270 lines
- [ ] Database has at least one closed sprint
- [ ] Issues in sprints have story_points > 0
- [ ] Issues are marked with "Done" status  
- [ ] test-velocity.php renders without errors
- [ ] test_velocity_raw.php shows JSON data
- [ ] Browser console shows "VELOCITY SCRIPT LOADED"

If all checkboxes pass, the velocity chart should work!

## Final Test

1. Go to Reports
2. Click "Velocity Chart"
3. Should see chart page (even if empty)
4. Press F12 → Console
5. Should see "VELOCITY SCRIPT LOADED" (green text)
6. If board has closed sprints, chart should display
7. Click Export → PNG downloads

If any of these fails, note which step and check troubleshooting above.
