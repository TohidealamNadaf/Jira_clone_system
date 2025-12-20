<?php
require_once 'bootstrap/autoload.php';
require_once 'config/config.php';

echo "Testing project query...\n";

try {
    $projectService = new App\Services\ProjectService();
    $projects = $projectService->getAllProjects([], 1, 5);

    echo "Query executed successfully!\n";
    echo "Projects loaded: " . count($projects['items']) . "\n";

    if (!empty($projects['items'])) {
        $first = $projects['items'][0];
        echo "First project: " . ($first['name'] ?? 'Unknown') . "\n";
        echo "Issue count: " . ($first['issue_count'] ?? 'N/A') . "\n";
        echo "Member count: " . ($first['member_count'] ?? 'N/A') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

