# Fix: Comments Not Being Added or Displayed on Issues

**Date**: December 6, 2025  
**Issue**: Comments don't appear when added, form submission fails silently  
**Status**: ✅ FIXED

## Problem Summary

When users try to add a comment to an issue:
1. They write a comment in the comment box
2. Click the "Comment" button
3. Nothing happens - no error message, no comment displayed
4. Page state remains unchanged

## Root Causes

### Issue 1: Comments Not Being Loaded from Database
**File:** `src/Services/IssueService.php` (Line 204)  
**Problem:** The `getIssueByKey()` method was not loading comments from the database at all.

The method loaded labels, components, versions, watchers, and votes - but completely skipped comments.

**Impact:** Even if comments were saved, they would never display when viewing an issue.

### Issue 2: Incorrect Column Name in CommentController
**File:** `src/Controllers/CommentController.php` (Lines 50, 114)  
**Problem:** Querying for `avatar_url` field which doesn't exist in the database.

```php
// WRONG:
u.avatar_url as author_avatar  // Column doesn't exist!

// CORRECT:
u.avatar as author_avatar      // Correct column name
```

**Impact:** Comment insert succeeded, but retrieving comment data would fail.

### Issue 3: Data Structure Mismatch Between Controller and View
**File:** `views/issues/show.php` (Lines 177-188)  
**Problem:** View expected nested user object structure:
```php
$comment['user']['avatar']        // Nested
$comment['user']['display_name']  // Nested
```

But CommentController returned flat structure:
```php
$comment['author_avatar']         // Flat
$comment['author_name']           // Flat
```

**Impact:** Comments would display incorrectly even if they loaded.

### Issue 4: Inadequate Error Handling in JavaScript
**File:** `views/issues/show.php` (Lines 614-635)  
**Problem:** JavaScript didn't check HTTP response status before reloading page.

```javascript
// Old code - blindly reloads regardless of response status
await fetch(...);
window.location.reload();  // Executed even if request failed!
```

**Impact:** Users saw no error feedback when submission failed.

## Solutions Applied

### Fix 1: Load Comments in IssueService

**File:** `src/Services/IssueService.php` (Lines 203-232)

**Added:**
```php
$issue['comments'] = Database::select(
    "SELECT c.*, u.display_name as author_name, u.avatar as author_avatar, u.id as author_id,
            u.first_name, u.last_name, u.email
     FROM issue_comments c
     JOIN users u ON c.author_id = u.id
     WHERE c.issue_id = ?
     ORDER BY c.created_at ASC",
    [$issue['id']]
);

// Transform flat comments into nested structure for view compatibility
$issue['comments'] = array_map(function ($comment) {
    return [
        'id' => $comment['id'],
        'body' => $comment['body'],
        'created_at' => $comment['created_at'],
        'updated_at' => $comment['updated_at'],
        'user' => [
            'id' => $comment['author_id'],
            'display_name' => $comment['author_name'],
            'first_name' => $comment['first_name'],
            'last_name' => $comment['last_name'],
            'avatar' => $comment['author_avatar'],
            'email' => $comment['email'],
        ]
    ];
}, $issue['comments']);
```

**Why:** Ensures comments are loaded and transformed to the expected structure.

### Fix 2: Correct Column Names in CommentController

**File:** `src/Controllers/CommentController.php`

**Changes:**
- Line 50: `avatar_url` → `avatar`
- Line 114: `avatar_url` → `avatar`

**Why:** Matches actual database column names.

### Fix 3: Improved JavaScript Error Handling

**File:** `views/issues/show.php` (Lines 614-651)

**Added:**
```javascript
// Check response status before reloading
if (!response.ok) {
    const errorData = await response.json().catch(() => ({}));
    console.error('Comment error response:', response.status, errorData);
    alert(errorData.error || 'Failed to add comment');
    return;  // Don't reload!
}

// Also added input validation
if (!commentBody || !commentBody.trim()) {
    alert('Please enter a comment');
    return;
}
```

**Why:** Provides error feedback to users and prevents page reload on failure.

## Files Modified

1. **src/Services/IssueService.php** (Lines 203-232)
   - Added comment loading with proper transformation

2. **src/Controllers/CommentController.php** (Lines 50, 114)
   - Fixed `avatar_url` → `avatar`

3. **views/issues/show.php** (Lines 614-651)
   - Improved JavaScript error handling and validation

## How It Works Now

### Comment Submission Flow

```
1. User writes comment and clicks "Comment"
   ↓
2. JavaScript validates comment is not empty
   ↓
3. Fetch POST request sent to /issue/{key}/comments
   ↓
4. CommentController.store() receives request
   ↓
5. Comment inserted into database
   ↓
6. Author user data retrieved correctly (avatar column exists!)
   ↓
7. Response returned with 201 status
   ↓
8. JavaScript checks response.ok === true
   ↓
9. Page reloads to show new comment
   ↓
10. IssueService.getIssueByKey() loads comments from database
    ↓
11. Comments transformed to nested structure
    ↓
12. View displays comment with author info
```

## Verification Steps

### Test 1: Add Comment to Issue
1. Navigate to any issue
2. Scroll to "Comments" section
3. Type a comment in the text area
4. Click "Comment" button
5. ✅ Comment should appear immediately
6. ✅ Author name and avatar should display
7. ✅ Timestamp should show "just now"

### Test 2: Multiple Comments
1. Add first comment
2. Add second comment
3. ✅ Both comments should display in order
4. ✅ Both should have correct author info

### Test 3: Comment with Special Characters
1. Add comment with: `<script>`, `&`, `"`, etc.
2. ✅ Should be properly escaped (no XSS issues)
3. ✅ Special characters should display correctly

### Test 4: Empty Comment Error
1. Leave comment box empty
2. Click "Comment" button
3. ✅ Alert: "Please enter a comment"
4. ✅ Page doesn't reload

### Test 5: Browser Console
1. Open browser DevTools (F12)
2. Go to Console tab
3. Add a comment
4. ✅ No JavaScript errors
5. ✅ Fetch request shows 201 status

## Database Verification

Comments are stored in the `issue_comments` table:

```sql
SELECT * FROM issue_comments 
JOIN users ON issue_comments.author_id = users.id 
WHERE issue_comments.issue_id = 107 
ORDER BY issue_comments.created_at ASC;
```

## Testing Checklist

After deploying these fixes:

- [ ] Navigate to issue detail page
- [ ] Verify existing comments display (if any)
- [ ] Add a new comment and verify it appears immediately
- [ ] Verify comment shows correct author name
- [ ] Verify comment shows author avatar (or initial)
- [ ] Verify comment shows correct timestamp
- [ ] Try adding empty comment - should show error
- [ ] Try adding comment with special characters
- [ ] Check browser console for JavaScript errors
- [ ] Verify comment persists after page reload

## Architecture Notes

### View Expectations

The comments display view expects nested structure:
```php
$comment['user']['display_name']  // User object nested inside comment
$comment['user']['avatar']
$comment['user']['first_name']
```

### Why Transformation?

The database query returns flat results, so `array_map()` transforms them into the expected nested structure. This keeps the database query simple while matching view expectations.

Alternative approach would be to change the view, but the current implementation is cleaner.

## Security Considerations

✅ **XSS Prevention:** Comment body is output escaped with `e()` helper  
✅ **CSRF Protection:** Comments require valid CSRF token  
✅ **Authorization:** Comments require `issues.comment` permission  
✅ **Input Validation:** Max 50,000 characters enforced  

## Future Improvements

1. **Edit Comments** - Allow users to edit their own comments
2. **Delete Comments** - Allow users to delete their comments
3. **Comment Threading** - Support nested/threaded comments
4. **Mentions** - Support @mention notifications
5. **Reactions** - Allow emoji reactions on comments
6. **Comment Attachments** - Allow uploading files with comments

## Summary

This fix ensures the complete comment lifecycle works properly:
- Comments are saved to database ✅
- Comments are retrieved from database ✅
- Comments display with correct author info ✅
- Users get feedback on errors ✅
- All security protections in place ✅

Comments now work as expected on the issue detail page!
