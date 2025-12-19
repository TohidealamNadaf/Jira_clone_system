<?php
/**
 * Test Notification Preferences Endpoint
 * Run from browser: http://localhost/jira_clone_system/public/test-notification-prefs.php
 */

// Start session for authentication simulation
session_start();

// Get user ID from URL or session
$userId = $_GET['user_id'] ?? (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2);

echo "<h1>Notification Preferences Test</h1>";
echo "<p>User ID: $userId</p>";

// Include bootstrap to get access to Database
require '../bootstrap/autoload.php';

use App\Core\Database;
use App\Services\NotificationService;

echo "<h2>1. Current Preferences</h2>";
try {
    $prefs = NotificationService::getPreferences($userId);
    if (empty($prefs)) {
        echo "<p style='color: orange;'>⚠️ No preferences found - they may not be initialized</p>";
    } else {
        echo "<pre>";
        foreach ($prefs as $pref) {
            echo "{$pref['event_type']}: in_app=" . ($pref['in_app'] ? 'true' : 'false') 
                 . ", email=" . ($pref['email'] ? 'true' : 'false')
                 . ", push=" . ($pref['push'] ? 'true' : 'false') . "\n";
        }
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}

echo "<h2>2. Test INSERT OR UPDATE</h2>";
try {
    $result = Database::insertOrUpdate(
        'notification_preferences',
        [
            'user_id' => $userId,
            'event_type' => 'issue_created',
            'in_app' => 1,
            'email' => 0,
            'push' => 1,
        ],
        ['user_id', 'event_type']
    );
    echo "<p style='color: green;'>✓ insertOrUpdate() returned: " . ($result ? 'true' : 'false') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}

echo "<h2>3. Verify Updated Value</h2>";
try {
    $prefs = NotificationService::getPreferences($userId);
    foreach ($prefs as $pref) {
        if ($pref['event_type'] === 'issue_created') {
            echo "<p>issue_created: in_app=" . ($pref['in_app'] ? 'true' : 'false') 
                 . ", email=" . ($pref['email'] ? 'true' : 'false')
                 . ", push=" . ($pref['push'] ? 'true' : 'false') . "</p>";
            if ($pref['in_app'] == 1 && $pref['email'] == 0 && $pref['push'] == 1) {
                echo "<p style='color: green;'>✓ Update verified!</p>";
            }
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}
