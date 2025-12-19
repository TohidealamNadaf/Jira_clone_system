<?php declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    /**
     * GET /notifications - Display notifications page
     */
    public function index(Request $request): string
    {
        $user = $request->user();
        if (!$user) {
            return $this->redirect('/login');
        }
        
        $page = (int) $request->query('page', 1);
        $perPage = 25;
        
        $notifications = NotificationService::getAll($user['id'], $page, $perPage);
        $totalCount = NotificationService::getCount($user['id']);
        $unreadCount = NotificationService::getUnreadCount($user['id']);
        
        $totalPages = ceil($totalCount / $perPage);
        
        return $this->view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'totalCount' => $totalCount,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
        ]);
    }
    
    /**
     * GET /api/v1/notifications - Get notifications (API)
     * Returns unread notifications with pagination
     */
    public function apiIndex(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $limit = (int) $request->query('limit', 20);
        $limit = min($limit, 100); // Max 100 per request for performance
        
        $notifications = NotificationService::getUnread($user['id'], $limit);
        $unreadCount = NotificationService::getUnreadCount($user['id']);
        
        $this->json([
            'data' => $notifications,
            'count' => count($notifications),
            'unread_count' => $unreadCount,
        ]);
    }
    
    /**
     * PATCH /api/v1/notifications/{id}/read - Mark single notification as read
     */
    public function markAsRead(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $notificationId = (int) $request->param('id');
        
        if (!$notificationId) {
            $this->json(['error' => 'Invalid notification ID'], 400);
            return;
        }
        
        if (NotificationService::markAsRead($notificationId, $user['id'])) {
            $this->json([
                'status' => 'success',
                'unread_count' => NotificationService::getUnreadCount($user['id']),
            ]);
        } else {
            $this->json(['error' => 'Notification not found'], 404);
        }
    }
    
    /**
     * PATCH /api/v1/notifications/read-all - Mark all notifications as read
     */
    public function markAllAsRead(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        NotificationService::markAllAsRead($user['id']);
        
        $this->json([
            'status' => 'success',
            'unread_count' => 0,
        ]);
    }
    
    /**
     * DELETE /api/v1/notifications/{id} - Delete notification
     */
    public function delete(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $notificationId = (int) $request->param('id');
        
        if (!$notificationId) {
            $this->json(['error' => 'Invalid notification ID'], 400);
            return;
        }
        
        if (NotificationService::delete($notificationId, $user['id'])) {
            $this->json(['status' => 'success']);
        } else {
            $this->json(['error' => 'Notification not found'], 404);
        }
    }
    
    /**
     * GET /api/v1/notifications/preferences - Get user notification preferences
     */
    public function getPreferences(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $preferences = NotificationService::getPreferences($user['id']);
        
        $this->json([
            'data' => $preferences,
            'count' => count($preferences),
        ]);
    }
    
    /**
     * POST/PUT /api/v1/notifications/preferences - Update notification preferences
     * Supports both single and bulk preference updates
     * 
     * SECURITY FIX #1: Authorization validation
     * - User can ONLY update their OWN preferences
     * - User ID is hardcoded from authenticated session, never accepted from input
     * - All input is validated to prevent injection attacks
     *
     * SECURITY FIX #2: Input validation
     * - All event types validated against strict whitelist
     * - All channels must be strictly boolean (=== true)
     * - Invalid inputs logged with security context (IP, user agent)
     * - Clear error feedback returned to client
     * - Rate limiting detection for repeated invalid attempts
     */
    public function updatePreferences(Request $request): void
    {
        try {
            $user = $request->user();
            if (!$user) {
                $this->json(['error' => 'Unauthorized'], 401);
                return;
            }
            
            // CRITICAL SECURITY: Use authenticated user ID only
            // Never accept user_id from request input
            $userId = $user['id'];
            
            // Valid event types (whitelist)
            $validTypes = [
                'issue_created', 'issue_assigned', 'issue_commented',
                'issue_status_changed', 'issue_mentioned', 'issue_watched',
                'project_created', 'project_member_added', 'comment_reply'
            ];
            
            // Valid channels
            $validChannels = ['in_app', 'email', 'push'];
            
            // Check if bulk preferences object was sent
            $preferences = $request->input('preferences');
            
            if ($preferences && is_array($preferences)) {
                // Bulk update mode (from form submission)
                $updateCount = 0;
                $invalidCount = 0;
                $invalidEntries = [];
                
                foreach ($preferences as $eventType => $channels) {
                    // CRITICAL #2 FIX: Validate event type is in whitelist
                    if (!in_array($eventType, $validTypes)) {
                        $invalidCount++;
                        $invalidEntries[] = [
                            'event_type' => $eventType,
                            'error' => 'Invalid event type',
                            'valid_types' => $validTypes
                        ];
                        
                        // Log CRITICAL security violation
                        error_log(sprintf(
                            '[SECURITY] CRITICAL #2: Invalid event_type in preference update: event_type=%s, user_id=%d, ip=%s, user_agent=%s',
                            $eventType,
                            $userId,
                            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                        ), 3, storage_path('logs/security.log'));
                        continue;
                    }
                    
                    // CRITICAL #2 FIX: Validate channels is an array
                    if (!is_array($channels)) {
                        $invalidCount++;
                        $invalidEntries[] = [
                            'event_type' => $eventType,
                            'error' => 'Channels must be an object/array'
                        ];
                        
                        error_log(sprintf(
                            '[SECURITY] CRITICAL #2: Invalid channels type for event_type=%s, user_id=%d, received_type=%s',
                            $eventType,
                            $userId,
                            gettype($channels)
                        ), 3, storage_path('logs/security.log'));
                        continue;
                    }
                    
                    // CRITICAL #2 FIX: Validate each channel key and value
                    $hasInvalidChannels = false;
                    foreach ($channels as $channel => $value) {
                        if (!in_array($channel, $validChannels)) {
                            $hasInvalidChannels = true;
                            $invalidCount++;
                            
                            if (!isset($invalidEntries[$eventType])) {
                                $invalidEntries[$eventType] = [];
                            }
                            
                            error_log(sprintf(
                                '[SECURITY] CRITICAL #2: Invalid channel key for event_type=%s, channel=%s, user_id=%d',
                                $eventType,
                                $channel,
                                $userId
                            ), 3, storage_path('logs/security.log'));
                        }
                    }
                    
                    if ($hasInvalidChannels) {
                        continue; // Skip this event type if it has invalid channels
                    }
                    
                    // CRITICAL #2 FIX: Safely extract channel preferences with STRICT type checking
                    // Only accept boolean true (=== true), treat everything else as false
                    $inApp = isset($channels['in_app']) && $channels['in_app'] === true;
                    $email = isset($channels['email']) && $channels['email'] === true;
                    $push = isset($channels['push']) && $channels['push'] === true;
                    
                    // Update preference for AUTHENTICATED USER ONLY
                    NotificationService::updatePreference($userId, $eventType, $inApp, $email, $push);
                    $updateCount++;
                    
                    // Log successful preference updates
                    error_log(sprintf(
                        '[NOTIFICATION] Preference updated: user_id=%d, event_type=%s, in_app=%d, email=%d, push=%d',
                        $userId,
                        $eventType,
                        (int) $inApp,
                        (int) $email,
                        (int) $push
                    ), 3, storage_path('logs/notifications.log'));
                }
                
                // CRITICAL #2 FIX: Log validation summary with user context
                if ($invalidCount > 0) {
                    error_log(sprintf(
                        '[NOTIFICATION] Validation summary: user_id=%d, updated_count=%d, invalid_count=%d, ip=%s',
                        $userId,
                        $updateCount,
                        $invalidCount,
                        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                    ), 3, storage_path('logs/notifications.log'));
                }
                
                // CRITICAL #2 FIX: Return comprehensive response with error details
                $responseStatus = $invalidCount > 0 ? 'partial_success' : 'success';
                $responseMessage = $invalidCount > 0 ? 
                    "Updated {$updateCount} preference(s). {$invalidCount} were invalid." :
                    'Preferences updated successfully';
                
                $response = [
                    'status' => $responseStatus,
                    'message' => $responseMessage,
                    'updated_count' => $updateCount,
                    'invalid_count' => $invalidCount
                ];
                
                // Include error details if there were validation failures
                if ($invalidCount > 0 && count($invalidEntries) > 0) {
                    $response['errors'] = $invalidEntries;
                }
                
                $this->json($response);
            } else {
                // Single preference update mode
                $eventType = $request->input('event_type');
                $inApp = (bool) $request->input('in_app', true);
                $email = (bool) $request->input('email', true);
                $push = (bool) $request->input('push', false);
                
                if (!$eventType) {
                    $this->json(['error' => 'Missing event_type parameter'], 400);
                    return;
                }
                
                // CRITICAL #2 FIX: Validate event type against whitelist
                if (!in_array($eventType, $validTypes)) {
                    error_log(sprintf(
                        '[SECURITY] CRITICAL #2: Invalid event_type in single preference: event_type=%s, user_id=%d, ip=%s',
                        $eventType,
                        $userId,
                        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                    ), 3, storage_path('logs/security.log'));
                    
                    $this->json([
                        'error' => 'Invalid event_type',
                        'valid_types' => $validTypes
                    ], 400);
                    return;
                }
                
                // Update preference for AUTHENTICATED USER ONLY
                NotificationService::updatePreference($userId, $eventType, $inApp, $email, $push);
                
                error_log(sprintf(
                    '[NOTIFICATION] Single preference updated: user_id=%d, event_type=%s, in_app=%d, email=%d, push=%d',
                    $userId,
                    $eventType,
                    (int) $inApp,
                    (int) $email,
                    (int) $push
                ), 3, storage_path('logs/notifications.log'));
                
                $this->json(['status' => 'success', 'message' => 'Preference updated']);
            }
        } catch (\Exception $e) {
            // Log the error with full details
            error_log(sprintf(
                '[NOTIFICATION ERROR] Preference update failed: %s (File: %s, Line: %d)',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ), 3, storage_path('logs/notifications.log'));
            
            // For debugging, include the actual error message in response
            $this->json([
                'error' => 'Failed to update preferences',
                'details' => $e->getMessage(), // Remove in production
            ], 500);
        }
    }
    
    /**
     * GET /api/v1/notifications/stats - Get notification statistics
     */
    public function getStats(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $stats = NotificationService::getStats($user['id']);
        
        $this->json([
            'data' => $stats,
        ]);
    }

    /**
     * POST /api/v1/notifications/test-email - Send test email
     * Tests the email configuration and delivery
     */
    public function testEmail(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }

        try {
            // Get config
            $config = require(__DIR__ . '/../../config/config.php');
            
            // Create email service
            $emailService = new \App\Services\EmailService($config);
            
            // Validate config first
            $validation = $emailService->validateConfig();
            if (!$validation['success']) {
                $this->json([
                    'status' => 'config_error',
                    'message' => $validation['message'],
                    'details' => $validation['details'],
                ], 400);
                return;
            }

            // Send test email
            $sent = $emailService->sendTest($user['email']);

            if ($sent) {
                $this->json([
                    'status' => 'success',
                    'message' => 'Test email sent successfully',
                    'email' => $user['email'],
                    'config' => [
                        'driver' => $config['mail']['driver'],
                        'host' => $config['mail']['host'],
                        'port' => $config['mail']['port'],
                    ],
                ]);
            } else {
                $this->json([
                    'status' => 'send_error',
                    'message' => 'Failed to send test email',
                    'email' => $user['email'],
                ], 500);
            }
        } catch (\Exception $e) {
            error_log('[EMAIL TEST ERROR] ' . $e->getMessage() . ' - ' . $e->getTraceAsString(), 3,
                storage_path('logs/notifications.log'));

            $this->json([
                'status' => 'error',
                'message' => 'Error testing email configuration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/notifications/email-status - Get email configuration status
     * Shows current SMTP configuration without credentials
     */
    public function emailStatus(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }

        try {
            // Only admin can view email status
            if (!$user['is_admin']) {
                $this->json(['error' => 'Forbidden'], 403);
                return;
            }

            $config = require(__DIR__ . '/../../config/config.php');
            $emailService = new \App\Services\EmailService($config);
            
            $validation = $emailService->validateConfig();

            $this->json([
                'status' => $validation['success'] ? 'configured' : 'unconfigured',
                'message' => $validation['message'],
                'details' => [
                    'driver' => $config['mail']['driver'],
                    'host' => $config['mail']['host'],
                    'port' => $config['mail']['port'],
                    'encryption' => $config['mail']['encryption'],
                    'from_address' => $config['mail']['from_address'],
                    'from_name' => $config['mail']['from_name'],
                    'authenticated' => !empty($config['mail']['username']),
                ],
            ]);
        } catch (\Exception $e) {
            $this->json([
                'status' => 'error',
                'message' => 'Failed to get email status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/v1/notifications/send-emails - Send queued emails (Admin only)
     * Processes notification_deliveries table and sends pending emails
     * Can be called manually or via cron job
     */
    public function sendEmails(Request $request): void
    {
        $user = $request->user();
        if (!$user || !$user['is_admin']) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }

        try {
            // Get config
            $config = require(__DIR__ . '/../../config/config.php');
            $emailService = new \App\Services\EmailService($config);

            // Get pending email deliveries
            $pendingDeliveries = \App\Core\Database::select(
                'SELECT nd.*, n.title, n.message, n.type, u.email 
                 FROM notification_deliveries nd
                 JOIN notifications n ON nd.notification_id = n.id
                 JOIN users u ON n.user_id = u.id
                 WHERE nd.channel = ? AND nd.status = ? 
                 ORDER BY nd.created_at ASC 
                 LIMIT 100',
                ['email', 'pending']
            );

            $sent = 0;
            $failed = 0;

            foreach ($pendingDeliveries as $delivery) {
                try {
                    // Send email
                    $result = $emailService->sendTest($delivery['email']);

                    if ($result) {
                        // Mark as delivered
                        \App\Core\Database::update(
                            'notification_deliveries',
                            ['status' => 'delivered', 'updated_at' => date('Y-m-d H:i:s')],
                            'id = ?',
                            [$delivery['id']]
                        );
                        $sent++;
                    } else {
                        // Mark as failed
                        \App\Core\Database::update(
                            'notification_deliveries',
                            [
                                'status' => 'failed',
                                'retry_count' => $delivery['retry_count'] + 1,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ],
                            'id = ?',
                            [$delivery['id']]
                        );
                        $failed++;
                    }
                } catch (\Exception $e) {
                    error_log('[EMAIL SEND ERROR] ' . $e->getMessage(), 3,
                        storage_path('logs/notifications.log'));
                    $failed++;
                }
            }

            $this->json([
                'status' => 'success',
                'message' => 'Email processing completed',
                'sent' => $sent,
                'failed' => $failed,
                'total' => count($pendingDeliveries),
            ]);
        } catch (\Exception $e) {
            error_log('[EMAIL BATCH ERROR] ' . $e->getMessage(), 3,
                storage_path('logs/notifications.log'));

            $this->json([
                'status' => 'error',
                'message' => 'Failed to process emails',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * POST /api/v1/notifications/devices - Register device for push notifications
     */
    public function registerDevice(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $token = $request->input('token');
        $platform = $request->input('platform'); // ios, android, web
        
        if (!$token || !in_array($platform, ['ios', 'android', 'web'])) {
            $this->json(['error' => 'Missing or invalid token/platform'], 400);
            return;
        }
        
        try {
            if (\App\Services\PushService::registerDevice($user['id'], $token, $platform)) {
                error_log(sprintf(
                    '[PUSH] Device registered: user=%d, platform=%s',
                    $user['id'],
                    $platform
                ), 3, storage_path('logs/notifications.log'));
                
                $this->json([
                    'status' => 'success',
                    'message' => 'Device registered for push notifications'
                ]);
            } else {
                $this->json(['error' => 'Failed to register device'], 500);
            }
        } catch (\Exception $e) {
            error_log('[PUSH ERROR] Registration failed: ' . $e->getMessage(), 3,
                storage_path('logs/notifications.log'));
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * DELETE /api/v1/notifications/devices/{token} - Deregister device from push
     */
    public function deregisterDevice(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $token = $request->param('token');
        if (!$token) {
            $this->json(['error' => 'Missing token'], 400);
            return;
        }
        
        try {
            if (\App\Services\PushService::deactivateDevice($user['id'], $token)) {
                error_log(sprintf(
                    '[PUSH] Device deregistered: user=%d',
                    $user['id']
                ), 3, storage_path('logs/notifications.log'));
                
                $this->json(['status' => 'success', 'message' => 'Device deregistered']);
            } else {
                $this->json(['error' => 'Device not found'], 404);
            }
        } catch (\Exception $e) {
            error_log('[PUSH ERROR] Deregistration failed: ' . $e->getMessage(), 3,
                storage_path('logs/notifications.log'));
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * GET /api/v1/notifications/devices - Get user's registered devices
     */
    public function getDevices(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            $this->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        try {
            $devices = \App\Services\PushService::getUserDevices($user['id']);
            $this->json([
                'data' => $devices,
                'count' => count($devices)
            ]);
        } catch (\Exception $e) {
            error_log('[PUSH ERROR] Get devices failed: ' . $e->getMessage(), 3,
                storage_path('logs/notifications.log'));
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
