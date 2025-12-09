# SELECT2 DROPDOWN - DEBUGGING GUIDE

## Quick Test

**Go to**: `http://localhost:8080/jira_clone_system/public/test-dropdown.php`

This page will help you diagnose if Select2 is working properly.

---

## Testing Checklist

### Step 1: Test Dropdown on Test Page
1. Go to: `http://localhost:8080/jira_clone_system/public/test-dropdown.php`
2. Scroll down to "Test Dropdown Below"
3. Click the dropdown
4. **Expected**: Dropdown opens with 4 options ✓
5. **Try scrolling**: Use mouse wheel
6. **Try typing**: Start typing to search

### Step 2: Test Dashboard Dropdown
1. Go to: `http://localhost:8080/jira_clone_system/public/dashboard`
2. Click "Create" button (top-right)
3. Click "Project" dropdown
4. **Expected**: Projects should appear ✓
5. **If empty**: Continue with debugging below

### Step 3: Check Browser Console
1. Press `F12` to open Developer Tools
2. Go to "Console" tab
3. Look for any red error messages
4. Take note of what errors appear

### Step 4: Check Network Tab
1. In Developer Tools, go to "Network" tab
2. Click "Create" button on dashboard
3. Look for requests to:
   - `select2.min.js` - Should be loaded
   - `select2.min.css` - Should be loaded
   - `/api/v1/projects` - API call for projects

---

## Common Issues & Solutions

### Issue 1: Test Dropdown Works, But Dashboard Doesn't

**Problem**: Test page shows projects, but dashboard dropdown is empty

**Solution**:
1. Hard refresh dashboard: `Ctrl+F5`
2. Check browser console (F12) for errors
3. Check if `/api/v1/projects` returns data
   - Go to: `http://localhost:8080/jira_clone_system/public/api/v1/projects`
   - Should see JSON with project list

### Issue 2: No Projects in Database

**Problem**: Dropdown is empty and no projects in API response

**Solution**:
Run the database seeder:
```bash
php scripts/verify-and-seed.php
```

This will create sample projects. Then reload dashboard.

### Issue 3: JavaScript Errors in Console

**Problem**: Console shows errors like:
- `$ is not defined`
- `select2 is not a function`
- Other JavaScript errors

**Solution**:
1. Hard refresh page: `Ctrl+F5`
2. Clear browser cache
3. Check if jQuery loads before Select2
4. Verify CDN links are accessible

### Issue 4: Dropdown Opens But No Options Show

**Problem**: Dropdown opens but no projects listed

**Solution**:
1. Check `/api/v1/projects` endpoint
2. Verify database has projects
3. Run seeder: `php scripts/verify-and-seed.php`
4. Check browser console for errors

### Issue 5: "No projects found" Message

**Problem**: Dropdown shows "No projects found"

**Solution**:
1. Verify projects exist in database
2. Run seeder if needed
3. Check API is returning projects
4. Verify project status (not archived)

---

## Debugging Steps

### Check 1: Verify Libraries Load

**In browser console (F12), paste:**
```javascript
console.log('jQuery:', typeof jQuery);
console.log('Select2:', typeof $.fn.select2);
```

**Expected output:**
```
jQuery: object
Select2: function
```

### Check 2: Verify Projects API

**Go to**: `http://localhost:8080/jira_clone_system/public/api/v1/projects`

**Expected response**: JSON array with projects
```json
[
  {
    "id": 1,
    "name": "Project Name",
    "key": "PROJ",
    ...
  }
]
```

### Check 3: Check Select2 Initialization

**In browser console (F12), paste:**
```javascript
console.log('Project Select initialized:', $('#quickCreateProject').data('select2'));
console.log('Issue Type Select initialized:', $('#quickCreateIssueType').data('select2'));
```

**Expected**: Shows Select2 object if initialized

### Check 4: Manually Trigger Project Load

**In browser console (F12), paste:**
```javascript
// Check if projects loaded
var select = document.getElementById('quickCreateProject');
console.log('Number of options:', select.options.length);
console.log('Options:', Array.from(select.options).map(o => o.text));
```

---

## Network Troubleshooting

### Check If Files Load

**In Developer Tools (F12):**
1. Go to "Network" tab
2. Reload page
3. Look for these files:
   - ✓ `select2.min.css`
   - ✓ `select2.min.js`
   - ✓ `jquery-3.6.0.min.js`
   - ✓ Refresh should show `/api/v1/projects` call

### Check API Response

1. In Network tab, find `/api/v1/projects` request
2. Click on it
3. Go to "Response" tab
4. Should show JSON with projects

---

## File Locations

### Main Files
- `views/layouts/app.php` - Contains modal and initialization code
- `public/assets/css/app.css` - CSS styling

### Test Files
- `public/test-dropdown.php` - Test page with debugging info
- Created this file: `SELECT2_DEBUGGING_GUIDE.md`

---

## Step-by-Step Debugging

### If Projects Don't Show:

**Step 1**: Hard refresh
```
Ctrl+F5
```

**Step 2**: Open console (F12) and check for errors
```
Look for red error messages
```

**Step 3**: Check API endpoint
```
Go to: /api/v1/projects
Should return JSON list
```

**Step 4**: Check if projects exist
```
Run: php scripts/verify-and-seed.php
```

**Step 5**: Reload and test again

---

## Performance Checks

### Check Load Time
1. Open Developer Tools (F12)
2. Go to "Network" tab
3. Reload page
4. Look at load times:
   - CSS should load in < 100ms
   - JS should load in < 500ms

### Check for Blocking Resources
1. Look in Network tab for red X marks
2. These indicate failed requests
3. Fix any failed CDN resources

---

## Solutions Summary

| Issue | Solution |
|-------|----------|
| Test works, dashboard doesn't | Hard refresh (Ctrl+F5) |
| No projects in dropdown | Run seeder: `php scripts/verify-and-seed.php` |
| JavaScript errors | Check console (F12), verify libraries load |
| Dropdown opens but no options | Check API endpoint `/api/v1/projects` |
| Select2 not loading | Verify CDN links are accessible |
| Scrolling not working | Hard refresh, clear cache |

---

## Contact

If you're still having issues:

1. **Check test page**: `http://localhost:8080/jira_clone_system/public/test-dropdown.php`
2. **Check console**: Press F12 and look at errors
3. **Check API**: Go to `/api/v1/projects`
4. **Run seeder**: `php scripts/verify-and-seed.php`
5. **Hard refresh**: Ctrl+F5

---

## Resources

- **Select2 Docs**: https://select2.org/
- **jQuery Docs**: https://jquery.com/
- **Bootstrap Docs**: https://getbootstrap.com/

---

## Final Checklist

- [ ] Test page shows dropdown working
- [ ] Dashboard shows "Create" button
- [ ] Project dropdown opens when clicked
- [ ] Projects appear in dropdown list
- [ ] Can scroll with mouse wheel
- [ ] Can search by typing
- [ ] Can select a project
- [ ] Issue types load when project selected
- [ ] Can create an issue

If all checked ✓, dropdown is working correctly!
