<?php
require_once 'bootstrap/app.php';
use App\Core\Database;

$perms = Database::select('SELECT id, slug FROM permissions');
echo 'Total permissions: ' . count($perms) . PHP_EOL;

$rolePerms = Database::select('SELECT * FROM role_permissions');
echo 'Total role_permissions: ' . count($rolePerms) . PHP_EOL;

$roles = Database::select('SELECT id, name FROM roles');
echo 'Total roles: ' . count($roles) . PHP_EOL;
?>




