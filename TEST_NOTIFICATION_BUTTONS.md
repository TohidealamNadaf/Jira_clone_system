# Notification Buttons Testing Guide

**Status**: ✅ Ready to Test  
**Date**: December 7, 2025

---

## Quick Test (5 minutes)

### Prerequisites
- ✅ You must be logged in to your account
- ✅ You must have at least one notification (create a test issue in a project)

### Test Steps

#### 1. Navigate to Notifications Page
```
1. Click the bell icon in the top navigation (if you have unread notifications)
   OR
2. Visit: http://localhost:8080/jira_clone_system/public/notifications
```

#### 2. Test Mark as Read Button
```
EXPECTED STATE:
- You see a list of notifications
- Unread notifications have a blue left border and light blue background
- Unread notifications show a "New" badge in the title
- Unread notifications have a circle icon (mark as read button)

PERFORM ACTION:
1. Find an unread notification in the list
2. Click the circle icon on the right side (Mark as Read button)
3. Wait 1-2 seconds

EXPECTED RESULT:
✅ Notification should:
   - Lose the blue left border
   - Change to white background
   - Remove the "New" badge
   - Remove the circle button
   - Stay in the same position
✅ Browser console should show:
   - "Marking notification as read: [ID]"
   - "Response status: 200"
   - "Response data: {status: "success", ...}"
✅ No errors like "Unauthenticated"
```

#### 3. Test Delete Button
```
EXPECTED STATE:
- All notifications show a trash icon on the right side (delete button)

PERFORM ACTION:
1. Find any notification in the list
2. Click the trash icon on the right side
3. Confirm the deletion in the popup dialog
4. Wait 1-2 seconds

EXPECTED RESULT:
✅ Notification should:
   - Fade out and disappear from the list
   - Be removed completely from the page
✅ Browser console should show:
   - "Deleting notification: [ID]"
   - "Response status: 200"
   - "Response data: {status: "success"}"
✅ Stats in sidebar should update:
   - Total count should decrease by 1
```

#### 4. Test Mark All as Read Button
```
PREREQUISITES:
- You must have at least 2 unread notifications

EXPECTED STATE:
- You see a blue "Mark All as Read" button in the top right
- Multiple unread notifications in the list

PERFORM ACTION:
1. Click the "Mark All as Read" button (blue button in header)
2. Wait 1-2 seconds

EXPECTED RESULT:
✅ All unread notifications should:
   - Lose the blue left border
   - Change to white background
   - Remove the "New" badge
   - Remove the circle button
✅ The "Mark All as Read" button should:
   - Disappear completely from the page
✅ Stats in sidebar should update:
   - Unread count should become 0
✅ Browser console should show:
   - "Marking all notifications as read"
   - "Response status: 200"
   - "Response data: {status: "success", unread_count: 0}"
```

---

## Full Test Suite (15 minutes)

### Test 1: Page Load & Authentication

**File**: Open browser Developer Tools (F12)  
**Tab**: Console

**Steps**:
```
1. Log in to your account
2. Navigate to /notifications
3. Check console for errors

EXPECTED RESULT:
✅ Page loads successfully
✅ No 401 errors
✅ No "Unauthenticated" messages
✅ See notification list
```

### Test 2: Mark Single as Read

**Steps**:
```
1. Find unread notification (blue border)
2. Click the circle icon (mark-read-btn)
3. Check network tab in dev tools
4. Verify response

EXPECTED:
✅ Request URL: /api/v1/notifications/{id}/read
✅ Method: PATCH
✅ Status: 200
✅ Response body: {"status":"success","unread_count":N}
✅ UI updates immediately
✅ No page reload
```

### Test 3: Delete Single Notification

**Steps**:
```
1. Find any notification
2. Click the trash icon (delete-btn)
3. Confirm in dialog
4. Check network tab
5. Verify response

EXPECTED:
✅ Request URL: /api/v1/notifications/{id}
✅ Method: DELETE
✅ Status: 200
✅ Response body: {"status":"success"}
✅ Notification removed from list
✅ Total count decreases
```

### Test 4: Mark All as Read

**Steps**:
```
1. Ensure 2+ unread notifications exist
2. Click "Mark All as Read" button
3. Check network tab
4. Verify response

EXPECTED:
✅ Request URL: /api/v1/notifications/read-all
✅ Method: PATCH
✅ Status: 200
✅ Response body: {"status":"success","unread_count":0}
✅ All unread notifications become read
✅ Button disappears
✅ Unread count = 0
```

### Test 5: Session Validation

**Steps**:
```
1. Open browser dev tools → Console
2. Add test code:
   localStorage.setItem('test_auth', 'session_test');
3. Perform mark-as-read action
4. Check cookies

EXPECTED:
✅ PHPSESSID cookie present and valid
✅ Session user data available
✅ API request succeeds with session auth
```

### Test 6: CSRF Protection

**Steps**:
```
1. Open browser dev tools → Network
2. Perform a mark-as-read action
3. Select the PATCH request
4. Check request headers

EXPECTED:
✅ Header: X-CSRF-Token present
✅ Value: Non-empty token
✅ Response: 200 (CSRF validation passed)
```

---

## Browser Console Monitoring

### What You Should See (Good)

```javascript
// Mark as read
"Marking notification as read: 123"
"Fetching: /jira_clone_system/public/api/v1/notifications/123/read"
"CSRF Token: eyJ0eXAi..." (first 10 chars)
"Response status: 200"
"Response data: {status: "success", unread_count: 5}"

// Delete
"Deleting notification: 456"
"Fetching: /jira_clone_system/public/api/v1/notifications/456"
"Response status: 200"
"Response data: {status: "success"}"

// Mark all as read
"Marking all notifications as read"
"Fetching: /jira_clone_system/public/api/v1/notifications/read-all"
"Response status: 200"
"Response data: {status: "success", unread_count: 0}"
```

### What You Should NOT See (Bad)

```javascript
// These would indicate problems:
"Unauthenticated"
"Invalid or missing authentication token"
"Unauthorized action"
"CSRF token mismatch"
"404 Not Found"
"500 Internal Server Error"
```

---

## Debugging Steps

### If Buttons Don't Work

**1. Check Session is Active**
```
Press F12 → Application → Cookies
Look for: PHPSESSID
Value should be: non-empty alphanumeric string
```

**2. Check Console for Errors**
```
Press F12 → Console
Look for red error messages
Copy and search for solution
```

**3. Verify CSRF Token Exists**
```
Right-click page → View Page Source
Search for: csrf-token
Should find: <meta name="csrf-token" content="eyJ0eXA...">
```

**4. Check Network Requests**
```
Press F12 → Network
Click "Mark as Read"
Find the PATCH request to /api/v1/notifications/{id}/read
Check:
  - Status should be 200 (not 401, 403, 404, 500)
  - Response should show: {"status":"success",...}
```

**5. Check Notification Count**
```
Expected in database:
- Count unread notifications for your user
  SELECT COUNT(*) FROM notifications 
  WHERE user_id = 1 AND is_read = 0;
- Should match the UI count
```

---

## Common Issues & Solutions

### Issue: "Unauthenticated" Error 401

**Cause**: Session auth not working  
**Solution**:
1. Make sure you're logged in (check navbar shows your name)
2. Clear browser cookies and log in again
3. Check server logs: `storage/logs/app.log`
4. Restart your browser

### Issue: CSRF Token Mismatch

**Cause**: CSRF token missing or invalid  
**Solution**:
1. Check page source has `<meta name="csrf-token">`
2. Hard refresh page: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
3. Check CSRF middleware is enabled
4. Verify you're not using incognito mode (can cause session issues)

### Issue: Notification Count Incorrect

**Cause**: Database not updated or cache issue  
**Solution**:
1. Refresh the page
2. Check actual database count
3. Clear browser cache
4. Check if multiple tabs are open (can cause sync issues)

### Issue: Network Error / No Response

**Cause**: Server not responding  
**Solution**:
1. Check server is running
2. Check URL is correct
3. Check internet connection
4. Restart Apache/PHP

---

## Expected Behavior Summary

| Action | Before | After | Console |
|--------|--------|-------|---------|
| Click Mark Read | Unread (blue border) | Read (white) | Status 200 |
| Click Delete | Notification visible | Removed | Status 200 |
| Click Mark All Read | Multiple unread | All read | Status 200 |
| No auth | Login page | Session active | No 401 error |
| Bad CSRF | Should fail | CSRF header sent | Token present |

---

## Performance Expectations

| Action | Expected Time | Notes |
|--------|---------------|-------|
| Mark as Read | < 500ms | Instant UI update |
| Delete | < 500ms | Notification fades out |
| Mark All Read | < 1s | Batch operation |
| Page Load | < 2s | May vary with network |

---

## Test Checklist

- [ ] ✅ Page loads without errors
- [ ] ✅ Notification list displays correctly
- [ ] ✅ Mark as Read button works
- [ ] ✅ Delete button works
- [ ] ✅ Mark All as Read button works
- [ ] ✅ UI updates without page reload
- [ ] ✅ Browser console shows no errors
- [ ] ✅ Network shows 200 status codes
- [ ] ✅ CSRF token present in requests
- [ ] ✅ Session is validated
- [ ] ✅ Notification counts match database
- [ ] ✅ Stats sidebar updates

---

## Success Criteria

✅ All buttons work without "Unauthenticated" error  
✅ UI updates immediately (no page reload)  
✅ Network requests return status 200  
✅ Browser console shows no errors  
✅ Database is updated correctly  

---

## Next Steps After Testing

1. If tests pass:
   - ✅ Deploy to production
   - ✅ Notify users about new notifications feature
   - ✅ Monitor logs for any issues

2. If tests fail:
   - Check the debugging steps above
   - Review NOTIFICATION_AUTHENTICATION_FIX.md
   - Check browser console and network tab
   - Verify all three files were modified correctly

---

**Ready to Test**: 🚀

Start with the Quick Test (5 minutes), then proceed to Full Test Suite if you want comprehensive verification.
