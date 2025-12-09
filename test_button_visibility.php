<?php
/**
 * Test script to debug comment edit/delete button visibility
 */

// Include the application bootstrap
require_once __DIR__ . '/bootstrap/app.php';

// Get the issue service
$issueService = new \App\Services\IssueService();

// Get a test issue - using the first available issue
$issues = $issueService->getIssues([], 'created_at', 'DESC', 1, 1);

if (empty($issues['data'])) {
    die('No issues found in database');
}

$issue = $issues['data'][0];
$fullIssue = $issueService->getIssueByKey($issue['issue_key']);

echo "=== Issue Information ===\n";
echo "Issue Key: " . $fullIssue['issue_key'] . "\n";
echo "Project ID: " . $fullIssue['project_id'] . "\n";
echo "Total Comments: " . count($fullIssue['comments']) . "\n\n";

// Simulate authentication
$_SESSION['_user'] = [
    'id' => 1,
    'email' => 'admin@example.com',
    'display_name' => 'Admin User',
    'role_id' => 1,
];

// Get current user
$currentUserId = isset($_SESSION['_user']) ? $_SESSION['_user']['id'] : null;
echo "Current User ID: " . ($currentUserId ?? 'null') . "\n\n";

// Check each comment
if (!empty($fullIssue['comments'])) {
    echo "=== Comment Analysis ===\n";
    foreach ($fullIssue['comments'] as $index => $comment) {
        echo "\nComment #{" . ($index + 1) . "}:\n";
        echo "  ID: " . $comment['id'] . "\n";
        echo "  Author ID (user_id): " . ($comment['user_id'] ?? 'NULL') . "\n";
        echo "  Author Name: " . ($comment['user']['display_name'] ?? 'Unknown') . "\n";
        echo "  Body: " . substr($comment['body'], 0, 50) . "...\n";
        
        // Check if buttons would be visible
        $isAuthor = $comment['user_id'] === $currentUserId;
        echo "  Is Author: " . ($isAuthor ? 'YES' : 'NO') . "\n";
        echo "  Has user_id field: " . (isset($comment['user_id']) ? 'YES' : 'NO') . "\n";
        echo "  Comment array keys: " . implode(', ', array_keys($comment)) . "\n";
    }
} else {
    echo "No comments found in issue\n";
}
?>
