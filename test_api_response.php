<?php
/**
 * Test API response directly
 */
header('Content-Type: application/json');

require 'bootstrap/autoload.php';

use App\Services\ProjectService;
use App\Core\Database;

// Get all projects to see the full count
$allProjects = Database::select("SELECT id, name, key, is_archived FROM projects ORDER BY name");

$response = [
    'total_in_db' => count($allProjects),
    'all_projects' => $allProjects,
];

// Check non-archived projects
$nonArchivedProjects = Database::select(
    "SELECT id, name, key, is_archived FROM projects WHERE is_archived = 0 ORDER BY name"
);

$response['non_archived_count'] = count($nonArchivedProjects);
$response['non_archived_projects'] = $nonArchivedProjects;

// Test the service with pagination
$projectService = new ProjectService();
$result = $projectService->getAllProjects(['is_archived' => false], 1, 100);

$response['api_result'] = [
    'total' => $result['total'],
    'items_count' => count($result['items']),
    'items' => $result['items'],
    'per_page' => $result['per_page'],
    'current_page' => $result['current_page'],
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
