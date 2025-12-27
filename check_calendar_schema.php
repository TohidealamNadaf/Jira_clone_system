<?php
require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

try {
    // Use static query method directly
    $stmt = Database::query("SHOW COLUMNS FROM issues");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $hasStartDate = false;
    $hasEndDate = false;

    foreach ($columns as $col) {
        if ($col['Field'] === 'start_date')
            $hasStartDate = true;
        if ($col['Field'] === 'end_date')
            $hasEndDate = true;
    }

    echo "Schema Check Results:\n";
    echo "start_date: " . ($hasStartDate ? "EXISTS" : "MISSING") . "\n";
    echo "end_date: " . ($hasEndDate ? "EXISTS" : "MISSING") . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
