<?php
// Simple diagnostic - no routing, direct database access
require '../bootstrap/autoload.php';

use App\Core\Database;

// Get user from session
session_start();
$userId = $_SESSION['user_id'] ?? null;

echo "<h2>Calendar Diagnostic</h2>";
echo "<p>User ID: " . ($userId ? $userId : "NOT AUTHENTICATED") . "</p>";

if (!$userId) {
    echo "<p style='color: red;'><strong>ERROR: Not authenticated. Login first.</strong></p>";
}

echo "<h3>Database Issues Check</h3>";

// Count total issues
$total = Database::selectValue("SELECT COUNT(*) FROM issues");
echo "<p>Total issues in database: <strong>$total</strong></p>";

// Show some issues
echo "<h4>Sample Issues:</h4>";
$issues = Database::select("
    SELECT 
        i.id,
        i.`key`,
        i.summary,
        i.due_date,
        i.start_date,
        i.end_date,
        p.`key` as project_key,
        p.name as project_name
    FROM issues i
    LEFT JOIN projects p ON i.project_id = p.id
    LIMIT 10
");

if (empty($issues)) {
    echo "<p style='color: red;'>No issues found in database!</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Key</th><th>Summary</th><th>Project</th><th>Due Date</th><th>Start Date</th><th>End Date</th></tr>";
    foreach ($issues as $issue) {
        echo "<tr>";
        echo "<td>{$issue['key']}</td>";
        echo "<td>{$issue['summary']}</td>";
        echo "<td>{$issue['project_key']} - {$issue['project_name']}</td>";
        echo "<td>" . ($issue['due_date'] ?? 'NULL') . "</td>";
        echo "<td>" . ($issue['start_date'] ?? 'NULL') . "</td>";
        echo "<td>" . ($issue['end_date'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>Projects Check</h3>";
$projects = Database::select("SELECT id, `key`, name FROM projects LIMIT 5");
if (empty($projects)) {
    echo "<p style='color: red;'>No projects found!</p>";
} else {
    echo "<p>Found " . count($projects) . " projects:</p>";
    foreach ($projects as $p) {
        echo "<li>{$p['key']} - {$p['name']}</li>";
    }
}

echo "<h3>API Test</h3>";
echo "<p>Open browser console (F12) and run:</p>";
echo "<pre>
fetch('/jira_clone_system/public/api/v1/calendar/projects', {
    credentials: 'include'
})
.then(r => r.json())
.then(d => console.log('Projects:', d))
.catch(e => console.error('Error:', e));

fetch('/jira_clone_system/public/api/v1/calendar/events?start=2025-01-01&end=2025-12-31', {
    credentials: 'include'
})
.then(r => r.json())
.then(d => console.log('Events:', d))
.catch(e => console.error('Error:', e));
</pre>";

echo "<h3>Quick Links</h3>";
echo "<ul>";
echo "<li><a href='/jira_clone_system/public/calendar'>Calendar Page</a></li>";
echo "<li><a href='/jira_clone_system/public/dashboard'>Dashboard</a></li>";
echo "<li><a href='/jira_clone_system/public/issues'>Issues List</a></li>";
echo "</ul>";
?>
