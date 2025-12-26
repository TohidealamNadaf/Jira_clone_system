<?php
/**
 * Direct test of API routes
 */
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$app = app();
$router = $app->getRouter();

// Use reflection
$reflection = new ReflectionClass($router);
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

$issueTypesFound = false;
$prioritiesFound = false;

foreach ($routes as $route) {
    if (strpos($route['path'], '/api/v1/issue-types') !== false) {
        $issueTypesFound = true;
    }
    if (strpos($route['path'], '/api/v1/priorities') !== false) {
        $prioritiesFound = true;
    }
}

echo json_encode([
    'total_routes' => count($routes),
    'api_routes' => count(array_filter($routes, fn($r) => strpos($r['path'], '/api/v1') !== false)),
    'issue_types_route_found' => $issueTypesFound,
    'priorities_route_found' => $prioritiesFound,
]);
?>
