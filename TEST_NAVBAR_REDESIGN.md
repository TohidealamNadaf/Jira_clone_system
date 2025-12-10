# Navbar Redesign - Testing Guide

## Quick Test (5 minutes)

### Desktop Test
1. Open http://localhost/jira_clone_system/public/
2. Check navbar appears correctly
3. Click "Projects" - dropdown should appear
4. Click "Issues" - dropdown should appear
5. Click "Reports" - dropdown should appear
6. Click search box - should be focused
7. Click "Create" button - modal should open
8. Click bell icon - notifications should load
9. Click user avatar - menu should appear
10. Close all dropdowns - click on page

### Mobile Test (Chrome DevTools)
1. Open DevTools (F12)
2. Click device toggle (toggle device toolbar)
3. Select "iPhone 12"
4. Navbar should be compact
5. Click hamburger menu (three lines)
6. Menu should expand vertically
7. Click a link - menu should collapse
8. Verify all functionality works

### Tablet Test (Chrome DevTools)
1. Select "iPad Air" from device list
2. Menu should show some items
3. Toggle button should appear
4. Search should be hidden
5. All dropdowns should work

## Detailed Testing

### Visual Inspection

**Navbar Container**
- [ ] Background is white (`var(--bg-primary)`)
- [ ] Border-bottom is visible (1px solid `var(--border-color)`)
- [ ] Shadow is subtle (`var(--shadow-sm)`)
- [ ] Height looks professional (56px height)
- [ ] Sticky to top when scrolling

**Brand/Logo**
- [ ] Kanban icon appears in blue (`var(--jira-blue)`)
- [ ] App name appears next to icon
- [ ] Text is bold and readable (18px)
- [ ] Clicking logo navigates home

**Navigation Links**
- [ ] "Projects" text visible with folder icon
- [ ] "Issues" text visible with task icon
- [ ] "Reports" text visible with chart icon
- [ ] "Admin" visible if logged in as admin
- [ ] Icons are 16px, text is 14px
- [ ] Gap between icon and text (8px)
- [ ] All links have hover effect (background color change)

**Search Bar** (Desktop only)
- [ ] Background is light gray (`var(--bg-secondary)`)
- [ ] Search icon visible
- [ ] Input field receives focus
- [ ] Placeholder text visible
- [ ] Submits form on Enter

**Create Button**
- [ ] Blue background (`var(--jira-blue)`)
- [ ] "Create" text visible on desktop
- [ ] Icon only visible on mobile
- [ ] Proper padding and size
- [ ] Hover effect darkens button
- [ ] Clicking opens quick-create modal

**Bell Icon** (Notifications)
- [ ] Bell icon visible (18px)
- [ ] Red badge appears if unread notifications
- [ ] Badge shows count (e.g., "5", "99+")
- [ ] Clicking opens dropdown
- [ ] Dropdown height is scrollable (400px)

**Notifications Dropdown**
- [ ] Header says "Notifications"
- [ ] Shows up to 5 recent notifications
- [ ] Each notification shows:
  - Title (bold)
  - Message preview (12px, gray)
  - Time ago (11px, gray)
  - "New" badge if unread
- [ ] Left border highlight for unread
- [ ] "View All Notifications" link at bottom
- [ ] Closes when clicking elsewhere

**User Avatar Menu**
- [ ] Avatar image visible (32x32px)
- [ ] Clicking shows dropdown menu
- [ ] Menu items:
  - Profile
  - Settings
  - Notification Preferences
  - Logout button in red
- [ ] All items have icons
- [ ] Logout is POST form (not GET)

**Mobile Toggle Button**
- [ ] Only appears on mobile (<992px)
- [ ] Three-line hamburger icon
- [ ] Clicking expands/collapses menu
- [ ] Proper icon color
- [ ] Hover effect visible

### Functional Testing

**Dropdown Functionality**
- [ ] Click "Projects" → dropdown opens
- [ ] "View All Projects" link works
- [ ] "Create Project" link appears (if permission)
- [ ] Clicking a link closes dropdown
- [ ] Click elsewhere closes dropdown
- [ ] Same for Issues and Reports dropdowns

**Search Functionality**
- [ ] Type in search box
- [ ] Search form submits on Enter
- [ ] Navigates to search page with query

**Create Button**
- [ ] Clicking opens quick-create modal
- [ ] Modal has form fields (Project, Issue Type, Summary)
- [ ] Modal can be closed
- [ ] Form submits correctly

**Notifications**
- [ ] Bell shows correct unread count
- [ ] Clicking bell loads notifications
- [ ] Notifications display correctly
- [ ] "View All" link navigates to notifications page
- [ ] Notifications refresh every 30 seconds

**User Menu**
- [ ] Clicking avatar opens menu
- [ ] Profile link works
- [ ] Settings link works
- [ ] Notification Preferences link works
- [ ] Logout form submits correctly
- [ ] Session ends after logout

**Mobile Menu**
- [ ] Hamburger button appears on mobile
- [ ] Menu expands when clicked
- [ ] Menu items stack vertically
- [ ] Dropdowns work in mobile menu
- [ ] Menu collapses when item is clicked
- [ ] Menu collapses when modal opens

### Responsive Breakpoints

**Desktop (> 991px)**
- [ ] All menu items visible
- [ ] Dropdowns horizontal placement
- [ ] Search visible
- [ ] "Create" text visible
- [ ] All actions visible in one row

**Tablet (768px - 991px)**
- [ ] Toggle button appears
- [ ] Menu can be collapsed/expanded
- [ ] Search may be hidden
- [ ] Icons and text scale properly
- [ ] Touch targets are large enough (40px+)

**Mobile (< 768px)**
- [ ] Navbar is compact
- [ ] Toggle button prominent
- [ ] Menu stacks vertically
- [ ] All items accessible in menu
- [ ] Touch targets are large (44px+)

### Accessibility Testing

**Keyboard Navigation**
- [ ] Tab through navbar items
- [ ] Enter/Space opens dropdowns
- [ ] Escape closes dropdowns
- [ ] Tab through dropdown items
- [ ] All buttons are keyboard accessible

**Screen Reader**
- [ ] Links have proper labels
- [ ] Buttons labeled correctly
- [ ] ARIA attributes present:
  - `aria-expanded` on dropdowns
  - `aria-controls` where appropriate
  - `role="button"` on clickable divs
- [ ] Icons have `aria-label` if no text

**Color Contrast**
- [ ] All text passes WCAG AA (4.5:1)
- [ ] Links distinguishable from text
- [ ] Icons visible and meaningful

### Browser Compatibility

**Chrome/Edge**
- [ ] All features work
- [ ] No console errors
- [ ] Responsive works
- [ ] Animations smooth

**Firefox**
- [ ] All features work
- [ ] No console errors
- [ ] Responsive works
- [ ] Animations smooth

**Safari**
- [ ] All features work
- [ ] No console errors
- [ ] Responsive works
- [ ] Animations smooth

**Mobile Browsers**
- [ ] iOS Safari works
- [ ] Android Chrome works
- [ ] Mobile dropdowns work
- [ ] Touch interactions smooth

### Performance Testing

**Page Load**
- [ ] No 404 errors for navbar assets
- [ ] CSS loads without errors
- [ ] No layout shifts (CLS)
- [ ] Navbar visible in < 1 second

**Interactions**
- [ ] Dropdown opens instantly (< 100ms)
- [ ] Search responds to typing
- [ ] Notifications load within 500ms
- [ ] No jank during animations

**JavaScript Console**
- [ ] No errors
- [ ] No warnings (except external libraries)
- [ ] CSRF token loaded correctly
- [ ] API calls successful

## Quick Check Commands

### PHP Syntax
```bash
php -l views/layouts/app.php
```

### CSS Validation (visual)
Open DevTools → Elements → Check for CSS errors

### JavaScript Errors
Open DevTools → Console → Check for red errors

### Network Tab
- Check all navbar assets load (no 404s)
- CSS loads from `asset('css/app.css')`
- Bootstrap CSS loads from CDN
- Select2 loads (if used)

## Test Results Template

```
Browser: ____________
Date: _______________
Tester: _____________

Desktop Testing: ✓ PASS / ✗ FAIL
- Comments: 

Mobile Testing: ✓ PASS / ✗ FAIL
- Comments:

Functionality: ✓ PASS / ✗ FAIL
- Comments:

Accessibility: ✓ PASS / ✗ FAIL
- Comments:

Visual: ✓ PASS / ✗ FAIL
- Comments:

Performance: ✓ PASS / ✗ FAIL
- Comments:

Overall: ✓ PASS / ✗ FAIL
Issues Found:
1. 
2. 
3. 

Recommendations:
1. 
2. 
```

## Common Issues & Solutions

### Issue: Dropdown not opening
**Solution**: 
- Clear browser cache (Ctrl+Shift+Delete)
- Check Bootstrap JS is loaded
- Check for JavaScript errors in console
- Verify `data-bs-toggle="dropdown"` on link

### Issue: Navbar not sticky
**Solution**:
- Check `sticky-top` class present
- Check z-index is 2000 or higher
- Verify no CSS overrides with `position: static`

### Issue: Mobile menu not collapsing
**Solution**:
- Check `data-bs-toggle="collapse"` on toggle
- Check `data-bs-target="#navbarNav"` matches ID
- Verify Bootstrap JS included
- Check for JavaScript errors

### Issue: Search not appearing
**Solution**:
- Check responsive classes (`d-none d-lg-flex`)
- Verify screen width is > 992px
- Check CSS is loading correctly

### Issue: Notifications not loading
**Solution**:
- Check API endpoint: `/api/v1/notifications`
- Check CSRF token in page
- Check browser console for fetch errors
- Verify user has permission to view notifications

### Issue: User avatar not showing
**Solution**:
- Check user has avatar URL in database
- Check image URL is valid
- Check image permissions
- Fallback gravatar URL should load if broken

## Sign-Off

- [ ] All tests passed
- [ ] No critical issues
- [ ] Ready for production
- [ ] User documentation reviewed
- [ ] Team trained on new navbar

---

**Last Updated**: December 10, 2025  
**Version**: 1.0
