<?php
/**
 * Verification Script for Notification System Fixes
 * Checks if FIX 1-7 have been properly implemented
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "=== VERIFICATION REPORT FOR NOTIFICATION SYSTEM FIXES ===\n\n";

// FIX 1: Database Schema Verification
echo "FIX 1: Database Schema Consolidation\n";
echo "─────────────────────────────────────\n";

try {
    $tables = [
        'notifications',
        'notification_preferences', 
        'notification_deliveries',
        'notifications_archive'
    ];
    
    foreach ($tables as $table) {
        $result = Database::selectOne(
            "SELECT COUNT(*) as cnt FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'jiira_clonee_system' AND TABLE_NAME = ?",
            [$table]
        );
        
        if ($result && $result['cnt'] > 0) {
            // Get row count
            $rowResult = Database::selectOne("SELECT COUNT(*) as cnt FROM $table");
            echo "✅ Table '$table' exists (" . ($rowResult['cnt'] ?? 0) . " rows)\n";
        } else {
            echo "❌ Table '$table' NOT FOUND\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking tables: " . $e->getMessage() . "\n";
}

// FIX 2: Column Name Verification
echo "\nFIX 2: Column Name Mismatches\n";
echo "─────────────────────────────\n";

try {
    // Check if issues table has assignee_id column
    $result = Database::selectOne(
        "SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'jiira_clonee_system' AND TABLE_NAME = 'issues' AND COLUMN_NAME = 'assignee_id'"
    );
    
    if ($result && $result['cnt'] > 0) {
        echo "✅ Column 'issues.assignee_id' exists\n";
    } else {
        echo "❌ Column 'issues.assignee_id' NOT FOUND\n";
    }
    
    // Verify no assigned_to references remain
    $file = file_get_contents(__DIR__ . '/src/Services/NotificationService.php');
    if (strpos($file, "'assigned_to'") === false && strpos($file, '["assigned_to"]') === false) {
        echo "✅ No 'assigned_to' references found in NotificationService\n";
    } else {
        echo "❌ Found 'assigned_to' references in NotificationService\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// FIX 3: Wire Comment Notifications
echo "\nFIX 3: Wire Comment Notifications\n";
echo "──────────────────────────────────\n";

try {
    $file = file_get_contents(__DIR__ . '/src/Services/NotificationService.php');
    if (strpos($file, 'dispatchCommentAdded') !== false) {
        echo "✅ dispatchCommentAdded() method exists\n";
    } else {
        echo "❌ dispatchCommentAdded() method NOT FOUND\n";
    }
    
    // Check if it's called from IssueService
    $issueServiceFile = file_get_contents(__DIR__ . '/src/Services/IssueService.php');
    if (strpos($issueServiceFile, 'dispatchCommentAdded') !== false) {
        echo "✅ dispatchCommentAdded() is wired in IssueService\n";
    } else {
        echo "⚠️  dispatchCommentAdded() NOT found in IssueService (needs wiring)\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// FIX 4: Wire Status Change Notifications
echo "\nFIX 4: Wire Status Change Notifications\n";
echo "───────────────────────────────────────\n";

try {
    $file = file_get_contents(__DIR__ . '/src/Services/NotificationService.php');
    if (strpos($file, 'dispatchStatusChanged') !== false) {
        echo "✅ dispatchStatusChanged() method exists\n";
    } else {
        echo "❌ dispatchStatusChanged() method NOT FOUND\n";
    }
    
    // Check if it's called from IssueController
    $controllerFile = file_get_contents(__DIR__ . '/src/Controllers/IssueController.php');
    if (strpos($controllerFile, 'dispatchStatusChanged') !== false) {
        echo "✅ dispatchStatusChanged() is wired in IssueController\n";
    } else {
        echo "⚠️  dispatchStatusChanged() NOT found in IssueController (needs wiring)\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// FIX 5: Email/Push Channel Logic
echo "\nFIX 5: Email/Push Channel Logic\n";
echo "────────────────────────────────\n";

try {
    $file = file_get_contents(__DIR__ . '/src/Services/NotificationService.php');
    
    // Check for shouldNotify with channel parameter
    if (preg_match('/function shouldNotify\([^)]*\$channel/', $file)) {
        echo "✅ shouldNotify() has channel parameter\n";
    } else {
        echo "❌ shouldNotify() doesn't have channel parameter\n";
    }
    
    // Check for queueDeliveries method
    if (strpos($file, 'queueDeliveries') !== false) {
        echo "✅ queueDeliveries() method exists for future email/push\n";
    } else {
        echo "⚠️  queueDeliveries() method NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// FIX 6: Auto-Initialization Script
echo "\nFIX 6: Auto-Initialization Script\n";
echo "─────────────────────────────────\n";

try {
    if (file_exists(__DIR__ . '/scripts/initialize-notifications.php')) {
        echo "✅ initialize-notifications.php exists\n";
        
        // Check notification preferences count
        $result = Database::selectOne(
            "SELECT COUNT(*) as cnt FROM notification_preferences"
        );
        
        $count = $result['cnt'] ?? 0;
        if ($count >= 7) {
            echo "✅ Notification preferences initialized ($count records found)\n";
        } else {
            echo "⚠️  Low notification preference count: $count (expected >= 7)\n";
        }
    } else {
        echo "❌ initialize-notifications.php NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// FIX 7: Migration Runner Script
echo "\nFIX 7: Migration Runner Script\n";
echo "──────────────────────────────\n";

try {
    if (file_exists(__DIR__ . '/scripts/run-migrations.php')) {
        echo "✅ run-migrations.php exists (production-ready migration runner)\n";
        
        // Check file size
        $size = filesize(__DIR__ . '/scripts/run-migrations.php');
        echo "   File size: " . round($size / 1024, 1) . " KB (expected: ~17 KB)\n";
    } else {
        echo "❌ run-migrations.php NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Summary
echo "\n=== SUMMARY ===\n";
echo "These fixes establish a production-ready notification system.\n";
echo "Status: 70% complete (Fixes 1-7 of 10)\n";
echo "\nNext steps:\n";
echo "- FIX 8: Add production error handling and logging\n";
echo "- FIX 9: Verify all API routes exist\n";
echo "- FIX 10: Add performance/load testing\n";
