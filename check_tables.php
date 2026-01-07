<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);

    $dbs = ['jiira_clonee_system', 'jira_clone'];
    foreach ($dbs as $db) {
        echo "Database: $db\n";
        try {
            $pdo->exec("USE `$db`");
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (empty($tables)) {
                echo "  No tables found.\n";
            } else {
                foreach ($tables as $table) {
                    echo "  - $table\n";
                }
            }
        } catch (Exception $e) {
            echo "  Error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "Global Error: " . $e->getMessage() . "\n";
}
