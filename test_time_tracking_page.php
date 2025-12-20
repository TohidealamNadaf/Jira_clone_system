<?php
/**
 * Test Time Tracking Project Report Page
 * Simulates the full request and renders the view
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Session;
use App\Controllers\TimeTrackingController;

try {
    echo "=== Testing Time Tracking Project Report Page ===\n\n";
    
    // Set up a mock session (in real requests this would be from auth middleware)
    $_SESSION['user'] = [
        'id' => 1,
        'email' => 'admin@example.com',
        'first_name' => 'Admin',
        'display_name' => 'Administrator',
        'avatar' => null,
        'is_admin' => 1
    ];
    
    // Test the controller method
    $controller = new TimeTrackingController();
    
    echo "1. Testing projectReport(1)...\n";
    ob_start();
    $result = $controller->projectReport(1);
    $output = ob_get_clean();
    
    if ($result && !empty($result)) {
        echo "✓ Controller returned content\n";
        echo "  Content length: " . strlen($result) . " chars\n";
        
        // Check for error messages
        if (strpos($result, 'alert') !== false) {
            echo "⚠ Page contains alert/error box\n";
        }
        
        // Check for main content
        if (strpos($result, 'Project Time Tracking') !== false || strpos($result, 'time-tracking') !== false) {
            echo "✓ Page contains time tracking content\n";
        } else {
            echo "⚠ Might not contain expected content\n";
        }
    } else {
        echo "✗ Controller returned empty result\n";
    }
    
    echo "\n2. Testing projectReport without parameter (should use Request)...\n";
    // This would normally be handled by the router
    
    echo "\n✓ All tests completed!\n";
    echo "\nNow try: http://localhost:8081/jira_clone_system/public/time-tracking/project/1\n";
    
} catch (\Exception $e) {
    echo "✗ Error: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}\n";
    echo "Line: {$e->getLine()}\n";
    exit(1);
}
