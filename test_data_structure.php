<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Services\IssueService;

$issueService = new IssueService();

// Get issues for a project (adjust project_id as needed)
$result = $issueService->getIssues(['project_id' => 1]);

if (!empty($result['data'])) {
    $issue = $result['data'][0];
    
    echo "=== First Issue Data Structure ===\n";
    echo "Keys present:\n";
    foreach (array_keys($issue) as $key) {
        echo "  - $key\n";
    }
    
    echo "\n=== Assignee-related fields ===\n";
    echo "assignee_name: " . ($issue['assignee_name'] ?? 'NULL') . "\n";
    echo "assignee_avatar: " . ($issue['assignee_avatar'] ?? 'NULL') . "\n";
    
    echo "\n=== Reporter-related fields ===\n";
    echo "reporter_name: " . ($issue['reporter_name'] ?? 'NULL') . "\n";
    echo "reporter_avatar: " . ($issue['reporter_avatar'] ?? 'NULL') . "\n";
} else {
    echo "No issues found to test.\n";
}
?>
