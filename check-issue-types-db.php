<?php
/**
 * Quick check: Are there issue types in the database?
 */

require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

echo "<h2>🔍 Database Check: Issue Types</h2>\n\n";

try {
    // Check if table exists
    $sql = "SELECT COUNT(*) as count FROM issue_types";
    $result = \App\Core\Database::select($sql);
    $count = $result[0]['count'] ?? 0;
    
    echo "Total issue types: <strong>$count</strong><br><br>";
    
    // Get all issue types
    $sql = "SELECT id, name, is_active FROM issue_types LIMIT 20";
    $types = \App\Core\Database::select($sql);
    
    if (empty($types)) {
        echo "<p style='color: red;'><strong>⚠️ WARNING: No issue types found!</strong></p>";
        echo "<p>You need to seed the database with issue types.</p>";
        echo "<p>Run: <code>php scripts/verify-and-seed.php</code></p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Active</th></tr>";
        foreach ($types as $type) {
            $active = $type['is_active'] ? '✅ Yes' : '❌ No';
            echo "<tr><td>" . $type['id'] . "</td><td>" . $type['name'] . "</td><td>$active</td></tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Database Error:</strong> " . $e->getMessage() . "</p>";
}

echo "\n<hr><p><a href='test-issue-types-endpoint.php'>Back to diagnostic page</a></p>";
?>
