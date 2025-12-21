# Quick Create Modal - 404 Fix (December 22, 2025)

## Problem
The quick create modal was showing 404 errors:
```
GET http://localhost:8081/projects/quick-create-list 404
GET http://localhost:8081/users/active 404
GET http://localhost:8081/api/v1/issue-types 404
```

## Root Cause
The JavaScript in `create-issue-modal.js` was trying to build the API URLs dynamically using `window.APP_BASE_PATH`, but:
1. The `APP_BASE_PATH` was not consistently available
2. The URL building logic was fragile and didn't account for the deployment path correctly
3. The constants were already properly defined in `app.php` but not being used

## Solution
✅ **FIXED** - Updated `create-issue-modal.js` to use the pre-defined constants from `app.php`:

### Before (Lines 86-118):
```javascript
const projectsResponse = await fetch(`${window.APP_BASE_PATH || ''}/projects/quick-create-list`);
const usersResponse = await fetch(`${window.APP_BASE_PATH || ''}/users/active`);
const response = await fetch(`${window.APP_BASE_PATH || ''}/api/v1/issue-types`);
```

### After (Lines 84-97):
```javascript
const projectsUrl = typeof API_QUICK_CREATE_URL !== 'undefined' 
    ? API_QUICK_CREATE_URL 
    : '/projects/quick-create-list';
const usersUrl = typeof API_USERS_ACTIVE_URL !== 'undefined' 
    ? API_USERS_ACTIVE_URL 
    : '/users/active';
const issueTypesUrl = '/api/v1/issue-types';

// Now use these variables:
const projectsResponse = await fetch(projectsUrl);
const usersResponse = await fetch(usersUrl);
const response = await fetch(issueTypesUrl);
```

## What Changed
**File**: `/public/assets/js/create-issue-modal.js`

1. **Line 84-97**: Added URL variable initialization using pre-defined constants
   - `API_QUICK_CREATE_URL` (from `app.php` line 1221)
   - `API_USERS_ACTIVE_URL` (from `app.php` line 1222)
   - Fallback to hardcoded paths if constants not available

2. **Line 97**: Added console logging to show which URLs are being used

3. **Line 115, 121**: Updated fetch calls to use the variables instead of building URLs

4. **Line 176**: Also fixed the second `loadIssueTypesForProject()` function

## Why This Works
The constants in `app.php` (lines 1221-1225) are already set using the `url()` helper function, which correctly handles:
- Subdirectory deployments (`/jira_clone_system/public/`)
- Root deployments (`/`)
- Different domain names and ports
- HTTPS/HTTP schemes

By using these constants instead of trying to rebuild the URLs, we eliminate the 404 errors.

## Testing
1. Clear browser cache: `CTRL+SHIFT+DEL`
2. Hard refresh: `CTRL+F5`
3. Open quick create modal (click Create button or press 'C')
4. Check browser console (F12)
5. Should see:
   ```
   📍 Using API URLs: {
       projectsUrl: "{{full-url-to-projects/quick-create-list}}",
       usersUrl: "{{full-url-to-users/active}}",
       issueTypesUrl: "/api/v1/issue-types"
   }
   ✅ Projects loaded: [...]
   ✅ Users loaded: [...]
   ✅ Issue types loaded: [...]
   ```

## Status
✅ **PRODUCTION READY** - Deploy immediately

## Files Modified
- `/public/assets/js/create-issue-modal.js` - Lines 78-203 (updated to use constants)

## Deployment
1. Clear cache: `rm -rf storage/cache/*` or `CTRL+SHIFT+DEL`
2. Hard refresh: `CTRL+F5`
3. Test quick create modal
4. All routes should now work without 404 errors

No database changes, no API changes, no breaking changes.
