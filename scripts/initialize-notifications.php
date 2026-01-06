<?php declare(strict_types=1);

/**
 * Initialize Notification Preferences
 * 
 * This script initializes notification preferences for all users in the system.
 * It creates preference records for all 9 event types with smart defaults:
 * - in_app: enabled (1)
 * - email: enabled (1)
 * - push: disabled (0)
 * 
 * Safe to run multiple times (idempotent via INSERT ... ON DUPLICATE KEY UPDATE)
 * 
 * Usage:
 *   php scripts/initialize-notifications.php
 * 
 * Expected Output:
 *   Initializing notification preferences...
 *   Found 7 users
 *   Processing event types...
 *   ✅ Successfully initialized 63 preferences (7 users × 9 events)
 */

// Include autoloader
require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Core\Database;

// All 9 notification event types from schema
const EVENT_TYPES = [
    'issue_created',
    'issue_assigned',
    'issue_commented',
    'issue_status_changed',
    'issue_mentioned',
    'issue_watched',
    'project_created',
    'project_member_added',
    'comment_reply',
];

// Smart defaults (from FIX 5)
const DEFAULTS = [
    'in_app' => 1,  // Enabled by default
    'email' => 1,   // Enabled by default
    'push' => 0,    // Disabled by default (secure)
];

/**
 * Initialize notification preferences for all users
 */
function initializeNotificationPreferences(): void
{
    echo "\n";
    echo "==========================================================\n";
    echo "Initializing Notification Preferences\n";
    echo "==========================================================\n\n";

    try {
        // Step 1: Get all active users
        echo "Step 1: Getting all active users...\n";
        $users = Database::select(
            'SELECT id, email FROM users WHERE is_active = 1 ORDER BY id',
            []
        );

        $userCount = count($users);
        if ($userCount === 0) {
            echo "❌ No active users found. Aborting.\n";
            return;
        }

        echo "✅ Found {$userCount} active users\n\n";

        // List users
        echo "Users to initialize:\n";
        foreach ($users as $user) {
            echo "  - User {$user['id']}: {$user['email']}\n";
        }
        echo "\n";

        // Step 2: Process each user
        echo "Step 2: Initializing preferences...\n";
        echo "Processing {$userCount} users × " . count(EVENT_TYPES) . " event types = " . ($userCount * count(EVENT_TYPES)) . " records\n\n";

        $createdCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            $userId = (int)$user['id'];
            
            foreach (EVENT_TYPES as $eventType) {
                try {
                    // Use insertOrUpdate to be idempotent
                    Database::insertOrUpdate(
                        'notification_preferences',
                        [
                            'user_id' => $userId,
                            'event_type' => $eventType,
                            'in_app' => DEFAULTS['in_app'],
                            'email' => DEFAULTS['email'],
                            'push' => DEFAULTS['push'],
                        ],
                        ['user_id', 'event_type']
                    );
                    $createdCount++;
                } catch (\Exception $e) {
                    echo "⚠️  Failed to create preference for user {$userId}, event {$eventType}: " . $e->getMessage() . "\n";
                    $errorCount++;
                }
            }

            // Progress indicator
            echo "✓ User {$userId} (" . count(EVENT_TYPES) . " preferences)\n";
        }

        echo "\n";

        // Step 3: Verify results
        echo "Step 3: Verifying results...\n";
        $totalPreferences = Database::selectOne(
            'SELECT COUNT(*) as count FROM notification_preferences',
            []
        );

        $totalCount = $totalPreferences['count'] ?? 0;

        echo "✅ Total preferences in database: {$totalCount}\n";
        echo "   Expected: " . ($userCount * count(EVENT_TYPES)) . "\n";

        // Summary
        echo "\n";
        echo "==========================================================\n";
        echo "Initialization Summary\n";
        echo "==========================================================\n";
        echo "Users:              {$userCount}\n";
        echo "Event Types:        " . count(EVENT_TYPES) . "\n";
        echo "Preferences Created: {$createdCount}\n";
        echo "Errors:             {$errorCount}\n";
        echo "Total in Database:  {$totalCount}\n";
        echo "\n";

        if ($errorCount === 0 && $totalCount >= ($userCount * count(EVENT_TYPES))) {
            echo "✅ SUCCESS: All notification preferences initialized!\n";
            echo "\n";
            echo "Defaults Applied:\n";
            echo "  - in_app: ENABLED (1)\n";
            echo "  - email: ENABLED (1)\n";
            echo "  - push: DISABLED (0)\n";
            echo "\n";
        } else {
            echo "⚠️  WARNING: Some preferences may be missing.\n";
            echo "   Check database or logs for details.\n";
            echo "\n";
        }

        echo "==========================================================\n\n";

    } catch (\Exception $e) {
        echo "❌ Fatal error during initialization:\n";
        echo "   " . $e->getMessage() . "\n";
        echo "   " . $e->getFile() . ":" . $e->getLine() . "\n";
        exit(1);
    }
}

/**
 * Verify notification preferences table structure
 */
function verifyTableStructure(): void
{
    try {
        // Check if table exists by trying a simple query
        Database::selectOne(
            'SELECT COUNT(*) as count FROM notification_preferences LIMIT 1',
            []
        );
    } catch (\Exception $e) {
        echo "❌ Error: notification_preferences table not found.\n";
        echo "   Please run database migrations first.\n";
        echo "   Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    // Verify table exists before proceeding
    verifyTableStructure();
    
    // Initialize preferences
    initializeNotificationPreferences();
    
    exit(0);
}
