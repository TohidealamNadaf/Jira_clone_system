<?php
/**
 * Check projects in database
 * Run: php check_projects.php
 */

require_once __DIR__ . '/bootstrap/app.php';

try {
    $pdo = \App\Core\Database::getConnection();
    
    echo "\n=== SEARCHING FOR BARAMATI PROJECT ===\n\n";
    
    // Check for Baramati project (case-insensitive)
    $sql = "SELECT * FROM projects WHERE LOWER(name) LIKE '%baramati%' OR LOWER(`key`) LIKE '%bara%'";
    $stmt = $pdo->query($sql);
    $baramatiProjects = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    if (empty($baramatiProjects)) {
        echo "❌ NO BARAMATI PROJECT FOUND IN DATABASE\n\n";
    } else {
        echo "✅ FOUND " . count($baramatiProjects) . " PROJECT(S):\n\n";
        foreach ($baramatiProjects as $proj) {
            echo "  Name: {$proj['name']}\n";
            echo "  Key: {$proj['key']}\n";
            echo "  ID: {$proj['id']}\n";
            echo "  Created: {$proj['created_at']}\n";
            echo "  Archived: " . ($proj['is_archived'] ? 'YES' : 'NO') . "\n";
            echo "  Issues: {$proj['issue_count']}\n\n";
        }
    }
    
    echo "=== ALL PROJECTS IN DATABASE ===\n\n";
    
    // Get all projects
    $sql2 = "SELECT id, `key`, name, is_archived, issue_count, created_at FROM projects ORDER BY created_at DESC";
    $stmt2 = $pdo->query($sql2);
    $allProjects = $stmt2->fetchAll(\PDO::FETCH_ASSOC);
    
    if (empty($allProjects)) {
        echo "No projects found\n\n";
    } else {
        echo "Total Projects: " . count($allProjects) . "\n\n";
        foreach ($allProjects as $proj) {
            $status = $proj['is_archived'] ? '[ARCHIVED]' : '[ACTIVE]';
            echo "{$proj['key']}: {$proj['name']} $status\n";
            echo "   ID: {$proj['id']}, Issues: {$proj['issue_count']}, Created: {$proj['created_at']}\n\n";
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
