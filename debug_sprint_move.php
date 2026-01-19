<?php
require 'config/config.php';
require 'bootstrap/autoload.php';

use App\Core\Database;
use App\Services\SprintService;

// Setup
header('Content-Type: text/plain');

try {
    echo "--- STARTING DEBUG ---\n";

    // 1. Get a test issue and sprint
    $issue = Database::selectOne("SELECT * FROM issues LIMIT 1");
    if (!$issue)
        die("No issues found to test.\n");

    $sprint = Database::selectOne("SELECT * FROM sprints WHERE status != 'completed' LIMIT 1");
    if (!$sprint)
        die("No active sprints found to test.\n");

    echo "Testing Issue: ID {$issue['id']} (Current Sprint: {$issue['sprint_id']})\n";
    echo "Testing Sprint: ID {$sprint['id']} ({$sprint['name']})\n";

    $service = new SprintService();

    // 2. Test with USER ID 0 (Simulate Auth Failure)
    echo "\n[TEST 1] Attempting addIssueToSprint with User ID 0...\n";
    try {
        $service->addIssueToSprint($sprint['id'], $issue['id'], 0);
        echo "SUCCESS (Unexpected if FK exists)\n";
    } catch (Exception $e) {
        echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
    }

    // 3. Test with Valid User
    $user = Database::selectOne("SELECT * FROM users LIMIT 1");
    $userId = $user ? $user['id'] : 1;
    echo "\n[TEST 2] Attempting addIssueToSprint with User ID $userId...\n";

    try {
        // First ensure it's removed?
        // Database::update('issues', ['sprint_id' => null], 'id = ?', [$issue['id']]);

        $service->addIssueToSprint($sprint['id'], $issue['id'], $userId);
        echo "SUCCESS\n";
    } catch (Exception $e) {
        echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
    }

    // 4. Test Recursive Add (Simulate Dragging same issue twice)
    echo "\n[TEST 3] Attempting RECURSIVE addIssueToSprint (Duplicate Check)...\n";
    try {
        $service->addIssueToSprint($sprint['id'], $issue['id'], $userId);
        echo "SUCCESS (Idempotency working)\n";
    } catch (Exception $e) {
        echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
    }

} catch (Exception $mainE) {
    echo "CRITICAL SCRIPT ERROR: " . $mainE->getMessage() . "\n";
}
