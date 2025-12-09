# Issue Resolution: 404 Error on Issue Transition

## Problem
When attempting to transition an issue by clicking the "Transition" button in the issue detail view, the application was returning a 404 error from Apache instead of properly transitioning the issue.

**Error Response:**
```
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
<hr>
<address>Apache/2.4.58 (Win64) OpenSSL/3.1.3 PHP/8.2.12 Server at localhost Port 8080</address>
</body></html>
```

## Root Cause
The JavaScript code in `views/issues/show.php` was making AJAX fetch requests using absolute paths starting with `/` (e.g., `/issue/BP-7/transition`). When the application is running in a subdirectory (`/jira_clone_system/public/`), these absolute paths don't work correctly:

1. The browser resolves `/issue/BP-7/transition` relative to the web server root (`http://localhost:8080/`)
2. This results in a request to `http://localhost:8080/issue/BP-7/transition`
3. Apache returns 404 because there's no actual file or directory at that path
4. The request never reaches the PHP application's router, which would properly rewrite it to `index.php`

## Solution
Updated all AJAX fetch calls in `views/issues/show.php` to dynamically construct the proper base URL using JavaScript:

```javascript
const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
```

This extracts the base path from the current URL and constructs absolute paths correctly.

### Files Modified
**`views/issues/show.php`** - Updated 4 fetch calls:

1. **Line 541** - Transition form submission
   - Before: `fetch(`/issue/${issueKey}/transition`, ...)`
   - After: `fetch(`${baseUrl}/issue/${issueKey}/transition`, ...)`

2. **Line 577** - Watch issue function
   - Before: `fetch(`/issue/${issueKey}/${action}`, ...)`
   - After: `fetch(`${baseUrl}/issue/${issueKey}/${action}`, ...)`

3. **Line 597** - Vote issue function
   - Before: `fetch(`/issue/${issueKey}/${action}`, ...)`
   - After: `fetch(`${baseUrl}/issue/${issueKey}/${action}`, ...)`

4. **Line 619** - Add comment function
   - Before: `fetch(`/issue/${issueKey}/comments`, ...)`
   - After: `fetch(`${baseUrl}/issue/${issueKey}/comments`, ...)`

## How It Works
The fix uses the current browser URL to determine the base path:
- Current URL: `http://localhost:8080/jira_clone_system/public/issue/BP-7`
- `window.location.pathname`: `/jira_clone_system/public/issue/BP-7`
- Split by `/public/`: `['/jira_clone_system', '/issue/BP-7']`
- Extract base: `/jira_clone_system` + `/public` = `/jira_clone_system/public`
- Result: Fetch to `http://localhost:8080/jira_clone_system/public/issue/BP-7/transition`

## Testing
After applying the fix:
1. Navigate to any issue detail page
2. Click the transition button
3. Select a status from the modal
4. Click "Transition"
5. The issue should transition successfully without 404 errors

## Why This Pattern Matters
This fix applies to ANY fetch calls that need to work regardless of where the application is deployed:
- Development: `http://localhost/jira_clone_system/public/`
- Sub-path: `http://example.com/jira/public/`
- Root: `http://example.com/public/`

The dynamic base URL ensures AJAX requests work correctly in all deployment scenarios.

## Related Issues
This issue affected any AJAX functionality that used hardcoded absolute paths:
- Issue transitions ✅ Fixed
- Watching issues ✅ Fixed
- Voting on issues ✅ Fixed
- Adding comments ✅ Fixed

## Prevention
When adding new AJAX functionality in the future:
1. Use PHP's `url()` helper function for links: `<?= url('/route') ?>`
2. For JavaScript fetch calls, always calculate base URL dynamically
3. Consider creating a global JavaScript utility function:

```javascript
function getBaseUrl() {
    return window.location.pathname.split('/public/')[0] + '/public';
}
```

Then use it in all fetch calls:
```javascript
fetch(`${getBaseUrl()}/api/endpoint`, options)
```

---
**Resolution Date:** December 5, 2025  
**Status:** ✅ RESOLVED
