<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\TimeTrackingService;
use App\Services\IssueService;
use App\Services\ProjectService;
use Exception;

/**
 * TimeTrackingController
 * 
 * Handles all HTTP requests for time tracking functionality
 * - Timer API endpoints
 * - Reports and dashboards
 * - Rate management
 * - Budget management
 */
class TimeTrackingController extends Controller
{
    private TimeTrackingService $timeTrackingService;
    private IssueService $issueService;
    private ProjectService $projectService;

    public function __construct()
    {
        $this->timeTrackingService = new TimeTrackingService();
        $this->issueService = new IssueService();
        $this->projectService = new ProjectService();
    }

    /**
     * Global Dashboard - Company-wide analytics and insights
     * 
     * Shows aggregate time tracking data across all projects and team members
     * Includes: team performance, cost analysis, billable breakdown, trends
     */
    public function globalDashboard(Request $request): string
    {
        $user = Session::user();

        if (!$user || !isset($user['id'])) {
            throw new Exception("User session not found. Please log in.");
        }

        $userId = (int)$user['id'];
        
        // Get date range filter (default to this week)
        $dateRange = $request->input('date_range') ?? 'week';
        [$startDate, $endDate] = $this->getDateRange($dateRange);

        // Get user stats for current user
        $userStats = $this->timeTrackingService->getUserTimeLogs($userId);
        $userAggregates = $this->aggregateTimeLogs($userStats);
        $userAggregates['currency'] = $this->detectCurrency($userStats);

        // Get team stats (if user is manager/admin)
        $teamStats = [];
        $topUsers = [];
        if ($this->isManagerOrAdmin($user)) {
            $topUsers = $this->timeTrackingService->getTopUsersByTime(10, $startDate, $endDate);
            foreach ($topUsers as &$tu) {
                $tu['avg_daily_hours'] = $this->calculateDailyAverage($tu['total_seconds'] ?? 0);
            }
        }

        // Get top issues by time
        $topIssues = $this->timeTrackingService->getTopIssuesByTime(10, $startDate, $endDate);

        // Get recent logs
        $recentLogs = $this->timeTrackingService->getRecentLogs(10, $startDate, $endDate);

        // Get weekly trend data
        $weeklyTrend = $this->timeTrackingService->getWeeklyTrend($userId);

        // Get project analysis
        $projectAnalysis = $this->timeTrackingService->getProjectAnalysis($userId, $startDate, $endDate);

        return $this->view('time-tracking.global-dashboard', [
            'userStats' => $userAggregates,
            'teamStats' => $teamStats,
            'topIssues' => $topIssues,
            'recentLogs' => $recentLogs,
            'weeklyTrend' => $weeklyTrend,
            'topUsers' => $topUsers,
            'projectAnalysis' => $projectAnalysis,
            'dateRange' => $dateRange,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * Dashboard - User's time tracking overview (project-scoped if selected)
     * 
     * Shows consolidated time tracking data for the current user across all projects.
     * If user wants to see a specific project, they should use /time-tracking/project/{id}
     */
    public function dashboard(Request $request): string
    {
        $user = Session::user();

        // Validate user session
        if (!$user || !isset($user['id'])) {
            throw new Exception("User session not found. Please log in.");
        }

        $userId = (int)$user['id'];

        // Get user's available projects
        $userProjects = $this->projectService->getUserProjects($userId);
        
        if (empty($userProjects)) {
            // No projects assigned - show empty state
            return $this->view('time-tracking.no-projects', [
                'user' => $user
            ]);
        }

        // Get time logs across ALL projects
        $allTimeLogs = $this->timeTrackingService->getUserTimeLogs($userId);

        // Calculate consolidated statistics from time logs
        $stats = [
            'total_hours' => 0,
            'total_cost' => 0,
            'total_logs' => count($allTimeLogs),
            'currency' => 'USD'
        ];
        
        foreach ($allTimeLogs as $log) {
            $stats['total_cost'] += (float)($log['total_cost'] ?? 0);
            $stats['total_hours'] += (int)($log['duration_seconds'] ?? 0) / 3600;
            if (!$stats['currency'] || $stats['currency'] === 'USD') {
                $stats['currency'] = $log['currency'] ?? 'USD';
            }
        }

        // Group time logs by project
        $byProject = [];
        foreach ($allTimeLogs as $log) {
            $projectId = (int)($log['project_id'] ?? 0);
            if (!isset($byProject[$projectId])) {
                $byProject[$projectId] = [];
            }
            $byProject[$projectId][] = $log;
        }

        // Get project details for the logs
        $projectDetails = [];
        foreach (array_keys($byProject) as $projectId) {
            $project = $this->projectService->getProjectById($projectId);
            if ($project) {
                $projectDetails[$projectId] = $project;
            }
        }

        return $this->view('time-tracking.dashboard', [
            'user' => $user,
            'timeLogs' => $allTimeLogs,
            'byProject' => $byProject,
            'projectDetails' => $projectDetails,
            'stats' => $stats,
            'projects' => $userProjects
        ]);
    }

    /**
     * Helper: Aggregate time log data
     */
    private function aggregateTimeLogs(array $logs): array
    {
        $total_seconds = 0;
        $total_cost = 0;
        $billable_seconds = 0;
        $billable_cost = 0;
        $non_billable_seconds = 0;
        $non_billable_cost = 0;
        $log_count = count($logs);

        foreach ($logs as $log) {
            $seconds = (int)($log['duration_seconds'] ?? 0);
            $cost = (float)($log['total_cost'] ?? 0);

            $total_seconds += $seconds;
            $total_cost += $cost;

            if ($log['is_billable'] === 1 || $log['is_billable'] === '1') {
                $billable_seconds += $seconds;
                $billable_cost += $cost;
            } else {
                $non_billable_seconds += $seconds;
                $non_billable_cost += $cost;
            }
        }

        $workDays = max(1, intdiv($total_seconds, 8 * 3600)); // Assume 8-hour workdays

        return [
            'total_seconds' => $total_seconds,
            'total_cost' => $total_cost,
            'billable_seconds' => $billable_seconds,
            'billable_cost' => $billable_cost,
            'non_billable_seconds' => $non_billable_seconds,
            'non_billable_cost' => $non_billable_cost,
            'log_count' => $log_count,
            'avg_daily_hours' => $this->calculateDailyAverage($total_seconds)
        ];
    }

    /**
     * Helper: Calculate daily average hours
     */
    private function calculateDailyAverage(int $totalSeconds): float
    {
        if ($totalSeconds === 0) return 0;
        // Assume 5-day workweek, 8-hour days
        $days = ceil($totalSeconds / (8 * 3600));
        return round(($totalSeconds / 3600) / max($days, 1), 1);
    }

    /**
     * Helper: Detect currency from logs
     */
    private function detectCurrency(array $logs): string
    {
        foreach ($logs as $log) {
            if (!empty($log['currency'])) {
                return $log['currency'];
            }
        }
        return 'USD';
    }

    /**
     * Helper: Check if user is manager or admin
     */
    private function isManagerOrAdmin(array $user): bool
    {
        $role = $user['role'] ?? '';
        return in_array($role, ['admin', 'manager', 'team_lead'], true);
    }

    /**
     * Helper: Get date range based on filter
     */
    private function getDateRange(string $range): array
    {
        $today = new \DateTime();
        $startDate = clone $today;
        $endDate = clone $today;

        switch ($range) {
            case 'today':
                $startDate->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;
            case 'week':
                $startDate->modify('-' . (($today->format('w') ?: 7) - 1) . ' days');
                $startDate->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;
            case 'month':
                $startDate->modify('first day of this month');
                $startDate->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;
            case 'quarter':
                $quarter = ceil($today->format('m') / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $startDate->setDate($today->format('Y'), $startMonth, 1);
                $startDate->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;
            case 'year':
                $startDate->setDate($today->format('Y'), 1, 1);
                $startDate->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;
            default:
                // Default to this week
                $startDate->modify('-' . (($today->format('w') ?: 7) - 1) . ' days');
                $startDate->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
        }

        return [$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')];
    }

    /**
     * Load and render project time tracking report
     * (Used by both dashboard smart default and direct /project/{id} access)
     */
    private function loadProjectReport(int $projectId, array $user): string
    {
        try {
            $userId = (int)$user['id'];
            
            // Store in session for future dashboard visits
            Session::set('last_viewed_project_id', $projectId);

            $project = $this->projectService->getProjectById($projectId);
            if (!$project) {
                throw new Exception("Project not found");
            }

            // Get budget information
            $budgetStatus = $this->projectService->getBudgetStatus($projectId);

            // Get time logs
            $timeLogs = $this->timeTrackingService->getProjectTimeLogs($projectId);

            // Get statistics
            $stats = $this->timeTrackingService->getCostStatistics($projectId);

            // Group by user
            $byUser = [];
            foreach ($timeLogs as $log) {
                if (!isset($byUser[$log['user_id']])) {
                    $byUser[$log['user_id']] = [
                        'name' => $log['display_name'] ?? $log['user_name'] ?? 'Unknown',
                        'avatar' => $log['avatar'] ?? null,
                        'total_seconds' => 0,
                        'total_cost' => 0,
                        'log_count' => 0
                    ];
                }
                $byUser[$log['user_id']]['total_seconds'] += (int)$log['duration_seconds'];
                $byUser[$log['user_id']]['total_cost'] += (float)$log['total_cost'];
                $byUser[$log['user_id']]['log_count']++;
            }

            return $this->view('time-tracking.project-report', [
                'project' => $project,
                'budget' => $budgetStatus,
                'timeLogs' => $timeLogs,
                'statistics' => $stats,
                'byUser' => $byUser
            ]);
        } catch (Exception $e) {
            return $this->view('errors.500', ['message' => $e->getMessage()]);
        }
    }

    /**
     * Issue timer widget - displayed on issue detail page
     */
    public function issueTimer(int $issueId): string
    {
        $user = Session::user();

        // Validate user session
        if (!$user || !isset($user['id'])) {
            throw new Exception("User session not found. Please log in.");
        }

        $userId = (int)$user['id'];

        // Get issue details
        $issue = $this->issueService->getIssue($issueId);
        if (!$issue) {
            throw new Exception("Issue not found");
        }

        // Get issue's time logs
        $timeLogs = $this->timeTrackingService->getIssueTimeLogs($issueId);

        // Calculate totals
        $totals = [
            'total_seconds' => 0,
            'total_cost' => 0,
            'by_user' => []
        ];

        foreach ($timeLogs as $log) {
            $totals['total_seconds'] += (int)$log['duration_seconds'];
            $totals['total_cost'] += (float)$log['total_cost'];

            if (!isset($totals['by_user'][$log['user_id']])) {
                $totals['by_user'][$log['user_id']] = [
                    'name' => $log['display_name'],
                    'avatar' => $log['avatar'],
                    'total_seconds' => 0,
                    'total_cost' => 0,
                    'log_count' => 0
                ];
            }

            $totals['by_user'][$log['user_id']]['total_seconds'] += (int)$log['duration_seconds'];
            $totals['by_user'][$log['user_id']]['total_cost'] += (float)$log['total_cost'];
            $totals['by_user'][$log['user_id']]['log_count']++;
        }

        // Check if user has active timer on this issue
        $activeTimer = null;
        try {
            $activeTimer = $this->timeTrackingService->getActiveTimer($userId);
            if ($activeTimer && $activeTimer['issue_id'] !== $issueId) {
                $activeTimer = null; // Active timer is on different issue
            }
        } catch (Exception $e) {
            // No active timer
        }

        return $this->view('time-tracking.issue-timer', [
            'issue' => $issue,
            'time_logs' => $timeLogs,
            'totals' => $totals,
            'active_timer' => $activeTimer,
            'current_user_id' => $userId
        ]);
    }

    /**
     * Get current timer status (AJAX)
     */
    public function getTimerStatus(Request $request): void
    {
        $user = Session::user();

        // Validate user session
        if (!$user || !isset($user['id'])) {
            $this->json(['error' => 'User session not found. Please log in.'], 401);
            return;
        }

        $userId = (int)$user['id'];

        try {
            $activeTimer = $this->timeTrackingService->getActiveTimer($userId);

            if (!$activeTimer) {
                $this->json(['status' => 'stopped']);
                return;
            }

            // Get time log details
            $timeLog = $this->timeTrackingService->getTimeLog($activeTimer['issue_time_log_id']);
            $issue = $this->issueService->getIssue($activeTimer['issue_id']);

            // Calculate elapsed time
            $startTime = strtotime($activeTimer['started_at']);
            $elapsedSeconds = time() - $startTime + (int)$timeLog['duration_seconds'];

            // Calculate cost
            $cost = ($elapsedSeconds / 3600) * (float)$timeLog['user_rate_amount'];

            $this->json([
                'status' => 'running',
                'time_log_id' => $activeTimer['issue_time_log_id'],
                'issue_id' => $activeTimer['issue_id'],
                'issue_key' => $issue['key'],
                'issue_summary' => $issue['summary'],
                'started_at' => $startTime,
                'elapsed_seconds' => $elapsedSeconds,
                'cost' => round($cost, 2),
                'rate_type' => $timeLog['user_rate_type'],
                'rate_amount' => (float)$timeLog['user_rate_amount']
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Start timer (AJAX)
     */
    public function startTimer(Request $request): void
    {
        $user = Session::user();

        // Validate user session
        if (!$user || !isset($user['id'])) {
            $this->json(['error' => 'User session not found. Please log in.'], 401);
            return;
        }

        $userId = (int)$user['id'];

        try {
            $issueId = (int)$request->input('issue_id');
            $projectId = (int)$request->input('project_id');

            if (!$issueId || !$projectId) {
                throw new Exception("Issue ID and Project ID required");
            }

            // Verify user has access to this project
            // (Skip for now - add authorization check as needed)

            $result = $this->timeTrackingService->startTimer($issueId, $userId, $projectId);

            $this->json($result);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Pause timer (AJAX)
     */
    public function pauseTimer(Request $request): void
    {
        $user = Session::user();

        // Validate user session
        if (!$user || !isset($user['id'])) {
            $this->json(['error' => 'User session not found. Please log in.'], 401);
            return;
        }

        $userId = (int)$user['id'];

        try {
            $result = $this->timeTrackingService->pauseTimer($userId);
            $this->json($result);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Resume timer (AJAX)
     */
    public function resumeTimer(Request $request): void
    {
        $user = Session::user();

        // Validate user session
        if (!$user || !isset($user['id'])) {
            $this->json(['error' => 'User session not found. Please log in.'], 401);
            return;
        }

        $userId = (int)$user['id'];

        try {
            $result = $this->timeTrackingService->resumeTimer($userId);
            $this->json($result);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Stop timer (AJAX)
     */
    public function stopTimer(Request $request): void
    {
        $user = Session::user();

        // Validate user session
        if (!$user || !isset($user['id'])) {
            $this->json(['error' => 'User session not found. Please log in.'], 401);
            return;
        }

        $userId = (int)$user['id'];

        try {
            $description = $request->input('description');

            $result = $this->timeTrackingService->stopTimer($userId, $description);

            $this->json($result);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get user's time logs (AJAX)
     */
    public function getUserTimeLogs(Request $request): void
    {
        $user = Session::user();

        // Validate user session
        if (!$user || !isset($user['id'])) {
            $this->json(['error' => 'User session not found. Please log in.'], 401);
            return;
        }

        $userId = (int)$user['id'];

        try {
            $filters = [
                'project_id' => $request->input('project_id') ? (int)$request->input('project_id') : null,
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'status' => $request->input('status'),
                'is_billable' => $request->input('is_billable') !== null
                    ? $request->input('is_billable') === 'true'
                    : null
            ];

            $logs = $this->timeTrackingService->getUserTimeLogs($userId, $filters);

            $this->json([
                'success' => true,
                'logs' => $logs,
                'count' => count($logs)
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Set user rate
     */
    public function setUserRate(Request $request): void
    {
        $user = Session::user();

        // Validate user session
        if (!$user || !isset($user['id'])) {
            $this->json(['error' => 'User session not found. Please log in.'], 401);
            return;
        }

        $userId = (int)$user['id'];

        try {
            $request->validate([
                'rate_type' => 'required|in:hourly,minutely,secondly',
                'rate_amount' => 'required|numeric|minValue:0.01',
                'currency' => 'required|min:3|max:3'
            ]);

            $result = $this->timeTrackingService->setUserRate(
                $userId,
                $request->input('rate_type'),
                (float)$request->input('rate_amount'),
                $request->input('currency')
            );

            $this->json($result);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get project time tracking report
     */
    public function projectReport($projectId = null): string
    {
        // Handle parameter extraction from Request if needed
        if ($projectId instanceof \App\Core\Request) {
            $projectId = (int) $projectId->param('projectId');
        } else {
            $projectId = (int) $projectId;
        }

        $user = Session::user();
        if (!$user) {
            throw new Exception("User not authenticated");
        }

        // Use shared loadProjectReport method
        return $this->loadProjectReport($projectId, $user);
    }

    /**
     * Get user time tracking report
     */
    public function userReport($userId = null): string
    {
        try {
            // Handle parameter extraction from Request if needed
            if ($userId instanceof \App\Core\Request) {
                $userId = (int) $userId->param('userId');
            } else {
                $userId = (int) $userId;
            }

            $logs = $this->timeTrackingService->getUserTimeLogs($userId);

            // Calculate totals
            $totals = [
                'total_seconds' => 0,
                'total_cost' => 0,
                'billable_cost' => 0,
                'non_billable_cost' => 0,
                'log_count' => count($logs)
            ];

            $byProject = [];

            foreach ($logs as $log) {
                $totals['total_seconds'] += (int)$log['duration_seconds'];
                $totals['total_cost'] += (float)$log['total_cost'];

                if ($log['is_billable']) {
                    $totals['billable_cost'] += (float)$log['total_cost'];
                } else {
                    $totals['non_billable_cost'] += (float)$log['total_cost'];
                }

                $projectId = $log['project_id'];
                if (!isset($byProject[$projectId])) {
                    $byProject[$projectId] = [
                        'name' => $log['project_name'],
                        'total_seconds' => 0,
                        'total_cost' => 0
                    ];
                }
                $byProject[$projectId]['total_seconds'] += (int)$log['duration_seconds'];
                $byProject[$projectId]['total_cost'] += (float)$log['total_cost'];
            }

            return $this->view('time-tracking.user-report', [
                'user_id' => $userId,
                'logs' => $logs,
                'totals' => $totals,
                'by_project' => $byProject
            ]);
        } catch (Exception $e) {
            return $this->view('errors.500', ['message' => $e->getMessage()]);
        }
    }

    /**
     * Budget dashboard
     */
    public function budgetDashboard(): string
    {
        try {
            // Get all projects (admin only - add check)
            $projectsData = $this->projectService->getAllProjects();
            $projects = $projectsData['items'] ?? [];

            $budgets = [];
            foreach ($projects as $project) {
                $projectId = $project['id'] ?? null;
                if (empty($projectId)) {
                    continue;
                }
                
                $budget = $this->timeTrackingService->getProjectBudgetSummary($projectId);
                if (!empty($budget)) {
                    $budgets[] = array_merge($budget, ['project_name' => $project['name']]);
                }
            }

            return $this->view('time-tracking.budget-dashboard', [
                'budgets' => $budgets
            ]);
        } catch (Exception $e) {
            return $this->view('errors.500', ['message' => $e->getMessage()]);
        }
    }
    }
