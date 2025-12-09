<?php
/**
 * Test Notification API Endpoints
 */

echo "<h2>Testing Notification API Endpoints</h2>\n";
echo "<pre>\n";

// Direct database test with user 1
try {
    $mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system');
    
    echo "1. Creating a test notification for user 1...\n";
    $mysqli->query("
        INSERT INTO notifications (user_id, type, title, message, action_url, priority, is_read, created_at)
        VALUES (1, 'test', 'Test API', 'Testing API endpoints', '/notifications', 'normal', 0, NOW())
    ");
    $notificationId = $mysqli->insert_id;
    echo "   Created notification ID: $notificationId\n\n";
    
    echo "2. Verifying notification exists...\n";
    $result = $mysqli->query("SELECT id, title, is_read FROM notifications WHERE id = $notificationId");
    $row = $result->fetch_assoc();
    echo "   Found: {$row['title']} (is_read: {$row['is_read']})\n\n";
    
    echo "3. Testing API route: /api/v1/notifications/{$notificationId}/read\n";
    echo "   The JavaScript should send PATCH request to this URL\n";
    echo "   Expected response: {'status': 'success', 'unread_count': X}\n\n";
    
    echo "4. Testing API route: /api/v1/notifications/read-all\n";
    echo "   The JavaScript should send PATCH request to this URL\n";
    echo "   Expected response: {'status': 'success', 'unread_count': 0}\n\n";
    
    echo "5. Testing API route: /api/v1/notifications/{$notificationId}\n";
    echo "   The JavaScript should send DELETE request to this URL\n";
    echo "   Expected response: {'status': 'success'}\n\n";
    
    // Check if routes are registered
    echo "═══════════════════════════════════════════════\n";
    echo "To test manually, use curl commands:\n\n";
    
    echo "curl -X PATCH http://localhost:8080/jira_clone_system/public/api/v1/notifications/$notificationId/read \\\n";
    echo "  -H 'Content-Type: application/json' \\\n";
    echo "  -H 'X-CSRF-Token: YOUR_CSRF_TOKEN'\n\n";
    
    echo "curl -X PATCH http://localhost:8080/jira_clone_system/public/api/v1/notifications/read-all \\\n";
    echo "  -H 'Content-Type: application/json' \\\n";
    echo "  -H 'X-CSRF-Token: YOUR_CSRF_TOKEN'\n\n";
    
    echo "curl -X DELETE http://localhost:8080/jira_clone_system/public/api/v1/notifications/$notificationId \\\n";
    echo "  -H 'X-CSRF-Token: YOUR_CSRF_TOKEN'\n\n";
    
    echo "═══════════════════════════════════════════════\n";
    echo "Common Issues:\n";
    echo "1. CSRF token not in meta tag\n";
    echo "2. Routes not matching (check api.php)\n";
    echo "3. Controller methods not found\n";
    echo "4. Fetch URL wrong format\n";
    echo "5. Request method (PATCH vs PUT) mismatch\n";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "</pre>\n";
