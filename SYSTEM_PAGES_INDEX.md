# 🗺️ Jira Clone - Complete Pages & Routes Index

**Complete map of all pages, routes, and features in the system**

---

## 🎯 Quick Access by Category

- [Authentication & User Pages](#-authentication--user-pages)
- [Projects & Issues](#-projects--issues)
- [Boards & Sprints](#-boards--sprints)
- [Reports](#-reports)
- [Admin Pages](#-admin-pages)
- [API Endpoints](#-api-endpoints)

---

## 🔐 Authentication & User Pages

### Login
- **Route**: `/login`
- **File**: `views/auth/login.php`
- **Controller**: `AuthController::login()`, `AuthController::authenticate()`
- **Action**: User login with email & password
- **Status**: ✅ Core Feature

### Register
- **Route**: `/register`
- **File**: `views/auth/register.php`
- **Controller**: `AuthController::register()`, `AuthController::store()`
- **Action**: New user registration
- **Status**: ✅ Core Feature

### Forgot Password
- **Route**: `/forgot-password`
- **File**: `views/auth/forgot-password.php`
- **Controller**: `AuthController::forgotPassword()`, `AuthController::sendReset()`
- **Action**: Password reset request
- **Status**: ✅ Core Feature

### Reset Password
- **Route**: `/reset-password/{token}`
- **File**: `views/auth/reset-password.php`
- **Controller**: `AuthController::resetPassword()`, `AuthController::updatePassword()`
- **Action**: Set new password
- **Status**: ✅ Core Feature

### Profile
- **Route**: `/profile`
- **File**: `views/profile/index.php`, `views/profile/edit.php`
- **Controller**: `ProfileController::index()`, `ProfileController::update()`
- **Action**: View and edit user profile
- **Status**: ✅ Core Feature

---

## 📁 Projects & Issues

### Dashboard
- **Route**: `/`
- **File**: `views/dashboard/index.php`
- **Controller**: `DashboardController::index()`
- **Action**: Main dashboard with quick stats
- **Features**: Recent issues, project overview, assigned tasks
- **Status**: ✅ Core Feature

### Projects List
- **Route**: `/projects`
- **File**: `views/projects/index.php`
- **Controller**: `ProjectController::index()`
- **Action**: View all projects
- **Features**: Search, filter, pagination
- **Status**: ✅ Core Feature

### Create Project
- **Route**: `/projects/create`
- **File**: `views/projects/create.php`
- **Controller**: `ProjectController::create()`, `ProjectController::store()`
- **Action**: Create new project
- **Status**: ✅ Core Feature

### Project Detail
- **Route**: `/projects/{key}`
- **File**: `views/projects/show.php`
- **Controller**: `ProjectController::show()`
- **Action**: View project details
- **Features**: Issues, members, settings
- **Status**: ✅ Core Feature

### Edit Project
- **Route**: `/projects/{key}/edit`
- **File**: `views/projects/edit.php`
- **Controller**: `ProjectController::edit()`, `ProjectController::update()`
- **Action**: Modify project settings
- **Status**: ✅ Core Feature

### Project Settings
- **Route**: `/projects/{key}/settings`
- **File**: `views/projects/settings.php`
- **Controller**: `ProjectController::settings()`, `ProjectController::updateSettings()`
- **Action**: Manage project configuration
- **Features**: Name, description, members, visibility
- **Status**: ✅ Core Feature

### Issues List
- **Route**: `/issues` or `/projects/{key}/issues`
- **File**: `views/issues/index.php`
- **Controller**: `IssueController::index()`
- **Action**: List issues (all or filtered)
- **Features**: Search, filter, sort, pagination
- **Status**: ✅ Core Feature

### Create Issue
- **Route**: `/issues/create`
- **File**: `views/issues/create.php`
- **Controller**: `IssueController::create()`, `IssueController::store()`
- **Action**: Create new issue
- **Features**: Select project, type, assignee, priority
- **Status**: ✅ Core Feature (with Quick Create Modal)

### Issue Detail
- **Route**: `/issues/{key}`
- **File**: `views/issues/show.php`
- **Controller**: `IssueController::show()`
- **Action**: View issue details
- **Features**: 
  - Description, attachments, comments
  - Watchers, linked issues
  - History, transitions
  - Comment edit/delete
- **Status**: ✅ Core Feature

### Edit Issue
- **Route**: `/issues/{key}/edit`
- **File**: `views/issues/edit.php`
- **Controller**: `IssueController::edit()`, `IssueController::update()`
- **Action**: Modify issue
- **Status**: ✅ Core Feature

### Issue Transitions
- **Route**: `/issues/{key}/transition` (POST)
- **File**: API endpoint (AJAX)
- **Controller**: `IssueController::transition()`
- **Action**: Change issue status
- **Status**: ✅ Core Feature

### Issue Comments
- **Route**: `/api/v1/issues/{key}/comments` (POST, PUT, DELETE)
- **File**: API endpoints
- **Controller**: `IssueController::addComment()`, `updateComment()`, `deleteComment()`
- **Action**: Add, edit, delete comments
- **Features**: Threaded, timestamps, edit history
- **Status**: ✅ Complete (includes edit/delete)

### Issue Attachments
- **Route**: `/api/v1/issues/{key}/attachments` (POST, DELETE)
- **File**: API endpoints
- **Controller**: `IssueController::addAttachment()`, `deleteAttachment()`
- **Action**: Upload/delete files
- **Status**: ✅ Core Feature

---

## 📊 Boards & Sprints

### Scrum Board
- **Route**: `/boards/{id}` or `/projects/{key}/board/scrum`
- **File**: `views/boards/scrum.php`
- **Controller**: `BoardController::scrum()`
- **Action**: Scrum board with drag-and-drop
- **Features**: Swimlanes by status/assignee, backlog, sprint view
- **Status**: ✅ Core Feature

### Kanban Board
- **Route**: `/projects/{key}/board/kanban`
- **File**: `views/boards/kanban.php`
- **Controller**: `BoardController::kanban()`
- **Action**: Kanban board with drag-and-drop
- **Features**: Columns by status, WIP limits
- **Status**: ✅ Core Feature

### Sprints List
- **Route**: `/projects/{key}/sprints`
- **File**: `views/sprints/index.php`
- **Controller**: `SprintController::index()`
- **Action**: View all sprints
- **Features**: Active, upcoming, completed sprints
- **Status**: ✅ Core Feature

### Create Sprint
- **Route**: `/projects/{key}/sprints/create`
- **File**: `views/sprints/create.php`
- **Controller**: `SprintController::create()`, `SprintController::store()`
- **Action**: Create new sprint
- **Status**: ✅ Core Feature

### Sprint Detail
- **Route**: `/projects/{key}/sprints/{id}`
- **File**: `views/sprints/show.php`
- **Controller**: `SprintController::show()`
- **Action**: View sprint details
- **Features**: Issues, velocity, burndown
- **Status**: ✅ Core Feature

### Sprint Planning
- **Route**: `/projects/{key}/sprints/{id}/plan`
- **File**: `views/sprints/plan.php`
- **Controller**: `SprintController::plan()`, `SprintController::updatePlan()`
- **Action**: Plan sprint issues
- **Features**: Drag backlog items into sprint
- **Status**: ✅ Core Feature

### Backlog
- **Route**: `/projects/{key}/backlog`
- **File**: `views/backlog/index.php`
- **Controller**: `BacklogController::index()`
- **Action**: Manage backlog
- **Features**: Drag to arrange priority, drag to sprint
- **Status**: ✅ Core Feature

---

## 📈 Reports

### Reports Dashboard
- **Route**: `/reports`
- **File**: `views/reports/index.php`
- **Controller**: `ReportController::index()`
- **Action**: Reports overview & navigation
- **Status**: ✅ Available

### 1. Created vs Resolved
- **Route**: `/reports/created-vs-resolved`
- **File**: `views/reports/created-vs-resolved.php`
- **Controller**: `ReportController::createdVsResolved()`
- **Visualization**: Line chart
- **Filters**: Project, date range
- **Status**: ✅ Complete

### 2. Resolution Time
- **Route**: `/reports/resolution-time`
- **File**: `views/reports/resolution-time.php`
- **Controller**: `ReportController::resolutionTime()`
- **Visualization**: Statistics table
- **Filters**: Project, date range
- **Status**: ✅ Complete

### 3. Priority Breakdown
- **Route**: `/reports/priority-breakdown`
- **File**: `views/reports/priority-breakdown.php`
- **Controller**: `ReportController::priorityBreakdown()`
- **Visualization**: Pie chart
- **Filters**: Project, date range
- **Status**: ✅ Complete

### 4. Time Logged
- **Route**: `/reports/time-logged`
- **File**: `views/reports/time-logged.php`
- **Controller**: `ReportController::timeLogged()`
- **Visualization**: Statistics table
- **Filters**: Project, date range
- **Status**: ✅ Complete

### 5. Estimate Accuracy
- **Route**: `/reports/estimate-accuracy`
- **File**: `views/reports/estimate-accuracy.php`
- **Controller**: `ReportController::estimateAccuracy()`
- **Visualization**: Statistics
- **Filters**: Project, date range
- **Status**: ✅ Complete

### 6. Version Progress
- **Route**: `/reports/version-progress`
- **File**: `views/reports/version-progress.php`
- **Controller**: `ReportController::versionProgress()`
- **Visualization**: Statistics table
- **Filters**: Version
- **Status**: ✅ Complete

### 7. Release Burndown
- **Route**: `/reports/release-burndown`
- **File**: `views/reports/release-burndown.php`
- **Controller**: `ReportController::releaseBurndown()`
- **Visualization**: Line chart
- **Filters**: Release version
- **Status**: ✅ Complete

---

## 🔒 Admin Pages

### Admin Dashboard
- **Route**: `/admin`
- **File**: `views/admin/index.php`
- **Controller**: `AdminController::index()`
- **Action**: Admin overview & statistics
- **Features**: User count, project count, issue count, storage used
- **Status**: ✅ Core Feature

### User Management

#### Users List
- **Route**: `/admin/users`
- **File**: `views/admin/users/index.php`
- **Controller**: `AdminController::users()`
- **Action**: List all users
- **Features**: Search, filter, pagination
- **Status**: ✅ Core Feature

#### Create User
- **Route**: `/admin/users/create`
- **File**: `views/admin/user-form.php`
- **Controller**: `AdminController::createUser()`, `AdminController::storeUser()`
- **Action**: Create new user
- **Status**: ✅ Core Feature

#### Edit User
- **Route**: `/admin/users/{id}/edit`
- **File**: `views/admin/user-form.php`
- **Controller**: `AdminController::editUser()`, `AdminController::updateUser()`
- **Action**: Modify user
- **Protection**: Admin users cannot be edited (protection enabled)
- **Status**: ✅ Core Feature (with Protection)

#### Delete User
- **Route**: `/admin/users/{id}/delete` (POST)
- **File**: API
- **Controller**: `AdminController::deleteUser()`
- **Action**: Remove user
- **Protection**: Admin users cannot be deleted
- **Status**: ✅ Core Feature (with Protection)

### Role Management

#### Roles List
- **Route**: `/admin/roles`
- **File**: `views/admin/roles/index.php`
- **Controller**: `AdminController::roles()`
- **Action**: List all roles
- **Features**: System roles marked as protected, custom roles editable
- **Status**: ✅ Core Feature

#### Create Role
- **Route**: `/admin/roles/create`
- **File**: `views/admin/roles/form.php`
- **Controller**: `AdminController::createRole()`, `AdminController::storeRole()`
- **Action**: Create custom role
- **Status**: ✅ Core Feature

#### Edit Role
- **Route**: `/admin/roles/{id}/edit`
- **File**: `views/admin/roles/form.php`
- **Controller**: `AdminController::editRole()`, `AdminController::updateRole()`
- **Action**: Modify role
- **Protection**: System roles cannot be edited (protection enabled)
- **Status**: ✅ Core Feature (with Protection)

#### Delete Role
- **Route**: `/admin/roles/{id}/delete` (POST)
- **File**: API
- **Controller**: `AdminController::deleteRole()`
- **Action**: Remove role
- **Protection**: System roles cannot be deleted
- **Status**: ✅ Core Feature (with Protection)

### Projects Management

#### Projects List
- **Route**: `/admin/projects`
- **File**: `views/admin/projects.php`
- **Controller**: `AdminController::projects()`
- **Action**: View all projects
- **Features**: Search, filter, pagination, member count, issue count
- **Status**: ✅ Core Feature

### Project Categories

#### Categories List
- **Route**: `/admin/project-categories`
- **File**: `views/admin/project-categories.php`
- **Controller**: `AdminController::projectCategories()`
- **Action**: List project categories
- **Status**: ✅ Core Feature

#### Create Category
- **Route**: `/admin/project-categories` (POST)
- **File**: Modal dialog
- **Controller**: `AdminController::storeProjectCategory()`
- **Action**: Add category
- **Status**: ✅ Core Feature

#### Edit Category
- **Route**: `/admin/project-categories/{id}` (PUT)
- **File**: Modal dialog
- **Controller**: `AdminController::updateProjectCategory()`
- **Action**: Modify category
- **Status**: ✅ Core Feature

#### Delete Category
- **Route**: `/admin/project-categories/{id}` (DELETE)
- **File**: API
- **Controller**: `AdminController::deleteProjectCategory()`
- **Action**: Remove category (prevents if projects exist)
- **Status**: ✅ Core Feature

### Issue Types Management

#### Issue Types List
- **Route**: `/admin/issue-types`
- **File**: `views/admin/issue-types.php`
- **Controller**: `AdminController::issueTypes()`
- **Action**: List issue types
- **Status**: ✅ Core Feature

#### Create Issue Type
- **Route**: `/admin/issue-types` (POST)
- **File**: Modal dialog
- **Controller**: `AdminController::storeIssueType()`
- **Action**: Create custom issue type with icon & color
- **Features**: Color picker, Bootstrap icon picker
- **Status**: ✅ Core Feature

#### Edit Issue Type
- **Route**: `/admin/issue-types/{id}` (PUT)
- **File**: Modal dialog
- **Controller**: `AdminController::updateIssueType()`
- **Action**: Modify issue type
- **Status**: ✅ Core Feature

#### Delete Issue Type
- **Route**: `/admin/issue-types/{id}` (DELETE)
- **File**: API
- **Controller**: `AdminController::deleteIssueType()`
- **Action**: Remove issue type (prevents if issues exist)
- **Status**: ✅ Core Feature

### Permissions

#### Global Permissions
- **Route**: `/admin/global-permissions`
- **File**: `views/admin/global-permissions.php`
- **Controller**: `AdminController::globalPermissions()`, `AdminController::updateGlobalPermissions()`
- **Action**: View and edit system-wide permissions
- **Features**: Grouped by category
- **Status**: ✅ Core Feature

### Workflows

#### Workflows List
- **Route**: `/admin/workflows`
- **File**: `views/admin/workflows/index.php`
- **Controller**: `AdminController::workflows()`
- **Action**: View all workflows
- **Status**: ✅ Core Feature

#### Edit Workflow
- **Route**: `/admin/workflows/{id}/edit`
- **File**: `views/admin/workflows/edit.php`
- **Controller**: `AdminController::editWorkflow()`, `AdminController::updateWorkflow()`
- **Action**: Modify status and transitions
- **Status**: ✅ Core Feature

---

## 🌐 API Endpoints

### Authentication

#### Login
- **Method**: `POST`
- **Route**: `/api/v1/auth/login`
- **Input**: `email`, `password`
- **Output**: `token`, `expires_in`
- **Status**: ✅ Complete

#### Logout
- **Method**: `POST`
- **Route**: `/api/v1/auth/logout`
- **Auth**: Required (Bearer token)
- **Status**: ✅ Complete

#### Refresh Token
- **Method**: `POST`
- **Route**: `/api/v1/auth/refresh`
- **Auth**: Required (Bearer token)
- **Status**: ✅ Complete

### Projects

#### List Projects
- **Method**: `GET`
- **Route**: `/api/v1/projects`
- **Auth**: Required
- **Query Params**: `page`, `per_page`, `search`
- **Status**: ✅ Complete

#### Create Project
- **Method**: `POST`
- **Route**: `/api/v1/projects`
- **Auth**: Required
- **Input**: `name`, `key`, `description`, `type`
- **Status**: ✅ Complete

#### Get Project
- **Method**: `GET`
- **Route**: `/api/v1/projects/{key}`
- **Auth**: Required
- **Status**: ✅ Complete

#### Update Project
- **Method**: `PUT`
- **Route**: `/api/v1/projects/{key}`
- **Auth**: Required
- **Status**: ✅ Complete

#### Delete Project
- **Method**: `DELETE`
- **Route**: `/api/v1/projects/{key}`
- **Auth**: Required
- **Status**: ✅ Complete

### Issues

#### List Issues
- **Method**: `GET`
- **Route**: `/api/v1/issues`
- **Auth**: Required
- **Query Params**: `project`, `page`, `per_page`, `search`, `status`
- **Status**: ✅ Complete

#### Create Issue
- **Method**: `POST`
- **Route**: `/api/v1/issues`
- **Auth**: Required
- **Input**: `project_id`, `title`, `description`, `type`, `assignee`
- **Status**: ✅ Complete

#### Get Issue
- **Method**: `GET`
- **Route**: `/api/v1/issues/{key}`
- **Auth**: Required
- **Status**: ✅ Complete

#### Update Issue
- **Method**: `PUT`
- **Route**: `/api/v1/issues/{key}`
- **Auth**: Required
- **Status**: ✅ Complete

#### Delete Issue
- **Method**: `DELETE`
- **Route**: `/api/v1/issues/{key}`
- **Auth**: Required
- **Status**: ✅ Complete

#### Transition Issue
- **Method**: `POST`
- **Route**: `/api/v1/issues/{key}/transitions`
- **Auth**: Required
- **Input**: `status`
- **Status**: ✅ Complete

### Comments

#### Add Comment
- **Method**: `POST`
- **Route**: `/api/v1/issues/{key}/comments`
- **Auth**: Required
- **Input**: `body`
- **Status**: ✅ Complete

#### Update Comment
- **Method**: `PUT`
- **Route**: `/api/v1/issues/{key}/comments/{comment_id}`
- **Auth**: Required
- **Input**: `body`
- **Status**: ✅ Complete (with feature)

#### Delete Comment
- **Method**: `DELETE`
- **Route**: `/api/v1/issues/{key}/comments/{comment_id}`
- **Auth**: Required
- **Status**: ✅ Complete (with feature)

### Attachments

#### Upload Attachment
- **Method**: `POST`
- **Route**: `/api/v1/issues/{key}/attachments`
- **Auth**: Required
- **Input**: `file` (multipart)
- **Status**: ✅ Complete

#### Delete Attachment
- **Method**: `DELETE`
- **Route**: `/api/v1/issues/{key}/attachments/{attachment_id}`
- **Auth**: Required
- **Status**: ✅ Complete

### Boards

#### List Boards
- **Method**: `GET`
- **Route**: `/api/v1/boards`
- **Auth**: Required
- **Status**: ✅ Complete

#### Get Board
- **Method**: `GET`
- **Route**: `/api/v1/boards/{id}`
- **Auth**: Required
- **Status**: ✅ Complete

### Sprints

#### Get Sprint
- **Method**: `GET`
- **Route**: `/api/v1/sprints/{id}`
- **Auth**: Required
- **Status**: ✅ Complete

#### List Sprint Issues
- **Method**: `GET`
- **Route**: `/api/v1/sprints/{id}/issues`
- **Auth**: Required
- **Status**: ✅ Complete

---

## 🎨 Quick Create Modal

### Feature Details
- **Trigger**: "Create" button in navbar (top-right)
- **Implementation**: `views/layouts/app.php` (lines 190-230)
- **Endpoint**: `/projects/quick-create-list` (get projects for dropdown)
- **Endpoint**: `/api/v1/issues` (create issue)
- **JavaScript**: Select2 for dropdowns
- **Mobile Responsive**: Yes (all breakpoints)
- **Status**: ✅ Complete

---

## 📋 Summary

### Total Pages
- **Web Pages**: 30+ core pages
- **Admin Pages**: 15+ admin management pages
- **API Endpoints**: 30+ REST API endpoints
- **Reports**: 7 enterprise-grade reports
- **Status**: ✅ All Major Features Implemented

### Navigation
- **Primary Hub**: [DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md)
- **Quick Reference**: [QUICK_NAVIGATION.md](QUICK_NAVIGATION.md)
- **Code Standards**: [AGENTS.md](AGENTS.md)
- **Interactive Dashboard**: [developer-dashboard.html](public/developer-dashboard.html)

---

**Last Updated**: December 2025  
**Version**: 1.0.0

> Find any broken links? Update this file and [DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md)
