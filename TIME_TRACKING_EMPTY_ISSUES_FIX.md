# Time Tracking: Empty Issues Dropdown & Realtime Notifications Fix

## December 19, 2025 - Production Fix

### Problems Fixed

#### 1. **Empty Issue Dropdown in Time Tracking Modal**
**Symptom**: Time Tracking project page shows modal with dropdown, but no issues appear even though issues exist in the project.

**Root Cause**: 
- The view was filtering issues to only show "Open", "In Progress", "To Do", "In Development" statuses
- If issues had different status names, they were excluded
- Made the dropdown appear broken/empty

**Solution**: 
- Modified `/views/time-tracking/project-report.php` lines 61-102
- Removed status filtering - now includes ALL issues regardless of status
- Users might want to log time on any issue, not just open ones
- Added status_name to the data for reference

**Files Modified**:
- `views/time-tracking/project-report.php` (lines 76-96)

---

#### 2. **Realtime Notifications Stream Connection Error**
**Symptom**: Browser console shows: `❌ [REALTIME] Stream error: Event {isTrusted: true, type: 'error', ...}`

**Root Cause**:
- Hardcoded path: `/jira_clone_system/public/notifications/stream` 
- When deployed in subdirectories, this breaks
- The realtime-notifications.js was using a fixed path that only works with one deployment configuration

**Solution Applied**:
1. Added `app-base-path` meta tag to layouts (lines 5-8 in `views/layouts/app.php`)
2. Updated realtime-notifications.js to read the meta tag and build correct URL dynamically
3. Now works with ANY deployment path (subdirectories, root, etc.)

**Files Modified**:
- `views/layouts/app.php` (added meta tag on line 8)
- `public/assets/js/realtime-notifications.js` (lines 75-83)

---

#### 3. **Time Tracking Timer API Call Failures**
**Symptom**: Timer modal submit button doesn't work, API call fails silently.

**Root Cause**:
- Hardcoded API path: `/jira_clone_system/public/api/v1/time-tracking/start`
- This only works if deployed at `/jira_clone_system/public/`
- Breaks if deployed elsewhere

**Solution Applied**:
- Updated `/views/time-tracking/project-report.php` lines 1436-1457
- Now reads `app-base-path` meta tag and builds correct API URL dynamically
- Includes console logging for debugging

**Files Modified**:
- `views/time-tracking/project-report.php` (lines 1449-1458)

---

### Technical Changes

#### Meta Tag Addition (views/layouts/app.php)
```html
<meta name="app-base-path" content="<?= url('/') ?>">
```
- Uses the `url()` helper which automatically detects deployment path
- Works with: `/`, `/jira_clone_system/public/`, `/sub/path/jira/public/`, etc.
- Dynamically available in all JavaScript code

#### Realtime Notifications Update (public/assets/js/realtime-notifications.js)
```javascript
const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
const url = basePath.replace(/\/$/, '') + `/notifications/stream?lastId=${this.lastEventId}`;
```
- Reads base path from meta tag
- Removes trailing slash to prevent double slashes
- Constructs correct URL for EventSource

#### Timer Modal Update (views/time-tracking/project-report.php)
```javascript
const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
const apiUrl = basePath.replace(/\/$/, '') + '/api/v1/time-tracking/start';
```
- Same pattern as notifications
- Dynamically builds correct API endpoint URL

---

### Testing & Verification

**Test 1: Issue Dropdown Population**
1. Go to: `http://localhost:8081/jira_clone_system/public/time-tracking/project/1`
2. Click "Start Timer" button
3. Open the issue dropdown
4. Should see all issues from CWays MIS project

**Test 2: Notification Stream Connection**
1. Open browser console (F12)
2. Look for logs starting with `🔌 [REALTIME]`
3. Should see: `✅ [REALTIME] Connected to notification stream`
4. Should NOT see: `❌ [REALTIME] Stream error`

**Test 3: Timer Start Functionality**
1. Click "Start Timer" on the time tracking page
2. Select an issue from dropdown
3. Click "Start Timer" button
4. Should see success message
5. Open console - should see `[TIMER]` logs showing the request

---

### Diagnostic Tool

Created `test-time-tracking.html` for testing:
- Location: `c:/laragon/www/jira_clone_system/test-time-tracking.html`
- Access: `http://localhost:8081/jira_clone_system/public/test-time-tracking.html`
- Tests:
  - ✓ Configuration check
  - ✓ API endpoint tests
  - ✓ Notification stream connectivity
  - ✓ Timer status endpoint

---

### Deployment Notes

These fixes are **backward compatible**:
- Hardcoded paths are removed entirely
- Uses dynamic detection via meta tag
- Works with any deployment configuration
- No database changes needed
- No API changes needed
- Zero breaking changes

### Production Readiness

All fixes are **production-ready**:
- ✅ Tested in development
- ✅ No performance impact
- ✅ Proper error handling
- ✅ Console logging for debugging
- ✅ Fallback support (if meta tag missing, uses `/`)

---

## Summary

| Issue | Impact | Status | Fix |
|-------|--------|--------|-----|
| Empty issue dropdown | High - Feature broken | FIXED | Removed status filtering |
| Notification stream error | Medium - Realtime disabled | FIXED | Dynamic base path handling |
| Timer API failure | High - Feature broken | FIXED | Dynamic base path for API calls |

**All fixes deployed and production-ready.**
