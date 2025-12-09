<?php
require 'bootstrap/autoload.php';

use App\Core\Database;

try {
    // Test 1: Check if comments table exists and has data
    $result = Database::select("SELECT COUNT(*) as cnt FROM comments", []);
    echo "✓ Comments table exists. Total comments: " . $result[0]['cnt'] . "\n";
    
    // Test 2: Test a sample query like IssueService uses
    $sample = Database::select(
        "SELECT c.*, u.display_name as author_name, u.avatar as author_avatar, u.id as author_id
         FROM comments c
         LEFT JOIN users u ON c.user_id = u.id
         LIMIT 5",
        []
    );
    
    if (count($sample) > 0) {
        echo "✓ Sample query successful. Found " . count($sample) . " comments.\n";
        echo "  First comment: " . json_encode($sample[0]) . "\n";
    } else {
        echo "✓ No comments yet (table is empty - this is fine).\n";
    }
    
    echo "\n✓✓✓ ALL CHECKS PASSED - COMMENTS TABLE IS WORKING ✓✓✓\n";
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
