<?php
/**
 * Apply Roadmap Tables Migration
 * 
 * Run: php scripts/apply-roadmap-migration.php
 */

declare(strict_types=1);

use App\Core\Database;

// Load application
require_once __DIR__ . '/../bootstrap/app.php';

echo "\n";
echo "========================================\n";
echo "Roadmap Tables Migration\n";
echo "========================================\n";

try {
    // Get migration SQL
    $migrationFile = __DIR__ . '/../database/migrations/003_create_roadmap_tables.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }

    $sql = file_get_contents($migrationFile);
    
    if (!$sql) {
        throw new Exception("Failed to read migration file");
    }

    // Split by semicolon and execute each statement
    $statements = array_filter(
        explode(';', $sql),
        fn($stmt) => !empty(trim($stmt))
    );

    $executedCount = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) {
            continue;
        }

        try {
            Database::exec($statement);
            $executedCount++;
            echo "✓ Executed statement $executedCount\n";
        } catch (Exception $e) {
            // Skip if table already exists
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Table already exists - skipping\n";
                continue;
            }
            throw $e;
        }
    }

    echo "\n";
    echo "✅ Migration completed successfully!\n";
    echo "Executed $executedCount statements\n";
    echo "\n";
    echo "Tables created:\n";
    echo "  • roadmap_items\n";
    echo "  • roadmap_item_sprints\n";
    echo "  • roadmap_dependencies\n";
    echo "  • roadmap_item_issues\n";
    echo "\n";
    echo "Next steps:\n";
    echo "  1. Navigate to: /projects/CWAYS/roadmap\n";
    echo "  2. Click 'Add Roadmap Item' to create your first roadmap item\n";
    echo "  3. Link sprints and issues to roadmap items\n";
    echo "\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "\nError details:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
