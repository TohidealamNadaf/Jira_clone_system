<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

$outputFile = __DIR__ . '/db_diagnosis.txt';
$output = "";

try {
    $output .= "--- TABLES ---\n";
    $tables = Database::select("SHOW TABLES");
    foreach ($tables as $table) {
        $val = array_values($table)[0];
        $output .= $val . "\n";
    }

    $output .= "\n--- project_documents COLUMNS ---\n";
    try {
        $cols = Database::select("SHOW COLUMNS FROM project_documents");
        foreach ($cols as $col) {
            $output .= $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    } catch (Exception $e) {
        $output .= "Error listing columns for project_documents: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    $output .= "CRITICAL ERROR: " . $e->getMessage() . "\n";
}

file_put_contents($outputFile, $output);
echo "Diagnosis complete. Check db_diagnosis.txt\n";
