<?php
declare(strict_types=1);

// Load application
require_once __DIR__ . '/bootstrap/autoload.php';

use App\Services\IssueService;
use App\Services\ProjectService;

// Get project ID from URL or use 1
$projectId = $_GET['project_id'] ?? 1;

echo "<h2>Debugging Time Tracking Issues for Project ID: $projectId</h2>";

try {
    // Try to get the project
    $projectService = new ProjectService();
    $project = $projectService->getProjectById($projectId);
    
    if (!$project) {
        echo "<p style='color: red;'>Project not found with ID: $projectId</p>";
        exit;
    }
    
    echo "<p><strong>Project:</strong> {$project['name']} ({$project['key']})</p>";
    
    // Try to get issues
    $issueService = new IssueService();
    $response = $issueService->getIssues(
        ['project_id' => $projectId],
        'key',
        'ASC',
        1,
        1000
    );
    
    echo "<p><strong>Total Issues Found:</strong> {$response['total']}</p>";
    
    if (empty($response['data'])) {
        echo "<p style='color: orange;'>No issues found in response data</p>";
    } else {
        echo "<p style='color: green;'>Found " . count($response['data']) . " issues</p>";
        echo "<h3>Issues:</h3>";
        echo "<ul>";
        foreach (array_slice($response['data'], 0, 10) as $issue) {
            echo "<li>{$issue['key']} - {$issue['summary']}</li>";
        }
        if (count($response['data']) > 10) {
            echo "<li>... and " . (count($response['data']) - 10) . " more</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
