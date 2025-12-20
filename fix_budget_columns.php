<?php
/**
 * Fix Budget Columns in Projects Table
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

try {
    echo "=== Adding Budget Columns to Projects ===\n\n";
    
    // Try to add budget column
    try {
        Database::query("ALTER TABLE `projects` ADD COLUMN `budget` DECIMAL(12,2) NULL DEFAULT NULL");
        echo "✓ Added budget column\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "✓ budget column already exists\n";
        } else {
            echo "✗ Error adding budget column: " . $e->getMessage() . "\n";
        }
    }
    
    // Try to add budget_currency column
    try {
        Database::query("ALTER TABLE `projects` ADD COLUMN `budget_currency` VARCHAR(3) DEFAULT 'USD'");
        echo "✓ Added budget_currency column\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "✓ budget_currency column already exists\n";
        } else {
            echo "✗ Error adding budget_currency column: " . $e->getMessage() . "\n";
        }
    }
    
    // Verify
    echo "\nVerifying columns...\n";
    $columns = Database::select("SHOW COLUMNS FROM projects WHERE Field IN ('budget', 'budget_currency')");
    
    echo "Current columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col['Field']}: {$col['Type']}\n";
    }
    
    if (count($columns) === 2) {
        echo "\n✓✓✓ SUCCESS! Both columns are ready. ✓✓✓\n";
    } else {
        echo "\n✗ Not all columns present\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Fatal Error: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}\n";
    echo "Line: {$e->getLine()}\n";
    exit(1);
}
