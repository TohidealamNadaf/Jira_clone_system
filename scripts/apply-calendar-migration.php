<?php
/**
 * Apply Calendar Migration
 * Adds start_date and end_date fields to issues table
 */

require_once __DIR__ . '/../bootstrap/app.php';

// Get database class (static methods)
use App\Core\Database;

echo "\n==============================================\n";
echo "Calendar Migration Script\n";
echo "==============================================\n\n";

try {
    // Check if columns already exist
    $columns = Database::select("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'issues' 
        AND COLUMN_NAME IN ('start_date', 'end_date')
    ");
    
    if (count($columns) >= 2) {
        echo "✓ Calendar fields already exist. No migration needed.\n";
        exit(0);
    }
    
    echo "Applying migration...\n\n";
    
    // Add date fields if they don't exist
    if (count($columns) == 0) {
        echo "1. Adding start_date and end_date columns...\n";
        Database::query("
            ALTER TABLE issues 
            ADD COLUMN start_date DATE DEFAULT NULL AFTER due_date,
            ADD COLUMN end_date DATE DEFAULT NULL AFTER start_date
        ");
        echo "   ✓ Columns added\n\n";
    }
    
    // Add indexes for performance
    echo "2. Adding indexes for performance...\n";
    try {
        Database::query("ALTER TABLE issues ADD INDEX idx_issues_start_date (start_date)");
        echo "   ✓ start_date index added\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate key') === false) {
            throw $e;
        }
        echo "   ✓ start_date index already exists\n";
    }
    
    try {
        Database::query("ALTER TABLE issues ADD INDEX idx_issues_end_date (end_date)");
        echo "   ✓ end_date index added\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate key') === false) {
            throw $e;
        }
        echo "   ✓ end_date index already exists\n";
    }
    
    echo "\n3. Backfilling data...\n";
    
    // Set start_date to 7 days before due_date for existing issues
    Database::query("
        UPDATE issues 
        SET start_date = DATE_SUB(due_date, INTERVAL 7 DAY) 
        WHERE due_date IS NOT NULL AND start_date IS NULL
    ");
    echo "   ✓ start_date backfilled\n";
    
    // Set end_date to due_date for existing issues
    Database::query("
        UPDATE issues 
        SET end_date = due_date 
        WHERE due_date IS NOT NULL AND end_date IS NULL
    ");
    echo "   ✓ end_date backfilled\n";
    
    // Verify
    echo "\n4. Verifying migration...\n";
    $stats = Database::selectOne("
        SELECT 
            COUNT(*) as total_issues,
            COUNT(CASE WHEN start_date IS NOT NULL THEN 1 END) as issues_with_start_date,
            COUNT(CASE WHEN end_date IS NOT NULL THEN 1 END) as issues_with_end_date,
            COUNT(CASE WHEN due_date IS NOT NULL THEN 1 END) as issues_with_due_date
        FROM issues
    ");
    
    echo "   Total issues: " . $stats['total_issues'] . "\n";
    echo "   Issues with start_date: " . $stats['issues_with_start_date'] . "\n";
    echo "   Issues with end_date: " . $stats['issues_with_end_date'] . "\n";
    echo "   Issues with due_date: " . $stats['issues_with_due_date'] . "\n";
    
    echo "\n✓ Migration completed successfully!\n";
    echo "==============================================\n\n";
    
} catch (\Exception $e) {
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
    echo "==============================================\n\n";
    exit(1);
}
