<?php declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * Push Notification Service - Firebase Cloud Messaging Integration
 * Handles device registration and push notification delivery
 */
class PushService
{
    private string $apiKey;
    private string $projectId;
    private const FCM_API_URL = 'https://fcm.googleapis.com/v1/projects/{projectId}/messages:send';
    
    public function __construct(array $config = [])
    {
        $this->apiKey = $config['push']['fcm_server_key'] ?? '';
        $this->projectId = $config['push']['fcm_project_id'] ?? '';
    }
    
    /**
     * Check if push service is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->projectId);
    }
    
    /**
     * Send push notification to user's devices
     */
    public function sendToUser(
        int $userId,
        string $title,
        string $body,
        array $data = []
    ): bool {
        try {
            if (!$this->isConfigured()) {
                error_log('[PUSH] FCM not configured', 3, storage_path('logs/notifications.log'));
                return false;
            }
            
            // Get user's active device tokens
            $tokens = Database::select(
                'SELECT token FROM push_device_tokens WHERE user_id = ? AND active = 1',
                [$userId]
            );
            
            if (empty($tokens)) {
                error_log(sprintf(
                    '[PUSH] No active tokens for user %d',
                    $userId
                ), 3, storage_path('logs/notifications.log'));
                return false;
            }
            
            $successCount = 0;
            foreach ($tokens as $token) {
                if ($this->sendToToken($token['token'], $title, $body, $data)) {
                    $successCount++;
                }
            }
            
            return $successCount > 0;
        } catch (\Exception $e) {
            error_log(sprintf(
                '[PUSH ERROR] Failed to send to user %d: %s',
                $userId,
                $e->getMessage()
            ), 3, storage_path('logs/notifications.log'));
            return false;
        }
    }
    
    /**
     * Send push notification to specific device token
     */
    private function sendToToken(
        string $token,
        string $title,
        string $body,
        array $data = []
    ): bool {
        try {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                    'android' => [
                        'priority' => 'HIGH',
                        'notification' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ]
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10',
                        ]
                    ]
                ]
            ];
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => str_replace('{projectId}', $this->projectId, self::FCM_API_URL),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->getAccessToken(),
                ],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200) {
                error_log(sprintf(
                    '[PUSH] Sent successfully to token: %s',
                    substr($token, 0, 20) . '...'
                ), 3, storage_path('logs/notifications.log'));
                return true;
            } else {
                error_log(sprintf(
                    '[PUSH ERROR] HTTP %d from FCM: %s | Error: %s',
                    $httpCode,
                    $response ?: 'no response',
                    $error ?: 'no curl error'
                ), 3, storage_path('logs/notifications.log'));
                return false;
            }
        } catch (\Exception $e) {
            error_log(sprintf(
                '[PUSH ERROR] Exception: %s',
                $e->getMessage()
            ), 3, storage_path('logs/notifications.log'));
            return false;
        }
    }
    
    /**
     * Get FCM access token for API authentication
     * Note: In production, use service account JSON key for OAuth 2.0
     */
    private function getAccessToken(): string
    {
        // For now, use the server key directly
        // In production, implement proper OAuth 2.0 with service account
        return $this->apiKey;
    }
    
    /**
     * Register device token for push notifications
     */
    public static function registerDevice(int $userId, string $token, string $platform): bool
    {
        try {
            if (!filter_var($token, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/.{50,}/']])) {
                error_log('[PUSH] Invalid token format', 3, storage_path('logs/notifications.log'));
                return false;
            }
            
            if (!in_array($platform, ['ios', 'android', 'web'])) {
                error_log('[PUSH] Invalid platform: ' . $platform, 3, storage_path('logs/notifications.log'));
                return false;
            }
            
            return (bool) Database::insertOrUpdate(
                'push_device_tokens',
                [
                    'user_id' => $userId,
                    'token' => $token,
                    'platform' => $platform,
                    'active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                ['user_id', 'token']
            );
        } catch (\Exception $e) {
            error_log(sprintf(
                '[PUSH] Failed to register device: %s',
                $e->getMessage()
            ), 3, storage_path('logs/notifications.log'));
            return false;
        }
    }
    
    /**
     * Deactivate device token (user unsubscribed)
     */
    public static function deactivateDevice(int $userId, string $token): bool
    {
        try {
            return (bool) Database::update(
                'push_device_tokens',
                ['active' => 0, 'updated_at' => date('Y-m-d H:i:s')],
                'user_id = ? AND token = ?',
                [$userId, $token]
            );
        } catch (\Exception $e) {
            error_log(sprintf(
                '[PUSH] Failed to deactivate device: %s',
                $e->getMessage()
            ), 3, storage_path('logs/notifications.log'));
            return false;
        }
    }
    
    /**
     * Get user's active devices
     */
    public static function getUserDevices(int $userId): array
    {
        try {
            return Database::select(
                'SELECT id, platform, token, last_used_at FROM push_device_tokens 
                WHERE user_id = ? AND active = 1 
                ORDER BY last_used_at DESC NULLS LAST',
                [$userId]
            );
        } catch (\Exception $e) {
            error_log('[PUSH] Failed to get devices: ' . $e->getMessage(), 3,
                storage_path('logs/notifications.log'));
            return [];
        }
    }
}
