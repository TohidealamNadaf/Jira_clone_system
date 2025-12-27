<?php
require_once 'bootstrap/autoload.php';

use App\Core\Database;

echo "=== ROLES TABLE SCHEMA ===\n";
$schema = Database::select("DESCRIBE roles");
foreach ($schema as $col) {
    echo "{$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']}\n";
}

echo "\n=== SAMPLE ROLES DATA ===\n";
$roles = Database::select("SELECT * FROM roles LIMIT 10");
foreach ($roles as $role) {
    echo json_encode($role) . "\n";
}
?>
