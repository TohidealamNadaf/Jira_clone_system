<?php
/**
 * Test script to verify projects are loading in Select2 dropdown
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/bootstrap/autoload.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Select2 Projects Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .success { color: green; padding: 10px; background: #e8f5e9; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #ffebee; margin: 10px 0; }
        .info { color: blue; padding: 10px; background: #e3f2fd; margin: 10px 0; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        table td, table th { border: 1px solid #ddd; padding: 10px; text-align: left; }
        table th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Select2 Dropdown - Projects Loading Test</h1>";

try {
    // Get projects from API
    $apiUrl = '/api/v1/projects?archived=false&per_page=100';
    
    echo "<div class='info'>Testing API endpoint: <code>$apiUrl</code></div>";
    
    // Load projects using the application's database
    $database = new \App\Core\Database();
    
    // Query projects
    $sql = "SELECT id, name, key, description FROM projects WHERE archived = 0 ORDER BY name LIMIT 100";
    $projects = $database->select($sql, []);
    
    if ($projects) {
        echo "<div class='success'>✓ Found " . count($projects) . " projects in database</div>";
        
        echo "<h2>Projects List</h2>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Key</th><th>Description</th></tr>";
        
        foreach ($projects as $project) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($project['id']) . "</td>";
            echo "<td>" . htmlspecialchars($project['name']) . "</td>";
            echo "<td>" . htmlspecialchars($project['key']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($project['description'] ?? '', 0, 50)) . "...</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Test JSON output
        echo "<h2>JSON Output (for API)</h2>";
        echo "<pre><code>";
        echo htmlspecialchars(json_encode(['items' => $projects], JSON_PRETTY_PRINT));
        echo "</code></pre>";
    } else {
        echo "<div class='error'>✗ No projects found in database</div>";
    }
    
    // Test Select2 HTML
    echo "<h2>HTML for Select2</h2>";
    echo "<div class='info'>Select2 should display these options:</div>";
    echo "<select style='width: 300px;'>";
    echo "<option value=''>Select Project...</option>";
    
    if ($projects) {
        foreach ($projects as $project) {
            echo "<option value='" . htmlspecialchars($project['id']) . "'>";
            echo htmlspecialchars($project['name'] . " (" . $project['key'] . ")");
            echo "</option>";
        }
    }
    
    echo "</select>";
    
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body>
</html>";
?>
