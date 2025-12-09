<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;
use App\Services\IssueService;

// Test 1: Check database connection
echo "=== Test 1: Database Connection ===\n";
try {
    $count = Database::selectValue('SELECT COUNT(*) FROM issues');
    echo "✓ Database connected successfully\n";
    echo "Total issues in database: " . $count . "\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check if issues table has data
echo "\n=== Test 2: Check Issues Table ===\n";
$allIssues = Database::select('SELECT id, issue_key, summary FROM issues LIMIT 5');
echo "First 5 issues:\n";
if (empty($allIssues)) {
    echo "✗ No issues found in the database\n";
} else {
    foreach ($allIssues as $issue) {
        echo "  - {$issue['issue_key']}: {$issue['summary']}\n";
    }
}

// Test 3: Check projects table
echo "\n=== Test 3: Check Projects Table ===\n";
$projects = Database::select('SELECT id, key, name FROM projects');
echo "Available projects:\n";
if (empty($projects)) {
    echo "✗ No projects found\n";
} else {
    foreach ($projects as $proj) {
        echo "  - {$proj['key']} ({$proj['name']})\n";
    }
}

// Test 4: Try IssueService getIssues method
echo "\n=== Test 4: Test IssueService ===\n";
$issueService = new IssueService();

if (!empty($projects)) {
    $projectId = $projects[0]['id'];
    $result = $issueService->getIssues(['project_id' => $projectId]);
    echo "IssueService getIssues() result structure:\n";
    echo "  - Keys: " . implode(', ', array_keys($result)) . "\n";
    echo "  - Data count: " . count($result['data'] ?? []) . "\n";
    echo "  - Total: " . ($result['total'] ?? 0) . "\n";
    
    if (!empty($result['data'])) {
        echo "\nFirst issue from service:\n";
        $firstIssue = $result['data'][0];
        echo "  - issue_key: " . ($firstIssue['issue_key'] ?? 'N/A') . "\n";
        echo "  - summary: " . ($firstIssue['summary'] ?? 'N/A') . "\n";
    }
}

// Test 5: Check if there's a database name mismatch
echo "\n=== Test 5: Database Configuration ===\n";
$config = config('database');
echo "Database Name: " . $config['name'] . "\n";

echo "\n=== Debug Complete ===\n";
?>
