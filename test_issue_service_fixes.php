<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Services\IssueService;
use App\Core\Database;

// Mock session/global if needed
$GLOBALS['api_user'] = ['id' => 1];

echo "Initializing IssueService...\n";
$service = new IssueService();

// Create a dummy issue
$projectId = Database::selectValue("SELECT id FROM projects LIMIT 1");
if (!$projectId)
    die("No projects found\n");
$issueTypeId = Database::selectValue("SELECT id FROM issue_types LIMIT 1");
$statusId = Database::selectValue("SELECT id FROM statuses LIMIT 1");
$priorityId = Database::selectValue("SELECT id FROM issue_priorities LIMIT 1");

$issueId = Database::insert('issues', [
    'project_id' => $projectId,
    'issue_type_id' => $issueTypeId,
    'status_id' => $statusId,
    'priority_id' => $priorityId ? $priorityId : 1,
    'issue_key' => 'TEST-9999',
    'summary' => 'Test Issue for Verification',
    'reporter_id' => 1,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
]);

echo "Created test issue ID: $issueId\n";

try {
    // 1. Test Worklogs
    echo "Testing Worklogs...\n";

    // Existing method logWork
    $service->logWork($issueId, 1, 3600, date('Y-m-d H:i:s'), 'Initial work');

    // Now get worklogs
    $logs = $service->getWorklogs($issueId);
    echo "Got " . count($logs) . " worklogs.\n";
    if (count($logs) !== 1)
        throw new Exception("Expected 1 worklog");

    // Update worklog
    $logId = $logs[0]['id'];
    $updated = $service->updateWorklog($logId, ['time_spent' => 7200, 'description' => 'Updated work'], 1);
    echo "Updated worklog time spent: " . $updated['time_spent'] . "\n";
    if ($updated['time_spent'] != 7200)
        throw new Exception("Update failed");

    // Delete worklog
    $service->deleteWorklog($logId, 1);
    $logs = $service->getWorklogs($issueId);
    echo "After delete, got " . count($logs) . " worklogs.\n";
    if (count($logs) !== 0)
        throw new Exception("Delete failed");

    // 2. Test Attachments
    echo "Testing Attachments...\n";
    // We can't easily upload a file, but we can call getAttachments
    $atts = $service->getAttachments($issueId);
    echo "Got " . count($atts) . " attachments.\n";

    // Manual insert to test get/delete
    $attId = Database::insert('issue_attachments', [
        'issue_id' => $issueId,
        'filename' => 'test.txt',
        'filepath' => '/test.txt',
        'file_size' => 123,
        'file_type' => 'text/plain',
        'uploaded_by' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $atts = $service->getAttachments($issueId);
    if (count($atts) !== 1)
        throw new Exception("Expected 1 attachment");

    $service->deleteAttachment($attId, 1);
    $atts = $service->getAttachments($issueId);
    if (count($atts) !== 0)
        throw new Exception("Expected 0 attachments");

    echo "ALL TESTS PASSED\n";

} catch (Exception $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
} finally {
    // Cleanup
    Database::delete('issues', 'id = ?', [$issueId]);
    Database::delete('issue_attachments', 'issue_id = ?', [$issueId]); // Just in case
    Database::delete('worklogs', 'issue_id = ?', [$issueId]); // Just in case
}
