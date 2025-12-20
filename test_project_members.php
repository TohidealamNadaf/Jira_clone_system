<?php
require_once 'bootstrap/autoload.php';
require_once 'config/config.php';

echo "Testing project members loading...\n";

try {
    $projectService = new App\Services\ProjectService();
    $members = $projectService->getProjectMembers(1); // Assuming project ID 1 exists

    echo "Members query executed successfully!\n";
    echo "Members found: " . count($members) . "\n";

    if (!empty($members)) {
        $first = $members[0];
        echo "First member: " . ($first['display_name'] ?? 'Unknown') . "\n";
        echo "Role: " . ($first['role_name'] ?? 'Unknown') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


