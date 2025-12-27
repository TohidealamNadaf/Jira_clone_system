<?php
/**
 * Time Tracking Migration - Direct Execution
 */

echo "Time Tracking Migration\n";
echo "======================\n\n";

try {
    // Direct database config
    $dbHost = 'localhost';
    $dbPort = 3306;
    $dbName = 'jiira_clonee_system';
    $dbUser = 'root';
    $dbPass = '';
    
    // Connect
    $conn = new PDO(
        "mysql:host=$dbHost:$dbPort;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✓ Connected to MySQL\n";
    
    // Select database
    $conn->exec("USE `$dbName`");
    echo "✓ Using database: $dbName\n\n";
    
    // Read migration file
    $migrationFile = __DIR__ . '/database/migrations/006_create_time_tracking_tables.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    echo "✓ Reading migration file...\n";
    $sql = file_get_contents($migrationFile);
    
    // Remove USE statement (we already selected the DB)
    $sql = str_replace("USE `jiira_clonee_system`;", "", $sql);
    
    // Split statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($stmt) => !empty($stmt) && !str_starts_with($stmt, '--') && !str_starts_with($stmt, '/*')
    );
    
    echo "✓ Found " . count($statements) . " SQL statements\n";
    echo "✓ Executing migration...\n\n";
    
    $count = 0;
    foreach ($statements as $i => $statement) {
        $stmt = trim($statement);
        if (empty($stmt)) {
            continue;
        }
        
        try {
            $conn->exec($stmt);
            $count++;
            
            // Show progress for table creation
            if (strpos($stmt, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE.*?`(\w+)`/', $stmt, $matches);
                if ($matches) {
                    echo "  ✓ Created table: {$matches[1]}\n";
                }
            }
        } catch (PDOException $e) {
            // Ignore duplicate key/index errors - they're expected with IF NOT EXISTS
            if (strpos($e->getMessage(), 'Duplicate') === false && 
                strpos($e->getMessage(), 'already exists') === false &&
                strpos($e->getMessage(), 'key') === false) {
                throw $e;
            }
        }
    }
    
    echo "\n✅ Migration completed!\n";
    echo "✓ Executed $count statements\n\n";
    
    // Verify tables
    echo "Verifying tables:\n";
    echo str_repeat("-", 50) . "\n";
    
    $tables = [
        'user_rates' => 'User rates',
        'issue_time_logs' => 'Issue time logs',
        'active_timers' => 'Active timers',
        'project_budgets' => 'Project budgets',
        'budget_alerts' => 'Budget alerts',
        'time_tracking_settings' => 'Time tracking settings'
    ];
    
    $allExist = true;
    foreach ($tables as $table => $description) {
        $check = $conn->query("SELECT 1 FROM information_schema.tables WHERE table_schema = '$dbName' AND table_name = '$table'");
        if ($check && $check->rowCount() > 0) {
            echo "✓ $description ........................ OK\n";
        } else {
            echo "✗ $description ........................ MISSING\n";
            $allExist = false;
        }
    }
    
    echo str_repeat("-", 50) . "\n";
    
    if ($allExist) {
        echo "\n✅ All tables created successfully!\n";
        echo "✅ Time tracking system is ready to use.\n";
    } else {
        echo "\n⚠️  Some tables are missing. Check the migration.\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
?>
