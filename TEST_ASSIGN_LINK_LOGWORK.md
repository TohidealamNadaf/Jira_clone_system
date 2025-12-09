# Test Assign, Link Issue, and Log Work Features

## Quick Test Guide

### Prerequisites
- Be logged in as a user with appropriate permissions
- Have a project with multiple members
- Have an issue to work with (e.g., BP-16)

### Test 1: Assign Issue
1. Open issue BP-16 (or any issue)
2. Click the three-dots menu (top right)
3. Click "Assign"
4. Select a team member from dropdown
5. Click "Assign" button
6. **Expected**: Success notification, page refreshes, assignee shown in sidebar

**Debug if fails:**
```
- Check browser console for errors (F12 -> Console)
- Verify user has 'issues.assign' permission in project role
- Check network tab (F12 -> Network) - look for POST to /issue/XX/assign
```

### Test 2: Link Issue
1. Open issue BP-16
2. Click the three-dots menu
3. Click "Link Issue"
4. Select link type (e.g., "Blocks")
5. Enter target issue key (e.g., "BP-1")
6. Click "Link" button
7. **Expected**: Success notification, page refreshes, linked issue appears

**Debug if fails:**
```
- Verify target issue exists
- Check user has 'issues.link' permission
- Verify link type exists in database
```

### Test 3: Log Work
1. Open issue BP-16
2. Click the three-dots menu
3. Click "Log Work"
4. Enter time spent (e.g., 2.5 hours)
5. Verify start time is set (defaults to now)
6. Optionally enter description
7. Click "Log Work" button
8. **Expected**: Success notification, page refreshes, work log entry appears

**Debug if fails:**
```
- Check browser console
- Verify user has 'issues.log_work' permission
- Confirm datetime format is valid
```

## Manual Database Verification

Run these queries to verify data was saved:

```sql
-- Check assignment
SELECT id, issue_key, assignee_id FROM issues WHERE issue_key = 'BP-16';

-- Check links
SELECT * FROM issue_links WHERE source_issue_id = (SELECT id FROM issues WHERE issue_key = 'BP-16');

-- Check worklogs
SELECT * FROM worklogs WHERE issue_id = (SELECT id FROM issues WHERE issue_key = 'BP-16');
```

## API Endpoint Testing

### Test Assign via curl
```bash
curl -X POST http://localhost/jira_clone_system/public/issue/BP-16/assign \
  -H "Content-Type: application/json" \
  -d '{"assignee_id": 2}' \
  -b "session_id=..."
```

### Test Link via curl
```bash
curl -X POST http://localhost/jira_clone_system/public/issue/BP-16/link \
  -H "Content-Type: application/json" \
  -d '{"link_type_id": 1, "target_issue_key": "BP-1"}' \
  -b "session_id=..."
```

### Test Log Work via curl
```bash
curl -X POST http://localhost/jira_clone_system/public/issue/BP-16/logwork \
  -H "Content-Type: application/json" \
  -d '{"time_spent": 7200, "started_at": "2025-12-08T14:30", "description": "Test"}' \
  -b "session_id=..."
```

## Permissions Check

User must have these permissions in project role:
- `issues.assign` - For Assign feature
- `issues.link` - For Link Issue feature
- `issues.log_work` - For Log Work feature

To check/grant permissions:
1. Go to Admin → Roles
2. Find user's project role (e.g., "Developer")
3. Verify these permissions are checked
4. Save changes

## Common Issues & Solutions

### Issue 1: "Select Assign" button not showing
**Solution**: Check if user has `issues.assign` permission

### Issue 2: Modal shows but members don't load
**Solution**: 
- Check project has members assigned
- Check API endpoint `/api/v1/projects/{projectKey}/members` returns data
- Check CORS/fetch errors in console

### Issue 3: "Link Issue" fails with "Issue not found"
**Solution**: 
- Verify target issue key is correct
- Issue must be in same project or accessible to user
- Check spelling exactly (case-sensitive)

### Issue 4: Log Work shows but won't submit
**Solution**:
- Verify time_spent is a valid number
- Check started_at datetime is valid format
- Ensure user has `issues.log_work` permission

## Status

✅ **All features implemented:**
- Modals created (lines 686-772 in views/issues/show.php)
- JavaScript functions created (lines 1384-1576)
- Controller methods exist (assign, link, logWork)
- API endpoints registered
- Database tables ready (issue_links, worklogs)

✅ **What was done:**
1. Added 3 Bootstrap modals for forms
2. Added JavaScript to load dropdowns and submit forms
3. Connected to existing proven controller methods
4. Added global variables (issueKey, projectKey, projectId)

Ready for testing and production use.
