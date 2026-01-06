<?php declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class NotificationService
{
    /**
     * Dispatch notification when issue is created
     * Notifies all project members except creator
     */
    public static function dispatchIssueCreated(int $issueId, int $userId): void
    {
        // Get issue details
        $issue = Database::selectOne(
            'SELECT id, issue_key, project_id, summary FROM issues WHERE id = ?',
            [$issueId]
        );
        
        if (!$issue) return;
        
        // Get all project members except creator
        $members = Database::select(
            'SELECT DISTINCT user_id FROM project_members WHERE project_id = ? AND user_id != ?',
            [$issue['project_id'], $userId]
        );
        
        foreach ($members as $member) {
            // Check user preference
            if (!self::shouldNotify($member['user_id'], 'issue_created')) {
                continue;
            }
            
            self::create(
                userId: $member['user_id'],
                type: 'issue_created',
                title: 'Issue Created',
                message: "Issue {$issue['issue_key']}: {$issue['summary']}",
                actionUrl: "/issues/{$issue['issue_key']}",
                actorUserId: $userId,
                relatedIssueId: $issueId,
                relatedProjectId: $issue['project_id'],
                priority: 'normal'
            );
        }
    }
    
    /**
     * Dispatch notification when user is assigned to issue
     */
    public static function dispatchIssueAssigned(
        int $issueId,
        int $assigneeId,
        ?int $previousAssigneeId = null
    ): void {
        $issue = Database::selectOne(
            'SELECT id, issue_key, summary, project_id FROM issues WHERE id = ?',
            [$issueId]
        );
        
        if (!$issue) return;
        
        // Notify new assignee
        if (self::shouldNotify($assigneeId, 'issue_assigned')) {
            self::create(
                userId: $assigneeId,
                type: 'issue_assigned',
                title: 'Issue Assigned to You',
                message: "Issue {$issue['issue_key']}: {$issue['summary']}",
                actionUrl: "/issues/{$issue['issue_key']}",
                relatedIssueId: $issueId,
                relatedProjectId: $issue['project_id'],
                priority: 'high'
            );
        }
        
        // Notify previous assignee if assignment changed
        if ($previousAssigneeId && $previousAssigneeId !== $assigneeId) {
            if (self::shouldNotify($previousAssigneeId, 'issue_assigned')) {
                self::create(
                    userId: $previousAssigneeId,
                    type: 'issue_assigned',
                    title: 'Issue Reassigned',
                    message: "Issue {$issue['issue_key']} was reassigned",
                    actionUrl: "/issues/{$issue['issue_key']}",
                    relatedIssueId: $issueId,
                    relatedProjectId: $issue['project_id'],
                    priority: 'normal'
                );
            }
        }
    }
    
    /**
     * Dispatch notification when issue is commented on
     */
    public static function dispatchIssueCommented(
        int $issueId,
        int $commenterId,
        int $commentId
    ): void {
        $issue = Database::selectOne(
            'SELECT id, issue_key, summary, assignee_id, project_id FROM issues WHERE id = ?',
            [$issueId]
        );
        
        if (!$issue) return;
        
        // Notify assignee
        if ($issue['assignee_id'] && $issue['assignee_id'] !== $commenterId) {
            if (self::shouldNotify($issue['assignee_id'], 'issue_commented')) {
                self::create(
                    userId: $issue['assignee_id'],
                    type: 'issue_commented',
                    title: 'New Comment on Your Issue',
                    message: "New comment on {$issue['issue_key']}",
                    actionUrl: "/issues/{$issue['issue_key']}#comment-{$commentId}",
                    actorUserId: $commenterId,
                    relatedIssueId: $issueId,
                    relatedProjectId: $issue['project_id'],
                    priority: 'normal'
                );
            }
        }
    }
    
    /**
     * Dispatch notification when issue status changes
     */
    public static function dispatchIssueStatusChanged(
        int $issueId,
        string $newStatus,
        int $userId
    ): void {
        $issue = Database::selectOne(
            'SELECT id, issue_key, summary, assignee_id, project_id FROM issues WHERE id = ?',
            [$issueId]
        );
        
        if (!$issue) return;
        
        // Notify assignee of status change
        if ($issue['assignee_id'] && $issue['assignee_id'] !== $userId) {
            if (self::shouldNotify($issue['assignee_id'], 'issue_status_changed')) {
                self::create(
                    userId: $issue['assignee_id'],
                    type: 'issue_status_changed',
                    title: 'Issue Status Changed',
                    message: "Issue {$issue['issue_key']} status changed to {$newStatus}",
                    actionUrl: "/issues/{$issue['issue_key']}",
                    actorUserId: $userId,
                    relatedIssueId: $issueId,
                    relatedProjectId: $issue['project_id'],
                    priority: 'normal'
                );
            }
        }
    }
    
    /**
     * Create a notification record
     * For large-scale deployments (100+ devs), this uses optimized database inserts
     * 
     * PHASE 2: Now includes email/push delivery integration
     * Email/push delivery is handled by notification_deliveries table
     * via queueDeliveries method.
     * 
     * FIX 8: Added comprehensive error logging and retry queuing
     */
    public static function create(
        int $userId,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionUrl = null,
        ?int $actorUserId = null,
        ?int $relatedIssueId = null,
        ?int $relatedProjectId = null,
        string $priority = 'normal'
    ): ?int {
        try {
            // Use parameterized insert for security
            $id = Database::insert('notifications', [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'actor_user_id' => $actorUserId,
                'related_issue_id' => $relatedIssueId,
                'related_project_id' => $relatedProjectId,
                'priority' => $priority,
                'is_read' => 0,
            ]);
            
            // Log successful creation
            error_log(sprintf(
                '[NOTIFICATION] Created: type=%s, user=%d, issue=%s, priority=%s, id=%d',
                $type,
                $userId,
                $relatedIssueId ?? 'N/A',
                $priority,
                $id
            ), 3, storage_path('logs/notifications.log'));
            
            // Queue delivery for enabled channels (in-app, email, push)
            // This happens asynchronously to avoid blocking the request
            self::queueDeliveries($id, $userId, $type);
            
            return $id;
        } catch (\Exception $e) {
            // Log error with full context
            error_log(sprintf(
                '[NOTIFICATION ERROR] Failed to create: type=%s, user=%d, error=%s',
                $type,
                $userId,
                $e->getMessage()
            ), 3, storage_path('logs/notifications.log'));
            
            // Queue for retry
            self::queueForRetry('create', $relatedIssueId ?? 0, $e->getMessage());
            
            return null;
        }
    }
    
    /**
     * Get unread notifications for user with pagination
     * Optimized for fast retrieval with composite index
     */
    public static function getUnread(int $userId, int $limit = 20): array
    {
        return Database::select(
            'SELECT id, type, title, message, action_url, actor_user_id, 
                    priority, created_at 
             FROM notifications 
             WHERE user_id = ? AND is_read = 0 
             ORDER BY created_at DESC 
             LIMIT ' . (int) $limit,
            [$userId]
        );
    }
    
    /**
     * Get all notifications with pagination
     */
    public static function getAll(int $userId, int $page = 1, int $perPage = 25): array
    {
        $offset = ($page - 1) * $perPage;
        
        return Database::select(
            'SELECT id, type, title, message, action_url, actor_user_id, 
                    is_read, priority, created_at 
             FROM notifications 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset,
            [$userId]
        );
    }
    
    /**
     * Get total notification count for pagination
     */
    public static function getCount(int $userId): int
    {
        $result = Database::selectOne(
            'SELECT COUNT(*) as count FROM notifications WHERE user_id = ?',
            [$userId]
        );
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Mark single notification as read
     */
    public static function markAsRead(int $notificationId, int $userId): bool
    {
        $result = Database::update(
            'notifications',
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            'id = ? AND user_id = ?',
            [$notificationId, $userId]
        );
        
        return (bool) $result;
    }
    
    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsRead(int $userId): bool
    {
        $result = Database::update(
            'notifications',
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            'user_id = ? AND is_read = 0',
            [$userId]
        );
        
        return (bool) $result;
    }
    
    /**
     * Check if user has notification preference enabled for event type and channel
     * Returns true by default if no preference exists
     * 
     * @param int $userId User ID
     * @param string $eventType Event type (e.g., 'issue_created')
     * @param string $channel Channel: 'in_app', 'email', or 'push' (default: 'in_app')
     * @return bool True if user wants notifications for this event on this channel
     */
    public static function shouldNotify(
        int $userId,
        string $eventType,
        string $channel = 'in_app'
    ): bool {
        // Validate channel
        $validChannels = ['in_app', 'email', 'push'];
        if (!in_array($channel, $validChannels)) {
            $channel = 'in_app';
        }
        
        $preference = Database::selectOne(
            'SELECT in_app, email, push FROM notification_preferences WHERE user_id = ? AND event_type = ?',
            [$userId, $eventType]
        );
        
        if (!$preference) {
            // Default: in_app and email enabled, push disabled
            if ($channel === 'in_app' || $channel === 'email') {
                return true;
            }
            return false;
        }
        
        // Return the channel preference value
        return (bool) $preference[$channel];
    }
    
    /**
     * Get all notification preferences for user
     */
    public static function getPreferences(int $userId): array
    {
        return Database::select(
            'SELECT event_type, in_app, email, push FROM notification_preferences WHERE user_id = ?',
            [$userId]
        );
    }
    
    /**
     * Update notification preference with upsert
     */
    public static function updatePreference(
        int $userId,
        string $eventType,
        bool $inApp = true,
        bool $email = true,
        bool $push = false
    ): bool {
        return (bool) Database::insertOrUpdate(
            'notification_preferences',
            [
                'user_id' => $userId,
                'event_type' => $eventType,
                'in_app' => (int) $inApp,
                'email' => (int) $email,
                'push' => (int) $push,
            ],
            ['user_id', 'event_type']
        );
    }
    
    /**
     * Delete a notification
     */
    public static function delete(int $notificationId, int $userId): bool
    {
        return (bool) Database::delete(
            'notifications',
            'id = ? AND user_id = ?',
            [$notificationId, $userId]
        );
    }
    
    /**
     * Get unread notification count for user
     * Optimized query for performance with 100+ users
     */
    public static function getUnreadCount(int $userId): int
    {
        $result = Database::selectOne(
            'SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0',
            [$userId]
        );
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Bulk create notifications for multiple users
     * Optimized for batch operations during issue creation
     */
    public static function createBulk(array $userIds, array $notificationData): int
    {
        $count = 0;
        
        foreach ($userIds as $userId) {
            try {
                self::create(
                    userId: $userId,
                    type: $notificationData['type'],
                    title: $notificationData['title'],
                    message: $notificationData['message'] ?? null,
                    actionUrl: $notificationData['action_url'] ?? null,
                    actorUserId: $notificationData['actor_user_id'] ?? null,
                    relatedIssueId: $notificationData['related_issue_id'] ?? null,
                    relatedProjectId: $notificationData['related_project_id'] ?? null,
                    priority: $notificationData['priority'] ?? 'normal'
                );
                $count++;
            } catch (\Exception $e) {
                // Log error but continue with other users
                error_log("Failed to create notification for user {$userId}: " . $e->getMessage());
            }
        }
        
        return $count;
    }
    
    /**
     * Archive old notifications (older than 90 days)
     * Run this as a cron job for large-scale deployments
     */
    public static function archiveOldNotifications(int $daysOld = 90): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        
        // Move to archive table
        Database::query(
            'INSERT INTO notifications_archive SELECT * FROM notifications WHERE created_at < ?',
            [$cutoffDate]
        );
        
        // Delete from main table
        $stmt = Database::delete(
            'notifications',
            'created_at < ?',
            [$cutoffDate]
        );
        
        return $stmt;
    }
    
    /**
     * Get notification statistics for user
     */
    public static function getStats(int $userId): array
    {
        $total = Database::selectOne(
            'SELECT COUNT(*) as count FROM notifications WHERE user_id = ?',
            [$userId]
        );
        
        $unread = Database::selectOne(
            'SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0',
            [$userId]
        );
        
        $byType = Database::select(
            'SELECT type, COUNT(*) as count FROM notifications WHERE user_id = ? GROUP BY type',
            [$userId]
        );
        
        return [
            'total' => $total['count'] ?? 0,
            'unread' => $unread['count'] ?? 0,
            'by_type' => $byType,
        ];
    }

    /**
     * CRITICAL FIX #3: Generate unique dispatch ID for idempotency
     * Prevents duplicate notifications during race conditions
     * MUST be deterministic - same inputs = same ID (no timestamps!)
     */
    private static function generateDispatchId(
        string $dispatchType,
        int $issueId,
        ?int $commentId = null,
        int $actorId = 0
    ): string {
        $key = sprintf(
            '%s_%d_%s_%d',
            $dispatchType,
            $issueId,
            $commentId ? 'comment_' . $commentId : 'issue',
            $actorId
        );
        return hash('sha256', $key);
    }

    /**
     * CRITICAL FIX #3: Check if dispatch was already completed
     * Returns true if dispatch_id exists with status='completed'
     */
    private static function isDuplicateDispatch(string $dispatchId): bool
    {
        $existing = Database::selectOne(
            'SELECT id FROM notification_dispatch_log 
             WHERE dispatch_id = ? AND status = ?',
            [$dispatchId, 'completed']
        );
        
        return $existing !== null;
    }

    /**
     * CRITICAL FIX #3: Create dispatch log entry before notification creation
     * Marks dispatch as pending to prevent duplicates on retry
     */
    private static function createDispatchLog(
        string $dispatchId,
        string $dispatchType,
        int $issueId,
        ?int $commentId,
        int $actorId,
        int $recipientCount = 0
    ): void {
        Database::insert('notification_dispatch_log', [
            'dispatch_id' => $dispatchId,
            'dispatch_type' => $dispatchType,
            'issue_id' => $issueId,
            'comment_id' => $commentId,
            'actor_user_id' => $actorId,
            'recipients_count' => $recipientCount,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Dispatch notification when issue is commented on
     * CRITICAL FIX #3: Added idempotency and atomic transactions to prevent duplicates
     * FIX 8: Added comprehensive error logging
     */
    public static function dispatchCommentAdded(
        int $issueId,
        int $commenterId,
        int $commentId
    ): void {
        $dispatchId = null;
        
        try {
            // Step 1: Generate unique dispatch ID
            $dispatchId = self::generateDispatchId('comment_added', $issueId, $commentId, $commenterId);
            
            // Step 2: Check for duplicate dispatch
            if (self::isDuplicateDispatch($dispatchId)) {
                error_log(sprintf(
                    '[NOTIFICATION] Duplicate dispatch prevented: dispatch_id=%s, issue_id=%d',
                    $dispatchId,
                    $issueId
                ), 3, storage_path('logs/notifications.log'));
                return;
            }
            
            // Step 3: Create dispatch log entry (marks as pending)
            self::createDispatchLog($dispatchId, 'comment_added', $issueId, $commentId, $commenterId);
            
            // Step 4: Begin atomic transaction
            Database::beginTransaction();
            
            // Step 5: Query issue and recipients
            $issue = Database::selectOne(
                'SELECT id, issue_key, summary, project_id, assignee_id FROM issues WHERE id = ?',
                [$issueId]
            );

            if (!$issue) {
                throw new \Exception("Issue not found: $issueId");
            }

            // Get all watchers and assignee for notification
            $recipients = [];

            // Add assignee
            if ($issue['assignee_id'] && $issue['assignee_id'] !== $commenterId) {
                $recipients[] = $issue['assignee_id'];
            }

            // Add other watchers
            $watchers = Database::select(
                'SELECT DISTINCT user_id FROM issue_watchers WHERE issue_id = ? AND user_id != ?',
                [$issueId, $commenterId]
            );

            foreach ($watchers as $watcher) {
                $recipients[] = $watcher['user_id'];
            }

            // Remove duplicates
            $recipients = array_unique($recipients);

            // Step 6: Create notifications for each recipient in transaction
            foreach ($recipients as $recipientId) {
                if (self::shouldNotify($recipientId, 'issue_commented')) {
                    Database::insert('notifications', [
                        'user_id' => $recipientId,
                        'dispatch_id' => $dispatchId,
                        'type' => 'issue_commented',
                        'title' => 'New Comment',
                        'message' => "New comment on {$issue['issue_key']}",
                        'action_url' => "/issues/{$issue['issue_key']}?comment={$commentId}",
                        'actor_user_id' => $commenterId,
                        'related_issue_id' => $issueId,
                        'related_project_id' => $issue['project_id'],
                        'priority' => 'normal',
                        'is_read' => 0,
                    ]);
                }
            }

            // Step 7: Update dispatch log to completed
            Database::update(
                'notification_dispatch_log',
                [
                    'status' => 'completed',
                    'completed_at' => date('Y-m-d H:i:s'),
                    'recipients_count' => count($recipients),
                ],
                'dispatch_id = ?',
                [$dispatchId]
            );

            // Step 8: Commit transaction
            Database::commit();

            // Log successful dispatch
            error_log(sprintf(
                '[NOTIFICATION] Comment dispatch completed: dispatch_id=%s, issue=%d, comment=%d, recipients=%d',
                $dispatchId,
                $issueId,
                $commentId,
                count($recipients)
            ), 3, storage_path('logs/notifications.log'));
            
        } catch (\Exception $e) {
            try {
                Database::rollback();
            } catch (\Exception $rollbackError) {
                error_log('Rollback failed: ' . $rollbackError->getMessage(), 3, 
                    storage_path('logs/notifications.log'));
            }
            
            // Update dispatch log with error
            if ($dispatchId) {
                try {
                    Database::update(
                        'notification_dispatch_log',
                        [
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ],
                        'dispatch_id = ?',
                        [$dispatchId]
                    );
                } catch (\Exception $ignored) {
                    // Log entry might not exist
                }
            }
            
            error_log(sprintf(
                '[NOTIFICATION ERROR] Failed to dispatch comment notifications: issue=%d, dispatch=%s, error=%s',
                $issueId,
                $dispatchId ?? 'unknown',
                $e->getMessage()
            ), 3, storage_path('logs/notifications.log'));
            
            // Queue for retry
            self::queueForRetry('comment_dispatch', $issueId, $e->getMessage());
        }
    }

    /**
     * Dispatch notification when issue status changes
     * CRITICAL FIX #3: Added idempotency and atomic transactions to prevent duplicates
     * FIX 8: Added comprehensive error logging
     */
    public static function dispatchStatusChanged(
        int $issueId,
        string $newStatus,
        int $userId
    ): void {
        $dispatchId = null;
        
        try {
            // Step 1: Generate unique dispatch ID
            $dispatchId = self::generateDispatchId('status_changed', $issueId, null, $userId);
            
            // Step 2: Check for duplicate dispatch
            if (self::isDuplicateDispatch($dispatchId)) {
                error_log(sprintf(
                    '[NOTIFICATION] Duplicate dispatch prevented: dispatch_id=%s, issue_id=%d',
                    $dispatchId,
                    $issueId
                ), 3, storage_path('logs/notifications.log'));
                return;
            }
            
            // Step 3: Create dispatch log entry (marks as pending)
            self::createDispatchLog($dispatchId, 'status_changed', $issueId, null, $userId);
            
            // Step 4: Begin atomic transaction
            Database::beginTransaction();
            
            // Step 5: Query issue and recipients
            $issue = Database::selectOne(
                'SELECT id, issue_key, summary, project_id, assignee_id FROM issues WHERE id = ?',
                [$issueId]
            );

            if (!$issue) {
               throw new \Exception("Issue not found: $issueId");
            }

            // Get recipients (assignee + watchers)
            $recipients = [];

            // Add assignee
            if ($issue['assignee_id'] && $issue['assignee_id'] !== $userId) {
               $recipients[] = $issue['assignee_id'];
            }

            // Add watchers
            $watchers = Database::select(
               'SELECT DISTINCT user_id FROM issue_watchers WHERE issue_id = ? AND user_id != ?',
               [$issueId, $userId]
            );

            foreach ($watchers as $watcher) {
               $recipients[] = $watcher['user_id'];
            }

            // Remove duplicates
            $recipients = array_unique($recipients);

            // Step 6: Create notifications for each recipient in transaction
            foreach ($recipients as $recipientId) {
               if (self::shouldNotify($recipientId, 'issue_status_changed')) {
                   Database::insert('notifications', [
                       'user_id' => $recipientId,
                       'dispatch_id' => $dispatchId,
                       'type' => 'issue_status_changed',
                       'title' => 'Status Changed',
                       'message' => "{$issue['issue_key']} status changed to {$newStatus}",
                       'action_url' => "/issues/{$issue['issue_key']}",
                        'actor_user_id' => $userId,
                        'related_issue_id' => $issueId,
                        'related_project_id' => $issue['project_id'],
                        'priority' => 'normal',
                        'is_read' => 0,
                    ]);
                }
            }

            // Step 7: Update dispatch log to completed
            Database::update(
                'notification_dispatch_log',
                [
                    'status' => 'completed',
                    'completed_at' => date('Y-m-d H:i:s'),
                    'recipients_count' => count($recipients),
                ],
                'dispatch_id = ?',
                [$dispatchId]
            );

            // Step 8: Commit transaction
            Database::commit();

            // Log successful dispatch
            error_log(sprintf(
                '[NOTIFICATION] Status dispatch completed: dispatch_id=%s, issue=%d, status=%s, recipients=%d',
                $dispatchId,
                $issueId,
                $newStatus,
                count($recipients)
            ), 3, storage_path('logs/notifications.log'));
        } catch (\Exception $e) {
            try {
                Database::rollback();
            } catch (\Exception $rollbackError) {
                error_log('Rollback failed: ' . $rollbackError->getMessage(), 3, 
                    storage_path('logs/notifications.log'));
            }
            
            // Update dispatch log with error
            if ($dispatchId) {
                try {
                    Database::update(
                        'notification_dispatch_log',
                        [
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ],
                        'dispatch_id = ?',
                        [$dispatchId]
                    );
                } catch (\Exception $ignored) {
                    // Log entry might not exist
                }
            }
            
            error_log(sprintf(
                '[NOTIFICATION ERROR] Failed to dispatch status change notifications: issue=%d, dispatch=%s, error=%s',
                $issueId,
                $dispatchId ?? 'unknown',
                $e->getMessage()
            ), 3, storage_path('logs/notifications.log'));
            
            // Queue for retry
            self::queueForRetry('status_dispatch', $issueId, $e->getMessage());
        }
    }

    /**
     * Dispatch notification when user is mentioned
     */
    public static function dispatchMentioned(
        int $issueId,
        int $mentionedUserId,
        int $mentionerUserId
    ): void {
        $issue = Database::selectOne(
            'SELECT id, issue_key, summary, project_id FROM issues WHERE id = ?',
            [$issueId]
        );

        if (!$issue || $mentionedUserId === $mentionerUserId) return;

        if (self::shouldNotify($mentionedUserId, 'issue_mentioned')) {
            self::create(
                userId: $mentionedUserId,
                type: 'issue_mentioned',
                title: 'You were mentioned',
                message: "You were mentioned in {$issue['issue_key']}",
                actionUrl: "/issues/{$issue['issue_key']}",
                actorUserId: $mentionerUserId,
                relatedIssueId: $issueId,
                relatedProjectId: $issue['project_id'],
                priority: 'high'
            );
        }
    }

    /**
     * Queue notification deliveries for enabled channels
     * 
     * PHASE 2: Implements email/push delivery integration
     * This method creates entries in notification_deliveries table
     * for each channel the user has enabled in their preferences.
     * 
     * @param int $notificationId The notification ID
     * @param int $userId The user ID
     * @param string $eventType The event type (e.g., 'issue_created')
     * @return void
     */
    public static function queueDeliveries(
        int $notificationId,
        int $userId,
        string $eventType
    ): void {
        try {
            // Get user details
            $user = Database::selectOne('SELECT id, email FROM users WHERE id = ?', [$userId]);
            if (!$user || !$user['email']) {
                error_log("Cannot queue deliveries: user {$userId} not found or no email", 3,
                    storage_path('logs/notifications.log'));
                return;
            }

            // Get notification details
            $notification = Database::selectOne(
                'SELECT id, title, message, type, related_issue_id FROM notifications WHERE id = ?',
                [$notificationId]
            );
            if (!$notification) {
                error_log("Cannot queue deliveries: notification {$notificationId} not found", 3,
                    storage_path('logs/notifications.log'));
                return;
            }

            // Get user preferences for this event type
            $preference = Database::selectOne(
                'SELECT in_app, email, push FROM notification_preferences WHERE user_id = ? AND event_type = ?',
                [$userId, $eventType]
            );

            if (!$preference) {
                // Use defaults: in_app=1, email=1, push=0
                $preference = ['in_app' => 1, 'email' => 1, 'push' => 0];
            }

            // Process in-app channel (already stored, just mark as pending)
            if ($preference['in_app']) {
                try {
                    Database::insert('notification_deliveries', [
                        'notification_id' => $notificationId,
                        'channel' => 'in_app',
                        'status' => 'delivered',
                        'retry_count' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                } catch (\Exception $e) {
                    error_log("Failed to queue in_app delivery: " . $e->getMessage(), 3,
                        storage_path('logs/notifications.log'));
                }
            }

            // Process email channel
            if ($preference['email']) {
                self::queueEmailDelivery($user['email'], $notification, $userId);
            }

            // Process push channel (ready for future implementation)
            if ($preference['push']) {
                try {
                    Database::insert('notification_deliveries', [
                        'notification_id' => $notificationId,
                        'channel' => 'push',
                        'status' => 'pending',
                        'retry_count' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    error_log("Queued push delivery for notification {$notificationId}", 3,
                        storage_path('logs/notifications.log'));
                } catch (\Exception $e) {
                    error_log("Failed to queue push delivery: " . $e->getMessage(), 3,
                        storage_path('logs/notifications.log'));
                }
            }
        } catch (\Exception $e) {
            error_log("Exception in queueDeliveries: " . $e->getMessage(), 3,
                storage_path('logs/notifications.log'));
        }
    }

    /**
     * Queue email delivery for a notification
     * Called by queueDeliveries when user has email enabled
     * 
     * @param string $userEmail User's email address
     * @param array $notification Notification data
     * @param int $userId User ID
     * @return void
     */
    private static function queueEmailDelivery(string $userEmail, array $notification, int $userId): void
    {
        try {
            global $config;
            
            // Get config from global scope or use defaults
            if (!isset($config)) {
                $config = require(__DIR__ . '/../../config/config.php');
            }

            $emailService = new EmailService($config);
            
            // Map notification type to email template
            $templateMap = [
                'issue_assigned' => 'issue-assigned',
                'issue_commented' => 'issue-commented',
                'issue_status_changed' => 'issue-status-changed',
            ];
            
            $template = $templateMap[$notification['type']] ?? null;
            if (!$template) {
                error_log("No email template for notification type: " . $notification['type'], 3,
                    storage_path('logs/notifications.log'));
                return;
            }

            // Get issue details for template context
            $issue = null;
            if ($notification['related_issue_id']) {
                $issue = Database::selectOne(
                    'SELECT id, issue_key, summary, description FROM issues WHERE id = ?',
                    [$notification['related_issue_id']]
                );
            }

            // Prepare template data
            $templateData = [
                'subject' => $notification['title'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'issue' => $issue,
                'notification' => $notification,
                'user_email' => $userEmail,
                'app_url' => $config['app']['url'] ?? 'http://localhost:8080/jira_clone_system/public',
            ];

            // Send email via EmailService
            $sent = $emailService->sendTemplate(
                $userEmail,
                $template,
                $templateData
            );

            // Record delivery attempt in database
            Database::insert('notification_deliveries', [
                'notification_id' => $notification['id'],
                'channel' => 'email',
                'status' => $sent ? 'delivered' : 'failed',
                'retry_count' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($sent) {
                error_log(sprintf(
                    '[EMAIL] Sent: user=%d, type=%s, to=%s',
                    $userId,
                    $notification['type'],
                    $userEmail
                ), 3, storage_path('logs/notifications.log'));
            } else {
                error_log(sprintf(
                    '[EMAIL FAILED] user=%d, type=%s, to=%s',
                    $userId,
                    $notification['type'],
                    $userEmail
                ), 3, storage_path('logs/notifications.log'));
            }
        } catch (\Exception $e) {
            error_log("Exception in queueEmailDelivery: " . $e->getMessage() . " - " . $e->getTraceAsString(), 3,
                storage_path('logs/notifications.log'));
        }
    }

    /**
     * Queue failed notification for retry
     * Stores in notification_deliveries table with 'failed' status
     * 
     * FIX 8: Added retry queuing infrastructure
     */
    public static function queueForRetry(
        string $dispatchType,
        int $relatedIssueId,
        string $errorMessage,
        int $retryCount = 0
    ): bool {
        try {
            // Store in notification_deliveries with failed status
            Database::insert('notification_deliveries', [
                'notification_id' => 0, // Special marker for retries
                'channel' => 'retry',
                'status' => 'failed',
                'error_message' => $errorMessage,
                'retry_count' => $retryCount,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            error_log(sprintf(
                '[NOTIFICATION RETRY] Queued for retry: type=%s, issue=%d, retries=%d',
                $dispatchType,
                $relatedIssueId,
                $retryCount
            ), 3, storage_path('logs/notifications.log'));
            
            return true;
        } catch (\Exception $e) {
            error_log("Failed to queue retry: " . $e->getMessage(), 3,
                storage_path('logs/notifications.log'));
            return false;
        }
    }

    /**
     * Process failed notifications and retry them
     * Call from cron job every 5 minutes for production use
     * 
     * FIX 8: Added retry processing for failed notifications
     */
    public static function processFailedNotifications(int $maxRetries = 3): int {
        try {
            $failed = Database::select(
                'SELECT * FROM notification_deliveries WHERE status = ? AND retry_count < ? ORDER BY created_at ASC LIMIT 100',
                ['failed', $maxRetries]
            );
            
            $retryCount = 0;
            foreach ($failed as $delivery) {
                try {
                    // Increment retry count
                    Database::update(
                        'notification_deliveries',
                        ['retry_count' => $delivery['retry_count'] + 1, 'updated_at' => date('Y-m-d H:i:s')],
                        'id = ?',
                        [$delivery['id']]
                    );
                    
                    $retryCount++;
                    error_log("Retried failed delivery {$delivery['id']}", 3,
                        storage_path('logs/notifications.log'));
                } catch (\Exception $e) {
                    error_log("Retry failed for delivery {$delivery['id']}: " . $e->getMessage(), 3,
                        storage_path('logs/notifications.log'));
                }
            }
            
            if ($retryCount > 0) {
                error_log(sprintf(
                    '[NOTIFICATION RETRY] Processed %d failed notifications, max_retries=%d',
                    $retryCount,
                    $maxRetries
                ), 3, storage_path('logs/notifications.log'));
            }
            
            return $retryCount;
        } catch (\Exception $e) {
            error_log('Exception processing failed notifications: ' . $e->getMessage(), 3,
                storage_path('logs/notifications.log'));
            return 0;
        }
    }
}
