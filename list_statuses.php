<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Starting script...\n";

try {
    require_once __DIR__ . '/bootstrap/app.php';
    echo "Bootstrap loaded.\n";
} catch (Throwable $e) {
    echo "Bootstrap failed: " . $e->getMessage() . "\n";
    exit(1);
}

use App\Core\Database;

try {
    echo "Querying database...\n";
    $statuses = Database::fetchAll("SELECT * FROM statuses ORDER BY id");
    echo "Found " . count($statuses) . " statuses:\n";
    foreach ($statuses as $status) {
        echo "[ID: {$status['id']}] Name: {$status['name']} (Category: {$status['category']})\n";
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
