<?php
/**
 * Test Notification Preferences API
 */

require 'bootstrap/autoload.php';

use App\Core\Database;
use App\Services\NotificationService;

echo "=== Notification Preferences Test ===\n\n";

// Test with user ID 2 (John Smith)
$userId = 2;

echo "1. Testing getPreferences()...\n";
$prefs = NotificationService::getPreferences($userId);
echo "   Got " . count($prefs) . " preferences\n";
if (empty($prefs)) {
    echo "   WARNING: No preferences found!\n";
}
foreach ($prefs as $pref) {
    echo "   - {$pref['event_type']}: in_app={$pref['in_app']}, email={$pref['email']}, push={$pref['push']}\n";
}

echo "\n2. Testing updatePreference()...\n";
try {
    $result = NotificationService::updatePreference(
        userId: $userId,
        eventType: 'issue_created',
        inApp: true,
        email: false,
        push: true
    );
    echo "   Update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n3. Verifying update...\n";
$prefs = NotificationService::getPreferences($userId);
foreach ($prefs as $pref) {
    if ($pref['event_type'] === 'issue_created') {
        echo "   issue_created: in_app={$pref['in_app']}, email={$pref['email']}, push={$pref['push']}\n";
        if ($pref['in_app'] == 1 && $pref['email'] == 0 && $pref['push'] == 1) {
            echo "   ✓ Update verified successfully!\n";
        } else {
            echo "   ✗ Update verification FAILED - values don't match!\n";
        }
    }
}

echo "\n4. Testing Database::insertOrUpdate() directly...\n";
try {
    $result = Database::insertOrUpdate(
        'notification_preferences',
        [
            'user_id' => $userId,
            'event_type' => 'issue_assigned',
            'in_app' => 1,
            'email' => 1,
            'push' => 0,
        ],
        ['user_id', 'event_type']
    );
    echo "   Direct insertOrUpdate result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n✓ Test Complete!\n";
