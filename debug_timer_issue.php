<?php
// Debug script to check time tracking tables

require_once 'bootstrap/autoload.php';

use App\Core\Database;

echo "═══════════════════════════════════════════════════════════════\n";
echo "  DEBUGGING TIMER ISSUE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Check issue_time_logs table structure
echo "1. ISSUE_TIME_LOGS TABLE COLUMNS:\n";
echo "───────────────────────────────────────────────────────────────\n";
$columns = Database::select(
    "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
     FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'issue_time_logs' 
     AND TABLE_SCHEMA = DATABASE()"
);

foreach ($columns as $col) {
    $nullable = $col['IS_NULLABLE'] === 'YES' ? 'NULL' : 'NOT NULL';
    $default = $col['COLUMN_DEFAULT'] ? " DEFAULT {$col['COLUMN_DEFAULT']}" : '';
    echo "  • {$col['COLUMN_NAME']}: {$col['COLUMN_TYPE']} {$nullable}{$default}\n";
}

// Check for any paused timers
echo "\n2. CHECKING FOR PAUSED TIMERS:\n";
echo "───────────────────────────────────────────────────────────────\n";
$pausedTimers = Database::select(
    "SELECT id, user_id, issue_id, status, start_time, created_at 
     FROM issue_time_logs 
     WHERE status = 'paused' 
     ORDER BY created_at DESC"
);

if (empty($pausedTimers)) {
    echo "  ⚠ NO PAUSED TIMERS FOUND\n";
} else {
    echo "  Found " . count($pausedTimers) . " paused timer(s):\n";
    foreach ($pausedTimers as $timer) {
        echo "    - ID: {$timer['id']}, User: {$timer['user_id']}, Status: {$timer['status']}\n";
        echo "      Started: {$timer['start_time']}, Created: {$timer['created_at']}\n";
    }
}

// Check all timers
echo "\n3. ALL ACTIVE/PAUSED TIMERS:\n";
echo "───────────────────────────────────────────────────────────────\n";
$allTimers = Database::select(
    "SELECT id, user_id, issue_id, status, start_time, created_at 
     FROM issue_time_logs 
     WHERE status IN ('running', 'paused')
     ORDER BY created_at DESC"
);

if (empty($allTimers)) {
    echo "  ⚠ NO RUNNING OR PAUSED TIMERS FOUND\n";
} else {
    echo "  Found " . count($allTimers) . " timer(s):\n";
    foreach ($allTimers as $timer) {
        echo "    - ID: {$timer['id']}, User: {$timer['user_id']}, Status: {$timer['status']}\n";
    }
}

// Check active_timers table
echo "\n4. ACTIVE_TIMERS TABLE:\n";
echo "───────────────────────────────────────────────────────────────\n";
$activeTimers = Database::select(
    "SELECT id, user_id, issue_id, issue_time_log_id FROM active_timers"
);

if (empty($activeTimers)) {
    echo "  ⚠ NO ACTIVE TIMERS IN active_timers TABLE\n";
} else {
    echo "  Found " . count($activeTimers) . " active timer(s):\n";
    foreach ($activeTimers as $timer) {
        echo "    - ID: {$timer['id']}, User: {$timer['user_id']}, Time Log ID: {$timer['issue_time_log_id']}\n";
    }
}

// Test the resume query
echo "\n5. TEST RESUME QUERY FOR USER 1:\n";
echo "───────────────────────────────────────────────────────────────\n";
$testResume = Database::selectOne(
    "SELECT * FROM issue_time_logs
     WHERE user_id = ? AND status = 'paused'
     ORDER BY created_at DESC
     LIMIT 1",
    [1]
);

if ($testResume) {
    echo "  ✓ Query found a paused timer:\n";
    echo "    ID: {$testResume['id']}, Status: {$testResume['status']}\n";
} else {
    echo "  ✗ Query returned NO paused timer for user 1\n";
    echo "\n  Checking what exists for user 1:\n";
    $userTimers = Database::select(
        "SELECT id, user_id, status, start_time FROM issue_time_logs WHERE user_id = 1 LIMIT 5"
    );
    if (empty($userTimers)) {
        echo "    → No timers at all for user 1\n";
    } else {
        foreach ($userTimers as $t) {
            echo "    → Timer ID {$t['id']}: Status = '{$t['status']}'\n";
        }
    }
}

echo "\n6. CHECKING PAUSE OPERATION:\n";
echo "───────────────────────────────────────────────────────────────\n";
echo "  If you paused a timer, check if the status was actually updated.\n";
echo "  The pauseTimer() method should set status='paused'.\n";

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "Debug complete. Check the output above.\n";
echo "═══════════════════════════════════════════════════════════════\n";
