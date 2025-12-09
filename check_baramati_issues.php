<?php
/**
 * Check Baramati Project Issues
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "<h2>Baramati Project Issues Debug</h2>";

// 1. Find Baramati project
echo "<h3>1. Finding Baramati Project</h3>";
$project = Database::selectOne(
    "SELECT id, key, name FROM projects WHERE name LIKE '%Baramati%' OR key LIKE '%Baramati%'"
);

if (!$project) {
    echo "❌ Baramati project not found<br>";
    echo "Available projects:<br>";
    $allProjects = Database::select("SELECT id, key, name FROM projects");
    foreach ($allProjects as $p) {
        echo "- {$p['key']}: {$p['name']}<br>";
    }
    exit;
}

echo "✅ Found: {$project['key']} - {$project['name']}<br>";
echo "Project ID: {$project['id']}<br><br>";

// 2. Get all issues in this project
echo "<h3>2. Issues in {$project['key']} project</h3>";
$issues = Database::select(
    "SELECT id, issue_key, summary FROM issues WHERE project_id = ? ORDER BY issue_key",
    [$project['id']]
);

if (empty($issues)) {
    echo "❌ No issues found in this project<br>";
    exit;
}

echo "✅ Found " . count($issues) . " issues:<br>";
echo "<ul>";
foreach ($issues as $issue) {
    echo "<li><strong>{$issue['issue_key']}</strong>: {$issue['summary']}</li>";
}
echo "</ul>";

// 3. Test first issue
echo "<h3>3. Testing First Issue Load</h3>";
$firstIssue = $issues[0];
echo "Testing: {$firstIssue['issue_key']}<br>";

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
        "SELECT i.*, 
                p.key as project_key, p.name as project_name,
                it.name as issue_type_name, it.icon as issue_type_icon,
                s.name as status_name
         FROM issues i
         LEFT JOIN projects p ON i.project_id = p.id
         LEFT JOIN issue_types it ON i.issue_type_id = it.id
         LEFT JOIN statuses s ON i.status_id = s.id
         WHERE i.issue_key = ?"
    );
    
    $stmt->execute([$firstIssue['issue_key']]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✅ Issue loads successfully<br>";
        echo "Title: {$result['summary']}<br>";
    } else {
        echo "❌ Issue doesn't load<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// 4. Access URLs
echo "<h3>4. Click to Access Issues</h3>";
echo "<p>Try these URLs:</p>";
echo "<ul>";
foreach ($issues as $issue) {
    $url = "http://" . $_SERVER['HTTP_HOST'] . "/jira_clone_system/public/issue/" . $issue['issue_key'];
    echo "<li><a href='{$url}' target='_blank'>{$issue['issue_key']}: {$issue['summary']}</a></li>";
}
echo "</ul>";
?>
