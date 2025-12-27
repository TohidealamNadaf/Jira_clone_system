<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../bootstrap/app.php';

try {
    echo "Checking tables...\n";
    $tables = \App\Core\Database::select("SHOW TABLES");
    foreach ($tables as $t) {
        $name = array_values($t)[0];
        if (str_contains($name, 'watch')) {
            echo "Found table: $name\n";
            $cols = \App\Core\Database::select("DESCRIBE $name");
            foreach ($cols as $col)
                echo "  " . $col['Field'] . "\n";
        }
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
