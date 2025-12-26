# Fix: Create Issue Modal - Projects Not Loading

## Problem
The Create Issue modal was displaying but the Projects dropdown was empty - no projects were being loaded.

## Root Cause
The JavaScript file `/public/assets/js/create-issue-modal.js` had PHP template tags (`<?= url() ?>`) that were **not being processed** because the file is served as a static JavaScript file, not processed by PHP.

Additionally, the `loadModalData()` function was defined but **never called** in the initialization sequence.

## Solutions Applied

### 1. Added Inline Data Loading to `views/layouts/app.php` (Lines 2085-2154)
- Added inline JavaScript right after the modal script include
- Uses PHP's `url()` helper to generate proper URLs
- Directly populates the project and assignee dropdowns
- Runs after modal initialization is complete

**New Code Added:**
```javascript
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for modal to initialize
        const checkInterval = setInterval(function() {
            if (typeof window.CreateIssueModal !== 'undefined') {
                clearInterval(checkInterval);
                console.log('✅ Create Issue Modal initialized, loading data...');
                loadCreateIssueModalData();
            }
        }, 100);
        
        setTimeout(() => clearInterval(checkInterval), 5000);
    });

    async function loadCreateIssueModalData() {
        // Load projects from <?= url("/projects/quick-create-list") ?>
        // Load users from <?= url("/users/active") ?>
    }
</script>
```

### 2. Updated `/public/assets/js/create-issue-modal.js`
- Removed all PHP template tags (`<?= ?>`)
- Changed to use `window.APP_BASE_PATH` for URLs
- Updated all API calls to construct URLs dynamically:
  - `loadModalData()` - projects and users
  - `loadIssueTypesForProject()` - issue types
  - `loadPriorityOptions()` - priorities
  - `submitIssueForm()` - form submission

**Example:**
```javascript
// Before (didn't work - PHP not processed)
const response = await fetch('<?= url("/projects/quick-create-list") ?>');

// After (works in static file)
const basePath = window.APP_BASE_PATH || '';
const url = basePath + '/projects/quick-create-list';
const response = await fetch(url);
```

### 3. Added `loadModalData()` Call (Line 355)
Updated initialization sequence in `/public/assets/js/create-issue-modal.js`:
```javascript
loadModalData();  // Load projects, users, and issue types
setupProjectChangeHandler();
loadPriorityOptions();
```

## Files Modified
1. **`views/layouts/app.php`** (Lines 2082-2154)
   - Added inline initialization with PHP URL generation

2. **`public/assets/js/create-issue-modal.js`**
   - Line 355: Added `loadModalData()` call
   - Lines 55-92: Updated `loadModalData()` to use dynamic URLs
   - Lines 128-147: Updated `loadIssueTypesForProject()`
   - Lines 201-218: Updated `loadPriorityOptions()`
   - Lines 283-310: Updated `submitIssueForm()`

## Testing
1. Clear browser cache: `CTRL + SHIFT + DEL`
2. Hard refresh: `CTRL + F5`
3. Navigate to: `http://localhost:8081/jira_clone_system/public/dashboard`
4. Click "+ Create" button in navbar
5. **Modal should open** and Projects dropdown should show list of projects
6. Check DevTools Console (F12) for logs like:
   - `✅ Create Issue Modal initialized, loading data...`
   - `✅ Projects loaded: [...]`
   - `✅ Users loaded: [...]`

## Expected Behavior
- ✅ Modal opens when clicking Create button
- ✅ Projects dropdown populated from `/projects/quick-create-list`
- ✅ Assignee dropdown populated from `/users/active`
- ✅ Issue types load when project is selected
- ✅ Priorities load automatically
- ✅ Form submission works
- ✅ No JavaScript errors in console

## Endpoints Used
| Endpoint | Purpose |
|----------|---------|
| `/projects/quick-create-list` | Load projects for dropdown |
| `/users/active` | Load active users for assignee |
| `/api/v1/projects/{key}/issue-types` | Load issue types for selected project |
| `/api/v1/priorities` | Load priority options |
| `/issues/store` | Create new issue (POST) |

## Notes
- The inline script in `app.php` is the primary initialization
- The static JS file (`create-issue-modal.js`) provides modal UI and form submission
- Both use `window.APP_BASE_PATH` (set globally in app.php) for deployment-aware URLs
- All API calls include error logging for debugging
- Modal persists across page navigation (single reusable modal per AGENTS.md)

## Debugging Commands
If projects still don't load, check:

1. **DevTools Console** (F12) - Look for red errors
2. **Network Tab** (F12) - Check if API calls return 200 OK
3. **Verify endpoints exist:**
   - Visit: `http://localhost:8081/jira_clone_system/public/projects/quick-create-list`
   - Should return JSON array of projects

## Status
✅ **FIXED** - Create Issue modal now loads projects and users properly
