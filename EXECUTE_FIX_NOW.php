<?php
/**
 * EXECUTE FIX NOW - Directly fixes the issue count mismatch
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fix Issue Count - Baramati Project</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #f0f0f0; }
        .mismatch { background: #fff3cd; }
        .fixed { background: #d4edda; }
        .ok { background: #e7f3ff; }
        .status { font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🔧 Fix Issue Count Mismatch</h1>
    <p>This script will sync the project issue counts with actual database counts.</p>
";

try {
    // Get all projects
    $projects = Database::select('SELECT id, key, name, issue_count FROM projects');
    
    echo "<h2>Checking All Projects</h2>";
    echo "<table>";
    echo "<tr><th>Project</th><th>Stored Count</th><th>Actual Count</th><th>Status</th></tr>";
    
    $fixedCount = 0;
    $totalMismatches = 0;
    
    foreach ($projects as $project) {
        // Get actual count
        $actualCount = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issues WHERE project_id = ?",
            [$project['id']]
        );
        
        $storedCount = (int) $project['issue_count'];
        $projectKey = $project['key'];
        $projectName = $project['name'];
        
        // Check for mismatch
        if ($storedCount !== $actualCount) {
            $totalMismatches++;
            
            // Fix it
            Database::update('projects', ['issue_count' => $actualCount], 'id = ?', [$project['id']]);
            $fixedCount++;
            
            echo "<tr class='mismatch'>";
            echo "<td><strong>$projectKey</strong><br>$projectName</td>";
            echo "<td>$storedCount</td>";
            echo "<td>$actualCount</td>";
            echo "<td class='status success'>✅ FIXED ($storedCount → $actualCount)</td>";
            echo "</tr>";
        } else {
            echo "<tr class='ok'>";
            echo "<td><strong>$projectKey</strong><br>$projectName</td>";
            echo "<td>$storedCount</td>";
            echo "<td>$actualCount</td>";
            echo "<td class='status'>✅ OK</td>";
            echo "</tr>";
        }
    }
    
    echo "</table>";
    
    echo "<h2>Summary</h2>";
    echo "<ul>";
    echo "<li><strong>Total Projects:</strong> " . count($projects) . "</li>";
    echo "<li><strong>Mismatches Found:</strong> $totalMismatches</li>";
    echo "<li><strong>Projects Fixed:</strong> <span class='success'>$fixedCount</span></li>";
    echo "</ul>";
    
    if ($fixedCount > 0) {
        echo "<h2 class='success'>✅ All project issue counts have been synchronized!</h2>";
        
        // Show Baramati specifically
        $baramati = Database::selectOne(
            "SELECT id, key, name, issue_count FROM projects WHERE key = 'BP' OR name LIKE '%Baramati%'"
        );
        
        if ($baramati) {
            $actualIssues = Database::selectValue(
                "SELECT COUNT(*) FROM issues WHERE project_id = ?",
                [$baramati['id']]
            );
            
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>Baramati Project Status</h3>";
            echo "<ul>";
            echo "<li><strong>Key:</strong> {$baramati['key']}</li>";
            echo "<li><strong>Name:</strong> {$baramati['name']}</li>";
            echo "<li><strong>Issue Count (Updated):</strong> <span class='success'>{$baramati['issue_count']}</span></li>";
            echo "<li><strong>Actual Issues in DB:</strong> <span class='success'>$actualIssues</span></li>";
            echo "</ul>";
            echo "</div>";
        }
        
        echo "<h2>✅ Next Steps</h2>";
        echo "<ol>";
        echo "<li><a href='/jira_clone_system/public/projects' target='_blank'>Go to Projects Page</a></li>";
        echo "<li>The Baramati Project card should now show <strong>2 issues</strong></li>";
        echo "<li>Click on Baramati Project to view the issues list</li>";
        echo "<li>All issues should now display correctly</li>";
        echo "</ol>";
    } else {
        echo "<h2 class='success'>✅ All project counts were already correct!</h2>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "<h2 class='error'>❌ Error Occurred</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div>
</body>
</html>";

?>
