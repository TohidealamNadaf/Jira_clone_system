<?php
/**
 * Fix Project Issue Count
 * 
 * Syncs the projects.issue_count with actual issue count in the issues table
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "<h1>Fix Project Issue Counts</h1>";

// Get all projects
$projects = Database::select("SELECT id, key, name, issue_count FROM projects");

echo "<h2>Current Project Counts</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Project</th><th>Stored Count</th><th>Actual Count</th><th>Status</th></tr>";

$fixedCount = 0;
$totalMismatches = 0;

foreach ($projects as $project) {
    // Get actual issue count
    $actualCount = (int) Database::selectValue(
        "SELECT COUNT(*) FROM issues WHERE project_id = ?",
        [$project['id']]
    );
    
    $storedCount = (int) $project['issue_count'];
    
    if ($actualCount !== $storedCount) {
        $totalMismatches++;
        // Fix it
        Database::update('projects', ['issue_count' => $actualCount], 'id = ?', [$project['id']]);
        $fixedCount++;
        $status = "✅ FIXED ($storedCount → $actualCount)";
    } else {
        $status = "✅ OK";
    }
    
    echo "<tr>";
    echo "<td><strong>{$project['key']}</strong> - {$project['name']}</td>";
    echo "<td>$storedCount</td>";
    echo "<td>$actualCount</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";

echo "<br><h2>Summary</h2>";
echo "<p><strong>Total Projects:</strong> " . count($projects) . "</p>";
echo "<p><strong>Projects with Mismatched Counts:</strong> $totalMismatches</p>";
echo "<p><strong>Projects Fixed:</strong> $fixedCount</p>";

if ($fixedCount > 0) {
    echo "<br><p style='color: green;'><strong>✅ All project issue counts have been synchronized!</strong></p>";
    echo "<p>For Baramati Project specifically:</p>";
    
    $baramati = Database::selectOne(
        "SELECT id, key, name, issue_count FROM projects WHERE key = 'BP' OR name LIKE '%Baramati%'"
    );
    
    if ($baramati) {
        echo "<ul>";
        echo "<li><strong>Project:</strong> {$baramati['key']} - {$baramati['name']}</li>";
        echo "<li><strong>Issue Count Now:</strong> {$baramati['issue_count']}</li>";
        echo "</ul>";
    }
} else {
    echo "<br><p style='color: green;'><strong>✅ All project counts are already correct!</strong></p>";
}

echo "<br><h2>Verification</h2>";
echo "<p><a href='/jira_clone_system/public/projects' target='_blank'>Go to Projects Page</a></p>";
echo "<p>The Baramati Project card should now show the correct issue count.</p>";

?>
