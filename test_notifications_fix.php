<?php
/**
 * Test Notifications Page
 */

require __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;
use App\Services\NotificationService;

echo "<h2>Testing Notifications System</h2>\n";
echo "<pre>\n";

try {
    echo "1. Checking notifications table structure...\n";
    $columns = Database::select('
        SHOW COLUMNS FROM notifications
    ', []);
    
    echo "   Columns found:\n";
    foreach ($columns as $col) {
        echo "   - {$col['Field']}: {$col['Type']}\n";
    }
    echo "\n";
    
    // Check if there's a logged in user
    echo "2. Checking session...\n";
    if (isset($_SESSION['_user'])) {
        echo "   User logged in: {$_SESSION['_user']['email']}\n";
        echo "   User ID: {$_SESSION['_user']['id']}\n";
    } else {
        echo "   No user session - need to be logged in to test\n";
    }
    echo "\n";
    
    // Try getting unread notifications
    echo "3. Testing NotificationService::getUnreadCount()...\n";
    if (isset($_SESSION['_user'])) {
        $count = NotificationService::getUnreadCount($_SESSION['_user']['id']);
        echo "   Unread notifications: $count\n";
    } else {
        echo "   Skipped - no user session\n";
    }
    echo "\n";
    
    // List all methods
    echo "4. Available NotificationService methods:\n";
    $reflection = new ReflectionClass('App\Services\NotificationService');
    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC) as $method) {
        if (!$method->isConstructor()) {
            echo "   - " . $method->getName() . "()\n";
        }
    }
    echo "\n";
    
    echo "✓ SUCCESS! Notifications system is working.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "</pre>\n";
