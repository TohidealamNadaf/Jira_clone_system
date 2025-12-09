<?php
/**
 * Debug dropdown issue
 */
require 'bootstrap/autoload.php';

use App\Services\ProjectService;
use App\Core\Database;

// Check database
$allProjects = Database::select("SELECT id, name, key, is_archived FROM projects ORDER BY name");

echo "=== ALL PROJECTS IN DATABASE ===\n";
echo "Total: " . count($allProjects) . "\n";
foreach ($allProjects as $p) {
    echo "- {$p['name']} ({$p['key']}) - Archived: " . ($p['is_archived'] ? 'Yes' : 'No') . "\n";
}

echo "\n=== API RESPONSE (non-archived only) ===\n";
$projectService = new ProjectService();
$filters = ['is_archived' => false];
$result = $projectService->getAllProjects($filters, 1, 100);

echo "Items returned: " . count($result['items']) . "\n";
echo "Total count: " . $result['total'] . "\n";
echo "Per page: " . $result['per_page'] . "\n";
echo "Current page: " . $result['current_page'] . "\n";
echo "Last page: " . $result['last_page'] . "\n";

echo "\nProjects in API response:\n";
foreach ($result['items'] as $p) {
    echo "- {$p['name']} ({$p['key']}) - ID: {$p['id']}\n";
}
?>
