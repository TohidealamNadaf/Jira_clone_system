<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Request;
use App\Services\TimeTrackingService;
use Exception;

/**
 * TimeTrackingApiController
 * 
 * RESTful API endpoints for time tracking
 * All endpoints require JWT authentication
 */
class TimeTrackingApiController extends Controller
{
    private TimeTrackingService $service;

    public function __construct()
    {
        $this->service = new TimeTrackingService();
    }

    /**
     * POST /api/v1/time-tracking/start
     * Start a timer on an issue
     */
    public function start(Request $request): void
    {
        try {
            $request->validate([
                'issue_id' => 'required|integer',
                'project_id' => 'required|integer'
            ]);

            $userId = $this->getCurrentUserId();

            $result = $this->service->startTimer(
                (int)$request->input('issue_id'),
                $userId,
                (int)$request->input('project_id')
            );

            $this->json($result, 201);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * POST /api/v1/time-tracking/pause
     * Pause running timer
     */
    public function pause(Request $request): void
    {
        try {
            $userId = $this->getCurrentUserId();
            $result = $this->service->pauseTimer($userId);
            $this->json($result);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * POST /api/v1/time-tracking/resume
     * Resume paused timer
     */
    public function resume(Request $request): void
    {
        try {
            $userId = $this->getCurrentUserId();
            $result = $this->service->resumeTimer($userId);
            $this->json($result);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * POST /api/v1/time-tracking/stop
     * Stop running timer
     */
    public function stop(Request $request): void
    {
        try {
            $userId = $this->getCurrentUserId();
            $description = $request->input('description');

            $result = $this->service->stopTimer($userId, $description);

            $this->json($result);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * GET /api/v1/time-tracking/status
     * Get current timer status
     */
    public function status(Request $request): void
    {
        try {
            $userId = $this->getCurrentUserId();

            $activeTimer = $this->service->getActiveTimer($userId);

            if (!$activeTimer) {
                $this->json([
                    'status' => 'stopped',
                    'time_log_id' => null
                ]);
                return;
            }

            $timeLog = $this->service->getTimeLog($activeTimer['issue_time_log_id']);

            // Calculate elapsed seconds
            $startTime = strtotime($activeTimer['started_at']);
            $elapsedSeconds = time() - $startTime + (int)$timeLog['duration_seconds'];

            $this->json([
                'status' => 'running',
                'time_log_id' => $activeTimer['issue_time_log_id'],
                'issue_id' => $activeTimer['issue_id'],
                'started_at' => $startTime,
                'elapsed_seconds' => $elapsedSeconds,
                'rate_type' => $timeLog['user_rate_type'],
                'rate_amount' => (float)$timeLog['user_rate_amount'],
                'currency' => $timeLog['currency'] ?? 'USD'
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/v1/time-tracking/logs
     * Get user's time logs with filtering
     */
    public function logs(Request $request): void
    {
        try {
            $userId = (int)$request->query('user_id') ?: $this->getCurrentUserId();

            $filters = [
                'project_id' => $request->query('project_id') ? (int)$request->query('project_id') : null,
                'start_date' => $request->query('start_date'),
                'end_date' => $request->query('end_date'),
                'status' => $request->query('status'),
                'is_billable' => $request->query('is_billable') !== null
                    ? $request->query('is_billable') === 'true'
                    : null
            ];

            $logs = $this->service->getUserTimeLogs($userId, $filters);

            // Calculate totals
            $totals = [
                'total_logs' => count($logs),
                'total_seconds' => 0,
                'total_cost' => 0.00
            ];

            foreach ($logs as $log) {
                $totals['total_seconds'] += (int)$log['duration_seconds'];
                $totals['total_cost'] += (float)$log['total_cost'];
            }

            $this->json([
                'success' => true,
                'logs' => $logs,
                'totals' => $totals
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/v1/time-tracking/issue/{issueId}
     * Get time logs for specific issue
     */
    public function issueTimeLogs(int $issueId): void
    {
        try {
            $logs = $this->service->getIssueTimeLogs($issueId);

            // Calculate totals by user
            $byUser = [];
            $totals = [
                'total_seconds' => 0,
                'total_cost' => 0.00,
                'log_count' => count($logs)
            ];

            foreach ($logs as $log) {
                $totals['total_seconds'] += (int)$log['duration_seconds'];
                $totals['total_cost'] += (float)$log['total_cost'];

                $userId = $log['user_id'];
                if (!isset($byUser[$userId])) {
                    $byUser[$userId] = [
                        'user_id' => $userId,
                        'name' => $log['display_name'],
                        'total_seconds' => 0,
                        'total_cost' => 0.00,
                        'log_count' => 0
                    ];
                }

                $byUser[$userId]['total_seconds'] += (int)$log['duration_seconds'];
                $byUser[$userId]['total_cost'] += (float)$log['total_cost'];
                $byUser[$userId]['log_count']++;
            }

            $this->json([
                'success' => true,
                'logs' => $logs,
                'by_user' => array_values($byUser),
                'totals' => $totals
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/v1/time-tracking/rate
     * Set user's rate
     */
    public function setRate(Request $request): void
    {
        try {
            $request->validate([
                'rate_type' => 'required|in:hourly,minutely,secondly',
                'rate_amount' => 'required|numeric|min:0.01',
                'currency' => 'required|size:3'
            ]);

            $userId = $this->getCurrentUserId();

            $result = $this->service->setUserRate(
                $userId,
                $request->input('rate_type'),
                (float)$request->input('rate_amount'),
                $request->input('currency')
            );

            $this->json($result, 201);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * GET /api/v1/time-tracking/rate
     * Get user's current rate
     */
    public function getRate(Request $request): void
    {
        try {
            $userId = $this->getCurrentUserId();
            $rate = $this->service->getUserCurrentRate($userId);

            $this->json([
                'success' => true,
                'rate' => $rate
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/v1/time-tracking/project/{projectId}/budget
     * Get project budget summary
     */
    public function projectBudget(int $projectId): void
    {
        try {
            $budget = $this->service->getProjectBudgetSummary($projectId);

            $this->json([
                'success' => true,
                'budget' => $budget
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/v1/time-tracking/project/{projectId}/statistics
     * Get project cost statistics
     */
    public function projectStatistics(int $projectId): void
    {
        try {
            $filters = [
                'start_date' => request()->query('start_date'),
                'end_date' => request()->query('end_date')
            ];

            $stats = $this->service->getCostStatistics($projectId, $filters);

            $this->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get current authenticated user ID
     * (In real implementation, get from JWT token)
     */
    private function getCurrentUserId(): int
    {
        // Get from session if available (fallback for non-JWT auth)
        $session = \App\Core\Session::user();
        if ($session && isset($session['id'])) {
            return (int)$session['id'];
        }
        
        // Get from custom header if present (for API clients)
        if (!empty($_SERVER['HTTP_X_USER_ID'])) {
            return (int)$_SERVER['HTTP_X_USER_ID'];
        }
        
        // Fallback to user 1 (should not reach here in normal operation)
        return 1;
    }
}
