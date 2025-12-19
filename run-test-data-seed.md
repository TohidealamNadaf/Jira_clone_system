# Running the Comprehensive Test Data Seeder

## How to Run

### Option 1: Using Command Line (Recommended)
```bash
php scripts/seed-comprehensive-test-data.php
```

### Option 2: Using Web Browser
1. Open: `http://localhost:8080/jira_clone_system/public/run-seed.php`
2. Click "Seed Test Data" button
3. Wait for completion

## What Gets Created

### 📁 5 Test Projects
1. **E-Commerce Platform (ECOM)** - Main e-commerce platform
2. **Mobile App (MOB)** - iOS and Android apps
3. **Backend API (API)** - RESTful API services
4. **DevOps Infrastructure (DEVOPS)** - Cloud infrastructure
5. **QA & Testing (QA)** - Testing framework

### 📋 50+ Test Issues Per Project
Each project includes:
- **Overdue Issues** (5 days, 3 days, 2 days ago)
- **Due Soon** (today, tomorrow, 3 days)
- **Future Issues** (1 week, 10 days, 2 weeks ahead)
- **Completed Issues** (various past dates)

### 🎯 Comprehensive Test Scenarios

#### Issue Types
- ✅ Bug
- ✅ Feature
- ✅ Task
- ✅ Improvement

#### Statuses
- 🔴 Open
- 🟡 In Progress
- ✅ Done
- 🔵 Closed

#### Priorities
- 🚨 Urgent
- 🔴 High
- 🟡 Medium
- 🟢 Low

#### Issue Features
- ✅ Descriptions with formatting (bold, italic, lists, code)
- ✅ Assigned to different team members
- ✅ Reported by various users
- ✅ Due dates (overdue, today, future)
- ✅ Story points (1-13)
- ✅ Linked issues (related, duplicates, etc.)
- ✅ Comments from team members
- ✅ Work logs with hours tracked

## What to Test

### 1. Dashboard
- [ ] View overdue issues in red
- [ ] See issues due today in orange
- [ ] Check upcoming issues
- [ ] View team workload distribution
- [ ] See recent activity

### 2. Project Overview
- [ ] Navigate to each project
- [ ] View project statistics
- [ ] Check team members
- [ ] See recent activity
- [ ] Review project details

### 3. Kanban Board
- [ ] Drag issues between columns (Open → In Progress → Done)
- [ ] View all issues on board
- [ ] Filter by assignee
- [ ] Sort by priority
- [ ] Group by status

### 4. Issue List
- [ ] View all issues in table format
- [ ] Filter by project
- [ ] Sort by due date
- [ ] Search by summary
- [ ] Check overdue count
- [ ] See priority indicators

### 5. Issue Detail
- [ ] Click on issue to view details
- [ ] Check formatted description (bold, lists, code)
- [ ] View assignee and reporter
- [ ] See due date with overdue indicator
- [ ] Check comments and discussions
- [ ] View work logs
- [ ] See linked issues

### 6. Reports
- [ ] Check Created vs Resolved report
- [ ] View Resolution Time analysis
- [ ] Check Priority Breakdown pie chart
- [ ] View Time Logged report
- [ ] Check Estimate Accuracy
- [ ] View Team Workload

### 7. Calendar & Roadmap
- [ ] View issues on calendar
- [ ] Check due date highlights
- [ ] View roadmap timeline
- [ ] See project progress

### 8. Search & Filtering
- [ ] Search for "bug" or "feature"
- [ ] Filter by status
- [ ] Filter by assignee
- [ ] Filter by priority
- [ ] Filter by due date
- [ ] Combine multiple filters

### 9. Overdue Issues
- [ ] Dashboard shows overdue count
- [ ] Search shows overdue issues
- [ ] Projects page highlights overdue
- [ ] Reports show overdue metrics

### 10. Notifications
- [ ] Create new issue
- [ ] Assign to team member
- [ ] Check notifications
- [ ] View notification details
- [ ] Mark as read

## Test Data Summary

After running the seeder, you'll have:
- ✅ 5 active projects
- ✅ 50+ issues with various statuses
- ✅ 10+ overdue issues
- ✅ 50+ comments
- ✅ 20+ work logs
- ✅ Multiple linked issues
- ✅ Complete team assignments
- ✅ Realistic due dates

## Next Steps

1. Run the seeder script
2. Navigate to Dashboard
3. Work through the test scenarios above
4. Check that all formatting displays correctly
5. Verify overdue indicators appear
6. Test filtering and sorting
7. Check reports for accuracy

## Troubleshooting

### Script doesn't run
- Make sure you're in the project root directory
- Check PHP is installed: `php -v`
- Verify database connection in config.php

### No projects appear
- Check that database seeding completed
- Verify projects table has data: `SELECT * FROM projects;`
- Check user permissions

### Issues not showing
- Verify issue_types table is populated
- Check statuses table has data
- Ensure users exist in database

### Data looks incomplete
- Re-run the seeder
- Clear browser cache
- Refresh the page

## Support

For issues or questions:
1. Check the database directly with SQL
2. Review browser console for errors (F12)
3. Check application logs
4. Verify all required tables exist
