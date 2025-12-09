# Velocity Chart Testing Guide

## Quick Test

### Step 1: Navigate to Velocity Chart
1. Go to Reports page: `http://localhost/jira_clone_system/public/reports`
2. Click "Velocity Chart" link
3. Should see the velocity chart page

### Step 2: Check Browser Console
1. Open Developer Tools: Press **F12**
2. Go to **Console** tab
3. You should see:
   ```
   VELOCITY SCRIPT LOADED
   === VELOCITY CHART DEBUG ===
   Raw velocity data: [...]
   Data type: object
   Is array: true
   Length: X (number of sprints)
   ```

### Step 3: Verify Data Display
- If sprints exist and are closed, you should see:
  - A bar chart with Committed vs Completed points
  - A red dashed line showing average velocity
  - Sprint names on x-axis
  - Story points on y-axis

- If no closed sprints exist, you should see:
  - "No sprint data available" message
  - Empty chart container

### Step 4: Test Export Button
1. Click the "Export" button
2. A PNG image should download: `velocity-chart-YYYY-MM-DD.png`
3. The image should contain the chart visualization

### Step 5: Test Board Selector
1. Change the board in the dropdown (top right)
2. Page should reload with new board's data
3. Chart should update accordingly

## If Chart Doesn't Show

### Check 1: Console Errors
Open F12 Developer Tools → Console tab
- Look for red error messages
- Note the exact error

### Check 2: Verify Data Exists
Visit this URL in browser:
```
http://localhost/jira_clone_system/test_velocity_raw.php
```

Should show JSON like:
```json
{
  "board": {...},
  "sprint_count": 5,
  "average_velocity": 25.5,
  "velocity_data": [
    {
      "sprint_id": 1,
      "sprint_name": "Sprint 1",
      "committed": 25,
      "completed": 24,
      "start_date": "2024-01-01",
      "end_date": "2024-01-08"
    }
  ]
}
```

If this returns "No boards found" or empty data:
- Create a board in the system
- Create sprints for the board
- Close some sprints
- Add issues to the sprints with story points
- Check issue status is marked as "Done"

### Check 3: Database Check
Run these SQL queries in your database:

```sql
-- Check boards exist
SELECT COUNT(*) FROM boards;

-- Check closed sprints exist  
SELECT * FROM sprints WHERE status = 'closed';

-- Check issues in closed sprints have story points
SELECT sprint_id, COUNT(*) as issue_count, SUM(story_points) as total_points
FROM issues 
WHERE sprint_id IN (SELECT id FROM sprints WHERE status = 'closed')
GROUP BY sprint_id;

-- Check done statuses exist
SELECT * FROM statuses WHERE category = 'done';
```

If any of these return no data, you need to:
1. Create issues with story points
2. Close some sprints
3. Move issues to "Done" status

## Expected Behavior

### With Data
- Chart displays with bars for each sprint
- Shows Committed (gray) vs Completed (green) points
- Red dashed line shows average velocity
- Sprint table shows accuracy percentage
- Board selector works

### Without Data
- "No sprint data available" message appears
- No chart rendered
- Sprint count shows 0
- Table shows "No sprint data available"

## Common Issues & Solutions

### Issue: "No sprint data available"
**Cause**: Board has no closed sprints
**Solution**: 
1. Go to board
2. Create sprints
3. Add issues to sprints
4. Close the sprint
5. Mark issues as Done

### Issue: Export button doesn't download
**Cause**: Chart.js library didn't load
**Solution**:
1. Check internet connection (CDN access)
2. Check console for library load errors
3. Refresh page and try again

### Issue: Chart shows but no bars/lines
**Cause**: Data exists but chart configuration issue
**Solution**:
1. Check browser console for errors
2. Verify data in test_velocity_raw.php
3. Make sure issues have story_points > 0
4. Make sure issues are in "Done" status

### Issue: Board selector doesn't work
**Cause**: JavaScript error
**Solution**:
1. Check console for JS errors
2. Verify boards exist in database
3. Refresh page

## Testing Checklist

- [ ] Page loads without errors
- [ ] Console shows "VELOCITY SCRIPT LOADED"
- [ ] Chart displays (if data exists)
- [ ] Board selector works
- [ ] Export button downloads PNG
- [ ] Sprint table shows correctly
- [ ] Statistics cards show correct numbers
- [ ] Empty state shows when no data

## Files Involved

- Controller: `src/Controllers/ReportController.php` (velocity method)
- View: `views/reports/velocity.php`
- Helper: `test_velocity_raw.php` (for testing data)
- Helper: `diagnose_velocity.php` (for diagnostics)

## Support

If still not working:
1. Check all three console.log outputs
2. Visit test_velocity_raw.php to verify data
3. Review database with SQL queries above
4. Check ReportController::velocity() method
5. Verify view file has correct code

The chart should work once you have:
- ✅ A board created
- ✅ Closed sprints assigned to the board
- ✅ Issues in closed sprints with story_points
- ✅ Issues marked as "Done" status
