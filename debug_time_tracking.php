<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Services\IssueService;
use App\Services\ProjectService;
use App\Core\Database;

// Test: Get issues for project ID 1
$projectService = new ProjectService();
$issueService = new IssueService();

echo "=== TIME TRACKING DEBUG ===\n\n";

// Get project
$project = $projectService->getProjectById(1);
echo "Project: " . json_encode($project) . "\n\n";

// Get issues for project
echo "Fetching issues for project_id = 1...\n";
$response = $issueService->getIssues(
    ['project_id' => 1],
    'key',
    'ASC',
    1,
    1000
);

echo "Issues found: " . $response['total'] . "\n";
echo "Issues in data: " . count($response['data']) . "\n\n";

if (!empty($response['data'])) {
    echo "Sample issues:\n";
    foreach (array_slice($response['data'], 0, 5) as $issue) {
        echo "  - {$issue['key']}: {$issue['summary']} (Status: {$issue['status_name']})\n";
    }
} else {
    echo "NO ISSUES FOUND!\n";
}

// Check what's in the issues table
echo "\n=== Direct DB Query ===\n";
$count = Database::selectValue("SELECT COUNT(*) FROM issues WHERE project_id = ?", [1]);
echo "Total issues in project 1: $count\n";

// Check all projects
echo "\n=== All Projects ===\n";
$allProjects = Database::select("SELECT id, name, key FROM projects LIMIT 10", []);
foreach ($allProjects as $p) {
    $issueCount = Database::selectValue("SELECT COUNT(*) FROM issues WHERE project_id = ?", [$p['id']]);
    echo "  - [{$p['id']}] {$p['key']}: {$p['name']} (Issues: $issueCount)\n";
}

// Check realtime notifications stream
echo "\n=== Check Stream Endpoint ===\n";
echo "Stream URL: /notifications/stream\n";
echo "Testing if endpoint exists...\n";

// Check if stream route exists
$routes = [
    '/notifications/stream' => 'EventSource stream',
    '/api/v1/time-tracking/start' => 'Start timer API',
    '/api/v1/time-tracking/stop' => 'Stop timer API',
];

foreach ($routes as $route => $desc) {
    echo "  - $route ($desc)\n";
}
