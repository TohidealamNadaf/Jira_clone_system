<?php
/**
 * Real-Time Notification Streaming Controller
 * Server-Sent Events (SSE) for live notifications
 * No page refresh needed
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

class NotificationStreamController extends Controller
{
    /**
     * Stream notifications in real-time using Server-Sent Events
     * Sends new notifications as they arrive
     */
    public function stream(): void
    {
        // Get current user
        $user = Session::user();
        if (!$user) {
            http_response_code(401);
            exit('Unauthorized');
        }

        // Set SSE headers
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        header('Access-Control-Allow-Origin: *');

        // Get last notification ID from client
        $lastId = $_GET['lastId'] ?? 0;
        
        // Send initial comment (keeps connection alive)
        echo ": SSE stream started\n\n";
        flush();

        // Stream loop - check for new notifications every 2 seconds for 30 minutes
        $startTime = time();
        $maxDuration = 1800; // 30 minutes

        while ((time() - $startTime) < $maxDuration) {
            // Check for new notifications
            $sql = "SELECT id, user_id, issue_id, type, data, created_at, read_at 
                    FROM notifications 
                    WHERE user_id = ? AND id > ? 
                    ORDER BY id ASC 
                    LIMIT 10";
            
            $notifications = Database::select($sql, [$user['id'], $lastId]);

            if (!empty($notifications)) {
                foreach ($notifications as $notification) {
                    // Update last ID
                    $lastId = $notification['id'];

                    // Prepare notification data
                    $data = json_decode($notification['data'], true) ?? [];
                    $message = $this->buildNotificationMessage($notification['type'], $data);

                    // Format SSE event
                    $eventData = [
                        'id' => $notification['id'],
                        'type' => $notification['type'],
                        'message' => $message,
                        'data' => $data,
                        'issueId' => $notification['issue_id'],
                        'timestamp' => $notification['created_at'],
                        'isRead' => !is_null($notification['read_at']),
                    ];

                    // Send SSE event
                    echo "id: " . $notification['id'] . "\n";
                    echo "event: notification\n";
                    echo "data: " . json_encode($eventData) . "\n\n";
                    flush();

                    // Mark as read
                    Database::update('notifications', 
                        ['read_at' => date('Y-m-d H:i:s')], 
                        'id = ?', 
                        [$notification['id']]
                    );
                }
            }

            // Sleep for 2 seconds before checking again
            sleep(2);
            
            // Send keep-alive comment
            echo ": keep-alive\n\n";
            flush();
        }

        // Stream ended
        echo "event: close\n";
        echo "data: Stream timeout\n\n";
        flush();
        exit;
    }

    /**
     * Build human-readable notification message
     */
    private function buildNotificationMessage(string $type, array $data): string
    {
        $messages = [
            'issue_assigned' => "🎯 Issue {$data['issueKey']} assigned to you",
            'issue_commented' => "💬 New comment on {$data['issueKey']}",
            'issue_status_changed' => "✅ {$data['issueKey']} status changed to {$data['newStatus']}",
            'issue_created' => "📝 New issue {$data['issueKey']} created",
            'issue_mentioned' => "👤 You were mentioned in {$data['issueKey']}",
            'issue_watched' => "👁️ {$data['issueKey']} is being watched",
            'comment_reply' => "↩️ Reply to your comment on {$data['issueKey']}",
            'worklog_added' => "⏱️ Work logged on {$data['issueKey']}",
        ];

        return $messages[$type] ?? "🔔 New notification";
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(): void
    {
        $user = Session::user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }

        $count = Database::selectValue(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND read_at IS NULL",
            [$user['id']]
        );

        $this->json(['unreadCount' => $count ?? 0]);
    }

    /**
     * Get recent notifications (for initial load)
     */
    public function getRecent(): void
    {
        $user = Session::user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }

        $limit = (int)($_GET['limit'] ?? 10);
        $notifications = Database::select(
            "SELECT id, user_id, issue_id, type, data, created_at, read_at 
             FROM notifications 
             WHERE user_id = ? 
             ORDER BY id DESC 
             LIMIT ?",
            [$user['id'], $limit]
        );

        $formatted = [];
        foreach ($notifications as $notif) {
            $data = json_decode($notif['data'], true) ?? [];
            $formatted[] = [
                'id' => $notif['id'],
                'type' => $notif['type'],
                'message' => $this->buildNotificationMessage($notif['type'], $data),
                'timestamp' => $notif['created_at'],
                'isRead' => !is_null($notif['read_at']),
                'issueKey' => $data['issueKey'] ?? null,
                'issueId' => $notif['issue_id'],
            ];
        }

        $this->json(['notifications' => $formatted]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $user = Session::user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }

        $notificationId = (int)$_POST['notificationId'];

        $notification = Database::selectOne(
            "SELECT id, user_id FROM notifications WHERE id = ?",
            [$notificationId]
        );

        if (!$notification || $notification['user_id'] != $user['id']) {
            $this->json(['error' => 'Not found'], 404);
            return;
        }

        Database::update('notifications',
            ['read_at' => date('Y-m-d H:i:s')],
            'id = ?',
            [$notificationId]
        );

        $this->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): void
    {
        $user = Session::user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }

        Database::update('notifications',
            ['read_at' => date('Y-m-d H:i:s')],
            'user_id = ? AND read_at IS NULL',
            [$user['id']]
        );

        $this->json(['success' => true]);
    }
}
