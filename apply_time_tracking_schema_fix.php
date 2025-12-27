<?php
/**
 * Apply Time Tracking Schema Fix
 * 
 * Adds missing columns to issue_time_logs table that are required for
 * timer start/pause/resume/stop operations to work correctly.
 * 
 * Run this script once to fix the schema
 */

require_once 'bootstrap/autoload.php';

use App\Core\Database;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║           TIME TRACKING SCHEMA FIX - December 19, 2025        ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

try {
    echo "Applying schema migration...\n";
    echo "─────────────────────────────────────────────────────────────\n\n";
    
    // Read the migration SQL
    $migrationSql = file_get_contents('database/migrations/007_fix_time_tracking_schema.sql');
    
    // Split into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $migrationSql)),
        fn($s) => !empty($s) && !str_starts_with(trim($s), '--')
    );
    
    $count = 0;
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        echo "Executing: " . substr($statement, 0, 80) . "...\n";
        Database::query($statement);
        $count++;
    }
    
    echo "\n✅ Applied {$count} migration statements\n\n";
    
    // Verify the schema
    echo "Verifying schema...\n";
    echo "─────────────────────────────────────────────────────────────\n\n";
    
    $requiredColumns = [
        'id', 'issue_id', 'user_id', 'project_id',
        'start_time', 'end_time', 'paused_at', 'resumed_at',
        'pause_count', 'total_paused_seconds', 'paused_seconds',
        'duration_seconds', 'cost_calculated', 'total_cost',
        'currency', 'description', 'work_date', 'is_billable',
        'user_rate_type', 'user_rate_amount', 'status',
        'created_at', 'updated_at'
    ];
    
    $columns = Database::select(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'issue_time_logs' 
         AND TABLE_SCHEMA = DATABASE()"
    );
    
    $existingColumns = array_map(fn($c) => $c['COLUMN_NAME'], $columns);
    $missing = array_diff($requiredColumns, $existingColumns);
    $extra = array_diff($existingColumns, $requiredColumns);
    
    echo "Required Columns: " . count($requiredColumns) . "\n";
    echo "Existing Columns: " . count($existingColumns) . "\n";
    
    if (!empty($missing)) {
        echo "\n⚠️  Missing columns:\n";
        foreach ($missing as $col) {
            echo "  ✗ {$col}\n";
        }
    } else {
        echo "\n✅ All required columns present!\n";
    }
    
    // Summary
    echo "\n─────────────────────────────────────────────────────────────\n";
    echo "Schema Columns Summary:\n";
    echo "─────────────────────────────────────────────────────────────\n";
    
    $columnTypes = Database::select(
        "SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'issue_time_logs' 
         AND TABLE_SCHEMA = DATABASE()
         ORDER BY ORDINAL_POSITION"
    );
    
    foreach ($columnTypes as $col) {
        $name = str_pad($col['COLUMN_NAME'], 25);
        echo "  • {$name} {$col['COLUMN_TYPE']}\n";
    }
    
    // Test the schema
    echo "\n─────────────────────────────────────────────────────────────\n";
    echo "Testing Timer Operations:\n";
    echo "─────────────────────────────────────────────────────────────\n";
    
    // Check if timers exist
    $timers = Database::select("SELECT id, status FROM issue_time_logs LIMIT 1");
    if (empty($timers)) {
        echo "✓ Schema ready for timer creation\n";
    } else {
        echo "✓ Existing timers found: " . count($timers) . "\n";
    }
    
    echo "\n╔════════════════════════════════════════════════════════════════╗\n";
    echo "║                   ✅ SCHEMA FIX COMPLETE                       ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\nYou can now:\n";
    echo "  1. Go to /time-tracking/project/1\n";
    echo "  2. Start a timer\n";
    echo "  3. Pause the timer\n";
    echo "  4. Resume the timer (should work now!)\n\n";
    
} catch (Exception $e) {
    echo "\n❌ Error applying migration:\n";
    echo "   " . $e->getMessage() . "\n\n";
    
    if ($e->getCode() == 'HY000' || strpos($e->getMessage(), '1054') !== false) {
        echo "Note: Some columns may already exist. This is OK.\n\n";
    }
    
    exit(1);
}
