<?php
require_once __DIR__ . '/src/Core/Database.php';
require_once __DIR__ . '/config/config.php';
use App\Core\Database;

try {
    $config = require __DIR__ . '/config/config.php';
    // Manually init DB if needed, but usually it's static

    $tables = Database::select("SHOW TABLES");
    echo "Tables:\n";
    print_r($tables);

    $sprintIssues = Database::select("DESCRIBE sprint_issues");
    echo "\nSprint Issues Schema:\n";
    print_r($sprintIssues);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
