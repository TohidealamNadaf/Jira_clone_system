# Calendar Issues Not Loading - Diagnosis & Solutions

## Problem
Calendar page loads but shows no events when clicking on dates or viewing the calendar.

## Root Cause Analysis

The calendar system requires **issue date data** to display events. If issues don't have `start_date`, `end_date`, or `due_date` values, the calendar will appear empty.

### Possible Causes

1. **No date data in database**
   - Issues exist but have NULL dates
   - No calendar seed data populated
   
2. **API endpoint not returning data**
   - CalendarService issues with date filtering
   - Database query problems
   
3. **JavaScript error**
   - Failed API call
   - Event modal not opening
   - Filter issues
   
4. **Permission issues**
   - User doesn't have `issues.view` permission
   - CSRF token problems

## Diagnostic Steps

### Step 1: Check Browser Console (F12)
Open DevTools and look for messages starting with `📅 [CALENDAR]`:

```javascript
// Look for these in console:
📅 [CALENDAR] DOMContentLoaded event fired
📅 [CALENDAR] Calendar element found: true
📅 [CALENDAR] Fetching events from: http://localhost:8081/...
📅 [CALENDAR] API Response Status: 200
📅 [CALENDAR] Events returned from API: 0
```

**If you see 0 events:** Database doesn't have date data.
**If you see an error:** Check the error message for specifics.

### Step 2: Test API Directly

Open this in your browser:
```
http://localhost:8081/jira_clone_system/public/api/v1/calendar/events?start=2025-12-01T00:00:00Z&end=2025-12-31T23:59:59Z
```

**Response should be:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "PROJ-1: Issue Summary",
      "start": "2025-12-20",
      "end": "2025-12-20",
      ...
    }
  ]
}
```

If `data` array is empty, go to Step 3.

### Step 3: Check Database

Run this PHP script to diagnose:
```bash
php diagnose_calendar_events_loading.php
```

This will show:
- ✓ Database connection status
- ✓ Issues table structure
- ✓ Total issues with date data
- ✓ Sample issues data
- ✓ Calendar service test results

### Step 4: Check Date Data in Database

Issues need AT LEAST ONE of these:
- `start_date` (YYYY-MM-DD)
- `end_date` (YYYY-MM-DD)  
- `due_date` (YYYY-MM-DD)

To check if your issues have dates:
```sql
SELECT 
  COUNT(*) as total_issues,
  SUM(CASE WHEN start_date IS NOT NULL THEN 1 ELSE 0 END) as with_start,
  SUM(CASE WHEN end_date IS NOT NULL THEN 1 ELSE 0 END) as with_end,
  SUM(CASE WHEN due_date IS NOT NULL THEN 1 ELSE 0 END) as with_due
FROM issues;
```

**Expected result:** At least one of `with_start`, `with_end`, or `with_due` should be > 0.

## Solutions

### Solution 1: Seed Calendar Date Data (RECOMMENDED)

If you have issues but no dates:

```bash
php scripts/seed_calendar_dates.php
```

This script will:
- Find all issues without dates
- Generate realistic due dates (spread across next 60 days)
- Populate start_date and end_date for multi-day issues
- Update the database automatically

### Solution 2: Create Test Issues with Dates

If you have no issues at all:

```bash
php scripts/verify-and-seed.php
```

This will:
- Create sample projects
- Create sample issues with proper dates
- Populate all required fields
- Ready for calendar display

### Solution 3: Manually Populate Dates

If you want to update specific issues:

```sql
UPDATE issues 
SET due_date = DATE_ADD(NOW(), INTERVAL FLOOR(1 + RAND() * 30) DAY)
WHERE due_date IS NULL;
```

## Testing After Fix

1. **Clear Browser Cache**
   - Ctrl+Shift+Del (Windows) or Cmd+Shift+Del (Mac)
   - Select "Cached images and files"
   - Clear

2. **Hard Refresh**
   - Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)

3. **Navigate to Calendar**
   - Go to: `http://localhost:8081/jira_clone_system/public/calendar`
   - Should see events on the calendar
   - Click on date → Modal should open with issue details

4. **Verify in Console**
   - F12 to open DevTools
   - Look for `📅 [CALENDAR] Events returned from API: X`
   - X should be > 0

## Calendar Features Checklist

Once events are loading:

- [ ] **View Issues on Calendar**: Click date to see events
- [ ] **Click Issue**: Click event → Modal opens with full details
- [ ] **Filters**: Use sidebar filters to search issues
- [ ] **Project Filter**: Filter by specific project
- [ ] **Status Filter**: Filter by status (Open, In Progress, etc.)
- [ ] **Drag & Drop**: Drag issue to different date (updates database)
- [ ] **Sidebar**: Shows upcoming issues, my schedule, etc.
- [ ] **Navigation**: Month/Year navigation buttons work

## API Endpoints

### Main Event Endpoints
- `GET /api/v1/calendar/events` - Get events for date range
- `GET /api/v1/calendar/upcoming` - Get upcoming issues
- `GET /api/v1/calendar/overdue` - Get overdue issues

### Filter Endpoints
- `GET /api/v1/calendar/projects` - List all projects
- `GET /api/v1/calendar/statuses` - List all statuses
- `GET /api/v1/calendar/priorities` - List all priorities
- `GET /api/v1/calendar/issue-types` - List all issue types
- `GET /api/v1/calendar/users` - List all users

## Common Issues & Fixes

### Issue: "0 events showing"
**Solution**: Run `php scripts/seed_calendar_dates.php`

### Issue: Modal won't open when clicking event
**Solution**: Check console for errors, ensure CSRF token is present

### Issue: Filter dropdowns empty
**Solution**: Check if projects/statuses/priorities exist in database

### Issue: "Failed to load events" error
**Solution**: 
- Check API endpoint: http://localhost:8081/.../api/v1/calendar/events
- Check browser console for exact error
- Verify user has `issues.view` permission

### Issue: Drag & drop doesn't work
**Solution**:
- Events must be loaded first
- Check calendar is in month/week view (not day)
- Verify user has `issues.edit` permission

## Contact & Support

For additional help:
1. Check browser console (F12) for error messages
2. Run diagnostic script: `php diagnose_calendar_events_loading.php`
3. Review API response directly in browser
4. Check AGENTS.md for calendar architecture details

## Related Files

- **Controller**: `src/Controllers/CalendarController.php`
- **Service**: `src/Services/CalendarService.php`
- **View**: `views/calendar/index.php`
- **JavaScript**: `public/assets/js/calendar-realtime.js`
- **Routes**: `routes/api.php` (lines 182-190)
- **Database**: `database/schema.sql` (issues table)
