<?php
/**
 * Simulate Complete Test Flow
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;
use App\Services\ProjectService;
use App\Services\IssueService;

$logFile = __DIR__ . '/storage/logs/full_test_simulation.log';
@mkdir(dirname($logFile), 0755, true);

function logMsg($msg) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $line = "[$timestamp] $msg\n";
    file_put_contents($logFile, $line, FILE_APPEND);
    echo $line;
}

try {
    logMsg("=== STARTING FULL TEST SIMULATION ===");
    logMsg("Testing project creation → issue creation → reload");
    
    $projectService = new ProjectService();
    $issueService = new IssueService();
    
    // Step 1: Create Project
    logMsg("\n--- STEP 1: Creating Project ---");
    $projectKey = 'SIM' . date('Hs');
    $projectData = [
        'key' => $projectKey,
        'name' => "Simulation Test " . date('Y-m-d H:i:s'),
        'description' => 'Test project for simulation',
    ];
    
    try {
        $project = $projectService->createProject($projectData, 1); // User ID 1
        logMsg("✓ Project created: {$project['key']} (ID: {$project['id']})");
        $projectId = $project['id'];
    } catch (\Exception $e) {
        logMsg("❌ Project creation failed: " . $e->getMessage());
        throw $e;
    }
    
    // Step 2: Verify project persists
    logMsg("\n--- STEP 2: Verifying Project Persistence ---");
    $verifyProject = Database::selectOne("SELECT id, `key` FROM projects WHERE id = ?", [$projectId]);
    if (!$verifyProject) {
        logMsg("❌ ERROR: Project does not exist after creation!");
        throw new \RuntimeException("Project was not persisted");
    }
    logMsg("✓ Project verified in database: {$verifyProject['key']}");
    
    // Step 3: Create Issue
    logMsg("\n--- STEP 3: Creating Issue ---");
    $issueData = [
        'project_id' => $projectId,
        'issue_type_id' => 3, // Task
        'summary' => 'Test Issue',
        'description' => 'Testing issue creation',
    ];
    
    try {
        $issue = $issueService->createIssue($issueData, 1); // User ID 1
        logMsg("✓ Issue created: {$issue['issue_key']} (ID: {$issue['id']})");
        $issueId = $issue['id'];
    } catch (\Exception $e) {
        logMsg("❌ Issue creation failed: " . $e->getMessage());
        throw $e;
    }
    
    // Step 4: Verify issue persists
    logMsg("\n--- STEP 4: Verifying Issue Persistence ---");
    $verifyIssue = Database::selectOne("SELECT id, issue_key FROM issues WHERE id = ?", [$issueId]);
    if (!$verifyIssue) {
        logMsg("❌ ERROR: Issue does not exist after creation!");
        throw new \RuntimeException("Issue was not persisted");
    }
    logMsg("✓ Issue verified in database: {$verifyIssue['issue_key']}");
    
    // Step 5: Verify we can fetch the issue with all joins (like the show page does)
    logMsg("\n--- STEP 5: Testing Issue Fetch (Simulating Show Page) ---");
    try {
        $fullIssue = $issueService->getIssueByKey($verifyIssue['issue_key']);
        if (!$fullIssue) {
            logMsg("❌ ERROR: Issue not found via getIssueByKey (this is what show page does)");
            logMsg("   This indicates a problem with the INNER JOIN to projects");
            throw new \RuntimeException("Issue fetch failed");
        }
        logMsg("✓ Issue fetched successfully with all data");
        logMsg("   Project Key: {$fullIssue['project_key']}");
        logMsg("   Issue Summary: {$fullIssue['summary']}");
    } catch (\Exception $e) {
        logMsg("❌ Issue fetch failed: " . $e->getMessage());
        throw $e;
    }
    
    // Step 6: Verify project still exists
    logMsg("\n--- STEP 6: Verifying Project Still Exists ---");
    $projectAfterIssue = Database::selectOne("SELECT id, `key` FROM projects WHERE id = ?", [$projectId]);
    if (!$projectAfterIssue) {
        logMsg("❌ ERROR: Project deleted after creating issue!");
        throw new \RuntimeException("Project was deleted");
    }
    logMsg("✓ Project still exists: {$projectAfterIssue['key']}");
    
    // Step 7: Add a comment
    logMsg("\n--- STEP 7: Adding Comment ---");
    try {
        $commentId = Database::insert('comments', [
            'issue_id' => $issueId,
            'user_id' => 1,
            'body' => 'Test comment',
        ]);
        logMsg("✓ Comment created (ID: $commentId)");
    } catch (\Exception $e) {
        logMsg("❌ Comment creation failed: " . $e->getMessage());
        throw $e;
    }
    
    // Step 8: Final verification
    logMsg("\n--- STEP 8: Final Verification ---");
    $finalProject = Database::selectOne("SELECT id, `key`, name FROM projects WHERE id = ?", [$projectId]);
    $finalIssue = Database::selectOne("SELECT id, issue_key FROM issues WHERE id = ?", [$issueId]);
    $finalComment = Database::selectOne("SELECT id, body FROM comments WHERE id = ?", [$commentId]);
    
    if ($finalProject && $finalIssue && $finalComment) {
        logMsg("✅ ALL TESTS PASSED!");
        logMsg("   Project: {$finalProject['key']} ({$finalProject['name']})");
        logMsg("   Issue: {$finalIssue['issue_key']}");
        logMsg("   Comment: {$finalComment['body']}");
    } else {
        logMsg("❌ FINAL VERIFICATION FAILED!");
        if (!$finalProject) logMsg("   - Project missing!");
        if (!$finalIssue) logMsg("   - Issue missing!");
        if (!$finalComment) logMsg("   - Comment missing!");
        throw new \RuntimeException("Final verification failed");
    }
    
    logMsg("\n=== TEST SIMULATION COMPLETE ===\n");
    
} catch (\Exception $e) {
    logMsg("\n❌ TEST FAILED: " . $e->getMessage());
    logMsg("   " . str_replace("\n", "\n   ", $e->getTraceAsString()));
    exit(1);
}

echo "\nLog saved to: $logFile\n";
?>
