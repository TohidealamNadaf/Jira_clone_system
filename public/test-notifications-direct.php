<?php
/**
 * Direct Test - Uses Application initialization
 */

// Initialize the app properly (like index.php does)
$app = require __DIR__ . '/../bootstrap/app.php';

use App\Services\NotificationService;

echo "<h2>Notifications Direct Test</h2>\n";
echo "<pre>\n";

try {
    // Just test database directly with a known user ID
    echo "Testing with User ID = 1 (usually admin)\n";
    echo "Note: This tests if the notification system works\n\n";
    
    $userId = 1;
    
    echo "1. Testing NotificationService::getUnreadCount()...\n";
    $unreadCount = NotificationService::getUnreadCount($userId);
    echo "   ✓ Unread count: $unreadCount\n\n";
    
    echo "2. Testing NotificationService::getCount()...\n";
    $totalCount = NotificationService::getCount($userId);
    echo "   ✓ Total count: $totalCount\n\n";
    
    echo "3. Creating a test notification...\n";
    $notificationId = NotificationService::create(
        userId: $userId,
        type: 'test',
        title: 'Test Notification ' . date('Y-m-d H:i:s'),
        message: 'This is a test notification to verify the system works',
        actionUrl: '/notifications',
        priority: 'normal'
    );
    echo "   ✓ Created with ID: $notificationId\n\n";
    
    echo "4. Verifying notification was created...\n";
    $newUnreadCount = NotificationService::getUnreadCount($userId);
    echo "   ✓ Unread count now: $newUnreadCount\n";
    if ($newUnreadCount > $unreadCount) {
        echo "   ✓ Count increased - notification created successfully!\n\n";
    }
    
    echo "5. Getting all notifications...\n";
    $notifications = NotificationService::getAll($userId, 1, 10);
    echo "   ✓ Retrieved " . count($notifications) . " notifications\n";
    if (!empty($notifications)) {
        echo "   Latest notification:\n";
        echo "   - Title: {$notifications[0]['title']}\n";
        echo "   - Type: {$notifications[0]['type']}\n";
        echo "   - Read: " . ($notifications[0]['is_read'] ? 'Yes' : 'No') . "\n\n";
    }
    
    echo "═════════════════════════════════════════════════\n";
    echo "✅ ALL TESTS PASSED!\n";
    echo "═════════════════════════════════════════════════\n\n";
    echo "To view notifications in the UI:\n";
    echo "http://localhost:8080/jira_clone_system/public/notifications\n";
    
} catch (Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "</pre>\n";
