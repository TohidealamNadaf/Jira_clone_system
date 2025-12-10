# Comment Edit/Delete Bug Fix - Testing Guide

**Status**: Complete & Ready for QA  
**Date**: December 9, 2025  
**File**: `views/issues/show.php`

---

## Quick Test Steps

### Setup
1. Open any issue with comments
2. Open DevTools (F12) and watch Console tab
3. Have Network tab open to see API calls

---

## Test 1: Edit Comment

### Steps
1. Click the **Edit (pencil) icon** on any comment
2. Observe the comment body

### Expected Result ✅
- Comment text should disappear
- Textarea should appear with original comment text
- "Save" and "Cancel" buttons should appear
- Textarea should have focus (blinking cursor)

### Failed? Debug
- Check Console for errors
- Check if `edit-comment-btn` class exists on button
- Check if `comment-{id}` div exists

---

## Test 2: Edit - Save Changes

### Steps
1. Edit a comment (see Test 1)
2. Change some text in the textarea
3. Click **Save** button

### Expected Result ✅
- Green notification: "Comment updated successfully"
- Comment text should update on page
- "(edited)" indicator should appear below comment
- Textarea should disappear

### Failed? Debug
- Check Console for errors
- Check Network tab - should see `PUT /comments/{id}`
- Check response status (should be 200)
- Check if CommentController::update() is being called

---

## Test 3: Edit - Cancel

### Steps
1. Edit a comment (see Test 1)
2. Change some text in the textarea
3. Click **Cancel** button

### Expected Result ✅
- Comment text should revert to original
- Textarea should disappear
- No API call made
- No notification shown

### Failed? Debug
- Check if `cancelCommentEdit()` function exists
- Check if button has correct onclick handler

---

## Test 4: Delete - First Click

### Steps
1. Click the **Delete (trash) icon** on a comment
2. Look at dialog box that appears

### Expected Result ✅
- Browser confirmation dialog appears
- Dialog says: "Are you sure you want to delete this comment? This action cannot be undone."
- Two buttons: "OK" and "Cancel"

### Failed? Debug
- Check if `confirm()` dialog is showing
- Check Console for JavaScript errors

---

## Test 5: Delete - Cancel Dialog

### Steps
1. Click Delete icon (Test 4)
2. Dialog appears
3. Click **Cancel** button on dialog

### Expected Result ✅
- Dialog disappears
- Comment still visible on page
- No API call made
- No notification shown

### Failed? Debug
- Check if early return is working in code

---

## Test 6: Delete - Confirm

### Steps
1. Click Delete icon (Test 4)
2. Dialog appears
3. Click **OK** button on dialog
4. Wait 1 second

### Expected Result ✅
- Green notification: "Comment deleted successfully"
- Comment should fade out (opacity 0.5)
- Comment should disappear from page
- You're still on the same issue page (NOT 404)
- Page did NOT refresh or redirect

### Failed? Debug
- Check Network tab - should see `DELETE /comments/{id}`
- Check response status (should be 200)
- Check if comment element is removed from DOM
- Check if page is trying to navigate away

---

## Test 7: Persistence - Refresh Page

### Steps
1. Edit a comment and save (Test 2)
2. Delete a comment (Test 6)
3. Refresh the page (F5)

### Expected Result ✅
- Issue page loads normally (NOT 404 "Issue not found")
- Edited comment shows new text + "(edited)" indicator
- Deleted comment is gone
- All other comments present

### Failed? Debug
- Check if DELETE endpoint is actually deleting comment from database
- Check if issue_id is correct
- Check error logs for database errors

---

## Test 8: Multiple Comments

### Steps
1. Edit comment #1
2. Click Cancel
3. Delete comment #2
4. Edit comment #3
5. Save changes
6. Refresh page

### Expected Result ✅
- All operations work correctly
- Correct comments are edited/deleted
- No cross-contamination between comments

### Failed? Debug
- Check if comment IDs are correct in data attributes
- Check if DOM selectors are finding correct elements

---

## Test 9: API Endpoints

### Setup
1. Open DevTools Network tab
2. Filter by "Fetch/XHR"

### Steps
1. Edit a comment → click Save
2. Delete a comment → confirm

### Expected Results ✅

**Edit Request**:
- Method: `PUT`
- URL: `/jira_clone_system/public/comments/{id}`
- Headers: 
  - `Content-Type: application/json`
  - `X-CSRF-TOKEN: [token]`
- Body: `{ "body": "new text" }`
- Response: `{ "success": true, "comment": {...} }`

**Delete Request**:
- Method: `DELETE`
- URL: `/jira_clone_system/public/comments/{id}`
- Headers:
  - `X-CSRF-TOKEN: [token]`
  - `X-Requested-With: XMLHttpRequest`
- Response: `{ "success": true }`

### Failed? Debug
- Check if base URL is calculated correctly
- Check if routes are registered (routes/web.php lines 89-90)
- Check if CommentController methods exist

---

## Test 10: Error Handling

### Setup
1. Open DevTools Console to see error messages

### Test 10a: Edit with Empty Comment
1. Edit a comment
2. Clear all text from textarea
3. Click Save

### Expected ✅
- Red notification: "Comment cannot be empty"
- No API call made
- Comment text unchanged

---

### Test 10b: Network Error
1. Open DevTools Network tab
2. Right-click any request → "Simulate offline"
3. Try to edit/delete a comment

### Expected ✅
- Red notification with error message
- No permanent changes
- Can retry action after going back online

---

## Quick Reference

| Feature | Working? | Notes |
|---------|----------|-------|
| Edit button opens form | [ ] | Should show textarea |
| Save button updates comment | [ ] | Should call PUT endpoint |
| Cancel button reverts | [ ] | Should not call API |
| Delete shows confirmation | [ ] | Should show dialog |
| Delete removes comment | [ ] | Should call DELETE endpoint |
| No page redirect on delete | [ ] | Should stay on same issue |
| Persistence on refresh | [ ] | Changes should save to DB |
| Error messages show | [ ] | Should notify user |

---

## Submission Checklist

Before marking as DONE:

- [ ] All 10 tests pass
- [ ] No console errors
- [ ] Network calls correct
- [ ] Database persists changes
- [ ] Page doesn't redirect
- [ ] User notifications clear

**Status**: Ready for QA ✅
