# Comprehensive Notification Preferences Testing Guide

**Status**: Production Testing Required  
**Test Coverage**: Full feature testing  
**Estimated Time**: 15-20 minutes

---

## Pre-Test Checklist

- [ ] Fix deployed (`src/Core/Database.php` updated)
- [ ] Database connection working
- [ ] User authenticated (logged in)
- [ ] Browser console open (F12)
- [ ] No cache issues (clear browser cache)
- [ ] Server error logs accessible

---

## Test Suite

### Test 1: Page Load and Initial State

**Objective**: Verify the preferences page loads correctly with current settings

**Steps**:
1. Navigate to: `http://localhost:8080/jira_clone_system/public/profile/notifications`
2. Wait for page to fully load
3. Observe all notification preference checkboxes

**Expected Results**:
- [ ] Page loads without JavaScript errors
- [ ] All 9 event types visible (issue_created, issue_assigned, etc.)
- [ ] All 3 channels visible (in_app, email, push)
- [ ] Total of 27 checkboxes (9 events × 3 channels)
- [ ] Form "Save Preferences" and "Reset" buttons visible
- [ ] Current user's preferences displayed correctly

**Pass Criteria**: Page loads cleanly with all elements visible

---

### Test 2: Single Checkbox Modification

**Objective**: Verify a single preference can be changed and saved

**Steps**:
1. From initial state, locate: `issue_created` → `in_app` checkbox
2. Click checkbox to toggle (if unchecked, check it; if checked, uncheck it)
3. Click "Save Preferences" button
4. Wait for response

**Expected Results**:
- [ ] No console errors (F12)
- [ ] Success message appears: "Preferences updated successfully"
- [ ] No error message displayed
- [ ] Button shows loading state briefly during save
- [ ] Network tab shows: `PUT /api/v1/notifications/preferences` → 200 OK
- [ ] Success message disappears after ~5 seconds

**Pass Criteria**: Preference saves without errors

---

### Test 3: Multiple Checkbox Modification

**Objective**: Verify multiple preferences can be changed simultaneously

**Steps**:
1. From initial state, modify MULTIPLE checkboxes:
   - `issue_assigned` → `email` (change it)
   - `issue_commented` → `in_app` (change it)
   - `issue_status_changed` → `push` (change it)
2. Click "Save Preferences" button
3. Observe responses

**Expected Results**:
- [ ] No console errors
- [ ] Success message appears
- [ ] All modified preferences save correctly
- [ ] API returns proper response with updated_count > 0

**Pass Criteria**: Multiple preferences save in single request

---

### Test 4: Page Refresh Persistence

**Objective**: Verify saved preferences persist across page refresh

**Steps**:
1. From Test 2, note which preferences were modified
2. Modify several preferences
3. Click "Save Preferences" and wait for success message
4. Press F5 or click refresh button
5. Wait for page to reload completely
6. Check if modified preferences remain as modified

**Expected Results**:
- [ ] Page reloads successfully
- [ ] All previously modified preferences remain modified
- [ ] No console errors during reload
- [ ] Checkboxes match what was saved

**Pass Criteria**: Preferences persist across page refresh

---

### Test 5: Different Event Types

**Objective**: Verify each of the 9 event types can be configured independently

**Steps**:
1. For each event type, modify at least one channel preference:
   - issue_created
   - issue_assigned
   - issue_commented
   - issue_status_changed
   - issue_mentioned
   - issue_watched
   - project_created
   - project_member_added
   - comment_reply
2. Save preferences
3. Refresh page
4. Verify all 9 event types saved correctly

**Expected Results**:
- [ ] All 9 event types save independently
- [ ] No cross-contamination between event types
- [ ] Each event type can have unique channel settings

**Pass Criteria**: All 9 event types configurable and persistent

---

### Test 6: Channel Independence

**Objective**: Verify each channel (in_app, email, push) works independently

**Steps**:
1. For a single event type (e.g., issue_created):
   - Check `in_app` only
   - Uncheck `email` and `push`
2. Save
3. For another event type (e.g., issue_assigned):
   - Uncheck `in_app`
   - Check `email` and `push`
4. Save
5. Refresh and verify different configurations for different event types

**Expected Results**:
- [ ] Each event type can have unique channel combinations
- [ ] No channel settings cross-contaminate between event types
- [ ] All combinations persist correctly

**Pass Criteria**: Channels are independent

---

### Test 7: Reset to Defaults

**Objective**: Verify the "Reset" button restores default settings

**Steps**:
1. Modify multiple preferences (check/uncheck various boxes)
2. Click "Reset" button
3. In confirmation dialog, click "OK"
4. Observe form reset to defaults

**Expected Results**:
- [ ] Confirmation dialog appears
- [ ] After confirmation, all checkboxes reset to defaults:
  - `in_app` → checked (enabled)
  - `email` → checked (enabled)
  - `push` → unchecked (disabled)
- [ ] All 27 checkboxes follow default pattern
- [ ] No save required (reset is immediate)

**Pass Criteria**: Reset button works correctly

---

### Test 8: Error Handling - Invalid Input

**Objective**: Verify system handles invalid data gracefully

**Steps**:
1. Open browser console (F12)
2. Attempt to manually send malformed request:
   ```javascript
   fetch('/api/v1/notifications/preferences', {
       method: 'PUT',
       headers: {
           'Content-Type': 'application/json',
           'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
       },
       body: JSON.stringify({
           preferences: {
               invalid_event_type: { in_app: true }
           }
       })
   }).then(r => r.json()).then(console.log)
   ```
3. Observe response

**Expected Results**:
- [ ] API rejects invalid event type
- [ ] Returns partial_success or error response
- [ ] Logs security event to security.log
- [ ] User sees appropriate error message

**Pass Criteria**: Invalid input handled safely

---

### Test 9: Cross-User Isolation

**Objective**: Verify preferences are user-specific

**Steps**:
1. As User A, modify preferences to unique configuration
2. Save preferences
3. Logout (or open private browser window)
4. Login as different user (User B)
5. Navigate to notification preferences
6. Verify User B has default preferences (not User A's)

**Expected Results**:
- [ ] User B sees default preferences
- [ ] User B's preferences don't reflect User A's changes
- [ ] Each user has completely isolated settings

**Pass Criteria**: Preferences are user-specific

---

### Test 10: API Response Validation

**Objective**: Verify API returns correct response structure

**Steps**:
1. Open browser console (F12)
2. Navigate to Network tab
3. Modify a preference and save
4. Find the `PUT /api/v1/notifications/preferences` request
5. Click on it and view Response tab

**Expected Results**:
- [ ] HTTP Status: 200 OK
- [ ] Response includes:
   ```json
   {
       "status": "success",
       "message": "Preferences updated successfully",
       "updated_count": 9,
       "invalid_count": 0
   }
   ```
- [ ] Or on partial success:
   ```json
   {
       "status": "partial_success",
       "message": "Updated X preference(s). Y were invalid.",
       "updated_count": X,
       "invalid_count": Y,
       "errors": [...]
   }
   ```

**Pass Criteria**: API response is correct

---

### Test 11: Performance Test

**Objective**: Verify system performs well under normal load

**Steps**:
1. Open Network tab in browser console
2. Rapidly click Save multiple times
3. Modify and save 10+ times in succession
4. Monitor response times

**Expected Results**:
- [ ] Each request completes within 200-500ms
- [ ] No timeout errors
- [ ] No duplicate processing
- [ ] All requests succeed
- [ ] Database integrity maintained

**Pass Criteria**: Performance is acceptable

---

### Test 12: Database Verification

**Objective**: Directly verify database records match UI

**Steps**:
1. Modify preferences through UI and save
2. Connect to database directly
3. Run query:
   ```sql
   SELECT user_id, event_type, in_app, email, push 
   FROM notification_preferences 
   WHERE user_id = 1 
   ORDER BY event_type;
   ```
4. Compare database values with what was saved in UI

**Expected Results**:
- [ ] All records exist for 9 event types
- [ ] Values match UI settings exactly
- [ ] No null or default values where modifications were made
- [ ] Data types correct (INT for channels, VARCHAR for event_type)

**Pass Criteria**: Database records match UI

---

## Summary Table

| Test | Purpose | Status | Notes |
|------|---------|--------|-------|
| 1 | Page Load | ❌ | |
| 2 | Single Checkbox | ❌ | |
| 3 | Multiple Checkboxes | ❌ | |
| 4 | Persistence | ❌ | |
| 5 | All Event Types | ❌ | |
| 6 | Channel Independence | ❌ | |
| 7 | Reset Function | ❌ | |
| 8 | Error Handling | ❌ | |
| 9 | Cross-User Isolation | ❌ | |
| 10 | API Response | ❌ | |
| 11 | Performance | ❌ | |
| 12 | Database | ❌ | |

---

## Known Issues & Workarounds

### Issue: Checkbox state doesn't update visually
**Workaround**: Refresh page after save, checkboxes should show correct state

### Issue: Success message doesn't appear
**Workaround**: Check browser console for errors, verify API request succeeded

### Issue: Settings revert after refresh
**Workaround**: Check database directly to verify data was actually saved

---

## Logs to Check

### Application Error Log
```bash
tail -f storage/logs/notifications.log
```

### Security Log
```bash
tail -f storage/logs/security.log
```

### Expected Log Entries (Success)
```
[NOTIFICATION] Preference updated: user_id=1, event_type=issue_created, in_app=1, email=1, push=0
```

### Expected Log Entries (Error)
```
[NOTIFICATION ERROR] Preference update failed: SQLSTATE[HY093]
```

---

## Test Execution Report

**Test Date**: _______________  
**Tester Name**: _______________  
**Browser**: _______________  
**PHP Version**: _______________  
**MySQL Version**: _______________

**Results**:
- Tests Passed: _____ / 12
- Tests Failed: _____ / 12
- Pass Rate: _____%

**Overall Result**: ❌ FAIL / ⚠️ PARTIAL / ✅ PASS

**Issues Found**:
1. _______________________________
2. _______________________________
3. _______________________________

**Sign-Off**: _______________

---

## Next Steps

If all tests pass:
- [x] Fix is production-ready
- [x] Update release notes
- [x] Notify users of fix
- [x] Monitor production for issues

If tests fail:
- [ ] Document specific failures
- [ ] Review error logs
- [ ] Rollback if necessary
- [ ] Report issues to development team

---

**Testing Complete** ✅
