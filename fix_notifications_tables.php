<?php
/**
 * Direct SQL execution for notification tables
 * This bypasses the Database class and executes SQL directly
 */

$config = require __DIR__ . '/config/database.php';

$connection = $config['default'] ?? 'mysql';
$db = $config['connections'][$connection] ?? [];

try {
    $pdo = new PDO(
        "mysql:host={$db['host']};port={$db['port']}",
        $db['username'],
        $db['password']
    );
    
    // Select the database
    $pdo->exec("USE {$db['database']}");
    
    echo "Connected to database: {$db['database']}\n\n";
    
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/database/migrations/fix_notifications_tables.sql');
    
    // Split into individual statements (handle comments and semicolons)
    $statements = preg_split('/;(?=\s*--|\s*$)/m', $sql);
    
    $count = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        
        // Skip empty statements and comments
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement . ';');
            $count++;
            echo "✓ Executed statement $count\n";
        } catch (PDOException $e) {
            // Ignore "already exists" errors for CREATE IF NOT EXISTS
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Table already exists (skipped)\n";
            } else if (strpos($e->getMessage(), 'Duplicate key') !== false) {
                echo "⚠ Constraint already exists (skipped)\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n=== VERIFICATION ===\n\n";
    
    // Verify tables
    $tables = ['notifications', 'notification_preferences', 'notification_deliveries', 'notifications_archive'];
    
    foreach ($tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo "✓ Table exists: $table\n";
            
            // Show columns
            $columns = $pdo->query("DESCRIBE $table");
            $cols = $columns->fetchAll(PDO::FETCH_COLUMN, 0);
            echo "  Columns: " . implode(", ", $cols) . "\n";
        } else {
            echo "✗ Table missing: $table\n";
        }
    }
    
    echo "\n✅ Migration complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
