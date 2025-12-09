<?php
/**
 * Simulate a request to /notifications
 */

// Set up the request as if coming from the web
$_GET = ['page' => '1'];
$_POST = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/jira_clone_system/public/notifications';
$_SERVER['HTTP_HOST'] = 'localhost:8080';

// Create a mock session with a logged-in user
session_start();
$_SESSION['_user'] = [
    'id' => 1,
    'name' => 'Admin User',
    'email' => 'admin@example.com',
];

try {
    require 'bootstrap/autoload.php';
    require 'bootstrap/app.php';
    
    $request = new \App\Core\Request(['page' => '1']);
    $user = $request->user();
    
    echo "=== Testing Notifications Page ===\n\n";
    echo "User from request: " . ($user ? "✓ " . $user['name'] : "✗ NOT FOUND") . "\n";
    
    if ($user) {
        echo "User ID: " . $user['id'] . "\n";
        echo "User Email: " . $user['email'] . "\n\n";
        
        // Test NotificationService
        echo "=== Testing NotificationService ===\n";
        
        $count = \App\Services\NotificationService::getCount($user['id']);
        echo "Total notifications: $count\n";
        
        try {
            $unread = \App\Services\NotificationService::getUnreadCount($user['id']);
            echo "Unread notifications: $unread\n";
        } catch (Exception $e) {
            echo "Error getting unread: " . $e->getMessage() . "\n";
            throw $e;
        }
        
        $all = \App\Services\NotificationService::getAll($user['id'], 1, 25);
        echo "Retrieved notifications: " . count($all) . "\n";
        
        echo "\n✅ All tests passed!\n";
    } else {
        echo "\n✗ User not found in session!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
