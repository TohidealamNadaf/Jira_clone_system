<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Using empty password as per .env
$db = 'cways_mis';

echo "Attempting to connect to database '$db'...\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful!\n";

    echo "Attempting to list tables...\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "No tables found in database '$db'.\n";
    } else {
        echo "Tables found: " . count($tables) . "\n";
        foreach ($tables as $table) {
            echo "- $table: ";
            // Try to select 1 row to see if engine error happens
            try {
                $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
                echo "OK\n";
            } catch (Exception $e) {
                echo "ERROR: " . $e->getMessage() . "\n";
            }
        }
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
