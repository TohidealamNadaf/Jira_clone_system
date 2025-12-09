<?php
/**
 * Check Baramati Project Data
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "<h1>Baramati Project Data Audit</h1>";

// Find Baramati project
$project = Database::selectOne(
    "SELECT id, key, name, issue_count FROM projects WHERE key = 'BP' OR name LIKE '%Baramati%'"
);

if (!$project) {
    echo "Baramati project not found!";
    exit;
}

echo "<h2>Project Info</h2>";
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
echo "<p><strong>Count: $actualCount</strong></p>";

// List all issues
$issues = Database::select(
    "SELECT id, issue_key, summary, status_id, priority_id FROM issues WHERE project_id = ? ORDER BY id",
    [$project['id']]
);

if (count($issues) > 0) {
    echo "<h3>Issues List:</h3>";
    echo "<table border='1' cellpadding='10'>";
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
    echo "<p style='color: red;'>No issues found!</p>";
}

echo "<h2>Mismatch Status</h2>";
if ($project['issue_count'] == $actualCount) {
    echo "<p style='color: green;'><strong>✅ CORRECT:</strong> Stored count matches actual count ({$actualCount})</p>";
} else {
    echo "<p style='color: red;'><strong>❌ MISMATCH:</strong> Stored count ({$project['issue_count']}) ≠ Actual count ({$actualCount})</p>";
    echo "<p>Click the link below to fix this:</p>";
    echo "<p><a href='EXECUTE_FIX_NOW.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Click Here to Fix</a></p>";
}

?>
