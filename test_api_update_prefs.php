<?php
/**
 * Test the API endpoint for updating notification preferences
 * Simulates what the frontend form sends
 */

require 'bootstrap/autoload.php';
require 'bootstrap/app.php';

use App\Core\Database;
use App\Services\NotificationService;

$userId = 1; // admin user

echo "=== Testing Preference Update API Simulation ===\n\n";

// 1. First, initialize preferences if needed
echo "Step 1: Ensuring preferences exist...\n";
$existingCount = Database::selectOne(
    'SELECT COUNT(*) as count FROM notification_preferences WHERE user_id = ?',
    [$userId]
)['count'];

if ($existingCount === 0) {
    echo "No preferences found. Creating defaults...\n";
    $eventTypes = [
        'issue_created', 'issue_assigned', 'issue_commented',
        'issue_status_changed', 'issue_mentioned', 'issue_watched',
        'project_created', 'project_member_added', 'comment_reply'
    ];
    
    foreach ($eventTypes as $type) {
        NotificationService::updatePreference($userId, $type, true, true, false);
    }
    echo "Created " . count($eventTypes) . " default preferences\n";
} else {
    echo "Preferences already exist: $existingCount\n";
}

// 2. Now simulate the form submission
echo "\nStep 2: Simulating form submission...\n";

// This is what the JavaScript would send after parsing
$formData = [
    'issue_created' => ['in_app' => true, 'email' => false, 'push' => false],
    'issue_assigned' => ['in_app' => true, 'email' => true, 'push' => true],
    'issue_commented' => ['in_app' => false, 'email' => true, 'push' => false],
    'issue_status_changed' => ['in_app' => true, 'email' => true, 'push' => false],
    'issue_mentioned' => ['in_app' => true, 'email' => true, 'push' => false],
    'issue_watched' => ['in_app' => true, 'email' => true, 'push' => false],
    'project_created' => ['in_app' => true, 'email' => true, 'push' => false],
    'project_member_added' => ['in_app' => true, 'email' => true, 'push' => false],
    'comment_reply' => ['in_app' => true, 'email' => true, 'push' => false],
];

echo "Processing " . count($formData) . " preferences...\n";

// 3. Update each preference
$updateCount = 0;
$invalidCount = 0;
$invalidEntries = [];

$validTypes = [
    'issue_created', 'issue_assigned', 'issue_commented',
    'issue_status_changed', 'issue_mentioned', 'issue_watched',
    'project_created', 'project_member_added', 'comment_reply'
];

$validChannels = ['in_app', 'email', 'push'];

foreach ($formData as $eventType => $channels) {
    // Validate event type
    if (!in_array($eventType, $validTypes)) {
        echo "❌ Invalid event_type: $eventType\n";
        $invalidCount++;
        $invalidEntries[] = [
            'event_type' => $eventType,
            'error' => 'Invalid event type',
            'valid_types' => $validTypes
        ];
        continue;
    }
    
    // Validate channels
    if (!is_array($channels)) {
        echo "❌ Channels not array for $eventType\n";
        $invalidCount++;
        continue;
    }
    
    // Check each channel
    $hasInvalidChannels = false;
    foreach ($channels as $channel => $value) {
        if (!in_array($channel, $validChannels)) {
            echo "❌ Invalid channel '$channel' for $eventType\n";
            $hasInvalidChannels = true;
            $invalidCount++;
        }
    }
    
    if ($hasInvalidChannels) {
        continue;
    }
    
    // Extract values with strict type checking (=== true)
    $inApp = isset($channels['in_app']) && $channels['in_app'] === true;
    $email = isset($channels['email']) && $channels['email'] === true;
    $push = isset($channels['push']) && $channels['push'] === true;
    
    // Update
    $result = NotificationService::updatePreference($userId, $eventType, $inApp, $email, $push);
    if ($result) {
        echo "✓ Updated $eventType: in_app=" . ($inApp ? '1' : '0') . 
             ", email=" . ($email ? '1' : '0') . ", push=" . ($push ? '1' : '0') . "\n";
        $updateCount++;
    } else {
        echo "❌ Failed to update $eventType\n";
        $invalidCount++;
    }
}

// 4. Verify the updates
echo "\nStep 3: Verifying updates...\n";
$updated = Database::select(
    'SELECT event_type, in_app, email, push FROM notification_preferences WHERE user_id = ? ORDER BY event_type',
    [$userId]
);

echo "Current preferences in database:\n";
foreach ($updated as $p) {
    echo sprintf(
        "  %s: in_app=%d, email=%d, push=%d\n",
        $p['event_type'],
        $p['in_app'],
        $p['email'],
        $p['push']
    );
}

// 5. Summary
echo "\n=== Summary ===\n";
echo "Updated: $updateCount\n";
echo "Invalid: $invalidCount\n";
echo "Status: " . ($invalidCount > 0 ? 'PARTIAL_SUCCESS' : 'SUCCESS') . "\n";
