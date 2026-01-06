<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

// Get first project
$projects = Database::select("SELECT id, key, name FROM projects LIMIT 1");
if (empty($projects)) {
    die("No projects found");
}

$project = $projects[0];
$projectId = $project['id'];

// Get members for this project
$members = Database::select("
    SELECT pm.user_id, u.email, u.display_name, u.avatar, u.first_name, pm.role_id
    FROM project_members pm
    JOIN users u ON pm.user_id = u.id
    WHERE pm.project_id = ?
", [$projectId]);

echo "<h2>Test: Project Members Avatar Debug</h2>\n";
echo "<p>Project: " . htmlspecialchars($project['name']) . " (" . htmlspecialchars($project['key']) . ")</p>\n";
echo "<p>Total members: " . count($members) . "</p>\n";

echo "<h3>Member Avatar Values:</h3>\n";
echo "<pre>\n";
foreach ($members as $member) {
    echo "User: {$member['display_name']} (ID: {$member['user_id']})\n";
    echo "Avatar value: " . ($member['avatar'] ?? 'NULL') . "\n";
    
    if (!empty($member['avatar'])) {
        // Test what the avatar() helper would return
        $avatarPath = $member['avatar'];
        if (str_contains($avatarPath, '/uploads/')) {
            $relativePath = substr($avatarPath, strpos($avatarPath, '/uploads/'));
            $testUrl = "http://localhost:8081/jira_clone_system/public{$relativePath}";
        } elseif (!str_contains($avatarPath, '/')) {
            $testUrl = "http://localhost:8081/jira_clone_system/public/uploads/avatars/{$avatarPath}";
        } else {
            $testUrl = "http://localhost:8081/jira_clone_system/public{$avatarPath}";
        }
        echo "Generated URL: {$testUrl}\n";
        
        // Check if file exists
        $filePath = __DIR__ . str_replace('http://localhost:8081/jira_clone_system/public', '', $testUrl);
        echo "File path: {$filePath}\n";
        echo "File exists: " . (file_exists($filePath) ? 'YES' : 'NO') . "\n";
    }
    echo "---\n";
}
echo "</pre>\n";
