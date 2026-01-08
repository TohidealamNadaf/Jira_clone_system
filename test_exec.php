<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cways_prod', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "--- CHECKING TABLES ---\n";
    $tables = ['notification_dispatch_log', 'notifications', 'notification_deliveries'];
    foreach ($tables as $t) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$t'");
        echo "$t: " . ($stmt->fetch() ? "EXISTS" : "MISSING") . "\n";
    }

    echo "\n--- CHECKING NOTIFICATIONS COLUMNS ---\n";
    $stmt = $pdo->query("DESCRIBE notifications");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns: " . implode(", ", $cols) . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
