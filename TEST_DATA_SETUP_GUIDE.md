# Comprehensive Test Data Setup Guide

## Overview

This guide helps you create realistic test data to fully test your Jira Clone system. The test data includes:

- ✅ **5 Active Projects** with different purposes
- ✅ **50+ Issues** with various types, statuses, and priorities
- ✅ **Overdue Issues** for testing overdue indicators
- ✅ **Due Dates** across different timeframes (past, today, future)
- ✅ **50+ Comments** from team members
- ✅ **20+ Work Logs** with time tracking
- ✅ **Linked Issues** showing relationships
- ✅ **Formatted Descriptions** with bold, lists, code blocks

---

## Method 1: Using SQL (Recommended - Fastest)

### Step 1: Open Database Client
- **MySQL Workbench**, **phpMyAdmin**, or **HeidiSQL**
- Connect to your database: `jiira_clonee_system`

### Step 2: Import SQL File
- Open file: `database/seed-test-data.sql`
- Execute entire script
- Wait for completion message

### Step 3: Verify Data
```sql
-- Check projects created
SELECT * FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA');

-- Check issues count
SELECT project_id, COUNT(*) as issue_count FROM issues GROUP BY project_id;

-- Check overdue issues
SELECT * FROM issues WHERE due_date < CURDATE() AND status_id != (SELECT id FROM statuses WHERE name = 'Done');
```

---

## Method 2: Using PHP Script

### Step 1: Run from Command Line
```bash
cd c:\laragon\www\jira_clone_system
php scripts/seed-comprehensive-test-data.php
```

### Step 2: Expected Output
```
🌱 Comprehensive Test Data Seeder Started
==========================================

📋 Loading users...
✅ Loaded 5 users

📋 Loading issue types...
✅ Loaded 4 issue types

🚀 Creating test projects...
   ✅ Created project: ECOM - E-Commerce Platform
   ✅ Created project: MOB - Mobile App
   ✅ Created project: API - Backend API
   ✅ Created project: DEVOPS - DevOps Infrastructure
   ✅ Created project: QA - QA & Testing

📝 Creating test issues for each project...
   ✅ Created 50+ issues

🔗 Linking related issues...
   ✅ Linked relationships

💬 Adding comments...
   ✅ Added 50+ comments

⏱️  Adding work logs...
   ✅ Added 20+ work logs

✅ Test data seeding completed successfully!
```

### Step 3: Verify in Browser
- Navigate to: `http://localhost:8080/jira_clone_system/public/`
- Check Dashboard for new data

---

## Method 3: Using Web Interface

### Step 1: Open Browser
- Go to: `http://localhost:8080/jira_clone_system/public/seed-test-data.php`

### Step 2: Review Data Summary
- See what will be created
- Understand test scenarios

### Step 3: Click "Create Test Data"
- Wait for seeding to complete
- See progress in log output

### Step 4: Navigate to Dashboard
- Click "Go to Dashboard" button
- View newly created data

---

## Test Projects Created

### 1. E-Commerce Platform (ECOM)
**Purpose**: Main e-commerce platform for online retail

**Issues Included**:
- 🚨 Fix critical bug in checkout (OVERDUE 5 days)
- 🔄 Update user authentication (OVERDUE 2 days, In Progress)
- ⚡ Performance optimization (OVERDUE 3 days)
- 🎯 Implement dark mode (Due Today)
- 📊 Database migration (Due Tomorrow, In Progress)
- 📝 Write API documentation (Due in 3 days)
- 🔔 Implement push notifications (Due in 7 days)
- ✅ Fix responsive design (COMPLETED)
- ✅ Optimize database indexes (COMPLETED)

### 2. Mobile App (MOB)
**Purpose**: iOS and Android application development

**Issues Included**:
- 🔴 Fix crash on app startup (OVERDUE 4 days, Critical)
- 📵 Implement offline mode (In Progress)
- 🎨 Update UI to new design (Due in 2 days)
- 🔐 Add biometric authentication (Due in 14 days)
- ✅ Fix memory leaks (COMPLETED)

### 3. Backend API (API)
**Purpose**: RESTful API development

**Issues Included**:
- 🚫 Rate limiting not working (OVERDUE 3 days)
- 📊 Implement GraphQL endpoint (Due in 5 days)
- ✅ Add webhook support (COMPLETED)
- ⚡ Improve response time (Due in 21 days)

### 4. DevOps Infrastructure (DEVOPS)
**Purpose**: Cloud infrastructure and CI/CD

**Issues Included**:
- 🔧 Upgrade Kubernetes cluster (Due Today, In Progress)
- 📈 Set up monitoring/alerting (Due in 4 days)
- ⚡ Improve CI/CD pipeline (Due in 12 days)

### 5. QA & Testing (QA)
**Purpose**: Quality assurance and testing

**Issues Included**:
- 🧪 Create automated test suite (Due in 7 days, In Progress)
- 📱 Test authentication on all devices (Due in 2 days)
- ✅ Update test documentation (COMPLETED)

---

## Test Scenarios Available

### 1. Overdue Issues Testing
- **Dashboard**: Red overdue indicators
- **Search**: Filter by overdue
- **Reports**: Overdue metrics
- **Notifications**: Overdue alerts

**Test Cases**:
- [ ] Dashboard shows overdue count
- [ ] Overdue issues appear in red
- [ ] Search filters overdue correctly
- [ ] Reports show overdue trends
- [ ] Notifications alert about overdue

### 2. Due Date Tracking
- **Today**: 2 issues
- **Tomorrow**: 2 issues
- **3 days**: 2 issues
- **7 days**: 2 issues
- **14 days**: 1 issue

**Test Cases**:
- [ ] Calendar shows all due dates
- [ ] Filter by "Due Soon"
- [ ] Sort by due date
- [ ] Upcoming alerts show correctly
- [ ] Date highlighting works

### 3. Issue Status Management
- **Open**: 20 issues (red)
- **In Progress**: 8 issues (yellow)
- **Done**: 7 issues (green)

**Test Cases**:
- [ ] Drag/drop between columns
- [ ] Status updates persist
- [ ] Color coding displays
- [ ] Status badges show
- [ ] Count badges update

### 4. Priority Filtering
- **Urgent**: 2 issues (critical work)
- **High**: 8 issues (important)
- **Medium**: 10 issues (normal)
- **Low**: 7 issues (future)

**Test Cases**:
- [ ] Filter by priority
- [ ] Priority icons display
- [ ] Sort by priority
- [ ] Color coding correct
- [ ] Reports show distribution

### 5. Team Assignment
Multiple users assigned across projects:
- Different assignees per issue
- Reporter vs Assignee distinction
- Team member workload distribution

**Test Cases**:
- [ ] Assignees display correctly
- [ ] Filter by assignee
- [ ] Reassign issues
- [ ] Workload distribution shows
- [ ] User profiles link correctly

### 6. Issue Types Testing
- Bug (3 issues)
- Feature (8 issues)
- Task (9 issues)
- Improvement (3 issues)

**Test Cases**:
- [ ] Icons display per type
- [ ] Filter by type
- [ ] Sort by type
- [ ] Type colors show
- [ ] Default fields per type

### 7. Description Formatting
Issues with formatted descriptions:
- **Bold** text: `<strong>text</strong>`
- *Italic* text: `<em>text</em>`
- Lists: `<ul><li>item</li></ul>`
- Code blocks: `<pre><code>code</code></pre>`
- Blockquotes: `<blockquote>quote</blockquote>`

**Test Cases**:
- [ ] Bold displays bold
- [ ] Italic displays italic
- [ ] Lists render properly
- [ ] Code blocks have gray background
- [ ] Blockquotes indent with left border

### 8. Comments & Collaboration
8+ comments across issues:
- Team discussions
- Work updates
- Status mentions
- Task blocking info

**Test Cases**:
- [ ] Comments display
- [ ] Add new comment
- [ ] Edit comment
- [ ] Delete comment
- [ ] Comment timestamps show

### 9. Work Logs & Time Tracking
20+ work log entries:
- Hours logged per issue
- Multiple entries per issue
- Dates and descriptions

**Test Cases**:
- [ ] View work logs
- [ ] Add work log
- [ ] Hours sum correctly
- [ ] Date history shows
- [ ] User attribution displays

### 10. Linked Issues
Issues with relationships:
- Related issues
- Blocking relationships
- Dependencies

**Test Cases**:
- [ ] Links display
- [ ] Link direction shows
- [ ] Navigate between links
- [ ] Create new link
- [ ] Delete link

---

## Testing Workflow

### Phase 1: Data Verification (5 minutes)
1. [ ] Login to dashboard
2. [ ] Count visible projects (should be 5)
3. [ ] Count visible issues (should be 50+)
4. [ ] Check overdue count in dashboard
5. [ ] Verify user assignments

### Phase 2: Dashboard Testing (10 minutes)
1. [ ] Check Dashboard shows statistics
2. [ ] Verify overdue issues highlight
3. [ ] Check due soon section
4. [ ] View team workload
5. [ ] Check recent activity

### Phase 3: Project Navigation (10 minutes)
1. [ ] Visit ECOM project
2. [ ] View issues list
3. [ ] Check board/kanban view
4. [ ] Verify statistics
5. [ ] Check team members

### Phase 4: Issue Detail Testing (10 minutes)
1. [ ] Click on overdue issue
2. [ ] Check formatted description
3. [ ] View assignee/reporter
4. [ ] See due date indicator
5. [ ] Check comments
6. [ ] View work logs

### Phase 5: Filtering & Search (10 minutes)
1. [ ] Filter by status
2. [ ] Filter by priority
3. [ ] Filter by assignee
4. [ ] Search by keyword
5. [ ] Sort by due date
6. [ ] Combine multiple filters

### Phase 6: Board Operations (10 minutes)
1. [ ] View kanban board
2. [ ] Drag issue between columns
3. [ ] Verify status updates
4. [ ] Check drag-drop persistence
5. [ ] Test on different projects

### Phase 7: Reports (10 minutes)
1. [ ] View Created vs Resolved
2. [ ] Check Resolution Time
3. [ ] View Priority Breakdown
4. [ ] Check Time Logged
5. [ ] View Team Workload

### Phase 8: Advanced Features (10 minutes)
1. [ ] View Calendar
2. [ ] Check Roadmap
3. [ ] Add comment to issue
4. [ ] Log work
5. [ ] Link issues

---

## Troubleshooting

### Issue: No data appears after seeding

**Solutions**:
1. Refresh browser (Ctrl+F5)
2. Check database directly: `SELECT COUNT(*) FROM issues;`
3. Verify projects exist: `SELECT * FROM projects WHERE key = 'ECOM';`
4. Check user permissions
5. Restart application

### Issue: Overdue issues not showing as overdue

**Solutions**:
1. Check system date is correct
2. Verify issue due_date is in past
3. Check issue status is not "Done"
4. Query: `SELECT * FROM issues WHERE due_date < CURDATE();`

### Issue: Comments or work logs not visible

**Solutions**:
1. Refresh page
2. Check issue detail page loads
3. Verify entries in database
4. Check JavaScript console for errors

### Issue: Images/avatars not showing

**Solutions**:
1. Generate missing user avatars
2. Clear browser cache
3. Check avatar file paths
4. Verify user profile pictures

---

## Clean Up (If Needed)

### Delete All Test Data
```sql
-- Delete in order (respecting foreign keys)
DELETE FROM worklogs WHERE issue_id IN (SELECT id FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')));
DELETE FROM comments WHERE issue_id IN (SELECT id FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')));
DELETE FROM issue_links WHERE from_issue_id IN (SELECT id FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')));
DELETE FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA'));
DELETE FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA');
```

---

## Next Steps

After setting up test data:

1. **Explore Dashboard**: See overview of all projects
2. **Test Each Feature**: Try all filtering, sorting, and operations
3. **Test Mobile**: Check responsiveness on tablet/mobile
4. **Performance Test**: Load testing with 50+ issues
5. **User Testing**: Have team members try it out
6. **Feedback**: Gather feedback on UX
7. **Refinement**: Improve based on feedback
8. **Documentation**: Document findings

---

## Statistics After Seeding

```
Total Projects: 5
Total Issues: 50+
Total Comments: 8+
Total Work Logs: 20+

Status Distribution:
  Open: 20 issues (40%)
  In Progress: 8 issues (16%)
  Done: 7 issues (14%)

Priority Distribution:
  Urgent: 2 issues (4%)
  High: 8 issues (16%)
  Medium: 10 issues (20%)
  Low: 7 issues (14%)

Due Date Distribution:
  Overdue: 10+ issues
  Due Today: 2 issues
  Due Soon (1-3 days): 4 issues
  Future (7+ days): 8 issues

Issue Type Distribution:
  Bug: 3 issues
  Feature: 8 issues
  Task: 9 issues
  Improvement: 3 issues
```

---

**Total Setup Time**: 5-15 minutes
**Testing Time**: 1-2 hours for comprehensive testing
**Ready for**: Production testing, user acceptance testing, performance benchmarking

✅ **You're ready to test the system!**
