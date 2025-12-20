<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "Testing Documentation Hub Access...\n\n";

// Check if project_documents table exists
$tables = Database::select("SHOW TABLES LIKE 'project_documents'");
if (empty($tables)) {
    echo "❌ project_documents table does not exist\n";
    echo "Run: php scripts/setup_documentation_hub.php\n";
    exit(1);
} else {
    echo "✅ project_documents table exists\n";
}

// Check if project CWAYS exists
$projects = Database::select("SELECT id, `key`, name FROM projects WHERE `key` = 'CWAYS'");
if (empty($projects)) {
    echo "❌ Project CWAYS not found\n";
    echo "Available projects:\n";
    $all = Database::select("SELECT id, `key`, name FROM projects LIMIT 5");
    foreach ($all as $p) {
        echo "  - {$p['key']}: {$p['name']}\n";
    }
    exit(1);
} else {
    $project = $projects[0];
    echo "✅ Found project: {$project['name']} (ID: {$project['id']})\n";
}

// Test Documentation Service
use App\Services\ProjectDocumentationService;
$docService = new ProjectDocumentationService();
$documents = $docService->getProjectDocuments($project['id']);
echo "✅ Found " . count($documents) . " documents\n";

// Test route handling
try {
    $router = app()->getRouter();
    
    // Test the exact route pattern
    echo "\n🔍 Testing route pattern...\n";
    
    // Check if routes are properly registered
    $routes = $router->getRoutes();
    $docRoutes = array_filter($routes, function($route) {
        return strpos($route['uri'], 'documentation') !== false;
    });
    
    echo "✅ Found " . count($docRoutes) . " documentation routes:\n";
    foreach ($docRoutes as $route) {
        echo "  - {$route['method']} {$route['uri']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Router error: " . $e->getMessage() . "\n";
}

echo "\n🚀 Documentation Hub should be accessible at:\n";
echo "http://localhost:8081/jira_clone_system/public/projects/CWAYS/documentation\n";