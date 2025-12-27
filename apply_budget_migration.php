<?php
/**
 * Apply Budget Migration
 * Adds missing budget columns to projects table
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

try {
    echo "=== Applying Budget Migration ===\n\n";
    
    // Read and execute migration
    $sql = file_get_contents(__DIR__ . '/database/migrations/003_add_budget_to_projects.sql');
    
    // Split by semicolon and execute
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $stmt) {
        if (!empty($stmt) && strpos($stmt, '--') !== 0) {  // Skip comments
            echo "Executing: " . substr($stmt, 0, 80) . "...\n";
            Database::query($stmt);
        }
    }
    
    echo "\n✓ Migration applied successfully!\n";
    echo "\nVerifying columns...\n";
    
    // Verify columns exist
    $columns = Database::select("DESCRIBE projects");
    $hasbudget = false;
    $hasCurrency = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'budget') $hasbudget = true;
        if ($col['Field'] === 'budget_currency') $hasCurrency = true;
    }
    
    if ($hasbudget && $hasCurrency) {
        echo "✓ Both budget columns exist\n";
        echo "  - budget column: OK\n";
        echo "  - budget_currency column: OK\n";
    } else {
        echo "✗ Missing columns:\n";
        if (!$hasbudget) echo "  - budget column: MISSING\n";
        if (!$hasCurrency) echo "  - budget_currency column: MISSING\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: {$e->getMessage()}\n";
    exit(1);
}
