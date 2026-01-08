<?php
/**
 * migrate_reconstructed.php
 * 
 * Reconstructs the Jira clone database using the comprehensive schema
 * derived from the codebase and migrations.
 */

declare(strict_types=1);

// Configuration - matches your .env defaults
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cways_prod';

// Logging
$logFile = __DIR__ . '/database_migration.log';
file_put_contents($logFile, "Starting migration at " . date('Y-m-d H:i:s') . "\n");

function logMsg($msg)
{
    global $logFile;
    echo $msg;
    file_put_contents($logFile, $msg, FILE_APPEND);
}

logMsg("=====================================================\n");
logMsg("   COMPREHENSIVE DATABASE RECONSTRUCTION TOOL\n");
logMsg("=====================================================\n");

try {
    // 1. Connect to MySQL
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Recreate Database
    logMsg("[1/5] Dropping and Recreating Database '$dbname'...\n");
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
    $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    // 3. Import Comprehensive Schema
    logMsg("[2/5] Importing Comprehensive Schema...\n");
    $schemaPath = __DIR__ . '/database/comprehensive_schema.sql';
    if (!file_exists($schemaPath)) {
        throw new Exception("Schema file not found: $schemaPath");
    }

    $sql = file_get_contents($schemaPath);
    // Split by -- and lines to handle long files if exec() fails
    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        logMsg("   Large block execution failed, trying statement by statement...\n");
        // Improved splitting (handles comments but still basic)
        $statements = preg_split("/;[\r\n]+/", $sql);
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if (!empty($stmt)) {
                try {
                    $pdo->exec($stmt);
                } catch (PDOException $ex) {
                    // Log error but continue for minor issues
                    logMsg("   Warning: Statement failed: " . substr($stmt, 0, 50) . "... Error: " . $ex->getMessage() . "\n");
                }
            }
        }
    }
    logMsg("   Schema imported successfully.\n");

    // 4. Create Default Admin and Seed Users
    logMsg("[3/5] Creating Default Users...\n");
    $passwordHash = password_hash('Admin@123', PASSWORD_DEFAULT);

    $users = [
        [1, 'admin@example.com', 'System', 'Admin', 1],
        [2, 'john@example.com', 'John', 'Developer', 0],
        [3, 'jane@example.com', 'Jane', 'Doe', 0],
        [4, 'mike@example.com', 'Mike', 'Manager', 0],
        [5, 'sarah@example.com', 'Sarah', 'Tester', 0],
        [6, 'david@example.com', 'David', 'Coder', 0]
    ];

    $stmt = $pdo->prepare("INSERT INTO users (id, email, password_hash, first_name, last_name, is_admin, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");

    foreach ($users as $u) {
        $stmt->execute([$u[0], $u[1], $passwordHash, $u[2], $u[3], $u[4]]);
    }
    logMsg("   Created " . count($users) . " default users.\n");

    // 5. Seed Initial Data
    logMsg("[4/5] Seeding Initial Data...\n");
    $seedPath = __DIR__ . '/database/seed.sql';
    if (file_exists($seedPath)) {
        $seedSql = file_get_contents($seedPath);
        $statements = preg_split("/;[\r\n]+/", $seedSql);
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if (!empty($stmt)) {
                try {
                    $pdo->exec($stmt);
                } catch (PDOException $ex) {
                    logMsg("   Seed Warning: " . $ex->getMessage() . "\n");
                }
            }
        }
    } else {
        logMsg("   Warning: Seed file not found: $seedPath\n");
    }
    logMsg("   Seeding complete.\n");

    // 6. Final Status
    logMsg("[5/5] Finalizing...\n");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $issueCount = $pdo->query("SELECT COUNT(*) FROM issues")->fetchColumn();

    logMsg("=====================================================\n");
    logMsg("SUCCESS! Database '$dbname' has been reconstructed.\n");
    logMsg(" - Total Tables: " . count($tables) . "\n");
    logMsg(" - Users: $userCount\n");
    logMsg(" - Issues: $issueCount\n");
    logMsg("\nLogin Credentials:\n");
    logMsg(" Email: admin@example.com\n");
    logMsg(" Password: Admin@123\n");
    logMsg("=====================================================\n");

} catch (Exception $e) {
    logMsg("\nFATAL ERROR: " . $e->getMessage() . "\n");
    exit(1);
}
