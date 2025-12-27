# Quick Create Modal Debug Instructions

## Issue
When creating an issue from the quick create modal with attachments, you get:
```
Error creating issue: Issue created but key extraction failed.
```

The console shows the response is a **projects list** instead of an **issue creation response**.

## New Debug Logs Added

The code has been updated with comprehensive logging. Follow these steps:

### 1. **Clear Browser Cache**
```
Press: CTRL + SHIFT + DEL
Select: All time
Check: Cached images and files
Click: Clear data
```

### 2. **Hard Refresh Page**
```
Press: CTRL + F5
Wait for page to fully load
```

### 3. **Open Browser Console**
```
Press: F12
Click: Console tab
```

### 4. **Look for Project Key Extraction Logs**
When you click "Create" button, look for these lines in console:
```
[SUBMIT] Project ID: {the number selected}
[SUBMIT] Project Key from dataset: {should be like "DEVOPS", "ECOM", etc}
[SUBMIT] ✅ URL computed: {should be like "/projects/DEVOPS/issues"}
```

### 5. **Look for URL Posted To**
```
[SUBMIT] Posting to URL: /projects/{KEY}/issues
[SUBMIT] ✓ Response received - status: 200
[SUBMIT] Response URL (after redirects): {check if this changed}
```

### 6. **Check Response Content**
```
[SUBMIT] Response content-type: {should be "application/json"}
[SUBMIT] Raw response text length: {number}
[SUBMIT] Full response text: {should show JSON, not HTML}
```

## Expected Output

**SUCCESS** - You should see something like:
```
[SUBMIT] Project Key from dataset: DEVOPS
[SUBMIT] ✅ URL computed: /projects/DEVOPS/issues
[SUBMIT] Posting to URL: /projects/DEVOPS/issues
[SUBMIT] ✓ Response received - status: 200
[SUBMIT] Response URL: http://localhost:8081/jira_clone_system/public/projects/DEVOPS/issues
[SUBMIT] Response content-type: application/json
[SUBMIT] Full response text: {"success":true,"issue_key":"DEVOPS-123","issue":{...}}
```

**FAILURE** - If you see:
```
[SUBMIT] Posting to URL: /projects/{KEY}/issues
[SUBMIT] Response text: {
  "items": [...],
  "total": 6,
  ...
}
```

This means the project key is literally "{KEY}" (not interpolated) OR the request is redirecting to `/projects/quick-create-list`.

## Next Steps

1. **Take a screenshot** of the console output starting from `[SUBMIT] Starting API request...` to `[SUBMIT] Full response text`
2. **Copy-paste** the complete log output
3. **Share** with development team

This will help identify:
- Is the project key being extracted correctly?
- What URL is actually being requested?
- Where is the response coming from?
- Is there a redirect happening?

## If Still Failing

Check these files:
- `views/layouts/app.php` - Line 2025 (projectsMap initialization)
- `views/layouts/app.php` - Line 2016 (dataset.projectKey setting)
- `views/layouts/app.php` - Line 2507 (project key extraction in submit)
- `routes/web.php` - Line 103 (issue store route)
- `src/Controllers/IssueController.php` - Line 120-188 (store method)
