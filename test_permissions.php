<?php
require_once 'bootstrap/app.php';
use App\Core\Database;

// Check if admin user has permissions
$user = Database::selectOne('SELECT id, is_admin FROM users WHERE email = ?', ['admin@example.com']);
echo 'Admin user ID: ' . $user['id'] . ', is_admin: ' . $user['is_admin'] . PHP_EOL;

// Check project permissions
$permissions = Database::select('SELECT p.name, rp.role_id FROM permissions p JOIN role_permissions rp ON p.id = rp.permission_id WHERE p.slug LIKE ?', ['%manage%']);
echo 'Manage permissions: ' . count($permissions) . PHP_EOL;
foreach($permissions as $perm) {
    echo '  Role ID: ' . $perm['role_id'] . ' - ' . $perm['name'] . PHP_EOL;
}

// Check what permissions admin role has
$adminRolePerms = Database::select('SELECT p.slug FROM permissions p JOIN role_permissions rp ON p.id = rp.permission_id WHERE rp.role_id = 1');
echo 'Admin role permissions: ' . count($adminRolePerms) . PHP_EOL;
foreach($adminRolePerms as $perm) {
    echo '  ' . $perm['slug'] . PHP_EOL;
}
?>

