# Comprehensive Testing Suite for Comment Features

## 1. FEATURE TESTING

### A. Comment Display
- [ ] Comments load on issue page
- [ ] Comments display with user avatar and name
- [ ] Timestamps show correctly (time_ago format)
- [ ] "(edited)" label shows for updated comments
- [ ] Comment count badge shows correct number

### B. Add Comment
- [ ] Form submits successfully
- [ ] Comment appears immediately after posting
- [ ] Page redirects to comment anchor (#comment-{id})
- [ ] Success notification shows
- [ ] Issue updated_at timestamp updates
- [ ] Watchers get notified

### C. Edit Comment
- [ ] Edit button appears on hover
- [ ] Edit form shows with current text
- [ ] Can save changes
- [ ] Comment body updates in UI
- [ ] "(edited)" label appears
- [ ] Updated comment timestamp shows
- [ ] Only comment author can edit (permission check)
- [ ] Admin can edit all comments (if permission exists)

### D. Delete Comment
- [ ] Delete button appears on hover
- [ ] Confirmation dialog shows
- [ ] Comment fades out and disappears
- [ ] Success notification shows
- [ ] Comment count updates
- [ ] Only comment author can delete (permission check)
- [ ] Admin can delete all comments (if permission exists)

### E. Collapse/Expand Comments
- [ ] Initial comments load (5 by default)
- [ ] Load More button shows remaining count
- [ ] Load More loads additional comments
- [ ] Collapse All button works
- [ ] Icon changes (chevron-up/down)
- [ ] Smooth transitions between states
- [ ] No layout jumping or tearing

### F. Activity Section
- [ ] Activity section collapses/expands
- [ ] Icon animates on toggle
- [ ] Content height transitions smoothly
- [ ] Activity history displays correctly

### G. Scroll To Top
- [ ] Button appears after scrolling 300px down
- [ ] Button disappears when scrolling up
- [ ] Click smoothly scrolls to top
- [ ] Button is fixed position and always accessible

### H. Comments Pagination
- [ ] Hidden comments are in #comments-data
- [ ] Load More removes data and shows comments
- [ ] Edit/Delete buttons work on loaded comments
- [ ] No duplicate comments after load

## 2. CODE QUALITY CHECKS

### A. API Routes
- [ ] /issue/{key}/comments POST - create comment
- [ ] /comments/{id} PUT - update comment
- [ ] /comments/{id} DELETE - delete comment
- [ ] All routes return proper JSON
- [ ] CSRF token validation works
- [ ] Authorization checks work

### B. Database
- [ ] Comments table has required columns
- [ ] user_id foreign key references users
- [ ] issue_id foreign key references issues
- [ ] created_at/updated_at timestamps work
- [ ] No orphaned comments (bad foreign keys)

### C. JavaScript
- [ ] No console errors
- [ ] No memory leaks
- [ ] Event listeners properly attached
- [ ] Fetch requests use correct headers
- [ ] Error handling is proper

### D. Security
- [ ] XSS protection (nl2br + e() escaping)
- [ ] CSRF token validation on all mutations
- [ ] SQL injection protection (parameterized queries)
- [ ] Authorization checks before edit/delete
- [ ] User can only edit/delete own comments (unless admin)

### E. Performance
- [ ] Page loads quickly with 20+ comments
- [ ] Collapse/expand is smooth (no jank)
- [ ] Edit/delete operations are fast
- [ ] No N+1 queries in comment loading
- [ ] Memory usage is reasonable

## 3. EDGE CASES

### A. Comment Content
- [ ] Very long comments display properly
- [ ] Multiline comments preserve formatting
- [ ] Special characters display correctly (e.g., <, >, &)
- [ ] URLs in comments work
- [ ] Emoji display correctly

### B. User Cases
- [ ] Deleted user comments show "Unknown User"
- [ ] User avatars show or initials if no avatar
- [ ] Multiple users can comment on same issue
- [ ] Same user can comment multiple times

### C. Timing
- [ ] Rapid edits work correctly
- [ ] Rapid deletes work correctly
- [ ] Edit + delete sequence works
- [ ] Multiple users editing same comment (last wins)

### D. Network
- [ ] Offline detection and retry
- [ ] Slow network doesn't break UI
- [ ] Failed requests show error notification
- [ ] Duplicate submission prevented

## 4. IMPROVEMENTS CHECKLIST

### A. Missing Features
- [ ] Edit history tracking
- [ ] @mentions support
- [ ] Rich text editor
- [ ] Comment threading/replies
- [ ] Comment search/filter
- [ ] Comment reactions/reactions
- [ ] Comment sorting options

### B. Code Missing
- [ ] IssueService::addComment()
- [ ] IssueService::updateComment()
- [ ] IssueService::deleteComment()
- [ ] IssueService::getComments()
- [ ] CommentHistory tracking

### C. UI/UX Improvements
- [ ] Show loading state during operations
- [ ] Better confirmation dialogs (not alert)
- [ ] Undo functionality for deletes
- [ ] Comment preview before posting
- [ ] Markdown support
- [ ] Syntax highlighting for code blocks

### D. Accessibility
- [ ] Keyboard navigation
- [ ] ARIA labels
- [ ] Screen reader support
- [ ] Focus management
- [ ] Color contrast

## 5. TESTS TO RUN

```bash
# Test comment creation
curl -X POST http://localhost/issue/TEST-1/comments \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {token}" \
  -d '{"body": "Test comment"}'

# Test comment update
curl -X PUT http://localhost/comments/1 \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {token}" \
  -d '{"body": "Updated comment"}'

# Test comment delete
curl -X DELETE http://localhost/comments/1 \
  -H "X-CSRF-TOKEN: {token}"
```

## 6. DATABASE VERIFICATION

```sql
-- Check comments table structure
DESCRIBE comments;

-- Check for orphaned comments
SELECT c.id FROM comments c 
LEFT JOIN issues i ON c.issue_id = i.id 
WHERE i.id IS NULL;

-- Check for deleted users in comments
SELECT c.id, c.user_id, u.id FROM comments c
LEFT JOIN users u ON c.user_id = u.id
WHERE u.id IS NULL;

-- Comment count by user
SELECT user_id, COUNT(*) as count 
FROM comments 
GROUP BY user_id 
ORDER BY count DESC;

-- Comments per issue
SELECT issue_id, COUNT(*) as count 
FROM comments 
GROUP BY issue_id 
ORDER BY count DESC;
```
