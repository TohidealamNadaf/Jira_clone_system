<?php
require_once 'bootstrap/autoload.php';

use App\Core\Database;

echo "=== ALL ROLES IN DATABASE ===\n\n";
$roles = Database::select("SELECT id, name, slug, display_name, description, is_system FROM roles ORDER BY id");
foreach ($roles as $role) {
    echo "ID: {$role['id']}\n";
    echo "  Name: {$role['name']}\n";
    echo "  Slug: {$role['slug']}\n";
    echo "  Display: {$role['display_name']}\n";
    echo "  Is System: {$role['is_system']}\n";
    echo "  Description: {$role['description']}\n";
    echo "\n";
}

echo "\n=== ROLES RETURNED BY getAvailableRoles QUERY ===\n\n";
$filtered = Database::select(
    "SELECT id, name, slug, description
     FROM roles
     WHERE is_system = 0 OR slug IN ('project-admin', 'project-member', 'project-viewer')
     ORDER BY name ASC"
);
echo "Found " . count($filtered) . " roles:\n";
foreach ($filtered as $role) {
    echo "- {$role['name']}\n";
}
?>
