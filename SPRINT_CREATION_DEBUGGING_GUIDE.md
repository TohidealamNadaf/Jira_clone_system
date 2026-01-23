# Sprint Creation Debugging Guide - January 12, 2026

## Critical Fix Applied

### Main Issue: Validation Error Handling
**Problem**: The controller was using `$request->validate()` which redirects on error instead of returning JSON for AJAX requests.

**Solution**: Changed to check request type and use `$request->validateApi()` for JSON requests.

### Secondary Issue: Accept Header
**Problem**: JavaScript wasn't sending `Accept: application/json` header.

**Solution**: Added `Accept: application/json` to fetch request headers.

## Files Modified

### 1. `/src/Controllers/ProjectController.php` - Sprint Creation Handler
- Added `$request->isJson()` check
- Uses `validateApi()` for JSON requests (returns JSON errors)
- Uses `validate()` for form requests (redirects on error)
- Added comprehensive `error_log()` statements with `[SPRINT]` prefix
- Added catch block for generic exceptions
- Better error responses with status codes (422 validation, 500 server error)

### 2. `/views/projects/sprints.php` - JavaScript Error Handling
- Added `Accept: application/json` header to fetch request
- Enhanced error parsing to handle validation errors array
- Added 500ms delay before reload to allow server to finish
- Better console error messages showing response status code
- Handles both `data.error` and `data.errors` (validation array)

## How to Diagnose Issues

### Step 1: Check PHP Error Log
```bash
# On Windows with XAMPP:
C:\xampp\apache\logs\error.log
# OR
C:\xampp\logs\php_error.log

# Look for lines starting with [SPRINT]
```

**Example output:**
```
[SPRINT] Starting sprint creation for project: CWAYSMIS
[SPRINT] Project found, checking authorization
[SPRINT] JSON request detected, using validateApi
[SPRINT] Validated data: {"name":"Sprint 1","goal":null,"start_date":null,"end_date":null}
[SPRINT] Using board ID: 5
[SPRINT] Creating sprint with service
[SPRINT] Sprint created successfully with ID: 3
[SPRINT] Returning JSON response
```

### Step 2: Check Browser Console
```
F12 → Console tab → Look for [SPRINT-FORM] messages
```

**Expected sequence:**
```
[SPRINT-FORM] Sprint form initialized successfully
[SPRINT-FORM] Opening create sprint modal
[SPRINT-FORM] Form submitted
[SPRINT-FORM] Form data: {name: "Sprint 1", ...}
[SPRINT-FORM] Posting to: http://localhost:8080/cways_mis/public/projects/CWAYSMIS/sprints
[SPRINT-FORM] Response status: 201
[SPRINT-FORM] Sprint created successfully, reloading...
```

### Step 3: Check Network Tab
```
F12 → Network tab → Filter: XHR
1. Click "Create Sprint" button
2. Look for POST request to /projects/CWAYSMIS/sprints
3. Click on request and check:
   - Request Headers → Look for "Accept: application/json"
   - Response → Should show JSON like {"success":true,"sprint":{...}}
   - Status → Should be 201 (or 422 if validation error)
```

## Common Issues & Solutions

### Issue 1: Page Reloads But No Sprint Appears

**Symptoms:**
- Console shows `Response status: 201`
- Page reloads but no new sprint in list
- No error messages

**Causes & Solutions:**

1. **Sprint not being created in database**
   - Check PHP error log for `[SPRINT]` messages
   - Verify board exists: `SELECT * FROM boards WHERE project_id = (SELECT id FROM projects WHERE key='CWAYSMIS');`
   - Check sprints table: `SELECT * FROM sprints WHERE board_id = 5;`

2. **Caching issue**
   - Clear browser cache: CTRL + SHIFT + DEL → All time
   - Hard refresh: CTRL + F5
   - Check browser DevTools → Application → Cache Storage → Clear

### Issue 2: Error Message Appears

**Symptoms:**
- Red error box appears in modal
- Console shows `Response status: 422` or `500`
- Page doesn't reload

**Causes & Solutions:**

1. **Validation error (422 status)**
   - Check console for validation error details
   - Common: Empty sprint name (required field)
   - Check Network tab → Response to see exact errors
   - Example: `{"errors":{"name":["The name field is required"]}}`

2. **Server error (500 status)**
   - Check PHP error log for `[SPRINT] Exception:` message
   - Common: No board found for project
   - Solution: Create a board first
   - Check: `SELECT * FROM boards WHERE project_id = ...`

3. **Authorization error (403 status)**
   - Current user doesn't have `projects.edit` permission
   - Check user roles: `SELECT * FROM user_roles WHERE user_id = ...`
   - Check role permissions: `SELECT * FROM role_permissions WHERE role_id = ...`

### Issue 3: Modal Closes But Page Doesn't Reload

**Symptoms:**
- Modal closes
- Error message appears then disappears
- Page doesn't reload
- Console shows error

**Causes & Solutions:**

1. **JSON parsing error**
   - Server returned non-JSON response (HTML)
   - Check Network tab → Response tab
   - Should be JSON, not HTML
   - Likely validation error treated as redirect

2. **Network error**
   - Check Network tab for CORS errors
   - Verify fetch URL is correct: `/projects/{KEY}/sprints`
   - Check if `Content-Type` and `Accept` headers are set correctly

### Issue 4: "No board found" Error

**Symptoms:**
- Error: "No board found for this project. Please create a board first."
- Status: 404

**Solution:**
1. Check if project has a board:
```sql
SELECT * FROM boards WHERE project_id = (SELECT id FROM projects WHERE key='CWAYSMIS');
```

2. If no boards exist, create one:
   - Go to project settings
   - Create a Scrum or Kanban board
   - Then try sprint creation again

## Testing Procedure

### Step 1: Preparation
```bash
# Clear all caches
# 1. Browser cache: CTRL + SHIFT + DEL → All time → Clear
# 2. Browser local storage: F12 → Application → Local Storage → Clear
# 3. XAMPP cache: Delete files in C:\xampp\htdocs\cways_mis\storage\cache\
```

### Step 2: Navigate to Sprints Page
```
http://localhost:8080/cways_mis/public/projects/CWAYSMIS/sprints
```

### Step 3: Open Developer Tools
```
F12 → Console tab (keep open throughout)
```

### Step 4: Create Sprint
1. Click "Create Sprint" button
2. Fill in:
   - Sprint Name: "Test Sprint 1"
   - Goal: "Test goal" (optional)
   - Start Date: (leave empty or set)
   - End Date: (leave empty or set)
3. Click "Create Sprint" button
4. Watch console for logs

### Step 5: Analyze Results

**Success (201 Response):**
```
[SPRINT-FORM] Response status: 201
[SPRINT-FORM] Sprint created successfully, reloading...
(page reloads automatically)
(new sprint appears in list)
```

**Validation Error (422 Response):**
```
[SPRINT-FORM] Response status: 422
[SPRINT-FORM] Error response (status 422): {"errors":{"name":["The name field is required"]}}
(error message appears in modal)
```

**Server Error (500 Response):**
```
[SPRINT-FORM] Response status: 500
[SPRINT-FORM] Error response (status 500): {"error":"Error message here"}
(error message appears in modal)
```

**Network Error:**
```
[SPRINT-FORM] Exception: TypeError: Failed to fetch
(check Network tab for CORS or connection errors)
```

## Checking Database After Sprint Creation

```sql
-- Check if sprint was created
SELECT * FROM sprints WHERE board_id = 5 ORDER BY id DESC LIMIT 1;

-- Should return a row like:
-- id | board_id | name | goal | status | created_at
-- 3  | 5        | Sprint 1 | NULL | future | 2026-01-12 ...

-- If no rows returned, sprint creation failed in database
-- Check PHP error log for exceptions
```

## Advanced Troubleshooting

### Enable Debug Mode
Edit `config/config.php`:
```php
'debug' => true,  // Enable detailed error messages
```

Then check Network tab → Response for detailed error info.

### Check CSRF Token
```javascript
// In browser console
document.querySelector('meta[name="csrf-token"]')?.content
// Should return a long string, not undefined
```

### Verify Project Exists
```sql
SELECT * FROM projects WHERE key = 'CWAYSMIS';
```

### Verify User Has Permission
```sql
-- Check user's roles
SELECT r.name FROM roles r
JOIN user_roles ur ON r.id = ur.role_id
WHERE ur.user_id = (SELECT id FROM users WHERE email = 'your@email.com');

-- Check if role has permission
SELECT p.name FROM permissions p
JOIN role_permissions rp ON p.id = rp.permission_id
JOIN roles r ON r.id = rp.role_id
WHERE r.name = 'Your Role'
AND p.name LIKE '%project%';
```

## Production Checklist

- [ ] Clear browser cache (CTRL + SHIFT + DEL)
- [ ] Hard refresh page (CTRL + F5)
- [ ] Open DevTools Console (F12)
- [ ] Fill sprint creation form completely
- [ ] Click "Create Sprint"
- [ ] Check console for `[SPRINT-FORM]` logs
- [ ] Verify Network tab shows POST 201 response
- [ ] Page reloads automatically
- [ ] New sprint appears in list without page refresh
- [ ] No error messages appear
- [ ] Repeat 2-3 times to verify consistency

## Key Code Changes

### Controller Method
```php
// OLD: Always redirects on validation error
$data = $request->validate([...]);

// NEW: Checks request type
if ($request->isJson()) {
    $data = $request->validateApi([...]);  // Returns JSON 422
} else {
    $data = $request->validate([...]);      // Redirects on error
}
```

### JavaScript Headers
```javascript
// OLD: Missing Accept header
headers: {
    'Content-Type': 'application/json',
    'X-CSRF-Token': '...'
}

// NEW: Includes Accept header
headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',  // ← ADDED
    'X-CSRF-Token': '...'
}
```

## Support

If issues persist after following this guide:
1. Provide PHP error log excerpt (lines with `[SPRINT]`)
2. Provide browser console screenshot (F12)
3. Provide Network tab request/response (F12 → Network)
4. Describe exact steps taken to reproduce issue
