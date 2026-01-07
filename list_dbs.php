<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Databases:\n";
    foreach ($databases as $db) {
        if ($db == 'information_schema' || $db == 'performance_schema' || $db == 'mysql' || $db == 'sys')
            continue;
        echo "- $db\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
