# Jira Clone - Verification & Testing Checklist

## Pre-Deployment Checklist

Before deploying to production, verify these items:

### Database Setup
- [ ] Database `jiira_clonee_system` exists
- [ ] All 67 tables imported from `database/schema.sql`
- [ ] Seed data imported from `database/seed.sql`
- [ ] MySQL service running on port 3306
- [ ] User has proper credentials (username: root)

### Configuration
- [ ] `config/config.local.php` created from `config/config.php`
- [ ] Database credentials configured correctly
- [ ] App URL configured: `http://localhost:8080/jira_clone_system/public`
- [ ] JWT secret configured and changed from default
- [ ] Application key configured and changed from default

### Apache Setup
- [ ] Apache running on port 8080
- [ ] `mod_rewrite` enabled
- [ ] `.htaccess` file present in `public/` folder
- [ ] Virtual host configured (optional but recommended)
- [ ] All directories writable: `storage/logs`, `storage/cache`, `public/uploads`

### File Permissions
- [ ] `storage/logs/` directory writable
- [ ] `storage/cache/` directory writable
- [ ] `public/uploads/` directory writable
- [ ] `public/.htaccess` file accessible

---

## Functional Testing Checklist

### Authentication
- [ ] Login with admin@example.com / Admin@123
- [ ] Login with john.smith@example.com / User@123
- [ ] Password reset flow works
- [ ] Session timeout works
- [ ] Logout clears session

### Dashboard
- [ ] Dashboard loads without errors
- [ ] Assigned issues display
- [ ] Recent activity shows
- [ ] Project list shows
- [ ] Quick stats display correctly

### Project Management
- [ ] View projects list - no 404 errors
- [ ] Create project - category dropdown populates
- [ ] Create project - lead dropdown populates
- [ ] Create project - form submits successfully
- [ ] Edit project - all data loads
- [ ] Delete project - confirmation works
- [ ] Project permissions enforced

### Issue Management
- [ ] Create issue - project dropdown populates
- [ ] Create issue - priority dropdown shows
- [ ] Create issue - assignee dropdown shows
- [ ] Create issue - form submits successfully
- [ ] Edit issue - all dropdowns have data
- [ ] Transition issue - status dropdown works
- [ ] Issue detail page loads
- [ ] Comments display and add correctly
- [ ] Attachments upload and display

### Boards
- [ ] View Kanban board - issues display
- [ ] View Scrum board - issues display
- [ ] Drag-and-drop issues - works
- [ ] Board filtering - functions
- [ ] Create board - no errors
- [ ] Edit board settings - works

### Sprints
- [ ] Create sprint - dates valid
- [ ] Plan sprint - add issues works
- [ ] Start sprint - transitions correctly
- [ ] Complete sprint - generates reports
- [ ] View sprint backlog - displays issues

### Search
- [ ] Quick search - finds issues by key
- [ ] Advanced search - filters work
- [ ] Search by status - dropdown populates
- [ ] Search results - display correctly
- [ ] Saved filters - save and load

### Reports
- [ ] Burndown chart loads - no errors
- [ ] Velocity report displays
- [ ] Cumulative flow shows data
- [ ] Workload report displays
- [ ] Export to PDF works

### Administration
- [ ] Admin dashboard loads
- [ ] Stats display correctly
- [ ] View users list - displays all users
- [ ] User filter by status - dropdown works
- [ ] User filter by role - dropdown works
- [ ] Create user - role dropdown populated
- [ ] Create user - timezone dropdown populated
- [ ] Create user - form submits
- [ ] Edit user - all fields display
- [ ] Edit user - password change works
- [ ] Edit user - role change works
- [ ] Delete user - confirmation works
- [ ] Audit log displays

### Settings
- [ ] Project settings load
- [ ] Version management works
- [ ] Component management works
- [ ] Custom field management works
- [ ] Workflow configuration works

---

## Dropdown-Specific Tests

### All Dropdown Data Tests

#### Admin - Create User
- [ ] Role dropdown shows: Admin, Project Manager, Developer, QA Tester, Viewer
- [ ] Timezone dropdown shows 500+ zones
- [ ] Status radio buttons show: Active, Inactive, Pending

#### Admin - Edit User
- [ ] Role dropdown pre-populates with user's role
- [ ] Timezone dropdown pre-populates with user's timezone
- [ ] Status shows current status

#### Admin - User Filter
- [ ] Status filter shows: All Status, Active, Inactive, Pending
- [ ] Role filter shows all roles

#### Projects - Create
- [ ] Category dropdown shows: Web Development, Mobile, Infrastructure
- [ ] Lead dropdown shows all active users

#### Projects - Edit
- [ ] Category dropdown pre-populates current category
- [ ] Lead dropdown pre-populates current lead

#### Issues - Create
- [ ] Project dropdown shows all active projects
- [ ] Priority dropdown shows: Blocker, Critical, Major, Minor, Trivial
- [ ] Assignee dropdown shows project members
- [ ] Labels multi-select shows project labels
- [ ] Components multi-select shows project components
- [ ] Fix Versions multi-select shows project versions

#### Issues - Edit
- [ ] All dropdowns pre-populate with current values
- [ ] Status dropdown shows available transitions
- [ ] Priority dropdown shows current priority

---

## Database Validation Tests

### Table Existence
- [ ] `users` table has records
- [ ] `roles` table has records
- [ ] `projects` table has records
- [ ] `issues` table has records
- [ ] `statuses` table has records (NOT `issue_statuses`)
- [ ] `issue_types` table has records
- [ ] `issue_priorities` table has records

### Query Tests
Run these SQL queries to verify data:

```sql
-- Check status data
SELECT COUNT(*) FROM statuses;  -- Should return > 0

-- Check roles data
SELECT COUNT(*) FROM roles;  -- Should return > 0

-- Check users data
SELECT COUNT(*) FROM users;  -- Should return > 0

-- Check projects data
SELECT COUNT(*) FROM projects;  -- Should return > 0

-- Check issues data
SELECT COUNT(*) FROM issues;  -- Should return > 0
```

---

## Error Log Checks

After testing, check these log files:

### Application Logs
- [ ] No errors in `storage/logs/` directory
- [ ] No "undefined variable" warnings
- [ ] No "table not found" errors
- [ ] No "column not found" errors

### Browser Console
- [ ] No JavaScript errors
- [ ] No CORS errors
- [ ] No CSP violations
- [ ] No 404 errors for assets

### Network Tab
- [ ] All API calls return 2xx or 3xx status
- [ ] No 500 errors
- [ ] Response times reasonable (<1s)
- [ ] No failed requests

---

## Security Verification

### Authentication Security
- [ ] Passwords hashed with Argon2ID
- [ ] Session tokens secure (HttpOnly, Secure flags)
- [ ] CSRF tokens on all forms
- [ ] No password visible in logs

### Authorization Security
- [ ] Non-admins cannot access admin panel
- [ ] Users cannot edit other users' data
- [ ] Permission checks enforced on all actions
- [ ] Audit log records all changes

### SQL Injection Prevention
- [ ] All queries use prepared statements
- [ ] No raw user input in SQL
- [ ] Special characters escaped properly

### XSS Prevention
- [ ] User input properly encoded
- [ ] No script tags rendered
- [ ] Content Security Policy headers set

---

## Performance Tests

### Page Load Times
- [ ] Dashboard loads in <1 second
- [ ] Projects list loads in <2 seconds
- [ ] Issues list loads in <2 seconds
- [ ] Boards load in <3 seconds
- [ ] Reports generate in <5 seconds

### Database Queries
- [ ] No N+1 query problems
- [ ] Queries use indexes efficiently
- [ ] Page does not make 50+ queries
- [ ] Long-running queries optimized

---

## Mobile Testing

### Responsive Design
- [ ] Navigation works on mobile
- [ ] Forms are mobile-friendly
- [ ] Dropdowns work on touch devices
- [ ] No horizontal scrolling needed
- [ ] Text is readable on small screens

---

## Browser Compatibility

Test on:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

Verify:
- [ ] All features work
- [ ] No console errors
- [ ] Styling renders correctly
- [ ] Forms submit properly

---

## User Acceptance Testing

### Sample User Flows

#### Flow 1: Create and Track an Issue
1. [ ] Login as project manager
2. [ ] Create a project
3. [ ] Create an issue in project
4. [ ] Assign issue to team member
5. [ ] Add comment to issue
6. [ ] Upload attachment
7. [ ] Transition issue through workflow
8. [ ] View in Kanban board

#### Flow 2: Sprint Planning
1. [ ] Login as project manager
2. [ ] Select project
3. [ ] Create sprint
4. [ ] Plan issues in sprint
5. [ ] View sprint backlog
6. [ ] Start sprint
7. [ ] View sprint board
8. [ ] Run burndown report

#### Flow 3: User Management (Admin)
1. [ ] Login as admin
2. [ ] Create new user
3. [ ] Assign role
4. [ ] Edit user password
5. [ ] Edit user timezone
6. [ ] View user in list
7. [ ] Filter by role
8. [ ] Delete test user

---

## Sign-Off

- [ ] All dropdown issues resolved
- [ ] All database queries working
- [ ] All data retrieving correctly
- [ ] All forms submitting successfully
- [ ] All security measures in place
- [ ] All performance acceptable
- [ ] No console errors
- [ ] No 404 errors
- [ ] Ready for production

---

**Date Tested**: _______________

**Tested By**: _______________

**Sign-Off**: _______________

