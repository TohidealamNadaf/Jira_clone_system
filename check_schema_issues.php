<?php
require_once __DIR__ . '/bootstrap/app.php';
use App\Core\Database;

try {
    $columns = Database::select("DESCRIBE issues");
    foreach ($columns as $col) {
        if ($col['Field'] === 'description') {
            echo "Column: " . $col['Field'] . " | Type: " . $col['Type'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
