<?php declare(strict_types=1);

namespace App\Helpers;

/**
 * Notification Logger Utility
 * 
 * Provides log viewing and analysis for the notification system
 * FIX 8: Created for production error visibility
 */
class NotificationLogger
{
    /**
     * Get recent notification logs
     * 
     * @param int $limit Number of log lines to return
     * @return array Array of log lines
     */
    public static function getRecentLogs(int $limit = 50): array
    {
        $logFile = storage_path('logs/notifications.log');

        if (!file_exists($logFile)) {
            return [];
        }

        try {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            return array_slice($lines, -$limit);
        } catch (\Exception $e) {
            error_log("Failed to read notification logs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get error statistics from log file
     * 
     * @return array Statistics including total errors, recent errors, and file size
     */
    public static function getErrorStats(): array
    {
        $logFile = storage_path('logs/notifications.log');

        if (!file_exists($logFile)) {
            return [
                'total_errors' => 0,
                'recent_errors' => [],
                'log_file_size' => 0,
                'success_count' => 0,
                'retry_count' => 0,
            ];
        }

        try {
            $content = file_get_contents($logFile);

            $errorCount = substr_count($content, '[NOTIFICATION ERROR]');
            $successCount = substr_count($content, '[NOTIFICATION]');
            $retryCount = substr_count($content, '[NOTIFICATION RETRY]');

            // Get recent errors by splitting the log content
            // We split at any [NOTIFICATION tag using a lookahead to keep the tag
            $parts = preg_split('/(?=\[NOTIFICATION)/', $content, -1, PREG_SPLIT_NO_EMPTY);
            $errors = [];
            foreach ($parts as $part) {
                if (strpos($part, '[NOTIFICATION ERROR]') === 0) {
                    // Extract until the next tag or newline (to avoid multi-line if any)
                    $errorSegment = preg_split('/\r?\n|(?=\[NOTIFICATION)/', $part, -1, PREG_SPLIT_NO_EMPTY);
                    $errorMsg = trim($errorSegment[0]);
                    if (!empty($errorMsg)) {
                        $errors[] = $errorMsg;
                    }
                }
            }

            return [
                'total_errors' => $errorCount,
                'recent_errors' => array_slice($errors, -10),
                'log_file_size' => filesize($logFile),
                'success_count' => $successCount,
                'retry_count' => $retryCount,
            ];
        } catch (\Exception $e) {
            error_log("Failed to get error stats: " . $e->getMessage());
            return [
                'total_errors' => 0,
                'recent_errors' => [],
                'log_file_size' => 0,
                'success_count' => 0,
                'retry_count' => 0,
            ];
        }
    }

    /**
     * Archive old logs (run via cron daily)
     * Moves current log to archive directory and deletes old archives
     * 
     * @param int $daysOld Delete archives older than this many days
     * @return int Number of old archives deleted
     */
    public static function archiveOldLogs(int $daysOld = 30): int
    {
        $logFile = storage_path('logs/notifications.log');
        $archiveDir = storage_path('logs/archive');

        if (!is_dir($archiveDir)) {
            @mkdir($archiveDir, 0755, true);
        }

        if (!file_exists($logFile)) {
            return 0;
        }

        try {
            // Archive current log
            $timestamp = date('Y-m-d_His');
            $archivePath = "$archiveDir/notifications_$timestamp.log";
            rename($logFile, $archivePath);

            // Clean up old archives
            $archived = 0;
            foreach (glob("$archiveDir/notifications_*.log") as $file) {
                if (time() - filemtime($file) > $daysOld * 86400) {
                    unlink($file);
                    $archived++;
                }
            }

            error_log(
                "Archived logs: moved to $archivePath, deleted $archived old archives",
                3,
                storage_path('logs/notifications.log')
            );

            return $archived;
        } catch (\Exception $e) {
            error_log("Failed to archive logs: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get log file size in human-readable format
     * 
     * @return string Formatted file size (e.g., "2.5 MB")
     */
    public static function getLogFileSizeFormatted(): string
    {
        $logFile = storage_path('logs/notifications.log');

        if (!file_exists($logFile)) {
            return '0 KB';
        }

        $size = filesize($logFile);
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $size / 1024;
        $unitIndex = 1;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 1) . ' ' . $units[$unitIndex];
    }

    /**
     * Check if log file exists and is writable
     * 
     * @return bool True if log file is operational
     */
    public static function isLogOperational(): bool
    {
        $logDir = storage_path('logs');

        if (!is_dir($logDir)) {
            return false;
        }

        return is_writable($logDir);
    }

    /**
     * Clear all logs (for testing or cleanup)
     * 
     * @return bool True if successful
     */
    public static function clearLogs(): bool
    {
        $logFile = storage_path('logs/notifications.log');

        try {
            if (file_exists($logFile)) {
                unlink($logFile);
            }
            return true;
        } catch (\Exception $e) {
            error_log("Failed to clear logs: " . $e->getMessage());
            return false;
        }
    }
}
