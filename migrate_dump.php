<?php
// migrate_dump.php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cways_prod'; // Use fresh DB
$dumpFile = __DIR__ . '/database/jiira_clonee_system (2).sql';
$logFile = __DIR__ . '/migration_log.txt';

file_put_contents($logFile, "Starting migration at " . date('Y-m-d H:i:s') . "\n");

function logMsg($msg)
{
    global $logFile;
    echo $msg;
    file_put_contents($logFile, $msg, FILE_APPEND);
}

logMsg("=====================================================\n");
logMsg("       JIRA CLONE MIGRATION TOOL\n");
logMsg("=====================================================\n");

if (!file_exists($dumpFile)) {
    logMsg("ERROR: Dump file not found: $dumpFile\n");
    exit(1);
}

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    logMsg("[1/3] Resetting Database '$dbname'...\n");
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
    $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    logMsg("[2/3] Importing SQL Dump...\n");

    $statements = [];
    $currentStmt = '';
    $fp = fopen($dumpFile, 'r');
    while (($line = fgets($fp)) !== false) {
        $trimLine = trim($line);
        if ($trimLine === '' || strpos($trimLine, '--') === 0 || strpos($trimLine, '/*') === 0) {
            continue;
        }
        $currentStmt .= $line;
        if (substr(trim($line), -1) === ';') {
            $statements[] = $currentStmt;
            $currentStmt = '';
        }
    }
    fclose($fp);

    $total = count($statements);
    logMsg("   Found $total statements.\n");

    $count = 0;
    foreach ($statements as $stmt) {
        $count++;
        if ($count % 50 === 0) {
            logMsg("   Executed $count / $total...\n");
        }
        try {
            // Replace original DB name and any corrupted name references on the fly
            $stmt = str_ireplace(['`jiira_clonee_system`', '`cways_mis`'], "`$dbname`", $stmt);
            $pdo->exec($stmt);
        } catch (PDOException $e) {
            logMsg("   WARNING: Statement $count failed: " . $e->getMessage() . "\n");
        }
    }

    logMsg("[3/3] Verification...\n");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $tableCount = count($tables);
    logMsg("   Total Tables: $tableCount\n");

    if ($tableCount > 0) {
        logMsg("SUCCESS: Migration completed!\n");
    } else {
        logMsg("WARNING: No tables found after migration.\n");
    }

} catch (PDOException $e) {
    logMsg("FATAL ERROR: " . $e->getMessage() . "\n");
    exit(1);
}
