<?php
require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

try {
    $tables = Database::select("SHOW TABLES LIKE 'project_workflows'");
    if (!empty($tables)) {
        echo "Table 'project_workflows' EXISTS\n";
        $columns = Database::select("DESCRIBE project_workflows");
        print_r($columns);
    } else {
        echo "Table 'project_workflows' DOES NOT EXIST\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
