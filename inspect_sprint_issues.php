<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cways_prod';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $stmt = $pdo->query("DESCRIBE sprint_issues");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "TABLE STRUCTURE: sprint_issues\n";
    foreach ($columns as $col) {
        echo "{$col['Field']} - {$col['Type']} - NULL:{$col['Null']} - KEY:{$col['Key']} - DEFAULT:{$col['Default']}\n";
    }

    echo "\nINDEXES:\n";
    $stmt = $pdo->query("SHOW INDEX FROM sprint_issues");
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($indexes as $idx) {
        echo "{$idx['Key_name']} - {$idx['Column_name']} - Unique: " . ($idx['Non_unique'] == 0 ? 'Yes' : 'No') . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
