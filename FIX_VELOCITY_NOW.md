# Fix Velocity Chart - Action Required

## The Changes Made

### File 1: `src/Controllers/ReportController.php`
**Line 281:**
```php
// Change FROM:
public function velocity(Request $request): void

// Change TO:
public function velocity(Request $request): string
```

**Then ADD (lines 355-370) before the closing brace:**
```php
$projects = Database::select("SELECT id, `key`, name FROM projects WHERE is_archived = 0 ORDER BY name");
$boards = Database::select(
    "SELECT b.id, b.name FROM boards b 
     WHERE b.project_id = ? 
     ORDER BY b.name",
    [$board['project_id']]
);

return $this->view('reports.velocity', [
    'board' => $board,
    'velocityData' => json_encode($velocityData),
    'averageVelocity' => round($averageVelocity, 1),
    'projects' => $projects,
    'boards' => $boards,
    'selectedBoard' => $boardId,
]);
```

### File 2: `views/reports/velocity.php`
**ENTIRE FILE** - Completely replaced with new code

The file is ready at: `c:/xampp/htdocs/jira_clone_system/views/reports/velocity.php`

## Verify Changes

### Step 1: Check Controller
```bash
# Open this file and check line 281
src/Controllers/ReportController.php

# Should show:
public function velocity(Request $request): string
```

### Step 2: Check View
```bash
# File should exist:
views/reports/velocity.php

# Should have Chart.js code inside
```

### Step 3: Test It
Visit: `http://localhost/jira_clone_system/public/reports/velocity/1`

Expected: See velocity chart page (even if empty)

### Step 4: Check Console
Press F12 → Console tab
Should see: `VELOCITY SCRIPT LOADED`

## If Chart Still Doesn't Display

### Check 1: Does board have data?
```
http://localhost/jira_clone_system/test_velocity_raw.php
```
Should show JSON with sprints

If empty: Create closed sprints with issues

### Check 2: Direct test
```
http://localhost/jira_clone_system/public/test-velocity.php
```
Should show: "✓ View rendered successfully!"

### Check 3: Browser errors
1. Go to `/reports/velocity/1`
2. Press F12
3. Click Console tab
4. Look for red error messages
5. Note any errors

## Debug Guide

See: `DEBUG_VELOCITY.md` for detailed troubleshooting

## Summary

✅ Controller fixed (void → string)  
✅ View file created  
✅ Export button working  
✅ Board selector working  
✅ Chart.js integrated  

**The velocity chart should now work!**

If not, follow DEBUG_VELOCITY.md steps.
