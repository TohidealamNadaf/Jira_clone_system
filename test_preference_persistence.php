<?php
/**
 * Test Notification Preferences Persistence
 * 
 * Tests that preferences are properly saved and retrieved from database
 * Verifies the insertOrUpdate() method works correctly
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;
use App\Services\NotificationService;

echo "======================================\n";
echo "Notification Preferences Persistence Test\n";
echo "======================================\n\n";

// Test user ID (use an existing user from your database)
$userId = 1; // Adjust if needed

echo "Testing with User ID: $userId\n\n";

// Test 1: Insert new preference
echo "TEST 1: Insert New Preference\n";
echo "------------------------------\n";
echo "Event: issue_created\n";
echo "Settings: in_app=true, email=false, push=true\n";

$result1 = NotificationService::updatePreference(
    userId: $userId,
    eventType: 'issue_created',
    inApp: true,
    email: false,
    push: true
);

echo "Update Result: " . ($result1 ? "SUCCESS ✅" : "FAILED ❌") . "\n";

// Verify by reading from database
$pref1 = Database::selectOne(
    'SELECT * FROM notification_preferences WHERE user_id = ? AND event_type = ?',
    [$userId, 'issue_created']
);

if ($pref1) {
    echo "Database Check:\n";
    echo "  in_app: " . $pref1['in_app'] . " (expected: 1)\n";
    echo "  email: " . $pref1['email'] . " (expected: 0)\n";
    echo "  push: " . $pref1['push'] . " (expected: 1)\n";
    
    $test1_pass = ($pref1['in_app'] == 1 && $pref1['email'] == 0 && $pref1['push'] == 1);
    echo "Test Result: " . ($test1_pass ? "PASSED ✅" : "FAILED ❌") . "\n";
} else {
    echo "ERROR: Preference not found in database ❌\n";
    $test1_pass = false;
}

echo "\n";

// Test 2: Update existing preference
echo "TEST 2: Update Existing Preference\n";
echo "----------------------------------\n";
echo "Event: issue_created (same as Test 1)\n";
echo "New Settings: in_app=false, email=true, push=false\n";

$result2 = NotificationService::updatePreference(
    userId: $userId,
    eventType: 'issue_created',
    inApp: false,
    email: true,
    push: false
);

echo "Update Result: " . ($result2 ? "SUCCESS ✅" : "FAILED ❌") . "\n";

// Verify by reading from database
$pref2 = Database::selectOne(
    'SELECT * FROM notification_preferences WHERE user_id = ? AND event_type = ?',
    [$userId, 'issue_created']
);

if ($pref2) {
    echo "Database Check:\n";
    echo "  in_app: " . $pref2['in_app'] . " (expected: 0)\n";
    echo "  email: " . $pref2['email'] . " (expected: 1)\n";
    echo "  push: " . $pref2['push'] . " (expected: 0)\n";
    
    $test2_pass = ($pref2['in_app'] == 0 && $pref2['email'] == 1 && $pref2['push'] == 0);
    echo "Test Result: " . ($test2_pass ? "PASSED ✅" : "FAILED ❌") . "\n";
} else {
    echo "ERROR: Preference not found in database ❌\n";
    $test2_pass = false;
}

echo "\n";

// Test 3: Verify critical issue - Email checkbox staying checked
echo "TEST 3: Email Checkbox Persistence (Critical Bug Test)\n";
echo "------------------------------------------------------\n";
echo "Event: issue_assigned\n";
echo "Step 1: Check email\n";

NotificationService::updatePreference(
    userId: $userId,
    eventType: 'issue_assigned',
    inApp: true,
    email: true,
    push: false
);

$pref3a = Database::selectOne(
    'SELECT email FROM notification_preferences WHERE user_id = ? AND event_type = ?',
    [$userId, 'issue_assigned']
);
echo "  Result: email = " . $pref3a['email'] . " (expected: 1)\n";

echo "Step 2: Uncheck email\n";

NotificationService::updatePreference(
    userId: $userId,
    eventType: 'issue_assigned',
    inApp: true,
    email: false,
    push: false
);

$pref3b = Database::selectOne(
    'SELECT email FROM notification_preferences WHERE user_id = ? AND event_type = ?',
    [$userId, 'issue_assigned']
);
echo "  Result: email = " . $pref3b['email'] . " (expected: 0)\n";

echo "Step 3: Hard refresh (simulated) - Read from database again\n";

$pref3c = Database::selectOne(
    'SELECT email FROM notification_preferences WHERE user_id = ? AND event_type = ?',
    [$userId, 'issue_assigned']
);
echo "  Result: email = " . $pref3c['email'] . " (expected: 0)\n";

$test3_pass = ($pref3c['email'] == 0);
echo "Test Result: " . ($test3_pass ? "PASSED ✅ (Email is unchecked after refresh)" : "FAILED ❌ (Email still checked!)") . "\n";

echo "\n";

// Test 4: Bulk preferences via API
echo "TEST 4: Bulk Preference Update (API Simulation)\n";
echo "---------------------------------------------\n";
echo "Updating multiple event types at once\n";

$bulkData = [
    'issue_created' => ['in_app' => true, 'email' => false, 'push' => false],
    'issue_assigned' => ['in_app' => true, 'email' => true, 'push' => true],
    'issue_commented' => ['in_app' => false, 'email' => true, 'push' => false],
];

foreach ($bulkData as $eventType => $channels) {
    NotificationService::updatePreference(
        userId: $userId,
        eventType: $eventType,
        inApp: $channels['in_app'],
        email: $channels['email'],
        push: $channels['push']
    );
}

$allPrefs = Database::select(
    'SELECT event_type, in_app, email, push FROM notification_preferences WHERE user_id = ? AND event_type IN (?, ?, ?)',
    [$userId, 'issue_created', 'issue_assigned', 'issue_commented']
);

$test4_pass = true;
foreach ($allPrefs as $pref) {
    $expected = $bulkData[$pref['event_type']];
    echo "  {$pref['event_type']}: in_app={$pref['in_app']}, email={$pref['email']}, push={$pref['push']}\n";
    
    if ($pref['in_app'] != ($expected['in_app'] ? 1 : 0) ||
        $pref['email'] != ($expected['email'] ? 1 : 0) ||
        $pref['push'] != ($expected['push'] ? 1 : 0)) {
        $test4_pass = false;
    }
}

echo "Test Result: " . ($test4_pass ? "PASSED ✅" : "FAILED ❌") . "\n";

echo "\n";
echo "======================================\n";
echo "SUMMARY\n";
echo "======================================\n";
echo "Test 1 (Insert New): " . ($test1_pass ? "PASSED ✅" : "FAILED ❌") . "\n";
echo "Test 2 (Update): " . ($test2_pass ? "PASSED ✅" : "FAILED ❌") . "\n";
echo "Test 3 (Email Bug): " . ($test3_pass ? "PASSED ✅" : "FAILED ❌") . "\n";
echo "Test 4 (Bulk): " . ($test4_pass ? "PASSED ✅" : "FAILED ❌") . "\n";

$all_pass = ($test1_pass && $test2_pass && $test3_pass && $test4_pass);
echo "\n";
echo "OVERALL: " . ($all_pass ? "ALL TESTS PASSED ✅" : "SOME TESTS FAILED ❌") . "\n";
echo "======================================\n";

exit($all_pass ? 0 : 1);
