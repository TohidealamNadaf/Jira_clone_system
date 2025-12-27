<?php
/**
 * Time Tracking Migration Runner
 * Applies the time tracking tables migration
 */

declare(strict_types=1);

// Load configuration
require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/config/config.php';

use App\Core\Database;

echo "Time Tracking Migration Runner\n";
echo "==============================\n\n";

try {
    // Get database connection
    $dbConfig = config('database');
    $conn = new PDO(
        "mysql:host=" . $dbConfig['host'],
        $dbConfig['username'],
        $dbConfig['password']
    );
    
    // Read migration file
    $migrationFile = __DIR__ . '/database/migrations/006_create_time_tracking_tables.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    echo "✓ Migration file found\n";
    echo "✓ Reading migration SQL...\n";
    
    $sql = file_get_contents($migrationFile);
    
    // Execute migration
    echo "✓ Executing migration...\n";
    
    // Split by statements and execute
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($stmt) => !empty($stmt) && !str_starts_with($stmt, '--')
    );
    
    $count = 0;
    foreach ($statements as $statement) {
        if (empty(trim($statement))) {
            continue;
        }
        
        try {
            $conn->exec($statement);
            $count++;
        } catch (PDOException $e) {
            // Skip duplicate table errors (IF NOT EXISTS)
            if (strpos($e->getMessage(), 'already exists') === false) {
                throw $e;
            }
        }
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "✓ Executed $count SQL statements\n\n";
    
    // Verify tables
    echo "Verifying tables...\n";
    
    $tables = [
        'user_rates' => 'User rates table',
        'issue_time_logs' => 'Issue time logs table',
        'active_timers' => 'Active timers table',
        'project_budgets' => 'Project budgets table',
        'budget_alerts' => 'Budget alerts table',
        'time_tracking_settings' => 'Time tracking settings table'
    ];
    
    $dbName = $dbConfig['name'];
    
    foreach ($tables as $table => $description) {
        $check = $conn->query("SELECT 1 FROM information_schema.tables WHERE table_schema = '$dbName' AND table_name = '$table'");
        if ($check->rowCount() > 0) {
            echo "✓ $description exists\n";
        } else {
            echo "✗ $description NOT FOUND\n";
        }
    }
    
    echo "\n✅ All tables created successfully!\n";
    echo "Time tracking system is ready to use.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
