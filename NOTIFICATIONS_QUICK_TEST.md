# Notifications System - Quick Test Guide

**Status**: ✅ INSTALLED AND READY FOR TESTING  
**Installation**: Complete  
**Date**: December 2025

---

## Quick Verification

Run this command to verify installation:

```bash
php verify_notifications.php
```

Expected output:
```
✅ 4 database tables created
✅ All columns exist
✅ All indexes created
✅ All files present
```

---

## Testing Timeline

### Setup (5 minutes)
- [ ] Verify installation with `php verify_notifications.php`
- [ ] Open browser to `http://localhost/jira_clone_system/public`
- [ ] Log in with any account

### Test 1: Issue Creation (5 minutes)
- [ ] Create new issue
- [ ] Notice teammates get notifications
- [ ] Check notification bell icon

### Test 2: Bell Icon (3 minutes)
- [ ] Click bell icon in navbar
- [ ] Verify dropdown shows 5 latest notifications
- [ ] Verify unread count appears in red badge

### Test 3: Assignment (5 minutes)
- [ ] Open an unassigned issue
- [ ] Assign to teammate
- [ ] Switch to teammate account
- [ ] Check notification in bell dropdown (should be "high" priority)

### Test 4: Comments (5 minutes)
- [ ] Log in as User A
- [ ] Find issue assigned to User B
- [ ] Add comment
- [ ] Switch to User B account
- [ ] Verify notification appears

### Test 5: Notification Center (5 minutes)
- [ ] Click "View All Notifications" link
- [ ] Verify notifications display with pagination
- [ ] Click "Mark as Read" button
- [ ] Verify notification styling changes
- [ ] Click "Mark All as Read"
- [ ] Verify all become read

### Test 6: API Endpoints (5 minutes)
Use curl or Postman:

```bash
# Get unread notifications
curl http://localhost/jira_clone_system/public/api/v1/notifications

# Mark one as read (replace 1 with real notification ID)
curl -X PATCH http://localhost/jira_clone_system/public/api/v1/notifications/1/read

# Mark all as read
curl -X PATCH http://localhost/jira_clone_system/public/api/v1/notifications/read-all

# Get preferences
curl http://localhost/jira_clone_system/public/api/v1/notifications/preferences

# Get statistics
curl http://localhost/jira_clone_system/public/api/v1/notifications/stats
```

**Total Time**: ~30 minutes

---

## Detailed Test Cases

### Test Case 1: Create Issue → Teammates Notified

**Precondition**: Project with 2+ members logged in

**Steps**:
1. Log in as **User A**
2. Go to `Projects` → Select a project with **User B** as member
3. Click `Create Issue` button
4. Fill in title: "Test Issue for Notifications"
5. Fill in description: "Testing notification system"
6. Leave assignee blank
7. Click `Create`
8. Note issue key (e.g., `TEST-123`)
9. Log out

**Expected Result**:
- Issue created successfully
- No errors in console
- Success message shown

**Verify in User B's Account**:
1. Log in as **User B** (teammate)
2. Look at bell icon
3. Should show **red badge with "1"**
4. Click bell icon
5. Dropdown should show:
   - Title: "Issue Created"
   - Message: "Issue TEST-123: Test Issue for Notifications"
   - Type badge: "CREATED" (blue)
   - Timestamp: "Just now" or "1m ago"

---

### Test Case 2: Assign Issue → Assignee Gets High-Priority Notification

**Precondition**: Test Case 1 completed, have `TEST-123` issue key

**Steps**:
1. Log in as **User A**
2. Go to `Projects` → Find project
3. Click on issue `TEST-123`
4. Click `Assign` button or dropdown
5. Select **User B**
6. Click `Assign`
7. Log out

**Expected Result**:
- Assignment confirmed
- Success message

**Verify in User B's Account**:
1. Log in as **User B**
2. Click bell icon
3. New notification should appear:
   - Title: "Issue Assigned to You"
   - Type badge: "ASSIGNED" (orange)
   - **Priority: HIGH** (visual indicator)
4. Click notification
5. Should navigate to issue detail page

---

### Test Case 3: Add Comment → Assignee Notified

**Precondition**: Issue `TEST-123` assigned to User B

**Steps**:
1. Log in as **User A**
2. Navigate to issue `TEST-123`
3. Scroll to Comments section
4. Enter comment: "This is a test comment"
5. Click `Add Comment`
6. Log out

**Expected Result**:
- Comment added successfully
- Timestamp shows "Just now"

**Verify in User B's Account**:
1. Log in as **User B**
2. Click bell icon
3. New notification should appear:
   - Title: "New Comment on Your Issue"
   - Message: "New comment on TEST-123"
   - Type badge: "COMMENTED"

---

### Test Case 4: Mark as Read Function

**Precondition**: Have unread notifications in dropdown

**Steps**:
1. Click bell icon
2. See unread notifications with blue "New" badge
3. Click "Mark as Read" button (checkmark icon) on one notification
4. Observe dropdown updates
5. Badge should disappear from that notification

**Expected Result**:
- Notification styling changes
- "New" badge disappears
- Notification moves to read state
- Unread count in bell decreases by 1

---

### Test Case 5: Notification Center Page

**Precondition**: Have 5+ notifications

**Steps**:
1. Click bell icon
2. Click "View All Notifications" link at bottom
3. Redirect to `/notifications` page
4. Page should show:
   - Title: "Notifications"
   - Unread count
   - "Mark All as Read" button
   - Notification list with pagination
   - Sidebar with stats

**Expected Result**:
- Page loads without errors
- Notifications display with:
  - Type badge (color-coded)
  - Title
  - Message preview
  - Timestamp
  - Read/unread indicator (blue left border)
  - Action buttons (Mark Read, Delete)

---

### Test Case 6: Mark All as Read

**Steps**:
1. Go to `/notifications` page
2. Note unread count (e.g., 5)
3. Click "Mark All as Read" button
4. Observe all notifications styling change
5. Refresh page

**Expected Result**:
- All notifications immediately marked as read
- Left border changes from blue to gray
- Unread count becomes 0
- Bell icon badge disappears
- After refresh, still shows 0 unread

---

### Test Case 7: Delete Notification

**Steps**:
1. Go to `/notifications` page
2. Hover over a notification
3. Click trash icon button
4. Confirm deletion
5. Refresh page

**Expected Result**:
- Notification removed from list
- Count decreases
- After refresh, notification gone

---

### Test Case 8: API - Get Notifications

**Endpoint**: `GET /api/v1/notifications?limit=5`

**Expected Response**:
```json
{
  "data": [
    {
      "id": 1,
      "type": "issue_created",
      "title": "Issue Created",
      "message": "Issue TEST-123: Test Issue",
      "action_url": "/issue/TEST-123",
      "priority": "normal",
      "is_read": 0,
      "created_at": "2025-12-07 14:30:00"
    }
  ],
  "count": 1,
  "unread_count": 1
}
```

---

### Test Case 9: API - Mark as Read

**Endpoint**: `PATCH /api/v1/notifications/{id}/read`

**Request**:
```bash
curl -X PATCH http://localhost/jira_clone_system/public/api/v1/notifications/1/read \
  -H "Content-Type: application/json"
```

**Expected Response**:
```json
{
  "status": "success",
  "unread_count": 0
}
```

---

### Test Case 10: API - Get Preferences

**Endpoint**: `GET /api/v1/notifications/preferences`

**Expected Response**:
```json
{
  "data": [
    {
      "event_type": "issue_created",
      "in_app": 1,
      "email": 1,
      "push": 0
    },
    {
      "event_type": "issue_assigned",
      "in_app": 1,
      "email": 1,
      "push": 0
    }
  ],
  "count": 2
}
```

---

## Performance Testing (For 100+ Developers)

### Test Case 11: Bulk Notifications

**Steps**:
1. Create issue in project with 50+ members
2. Time how long notifications take to appear
3. Check database notification count

**Expected Result**:
- All 50 notifications created in <1 second
- No UI lag
- No error messages

### Test Case 12: Cached Unread Count

**Steps**:
1. Note unread count
2. Create new issue
3. Click bell icon
4. Note timestamp when notification appears

**Expected Result**:
- Unread count updates within 30 seconds
- No database slowdown

---

## Debugging Checklist

If tests fail, check:

### Database
```bash
# Check tables exist
mysql -u root jira_clone -e "SHOW TABLES LIKE 'notification%';"

# Check column count
mysql -u root jira_clone -e "SHOW COLUMNS FROM notifications;"

# Check data
mysql -u root jira_clone -e "SELECT COUNT(*) FROM notifications;"
```

### Logs
```bash
# Check PHP errors
tail -f storage/logs/app.log

# Browser console errors
F12 → Console tab
```

### Routing
```bash
# Verify routes loaded
php -r "require 'bootstrap/app.php'; 
  print_r(app()->getRouter()->getRoutes());" | grep notification
```

---

## Success Criteria Checklist

- [ ] Notifications appear in bell icon dropdown
- [ ] Unread count badge displays correctly
- [ ] Mark as read works
- [ ] Mark all as read works
- [ ] Delete works
- [ ] `/notifications` page loads
- [ ] Pagination works on notification center
- [ ] All API endpoints return valid JSON
- [ ] No console errors
- [ ] No database errors
- [ ] Email preferences can be updated
- [ ] Issue creation notifies team
- [ ] Assignment notifies assignee
- [ ] Comments notify assignee

---

## Common Issues & Solutions

### Issue: Bell icon doesn't show badge

**Solution**:
1. Check user has unread notifications: `SELECT COUNT(*) FROM notifications WHERE user_id = 1 AND is_read = 0;`
2. Clear browser cache
3. Refresh page

### Issue: API returns 401 Unauthorized

**Solution**:
1. Ensure you're logged in
2. Check session valid
3. Include CSRF token for POST/PATCH requests

### Issue: Notifications not appearing after creating issue

**Solution**:
1. Check IssueController has NotificationService integrated
2. Verify issue creator and team member are different users
3. Check team member is part of project

### Issue: `/notifications` page shows 404

**Solution**:
1. Verify route added to `routes/web.php`
2. Restart web server
3. Check URL: `/notifications` not `/notification`

---

## Performance Metrics to Track

After deploying to 100+ developers:

| Metric | Target | Method |
|--------|--------|--------|
| Bell icon load time | <100ms | Chrome DevTools |
| API response time | <200ms | Postman/curl |
| Notification creation | <50ms | Database logs |
| Unread count accuracy | 100% | Manual verification |
| Page load time | <500ms | Chrome DevTools |

---

## Feedback Template

After testing, document:

```
Tester: [Name]
Date: [Date]
Tested By: [Manual/Automated]

✅ Passing Tests: [List]
❌ Failing Tests: [List]
⚠️  Warnings: [List]

Recommendations:
- 
```

---

**Ready to test? Start with Test Case 1!**
