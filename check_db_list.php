<?php
$pdo = new PDO("mysql:host=localhost", "root", "");
echo "DATABASES:\n";
foreach ($pdo->query("SHOW DATABASES") as $row) {
    echo "- " . $row[0] . "\n";
}

$dbname = 'cways_prod';
try {
    $pdo->exec("USE `$dbname` text");
    echo "\nTABLES in $dbname:\n";
    foreach ($pdo->query("SHOW TABLES") as $row) {
        echo "- " . $row[0] . "\n";
    }
} catch (Exception $e) {
    echo "\nError accessing $dbname: " . $e->getMessage() . "\n";
}
