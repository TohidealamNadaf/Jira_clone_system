<?php
/**
 * Apply Roadmap Migration
 * Creates the roadmap tables: roadmap_items, roadmap_item_sprints, roadmap_dependencies, roadmap_item_issues
 */

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

echo "🚀 Applying Roadmap Migration...\n\n";

try {
    // Read the migration file
    $migrationSql = file_get_contents(__DIR__ . '/database/migrations/003_create_roadmap_tables.sql');
    
    if (!$migrationSql) {
        throw new Exception("Failed to read migration file");
    }

    // Get PDO connection
    $db = Database::getConnection();
    
    // Split SQL statements
    $statements = array_filter(
        array_map('trim', preg_split('/;(?=(?:[^\']*\'[^\']*\')*[^\']*$)/', $migrationSql)),
        fn($s) => !empty($s) && !str_starts_with($s, '--')
    );

    $count = 0;
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            $db->exec($statement);
            $count++;
        }
    }

    echo "✅ Migration applied successfully!\n";
    echo "📊 Executed $count SQL statements\n\n";

    // Verify tables exist
    echo "📋 Verifying tables...\n";
    
    $tables = [
        'roadmap_items',
        'roadmap_item_sprints',
        'roadmap_dependencies',
        'roadmap_item_issues'
    ];

    $result = Database::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'jiira_clonee_system'", []);
    $existingTables = array_column($result, 'TABLE_NAME');

    foreach ($tables as $table) {
        if (in_array($table, $existingTables)) {
            echo "  ✅ $table\n";
        } else {
            echo "  ❌ $table (MISSING)\n";
        }
    }

    echo "\n✨ Roadmap tables are ready!\n";
    echo "📌 You can now access: http://localhost:8081/jira_clone_system/public/projects/CWAYS/roadmap\n";

} catch (Exception $e) {
    echo "❌ Error applying migration:\n";
    echo "   " . $e->getMessage() . "\n";
    exit(1);
}
?>
