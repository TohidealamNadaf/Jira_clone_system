<?php

declare(strict_types=1);

/**
 * Add Budget Columns to Projects Table
 * 
 * This script adds budget and budget_currency columns to the projects table
 * to enable project-level budget tracking for time tracking functionality.
 */

// Include bootstrap
require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

echo "🔄 Applying Project Budget Migration...\n\n";

try {
    $connection = Database::getConnection();
    
    // Get current table structure
    $columns = $connection->query("DESCRIBE projects")->fetchAll(\PDO::FETCH_ASSOC);
    $columnNames = array_column($columns, 'Field');
    
    echo "📋 Current projects table columns: " . count($columnNames) . "\n";
    
    // Check if budget column exists
    $hasBudget = in_array('budget', $columnNames);
    $hasBudgetCurrency = in_array('budget_currency', $columnNames);
    
    if ($hasBudget && $hasBudgetCurrency) {
        echo "✅ Budget columns already exist. Migration skipped.\n";
        exit(0);
    }
    
    // Add budget column
    if (!$hasBudget) {
        echo "➕ Adding 'budget' column...\n";
        $connection->exec(
            "ALTER TABLE `projects` 
             ADD COLUMN `budget` DECIMAL(12, 2) DEFAULT 0.00 
             COMMENT 'Project budget in default currency'"
        );
        echo "   ✅ 'budget' column added\n";
    }
    
    // Add budget_currency column  
    if (!$hasBudgetCurrency) {
        echo "➕ Adding 'budget_currency' column...\n";
        $connection->exec(
            "ALTER TABLE `projects` 
             ADD COLUMN `budget_currency` VARCHAR(3) DEFAULT 'USD'
             COMMENT 'Budget currency code'"
        );
        echo "   ✅ 'budget_currency' column added\n";
    }
    
    // Add index for performance
    echo "🔍 Adding index for budget queries...\n";
    try {
        $connection->exec("CREATE INDEX idx_projects_budget ON `projects` (`budget`)");
        echo "   ✅ Index created\n";
    } catch (\Exception $e) {
        // Index might already exist
        echo "   ℹ️  Index already exists (skipped)\n";
    }
    
    echo "\n✨ Migration completed successfully!\n";
    echo "📌 Projects table now supports budget tracking\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
    exit(1);
}
