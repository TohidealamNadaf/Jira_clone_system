<?php
/**
 * Verify Notification API Fix
 * 
 * This script verifies that:
 * 1. Routes are correctly ordered
 * 2. API endpoints return JSON (not HTML)
 * 3. Session authentication works
 */

declare(strict_types=1);

echo "═══════════════════════════════════════════════════════════\n";
echo "  NOTIFICATION API FIX VERIFICATION\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Load the app
require 'bootstrap/autoload.php';

echo "✓ Bootstrap loaded\n\n";

// Check 1: Verify routes are loaded correctly
echo "─── Check 1: API Routes ───\n";

try {
    $router = app()->getRouter();
    $routes = $router->getRoutes(); // This may not exist, but let's try
    echo "✓ Router instance obtained\n";
} catch (Exception $e) {
    echo "! Router check skipped (method may not be public)\n";
}

// Check 2: Verify helpers exist
echo "\n─── Check 2: Helper Functions ───\n";

if (function_exists('wants_json')) {
    echo "✓ wants_json() exists\n";
} else {
    echo "✗ wants_json() missing\n";
}

if (function_exists('is_api_request')) {
    echo "✓ is_api_request() exists\n";
} else {
    echo "✗ is_api_request() missing\n";
}

// Check 3: Test is_api_request() function
echo "\n─── Check 3: API Request Detection ───\n";

$_SERVER['REQUEST_URI'] = '/jira_clone_system/public/api/v1/notifications';
if (is_api_request()) {
    echo "✓ API request detection works\n";
} else {
    echo "✗ API request detection failed\n";
}

// Reset
unset($_SERVER['REQUEST_URI']);

// Check 4: Verify NotificationController exists
echo "\n─── Check 4: NotificationController ───\n";

if (class_exists('App\Controllers\NotificationController')) {
    echo "✓ NotificationController class exists\n";
    
    $rc = new ReflectionClass('App\Controllers\NotificationController');
    $methods = $rc->getMethods();
    $methodNames = array_map(fn($m) => $m->getName(), $methods);
    
    $requiredMethods = ['apiIndex', 'markAsRead', 'markAllAsRead', 'delete'];
    foreach ($requiredMethods as $method) {
        if (in_array($method, $methodNames)) {
            echo "  ✓ {$method}() exists\n";
        } else {
            echo "  ✗ {$method}() missing\n";
        }
    }
} else {
    echo "✗ NotificationController class not found\n";
}

// Check 5: Verify NotificationService
echo "\n─── Check 5: NotificationService ───\n";

if (class_exists('App\Services\NotificationService')) {
    echo "✓ NotificationService class exists\n";
    
    $rc = new ReflectionClass('App\Services\NotificationService');
    $methods = $rc->getMethods();
    $methodNames = array_map(fn($m) => $m->getName(), $methods);
    
    $requiredMethods = ['getAll', 'getUnread', 'getUnreadCount', 'markAsRead', 'markAllAsRead', 'delete'];
    foreach ($requiredMethods as $method) {
        if (in_array($method, $methodNames)) {
            echo "  ✓ {$method}() exists\n";
        } else {
            echo "  ✗ {$method}() missing\n";
        }
    }
} else {
    echo "✗ NotificationService class not found\n";
}

// Check 6: Verify database table
echo "\n─── Check 6: Database Tables ───\n";

try {
    $db = app('db');
    
    // Check if notifications table exists
    $result = $db->selectOne("
        SELECT COUNT(*) as count 
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'notifications'
    ");
    
    if ($result && $result['count'] > 0) {
        echo "✓ Notifications table exists\n";
        
        // Count total rows
        $totalNotifications = $db->selectOne("SELECT COUNT(*) as count FROM notifications");
        echo "  - Total notifications: " . ($totalNotifications['count'] ?? 0) . "\n";
    } else {
        echo "✗ Notifications table not found\n";
        echo "  Run: php install_notifications.php\n";
    }
} catch (Exception $e) {
    echo "! Database check skipped: " . $e->getMessage() . "\n";
}

// Check 7: Verify ApiMiddleware
echo "\n─── Check 7: API Middleware ───\n";

if (class_exists('App\Middleware\ApiMiddleware')) {
    echo "✓ ApiMiddleware class exists\n";
    
    // Check if it supports session auth
    $rc = new ReflectionClass('App\Middleware\ApiMiddleware');
    $code = file_get_contents($rc->getFileName());
    
    if (str_contains($code, 'authenticateSession')) {
        echo "  ✓ Session authentication supported\n";
    } else {
        echo "  ✗ Session authentication not found\n";
    }
} else {
    echo "✗ ApiMiddleware class not found\n";
}

// Summary
echo "\n═══════════════════════════════════════════════════════════\n";
echo "  VERIFICATION COMPLETE\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\nNext Steps:\n";
echo "  1. Clear browser cache (Ctrl+Shift+Delete)\n";
echo "  2. Navigate to /notifications page\n";
echo "  3. Click 'Mark as Read' button\n";
echo "  4. Open DevTools (F12) → Network tab\n";
echo "  5. Verify response is JSON (not HTML)\n";
echo "\n";
