<?php
/**
 * Verification Script: Notification Preferences SQL Fix
 * 
 * This script verifies that the notification preferences update fix is working correctly.
 * Run this AFTER deploying the fix to ensure the system is production-ready.
 */

declare(strict_types=1);

require 'bootstrap/app.php';

use App\Core\Database;

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║     NOTIFICATION PREFERENCES SQL FIX - VERIFICATION          ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$tests = [
    'database_connection' => false,
    'preferences_table_exists' => false,
    'insert_or_update_works' => false,
    'preferences_saved_correctly' => false,
    'all_tests_pass' => false,
];

try {
    // Test 1: Database connection
    echo "Test 1: Database Connection... ";
    $pdo = Database::getConnection();
    if ($pdo) {
        echo "✓ PASS\n";
        $tests['database_connection'] = true;
    } else {
        echo "✗ FAIL\n";
        throw new Exception('Could not connect to database');
    }
    
    // Test 2: Check if notification_preferences table exists
    echo "Test 2: Check notification_preferences table... ";
    $tables = Database::select(
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notification_preferences'"
    );
    if (!empty($tables)) {
        echo "✓ PASS\n";
        $tests['preferences_table_exists'] = true;
    } else {
        echo "✗ FAIL (Table does not exist)\n";
        throw new Exception('notification_preferences table not found');
    }
    
    // Test 3: Test insertOrUpdate functionality
    echo "Test 3: Test insertOrUpdate with positional parameters... ";
    
    // Use test user ID 1 (should exist from seed data)
    // Use a valid event_type from the ENUM
    $testData = [
        'user_id' => 1,
        'event_type' => 'issue_mentioned',  // Valid ENUM value
        'in_app' => 1,
        'email' => 1,
        'push' => 0,
    ];
    
    try {
        $result = Database::insertOrUpdate(
            'notification_preferences',
            $testData,
            ['user_id', 'event_type']
        );
        
        // Note: PDO rowCount() doesn't always return correct count for 
        // INSERT...ON DUPLICATE KEY UPDATE, so we don't check return value
        // Instead we verify in Test 4 that the data was actually saved
        echo "✓ PASS\n";
        $tests['insert_or_update_works'] = true;
    } catch (\Exception $e) {
        echo "✗ FAIL - Exception: " . $e->getMessage() . "\n";
        $tests['insert_or_update_works'] = false;
    }
    
    // Test 4: Verify the data was saved correctly
    echo "Test 4: Verify saved data... ";
    $saved = Database::selectOne(
        "SELECT * FROM notification_preferences WHERE user_id = ? AND event_type = ?",
        [1, $testData['event_type']]
    );
    
    if ($saved && $saved['in_app'] == 1 && $saved['email'] == 1 && $saved['push'] == 0) {
        echo "✓ PASS\n";
        $tests['preferences_saved_correctly'] = true;
    } else {
        echo "✗ FAIL (Data not saved correctly)\n";
        echo "  Expected: in_app=1, email=1, push=0\n";
        if ($saved) {
            echo "  Got: in_app=" . ($saved['in_app'] ?? 'NULL') . ", email=" . ($saved['email'] ?? 'NULL') . ", push=" . ($saved['push'] ?? 'NULL') . "\n";
        } else {
            echo "  Got: Record not found in database\n";
        }
        $tests['preferences_saved_correctly'] = false;
    }
    
    // Final summary
    echo "\n" . str_repeat("─", 65) . "\n";
    echo "SUMMARY\n";
    echo str_repeat("─", 65) . "\n";
    
    // Count actual tests (exclude all_tests_pass)
    $passedCount = 0;
    foreach ($tests as $test => $result) {
        if ($test !== 'all_tests_pass' && $result) {
            $passedCount++;
        }
    }
    $total = count($tests) - 1;  // Exclude all_tests_pass from total
    
    echo "Passed: $passedCount/$total tests\n\n";
    
    foreach ($tests as $test => $testPassed) {
        if ($test === 'all_tests_pass') continue;
        $status = $testPassed ? '✓' : '✗';
        $testName = str_replace('_', ' ', ucfirst($test));
        echo "$status $testName\n";
    }
    
    if ($passedCount === $total) {
        echo "\n✓ ALL TESTS PASSED - System is production-ready!\n";
        $tests['all_tests_pass'] = true;
    } else {
        echo "\n⚠️ WARNING: Some tests failed - Review the output above\n";
    }
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n" . str_repeat("═", 65) . "\n";
echo "Verification complete.\n";
