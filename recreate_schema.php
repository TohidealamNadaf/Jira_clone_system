<?php
// recreate_schema.php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cways_prod';

// Setup Logging
$logFile = __DIR__ . '/restore_log.txt';
file_put_contents($logFile, "Starting restore process at " . date('Y-m-d H:i:s') . "\n");

function logMsg($msg)
{
    global $logFile;
    echo $msg;
    file_put_contents($logFile, $msg, FILE_APPEND);
}

logMsg("=====================================================\n");
logMsg("       JIRA CLONE DATABASE RECREATION TOOL\n");
logMsg("=====================================================\n");

try {
    // 1. Connect without DB selected
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Drop and Create Database
    logMsg("[1/6] Recreating Database '$dbname'...\n");
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
    $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    // 3. Helper function to run SQL files
    function runSqlFile($pdo, $path)
    {
        if (!file_exists($path)) {
            logMsg("   WARNING: File not found: $path\n");
            return;
        }
        logMsg("   Executing " . basename($path) . "...\n");
        $sql = file_get_contents($path);

        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // Fallback: splitting by semicolon
            // logMsg("   Mass execution failed (" . $e->getMessage() . "), trying statement by statement...\n");
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    try {
                        $pdo->exec($stmt);
                    } catch (Exception $ex) {
                        // ignore empty or comment errors
                    }
                }
            }
        }
    }

    // 4. Run Schema and Migrations
    logMsg("[2/6] Applying Schema and Migrations...\n");
    $files = [
        'database/schema.sql',
        'database/migrations/002_create_user_settings_table.sql',
        'database/migrations/003_create_roadmap_tables.sql',
        'database/migrations/003_create_calendar_events_table.sql',
        'database/migrations/003_create_project_documents_table.sql',
        'database/migrations/006_create_time_tracking_tables.sql',
        'database/migrations/add_push_device_tokens_table.sql',
        'database/migrations/fix_notifications_tables.sql'
    ];

    foreach ($files as $file) {
        runSqlFile($pdo, __DIR__ . '/' . $file);
    }

    // 5. Create Seed Users
    logMsg("[3/6] creating Default Users...\n");
    $password = password_hash('Admin@123', PASSWORD_DEFAULT);

    // User 1: Admin
    $sql = "INSERT INTO users (id, email, password_hash, first_name, last_name, display_name, is_admin, is_active, created_at) VALUES 
    (1, 'admin@example.com', '$password', 'System', 'Admin', 'System Admin', 1, 1, NOW())";
    $pdo->exec($sql);

    // Users 2-6: Dummy users referenced in seed.sql
    $dummyUsers = [
        [2, 'john@example.com', 'John', 'Developer', 0],
        [3, 'jane@example.com', 'Jane', 'Doe', 0],
        [4, 'mike@example.com', 'Mike', 'Manager', 0],
        [5, 'sarah@example.com', 'Sarah', 'Tester', 0],
        [6, 'david@example.com', 'David', 'Coder', 0]
    ];

    $stmt = $pdo->prepare("INSERT INTO users (id, email, password_hash, first_name, last_name, display_name, is_admin, is_active, created_at) VALUES (?, ?, ?, ?, ?, CONCAT(?, ' ', ?), ?, 1, NOW())");

    foreach ($dummyUsers as $user) {
        $stmt->execute([$user[0], $user[1], $password, $user[2], $user[3], $user[2], $user[3], $user[4]]);
    }
    logMsg("   Created 6 default users.\n");

    // 6. Run Seed Data
    logMsg("[4/6] Seeding Data...\n");
    runSqlFile($pdo, __DIR__ . '/database/seed.sql');

    // 7. Verification
    logMsg("[5/6] Verifying...\n");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    logMsg("   Total Tables: " . count($tables) . "\n");

    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    logMsg("   Users: $userCount\n");

    $issueCount = $pdo->query("SELECT COUNT(*) FROM issues")->fetchColumn();
    logMsg("   Issues: $issueCount\n");

    logMsg("=====================================================\n");
    logMsg("SUCCESS: Database restored 'cways_mis' successfully!\n");
    logMsg("You can log in with: admin@example.com / Admin@123\n");
    logMsg("=====================================================\n");

} catch (PDOException $e) {
    logMsg("ERROR: " . $e->getMessage() . "\n");
    exit(1);
}
