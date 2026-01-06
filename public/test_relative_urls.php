<?php
require_once '../bootstrap/app.php';

// Simulate exact same server context
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['SCRIPT_NAME'] = '/jira_clone_system/public/index.php';
$_SERVER['REQUEST_SCHEME'] = 'http';

echo '<h1>Relative URL Resolution Test</h1>';

// Test what happens with relative URLs
$testPaths = [
    'uploads/avatars/avatar_1_1767008522.png',
    '/uploads/avatars/avatar_1_1767008522.png',
    'avatars/avatar_1_1767008522.png',
    '../uploads/avatars/avatar_1_1767008522.png'
];

echo '<div style="background: #f0f0f0; padding: 15px; font-family: monospace;">';
foreach ($testPaths as $path) {
    echo '<h3>Input: "' . $path . '"</h3>';
    echo '<p>url() result: ' . url($path) . '</p>';
    echo '<p>Direct browser would resolve to: ';
    echo '<a href="' . url($path) . '" target="_blank">' . url($path) . '</a></p>';
    echo '<hr>';
}
echo '</div>';

// Test what dirname actually does
echo '<h2>dirname() behavior test</h2>';
echo '<div style="background: #e0e0e0; padding: 15px; font-family: monospace;">';
$tests = [
    '/jira_clone_system/public/index.php',
    '/index.php',
    '/path/to/index.php'
];

foreach ($tests as $test) {
    echo 'dirname("' . $test . '") = "' . dirname($test) . '"<br>';
}
echo '</div>';
?>