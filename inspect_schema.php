<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cways_prod';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $stmt = $pdo->query("DESCRIBE roadmap_item_issues");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "TABLE STRUCTURE:\n";
    foreach ($columns as $col) {
        echo "{$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']} - {$col['Default']}\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
