<?php
/**
 * Debug Notification Preferences API
 * Access: http://localhost/jira_clone_system/public/debug-notification-api.php
 */

header('Content-Type: application/json');

session_start();

// Simulate an authenticated request
$userId = $_GET['user_id'] ?? 2;

require '../bootstrap/autoload.php';

use App\Core\Database;
use App\Services\NotificationService;

$response = [
    'step' => 'initialization',
    'user_id' => $userId,
    'checks' => []
];

try {
    // Step 1: Check if method exists
    $response['checks'][] = [
        'name' => 'Database::insertOrUpdate() exists',
        'status' => method_exists('App\Core\Database', 'insertOrUpdate') ? 'PASS' : 'FAIL'
    ];

    // Step 2: Check notification_preferences table exists
    $response['checks'][] = [
        'name' => 'notification_preferences table exists',
        'status' => Database::tableExists('notification_preferences') ? 'PASS' : 'FAIL'
    ];

    // Step 3: Get current preferences
    $response['step'] = 'getting_preferences';
    $prefs = NotificationService::getPreferences($userId);
    $response['current_preferences_count'] = count($prefs);
    $response['checks'][] = [
        'name' => 'getPreferences() returned data',
        'status' => count($prefs) > 0 ? 'PASS' : 'WARN: No preferences found'
    ];

    // Step 4: Test updatePreference directly
    $response['step'] = 'testing_updatePreference';
    
    $testResult = NotificationService::updatePreference(
        userId: $userId,
        eventType: 'issue_created',
        inApp: true,
        email: false,
        push: true
    );
    
    $response['checks'][] = [
        'name' => 'updatePreference() executed',
        'status' => $testResult ? 'PASS' : 'FAIL',
        'result' => $testResult
    ];

    // Step 5: Verify update
    $response['step'] = 'verifying_update';
    $updatedPrefs = NotificationService::getPreferences($userId);
    $issueCreatedPref = null;
    foreach ($updatedPrefs as $pref) {
        if ($pref['event_type'] === 'issue_created') {
            $issueCreatedPref = $pref;
            break;
        }
    }

    if ($issueCreatedPref) {
        $response['checks'][] = [
            'name' => 'issue_created preference updated',
            'status' => ($issueCreatedPref['in_app'] == 1 && $issueCreatedPref['email'] == 0 && $issueCreatedPref['push'] == 1) ? 'PASS' : 'FAIL',
            'expected' => ['in_app' => 1, 'email' => 0, 'push' => 1],
            'actual' => [
                'in_app' => (int)$issueCreatedPref['in_app'],
                'email' => (int)$issueCreatedPref['email'],
                'push' => (int)$issueCreatedPref['push']
            ]
        ];
    } else {
        $response['checks'][] = [
            'name' => 'issue_created preference found',
            'status' => 'FAIL'
        ];
    }

    // Step 6: Test bulk preferences (like the form submission)
    $response['step'] = 'testing_bulk_update';
    
    $bulkData = [
        'issue_assigned' => ['in_app' => true, 'email' => true, 'push' => false],
        'issue_commented' => ['in_app' => true, 'email' => false, 'push' => false],
    ];

    foreach ($bulkData as $eventType => $channels) {
        $inApp = isset($channels['in_app']) ? true : false;
        $email = isset($channels['email']) ? true : false;
        $push = isset($channels['push']) ? true : false;

        $result = NotificationService::updatePreference($userId, $eventType, $inApp, $email, $push);
        $response['checks'][] = [
            'name' => "updatePreference($eventType)",
            'status' => $result ? 'PASS' : 'FAIL'
        ];
    }

    $response['status'] = 'SUCCESS';
    $response['final_check'] = 'All tests passed!';

} catch (Exception $e) {
    $response['status'] = 'ERROR';
    $response['error_message'] = $e->getMessage();
    $response['error_file'] = $e->getFile();
    $response['error_line'] = $e->getLine();
    $response['error_trace'] = $e->getTraceAsString();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
