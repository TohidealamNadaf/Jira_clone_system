<?php
/**
 * Diagnostic script for time tracking dashboard issues
 * Checks database tables, routes, and service availability
 */

declare(strict_types=1);

// Load bootstrap
require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;
use App\Services\TimeTrackingService;
use App\Controllers\TimeTrackingController;

echo "=== TIME TRACKING DIAGNOSIS ===\n\n";

// 1. Check database tables
echo "1. CHECKING DATABASE TABLES:\n";
$tables = [
    'issue_time_logs',
    'active_timers',
    'user_rates',
    'project_budgets',
    'budget_alerts',
    'time_tracking_settings'
];

foreach ($tables as $table) {
    try {
        $result = Database::selectOne("SHOW TABLES LIKE ?", [$table]);
        echo "   ✓ $table: " . ($result ? "EXISTS" : "MISSING") . "\n";
    } catch (Exception $e) {
        echo "   ✗ $table: ERROR - " . $e->getMessage() . "\n";
    }
}

// 2. Check service methods
echo "\n2. CHECKING SERVICE METHODS:\n";
try {
    $service = new TimeTrackingService();
    
    // Test getUserCurrentRate with test user
    $testRate = $service->getUserCurrentRate(1);
    echo "   ✓ getUserCurrentRate: " . ($testRate ? "WORKING" : "No rates found (expected if no data)") . "\n";
    
    // Test getActiveTimer
    try {
        $timer = $service->getActiveTimer(1);
        echo "   ✓ getActiveTimer: " . ($timer ? "WORKING" : "No active timer (expected)") . "\n";
    } catch (Exception $e) {
        echo "   ✗ getActiveTimer: " . $e->getMessage() . "\n";
    }
    
    // Test getUserTimeLogs
    $logs = $service->getUserTimeLogs(1, [
        'start_date' => date('Y-m-d'),
        'end_date' => date('Y-m-d')
    ]);
    echo "   ✓ getUserTimeLogs: " . count($logs) . " entries found\n";
    
} catch (Exception $e) {
    echo "   ✗ Service initialization error: " . $e->getMessage() . "\n";
}

// 3. Check controller
echo "\n3. CHECKING CONTROLLER:\n";
try {
    $controller = new TimeTrackingController();
    echo "   ✓ TimeTrackingController: Loaded\n";
} catch (Exception $e) {
    echo "   ✗ TimeTrackingController: " . $e->getMessage() . "\n";
}

// 4. Check data integrity
echo "\n4. DATA INTEGRITY CHECK:\n";
try {
    // Check users table
    $users = Database::select("SELECT COUNT(*) as count FROM users", []);
    echo "   - Users in database: " . ($users[0]['count'] ?? 0) . "\n";
    
    // Check issues table
    $issues = Database::select("SELECT COUNT(*) as count FROM issues", []);
    echo "   - Issues in database: " . ($issues[0]['count'] ?? 0) . "\n";
    
    // Check time logs
    $timeLogs = Database::select("SELECT COUNT(*) as count FROM issue_time_logs", []);
    echo "   - Time logs recorded: " . ($timeLogs[0]['count'] ?? 0) . "\n";
    
    // Check user rates
    $rates = Database::select("SELECT COUNT(*) as count FROM user_rates WHERE is_active = 1", []);
    echo "   - Active user rates: " . ($rates[0]['count'] ?? 0) . " (⚠️  Need to set up at least one!)\n";
    
} catch (Exception $e) {
    echo "   ✗ Data check failed: " . $e->getMessage() . "\n";
}

// 5. Recommendations
echo "\n5. SETUP RECOMMENDATIONS:\n";
echo "   1. Ensure user rates are configured (INSERT INTO user_rates)\n";
echo "   2. Test timer start/stop operations\n";
echo "   3. Verify view file paths in app.php\n";
echo "   4. Check CSS/JS asset loading\n";

echo "\n=== DIAGNOSIS COMPLETE ===\n";
?>
