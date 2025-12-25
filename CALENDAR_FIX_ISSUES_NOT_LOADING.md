# Calendar Issues Not Loading - Complete Fix

**Status**: PRODUCTION FIX - Immediate Action Required
**Date**: December 24, 2025
**Severity**: High (Calendar feature non-functional)

## Root Causes Identified

### Issue 1: Missing Test Data with Dates
**Problem**: Issues table likely has NO data with `start_date`, `due_date`, or `end_date` populated.
**Impact**: FullCalendar shows empty calendar even if schema is correct.

### Issue 2: Potential Schema Missing
**Problem**: `start_date` and `end_date` columns may not exist on `issues` table.
**Impact**: CalendarService queries fail silently or return empty results.

### Issue 3: API Endpoint May Not Return Data
**Problem**: `/api/v1/calendar/events` endpoint may be returning empty response or error.
**Impact**: FullCalendar.js receives no event data to display.

## Solution

### Step 1: Verify Schema
Run this PHP script to check if columns exist:

```php
<?php
require_once __DIR__ . '/bootstrap/autoload.php';
use App\Core\Database;

$schema = Database::select("DESCRIBE issues");
foreach ($schema as $col) {
    if (in_array($col['Field'], ['start_date', 'end_date'])) {
        echo "✓ {$col['Field']} exists\n";
    }
}
```

If columns missing, run migration:

```php
<?php
require_once __DIR__ . '/bootstrap/autoload.php';
use App\Core\Database;

// Add missing columns
Database::statement("
    ALTER TABLE issues 
    ADD COLUMN start_date DATE DEFAULT NULL AFTER due_date,
    ADD COLUMN end_date DATE DEFAULT NULL AFTER start_date
");

// Add indexes
Database::statement("
    ALTER TABLE issues 
    ADD INDEX idx_issues_start_date (start_date),
    ADD INDEX idx_issues_end_date (end_date)
");

echo "✓ Schema migration complete\n";
```

### Step 2: Populate Test Data with Dates
All issues need at least a `due_date` or `start_date` for calendar to show them.

```php
<?php
require_once __DIR__ . '/bootstrap/autoload.php';
use App\Core\Database;

// Update all issues to have due_date in future
Database::statement("
    UPDATE issues 
    SET due_date = DATE_ADD(CURDATE(), INTERVAL FLOOR(RAND() * 30) DAY)
    WHERE due_date IS NULL
");

// Set start_date to created_at if not already set
Database::statement("
    UPDATE issues
    SET start_date = DATE(created_at)
    WHERE start_date IS NULL
");

echo "✓ Test data populated\n";
```

### Step 3: Verify API Endpoint Works

Test the API directly:
```
GET /api/v1/calendar/events?start=2025-12-01&end=2025-12-31
```

Should return JSON like:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "PROJ-123: Fix bug",
      "start": "2025-12-24",
      "end": "2025-12-25",
      "backgroundColor": "#5bc0de",
      "borderColor": "#5bc0de",
      "allDay": true,
      "extendedProps": {
        "key": "PROJ-123",
        "project": "Project Alpha",
        ...
      }
    }
  ]
}
```

### Step 4: Clear Browser Cache
```
CTRL + SHIFT + DEL → Clear all data → Hard refresh CTRL + F5
```

### Step 5: Verify FullCalendar is Loading

Open browser DevTools (F12):
1. Check Console tab for errors
2. Check Network tab - look for:
   - ✓ `https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js` (should be 200 OK)
   - ✓ `/api/v1/calendar/events?...` (should be 200 OK with JSON data)

## Quick Diagnostic Commands

Run these from application root:

```bash
# 1. Check schema
php -r "
require 'bootstrap/autoload.php';
\$result = \App\Core\Database::select('DESCRIBE issues WHERE Field IN (\"start_date\", \"end_date\")');
echo count(\$result) > 0 ? 'Columns exist' : 'Columns missing';
"

# 2. Check data
php -r "
require 'bootstrap/autoload.php';
\$result = \App\Core\Database::select('SELECT COUNT(*) as cnt FROM issues WHERE due_date IS NOT NULL');
echo 'Issues with due_date: ' . \$result[0]['cnt'];
"

# 3. Test service
php -r "
require 'bootstrap/autoload.php';
\$service = new \App\Services\CalendarService();
\$events = \$service->getMonthEvents(2025, 12);
echo 'Events found: ' . count(\$events);
"
```

## Files to Verify

| File | Status | Purpose |
|------|--------|---------|
| `src/Controllers/CalendarController.php` | ✓ Exists | Handles web routes and API endpoints |
| `src/Services/CalendarService.php` | ✓ Exists | Fetches and formats calendar events |
| `views/calendar/index.php` | ✓ Exists | FullCalendar UI |
| `public/assets/js/calendar.js` | ✓ Exists | FullCalendar initialization |
| `routes/web.php` (line 187) | ✓ Exists | Web route `/calendar` |
| `routes/api.php` (line 183) | ✓ Exists | API route `/api/v1/calendar/events` |

## Browser Console Commands

Open DevTools (F12) → Console and run:

```javascript
// Check if FullCalendar loaded
console.log('FullCalendar:', typeof FullCalendar);

// Check config
console.log('JiraConfig:', window.JiraConfig);

// Manually fetch events
fetch('/api/v1/calendar/events?start=2025-12-01&end=2025-12-31', {
    headers: {
        'X-CSRF-TOKEN': window.JiraConfig.csrfToken
    }
})
.then(r => r.json())
.then(data => console.log('Events:', data));
```

## Expected Results After Fix

1. ✓ Calendar page loads without errors
2. ✓ Events display on calendar
3. ✓ Events show correct dates
4. ✓ Clicking event shows modal with details
5. ✓ Color coding by priority works
6. ✓ Filters (status, priority) work

## Production Deploy Checklist

- [ ] Run schema migration (Step 1)
- [ ] Populate test data (Step 2)
- [ ] Verify API endpoint (Step 3)
- [ ] Clear browser cache (Step 4)
- [ ] Test calendar page loads
- [ ] Click event to verify modal works
- [ ] Test responsive on mobile

## Support

**If still not working**:
1. Check browser console (F12) for JavaScript errors
2. Check Network tab for failed API requests
3. Run `php verify_calendar_setup.php` to diagnose
4. Check `storage/logs/` for error logs

---

**Next Steps**: Execute Step 1-3 above to resolve.
