<?php
/**
 * Test the complete comment flow
 */
require 'bootstrap/autoload.php';

use App\Core\Database;
use App\Services\IssueService;

echo "=== Testing Comment Flow ===\n\n";

// Get parameters
$issueKey = $_GET['issue'] ?? 'BP-7';
$userId = (int)($_GET['user'] ?? 1);
$commentBody = $_GET['body'] ?? 'Test comment: ' . date('Y-m-d H:i:s');

echo "Parameters:\n";
echo "- Issue: $issueKey\n";
echo "- User ID: $userId\n";
echo "- Body: $commentBody\n\n";

try {
    // Step 1: Get Issue
    echo "Step 1: Loading issue '$issueKey'...\n";
    $issueService = new IssueService();
    $issue = $issueService->getIssueByKey($issueKey);
    
    if (!$issue) {
        throw new Exception("Issue not found");
    }
    echo "✓ Issue loaded (ID: {$issue['id']})\n\n";
    
    // Step 2: Insert Comment
    echo "Step 2: Inserting comment...\n";
    $commentId = Database::insert('comments', [
        'issue_id' => $issue['id'],
        'user_id' => $userId,
        'body' => $commentBody,
    ]);
    echo "✓ Comment inserted (ID: $commentId)\n\n";
    
    // Step 3: Retrieve Comment
    echo "Step 3: Retrieving comment with user data...\n";
    $comment = Database::selectOne(
        "SELECT c.*, u.display_name as author_name, u.avatar as author_avatar
         FROM comments c
         JOIN users u ON c.user_id = u.id
         WHERE c.id = ?",
        [$commentId]
    );
    
    if (!$comment) {
        throw new Exception("Failed to retrieve inserted comment");
    }
    
    echo "✓ Comment retrieved:\n";
    echo "  - ID: {$comment['id']}\n";
    echo "  - Author: {$comment['author_name']}\n";
    echo "  - Body: {$comment['body']}\n";
    echo "  - Created: {$comment['created_at']}\n\n";
    
    // Step 4: Update Issue
    echo "Step 4: Updating issue timestamp...\n";
    Database::update('issues', [
        'updated_at' => date('Y-m-d H:i:s'),
    ], 'id = :id', ['id' => $issue['id']]);
    echo "✓ Issue updated\n\n";
    
    // Step 5: Verify Comment Exists
    echo "Step 5: Verifying comment in database...\n";
    $verify = Database::selectOne("SELECT id FROM comments WHERE id = ?", [$commentId]);
    if (!$verify) {
        throw new Exception("Verification failed - comment not found after insert");
    }
    echo "✓ Comment verified in database\n\n";
    
    echo "✓✓✓ SUCCESS - All steps passed!\n";
    echo "\nComment Details:\n";
    echo json_encode($comment, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
