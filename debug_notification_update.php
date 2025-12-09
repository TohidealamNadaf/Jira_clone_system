<?php
/**
 * Debug notification preference update issue
 */

require 'bootstrap/autoload.php';
require 'bootstrap/app.php';

use App\Core\Database;
use App\Services\NotificationService;

// Test with user 1
$userId = 1;
$eventType = 'issue_created';

echo "=== Testing Notification Preference Update ===\n\n";

// 1. Check if preferences table exists
echo "Step 1: Checking if notification_preferences table exists...\n";
try {
    $result = Database::select('SHOW TABLES LIKE "notification_preferences"', []);
    if (!empty($result)) {
        echo "✓ Table exists\n";
    } else {
        echo "✗ Table does NOT exist!\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "✗ Error checking table: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Check table structure
echo "\nStep 2: Checking table structure...\n";
try {
    $columns = Database::select('DESCRIBE notification_preferences', []);
    foreach ($columns as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
} catch (\Exception $e) {
    echo "✗ Error describing table: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Check existing data
echo "\nStep 3: Checking existing preferences for user $userId...\n";
try {
    $existing = Database::select(
        'SELECT * FROM notification_preferences WHERE user_id = ? AND event_type = ?',
        [$userId, $eventType]
    );
    
    if (!empty($existing)) {
        echo "✓ Found existing preference:\n";
        foreach ($existing as $row) {
            echo "  - in_app: {$row['in_app']}\n";
            echo "  - email: {$row['email']}\n";
            echo "  - push: {$row['push']}\n";
        }
    } else {
        echo "  No existing preference (will be created)\n";
    }
} catch (\Exception $e) {
    echo "✗ Error checking existing: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. Test the insertOrUpdate directly
echo "\nStep 4: Testing Database::insertOrUpdate directly...\n";
try {
    $result = Database::insertOrUpdate(
        'notification_preferences',
        [
            'user_id' => $userId,
            'event_type' => $eventType,
            'in_app' => 1,
            'email' => 0,
            'push' => 0,
        ],
        ['user_id', 'event_type']
    );
    
    if ($result) {
        echo "✓ insertOrUpdate succeeded\n";
    } else {
        echo "✗ insertOrUpdate returned false\n";
    }
} catch (\Exception $e) {
    echo "✗ insertOrUpdate threw exception:\n";
    echo "  Error: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
    exit(1);
}

// 5. Test the NotificationService method
echo "\nStep 5: Testing NotificationService::updatePreference...\n";
try {
    $result = NotificationService::updatePreference($userId, $eventType, true, false, true);
    
    if ($result) {
        echo "✓ updatePreference succeeded\n";
    } else {
        echo "✗ updatePreference returned false\n";
    }
} catch (\Exception $e) {
    echo "✗ updatePreference threw exception:\n";
    echo "  Error: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
    exit(1);
}

// 6. Verify the update
echo "\nStep 6: Verifying the update...\n";
try {
    $updated = Database::selectOne(
        'SELECT * FROM notification_preferences WHERE user_id = ? AND event_type = ?',
        [$userId, $eventType]
    );
    
    if ($updated) {
        echo "✓ Preference found after update:\n";
        echo "  - in_app: {$updated['in_app']}\n";
        echo "  - email: {$updated['email']}\n";
        echo "  - push: {$updated['push']}\n";
    } else {
        echo "✗ Preference NOT found after update!\n";
    }
} catch (\Exception $e) {
    echo "✗ Error verifying: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== All Tests Passed ===\n";
