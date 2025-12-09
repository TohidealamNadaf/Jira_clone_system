<?php
/**
 * Test the quick create endpoint
 */
header('Content-Type: application/json');

require 'bootstrap/autoload.php';

use App\Services\ProjectService;
use App\Core\Database;

try {
    $projectService = new ProjectService();
    
    // Simulate the quickCreateList endpoint
    $projects = $projectService->getAllProjects([], 1, 1000);
    
    // Add issue types to each project
    if (!empty($projects['items'])) {
        foreach ($projects['items'] as &$project) {
            $issueTypes = Database::select(
                "SELECT id, name, icon, color FROM issue_types WHERE project_id = ? ORDER BY sort_order ASC",
                [$project['id']]
            );
            $project['issue_types'] = $issueTypes;
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $projects,
        'message' => 'Endpoint working correctly'
    ], JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>
