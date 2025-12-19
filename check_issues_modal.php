<?php
// Quick test to verify issues exist for modal

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;
use App\Services\IssueService;

try {
    // Check database
    echo "=== DATABASE VERIFICATION ===\n\n";
    
    // Count all issues
    $allCount = Database::query("SELECT COUNT(*) as count FROM issues WHERE project_id = 1")->fetch();
    echo "Total issues in project 1: " . ($allCount['count'] ?? 0) . "\n";
    
    // Count open issues
    $openCount = Database::query(
        "SELECT COUNT(*) as count FROM issues WHERE project_id = 1 AND status_id NOT IN (
            SELECT id FROM statuses WHERE name IN ('Closed', 'Resolved')
        )"
    )->fetch();
    echo "Open issues in project 1: " . ($openCount['count'] ?? 0) . "\n";
    
    // Show first 5 issues
    echo "\nFirst 5 issues:\n";
    $issues = Database::query(
        "SELECT i.id, i.key, i.summary, s.name as status FROM issues i
         LEFT JOIN statuses s ON i.status_id = s.id
         WHERE i.project_id = 1
         ORDER BY i.key
         LIMIT 5"
    )->fetchAll();
    
    foreach ($issues as $issue) {
        echo "  - {$issue['key']}: {$issue['summary']} [{$issue['status']}]\n";
    }
    
    // Test IssueService
    echo "\n=== SERVICE TEST ===\n\n";
    $issueService = new IssueService();
    $serviceIssues = $issueService->getIssues(['project_id' => 1], 'key', 'ASC', 1, 10);
    echo "IssueService returned: " . count($serviceIssues) . " issues\n";
    
    if (count($serviceIssues) > 0) {
        echo "First issue from service: {$serviceIssues[0]['key']} - {$serviceIssues[0]['summary']}\n";
        echo "Status name: {$serviceIssues[0]['status_name']}\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
