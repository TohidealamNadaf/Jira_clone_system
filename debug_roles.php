<?php
require_once 'bootstrap/autoload.php';

use App\Core\Database;

// Check roles in database
echo "=== ALL ROLES IN DATABASE ===\n";
$roles = Database::select("SELECT id, name, display_name, description, is_system FROM roles ORDER BY id");
foreach ($roles as $role) {
    echo "ID: {$role['id']}, Name: {$role['name']}, Display: {$role['display_name']}, System: {$role['is_system']}\n";
}
echo "\nTotal roles: " . count($roles) . "\n";
?>
