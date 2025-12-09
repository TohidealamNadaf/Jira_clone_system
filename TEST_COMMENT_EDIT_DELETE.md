# Test Guide - Comment Edit & Delete Feature

## Quick Test (5 minutes)

### Step 1: Open Issue Page
1. Go to any project
2. Click on an issue with comments
3. Scroll to Comments section

### Step 2: Test Edit Feature

**Test Own Comment Edit**:
1. Hover over a comment you wrote
2. ✅ Should see blue pencil (✏️) icon and red trash (🗑️) icon
3. Click the pencil icon
4. ✅ Comment text should be replaced with textarea
5. Modify the text
6. Click "Save"
7. ✅ Comment should update with new text
8. ✅ "Comment updated successfully" notification should appear

**Test Cannot Edit Others' Comments**:
1. Hover over a comment by another user
2. ❌ Should NOT see edit/delete buttons

### Step 3: Test Delete Feature

**Test Delete Own Comment**:
1. Hover over a comment you wrote
2. Click the red trash (🗑️) icon
3. ✅ Confirmation dialog should appear
4. Click "OK"
5. ✅ Comment should fade out and disappear
6. ✅ "Comment deleted successfully" notification should appear

**Test Cancel Delete**:
1. Hover over a comment you wrote
2. Click trash icon
3. Confirmation dialog appears
4. Click "Cancel"
5. ✅ Dialog closes, comment stays

### Step 4: Check Admin Permissions

**If you're an admin**:
1. Hover over ANY comment (even by other users)
2. ✅ Should see edit/delete buttons
3. ✅ Should be able to edit/delete any comment

---

## Detailed Test Scenarios

### Scenario 1: Edit Comment Content

**Setup**: Issue with 3+ comments

**Test Steps**:
1. Find a comment you wrote
2. Hover to reveal edit button
3. Click edit button
4. ✅ Textarea appears with original text
5. Change some text
6. Click "Save"
7. Wait for notification
8. ✅ Comment shows new text
9. Reload page
10. ✅ Comment still shows new text (persisted)

**Expected Result**: ✅ PASS

### Scenario 2: Cancel Edit

**Setup**: Issue with comments

**Test Steps**:
1. Find a comment to edit
2. Click edit button
3. Textarea appears
4. Modify text
5. Click "Cancel"
6. ✅ Textarea disappears
7. ✅ Comment shows original text (no changes)

**Expected Result**: ✅ PASS

### Scenario 3: Delete Comment

**Setup**: Issue with multiple comments

**Test Steps**:
1. Count comments visible
2. Find a comment to delete
3. Hover and click delete
4. ✅ Confirmation appears
5. Click "OK"
6. ✅ Comment fades out
7. ✅ Comment count decreases
8. ✅ Success notification appears
9. Reload page
10. ✅ Deleted comment is gone

**Expected Result**: ✅ PASS

### Scenario 4: Permissions - Regular User

**Setup**: You're a regular user (not admin)

**Test Steps**:
1. Open issue with comments
2. Hover over YOUR comments
3. ✅ Should see edit/delete buttons
4. Hover over OTHERS' comments
5. ❌ Should NOT see buttons
6. Try to access edit URL directly
7. ❌ Should get permission error

**Expected Result**: ✅ PASS

### Scenario 5: Permissions - Admin User

**Setup**: You're an admin user

**Test Steps**:
1. Open issue with comments
2. Hover over YOUR comments
3. ✅ Should see edit/delete buttons
4. Hover over OTHERS' comments
5. ✅ Should ALSO see edit/delete buttons
6. Edit someone else's comment
7. ✅ Should work
8. Delete someone else's comment
9. ✅ Should work

**Expected Result**: ✅ PASS

### Scenario 6: Empty Comment Validation

**Setup**: Issue with editable comment

**Test Steps**:
1. Click edit on a comment
2. Clear all text in textarea
3. Click "Save"
4. ✅ Alert should appear: "Comment cannot be empty"
5. Comment should not be updated
6. Text should still be editable
7. Type some text
8. Click "Save"
9. ✅ Should work now

**Expected Result**: ✅ PASS

### Scenario 7: Multiple Edits

**Setup**: Issue with your comment

**Test Steps**:
1. Edit comment and save
2. ✅ Works and notifies
3. Edit same comment again
4. ✅ Works and notifies
5. Edit again
6. ✅ Works and notifies
7. Delete comment
8. ✅ Works and notifies

**Expected Result**: ✅ PASS (no state issues)

### Scenario 8: Edit in Context

**Setup**: Issue with pagination (many comments)

**Test Steps**:
1. Load initial 5 comments
2. Edit one of initial comments
3. ✅ Works correctly
4. Click "Load More Comments"
5. New comments load
6. Edit one of new comments
7. ✅ Works correctly
8. Delete loaded comment
9. ✅ Works correctly

**Expected Result**: ✅ PASS

---

## Browser Testing

### Test in Different Browsers

**Chrome**:
- [ ] Edit works
- [ ] Delete works
- [ ] Hover shows buttons
- [ ] Notifications appear
- [ ] Fade animation smooth

**Firefox**:
- [ ] Edit works
- [ ] Delete works
- [ ] Hover shows buttons
- [ ] Notifications appear
- [ ] Fade animation smooth

**Safari**:
- [ ] Edit works
- [ ] Delete works
- [ ] Hover shows buttons
- [ ] Notifications appear
- [ ] Fade animation smooth

**Edge**:
- [ ] Edit works
- [ ] Delete works
- [ ] Hover shows buttons
- [ ] Notifications appear
- [ ] Fade animation smooth

**Mobile (Chrome Android)**:
- [ ] Buttons visible (hover/tap)
- [ ] Edit form appears
- [ ] Can save changes
- [ ] Delete works
- [ ] Notifications appear

---

## Visual Testing

### Check Visual Elements

**Hover State**:
- [ ] Edit button (blue pencil) appears on hover
- [ ] Delete button (red trash) appears on hover
- [ ] Both have hover scale effect (grow slightly)

**Edit Form**:
- [ ] Textarea shows with original text
- [ ] Save button is blue and shows icon
- [ ] Cancel button is gray and shows icon
- [ ] Form has proper spacing

**Delete Confirmation**:
- [ ] Dialog appears with confirmation text
- [ ] Warning message is clear
- [ ] OK and Cancel buttons visible
- [ ] Dialog is modal (blocks background)

**Notifications**:
- [ ] Success notification appears (top center)
- [ ] Has dismiss button
- [ ] Auto-dismisses after 3 seconds
- [ ] Proper success styling (green)

**Animations**:
- [ ] Edit form appears smoothly
- [ ] Delete fade out is smooth (0.3s)
- [ ] Buttons fade in on hover (0.2s)
- [ ] No visual glitches

---

## Console Testing

### Check Browser Console (F12)

**Edit Success**:
1. Click edit button
2. F12 → Console
3. Modify and save
4. ✅ Should see successful fetch response
5. ✅ No errors in console

**Delete Success**:
1. Click delete button
2. F12 → Console
3. Confirm delete
4. ✅ Should see successful fetch response
5. ✅ No errors in console

**No Errors**:
- [ ] Console shows no JavaScript errors
- [ ] No 404 or 500 responses
- [ ] CSRF token properly sent
- [ ] Content-Type headers correct

---

## Network Testing

### Check Network Requests (F12 → Network)

**Edit Request**:
1. Click edit and save
2. F12 → Network tab
3. ✅ Should see PUT request to `/comments/{id}`
4. ✅ Status should be 200 OK
5. ✅ Headers should include X-CSRF-Token
6. ✅ Request body should have new comment text

**Delete Request**:
1. Click delete and confirm
2. F12 → Network tab
3. ✅ Should see DELETE request to `/comments/{id}`
4. ✅ Status should be 200 OK
5. ✅ Headers should include X-CSRF-Token

---

## Data Verification

### Check Data Persistence

**Edit Test**:
1. Edit comment with specific text
2. Save successfully
3. Refresh page (F5)
4. ✅ Comment should show edited text
5. Open browser dev tools → Application → Cookies
6. ✅ Session cookie still present
7. ✅ Changes persisted in database

**Delete Test**:
1. Delete comment
2. Refresh page (F5)
3. ✅ Comment should be gone
4. ✅ Should not return

---

## Edge Cases

### Test Edge Case 1: Very Long Comment
1. Edit comment
2. Paste very long text (5000+ characters)
3. Save
4. ✅ Should work (max 50000)

### Test Edge Case 2: Special Characters
1. Edit comment
2. Add: `<script>alert('xss')</script>`
3. Save
4. ✅ Should display as text, not execute
5. ✅ No XSS vulnerability

### Test Edge Case 3: Line Breaks
1. Edit comment
2. Add text with multiple lines
3. Save
4. ✅ Line breaks preserved with `<br>` tags

### Test Edge Case 4: Emoji
1. Edit comment
2. Add emoji: 😊🎉✨
3. Save
4. ✅ Emoji should display correctly

### Test Edge Case 5: Rapid Clicks
1. Click edit button multiple times rapidly
2. ✅ Should only open once
3. Click save multiple times
4. ✅ Should only save once

---

## Sign-Off Checklist

- [ ] Edit button visible and functional
- [ ] Delete button visible and functional
- [ ] Permissions work correctly
- [ ] Edit form works smoothly
- [ ] Delete confirmation appears
- [ ] Notifications display properly
- [ ] Data persists across page reload
- [ ] No JavaScript errors in console
- [ ] Works in Chrome, Firefox, Safari, Edge
- [ ] Mobile responsive
- [ ] XSS protection verified
- [ ] CSRF token included
- [ ] No performance issues
- [ ] Animations smooth

---

## Reporting Issues

If you find any issues:

1. **Note the steps to reproduce**
   - What did you click?
   - What was the state before?
   - What happened?

2. **Take a screenshot or video**
   - Visual evidence is helpful
   - Include error messages

3. **Check browser console**
   - Open F12
   - Copy any error messages

4. **Note your environment**
   - Browser and version
   - OS
   - Device (desktop/mobile)

5. **Report with:**
   - Steps to reproduce
   - Expected result
   - Actual result
   - Screenshots/console errors

---

**Test Date**: 2025-12-06  
**Status**: Ready for Testing  
**Expected Result**: All tests PASS ✅
