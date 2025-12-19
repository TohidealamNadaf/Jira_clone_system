<?php declare(strict_types=1);

/**
 * Process Notification Retries
 * 
 * This script processes failed notifications and retries them.
 * Run from cron: */5 * * * * php /path/to/process-notification-retries.php
 * 
 * FIX 8: Created for production retry handling
 */

// Set up environment
define('BASE_PATH', dirname(__DIR__));
define('ENVIRONMENT', 'production');

// Load application
require_once BASE_PATH . '/bootstrap/app.php';

use App\Services\NotificationService;
use App\Helpers\NotificationLogger;

// Start processing
$startTime = microtime(true);

try {
    echo "[" . date('Y-m-d H:i:s') . "] Processing failed notifications...\n";
    
    // Process failed notifications with max 3 retries
    $processedCount = NotificationService::processFailedNotifications(maxRetries: 3);
    
    if ($processedCount > 0) {
        echo "[" . date('Y-m-d H:i:s') . "] Processed $processedCount failed notifications\n";
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] No failed notifications to process\n";
    }
    
    // Archive old logs if they're too large (> 10 MB)
    $logFile = storage_path('logs/notifications.log');
    if (file_exists($logFile) && filesize($logFile) > 10485760) { // 10 MB
        echo "[" . date('Y-m-d H:i:s') . "] Archiving old logs (size > 10 MB)...\n";
        $archivedCount = NotificationLogger::archiveOldLogs(daysOld: 30);
        echo "[" . date('Y-m-d H:i:s') . "] Archived $archivedCount old log files\n";
    }
    
    // Log execution time
    $duration = microtime(true) - $startTime;
    echo "[" . date('Y-m-d H:i:s') . "] Completed in " . round($duration, 3) . "s\n";
    
    exit(0);
    
} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    error_log("[NOTIFICATION RETRY CRON] Error: " . $e->getMessage(), 3, 
        storage_path('logs/notifications.log'));
    exit(1);
}
