<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

try {
    $columns = Database::select("SHOW COLUMNS FROM project_documents");
    $found = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'description') {
            $found = true;
            break;
        }
    }

    if ($found) {
        echo "VERIFICATION: SUCCESS - Column 'description' exists.\n";
    } else {
        echo "VERIFICATION: FAILED - Column 'description' NOT found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
