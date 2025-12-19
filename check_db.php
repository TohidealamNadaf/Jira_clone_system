<?php
require_once 'bootstrap/app.php';
use App\Core\Database;

$users = Database::select('SELECT id, email, display_name FROM users LIMIT 10');
echo 'Users: ' . count($users) . PHP_EOL;
foreach($users as $user) {
    echo '  ' . $user['id'] . ': ' . $user['email'] . ' - ' . $user['display_name'] . PHP_EOL;
}

$johnSmith = Database::selectOne('SELECT id FROM users WHERE email = ?', ['john.smith@example.com']);
echo 'John Smith ID: ' . ($johnSmith ? $johnSmith['id'] : 'not found') . PHP_EOL;

$roles = Database::select('SELECT id, name FROM roles ORDER BY name');
echo 'Roles: ' . count($roles) . PHP_EOL;
foreach($roles as $role) {
    echo '  ' . $role['id'] . ': ' . $role['name'] . PHP_EOL;
}

$developer = Database::selectOne('SELECT id FROM roles WHERE name = ?', ['Developer']);
echo 'Developer Role ID: ' . ($developer ? $developer['id'] : 'not found') . PHP_EOL;

$projects = Database::select('SELECT id, `key`, name FROM projects LIMIT 10');
echo 'Projects: ' . count($projects) . PHP_EOL;
foreach($projects as $project) {
    echo '  ' . $project['id'] . ': ' . $project['key'] . ' - ' . $project['name'] . PHP_EOL;
}

$members = Database::select('SELECT pm.*, u.display_name, r.name as role_name FROM project_members pm JOIN users u ON pm.user_id = u.id JOIN roles r ON pm.role_id = r.id WHERE pm.project_id = 1');
echo 'Project members for CWAYS: ' . count($members) . PHP_EOL;
foreach($members as $member) {
    echo '  ' . $member['display_name'] . ' - ' . $member['role_name'] . PHP_EOL;
}
?>
