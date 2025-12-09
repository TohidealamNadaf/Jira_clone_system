<?php
/**
 * Debug script to test comment insertion
 */

require 'bootstrap/autoload.php';

use App\Core\Database;
use App\Services\IssueService;

$issueKey = isset($_GET['issue']) ? $_GET['issue'] : 'BP-7';
$userId = isset($_GET['user']) ? (int)$_GET['user'] : 1;
$body = isset($_GET['body']) ? $_GET['body'] : 'Test comment';

echo "=== Comment Debug Test ===\n";
echo "Issue Key: $issueKey\n";
echo "User ID: $userId\n";
echo "Body: $body\n\n";

try {
    // Step 1: Load issue
    echo "Step 1: Loading issue...\n";
    $issueService = new IssueService();
    $issue = $issueService->getIssueByKey($issueKey);
    
    if (!$issue) {
        echo "✗ Issue not found\n";
        exit(1);
    }
    
    echo "✓ Issue loaded: {$issue['issue_key']}\n";
    echo "  ID: {$issue['id']}\n";
    echo "  Project ID: {$issue['project_id']}\n\n";
    
    // Step 2: Check user exists
    echo "Step 2: Checking user...\n";
    $user = Database::selectOne("SELECT id, display_name FROM users WHERE id = ?", [$userId]);
    
    if (!$user) {
        echo "✗ User not found\n";
        exit(1);
    }
    
    echo "✓ User exists: {$user['display_name']}\n\n";
    
    // Step 3: Insert comment
    echo "Step 3: Inserting comment...\n";
    $commentId = Database::insert('comments', [
        'issue_id' => $issue['id'],
        'user_id' => $userId,
        'body' => $body,
    ]);
    
    echo "✓ Comment inserted with ID: $commentId\n\n";
    
    // Step 4: Retrieve comment
    echo "Step 4: Retrieving comment...\n";
    $comment = Database::selectOne(
        "SELECT c.*, u.display_name as author_name, u.avatar as author_avatar
         FROM comments c
         JOIN users u ON c.user_id = u.id
         WHERE c.id = ?",
        [$commentId]
    );
    
    if (!$comment) {
        echo "✗ Failed to retrieve comment\n";
        exit(1);
    }
    
    echo "✓ Comment retrieved:\n";
    echo "  ID: {$comment['id']}\n";
    echo "  Author: {$comment['author_name']}\n";
    echo "  Body: {$comment['body']}\n";
    echo "  Created: {$comment['created_at']}\n\n";
    
    echo "✓✓✓ ALL TESTS PASSED ✓✓✓\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
