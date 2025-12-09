<?php
/**
 * Test: Database Schema Consolidation (Fix 1)
 * Verifies that all notification tables exist and have correct structure
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

echo "\n=== DATABASE SCHEMA FIX 1 - VERIFICATION TEST ===\n\n";

$results = [];
$errors = [];

// Test 1: Check if all tables exist
echo "Test 1: Checking for required tables...\n";
$tables = [
    'notifications',
    'notification_preferences',
    'notification_deliveries',
    'notifications_archive',
    'users'
];

foreach ($tables as $table) {
    try {
        $result = Database::selectOne(
            "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
            [$table]
        );
        
        if ($result) {
            echo "  ✅ Table '$table' exists\n";
            $results[] = "$table: OK";
        } else {
            echo "  ❌ Table '$table' NOT FOUND\n";
            $errors[] = "$table: Missing";
        }
    } catch (\Exception $e) {
        echo "  ⚠️  Error checking table '$table': " . $e->getMessage() . "\n";
        $errors[] = "$table: Error - " . $e->getMessage();
    }
}

// Test 2: Verify ENUM columns
echo "\nTest 2: Verifying ENUM column types...\n";

$enumChecks = [
    ['table' => 'notifications', 'column' => 'type', 'expected' => 'enum'],
    ['table' => 'notifications', 'column' => 'priority', 'expected' => 'enum'],
    ['table' => 'notification_preferences', 'column' => 'event_type', 'expected' => 'enum'],
    ['table' => 'notification_deliveries', 'column' => 'channel', 'expected' => 'enum'],
    ['table' => 'notification_deliveries', 'column' => 'status', 'expected' => 'enum'],
];

foreach ($enumChecks as $check) {
    try {
        $result = Database::selectOne(
            "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?",
            [$check['table'], $check['column']]
        );
        
        if ($result && stripos($result['COLUMN_TYPE'], 'enum') !== false) {
            echo "  ✅ {$check['table']}.{$check['column']} is ENUM\n";
            $results[] = "{$check['table']}.{$check['column']}: ENUM";
        } else {
            echo "  ❌ {$check['table']}.{$check['column']} is NOT ENUM (got: {$result['COLUMN_TYPE']})\n";
            $errors[] = "{$check['table']}.{$check['column']}: Not ENUM";
        }
    } catch (\Exception $e) {
        echo "  ⚠️  Error: " . $e->getMessage() . "\n";
        $errors[] = "ENUM check error: " . $e->getMessage();
    }
}

// Test 3: Verify Foreign Keys
echo "\nTest 3: Verifying foreign key constraints...\n";

$fkChecks = [
    ['table' => 'notifications', 'column' => 'user_id', 'ref_table' => 'users'],
    ['table' => 'notification_preferences', 'column' => 'user_id', 'ref_table' => 'users'],
    ['table' => 'notification_deliveries', 'column' => 'notification_id', 'ref_table' => 'notifications'],
];

foreach ($fkChecks as $check) {
    try {
        $result = Database::select(
            "SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$check['table'], $check['column']]
        );
        
        if (!empty($result)) {
            $refTable = $result[0]['REFERENCED_TABLE_NAME'];
            echo "  ✅ {$check['table']}.{$check['column']} → {$refTable} FK exists\n";
            $results[] = "{$check['table']}.{$check['column']} FK: OK";
        } else {
            echo "  ❌ {$check['table']}.{$check['column']} → {$check['ref_table']} FK NOT FOUND\n";
            $errors[] = "{$check['table']}.{$check['column']} FK: Missing";
        }
    } catch (\Exception $e) {
        echo "  ⚠️  Error: " . $e->getMessage() . "\n";
        $errors[] = "FK check error: " . $e->getMessage();
    }
}

// Test 4: Verify indexes
echo "\nTest 4: Verifying indexes for performance...\n";

$indexChecks = [
    ['table' => 'notifications', 'index' => 'notifications_user_unread_idx'],
    ['table' => 'notifications', 'index' => 'notifications_type_idx'],
    ['table' => 'notification_preferences', 'index' => 'notification_preferences_user_event_unique'],
    ['table' => 'notification_deliveries', 'index' => 'notification_deliveries_status_idx'],
];

foreach ($indexChecks as $check) {
    try {
        $result = Database::selectOne(
            "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [$check['table'], $check['index']]
        );
        
        if ($result) {
            echo "  ✅ {$check['table']}.{$check['index']} index exists\n";
            $results[] = "{$check['table']}.{$check['index']}: OK";
        } else {
            echo "  ❌ {$check['table']}.{$check['index']} index NOT FOUND\n";
            $errors[] = "{$check['table']}.{$check['index']}: Missing";
        }
    } catch (\Exception $e) {
        echo "  ⚠️  Error: " . $e->getMessage() . "\n";
        $errors[] = "Index check error: " . $e->getMessage();
    }
}

// Test 5: Verify column in users table
echo "\nTest 5: Verifying new column in users table...\n";

try {
    $result = Database::selectOne(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'unread_notifications_count'"
    );
    
    if ($result) {
        echo "  ✅ users.unread_notifications_count column exists\n";
        $results[] = "users.unread_notifications_count: OK";
    } else {
        echo "  ❌ users.unread_notifications_count column NOT FOUND\n";
        $errors[] = "users.unread_notifications_count: Missing";
    }
} catch (\Exception $e) {
    echo "  ⚠️  Error: " . $e->getMessage() . "\n";
    $errors[] = "Column check error: " . $e->getMessage();
}

// Summary
echo "\n=== SUMMARY ===\n";
echo "Passed: " . count($results) . "\n";
echo "Failed: " . count($errors) . "\n";

if (!empty($errors)) {
    echo "\n❌ ERRORS FOUND:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\n❌ FIX 1 TEST FAILED\n";
    exit(1);
} else {
    echo "\n✅ ALL TESTS PASSED - FIX 1 VERIFIED\n";
    echo "\nDatabase schema consolidation is complete and correct:\n";
    echo "  ✅ All 4 notification tables present\n";
    echo "  ✅ All ENUM columns properly typed\n";
    echo "  ✅ All foreign keys configured\n";
    echo "  ✅ All indexes created\n";
    echo "  ✅ Users table extended with unread_count\n";
    echo "\nReady to proceed to Fix 2 ✅\n";
    exit(0);
}
?>
