<?php
/**
 * API Routes (v1)
 */

declare(strict_types=1);

use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\ProjectApiController;
use App\Controllers\Api\ProjectBudgetApiController;
use App\Controllers\Api\IssueApiController;
use App\Controllers\Api\BoardApiController;
use App\Controllers\Api\SprintApiController;
use App\Controllers\Api\UserApiController;
use App\Controllers\Api\SearchApiController;
use App\Controllers\Api\HealthApiController;
use App\Controllers\Api\TimeTrackingApiController;
use App\Controllers\NotificationController;
use App\Controllers\CalendarController;
use App\Controllers\RoadmapController;

$router = app()->getRouter();

// =====================================================
// API V1 ROUTES
// =====================================================

$router->group(['prefix' => '/api/v1'], function ($router) {
    
    // =====================================================
    // PUBLIC API ROUTES
    // =====================================================
    
    $router->group(['middleware' => 'throttle:60,1'], function ($router) {
        // Authentication
        $router->post('/auth/login', [AuthApiController::class, 'login']);
        $router->post('/auth/refresh', [AuthApiController::class, 'refresh']);
    });
    
    // =====================================================
    // AUTHENTICATED API ROUTES
    // =====================================================
    
    $router->group(['middleware' => ['api', 'throttle:300,1']], function ($router) {
        
        // Current User
        $router->get('/me', [UserApiController::class, 'me']);
        $router->put('/me', [UserApiController::class, 'updateMe']);
        $router->post('/auth/logout', [AuthApiController::class, 'logout']);
        
        // Projects
        $router->get('/projects', [ProjectApiController::class, 'index']);
        $router->post('/projects', [ProjectApiController::class, 'store']);
        $router->get('/projects/{key}', [ProjectApiController::class, 'show']);
        $router->put('/projects/{key}', [ProjectApiController::class, 'update']);
        $router->delete('/projects/{key}', [ProjectApiController::class, 'destroy']);
        
        // Project Members
        $router->get('/projects/{key}/members', [ProjectApiController::class, 'members']);
        $router->post('/projects/{key}/members', [ProjectApiController::class, 'addMember']);
        $router->delete('/projects/{key}/members/{userId}', [ProjectApiController::class, 'removeMember']);
        
        // Project Components
        $router->get('/projects/{key}/components', [ProjectApiController::class, 'components']);
        $router->post('/projects/{key}/components', [ProjectApiController::class, 'storeComponent']);
        $router->put('/components/{id}', [ProjectApiController::class, 'updateComponent']);
        $router->delete('/components/{id}', [ProjectApiController::class, 'destroyComponent']);
        
        // Project Versions
        $router->get('/projects/{key}/versions', [ProjectApiController::class, 'versions']);
        $router->post('/projects/{key}/versions', [ProjectApiController::class, 'storeVersion']);
        $router->put('/versions/{id}', [ProjectApiController::class, 'updateVersion']);
        $router->delete('/versions/{id}', [ProjectApiController::class, 'destroyVersion']);
        
        // Project Budget
        $router->get('/projects/{projectId}/budget', [ProjectBudgetApiController::class, 'getBudget']);
        $router->put('/projects/{projectId}/budget', [ProjectBudgetApiController::class, 'updateBudget']);
        
        // Issues
        $router->get('/issues', [IssueApiController::class, 'index']);
        $router->post('/issues', [IssueApiController::class, 'store']);
        $router->get('/issues/{key}', [IssueApiController::class, 'show']);
        $router->put('/issues/{key}', [IssueApiController::class, 'update']);
        $router->delete('/issues/{key}', [IssueApiController::class, 'destroy']);
        
        // Issue Actions
        $router->post('/issues/{key}/transitions', [IssueApiController::class, 'transition']);
        $router->get('/issues/{key}/transitions', [IssueApiController::class, 'availableTransitions']);
        $router->put('/issues/{key}/assignee', [IssueApiController::class, 'assign']);
        $router->post('/issues/{key}/watchers', [IssueApiController::class, 'watch']);
        $router->delete('/issues/{key}/watchers', [IssueApiController::class, 'unwatch']);
        $router->post('/issues/{key}/votes', [IssueApiController::class, 'vote']);
        $router->delete('/issues/{key}/votes', [IssueApiController::class, 'unvote']);
        
        // Issue Comments
        $router->get('/issues/{key}/comments', [IssueApiController::class, 'comments']);
        $router->post('/issues/{key}/comments', [IssueApiController::class, 'storeComment']);
        $router->put('/comments/{id}', [IssueApiController::class, 'updateComment']);
        $router->delete('/comments/{id}', [IssueApiController::class, 'destroyComment']);
        
        // Issue Attachments
        $router->get('/issues/{key}/attachments', [IssueApiController::class, 'attachments']);
        $router->post('/issues/{key}/attachments', [IssueApiController::class, 'storeAttachment']);
        $router->delete('/attachments/{id}', [IssueApiController::class, 'destroyAttachment']);
        
        // Issue Worklogs
        $router->get('/issues/{key}/worklogs', [IssueApiController::class, 'worklogs']);
        $router->post('/issues/{key}/worklogs', [IssueApiController::class, 'storeWorklog']);
        $router->put('/worklogs/{id}', [IssueApiController::class, 'updateWorklog']);
        $router->delete('/worklogs/{id}', [IssueApiController::class, 'destroyWorklog']);
        
        // Issue Links
        $router->get('/issues/{key}/links', [IssueApiController::class, 'links']);
        $router->post('/issues/{key}/links', [IssueApiController::class, 'storeLink']);
        $router->delete('/issue-links/{id}', [IssueApiController::class, 'destroyLink']);
        
        // Issue History
        $router->get('/issues/{key}/history', [IssueApiController::class, 'history']);
        
        // Boards
        $router->get('/boards', [BoardApiController::class, 'index']);
        $router->post('/boards', [BoardApiController::class, 'store']);
        $router->get('/boards/{id}', [BoardApiController::class, 'show']);
        $router->put('/boards/{id}', [BoardApiController::class, 'update']);
        $router->delete('/boards/{id}', [BoardApiController::class, 'destroy']);
        $router->get('/boards/{id}/issues', [BoardApiController::class, 'issues']);
        $router->get('/boards/{id}/backlog', [BoardApiController::class, 'backlog']);
        $router->post('/boards/{id}/issues/rank', [BoardApiController::class, 'rankIssues']);
        
        // Sprints
        $router->get('/boards/{boardId}/sprints', [SprintApiController::class, 'index']);
        $router->post('/boards/{boardId}/sprints', [SprintApiController::class, 'store']);
        $router->get('/sprints/{id}', [SprintApiController::class, 'show']);
        $router->put('/sprints/{id}', [SprintApiController::class, 'update']);
        $router->delete('/sprints/{id}', [SprintApiController::class, 'destroy']);
        $router->post('/sprints/{id}/start', [SprintApiController::class, 'start']);
        $router->post('/sprints/{id}/complete', [SprintApiController::class, 'complete']);
        $router->get('/sprints/{id}/issues', [SprintApiController::class, 'issues']);
        $router->post('/sprints/{id}/issues', [SprintApiController::class, 'addIssue']);
        $router->delete('/sprints/{id}/issues/{issueId}', [SprintApiController::class, 'removeIssue']);
        
        // Users
        $router->get('/users/active', [UserApiController::class, 'active']);
        $router->get('/users', [UserApiController::class, 'index']);
        $router->get('/users/{id}', [UserApiController::class, 'show']);
        $router->get('/users/search', [UserApiController::class, 'search']);
        
        // Search
        $router->get('/search', [SearchApiController::class, 'search']);
        $router->post('/jql', [SearchApiController::class, 'jql']);
        
        // Filters
        $router->get('/filters', [SearchApiController::class, 'filters']);
        $router->post('/filters', [SearchApiController::class, 'storeFilter']);
        $router->get('/filters/{id}', [SearchApiController::class, 'showFilter']);
        $router->put('/filters/{id}', [SearchApiController::class, 'updateFilter']);
        $router->delete('/filters/{id}', [SearchApiController::class, 'destroyFilter']);
        
        // Lookups (for dropdowns)
        $router->get('/issue-types', [IssueApiController::class, 'issueTypes']);
        $router->get('/priorities', [IssueApiController::class, 'priorities']);
        $router->get('/statuses', [IssueApiController::class, 'statuses']);
        $router->get('/labels', [IssueApiController::class, 'labels']);
        $router->get('/link-types', [IssueApiController::class, 'linkTypes']);
        
        // Notifications
         $router->get('/notifications/preferences', [NotificationController::class, 'getPreferences']);
         $router->post('/notifications/preferences', [NotificationController::class, 'updatePreferences']);
         $router->put('/notifications/preferences', [NotificationController::class, 'updatePreferences']);
         $router->get('/notifications/stats', [NotificationController::class, 'getStats']);
         $router->patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
         $router->patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
         $router->delete('/notifications/{id}', [NotificationController::class, 'delete']);
         $router->get('/notifications', [NotificationController::class, 'apiIndex']);
         
         // Email Delivery (Phase 2)
         $router->post('/notifications/test-email', [NotificationController::class, 'testEmail']);
         $router->get('/notifications/email-status', [NotificationController::class, 'emailStatus']);
         $router->post('/notifications/send-emails', [NotificationController::class, 'sendEmails']);
         
         // Calendar API
         $router->get('/calendar/events', [CalendarController::class, 'getEvents']);
         $router->get('/calendar/upcoming', [CalendarController::class, 'upcoming']);
         $router->get('/calendar/overdue', [CalendarController::class, 'overdue']);
         $router->get('/calendar/projects', [CalendarController::class, 'projects']);
         
         // Roadmap API
         $router->get('/roadmap/project', [RoadmapController::class, 'project']);
         $router->get('/roadmap/epics', [RoadmapController::class, 'epics']);
         $router->get('/roadmap/versions', [RoadmapController::class, 'versions']);
         $router->get('/roadmap/epic-issues', [RoadmapController::class, 'epicIssues']);
         $router->get('/roadmap/version-issues', [RoadmapController::class, 'versionIssues']);
         $router->get('/roadmap/timeline-range', [RoadmapController::class, 'timelineRange']);
         $router->get('/roadmap/projects', [RoadmapController::class, 'projects']);
         
         // Time Tracking API v1
         $router->post('/time-tracking/start', [TimeTrackingApiController::class, 'start']);
         $router->post('/time-tracking/pause', [TimeTrackingApiController::class, 'pause']);
         $router->post('/time-tracking/resume', [TimeTrackingApiController::class, 'resume']);
         $router->post('/time-tracking/stop', [TimeTrackingApiController::class, 'stop']);
         $router->get('/time-tracking/status', [TimeTrackingApiController::class, 'status']);
         $router->get('/time-tracking/logs', [TimeTrackingApiController::class, 'logs']);
         $router->get('/time-tracking/issue/{issueId}', [TimeTrackingApiController::class, 'issueTimeLogs']);
         $router->post('/time-tracking/rate', [TimeTrackingApiController::class, 'setRate']);
         $router->get('/time-tracking/rate', [TimeTrackingApiController::class, 'getRate']);
         $router->get('/time-tracking/project/{projectId}/budget', [TimeTrackingApiController::class, 'projectBudget']);
         $router->get('/time-tracking/project/{projectId}/statistics', [TimeTrackingApiController::class, 'projectStatistics']);
         
         // Webhooks info
         $router->get('/myself', [UserApiController::class, 'me']);
    });
});

// API Health Check (no auth required)
$router->get('/api/health', [HealthApiController::class, 'check']);
