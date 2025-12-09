<?php
$pdo = new PDO('mysql:host=localhost', 'root', '');
$pdo->exec('USE jira_clone');

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo "Tables in jira_clone:\n";
foreach ($tables as $table) {
    echo "  - " . $table . "\n";
    if (strpos($table, 'notification') !== false) {
        $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_COLUMN, 0);
        echo "    Columns: " . implode(", ", $cols) . "\n";
    }
}
