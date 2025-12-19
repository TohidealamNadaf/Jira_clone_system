<?php
/**
 * Check Baramati Project Data
 */

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Baramati Project Data Audit</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin: 20px 0; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin: 20px 0; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 20px 0; }
        .action-link { 
            display: inline-block;
            padding: 10px 20px; 
            background: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px;
            margin: 10px 5px;
        }
        .action-link:hover { background: #0056b3; }
        ul { line-height: 1.8; }
        .mismatch { color: #dc3545; font-weight: bold; }
        .ok { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
<div class='container'>
    <h1>📊 Baramati Project Data Audit</h1>

<?php

try {
    // Find Baramati project
    $project = Database::selectOne(
        "SELECT id, `key`, name, issue_count FROM projects WHERE `key` = 'BP' OR name LIKE '%Baramati%'"
    );

    if (!$project) {
        echo "<div class='error'>";
        echo "<h2>❌ Error</h2>";
        echo "<p>Baramati project not found in the database!</p>";
        echo "</div>";
        exit;
    }

    echo "<h2>Project Information</h2>";
    echo "<ul>";
    echo "<li><strong>Key:</strong> {$project['key']}</li>";
    echo "<li><strong>Name:</strong> {$project['name']}</li>";
    echo "<li><strong>Stored issue_count:</strong> {$project['issue_count']}</li>";
    echo "</ul>";

    // Count actual issues
    $actualCount = Database::selectValue(
        "SELECT COUNT(*) FROM issues WHERE project_id = ?",
        [$project['id']]
    );

    echo "<h2>Actual Issues in Database</h2>";
    echo "<p><strong>Count: $actualCount issues</strong></p>";

    // List all issues
    $issues = Database::select(
        "SELECT id, issue_key, summary, status_id, priority_id FROM issues WHERE project_id = ? ORDER BY id",
        [$project['id']]
    );

    if (count($issues) > 0) {
        echo "<h3>Issues List:</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Key</th><th>Summary</th><th>Status</th><th>Priority</th></tr>";
        
        foreach ($issues as $issue) {
            $status = Database::selectValue("SELECT name FROM statuses WHERE id = ?", [$issue['status_id']]);
            $priority = Database::selectValue("SELECT name FROM issue_priorities WHERE id = ?", [$issue['priority_id']]);
            
            echo "<tr>";
            echo "<td>{$issue['id']}</td>";
            echo "<td><strong>{$issue['issue_key']}</strong></td>";
            echo "<td>{$issue['summary']}</td>";
            echo "<td>" . ($status ?: 'NULL') . "</td>";
            echo "<td>" . ($priority ?: 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: #666;'>No issues found in this project.</p>";
    }

    echo "<h2>Mismatch Status</h2>";
    if ($project['issue_count'] == $actualCount) {
        echo "<div class='success'>";
        echo "<h3>✅ CORRECT</h3>";
        echo "<p>Stored count matches actual count: <strong>$actualCount issues</strong></p>";
        echo "</div>";
    } else {
        echo "<div class='warning'>";
        echo "<h3 class='mismatch'>❌ MISMATCH DETECTED</h3>";
        echo "<p><strong>Stored count:</strong> {$project['issue_count']}</p>";
        echo "<p><strong>Actual count:</strong> $actualCount</p>";
        echo "<p><strong>Difference:</strong> " . ($project['issue_count'] - $actualCount) . " issues</p>";
        echo "<br>";
        echo "<p><strong>This needs to be fixed.</strong> Click the button below:</p>";
        echo "<a class='action-link' href='/jira_clone_system/public/fix-issue-count.php'>Fix Issue Count Now</a>";
        echo "</div>";
    }

} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Error Occurred</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

?>

    <h2>Navigation</h2>
    <p>
        <a class='action-link' href='/jira_clone_system/public/projects'>Back to Projects</a>
        <a class='action-link' href='/jira_clone_system/public/projects/BP/issues'>View Issues</a>
    </p>

</div>
</body>
</html>
