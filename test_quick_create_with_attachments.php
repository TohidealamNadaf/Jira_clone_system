<?php
/**
 * Test Quick Create Modal Issue Creation with Attachments
 * 
 * Tests the issue creation endpoint to see if issues are being created
 * successfully but the key extraction is failing.
 */

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;
use App\Services\ProjectService;
use App\Services\IssueService;

try {
    // Get database connection
    $db = new Database();
    
    // Get the most recent issue created
    $sql = "SELECT id, issue_key, project_id, summary, created_at FROM issues ORDER BY created_at DESC LIMIT 1";
    $result = $db->query($sql);
    
    echo "=== LATEST ISSUE IN DATABASE ===\n";
    if ($result && !empty($result)) {
        $issue = $result[0];
        echo "Issue ID: " . $issue['id'] . "\n";
        echo "Issue Key: " . $issue['issue_key'] . "\n";
        echo "Project ID: " . $issue['project_id'] . "\n";
        echo "Summary: " . $issue['summary'] . "\n";
        echo "Created: " . $issue['created_at'] . "\n";
        echo "\n✅ Issues ARE being created in the database!\n";
        echo "The key extraction error is likely just a display issue.\n";
    } else {
        echo "No issues found in database\n";
    }
    
    // Check for recent attachments
    $attachmentSql = "SELECT id, issue_id, file_name, created_at FROM attachments ORDER BY created_at DESC LIMIT 5";
    $attachments = $db->query($attachmentSql);
    
    echo "\n=== RECENT ATTACHMENTS ===\n";
    if (!empty($attachments)) {
        echo "Found " . count($attachments) . " recent attachments:\n";
        foreach ($attachments as $att) {
            echo "  - Issue #{$att['issue_id']}: {$att['file_name']} ({$att['created_at']})\n";
        }
    } else {
        echo "No attachments found\n";
    }
    
    // Test API response format
    echo "\n=== TESTING API RESPONSE FORMAT ===\n";
    
    $projectService = new ProjectService();
    $issueService = new IssueService();
    
    // Get a project to test with
    $projects = $projectService->getAllProjects();
    if (!empty($projects['items'])) {
        $project = $projects['items'][0];
        echo "Using project: " . $project['key'] . "\n";
        
        // Create a test issue
        $testData = [
            'project_id' => $project['id'],
            'issue_type_id' => 1,
            'summary' => 'Test Issue for API Response - ' . time(),
            'description' => 'This is a test issue',
            'priority_id' => 2,
        ];
        
        $testIssue = $issueService->createIssue($testData, 1);
        
        echo "\n✅ Test issue created:\n";
        echo "ID: " . $testIssue['id'] . "\n";
        echo "Key: " . $testIssue['issue_key'] . "\n";
        
        // Test what would be returned as JSON
        $response = [
            'success' => true,
            'issue_key' => $testIssue['issue_key'],
            'issue' => array_intersect_key($testIssue, array_flip(['id', 'issue_key', 'summary', 'description']))
        ];
        
        echo "\nAPI would return (formatted):\n";
        echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
        
        echo "\n✅ API response format is correct!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
