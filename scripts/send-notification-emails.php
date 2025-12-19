<?php

declare(strict_types=1);

/**
 * Notification Email Sender Script
 * 
 * Cron Job: Run every 5 minutes
 * Usage: php scripts/send-notification-emails.php
 * 
 * Purpose:
 * - Process queued email notifications
 * - Retry failed sends
 * - Update delivery status
 * - Log all attempts
 */

// Autoload
require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Core\Database;
use App\Services\EmailService;
use App\Services\NotificationLogger;

// Configuration
$config = require __DIR__ . '/../config/config.php';
$db = new Database($config['database']);
$logger = new NotificationLogger();
$emailService = new EmailService($config, $logger);

// Start processing
$logger->info('EmailSender', 'Starting notification email delivery process');

try {
    // Get pending email notifications
    // Note: This assumes a notification_deliveries table exists
    // For now, we'll process from notification_preferences instead
    
    $pendingSql = "
        SELECT 
            n.id,
            n.user_id,
            n.type,
            n.related_id,
            u.email,
            u.full_name,
            np.email as email_enabled
        FROM notifications n
        JOIN users u ON n.user_id = u.id
        LEFT JOIN notification_preferences np 
            ON np.user_id = n.user_id 
            AND np.event_type = n.type 
            AND np.channel = 'email'
        WHERE n.read_at IS NULL 
        AND np.email = 1
        AND (n.sent_at IS NULL OR n.sent_at < DATE_SUB(NOW(), INTERVAL 1 HOUR))
        LIMIT 100
    ";

    $pending = $db->select($pendingSql, []);

    if (empty($pending)) {
        $logger->info('EmailSender', 'No pending emails to send');
        exit(0);
    }

    $logger->info('EmailSender', 'Processing ' . count($pending) . ' pending emails');

    $sent = 0;
    $failed = 0;

    foreach ($pending as $notification) {
        try {
            $subject = getEmailSubject($notification['type']);
            $body = getEmailBody($notification);

            if (empty($notification['email'])) {
                $logger->warning('EmailSender', 'No email address for user', [
                    'user_id' => $notification['user_id'],
                    'notification_id' => $notification['id'],
                ]);
                $failed++;
                continue;
            }

            // Send email
            $success = $emailService->send(
                $notification['email'],
                $subject,
                $body
            );

            if ($success) {
                // Mark as sent
                $updateSql = "UPDATE notifications SET sent_at = NOW(), delivered_at = NOW() WHERE id = ?";
                $db->update($updateSql, [$notification['id']]);
                
                $logger->info('EmailSender', 'Email delivered successfully', [
                    'notification_id' => $notification['id'],
                    'user_id' => $notification['user_id'],
                    'to' => $notification['email'],
                ]);
                $sent++;
            } else {
                // Log failure for retry
                $updateSql = "UPDATE notifications SET retry_count = retry_count + 1, last_retry_at = NOW() WHERE id = ?";
                $db->update($updateSql, [$notification['id']]);
                
                $logger->warning('EmailSender', 'Email delivery failed', [
                    'notification_id' => $notification['id'],
                    'to' => $notification['email'],
                ]);
                $failed++;
            }
        } catch (\Exception $e) {
            $logger->error('EmailSender', 'Exception processing notification', [
                'notification_id' => $notification['id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $failed++;
        }
    }

    // Summary
    $logger->info('EmailSender', 'Delivery batch complete', [
        'sent' => $sent,
        'failed' => $failed,
        'total' => count($pending),
    ]);

    // Output for logging
    echo "Email Delivery Report\n";
    echo "====================\n";
    echo "Sent: $sent\n";
    echo "Failed: $failed\n";
    echo "Total: " . count($pending) . "\n";
    echo "Time: " . date('Y-m-d H:i:s') . "\n";

} catch (\Exception $e) {
    $logger->error('EmailSender', 'Fatal error in email sender', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

exit(0);

// Helper function: Get email subject by type
function getEmailSubject(string $type): string
{
    $subjects = [
        'issue_assigned' => 'You\'ve been assigned an issue',
        'issue_commented' => 'New comment on an issue you\'re watching',
        'issue_status_changed' => 'Issue status has changed',
        'issue_created' => 'New issue created',
        'project_created' => 'New project created',
        'user_mentioned' => 'You\'ve been mentioned',
        'comment_replied' => 'Someone replied to your comment',
        'due_date_approaching' => 'Issue due date is approaching',
    ];

    return $subjects[$type] ?? 'Notification from Jira Clone';
}

// Helper function: Build email body by type
function getEmailBody(array $notification): string
{
    $type = $notification['type'];
    $relatedId = $notification['related_id'];
    $userName = $notification['full_name'];

    // For now, return a generic body
    // In production, would fetch issue/comment details and render template
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #0052CC; color: #fff; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background-color: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 8px 8px; }
            .footer { margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Notification</h2>
            </div>
            <div class='content'>
                <p>Hi $userName,</p>
                <p>You have a notification: $type</p>
                <p>Log in to Jira Clone to view details.</p>
            </div>
            <div class='footer'>
                <p>You received this email because you're watching this issue or project.</p>
            </div>
        </div>
    </body>
    </html>";

    return $html;
}
