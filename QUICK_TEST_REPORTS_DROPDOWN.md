# Quick Test Guide - Reports Project Filter

## 5-Minute Test Procedure

### Test Case 1: Select Project
**Expected**: Stats update to show only selected project's data

1. Open: `http://localhost:8080/jira_clone_system/public/reports`
2. Look at "Total Issues" stat (top left card)
3. Note the number
4. Click "Filter by Project:" dropdown
5. Select "Baramati Project" (or any project)
6. **Expected**: 
   - URL changes to `?project_id=1` (or respective ID)
   - "Total Issues" count changes to reflect only that project
   - Other stat cards update accordingly
   - ✅ **PASS** if numbers changed and URL has `?project_id=X`

### Test Case 2: Clear Filter
**Expected**: Returns to showing all projects

1. After Test Case 1, click dropdown again
2. Select "All Projects"
3. **Expected**:
   - URL changes back to `/reports` (no query param)
   - "Total Issues" returns to original higher number
   - All stats show combined data from all projects
   - ✅ **PASS** if numbers go back to original

### Test Case 3: Report Navigation with Filter
**Expected**: Report pages respect the selected project

1. Open reports page: `/reports`
2. Select a project from dropdown (e.g., "Baramati Project")
3. Verify URL shows `?project_id=X`
4. Click on "Burndown Chart" (Agile Reports section)
5. **Expected**:
   - Redirects to `/reports/burndown/1?project_id=X`
   - Report shows only that project's data
   - Project dropdown on that page shows same selection
   - ✅ **PASS** if project filter persists to report page

### Test Case 4: UI Visual Check
**Expected**: Modern Jira-like design

1. Open: `/reports`
2. Check design elements:
   - ✅ Stat cards have white background with subtle gray border
   - ✅ Icons are color-coded (blue, green, orange)
   - ✅ Large numbers (36px) for metric values
   - ✅ Uppercase labels above metrics
   - ✅ "Filter by Project:" label visible
   - ✅ Dropdown has fixed width (240px)
   - ✅ Cards have subtle shadows (not bold)
   - ✅ Professional color scheme (dark gray text, light borders)

## What to Check in Browser

### Network Tab (F12 → Network)
```
GET /reports                           ← Initial load
GET /reports?project_id=1              ← After selecting project
GET /reports                           ← After selecting "All Projects"
GET /reports/workload?project_id=1     ← When clicking report with filter
```

### Console Tab (F12 → Console)
- No JavaScript errors
- No warnings
- Clean console output

### Mobile Responsiveness (F12 → Toggle Device)
- Dropdown stacks properly on tablet (< 768px)
- Stat cards stack to 2 columns on tablet
- Stat cards stack to 1 column on mobile
- Text is readable on mobile
- No horizontal scrolling

## Database Query Verification

If you want to verify the queries are working:

1. Open `/reports?project_id=1` in browser
2. Watch the queries in PHP error logs (if enabled)
3. The WHERE clauses should include: `AND project_id = 1`

## Troubleshooting

### Issue: Dropdown doesn't change URL
**Solution**: 
- Check browser console (F12) for JavaScript errors
- Clear browser cache: Ctrl+Shift+Delete
- Hard refresh: Ctrl+F5

### Issue: Stats don't update
**Solution**:
- Check that `$selectedProject` is being passed from controller
- Verify `project_id` parameter is being read correctly
- Check database contains data for selected project

### Issue: Report pages don't filter
**Solution**:
- Other report pages (`workload`, etc.) already support `project_id`
- They should automatically filter when passed the parameter
- If not filtering, check their controllers

## Success Checklist

✅ Dropdown changes URL from `?project_id=X` to `/reports`  
✅ Stats cards update when project is selected  
✅ Report navigation works with filter  
✅ "All Projects" option clears the filter  
✅ UI looks modern and Jira-like  
✅ No JavaScript console errors  
✅ Works on mobile/tablet  
✅ Page is responsive  

## Files Tested
- `views/reports/index.php`
- `src/Controllers/ReportController.php`
- All report pages that use `project_id` parameter

## Timeline
- View changes: 5 minutes
- Controller changes: 7 minutes
- UI improvements: 10 minutes
- Testing: 5 minutes

**Total**: ~27 minutes to implement and test
