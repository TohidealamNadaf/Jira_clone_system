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
            $sql = "
                INSERT INTO " . self::TABLE_TIME_LOGS . " (
                    issue_id, user_id, project_id,
                    status, start_time, paused_at,
                    duration_seconds, paused_seconds,
                    user_rate_type, user_rate_amount,
                    total_cost, currency, is_billable
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";

            Database::execute($sql, [
                $issueId, $userId, $projectId,
                'running', $startTime->format('Y-m-d H:i:s'), $startTime->format('Y-m-d H:i:s'),
                0, 0,
                $userRate['rate_type'], $userRate['rate_amount'],
                0.00, $userRate['currency'], 1
            ]);

            $timeLogId = Database::lastInsertId();

            // Create active timer entry
            $sql = "
                INSERT INTO " . self::TABLE_ACTIVE_TIMERS . " (
                    user_id, issue_time_log_id, issue_id, project_id,
                    started_at, last_heartbeat
                ) VALUES (?, ?, ?, ?, ?, ?)
            ";

            Database::execute($sql, [
                $userId, $timeLogId, $issueId, $projectId,
                $startTime->format('Y-m-d H:i:s'), $startTime->format('Y-m-d H:i:s')
            ]);

            return [
                'success' => true,
                'time_log_id' => $timeLogId,
                'status' => 'running',
                'start_time' => $startTime->getTimestamp(),
                'elapsed_seconds' => 0,
                'cost' => 0.00
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

            // Update time log
            $sql = "
                UPDATE " . self::TABLE_TIME_LOGS . "
                SET status = 'paused', paused_at = ?, duration_seconds = ?, total_cost = ?
                WHERE id = ?
            ";

            $totalCost = $this->calculateCost(
                $elapsedSeconds,
                $timeLog['user_rate_type'],
                (float)$timeLog['user_rate_amount']
            );

            Database::execute($sql, [
                $pausedAt->format('Y-m-d H:i:s'),
                $elapsedSeconds,
                $totalCost,
                $timeLogId
            ]);

            // Remove from active timers
            Database::execute(
                "DELETE FROM " . self::TABLE_ACTIVE_TIMERS . " WHERE user_id = ?",
                [$userId]
            );

            return [
                'success' => true,
                'status' => 'paused',
                'elapsed_seconds' => $elapsedSeconds,
                'cost' => $totalCost
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
            // Get the most recent paused timer for this user
            $sql = "
                SELECT * FROM " . self::TABLE_TIME_LOGS . "
                WHERE user_id = ? AND status = 'paused'
                ORDER BY created_at DESC
                LIMIT 1
            ";

            $timeLog = Database::selectOne($sql, [$userId]);
            if (!$timeLog) {
                throw new Exception("No paused timer found for this user.");
            }

            $resumedAt = new DateTime();

            // Update time log back to running
            $sql = "
                UPDATE " . self::TABLE_TIME_LOGS . "
                SET status = 'running', resumed_at = ?
                WHERE id = ?
            ";

            Database::execute($sql, [
                $resumedAt->format('Y-m-d H:i:s'),
                $timeLog['id']
            ]);

            // Create new active timer
            $sql = "
                INSERT INTO " . self::TABLE_ACTIVE_TIMERS . " (
                    user_id, issue_time_log_id, issue_id, project_id,
                    started_at, last_heartbeat
                ) VALUES (?, ?, ?, ?, ?, ?)
            ";

            Database::execute($sql, [
                $userId, $timeLog['id'], $timeLog['issue_id'], $timeLog['project_id'],
                $resumedAt->format('Y-m-d H:i:s'), $resumedAt->format('Y-m-d H:i:s')
            ]);

            return [
                'success' => true,
                'time_log_id' => $timeLog['id'],
                'status' => 'running',
                'elapsed_seconds' => (int)$timeLog['duration_seconds'],
                'cost' => (float)$timeLog['total_cost']
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
            $sql = "
                UPDATE " . self::TABLE_TIME_LOGS . "
                SET status = 'stopped', end_time = ?, duration_seconds = ?,
                    total_cost = ?, description = ?
                WHERE id = ?
            ";

            Database::execute($sql, [
                $stoppedAt->format('Y-m-d H:i:s'),
                $totalElapsedSeconds,
                $totalCost,
                $description ?? '',
                $timeLogId
            ]);

            // Remove from active timers
            Database::execute(
                "DELETE FROM " . self::TABLE_ACTIVE_TIMERS . " WHERE user_id = ?",
                [$userId]
            );

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
            $sql = "
                UPDATE " . self::TABLE_USER_RATES . "
                SET is_active = 0
                WHERE user_id = ? AND rate_type = ?
            ";
            Database::execute($sql, [$userId, $rateType]);

            // Create new rate
            $sql = "
                INSERT INTO " . self::TABLE_USER_RATES . " (
                    user_id, rate_type, rate_amount, currency, is_active, effective_from
                ) VALUES (?, ?, ?, ?, ?, ?)
            ";

            $today = (new DateTime())->format('Y-m-d');
            Database::execute($sql, [
                $userId, $rateType, $rateAmount, $currency, 1, $today
            ]);

            $rateId = Database::lastInsertId();

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
            $sql = "
                UPDATE project_budgets
                SET total_cost = total_cost + ?
                WHERE project_id = ?
            ";

            Database::execute($sql, [$additionalCost, $projectId]);

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
            $sql = "
                INSERT INTO budget_alerts (
                    project_budget_id, project_id, alert_type,
                    threshold_percentage, actual_percentage,
                    cost_at_alert, remaining_budget_at_alert
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ";

            Database::execute($sql, [
                $budget['id'], $projectId, $alertType,
                $budget['alert_threshold'], $percentageUsed,
                $budget['total_cost'],
                $budget['total_budget'] - $budget['total_cost']
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
}
