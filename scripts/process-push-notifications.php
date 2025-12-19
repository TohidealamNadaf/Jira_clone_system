<?php declare(strict_types=1);

/**
 * Push Notification Background Worker
 * Process queued push notifications for delivery
 * 
 * Run via cron: */5 * * * * php /path/to/scripts/process-push-notifications.php
 * 
 * This script:
 * 1. Fetches pending push notifications from queue
 * 2. Sends them via Firebase Cloud Messaging
 * 3. Tracks delivery status and retries
 * 4. Logs all activity for monitoring
 */

require __DIR__ . '/../bootstrap/app.php';

use App\Services\PushService;
use App\Core\Database;

// Configuration
const MAX_RETRIES = 3;
const BATCH_SIZE = 50;
const LOCK_TIMEOUT = 300; // 5 minutes

// Lock mechanism to prevent concurrent processing
$lockFile = storage_path('push-worker.lock');
$lockTime = @filemtime($lockFile);

if ($lockTime && (time() - $lockTime) < LOCK_TIMEOUT) {
    error_log('[PUSH WORKER] Already running (lock file recent)', 3,
        storage_path('logs/notifications.log'));
    exit(0);
}

// Create lock
file_put_contents($lockFile, time());

try {
    $config = require(__DIR__ . '/../config/config.php');
    $pushService = new PushService($config);
    
    // Check if FCM is configured
    if (!$pushService->isConfigured()) {
        error_log('[PUSH WORKER] FCM not configured, skipping', 3,
            storage_path('logs/notifications.log'));
        exit(0);
    }
    
    // Fetch pending push notifications
    $pending = Database::select(
        'SELECT nd.id, nd.notification_id, nd.retry_count 
        FROM notification_deliveries nd 
        WHERE nd.channel = "push" 
        AND nd.status = "pending" 
        AND nd.retry_count < ' . (int)MAX_RETRIES . '
        ORDER BY nd.created_at ASC 
        LIMIT ' . (int)BATCH_SIZE,
        []
    );
    
    if (empty($pending)) {
        error_log('[PUSH WORKER] No pending notifications to process', 3,
            storage_path('logs/notifications.log'));
        exit(0);
    }
    
    $processed = 0;
    $successful = 0;
    $failed = 0;
    
    foreach ($pending as $delivery) {
        try {
            // Get notification with user details
            $notification = Database::selectOne(
                'SELECT n.id, n.user_id, n.title, n.message, n.type, n.action_url, 
                        u.email
                FROM notifications n 
                JOIN users u ON n.user_id = u.id 
                WHERE n.id = ?',
                [$delivery['notification_id']]
            );
            
            if (!$notification) {
                // Notification deleted, mark delivery as failed
                Database::update(
                    'notification_deliveries',
                    [
                        'status' => 'failed',
                        'error_message' => 'Notification not found',
                        'retry_count' => $delivery['retry_count'] + 1,
                    ],
                    'id = ?',
                    [$delivery['id']]
                );
                $failed++;
                continue;
            }
            
            // Prepare push notification data
            $pushData = [
                'notification_id' => (string) $notification['id'],
                'type' => $notification['type'],
            ];
            
            if ($notification['action_url']) {
                $pushData['action_url'] = $notification['action_url'];
            }
            
            // Send push notification
            $sent = $pushService->sendToUser(
                $notification['user_id'],
                $notification['title'],
                $notification['message'],
                $pushData
            );
            
            // Update delivery status
            if ($sent) {
                Database::update(
                    'notification_deliveries',
                    [
                        'status' => 'delivered',
                        'retry_count' => $delivery['retry_count'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    'id = ?',
                    [$delivery['id']]
                );
                $successful++;
                
                error_log(sprintf(
                    '[PUSH WORKER] Delivered: delivery_id=%d, notification_id=%d, user=%d, attempt=%d',
                    $delivery['id'],
                    $delivery['notification_id'],
                    $notification['user_id'],
                    $delivery['retry_count'] + 1
                ), 3, storage_path('logs/notifications.log'));
                
            } else {
                // Mark for retry
                Database::update(
                    'notification_deliveries',
                    [
                        'status' => 'pending',
                        'retry_count' => $delivery['retry_count'] + 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    'id = ?',
                    [$delivery['id']]
                );
                $failed++;
                
                error_log(sprintf(
                    '[PUSH WORKER] Failed: delivery_id=%d, notification_id=%d, user=%d, attempt=%d',
                    $delivery['id'],
                    $delivery['notification_id'],
                    $notification['user_id'],
                    $delivery['retry_count'] + 1
                ), 3, storage_path('logs/notifications.log'));
            }
            
            $processed++;
            
        } catch (\Exception $e) {
            error_log(sprintf(
                '[PUSH WORKER ERROR] Processing delivery %d: %s',
                $delivery['id'],
                $e->getMessage()
            ), 3, storage_path('logs/notifications.log'));
            $failed++;
            $processed++;
        }
    }
    
    // Log summary
    error_log(sprintf(
        '[PUSH WORKER] Summary: processed=%d, successful=%d, failed=%d, pending=%d',
        $processed,
        $successful,
        $failed,
        max(0, count($pending) - $successful)
    ), 3, storage_path('logs/notifications.log'));
    
    // Clean up very old failed deliveries (older than 7 days)
    $oldFailed = Database::delete(
        'notification_deliveries',
        'channel = ? AND status = ? AND retry_count >= ? AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)',
        ['push', 'failed', MAX_RETRIES]
    );
    
    if ($oldFailed) {
        error_log(sprintf(
            '[PUSH WORKER] Cleaned up %d old failed deliveries',
            $oldFailed
        ), 3, storage_path('logs/notifications.log'));
    }
    
} catch (\Exception $e) {
    error_log(sprintf(
        '[PUSH WORKER FATAL ERROR] %s at %s:%d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ), 3, storage_path('logs/notifications.log'));
} finally {
    // Remove lock
    @unlink($lockFile);
}
