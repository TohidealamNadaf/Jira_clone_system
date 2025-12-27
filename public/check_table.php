<?php
require_once __DIR__ . '/../bootstrap/app.php';
use App\Core\Database;

header('Content-Type: text/plain');

function describeTable($tableName)
{
    try {
        echo "Table: $tableName\n";
        $cols = Database::select("SHOW COLUMNS FROM `$tableName`");
        foreach ($cols as $col) {
            echo " - " . $col['Field'] . " (" . $col['Type'] . ") Default: [" . $col['Default'] . "] Null: " . $col['Null'] . "\n";
        }
    } catch (Exception $e) {
        echo "Error describing $tableName: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

describeTable('issue_attachments');
