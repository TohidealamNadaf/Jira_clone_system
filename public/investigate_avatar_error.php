<?php
require_once '../bootstrap/app.php';

// Set proper server variables like in web context
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['SCRIPT_NAME'] = '/jira_clone_system/public/index.php';
$_SERVER['REQUEST_SCHEME'] = 'http';

$db = new \App\Core\Database();
$user = $db->selectOne('SELECT avatar, display_name FROM users WHERE id = 1');

echo '<h1>Avatar URL Investigation</h1>';

echo '<div class="test-section">';
echo '<h2>Database Avatar Value</h2>';
echo '<div class="code">';
echo 'User avatar from DB: ' . ($user['avatar'] ?? 'NULL') . PHP_EOL;
echo '</div>';
echo '</div>';

echo '<div class="test-section">';
echo '<h2>avatar() Function Test</h2>';
echo '<div class="code">';
echo 'Input: ' . ($user['avatar'] ?? 'NULL') . PHP_EOL;
echo 'avatar() result: ' . avatar($user['avatar'] ?? null) . PHP_EOL;
echo '</div>';
echo '</div>';

echo '<div class="test-section">';
echo '<h2>URL Generation Tests</h2>';
echo '<div class="code">';

// Test different scenarios
$testPaths = [
    $user['avatar'] ?? null,
    '/uploads/avatars/avatar_1_1767008522.png',
    'avatars/avatar_1_1767008522.png',
    'avatar_1_1767008522.png'
];

foreach ($testPaths as $path) {
    echo 'Path: "' . $path . '"' . PHP_EOL;
    echo '  avatar(): ' . avatar($path) . PHP_EOL;
    echo '  url(): ' . url($path) . PHP_EOL;
    echo '---' . PHP_EOL;
}
echo '</div>';
echo '</div>';

echo '<div class="test-section">';
echo '<h2>Error URL Analysis</h2>';
echo '<div class="code">';
echo 'Error URL 1: http://localhost:8080/Jira_clone_system/public/avatars/avatar_1_1767008522.png' . PHP_EOL;
echo 'Error URL 2: http://localhost:8080/uploads/avatars/avatar_1_1767008522.png' . PHP_EOL;
echo PHP_EOL;
echo 'Expected: http://localhost:8080/jira_clone_system/public/uploads/avatars/avatar_1_1767008522.png' . PHP_EOL;
echo '</div>';
echo '</div>';

echo '<div class="test-section">';
echo '<h2>File Existence Check</h2>';
echo '<div class="code">';
$filePath = __DIR__ . '/uploads/avatars/avatar_1_1767008522.png';
echo 'File path: ' . $filePath . PHP_EOL;
echo 'File exists: ' . (file_exists($filePath) ? 'YES' : 'NO') . PHP_EOL;
echo '</div>';
echo '</div>';
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
.test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; background: #f9f9f9; }
.code { background: #f0f0f0; padding: 10px; font-family: monospace; white-space: pre-wrap; }
</style>