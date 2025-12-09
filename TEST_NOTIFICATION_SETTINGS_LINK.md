# Test Plan: Notification Settings Link Fix

**Issue**: Notification settings link not redirecting correctly  
**Solution**: Fixed hard-coded paths to use `url()` helper  
**Status**: Ready for Testing

## Test Environment
- **URL**: http://localhost:8080/jira_clone_system/public/notifications
- **Browser**: Chrome, Firefox, Safari (any modern browser)
- **User**: Any authenticated user

## Pre-Test Checklist
- [ ] Application is running on XAMPP at http://localhost:8080
- [ ] Database is seeded with test data
- [ ] User is logged in
- [ ] Browser console is open for debugging

## Test Cases

### Test 1: Navigation to Notification Settings
**Objective**: Verify "Notification Settings" link works correctly from notifications page

**Steps**:
1. Navigate to `/notifications` page
2. Scroll down to the right sidebar
3. Locate the "PREFERENCES" card
4. Click on "Notification Settings" link
5. Verify browser navigates to `/profile/notifications`

**Expected Result**: 
- ✅ Page loads successfully at `/profile/notifications`
- ✅ User sees notification preferences form
- ✅ Form displays event types (issue_created, issue_assigned, issue_commented, etc.)
- ✅ Checkboxes for in_app, email, and push channels are visible
- ✅ No 404 errors in browser console

**Actual Result**: [To be filled during testing]
- [ ] Passed
- [ ] Failed
- [ ] Notes: _______________

---

### Test 2: Dashboard Link from Notifications Page
**Objective**: Verify "Dashboard" quick link works correctly

**Steps**:
1. Navigate to `/notifications` page
2. Scroll down to the right sidebar
3. Locate the "QUICK LINKS" card
4. Click on "Dashboard" link
5. Verify browser navigates to `/dashboard`

**Expected Result**:
- ✅ Page loads successfully
- ✅ User sees the main dashboard
- ✅ No 404 errors

**Actual Result**: [To be filled during testing]
- [ ] Passed
- [ ] Failed
- [ ] Notes: _______________

---

### Test 3: Projects Link from Notifications Page
**Objective**: Verify "Projects" quick link works correctly

**Steps**:
1. Navigate to `/notifications` page
2. Click on "Projects" link in the QUICK LINKS card
3. Verify browser navigates to `/projects`

**Expected Result**:
- ✅ Projects page loads successfully
- ✅ All projects are displayed
- ✅ No 404 errors

**Actual Result**: [To be filled during testing]
- [ ] Passed
- [ ] Failed
- [ ] Notes: _______________

---

### Test 4: Search Issues Link from Notifications Page
**Objective**: Verify "Search Issues" quick link works correctly

**Steps**:
1. Navigate to `/notifications` page
2. Click on "Search Issues" link in the QUICK LINKS card
3. Verify browser navigates to `/search`

**Expected Result**:
- ✅ Search page loads successfully
- ✅ Search form is displayed
- ✅ No 404 errors

**Actual Result**: [To be filled during testing]
- [ ] Passed
- [ ] Failed
- [ ] Notes: _______________

---

### Test 5: URL Generation with Different Base Paths
**Objective**: Verify `url()` helper works with various base path configurations

**Test Scenario**: Application at subdirectory

**Steps**:
1. Inspect HTML source of notification settings link
2. Check the `href` attribute value
3. Verify it's a relative path or properly generated path
4. Navigate to the link
5. Verify correct page loads

**Expected Result**:
- ✅ `href` contains `url('/profile/notifications')` output
- ✅ Link navigates to correct page regardless of base path
- ✅ No broken links

**Actual Result**: [To be filled during testing]
- [ ] Passed
- [ ] Failed
- [ ] Notes: _______________

---

### Test 6: Browser Console Check
**Objective**: Verify no JavaScript errors occur

**Steps**:
1. Open browser DevTools (F12)
2. Navigate to `/notifications` page
3. Open the Console tab
4. Click on notification settings link
5. Observe console for any errors

**Expected Result**:
- ✅ No 404 errors
- ✅ No JavaScript exceptions
- ✅ No CORS errors
- ✅ No routing errors

**Console Errors Found**: 
- None / List any: _______________

---

### Test 7: Responsive Design
**Objective**: Verify links work on mobile/tablet devices

**Steps**:
1. Open DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Test on multiple screen sizes:
   - [ ] Mobile (375px width)
   - [ ] Tablet (768px width)
   - [ ] Desktop (1920px width)
4. Click on each link
5. Verify links work on all sizes

**Expected Result**:
- ✅ Links are clickable on all screen sizes
- ✅ Sidebar is accessible on mobile
- ✅ Page loads correctly on all breakpoints

**Actual Result**: [To be filled during testing]
- [ ] Passed
- [ ] Failed
- [ ] Notes: _______________

---

## Code Review Checklist

### File: views/notifications/index.php

**Line 156 - Notification Settings Link**:
```php
<a href="<?= url('/profile/notifications') ?>" class="sidebar-link">
```
- [ ] Uses `url()` helper
- [ ] Correct route path
- [ ] Proper escaping
- [ ] Links to correct controller method

**Line 164 - Dashboard Link**:
```php
<a href="<?= url('/dashboard') ?>" class="sidebar-link">
```
- [ ] Uses `url()` helper
- [ ] Correct route path

**Line 167 - Projects Link**:
```php
<a href="<?= url('/projects') ?>" class="sidebar-link">
```
- [ ] Uses `url()` helper
- [ ] Correct route path

**Line 170 - Search Link**:
```php
<a href="<?= url('/search') ?>" class="sidebar-link">
```
- [ ] Uses `url()` helper
- [ ] Correct route path

---

## Route Verification

### Check routes/web.php

```php
// Line 159 - Route definition
$router->get('/profile/notifications', [UserController::class, 'profileNotifications'])
    ->name('profile.notifications');
```

- [ ] Route exists
- [ ] Route points to correct controller
- [ ] Controller method name is correct
- [ ] Route is protected with auth middleware

---

## Controller Verification

### Check src/Controllers/UserController.php

```php
// Line 380 - Controller method
public function profileNotifications(Request $request): string
{
    // Get user's notification preferences
    $preferencesList = NotificationService::getPreferences($userId);
    
    // Return view
    return $this->view('profile.notifications', [
        'user' => $user,
        'preferences' => $preferences,
    ]);
}
```

- [ ] Method exists
- [ ] Method is public
- [ ] Method accepts Request parameter
- [ ] Method returns string (view)
- [ ] Method gets user's preferences
- [ ] Method passes data to view

---

## View Verification

### Check views/profile/notifications.php

- [ ] File exists
- [ ] File loads without errors
- [ ] Form displays correctly
- [ ] Preferences are properly populated
- [ ] User can modify settings

---

## Production Deployment Checklist

- [ ] All links use `url()` helper
- [ ] No hard-coded paths remain
- [ ] Code reviewed by team member
- [ ] Tests passed in staging
- [ ] No performance impact
- [ ] No security issues
- [ ] Database schema unchanged
- [ ] No breaking changes
- [ ] Backward compatible

---

## Test Summary

| Test Case | Status | Notes |
|-----------|--------|-------|
| 1. Notification Settings Navigation | [ ] | |
| 2. Dashboard Link | [ ] | |
| 3. Projects Link | [ ] | |
| 4. Search Issues Link | [ ] | |
| 5. URL Generation | [ ] | |
| 6. Browser Console | [ ] | |
| 7. Responsive Design | [ ] | |

**Overall Status**: [ ] PASSED / [ ] FAILED

**Tester Name**: _________________  
**Test Date**: _________________  
**Signature**: _________________

---

## Troubleshooting Guide

### Issue: 404 Error on Notification Settings Link
**Solution**: 
1. Verify route exists in routes/web.php
2. Check UserController has `profileNotifications()` method
3. Check spelling of route path
4. Restart application server
5. Clear browser cache

### Issue: Link Points to Wrong URL
**Solution**:
1. Verify `url()` helper is being used
2. Check for missing `<?= ... ?>` tags
3. Verify no hard-coded paths remain
4. Check view is being properly rendered

### Issue: Page Loads But Shows 500 Error
**Solution**:
1. Check browser console for errors
2. Check server logs in `storage/logs/`
3. Verify database connection
4. Verify NotificationService methods exist
5. Check user has notification preferences

### Issue: Links Work Locally but Not in Production
**Solution**:
1. Verify base path configuration
2. Check `.htaccess` file exists and is correct
3. Verify `url()` helper is working
4. Check for hardcoded paths
5. Test with relative URLs

---

## Reference Documentation

- **AGENTS.md**: URL Routing standards
- **routes/web.php**: Route definitions
- **src/Controllers/UserController.php**: Controller implementation
- **views/profile/notifications.php**: Target view
- **NOTIFICATION_SETTINGS_LINK_FIX.md**: Complete fix documentation
