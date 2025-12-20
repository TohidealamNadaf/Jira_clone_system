<?php
/**
 * Test Timer Resume Fix - December 20, 2025
 * 
 * Tests the complete timer pause/resume flow to verify the fix works
 */

declare(strict_types=1);

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Load bootstrap
require_once __DIR__ . '/bootstrap/autoload.php';

use App\Services\TimeTrackingService;
use App\Core\Database;
use App\Core\Session;

// Initialize database connection
try {
    // Simulate session
    $_SESSION['user'] = [
        'id' => 1,
        'email' => 'test@example.com',
        'display_name' => 'Test User'
    ];

    $service = new TimeTrackingService();
    
    echo "=== TIMER PAUSE/RESUME FIX TEST ===\n\n";

    // Test 1: Check if user has active timer
    echo "TEST 1: Checking for existing active timers...\n";
    $activeTimer = $service->getActiveTimer(1);
    if ($activeTimer) {
        echo "  ✓ Found active timer: ID {$activeTimer['issue_time_log_id']}\n";
        echo "  Status: {$activeTimer['issue_time_log_id']}\n";
    } else {
        echo "  ✗ No active timer found\n";
    }

    echo "\nTEST 2: Testing pause functionality...\n";
    try {
        $pauseResult = $service->pauseTimer(1);
        echo "  ✓ Timer paused successfully\n";
        echo "    - Status: {$pauseResult['status']}\n";
        echo "    - Elapsed: {$pauseResult['elapsed_seconds']}s\n";
        echo "    - Cost: {$pauseResult['cost']} {$pauseResult['currency']}\n";
        echo "    - Time Log ID: {$pauseResult['time_log_id']}\n";
    } catch (Exception $e) {
        echo "  ✗ Error pausing timer: {$e->getMessage()}\n";
    }

    echo "\nTEST 3: Testing resume functionality...\n";
    try {
        $resumeResult = $service->resumeTimer(1);
        echo "  ✓ Timer resumed successfully\n";
        echo "    - Status: {$resumeResult['status']}\n";
        echo "    - Elapsed: {$resumeResult['elapsed_seconds']}s\n";
        echo "    - Cost: {$resumeResult['cost']} {$resumeResult['currency']}\n";
        echo "    - Time Log ID: {$resumeResult['time_log_id']}\n";
    } catch (Exception $e) {
        echo "  ✗ Error resuming timer: {$e->getMessage()}\n";
    }

    echo "\nTEST 4: Verifying timer state in database...\n";
    try {
        $activeTimer = $service->getActiveTimer(1);
        if ($activeTimer) {
            $timeLog = $service->getTimeLog($activeTimer['issue_time_log_id']);
            echo "  ✓ Timer state verified\n";
            echo "    - Time Log Status: {$timeLog['status']}\n";
            echo "    - Start Time: {$timeLog['start_time']}\n";
            echo "    - Resumed At: {$timeLog['resumed_at']}\n";
        } else {
            echo "  ✗ No active timer found after resume\n";
        }
    } catch (Exception $e) {
        echo "  ✗ Error verifying timer: {$e->getMessage()}\n";
    }

    echo "\n=== TEST COMPLETE ===\n";

} catch (Exception $e) {
    echo "FATAL ERROR: {$e->getMessage()}\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString();
    exit(1);
}
?>
