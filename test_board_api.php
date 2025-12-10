<?php
/**
 * Test board API drag-and-drop functionality
 */
declare(strict_types=1);

require 'bootstrap/autoload.php';
$app = require 'bootstrap/app.php';

$db = app()->getContainer()->get('database');

echo "=== BOARD DRAG & DROP API TEST ===\n\n";

// 1. Get test project
echo "1. Looking for test project...\n";
$project = $db->selectOne("SELECT id, `key`, name FROM projects LIMIT 1");
if ($project) {
    echo "   ✓ Found project: {$project['key']} - {$project['name']}\n";
} else {
    echo "   ✗ No projects found\n";
    exit(1);
}

// 2. Get issues for project
echo "\n2. Looking for issues in project {$project['key']}...\n";
$issues = $db->select("
    SELECT i.id, i.issue_key, i.status_id, s.name as status_name
    FROM issues i
    LEFT JOIN statuses s ON i.status_id = s.id
    WHERE i.project_id = ?
    LIMIT 1
", [$project['id']]);

if (!$issues) {
    echo "   ✗ No issues found\n";
    exit(1);
}

$issue = $issues[0];
echo "   ✓ Found issue: {$issue['issue_key']} (Status: {$issue['status_name']})\n";

// 3. Get available statuses
echo "\n3. Available statuses:\n";
$statuses = $db->select("SELECT id, name FROM statuses LIMIT 5");
foreach ($statuses as $status) {
    $marker = $status['id'] == $issue['status_id'] ? ' (current)' : '';
    echo "   - {$status['name']} (ID: {$status['id']})$marker\n";
}

// 4. Get a different status to transition to
echo "\n4. Finding target status for transition...\n";
$targetStatus = $db->selectOne("
    SELECT id, name FROM statuses 
    WHERE id != ? 
    LIMIT 1
", [$issue['status_id']]);

if ($targetStatus) {
    echo "   ✓ Target status: {$targetStatus['name']} (ID: {$targetStatus['id']})\n";
} else {
    echo "   ✗ No other status available\n";
    exit(1);
}

// 5. Check workflow transitions
echo "\n5. Checking workflow transitions...\n";
$transitions = $db->select("
    SELECT COUNT(*) as count FROM workflow_transitions
");
$transitionCount = $transitions[0]['count'] ?? 0;
echo "   - Total transitions in DB: $transitionCount\n";

if ($transitionCount == 0) {
    echo "   ⚠ No transitions configured. Fallback will allow any transition.\n";
} else {
    $isAllowed = $db->selectOne("
        SELECT 1 FROM workflow_transitions wt
        JOIN workflows w ON wt.workflow_id = w.id
        WHERE w.is_default = 1
        AND (wt.from_status_id = ? OR wt.from_status_id IS NULL)
        AND wt.to_status_id = ?
    ", [$issue['status_id'], $targetStatus['id']]);
    
    if ($isAllowed) {
        echo "   ✓ Transition {$issue['status_id']} → {$targetStatus['id']} is allowed\n";
    } else {
        echo "   ✗ Transition {$issue['status_id']} → {$targetStatus['id']} is NOT allowed\n";
        echo "   ℹ This will fail unless workflow transitions are empty (fallback enabled)\n";
    }
}

// 6. Test JavaScript URL format
echo "\n6. URL generation test:\n";
$baseUrl = config('app.url', 'http://localhost/jira_clone_system/public');
$apiUrl = rtrim($baseUrl, '/') . '/api/v1/issues/' . $issue['issue_key'] . '/transitions';
echo "   - Base URL: $baseUrl\n";
echo "   - Issue Key: {$issue['issue_key']}\n";
echo "   - API URL: $apiUrl\n";

// 7. Test IssueService
echo "\n7. Testing IssueService methods...\n";
try {
    $issueService = new \App\Services\IssueService();
    $issueData = $issueService->getIssueById($issue['id']);
    echo "   ✓ getIssueById() works\n";
} catch (Exception $e) {
    echo "   ✗ getIssueById() failed: {$e->getMessage()}\n";
}

// 8. Check if user has permission
echo "\n8. Testing permissions...\n";
echo "   - You need permission: issues.transition\n";
echo "   - This is typically granted to all authenticated users\n";

echo "\n=== SUMMARY ===\n";
echo "Setup is " . ($targetStatus ? "✓ READY" : "✗ INCOMPLETE") . "\n";
echo "\nNext steps:\n";
echo "1. Go to: /projects/{$project['key']}/board\n";
echo "2. Try dragging {$issue['issue_key']} to \"{$targetStatus['name']}\"\n";
echo "3. Open DevTools (F12) → Console to see drag-and-drop logs\n";
echo "4. Check Network tab for API calls to /api/v1/issues/...\n";
