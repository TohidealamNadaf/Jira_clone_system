<?php
/**
 * Debug script to test notification API endpoints
 */

require_once __DIR__ . '/bootstrap/autoload.php';

// Start session to simulate logged-in user
session_start();

echo "=== Notification API Debug ===\n\n";

// 1. Check if user is logged in
echo "Step 1: Check Session\n";
$user = Session::user();
if ($user) {
    echo "✅ User logged in: " . $user['email'] . " (ID: " . $user['id'] . ")\n\n";
} else {
    echo "❌ No user in session. Please log in first.\n";
    echo "URL: http://localhost:8080/jira_clone_system/public/\n";
    exit;
}

// 2. Check if notifications exist
echo "Step 2: Check Notifications Table\n";
$db = \App\Core\Database::class;
try {
    $notifications = \App\Services\NotificationService::getAll($user['id'], 1, 5);
    echo "✅ Found " . count($notifications) . " notifications\n";
    
    if (count($notifications) > 0) {
        echo "\nNotifications:\n";
        foreach ($notifications as $n) {
            $status = $n['is_read'] ? "Read" : "UNREAD";
            echo "  - ID: " . $n['id'] . " | Type: " . $n['type'] . " | Status: $status\n";
        }
    } else {
        echo "⚠️  No notifications. Create an issue to generate notifications.\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// 3. Test authentication methods
echo "\n\nStep 3: Test Authentication Methods\n";

$request = new \App\Core\Request();

// Test 1: Session auth
echo "\n3a. Session Auth:\n";
$sessionUser = $request->user();
if ($sessionUser) {
    echo "✅ Session user: " . $sessionUser['email'] . "\n";
} else {
    echo "❌ No session user\n";
}

// Test 2: Global API user
echo "\n3b. API Global User:\n";
if (isset($GLOBALS['api_user'])) {
    echo "✅ API global user set: " . $GLOBALS['api_user']['email'] . "\n";
} else {
    echo "⚠️  API global user not set (normal for direct script execution)\n";
}

// 4. Test marking as read (if notification exists)
echo "\n\nStep 4: Test Mark as Read Function\n";
if (count($notifications) > 0) {
    $firstNotif = $notifications[0];
    if (!$firstNotif['is_read']) {
        echo "Testing with unread notification ID: " . $firstNotif['id'] . "\n";
        try {
            $result = \App\Services\NotificationService::markAsRead($firstNotif['id'], $user['id']);
            if ($result) {
                echo "✅ Successfully marked notification as read\n";
                echo "✅ Verify by visiting /notifications page\n";
            } else {
                echo "❌ Failed to mark as read\n";
            }
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "ℹ️  First notification is already read\n";
    }
} else {
    echo "⚠️  No unread notifications to test with\n";
}

// 5. Test API route matching
echo "\n\nStep 5: Check Routes\n";
echo "Expected routes in /api/v1:\n";
echo "  - GET    /notifications\n";
echo "  - PATCH  /notifications/read-all\n";
echo "  - PATCH  /notifications/{id}/read\n";
echo "  - DELETE /notifications/{id}\n";
echo "\nTo test these endpoints, use:\n";
echo "  curl -b 'PHPSESSID=<session_id>' http://localhost:8080/jira_clone_system/public/api/v1/notifications\n";

echo "\n\n=== Debug Complete ===\n";
?>
