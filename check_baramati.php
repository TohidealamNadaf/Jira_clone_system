<?php
/**
 * Check if Baramati project exists in database
 */

require_once __DIR__ . '/bootstrap/app.php';

$pdo = \App\Core\Database::getConnection();

// Check for Baramati project
$sql = "SELECT * FROM projects WHERE name LIKE '%baramati%' OR `key` LIKE '%BARA%'";
$stmt = $pdo->query($sql);
$projects = $stmt->fetchAll(\PDO::FETCH_ASSOC);

echo "=== Baramati Project Search ===\n\n";

if (empty($projects)) {
    echo "❌ No Baramati project found in database\n\n";
} else {
    echo "✓ Found " . count($projects) . " matching project(s):\n\n";
    foreach ($projects as $project) {
        echo "- Project: {$project['name']} (Key: {$project['key']})\n";
        echo "  ID: {$project['id']}\n";
        echo "  Created: {$project['created_at']}\n";
        echo "  Archived: " . ($project['is_archived'] ? 'YES' : 'NO') . "\n\n";
    }
}

// Check all projects
echo "=== All Projects in Database ===\n\n";
$sql2 = "SELECT id, `key`, name, is_archived FROM projects ORDER BY created_at DESC";
$stmt2 = $pdo->query($sql2);
$allProjects = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

foreach ($allProjects as $proj) {
    $status = $proj['is_archived'] ? '[ARCHIVED]' : '[ACTIVE]';
    echo "- {$proj['key']}: {$proj['name']} $status\n";
}
?>
