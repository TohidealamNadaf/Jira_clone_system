<?php
/**
 * Test Notifications Page Load
 * Direct test of NotificationService
 */

require __DIR__ . '/../bootstrap/autoload.php';

use App\Services\NotificationService;
use App\Core\Session;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Notifications Page Test</h2>\n";
echo "<pre>\n";

try {
    // Check if user is logged in
    $user = Session::user();
    if (!$user) {
        echo "⚠ You need to be logged in to test notifications.\n";
        echo "Please login first at: http://localhost:8080/jira_clone_system/public/\n";
        echo "</pre>\n";
        exit;
    }
    
    $userId = $user['id'];
    echo "Logged in as: {$user['email']}\n";
    echo "User ID: $userId\n\n";
    
    // Get notifications  
    echo "1. Testing NotificationService::getAll()...\n";
    $notifications = NotificationService::getAll($userId, 1, 25);
    echo "   Notifications fetched: " . count($notifications) . "\n";
    if ($notifications) {
        echo "   Sample notification:\n";
        $notif = $notifications[0];
        foreach ($notif as $key => $value) {
            echo "     - $key: " . substr((string)$value, 0, 50) . "\n";
        }
    }
    echo "\n";
    
    // Get total count
    echo "2. Testing NotificationService::getCount()...\n";
    $count = NotificationService::getCount($userId);
    echo "   Total notifications: $count\n\n";
    
    // Get unread count
    echo "3. Testing NotificationService::getUnreadCount()...\n";
    $unreadCount = NotificationService::getUnreadCount($userId);
    echo "   Unread notifications: $unreadCount\n\n";
    
    // Get unread list
    echo "4. Testing NotificationService::getUnread()...\n";
    $unread = NotificationService::getUnread($userId, 20);
    echo "   Unread notifications fetched: " . count($unread) . "\n\n";
    
    // Get stats
    echo "5. Testing NotificationService::getStats()...\n";
    $stats = NotificationService::getStats($userId);
    echo "   Total: {$stats['total']}\n";
    echo "   Unread: {$stats['unread']}\n";
    if (isset($stats['by_type'])) {
        echo "   By type: " . count($stats['by_type']) . " types\n";
    }
    echo "\n";
    
    echo "✓ SUCCESS! Notifications page is working correctly.\n";
    echo "\nYou can now access: http://localhost:8080/jira_clone_system/public/notifications\n";
    
} catch (Throwable $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "</pre>\n";
