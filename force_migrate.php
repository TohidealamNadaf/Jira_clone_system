<?php
// force_migrate.php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cways_prod'; // Use fresh DB 
$dumpFile = __DIR__ . '/database/jiira_clonee_system (2).sql';

echo "FORCE MIGRATION START\n";
echo "Target: $dbname\n";

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Resetting database...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
    $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    if (!file_exists($dumpFile)) {
        die("Dump file not found: $dumpFile\n");
    }

    echo "Reading dump file...\n";
    $sql = file_get_contents($dumpFile);

    // Split by semicolon loosely
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $total = count($statements);
    echo "Found $total statements. Executing...\n";

    $count = 0;
    foreach ($statements as $stmt) {
        if (empty($stmt))
            continue;

        // Clean comments
        if (strpos($stmt, '/*') === 0 || strpos($stmt, '--') === 0)
            continue;

        try {
            $stmt = str_ireplace(['`jiira_clonee_system`', '`cways_mis`'], "`$dbname`", $stmt);
            $pdo->exec($stmt);
            $count++;
            if ($count % 50 === 0)
                echo "Executed $count...\n";
        } catch (Exception $e) {
            // echo "Error in statement: " . substr($stmt, 0, 50) . "... - " . $e->getMessage() . "\n";
        }
    }

    echo "Migration finished. Total executed: $count\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Total tables created: " . count($tables) . "\n";

} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
}
