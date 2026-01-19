<?php
/**
 * Web Routes
 */

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProjectController;
use App\Controllers\IssueController;
use App\Controllers\BoardController;
use App\Controllers\SprintController;
use App\Controllers\UserController;
use App\Controllers\CommentController;
use App\Controllers\AttachmentController;
use App\Controllers\SearchController;
use App\Controllers\ReportController;
use App\Controllers\SettingsController;
use App\Controllers\AdminController;
use App\Controllers\NotificationController;
use App\Controllers\NotificationStreamController;
use App\Controllers\CalendarController;
use App\Controllers\RoadmapController;
use App\Controllers\PrintController;
use App\Controllers\ProjectDocumentationController;

$router = app()->getRouter();

// =====================================================
// PUBLIC ROUTES
// =====================================================

// Root path - redirect to login if not authenticated
$router->get('/', [AuthController::class, 'home'])->name('home');

// API Documentation (public)
$router->get('/api/docs', [DashboardController::class, 'apiDocs'])->name('api.docs');

$router->group(['middleware' => ['guest', 'csrf']], function ($router) {
    $router->get('/login', [AuthController::class, 'showLogin'])->name('login');
    $router->post('/login', [AuthController::class, 'login'])->name('login.submit');
    $router->get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    $router->post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    $router->get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    $router->post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// =====================================================
// AUTHENTICATED ROUTES
// =====================================================

$router->group(['middleware' => ['auth', 'csrf']], function ($router) {
    // Logout
    $router->post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    $router->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Projects
    $router->get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    $router->get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    $router->get('/projects/quick-create-list', [ProjectController::class, 'quickCreateList'])->name('projects.quick-create-list');

    // Users
    $router->get('/users/active', [UserController::class, 'activeUsers'])->name('users.active');

    // Issue Types (for quick create modal and forms)
    $router->get('/issue-types-list', [IssueController::class, 'getIssueTypes'])->name('issue-types.list');

    // NOTE: API lookup endpoints are now in routes/api.php with public access (no auth required)
    // Removed: /api/v1/issue-types, /api/v1/priorities, /api/v1/statuses, /api/v1/labels, /api/v1/link-types

    $router->post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    $router->get('/projects/{key}', [ProjectController::class, 'show'])->name('projects.show');
    $router->get('/projects/{key}/activity', [ProjectController::class, 'activity'])->name('projects.activity');
    $router->get('/projects/{key}/backlog', [ProjectController::class, 'backlog'])->name('projects.backlog');
    $router->get('/projects/{key}/sprints', [ProjectController::class, 'sprints'])->name('projects.sprints');
    $router->post('/projects/{key}/sprints', [ProjectController::class, 'storeSprint'])->name('projects.sprints.store');
    $router->get('/projects/{key}/board', [ProjectController::class, 'board'])->name('projects.board');
    $router->get('/projects/{key}/reports', [ProjectController::class, 'reports'])->name('projects.reports');
    $router->get('/projects/{key}/roadmap', [RoadmapController::class, 'show'])->name('projects.roadmap');
    $router->post('/projects/{key}/roadmap', [RoadmapController::class, 'store'])->name('projects.roadmap.store');
    $router->put('/projects/{key}/roadmap/{itemId}', [RoadmapController::class, 'update'])->name('projects.roadmap.update');
    $router->delete('/projects/{key}/roadmap/{itemId}', [RoadmapController::class, 'destroy'])->name('projects.roadmap.destroy');
    $router->get('/projects/{key}/settings', [ProjectController::class, 'settings'])->name('projects.settings');
    $router->put('/projects/{key}', [ProjectController::class, 'update'])->name('projects.update');
    $router->delete('/projects/{key}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Project Documentation
    $router->get('/projects/{key}/documentation', [ProjectDocumentationController::class, 'index'])->name('projects.documentation');
    $router->post('/projects/{key}/documentation/upload', [ProjectDocumentationController::class, 'upload'])->name('projects.documentation.upload');
    $router->put('/projects/{key}/documentation/{documentId}', [ProjectDocumentationController::class, 'update'])->name('projects.documentation.update');
    $router->delete('/projects/{key}/documentation/{documentId}', [ProjectDocumentationController::class, 'delete'])->name('projects.documentation.delete');
    $router->get('/projects/{key}/documentation/{documentId}', [ProjectDocumentationController::class, 'getDocument'])->name('projects.documentation.get');
    $router->get('/projects/{key}/documentation/{documentId}/download', [ProjectDocumentationController::class, 'download'])->name('projects.documentation.download');

    // Project Budget (moved from API routes for proper session auth)
    $router->get('/projects/{key}/budget', [\App\Controllers\Api\ProjectBudgetApiController::class, 'getBudget'])->name('projects.budget.get');
    $router->put('/projects/{key}/budget', [\App\Controllers\Api\ProjectBudgetApiController::class, 'updateBudget'])->name('projects.budget.update');

    $router->get('/projects/{key}/members', [ProjectController::class, 'members'])->name('projects.members');
    $router->post('/projects/{key}/members', [ProjectController::class, 'addMember'])->name('projects.members.add');
    $router->put('/projects/{key}/members/{userId}', [ProjectController::class, 'updateMember'])->name('projects.members.update');
    $router->delete('/projects/{key}/members/{userId}', [ProjectController::class, 'removeMember'])->name('projects.members.remove');
    $router->get('/projects/{key}/workflows', [ProjectController::class, 'workflows'])->name('projects.workflows');

    // Issues
    $router->get('/projects/{key}/issues', [IssueController::class, 'index'])->name('issues.index');
    $router->get('/issues/create', [IssueController::class, 'create'])->name('issues.create');
    $router->post('/issues/store', [IssueController::class, 'store'])->name('issues.store');
    $router->get('/issue/{issueKey}', [IssueController::class, 'show'])->name('issues.show');
    $router->get('/issue/{issueKey}/edit', [IssueController::class, 'edit'])->name('issues.edit');
    $router->put('/issue/{issueKey}', [IssueController::class, 'update'])->name('issues.update');
    $router->delete('/issue/{issueKey}', [IssueController::class, 'destroy'])->name('issues.destroy');
    $router->post('/issue/{issueKey}/transition', [IssueController::class, 'transition'])->name('issues.transition');
    $router->post('/issue/{issueKey}/assign', [IssueController::class, 'assign'])->name('issues.assign');
    $router->post('/issue/{issueKey}/watch', [IssueController::class, 'watch'])->name('issues.watch');
    $router->post('/issue/{issueKey}/vote', [IssueController::class, 'vote'])->name('issues.vote');
    $router->post('/issue/{issueKey}/link', [IssueController::class, 'link'])->name('issues.link');

    // Comments
    $router->post('/issue/{issueKey}/comments', [CommentController::class, 'store'])->name('comments.store');
    $router->put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    $router->delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Attachments
    $router->post('/issue/{issueKey}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    $router->get('/attachments/{id}', [AttachmentController::class, 'download'])->name('attachments.download');
    $router->delete('/attachments/{id}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Worklogs
    $router->post('/issue/{issueKey}/logwork', [IssueController::class, 'logWork'])->name('worklogs.store');
    $router->post('/issue/{issueKey}/worklogs', [IssueController::class, 'logWork'])->name('worklogs.store');
    $router->put('/worklogs/{id}', [IssueController::class, 'updateWorklog'])->name('worklogs.update');
    $router->delete('/worklogs/{id}', [IssueController::class, 'deleteWorklog'])->name('worklogs.destroy');

    // Boards
    $router->get('/projects/{key}/boards', [BoardController::class, 'index'])->name('boards.index');
    $router->get('/projects/{key}/boards/create', [BoardController::class, 'create'])->name('boards.create');
    $router->get('/boards/{id}', [BoardController::class, 'show'])->name('boards.show');
    $router->get('/boards/{id}/backlog', [BoardController::class, 'backlog'])->name('boards.backlog');
    $router->get('/boards/{id}/settings', [BoardController::class, 'settings'])->name('boards.settings');
    $router->post('/boards', [BoardController::class, 'store'])->name('boards.store');
    $router->put('/boards/{id}', [BoardController::class, 'update'])->name('boards.update');
    $router->delete('/boards/{id}', [BoardController::class, 'destroy'])->name('boards.destroy');
    $router->post('/boards/{id}/move', [BoardController::class, 'moveIssue'])->name('boards.move');

    // Sprints
    $router->get('/boards/{boardId}/sprints', [SprintController::class, 'index'])->name('sprints.index');
    $router->post('/boards/{boardId}/sprints', [SprintController::class, 'store'])->name('sprints.store');
    $router->put('/sprints/{id}', [SprintController::class, 'update'])->name('sprints.update');
    $router->post('/sprints/{id}/start', [SprintController::class, 'start'])->name('sprints.start');
    $router->post('/sprints/{id}/complete', [SprintController::class, 'complete'])->name('sprints.complete');
    $router->delete('/sprints/{id}', [SprintController::class, 'destroy'])->name('sprints.destroy');
    $router->post('/sprints/{id}/issues', [SprintController::class, 'addIssue'])->name('sprints.issues.add');
    $router->delete('/sprints/{id}/issues/{issueId}', [SprintController::class, 'removeIssue'])->name('sprints.issues.remove');

    // Sprint Board View (redirects to board with sprint_id parameter)
    $router->get('/projects/{key}/sprints/{id}/board', [SprintController::class, 'viewBoard'])->name('sprints.board');

    // Search
    $router->get('/search', [SearchController::class, 'index'])->name('search.index');
    $router->get('/search/quick', [SearchController::class, 'quick'])->name('search.quick');
    $router->get('/search/advanced', [SearchController::class, 'advanced'])->name('search.advanced');

    // Filters
    $router->get('/filters', [SearchController::class, 'filters'])->name('filters.index');
    $router->post('/filters', [SearchController::class, 'saveFilter'])->name('filters.store');
    $router->put('/filters/{id}', [SearchController::class, 'updateFilter'])->name('filters.update');
    $router->delete('/filters/{id}', [SearchController::class, 'deleteFilter'])->name('filters.destroy');

    // Reports
    $router->get('/reports', [ReportController::class, 'index'])->name('reports.index');
    $router->get('/reports/stats', [ReportController::class, 'stats'])->name('reports.stats');
    $router->get('/reports/sprint', [ReportController::class, 'sprint'])->name('reports.sprint');
    $router->get('/reports/burndown', [ReportController::class, 'burndown'])->name('reports.burndown');
    $router->get('/reports/burndown/{sprintId}', [ReportController::class, 'burndown'])->name('reports.burndown.show');
    $router->get('/reports/velocity/{boardId}', [ReportController::class, 'velocity'])->name('reports.velocity');
    $router->get('/reports/cumulative-flow', [ReportController::class, 'cumulativeFlowSelector'])->name('reports.cumulative-flow');
    $router->get('/reports/cumulative-flow/{boardId}', [ReportController::class, 'cumulativeFlow'])->name('reports.cumulative-flow.show');
    $router->get('/reports/workload', [ReportController::class, 'workload'])->name('reports.workload');
    $router->get('/reports/created-vs-resolved', [ReportController::class, 'createdVsResolved'])->name('reports.created-vs-resolved');
    $router->get('/reports/resolution-time', [ReportController::class, 'resolutionTime'])->name('reports.resolution-time');
    $router->get('/reports/priority-breakdown', [ReportController::class, 'priorityBreakdown'])->name('reports.priority-breakdown');
    $router->get('/reports/time-logged', [ReportController::class, 'timeLogged'])->name('reports.time-logged');
    $router->get('/reports/time-estimate-accuracy', [ReportController::class, 'estimateAccuracy'])->name('reports.estimate-accuracy');
    $router->get('/reports/version-progress', [ReportController::class, 'versionProgress'])->name('reports.version-progress');
    $router->get('/reports/release-burndown', [ReportController::class, 'releaseBurndown'])->name('reports.release-burndown');

    // Calendar
    $router->get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    $router->get('/projects/{key}/calendar', [CalendarController::class, 'show'])->name('calendar.project');

    // Roadmap
    $router->get('/roadmap', [RoadmapController::class, 'index'])->name('roadmap.index');
    $router->get('/projects/{key}/roadmap', [RoadmapController::class, 'show'])->name('roadmap.project');

    // Time Tracking Routes
    $router->get('/time-tracking', [\App\Controllers\TimeTrackingController::class, 'globalDashboard'])->name('time-tracking.global');
    $router->get('/time-tracking/dashboard', [\App\Controllers\TimeTrackingController::class, 'dashboard'])->name('time-tracking.dashboard');
    $router->get('/time-tracking/user/{userId}', [\App\Controllers\TimeTrackingController::class, 'userReport'])->name('time-tracking.user');
    $router->get('/time-tracking/project/{projectId}', [\App\Controllers\TimeTrackingController::class, 'projectReport'])->name('time-tracking.project');
    $router->get('/time-tracking/budgets', [\App\Controllers\TimeTrackingController::class, 'budgetDashboard'])->name('time-tracking.budgets');
    $router->get('/time-tracking/issue/{issueId}', [\App\Controllers\TimeTrackingController::class, 'issueLogs'])->name('time-tracking.issue');

    // API Endpoints for UI (JSON responses for AJAX)
    $router->get('/api/web/projects', [ProjectController::class, 'apiProjects'])->name('api.web.projects');

    // User Profile
    $router->get('/profile', [UserController::class, 'profile'])->name('profile');
    $router->put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    $router->post('/profile/avatar', [UserController::class, 'updateAvatar'])->name('profile.avatar');
    $router->put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');
    $router->get('/profile/tokens', [UserController::class, 'tokens'])->name('profile.tokens');
    $router->post('/profile/tokens', [UserController::class, 'createToken'])->name('profile.tokens.create');
    $router->delete('/profile/tokens/{id}', [UserController::class, 'revokeToken'])->name('profile.tokens.revoke');
    $router->get('/profile/notifications', [UserController::class, 'profileNotifications'])->name('profile.notifications');
    $router->put('/profile/notifications', [UserController::class, 'updateNotificationSettings'])->name('profile.notifications.update');
    $router->get('/profile/security', [UserController::class, 'security'])->name('profile.security');
    $router->get('/profile/settings', [UserController::class, 'settings'])->name('profile.settings');
    $router->put('/profile/settings', [UserController::class, 'updateSettings'])->name('profile.settings.update');

    // Notifications
    $router->get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Real-Time Notification Streaming (Server-Sent Events)
    $router->get('/notifications/stream', [NotificationStreamController::class, 'stream'])->name('notifications.stream');
    $router->get('/notifications/unread-count', [NotificationStreamController::class, 'getUnreadCount'])->name('notifications.unread');
    $router->get('/notifications/recent', [NotificationStreamController::class, 'getRecent'])->name('notifications.recent');
    $router->post('/notifications/read', [NotificationStreamController::class, 'markAsRead'])->name('notifications.read');
    $router->post('/notifications/read-all', [NotificationStreamController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Settings (Project Settings)
    $router->get('/projects/{key}/settings/components', [SettingsController::class, 'components'])->name('settings.components');
    $router->get('/projects/{key}/settings/versions', [SettingsController::class, 'versions'])->name('settings.versions');
    $router->get('/projects/{key}/settings/labels', [SettingsController::class, 'labels'])->name('settings.labels');
});

// =====================================================
// ADMIN ROUTES
// =====================================================

$router->group(['prefix' => '/admin', 'middleware' => ['auth', 'admin', 'csrf']], function ($router) {
    $router->get('/', [AdminController::class, 'index'])->name('admin.index');

    // User Management
    $router->get('/users', [AdminController::class, 'users'])->name('admin.users');
    $router->get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    $router->post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    $router->get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    $router->get('/users/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');
    $router->put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    $router->post('/users/{id}/deactivate', [AdminController::class, 'deactivateUser'])->name('admin.users.deactivate');
    $router->post('/users/{id}/activate', [AdminController::class, 'activateUser'])->name('admin.users.activate');
    $router->delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Role Management
    $router->get('/roles', [AdminController::class, 'roles'])->name('admin.roles');
    $router->get('/roles/create', [AdminController::class, 'createRole'])->name('admin.roles.create');
    $router->post('/roles', [AdminController::class, 'storeRole'])->name('admin.roles.store');
    $router->get('/roles/{id}', [AdminController::class, 'showRole'])->name('admin.roles.show');
    $router->get('/roles/{id}/edit', [AdminController::class, 'editRole'])->name('admin.roles.edit');
    $router->put('/roles/{id}', [AdminController::class, 'updateRole'])->name('admin.roles.update');
    $router->delete('/roles/{id}', [AdminController::class, 'deleteRole'])->name('admin.roles.delete');

    // Group Management
    $router->get('/groups', [AdminController::class, 'groups'])->name('admin.groups');
    $router->post('/groups', [AdminController::class, 'storeGroup'])->name('admin.groups.store');
    $router->put('/groups/{id}', [AdminController::class, 'updateGroup'])->name('admin.groups.update');
    $router->delete('/groups/{id}', [AdminController::class, 'deleteGroup'])->name('admin.groups.delete');

    // Workflow Management
    $router->get('/workflows', [AdminController::class, 'workflows'])->name('admin.workflows');
    $router->post('/workflows', [AdminController::class, 'storeWorkflow'])->name('admin.workflows.store');
    $router->get('/workflows/{id}', [AdminController::class, 'showWorkflow'])->name('admin.workflows.show');
    $router->put('/workflows/{id}', [AdminController::class, 'updateWorkflow'])->name('admin.workflows.update');
    $router->delete('/workflows/{id}', [AdminController::class, 'deleteWorkflow'])->name('admin.workflows.delete');

    // Workflow Statuses
    $router->post('/workflows/{id}/statuses', [AdminController::class, 'addStatusToWorkflow'])->name('admin.workflows.statuses.add');
    $router->delete('/workflows/{id}/statuses/{statusId}', [AdminController::class, 'removeStatusFromWorkflow'])->name('admin.workflows.statuses.remove');

    // Workflow Transitions
    $router->post('/workflows/{id}/transitions', [AdminController::class, 'addTransitionToWorkflow'])->name('admin.workflows.transitions.add');
    $router->delete('/workflows/{id}/transitions/{transitionId}', [AdminController::class, 'removeTransitionFromWorkflow'])->name('admin.workflows.transitions.remove');

    // Issue Types
    $router->get('/issue-types', [AdminController::class, 'issueTypes'])->name('admin.issue-types');
    $router->post('/issue-types', [AdminController::class, 'storeIssueType'])->name('admin.issue-types.store');
    $router->put('/issue-types/{id}', [AdminController::class, 'updateIssueType'])->name('admin.issue-types.update');
    $router->delete('/issue-types/{id}', [AdminController::class, 'deleteIssueType'])->name('admin.issue-types.delete');

    // Audit Log
    $router->get('/audit-log', [AdminController::class, 'auditLog'])->name('admin.audit-log');

    // System Settings
    $router->get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    $router->put('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    $router->post('/settings/test-email', [AdminController::class, 'testEmail'])->name('admin.settings.test-email');

    // Global Permissions
    $router->get('/global-permissions', [AdminController::class, 'globalPermissions'])->name('admin.global-permissions');
    $router->put('/global-permissions', [AdminController::class, 'updateGlobalPermissions'])->name('admin.global-permissions.update');

    // Alias for convenience
    $router->get('/permissions', [AdminController::class, 'globalPermissions'])->name('admin.permissions');
    $router->put('/permissions', [AdminController::class, 'updateGlobalPermissions'])->name('admin.permissions.update');

    // Projects Management
    $router->get('/projects', [AdminController::class, 'projects'])->name('admin.projects');

    // Project Categories
    $router->get('/project-categories', [AdminController::class, 'projectCategories'])->name('admin.project-categories');
    $router->post('/project-categories', [AdminController::class, 'storeProjectCategory'])->name('admin.project-categories.store');
    $router->put('/project-categories/{id}', [AdminController::class, 'updateProjectCategory'])->name('admin.project-categories.update');
    $router->delete('/project-categories/{id}', [AdminController::class, 'deleteProjectCategory'])->name('admin.project-categories.delete');
});

// =====================================================
// PRINT & EXPORT ROUTES
// =====================================================

$router->group(['middleware' => ['auth']], function ($router) {
    // Print Routes
    $router->get('/projects/{key}/print/board', [PrintController::class, 'printBoard'])->name('print.board');
    $router->get('/projects/{key}/print/project', [PrintController::class, 'printProject'])->name('print.project');
    $router->get('/sprints/{sprint_id}/print', [PrintController::class, 'printSprint'])->name('print.sprint');

    // Export to PDF (with KoolReport)
    $router->get('/projects/{key}/export/board-pdf', [PrintController::class, 'exportBoardPDF'])->name('export.board-pdf');
    $router->get('/projects/{key}/export/project-pdf', [PrintController::class, 'exportProjectPDF'])->name('export.project-pdf');
    $router->get('/sprints/{sprint_id}/export/pdf', [PrintController::class, 'exportSprintPDF'])->name('export.sprint-pdf');
});
