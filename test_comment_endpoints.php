<?php
/**
 * Test Comment Endpoints
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "=== TESTING COMMENT ENDPOINTS ===\n\n";

// Get a recent issue with comments
$issue = Database::selectOne(
    "SELECT i.id, i.issue_key, i.project_id FROM issues i LIMIT 1"
);

if (!$issue) {
    echo "No issues found. Creating test data...\n";
    // This is just diagnostic
    exit(1);
}

echo "Issue: {$issue['issue_key']} (ID: {$issue['id']})\n\n";

// Get comments
$comments = Database::select(
    "SELECT c.`id`, c.body, c.created_at FROM comments c WHERE c.issue_id = ? ORDER BY created_at DESC",
    [$issue['id']]
);

if (empty($comments)) {
    echo "No comments found.\n";
} else {
    echo "Found " . count($comments) . " comments:\n";
    foreach ($comments as $comment) {
        echo "  - Comment #{$comment['id']}: {$comment['body']}\n";
    }
}

echo "\n";

// Test route matching
echo "=== TESTING ROUTE MATCHING ===\n";
echo "Routes defined:\n";
echo "  PUT /comments/{id} → CommentController::update\n";
echo "  DELETE /comments/{id} → CommentController::destroy\n\n";

// The issue: the router needs to see a PUT/DELETE request to /comments/5
// Let's verify the router can be instantiated
echo "Creating test request...\n";

// Simulate a PUT request to /comments/5
if (!empty($comments)) {
    $commentId = $comments[0]['id'];
    echo "\nSimulating PUT /jira_clone_system/public/comments/$commentId\n";
    echo "Expected route pattern: /comments/{id}\n";
    echo "Router should extract: id=$commentId\n";
}

echo "\nNote: If the endpoint is returning 404,  it's because:\n";
echo "1. The route is not matching, OR\n";
echo "2. The comment was not found in the database\n\n";

// Check if there are any recently deleted comments
$deletedComments = Database::select(
    "SELECT c.`id`, c.body, c.deleted_at FROM comments c WHERE c.issue_id = ? AND c.deleted_at IS NOT NULL ORDER BY c.deleted_at DESC LIMIT 5",
    [$issue['id']]
);

if (!empty($deletedComments)) {
    echo "Recently deleted comments (should be empty for this issue):\n";
    foreach ($deletedComments as $c) {
        echo "  - Comment #{$c['id']}: {$c['body']} (deleted at {$c['deleted_at']})\n";
    }
}

echo "\n";
?>
