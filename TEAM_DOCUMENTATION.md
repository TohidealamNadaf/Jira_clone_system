# Jira Clone System - Team Documentation

**Last Updated:** December 2025  
**Status:** Production Ready ✅

---

## Table of Contents

1. [Quick Start](#quick-start)
2. [System Overview](#system-overview)
3. [Core Features](#core-features)
4. [All Pages & Features](#all-pages--features)
5. [User Roles & Permissions](#user-roles--permissions)
6. [Workflow Guide](#workflow-guide)
7. [API Documentation](#api-documentation)
8. [Troubleshooting](#troubleshooting)
9. [Technical Stack](#technical-stack)
10. [Common Tasks](#common-tasks)

---

## Quick Start

### Access the System
- **URL:** `http://localhost/jira_clone_system/public/`
- **Test Admin Account:**
  - Email: `admin@example.com`
  - Password: `Admin@123`
- **Test User Account:**
  - Email: `user@example.com`
  - Password: `User@123`

### First Steps
1. Log in with your credentials
2. Navigate to **Dashboard** to see an overview of your projects
3. Click **Projects** to view or create a project
4. Create your first issue within a project

---

## System Overview

### What is This System?

A **Jira Clone** is a comprehensive project management and issue tracking system designed for software development teams. It helps track:
- Project planning and organization
- Issue creation and lifecycle management
- Sprint planning and execution
- Team collaboration through comments and mentions
- Time tracking and reporting
- Progress monitoring through various reports

### Key Capabilities

| Feature | Purpose |
|---------|---------|
| **Projects** | Organize work into logical groupings |
| **Issues** | Track bugs, features, and tasks |
| **Sprints** | Plan and execute short iteration cycles |
| **Boards** | Visual representation of workflow (Kanban) |
| **Reports** | Analyze team performance and progress |
| **Notifications** | Stay updated on changes |
| **API** | Integrate with external systems |

---

## Core Features

### 1. **Projects** 🗂️

**Purpose:** Organize work into separate project containers

**What You Can Do:**
- Create new projects with unique keys (e.g., "PROJ")
- Add project members and assign roles
- Configure project settings and workflows
- Track all issues within the project
- View project activity and reports

**Access:** Main sidebar → **Projects** or Dashboard cards

**Key Sections:**
- **Overview:** Project summary and recent activity
- **Board:** Kanban view of issues in workflow
- **Backlog:** All issues not yet started
- **Sprints:** Sprint planning and execution
- **Reports:** Analytics and metrics
- **Activity:** Audit trail of all changes
- **Settings:** Project configuration

---

### 2. **Issues** 🎯

**Purpose:** Track individual units of work (bugs, features, tasks, etc.)

**Issue Types:**
- **Story** - Feature request or user requirement
- **Task** - Generic work item
- **Bug** - Error or defect
- **Epic** - Large body of work (multiple issues)
- **Sub-task** - Child of another issue

**Issue Lifecycle:**
```
To Do → In Progress → In Review → Done
```

**What You Can Do with Issues:**
- Create issues with title, description, priority
- Assign to team members
- Add labels for categorization
- Link related issues
- Add comments and attachments
- Track time spent (worklogs)
- Watch issues for notifications
- Vote on issues
- Transition through workflow states

**Quick Create Modal:**
- Click "Create" button in navbar
- Select project and issue type
- Fill in details
- Issues appear on boards immediately

---

### 3. **Boards** 📋

**Purpose:** Visual Kanban-style view of work in progress

**How It Works:**
- Columns represent workflow states (To Do, In Progress, Done, etc.)
- Issues appear as cards
- **Drag and drop** issues between columns to change status
- Hover over cards to see details
- Color-coded by priority

**Board Features:**
- **Create Board:** Organize issues by project
- **Backlog View:** Issues waiting to be started
- **Board Settings:** Configure workflow columns
- **Filtering:** Filter by assignee, priority, labels
- **Real-time Updates:** Changes sync immediately

**Typical Workflow:**
1. Issues start in "To Do" column
2. Drag to "In Progress" when starting work
3. Move to "In Review" when ready for review
4. Move to "Done" when completed

---

### 4. **Sprints** 🏃

**Purpose:** Organize work into time-boxed iterations (typically 1-2 weeks)

**Sprint Lifecycle:**
```
Plan → Start → Execute → Complete
```

**What You Can Do:**
- Create sprints with dates and goals
- Add issues to sprints (from backlog)
- Start sprint to begin execution
- Track progress in real-time
- View sprint reports (velocity, burndown)
- Complete sprint to archive issues

**Key Concepts:**
- **Backlog Issues:** Not assigned to any sprint
- **Sprint Issues:** Issues being worked on
- **Velocity:** Avg story points completed per sprint
- **Burndown:** Ideal vs actual progress chart

---

### 5. **Notifications** 🔔

**Purpose:** Keep team members informed of changes

**What Triggers Notifications:**
- Issue assigned to you
- Comment on watched issues
- Status changes on watched issues
- Mentions (using @ in comments)

**Notification Channels:**
- **In-App:** Bell icon in navbar, `/notifications` page
- **Email:** Configured in profile preferences (Phase 2)
- **Push:** Browser notifications (Phase 2)

**Managing Notifications:**
- Profile → Notifications Settings
- Toggle channels on/off per event type
- Mark as read/archive
- Delete individual notifications

---

### 6. **Reports** 📊

**Available Reports:**

| Report | Purpose | View |
|--------|---------|------|
| **Velocity** | Story points completed per sprint | Line chart |
| **Burndown** | Remaining work vs time | Line chart |
| **Sprint Stats** | Issues completed, velocity metrics | Numbers |
| **Cumulative Flow** | Work in each status over time | Area chart |
| **Created vs Resolved** | Issue creation vs resolution rate | Line chart |
| **Priority Breakdown** | Issues by priority level | Pie chart |
| **Time Logged** | Hours worked by team member | Table |
| **Resolution Time** | Avg hours to resolve issues | Numbers |
| **Estimate Accuracy** | Estimated vs actual time | Table |
| **Version Progress** | Release completion percentage | Numbers |
| **Release Burndown** | Release progress over time | Line chart |
| **Workload** | Hours per team member | Table |

**How to Access:**
- Reports menu in navbar
- Or project-specific reports via project settings

---

### 7. **Search & Filters** 🔍

**Quick Search:**
- Navbar search box (Ctrl+K)
- Find issues by key, title, or description

**Advanced Search:**
- `/search/advanced` page
- Filter by project, status, assignee, priority, labels
- Save custom filters for reuse

**Filter Features:**
- Text search across issue fields
- Multiple condition filters
- Sort results
- Save as personal filter

---

### 8. **Comments & Collaboration** 💬

**Comment on Issues:**
- Add thoughts, questions, updates
- Edit your own comments
- Delete your comments
- Mention teammates using `@username`
- Add attachments to comments

**Activity Timeline:**
- See all changes to an issue
- Track status transitions
- See who changed what and when

---

### 9. **Worklogs (Time Tracking)** ⏱️

**Log Time Spent:**
- Track hours spent on each issue
- Use for team capacity planning
- Feed time-logged reports

**How to Log Work:**
- Issue detail page → Log Work button
- Enter hours and date
- Updates time-logged report

---

### 10. **Attachments** 📎

**Add Files to Issues:**
- Screenshots, documents, design files
- Drag-and-drop upload
- Download attached files
- Delete when no longer needed

---

## All Pages & Features

### Public Pages
| Page | URL | Purpose |
|------|-----|---------|
| Login | `/login` | User authentication |
| Forgot Password | `/forgot-password` | Password recovery |
| Password Reset | `/reset-password/{token}` | Set new password |
| API Docs | `/api/docs` | REST API documentation |

### Dashboard Pages (After Login)
| Page | URL | Purpose |
|------|-----|---------|
| **Dashboard** | `/dashboard` | Overview of all projects & issues |
| **Projects List** | `/projects` | All projects user has access to |
| **Create Project** | `/projects/create` | New project wizard |
| **Project Overview** | `/projects/{key}` | Single project details |
| **Project Board** | `/projects/{key}/board` | Kanban board view |
| **Project Backlog** | `/projects/{key}/backlog` | Unstarted issues list |
| **Project Sprints** | `/projects/{key}/sprints` | Sprint management |
| **Project Activity** | `/projects/{key}/activity` | Change audit trail |
| **Project Reports** | `/projects/{key}/reports` | Project-specific analytics |
| **Project Settings** | `/projects/{key}/settings` | Configure project |
| **Project Members** | `/projects/{key}/members` | Team management |

### Issue Pages
| Page | URL | Purpose |
|------|-----|---------|
| **Issue Detail** | `/issue/{issueKey}` | Full issue view & editing |
| **Create Issue** | `/projects/{key}/issues/create` | New issue form |
| **Edit Issue** | `/issue/{issueKey}/edit` | Modify issue details |
| **Issues List** | `/projects/{key}/issues` | All project issues |

### Search & Filter Pages
| Page | URL | Purpose |
|------|-----|---------|
| **Search** | `/search` | Basic search results |
| **Advanced Search** | `/search/advanced` | Detailed filtering |
| **Quick Search** | `/search/quick` | Navbar dropdown search |
| **Saved Filters** | `/filters` | User's custom filters |

### Report Pages
| Page | URL | Purpose |
|------|-----|---------|
| **Reports Hub** | `/reports` | All reports overview |
| **Sprint Report** | `/reports/sprint` | Sprint metrics |
| **Burndown** | `/reports/burndown/{sprintId}` | Sprint progress chart |
| **Velocity** | `/reports/velocity/{boardId}` | Historical velocity trends |
| **Cumulative Flow** | `/reports/cumulative-flow/{boardId}` | Workflow status tracking |
| **Created vs Resolved** | `/reports/created-vs-resolved` | Issue velocity trends |
| **Resolution Time** | `/reports/resolution-time` | Time to fix metrics |
| **Priority Breakdown** | `/reports/priority-breakdown` | Issue distribution |
| **Time Logged** | `/reports/time-logged` | Team time tracking |
| **Estimate Accuracy** | `/reports/time-estimate-accuracy` | Estimation vs reality |
| **Version Progress** | `/reports/version-progress` | Release status |
| **Release Burndown** | `/reports/release-burndown` | Release progress |

### User Profile Pages
| Page | URL | Purpose |
|------|-----|---------|
| **Profile** | `/profile` | Personal information |
| **Notifications** | `/profile/notifications` | Notification preferences |
| **Security** | `/profile/security` | Password & auth settings |
| **API Tokens** | `/profile/tokens` | Create API access tokens |
| **My Notifications** | `/notifications` | Notification inbox |

### Admin Pages (Admin Only)
| Page | URL | Purpose |
|------|-----|---------|
| **Admin Dashboard** | `/admin` | System overview & stats |
| **User Management** | `/admin/users` | Create/edit/delete users |
| **Create User** | `/admin/users/create` | New user form |
| **Edit User** | `/admin/users/{id}/edit` | Modify user |
| **Role Management** | `/admin/roles` | System & custom roles |
| **Create Role** | `/admin/roles/create` | New role form |
| **Edit Role** | `/admin/roles/{id}/edit` | Modify role |
| **Global Permissions** | `/admin/global-permissions` | System-wide permissions |
| **Issue Types** | `/admin/issue-types` | Manage issue types |
| **Project Categories** | `/admin/project-categories` | Issue classification |
| **Projects Management** | `/admin/projects` | System project view |
| **Audit Log** | `/admin/audit-log` | System activity trail |
| **System Settings** | `/admin/settings` | Email, SMTP configuration |

---

## User Roles & Permissions

### System Roles (Built-in)

| Role | Permissions | Use Case |
|------|-----------|----------|
| **Administrator** | Full system access | IT team, system owners |
| **Developer** | Create issues, update status, comment, log time | Development team |
| **Project Manager** | Manage projects, sprints, team | Team leads, product owners |
| **QA Tester** | Create issues, view reports | QA/Testing team |
| **Viewer** | View projects/issues only | Stakeholders, read-only access |

### Custom Roles

Admins can create custom roles with specific permission combinations.

### Permission Categories

- **Projects:** Create, view, edit, delete
- **Issues:** Create, view, edit, delete, assign
- **Sprints:** Create, manage, start, complete
- **Reports:** View reports, analytics
- **Users:** Manage team members
- **Settings:** Configure project settings
- **Admin:** System administration

### Permission Matrix

| Action | Regular Users | Admins | Custom Roles |
|--------|:---:|:---:|:---:|
| View Items | ✅ | ✅ | ✅ |
| Create Items | ✅ | ✅ | ✅ |
| Edit Own Items | ✅ | ❌ | ✅ |
| Delete Own Items | ✅ | ❌ | ✅ |
| Edit Others' Items | ✅ | ❌ | ✅ |
| Manage Users | ❌ | ✅ | ✅ |
| System Settings | ❌ | ✅ | ✅ |

**Note:** Administrators cannot edit or delete other administrators. This protects system integrity.

---

## Workflow Guide

### Typical Daily Workflow

#### Morning: Check Dashboard
1. Log in to system
2. View Dashboard for project overview
3. Check Notifications (bell icon)
4. Read any comments on your assigned issues

#### During Day: Work on Issues
1. Go to Board view of your project
2. Find "In Progress" issues assigned to you
3. Update issue status as work progresses
4. Add comments with progress updates
5. Log time spent on the issue
6. When done, move to "Done" column

#### Sprint Planning: Weekly
1. Navigate to Project → Sprints
2. Create new sprint with dates
3. From backlog, drag issues into sprint
4. Estimate story points if using that system
5. Click "Start Sprint"

#### Sprint Reporting: End of Sprint
1. Go to Project → Reports
2. View Sprint Burndown chart
3. Check Velocity (historical trends)
4. Review Time Logged report
5. Complete the sprint (archives issues)

#### Creating an Issue: When New Work Needed
1. Click "Create" button (navbar)
2. Select project
3. Choose issue type
4. Enter title and description
5. Set priority and other fields
6. Assign to yourself or team member
7. Submit

#### Collaborating on Issue: Team Work
1. Open issue detail page
2. Read description and comments
3. Add comments with updates/questions
4. Mention teammate with `@username`
5. Add attachments if needed
6. Click "Watch" to get notifications

---

## API Documentation

### Base URL
```
/api/v1
```

### Authentication
All API endpoints (except login) require JWT token:
```
Authorization: Bearer {token}
```

### Get API Token
1. Log in to system
2. Profile → API Tokens
3. Create new token
4. Copy token (shown once)

### Common API Endpoints

#### Authentication
```
POST   /auth/login          - Login and get token
POST   /auth/logout         - Logout
POST   /auth/refresh        - Refresh token
GET    /me                  - Current user info
```

#### Projects
```
GET    /projects            - List all projects
POST   /projects            - Create project
GET    /projects/{key}      - Get project details
PUT    /projects/{key}      - Update project
DELETE /projects/{key}      - Delete project
```

#### Issues
```
GET    /issues              - List issues (filtered)
POST   /issues              - Create issue
GET    /issues/{key}        - Get issue details
PUT    /issues/{key}        - Update issue
DELETE /issues/{key}        - Delete issue
POST   /issues/{key}/transitions - Change status
PUT    /issues/{key}/assignee - Assign issue
```

#### Comments
```
GET    /issues/{key}/comments - List comments
POST   /issues/{key}/comments - Add comment
PUT    /comments/{id}       - Edit comment
DELETE /comments/{id}       - Delete comment
```

#### Sprints
```
GET    /boards/{boardId}/sprints - List sprints
POST   /boards/{boardId}/sprints - Create sprint
POST   /sprints/{id}/start   - Start sprint
POST   /sprints/{id}/complete - Complete sprint
```

#### Notifications
```
GET    /notifications/preferences - Get settings
POST   /notifications/preferences - Update settings
GET    /notifications           - List notifications
PATCH  /notifications/{id}/read  - Mark as read
```

### Example API Usage

**Get JWT Token:**
```bash
curl -X POST http://localhost/jira_clone_system/public/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "token": "eyJhbGc...",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "John Doe"
  }
}
```

**List Projects:**
```bash
curl http://localhost/jira_clone_system/public/api/v1/projects \
  -H "Authorization: Bearer eyJhbGc..."
```

---

## Troubleshooting

### Common Issues & Solutions

#### Issue: Can't log in
**Solution:** 
- Verify username/email is correct
- Check if account is active (not deactivated)
- Use password reset if forgotten
- Ask admin to check user status

#### Issue: Can't see project
**Solution:**
- Verify you're a project member (Project → Members)
- Check your role has project access
- Ask project lead to add you
- Contact admin if still not visible

#### Issue: Issue not appearing on board
**Solution:**
- Verify issue status is one of the board columns
- Check if issue is filtered out (check filters)
- Refresh the page
- Try different browser

#### Issue: Notifications not working
**Solution:**
- Check notification preferences (Profile → Notifications)
- Verify you're watching the issue
- Check if issue has matching triggers
- Verify email settings if using email channel

#### Issue: Can't edit issue
**Solution:**
- Check if you have edit permission
- Verify issue isn't archived
- Check if you're the assignee (some actions need this)
- Ask project admin for permission

#### Issue: Report shows no data
**Solution:**
- Check if project/sprint has issues
- Verify date range includes data
- Try selecting different sprint/project
- Refresh page

#### Issue: Can't attach file
**Solution:**
- Check file size (limits may apply)
- Verify file format is allowed
- Try different file type
- Check internet connection

#### Issue: Permission denied on admin page
**Solution:**
- Only admins can access `/admin`
- Ask another admin to grant admin role
- Create issue for system administrator

---

## Technical Stack

### Technology Stack
- **Backend:** PHP 8.2+ with custom framework
- **Database:** MySQL 8
- **Frontend:** Bootstrap 5, vanilla JavaScript
- **API:** RESTful with JWT authentication
- **Authentication:** Argon2id password hashing
- **Architecture:** MVC (Model-View-Controller)

### Coding Standards
- Strict types required (`declare(strict_types=1);`)
- Type hints on all functions
- PSR-4 autoloader
- Prepared statements for all queries
- CSRF protection on all forms

### Database Structure
- **users** - User accounts
- **projects** - Project containers
- **issues** - Individual work items
- **boards** - Kanban boards
- **sprints** - Sprint iterations
- **comments** - Issue discussion
- **notifications** - System notifications
- **audit_log** - Change tracking
- Plus supporting tables for attachments, worklogs, links, etc.

---

## Common Tasks

### Create a New Project
1. Click **Projects** in sidebar
2. Click **Create Project** button
3. Fill in:
   - Project Name (e.g., "Mobile App")
   - Project Key (e.g., "MOBILE" - auto-generated)
   - Description
   - Category (optional)
4. Click **Create**
5. Project appears in your projects list

### Add Team Member to Project
1. Go to Project → Members
2. Click **Add Member** button
3. Select user from dropdown
4. Choose role (Developer, QA, etc.)
5. Click **Add**
6. Member can now see project

### Create Issue in Project
**Quick Method:**
1. Click **Create** button (navbar)
2. Select project
3. Enter title & details
4. Submit

**Detailed Method:**
1. Go to Project → Issues
2. Click **Create Issue** button
3. Fill all fields:
   - Type, Title, Description
   - Priority, Labels, Estimate
   - Assignee
4. Click **Create**

### Start Sprint
1. Go to Project → Sprints
2. Click **Create Sprint**
3. Set dates and goal
4. Drag issues from backlog to sprint
5. Click **Start Sprint**
6. Issues now visible on board

### Log Time on Issue
1. Open issue detail page
2. Scroll to "Time Tracking" section
3. Click **Log Work**
4. Enter:
   - Hours spent
   - Date worked
   - Description (optional)
5. Click **Log**
6. Time appears in reports

### Generate Sprint Report
1. Go to Reports → Sprint
2. Select project and sprint
3. View metrics:
   - Issues completed
   - Velocity
   - Time tracked
4. Share with team

### Run a Report
1. Click **Reports** in navbar
2. Choose report type
3. Select filters (project, date range, etc.)
4. View results
5. Export if needed

### Configure Notification Preferences
1. Click profile icon (navbar)
2. Select **Notifications**
3. For each event type, choose:
   - In-App: Yes/No
   - Email: Yes/No
   - Push: Yes/No
4. Click **Save**

### Create Custom Filter
1. Go to Search → Advanced
2. Set up filter criteria:
   - Status, Assignee, Priority, etc.
3. Click **Search**
4. Click **Save Filter**
5. Name your filter
6. Use from Filters page later

### Manage User Account (Admin)
1. Go to Admin → Users
2. Click user to edit
3. Modify:
   - Name, Email
   - Role assignment
   - Active status
4. Click **Save**
5. To delete: Click Delete button

### Create Custom Role (Admin)
1. Go to Admin → Roles
2. Click **Create Role**
3. Enter name and description
4. Check permissions needed:
   - Projects: Create, Edit, Delete, View
   - Issues: Create, Edit, Delete, View, Assign
   - Sprints, Reports, etc.
5. Click **Create**
6. Assign role to users

### Configure Email Notifications (Admin)
1. Go to Admin → Settings
2. Configure SMTP:
   - Host, Port, Username, Password
3. Test with **Send Test Email**
4. Save settings
5. Users can now get email notifications

---

## Quick Reference

### Keyboard Shortcuts
- `Ctrl+K` - Quick search
- `G + P` - Go to projects
- `G + D` - Go to dashboard
- `G + R` - Go to reports

### Naming Conventions
- **Project Key:** UPPERCASE letters (e.g., PROJ, MOBILE, API)
- **Issue Key:** AUTO-GENERATED (e.g., PROJ-123)
- **Labels:** lowercase-with-dashes (e.g., urgent, database-fix)
- **Filters:** CamelCase (e.g., MyFilter, TeamFilter)

### Status Meanings
- **To Do** - Not yet started
- **In Progress** - Currently being worked on
- **In Review** - Waiting for approval/review
- **Done** - Completed and closed

### Priority Levels
- **Blocker** - Critical, must fix immediately
- **High** - Important, fix soon
- **Medium** - Normal, routine work
- **Low** - Nice-to-have, lowest priority

---

## Getting Help

### Internal Resources
- AGENTS.md - Developer standards
- DEVELOPER_PORTAL.md - Technical navigation
- API docs at `/api/docs`

### Reporting Issues
1. Document the problem clearly
2. Include steps to reproduce
3. Mention your user role
4. Contact system administrator

### Team Communication
- Use issue comments for project discussion
- Mention teammates with `@username`
- Watch issues to stay informed
- Use notifications for urgent matters

---

## Final Notes

This system is production-ready and fully functional. All core features are implemented and tested. Team members should refer to this documentation for:
- **Getting started** with the system
- **Understanding features** and capabilities
- **Performing common tasks** efficiently
- **Troubleshooting** issues
- **Finding help** when stuck

For technical questions, contact your administrator or development team.

**Happy tracking! 🚀**
