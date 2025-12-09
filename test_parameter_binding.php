<?php
/**
 * Test parameter binding to find the HY093 error
 */
require 'bootstrap/autoload.php';

use App\Core\Database;

echo "=== Testing Parameter Binding ===\n\n";

try {
    // Test 1: Insert with named parameters (used by Database::insert)
    echo "Test 1: INSERT with named parameters...\n";
    $commentId = Database::insert('comments', [
        'issue_id' => 107,
        'user_id' => 1,
        'body' => 'Test comment: ' . time(),
    ]);
    echo "✓ Insert successful. Comment ID: $commentId\n\n";
    
    // Test 2: Select with positional parameter (used by selectOne)
    echo "Test 2: SELECT with positional parameter (?)\n";
    $comment = Database::selectOne(
        "SELECT c.* FROM comments c WHERE c.id = ?",
        [$commentId]
    );
    
    if (!$comment) {
        throw new Exception("Comment not found");
    }
    echo "✓ Select successful. Comment body: " . substr($comment['body'], 0, 30) . "\n\n";
    
    // Test 3: Update with named parameters
    echo "Test 3: UPDATE with named parameters...\n";
    Database::update('issues', [
        'updated_at' => date('Y-m-d H:i:s'),
    ], 'id = :id', ['id' => 107]);
    echo "✓ Update successful\n\n";
    
    // Test 4: Select with JOIN and positional parameter
    echo "Test 4: SELECT with JOIN and positional parameter...\n";
    $commentWithUser = Database::selectOne(
        "SELECT c.*, u.display_name as author_name, u.avatar as author_avatar
         FROM comments c
         JOIN users u ON c.user_id = u.id
         WHERE c.id = ?",
        [$commentId]
    );
    
    if (!$commentWithUser) {
        throw new Exception("Comment with user not found");
    }
    echo "✓ Select with JOIN successful. Author: " . $commentWithUser['author_name'] . "\n\n";
    
    // Test 5: The problematic notification insert
    echo "Test 5: Notification INSERT (the problematic one)...\n";
    $notificationId = Database::insert('notifications', [
        'user_id' => 2,
        'type' => 'comment_added',
        'notifiable_type' => 'issue',
        'notifiable_id' => 107,
        'data' => json_encode([
            'issue_key' => 'BP-7',
            'comment_id' => $commentId,
            'actor_id' => 1,
        ]),
    ]);
    echo "✓ Notification insert successful. Notification ID: $notificationId\n\n";
    
    echo "✓✓✓ ALL TESTS PASSED ✓✓✓\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    
    if (method_exists($e, 'getCode')) {
        echo "Code: " . $e->getCode() . "\n";
    }
    exit(1);
}
