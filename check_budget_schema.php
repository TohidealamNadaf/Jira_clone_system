<?php
$config = require __DIR__ . '/config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "--- PROJECTS TABLE SCHEMA ---\n";
    $stmt = $pdo->query("DESCRIBE projects");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "{$col['Field']} - {$col['Type']}\n";
    }

    echo "\n--- PROJECT #1 DATA ---\n";
    $stmt = $pdo->query("SELECT * FROM projects WHERE id = 1");
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($project);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
