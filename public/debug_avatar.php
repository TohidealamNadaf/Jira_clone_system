<?php
// public/debug_avatar.php
require_once __DIR__ . '/../bootstrap/app.php';

echo "<h1>Avatar Function Debug</h1>";

$filename = 'avatar_1_1766745340.png';
$generatedUrl = avatar($filename);

echo "<p>Filename: $filename</p>";
echo "<p>Generated URL: <strong>$generatedUrl</strong></p>";
echo "<p>EXPECTED: http://localhost:8081/Jira_clone_system/public/uploads/avatars/$filename</p>";

// Diagnostics
echo "<h2>Internals</h2>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "dirname(SCRIPT_NAME): " . dirname($_SERVER['SCRIPT_NAME']) . "<br>";

echo "<hr>";
echo "<img src='$generatedUrl' alt='Debug Avatar' style='border: 2px solid red; width: 100px;'>";
echo "<p>If the image above is broken, the generated URL is wrong or the server can't serve the file.</p>";
