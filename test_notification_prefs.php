<?php
require 'bootstrap/autoload.php';
require 'bootstrap/app.php';

use App\Core\Database;
use App\Services\NotificationService;

$userId = 1; // admin user

// Check preferences in database
echo "=== Checking Database ===\n";
$prefs = Database::select(
    'SELECT event_type, in_app, email, push FROM notification_preferences WHERE user_id = ? ORDER BY event_type',
    [$userId]
);

echo count($prefs) . " preferences found for user $userId\n\n";
if (!empty($prefs)) {
    foreach ($prefs as $p) {
        echo sprintf(
            "%s: in_app=%d, email=%d, push=%d\n",
            $p['event_type'],
            $p['in_app'],
            $p['email'],
            $p['push']
        );
    }
} else {
    echo "No preferences found - need to initialize!\n";
}

// Test the service method
echo "\n=== Testing Service Method ===\n";
$servicePrefs = NotificationService::getPreferences($userId);
echo "Service returned " . count($servicePrefs) . " preferences\n";

// Test shouldNotify method
echo "\n=== Testing shouldNotify Method ===\n";
$result_in_app = NotificationService::shouldNotify($userId, 'issue_created', 'in_app');
$result_email = NotificationService::shouldNotify($userId, 'issue_created', 'email');
$result_push = NotificationService::shouldNotify($userId, 'issue_created', 'push');

echo "issue_created (in_app): " . ($result_in_app ? 'true' : 'false') . "\n";
echo "issue_created (email): " . ($result_email ? 'true' : 'false') . "\n";
echo "issue_created (push): " . ($result_push ? 'true' : 'false') . "\n";

// Test updating a preference
echo "\n=== Testing updatePreference ===\n";
$updated = NotificationService::updatePreference($userId, 'issue_created', true, false, false);
echo "Update result: " . ($updated ? 'true' : 'false') . "\n";

// Check what was stored
$check = Database::selectOne(
    'SELECT * FROM notification_preferences WHERE user_id = ? AND event_type = ?',
    [$userId, 'issue_created']
);

if ($check) {
    echo "Stored: in_app=" . $check['in_app'] . ", email=" . $check['email'] . ", push=" . $check['push'] . "\n";
}
