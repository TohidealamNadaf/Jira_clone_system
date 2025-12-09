<?php
/**
 * Create Test Notification
 * Requires login
 */

// Start session BEFORE bootstrap
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../bootstrap/autoload.php';

use App\Services\NotificationService;
use App\Core\Database;

echo "<h2>Create Test Notification</h2>\n";
echo "<pre>\n";

try {
    // Check session directly first
    echo "Checking session...\n";
    if (empty($_SESSION)) {
        echo "⚠ Session is empty\n\n";
        echo "You need to be logged in.\n";
        echo "1. Log out completely\n";
        echo "2. Log back in at: http://localhost:8080/jira_clone_system/public/\n";
        echo "3. Then visit: http://localhost:8080/jira_clone_system/public/create-test-notification.php\n";
        echo "\nOr run debug: http://localhost:8080/jira_clone_system/public/debug-session.php\n";
        echo "</pre>\n";
        exit;
    }
    
    // Get user from session
    if (!isset($_SESSION['_user'])) {
        echo "⚠ User not in session\n\n";
        echo "Session contains:\n";
        foreach ($_SESSION as $key => $value) {
            echo "- $key\n";
        }
        echo "\nPlease log in again.\n";
        echo "</pre>\n";
        exit;
    }
    
    $user = $_SESSION['_user'];
    
    $userId = $user['id'];
    echo "Logged in as: {$user['email']}\n";
    echo "User ID: $userId\n\n";
    
    echo "Creating test notification...\n\n";
    
    // Create a test notification
    $id = NotificationService::create(
        userId: $userId,
        type: 'test_notification',
        title: 'Test Notification',
        message: 'This is a test notification to verify the system works! Created at ' . date('Y-m-d H:i:s'),
        actionUrl: '/notifications',
        priority: 'normal'
    );
    
    echo "✅ SUCCESS!\n\n";
    echo "Notification created with ID: $id\n";
    echo "Type: test_notification\n";
    echo "Title: Test Notification\n";
    echo "Priority: normal\n\n";
    
    // Verify it was created
    echo "Verifying notification in database...\n";
    $notification = Database::selectOne(
        'SELECT id, title, type, is_read FROM notifications WHERE id = ?',
        [$id]
    );
    
    if ($notification) {
        echo "✓ Notification found in database\n";
        echo "  - ID: {$notification['id']}\n";
        echo "  - Title: {$notification['title']}\n";
        echo "  - Type: {$notification['type']}\n";
        echo "  - Read: " . ($notification['is_read'] ? 'Yes' : 'No') . "\n\n";
    }
    
    // Get current stats
    echo "Getting notification stats...\n";
    $stats = NotificationService::getStats($userId);
    echo "✓ Total notifications: {$stats['total']}\n";
    echo "✓ Unread notifications: {$stats['unread']}\n\n";
    
    echo "═════════════════════════════════════════════════\n";
    echo "View your notifications at:\n";
    echo "http://localhost:8080/jira_clone_system/public/notifications\n";
    echo "═════════════════════════════════════════════════\n";
    
} catch (Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "</pre>\n";
