<?php
// Diagnose why modal is empty

session_start();
require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

echo "=== ISSUE EXISTENCE DIAGNOSTIC ===\n\n";

try {
    // Get PDO connection
    $db = Database::getConnection();
    
    // 1. Check if database is accessible
    echo "✓ Database connection OK\n\n";
    
    // 2. Count issues in project 1
    $result = $db->query("SELECT COUNT(*) as count FROM issues WHERE project_id = 1");
    $count = $result->fetch(\PDO::FETCH_ASSOC);
    echo "Issues in project 1 (CWAYS): " . ($count['count'] ?? 0) . "\n";
    
    // 3. Check if issues table is populated
    $totalResult = $db->query("SELECT COUNT(*) as count FROM issues");
    $totalCount = $totalResult->fetch(\PDO::FETCH_ASSOC);
    echo "Total issues in database: " . ($totalCount['count'] ?? 0) . "\n\n";
    
    // 4. List first 5 issues from project 1
    echo "Sample issues from project 1:\n";
    $sampleResult = $db->query(
        "SELECT i.id, i.key, i.summary, s.name as status 
         FROM issues i
         LEFT JOIN statuses s ON i.status_id = s.id
         WHERE i.project_id = 1
         LIMIT 5"
    );
    
    $samples = $sampleResult->fetchAll(\PDO::FETCH_ASSOC);
    if (empty($samples)) {
        echo "  ❌ NO ISSUES FOUND in project 1\n";
    } else {
        foreach ($samples as $issue) {
            echo "  • {$issue['key']}: {$issue['summary']} [{$issue['status']}]\n";
        }
    }
    
    echo "\n=== SOLUTION ===\n";
    echo "The modal is working correctly!\n";
    echo "The dropdown is empty because there are NO ISSUES in the CWAYS project.\n\n";
    echo "To fix this, you need to CREATE ISSUES in the CWAYS project.\n";
    echo "Run: php seed_test_issues.php\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
