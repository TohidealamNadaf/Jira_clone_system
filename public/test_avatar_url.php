<?php
require __DIR__ . '/../bootstrap/app.php';

// Test avatar URL generation
$testPaths = [
    'avatar_1_1767008522.png',
    '/uploads/avatars/avatar_1_1767008522.png',
    'http://localhost:8081/Jira_clone_system/public/uploads/avatars/avatar_1_1767008522.png',
];

echo "<h2>Avatar URL Generation Test</h2>";
echo "<p>Current URL: " . url('/') . "</p>";
echo "<p>Base Path: " . basePath() . "</p>";

foreach ($testPaths as $path) {
    $result = avatar($path);
    echo "<p><strong>Input:</strong> " . htmlspecialchars($path) . "<br>";
    echo "<strong>Output:</strong> " . htmlspecialchars($result) . "<br>";
    echo "<strong>Test:</strong> <img src='" . htmlspecialchars($result) . "' style='max-width:50px;max-height:50px;' onerror='this.alt=\"FAILED\"'></p>";
}

// Check what's in the database
$users = App\Core\Database::select('SELECT id, email, avatar FROM users WHERE avatar IS NOT NULL LIMIT 5');
echo "<h2>Database Avatar Paths</h2>";
foreach ($users as $user) {
    echo "<p><strong>User {$user['id']} ({$user['email']}):</strong> " . htmlspecialchars($user['avatar']) . "<br>";
    $avatarUrl = avatar($user['avatar']);
    echo "<strong>Generated URL:</strong> " . htmlspecialchars($avatarUrl) . "<br>";
    echo "<strong>Test:</strong> <img src='" . htmlspecialchars($avatarUrl) . "' style='max-width:50px;max-height:50px;' onerror='this.alt=\"FAILED: " . htmlspecialchars($avatarUrl) . "\"'></p>";
}

// Check if files exist
echo "<h2>File System Check</h2>";
$avatarDir = __DIR__ . '/uploads/avatars/';
if (is_dir($avatarDir)) {
    $files = scandir($avatarDir);
    $files = array_filter($files, fn($f) => $f !== '.' && $f !== '..');
    echo "<p>Found " . count($files) . " files in uploads/avatars/</p>";
    echo "<ul>";
    foreach (array_slice($files, 0, 5) as $file) {
        $fullPath = $avatarDir . $file;
        $size = filesize($fullPath);
        echo "<li>$file (" . round($size / 1024, 2) . " KB)</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Directory does not exist: $avatarDir</p>";
}
