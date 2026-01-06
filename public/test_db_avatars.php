<?php
require __DIR__ . '/../bootstrap/app.php';

// Check database avatar values
$users = App\Core\Database::select('SELECT id, email, avatar FROM users WHERE avatar IS NOT NULL LIMIT 10');

echo "<h2>Database Avatar Values</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Email</th><th>Avatar (Database)</th><th>Generated URL</th><th>Visual Test</th></tr>";
foreach ($users as $user) {
    $generatedUrl = avatar($user['avatar']);
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "<td>" . htmlspecialchars($user['avatar']) . "</td>";
    echo "<td>" . htmlspecialchars($generatedUrl) . "</td>";
    echo "<td><img src='" . htmlspecialchars($generatedUrl) . "' style='max-width:40px;max-height:40px;' alt='FAILED' onerror='this.alt=\"❌ FAILED\"'></td>";
    echo "</tr>";
}
echo "</table>";

// Check projects
$projects = App\Core\Database::select('SELECT id, name, avatar FROM projects WHERE avatar IS NOT NULL LIMIT 5');
echo "<h2>Database Project Avatar Values</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Name</th><th>Avatar (Database)</th><th>Generated URL</th><th>Visual Test</th></tr>";
foreach ($projects as $project) {
    $generatedUrl = avatar($project['avatar']);
    echo "<tr>";
    echo "<td>{$project['id']}</td>";
    echo "<td>" . htmlspecialchars($project['name']) . "</td>";
    echo "<td>" . htmlspecialchars($project['avatar']) . "</td>";
    echo "<td>" . htmlspecialchars($generatedUrl) . "</td>";
    echo "<td><img src='" . htmlspecialchars($generatedUrl) . "' style='max-width:40px;max-height:40px;' alt='FAILED' onerror='this.alt=\"❌ FAILED\"'></td>";
    echo "</tr>";
}
echo "</table>";

// Show what SHOULD be stored in database
echo "<h2>Recommendation</h2>";
echo "<p>Avatar paths should be stored in database as: <code>/uploads/avatars/filename.png</code></p>";
echo "<p>The avatar() function will then convert them to full URLs like: <code>http://localhost:8081/Jira_clone_system/public/uploads/avatars/filename.png</code></p>";
