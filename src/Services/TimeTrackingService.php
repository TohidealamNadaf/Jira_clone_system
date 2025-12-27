<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use Exception;
use DateTime;

/**
 * TimeTrackingService
 * 
 * Handles all time tracking operations:
 * - Start/pause/resume/stop timers
 * - Calculate costs based on user rates
 * - Manage active timers
 * - Generate reports
 * 
 * Server-side source of truth for all time calculations
 */
class TimeTrackingService
{
    private const TABLE_TIME_LOGS = 'issue_time_logs';
    private const TABLE_ACTIVE_TIMERS = 'active_timers';
    private const TABLE_USER_RATES = 'user_rates';

    /**
     * Start a new timer for an issue
     * 
     * @param int $issueId The issue ID
     * @param int $userId The user starting the timer
     * @param int $projectId The project ID
     * @return array The created time log with status
     * @throws Exception
     */
    public function startTimer(int $issueId, int $userId, int $projectId): array
    {
        try {
            // Stop any existing running timer for this user
            $this->stopActiveTimer($userId);

            // Get user's current rate
            $userRate = $this->getUserCurrentRate($userId);
            if (!$userRate) {
                throw new Exception("User rate not configured. Please contact administrator.");
            }

            // Create new time log entry
            $startTime = new DateTime();
            
            $timeLogId = Database::insert(self::TABLE_TIME_LOGS, [
                'issue_id' => $issueId,
                'user_id' => $userId,
                'project_id' => $projectId,
                'status' => 'running',
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'paused_at' => $startTime->format('Y-m-d H:i:s'),
                'duration_seconds' => 0,
                'paused_seconds' => 0,
                'user_rate_type' => $userRate['rate_type'],
                'user_rate_amount' => $userRate['rate_amount'],
                'total_cost' => 0.00,
                'currency' => $userRate['currency'],
                'is_billable' => 1
            ]);

            // Create active timer entry
            Database::insert(self::TABLE_ACTIVE_TIMERS, [
                'user_id' => $userId,
                'issue_time_log_id' => $timeLogId,
                'issue_id' => $issueId,
                'project_id' => $projectId,
                'started_at' => $startTime->format('Y-m-d H:i:s'),
                'last_heartbeat' => $startTime->format('Y-m-d H:i:s')
            ]);

            return [
                'success' => true,
                'time_log_id' => $timeLogId,
                'status' => 'running',
                'start_time' => $startTime->getTimestamp(),
                'elapsed_seconds' => 0,
                'cost' => 0.00,
                'rate_type' => $userRate['rate_type'],
                'rate_amount' => $userRate['rate_amount'],
                'currency' => $userRate['currency']
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Pause the running timer for a user
     * 
     * @param int $userId The user ID
     * @return array Updated timer status
     * @throws Exception
     */
    public function pauseTimer(int $userId): array
    {
        try {
            // Get active timer
            $activeTimer = $this->getActiveTimer($userId);
            if (!$activeTimer) {
                throw new Exception("No running timer found for this user.");
            }

            $timeLogId = $activeTimer['issue_time_log_id'];
            $timeLog = $this->getTimeLog($timeLogId);

            $pausedAt = new DateTime();
            $startTime = new DateTime($timeLog['start_time']);
            $elapsedSeconds = (int)$pausedAt->getTimestamp() - (int)$startTime->getTimestamp();

            $totalCost = $this->calculateCost(
                $elapsedSeconds,
                $timeLog['user_rate_type'],
                (float)$timeLog['user_rate_amount']
            );

            // Update time log
            Database::update(self::TABLE_TIME_LOGS, [
                'status' => 'paused',
                'paused_at' => $pausedAt->format('Y-m-d H:i:s'),
                'duration_seconds' => $elapsedSeconds,
                'total_cost' => $totalCost
            ], 'id = ?', [$timeLogId]);

            // Keep active timer entry so timer can be resumed
            // Only delete active timer when user explicitly stops (not pauses)
            // Update heartbeat for tracking
            Database::update(self::TABLE_ACTIVE_TIMERS, [
                'last_heartbeat' => $pausedAt->format('Y-m-d H:i:s')
            ], 'user_id = ?', [$userId]);

            return [
                'success' => true,
                'status' => 'paused',
                'elapsed_seconds' => $elapsedSeconds,
                'cost' => $totalCost,
                'time_log_id' => $timeLogId,
                'rate_type' => $timeLog['user_rate_type'],
                'rate_amount' => (float)$timeLog['user_rate_amount'],
                'currency' => $timeLog['currency'] ?? 'USD'
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Resume a paused timer
     * 
     * @param int $userId The user ID
     * @return array Updated timer status
     * @throws Exception
     */
    public function resumeTimer(int $userId): array
    {
        try {
            // First, get the active timer entry to find which time log to resume
            $activeTimer = $this->getActiveTimer($userId);
            if (!$activeTimer) {
                throw new Exception("No paused timer found for this user.");
            }

            // Get the specific time log from active_timers reference
            $timeLogId = $activeTimer['issue_time_log_id'];
            $timeLog = $this->getTimeLog($timeLogId);
            
            // If already running, return success (idempotent operation)
            if ($timeLog['status'] === 'running') {
                // Timer is already running, just return current state
                return [
                    'success' => true,
                    'time_log_id' => $timeLog['id'],
                    'status' => 'running',
                    'elapsed_seconds' => (int)$timeLog['duration_seconds'],
                    'cost' => (float)$timeLog['total_cost'],
                    'start_time' => strtotime($timeLog['start_time']),
                    'rate_type' => $timeLog['user_rate_type'],
                    'rate_amount' => (float)$timeLog['user_rate_amount'],
                    'currency' => $timeLog['currency'] ?? 'USD'
                ];
            }
            
            // Only resume if paused
            if ($timeLog['status'] !== 'paused') {
                throw new Exception("Timer is in unexpected state: " . $timeLog['status']);
            }

            $resumedAt = new DateTime();

            // Update time log back to running
            Database::update(self::TABLE_TIME_LOGS, [
                'status' => 'running',
                'resumed_at' => $resumedAt->format('Y-m-d H:i:s')
            ], 'id = ?', [$timeLog['id']]);

            // Update the existing active timer (don't insert a new one - violates UNIQUE constraint)
            // active_timers has UNIQUE KEY on user_id, so only one timer per user
            Database::update(self::TABLE_ACTIVE_TIMERS, [
                'issue_time_log_id' => $timeLog['id'],
                'issue_id' => $timeLog['issue_id'],
                'project_id' => $timeLog['project_id'],
                'started_at' => $resumedAt->format('Y-m-d H:i:s'),
                'last_heartbeat' => $resumedAt->format('Y-m-d H:i:s')
            ], 'user_id = ?', [$userId]);

            return [
                'success' => true,
                'time_log_id' => $timeLog['id'],
                'status' => 'running',
                'elapsed_seconds' => (int)$timeLog['duration_seconds'],
                'cost' => (float)$timeLog['total_cost'],
                'start_time' => $resumedAt->getTimestamp(),
                'rate_type' => $timeLog['user_rate_type'],
                'rate_amount' => (float)$timeLog['user_rate_amount'],
                'currency' => $timeLog['currency'] ?? 'USD'
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Stop the running timer and finalize the time log
     * 
     * @param int $userId The user ID
     * @param string|null $description Optional description of work done
     * @return array Finalized time log
     * @throws Exception
     */
    public function stopTimer(int $userId, ?string $description = null): array
    {
        try {
            $activeTimer = $this->getActiveTimer($userId);
            if (!$activeTimer) {
                throw new Exception("No running timer found for this user.");
            }

            $timeLogId = $activeTimer['issue_time_log_id'];
            $timeLog = $this->getTimeLog($timeLogId);

            $stoppedAt = new DateTime();
            
            // Calculate total elapsed time
            $startTime = new DateTime($timeLog['start_time']);
            $totalElapsedSeconds = (int)$stoppedAt->getTimestamp() - (int)$startTime->getTimestamp();

            // Calculate final cost
            $totalCost = $this->calculateCost(
                $totalElapsedSeconds,
                $timeLog['user_rate_type'],
                (float)$timeLog['user_rate_amount']
            );

            // Update time log
            Database::update(self::TABLE_TIME_LOGS, [
                'status' => 'stopped',
                'end_time' => $stoppedAt->format('Y-m-d H:i:s'),
                'duration_seconds' => $totalElapsedSeconds,
                'total_cost' => $totalCost,
                'description' => $description ?? ''
            ], 'id = ?', [$timeLogId]);

            // Remove from active timers
            Database::delete(self::TABLE_ACTIVE_TIMERS, 'user_id = ?', [$userId]);

            // Update project budget if exists
            $this->updateProjectBudget($timeLog['project_id'], $totalCost);

            return [
                'success' => true,
                'time_log_id' => $timeLogId,
                'status' => 'stopped',
                'elapsed_seconds' => $totalElapsedSeconds,
                'cost' => $totalCost,
                'end_time' => $stoppedAt->getTimestamp()
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the current active timer for a user
     * 
     * @param int $userId The user ID
     * @return array|null Active timer data or null
     */
    public function getActiveTimer(int $userId): ?array
    {
        $sql = "
            SELECT * FROM " . self::TABLE_ACTIVE_TIMERS . "
            WHERE user_id = ?
            LIMIT 1
        ";

        $result = Database::selectOne($sql, [$userId]);
        return $result ?: null;
    }

    /**
      * Get time log details
      * 
      * @param int $timeLogId The time log ID
      * @return array Time log data
      * @throws Exception
      */
    public function getTimeLog(int $timeLogId): array
    {
        $sql = "
            SELECT * FROM " . self::TABLE_TIME_LOGS . "
            WHERE id = ?
            LIMIT 1
        ";

        $result = Database::selectOne($sql, [$timeLogId]);
        if (!$result) {
            throw new Exception("Time log not found.");
        }

        return $result;
    }

    /**
     * Get all time logs for an issue
     * 
     * @param int $issueId The issue ID
     * @return array Array of time logs
     */
    public function getIssueTimeLogs(int $issueId): array
    {
        $sql = "
            SELECT 
                tl.*,
                u.display_name, u.avatar,
                i.issue_key, i.summary
            FROM " . self::TABLE_TIME_LOGS . " tl
            LEFT JOIN users u ON tl.user_id = u.id
            LEFT JOIN issues i ON tl.issue_id = i.id
            WHERE tl.issue_id = ?
            ORDER BY tl.created_at DESC
        ";

        return Database::select($sql, [$issueId]);
    }

    /**
      * Get all time logs for a user with optional filters
      * 
      * @param int $userId The user ID
      * @param array $filters Optional filters (project_id, start_date, end_date, status)
      * @return array Array of time logs
      */
    public function getUserTimeLogs(int $userId, array $filters = []): array
    {
        $sql = "
            SELECT 
                tl.*,
                i.issue_key, i.summary,
                p.name as project_name
            FROM " . self::TABLE_TIME_LOGS . " tl
            LEFT JOIN issues i ON tl.issue_id = i.id
            LEFT JOIN projects p ON tl.project_id = p.id
            WHERE tl.user_id = ?
        ";

        $params = [$userId];

        if (!empty($filters['project_id'])) {
            $sql .= " AND tl.project_id = ?";
            $params[] = $filters['project_id'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(tl.created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(tl.created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND tl.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['is_billable'])) {
            $sql .= " AND tl.is_billable = ?";
            $params[] = $filters['is_billable'] ? 1 : 0;
        }

        $sql .= " ORDER BY tl.created_at DESC";

        return Database::select($sql, $params);
    }

    /**
     * Get user's current rate
     * 
     * @param int $userId The user ID
     * @return array|null Current rate or null if not configured
     */
    public function getUserCurrentRate(int $userId): ?array
    {
        // First try to get rate from user_settings (new system)
        try {
            $result = Database::selectOne(
                "SELECT * FROM user_settings WHERE user_id = ? AND annual_package IS NOT NULL",
                [$userId]
            );
            
            if ($result && !empty($result['annual_package'])) {
                return [
                    'user_id' => $userId,
                    'rate_type' => 'hourly',
                    'rate_amount' => (float)$result['hourly_rate'],
                    'currency' => $result['rate_currency'] ?? 'USD',
                    'is_active' => 1
                ];
            }
        } catch (Exception $e) {
            // Table might not exist, continue to legacy system
        }
        
        // Fall back to legacy user_rates table
        $sql = "
            SELECT * FROM " . self::TABLE_USER_RATES . "
            WHERE user_id = ? AND is_active = 1
            ORDER BY effective_from DESC
            LIMIT 1
        ";

        $result = Database::selectOne($sql, [$userId]);
        return $result ?: null;
    }

    /**
      * Set user's rate (creates new rate or updates existing)
      * 
      * @param int $userId The user ID
      * @param string $rateType 'hourly', 'minutely', or 'secondly'
      * @param float $rateAmount Amount per unit
      * @param string $currency Currency code
      * @return array Created/updated rate
      * @throws Exception
      */
    public function setUserRate(
        int $userId,
        string $rateType,
        float $rateAmount,
        string $currency = 'USD'
    ): array {
        if (!in_array($rateType, ['hourly', 'minutely', 'secondly'])) {
            throw new Exception("Invalid rate type. Must be hourly, minutely, or secondly.");
        }

        if ($rateAmount <= 0) {
            throw new Exception("Rate amount must be greater than 0.");
        }

        try {
            // Deactivate any existing rates of this type
            Database::update(self::TABLE_USER_RATES, [
                'is_active' => 0
            ], 'user_id = ? AND rate_type = ?', [$userId, $rateType]);

            // Create new rate
            $today = (new DateTime())->format('Y-m-d');
            
            $rateId = Database::insert(self::TABLE_USER_RATES, [
                'user_id' => $userId,
                'rate_type' => $rateType,
                'rate_amount' => $rateAmount,
                'currency' => $currency,
                'is_active' => 1,
                'effective_from' => $today
            ]);

            return [
                'id' => $rateId,
                'user_id' => $userId,
                'rate_type' => $rateType,
                'rate_amount' => $rateAmount,
                'currency' => $currency,
                'is_active' => 1
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Calculate cost based on duration and rate
     * 
     * @param int $durationSeconds Total seconds worked
     * @param string $rateType 'hourly', 'minutely', or 'secondly'
     * @param float $rateAmount Rate per unit
     * @return float Calculated cost
     */
    private function calculateCost(int $durationSeconds, string $rateType, float $rateAmount): float
    {
        $duration = match($rateType) {
            'hourly' => $durationSeconds / 3600,
            'minutely' => $durationSeconds / 60,
            'secondly' => $durationSeconds,
            default => 0
        };

        return round($duration * $rateAmount, 2);
    }

    /**
     * Stop any running timer for a user (used when starting new timer)
     * 
     * @param int $userId The user ID
     * @return void
     */
    private function stopActiveTimer(int $userId): void
    {
        $activeTimer = $this->getActiveTimer($userId);
        if ($activeTimer) {
            try {
                $this->stopTimer($userId, "Auto-paused - new timer started");
            } catch (Exception $e) {
                // Log but don't fail
                error_log("Failed to auto-stop timer: " . $e->getMessage());
            }
        }
    }

    /**
     * Update project budget with new cost
     * 
     * @param int $projectId The project ID
     * @param float $additionalCost Additional cost to add
     * @return void
     */
    private function updateProjectBudget(int $projectId, float $additionalCost): void
    {
        try {
            // Use raw query to handle arithmetic
            Database::query(
                "UPDATE project_budgets SET total_cost = total_cost + ? WHERE project_id = ?",
                [$additionalCost, $projectId]
            );

            // Check if we need to trigger alerts
            $this->checkBudgetAlerts($projectId);
        } catch (Exception $e) {
            error_log("Failed to update project budget: " . $e->getMessage());
        }
    }

    /**
      * Check and trigger budget alerts if threshold exceeded
      * 
      * @param int $projectId The project ID
      * @return void
      */
    private function checkBudgetAlerts(int $projectId): void
    {
        try {
            $sql = "
                SELECT * FROM project_budgets
                WHERE project_id = ? AND total_budget > 0
            ";

            $budget = Database::selectOne($sql, [$projectId]);
            if (!$budget) {
                return;
            }

            $percentageUsed = ($budget['total_cost'] / $budget['total_budget']) * 100;

            // Skip if no threshold defined
            if ($budget['alert_threshold'] <= 0) {
                return;
            }

            // Determine alert type
            if ($percentageUsed >= 100) {
                $alertType = 'exceeded';
            } elseif ($percentageUsed >= 90) {
                $alertType = 'critical';
            } elseif ($percentageUsed >= (float)$budget['alert_threshold']) {
                $alertType = 'warning';
            } else {
                return;
            }

            // Check if we already have an unacknowledged alert of this type
            $sql = "
                SELECT id FROM budget_alerts
                WHERE project_budget_id = ? AND alert_type = ? AND is_acknowledged = 0
            ";

            $existingAlert = Database::selectOne($sql, [$budget['id'], $alertType]);
            if ($existingAlert) {
                return; // Alert already exists
            }

            // Create new alert
            Database::insert('budget_alerts', [
                'project_budget_id' => $budget['id'],
                'project_id' => $projectId,
                'alert_type' => $alertType,
                'threshold_percentage' => $budget['alert_threshold'],
                'actual_percentage' => $percentageUsed,
                'cost_at_alert' => $budget['total_cost'],
                'remaining_budget_at_alert' => $budget['total_budget'] - $budget['total_cost']
            ]);
        } catch (Exception $e) {
            error_log("Failed to check budget alerts: " . $e->getMessage());
        }
    }

    /**
     * Get project budget and cost summary
     * 
     * @param int $projectId The project ID
     * @return array Budget summary
     */
    public function getProjectBudgetSummary(int $projectId): array
    {
        $sql = "
            SELECT 
                *,
                (total_budget - total_cost) as remaining_budget,
                ROUND((total_cost / total_budget * 100), 2) as percentage_used,
                CASE 
                    WHEN total_cost >= total_budget THEN 'exceeded'
                    WHEN (total_cost / total_budget) >= 0.9 THEN 'critical'
                    WHEN (total_cost / total_budget) >= 0.8 THEN 'warning'
                    ELSE 'ok'
                END as status
            FROM project_budgets
            WHERE project_id = ?
        ";

        $result = Database::selectOne($sql, [$projectId]);
        return $result ?: [];
    }

    /**
      * Get all time logs for a project with totals
      * 
      * @param int $projectId The project ID
      * @param array $filters Optional filters
      * @return array Time logs with totals
      */
    public function getProjectTimeLogs(int $projectId, array $filters = []): array
    {
        $sql = "
            SELECT 
                tl.*,
                u.display_name, u.avatar,
                i.issue_key, i.summary
            FROM " . self::TABLE_TIME_LOGS . " tl
            LEFT JOIN users u ON tl.user_id = u.id
            LEFT JOIN issues i ON tl.issue_id = i.id
            WHERE tl.project_id = ?
        ";

        $params = [$projectId];

        if (!empty($filters['user_id'])) {
            $sql .= " AND tl.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(tl.created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(tl.created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        $sql .= " ORDER BY tl.created_at DESC";

        return Database::select($sql, $params);
    }

    /**
      * Get cost summary statistics
      * 
      * @param int $projectId The project ID
      * @param array $filters Optional filters
      * @return array Statistics
      */
    public function getCostStatistics(int $projectId, array $filters = []): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total_logs,
                SUM(duration_seconds) as total_seconds,
                SUM(total_cost) as total_cost,
                AVG(total_cost) as avg_cost_per_log,
                MIN(total_cost) as min_cost,
                MAX(total_cost) as max_cost,
                COUNT(DISTINCT user_id) as unique_users,
                SUM(is_billable) as billable_logs
            FROM " . self::TABLE_TIME_LOGS . "
            WHERE project_id = ?
        ";

        $params = [$projectId];

        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        return Database::selectOne($sql, $params) ?: [];
    }

    /**
     * Get top issues by time spent
     */
    public function getTopIssuesByTime(int $limit = 10, string $startDate = '', string $endDate = ''): array
    {
        $sql = "
            SELECT 
                itl.issue_id,
                i.issue_key,
                i.summary as issue_summary,
                p.key as project_key,
                p.name as project_name,
                SUM(itl.duration_seconds) as total_seconds,
                COUNT(itl.id) as log_count,
                SUM(itl.total_cost) as total_cost
            FROM " . self::TABLE_TIME_LOGS . " itl
            JOIN issues i ON i.id = itl.issue_id
            JOIN projects p ON p.id = itl.project_id
        ";

        $params = [];

        if ($startDate) {
            $sql .= " WHERE itl.created_at >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $sql .= (empty($params) ? " WHERE " : " AND ") . "itl.created_at <= ?";
            $params[] = $endDate;
        }

        $sql .= " GROUP BY itl.issue_id, i.issue_key, i.summary, p.key, p.name
                  ORDER BY total_seconds DESC
                  LIMIT ?";
        $params[] = $limit;

        return Database::select($sql, $params) ?? [];
    }

    /**
     * Get recent time log entries
     */
    public function getRecentLogs(int $limit = 10, string $startDate = '', string $endDate = ''): array
    {
        $sql = "
            SELECT 
                itl.id,
                itl.issue_id,
                itl.user_id,
                itl.duration_seconds,
                itl.total_cost,
                itl.created_at,
                i.issue_key,
                i.summary as issue_summary,
                p.key as project_key,
                p.name as project_name,
                u.display_name as user_name,
                u.first_name,
                u.last_name,
                u.avatar
            FROM " . self::TABLE_TIME_LOGS . " itl
            JOIN issues i ON i.id = itl.issue_id
            JOIN projects p ON p.id = itl.project_id
            JOIN users u ON u.id = itl.user_id
        ";

        $params = [];

        if ($startDate) {
            $sql .= " WHERE itl.created_at >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $sql .= (empty($params) ? " WHERE " : " AND ") . "itl.created_at <= ?";
            $params[] = $endDate;
        }

        $sql .= " ORDER BY itl.created_at DESC
                  LIMIT ?";
        $params[] = $limit;

        return Database::select($sql, $params) ?? [];
    }

    /**
     * Get weekly trend data
     */
    public function getWeeklyTrend(int $userId): array
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $trend = [];

        for ($i = 0; $i < 7; $i++) {
            $date = new DateTime();
            $date->modify('-' . (($date->format('w') ?: 7) - 1 - (6 - $i)) . ' days');
            $dateStr = $date->format('Y-m-d');

            $sql = "
                SELECT SUM(duration_seconds) as seconds
                FROM " . self::TABLE_TIME_LOGS . "
                WHERE user_id = ? AND DATE(created_at) = ?
            ";

            $result = Database::selectOne($sql, [$userId, $dateStr]);
            $seconds = (int)($result['seconds'] ?? 0);

            $trend[] = [
                'day' => $days[$i] ?? 'Unknown',
                'date' => $dateStr,
                'seconds' => $seconds
            ];
        }

        return $trend;
    }

    /**
     * Get project analysis by cost
     */
    public function getProjectAnalysis(int $userId, string $startDate = '', string $endDate = ''): array
    {
        $sql = "
            SELECT 
                p.id as project_id,
                p.key as project_key,
                p.name as project_name,
                SUM(itl.duration_seconds) as total_seconds,
                SUM(itl.total_cost) as total_cost,
                COUNT(itl.id) as log_count,
                'USD' as currency
            FROM " . self::TABLE_TIME_LOGS . " itl
            JOIN projects p ON p.id = itl.project_id
            WHERE itl.user_id = ?
        ";

        $params = [$userId];

        if ($startDate) {
            $sql .= " AND itl.created_at >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND itl.created_at <= ?";
            $params[] = $endDate;
        }

        $sql .= " GROUP BY p.id, p.key, p.name
                  ORDER BY total_cost DESC";

        return Database::select($sql, $params) ?? [];
    }

    /**
     * Get top users by time spent (for managers/admins)
     */
    public function getTopUsersByTime(int $limit = 10, string $startDate = '', string $endDate = ''): array
    {
        $sql = "
            SELECT 
                u.id as user_id,
                u.display_name,
                u.first_name,
                u.last_name,
                u.avatar,
                u.email,
                SUM(itl.duration_seconds) as total_seconds,
                SUM(itl.total_cost) as total_cost,
                SUM(CASE WHEN itl.is_billable = 1 THEN itl.duration_seconds ELSE 0 END) as billable_seconds,
                SUM(CASE WHEN itl.is_billable = 1 THEN itl.total_cost ELSE 0 END) as billable_cost,
                COUNT(itl.id) as log_count,
                'USD' as currency
            FROM " . self::TABLE_TIME_LOGS . " itl
            JOIN users u ON u.id = itl.user_id
        ";

        $params = [];

        if ($startDate) {
            $sql .= " WHERE itl.created_at >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $sql .= (empty($params) ? " WHERE " : " AND ") . "itl.created_at <= ?";
            $params[] = $endDate;
        }

        $sql .= " GROUP BY u.id, u.display_name, u.first_name, u.last_name, u.avatar, u.email
                  ORDER BY total_seconds DESC
                  LIMIT ?";
        $params[] = $limit;

        return Database::select($sql, $params) ?? [];
    }
}
