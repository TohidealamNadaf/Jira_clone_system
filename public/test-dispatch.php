<?php
/**
 * Test direct API endpoint dispatch
 */
declare(strict_types=1);

// Simulate the request path for /api/v1/issue-types
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/jira_clone_system/public/api/v1/issue-types';
$_SERVER['SCRIPT_NAME'] = '/jira_clone_system/public/index.php';
$_SERVER['PHP_SELF'] = '/jira_clone_system/public/index.php';

require_once __DIR__ . '/../bootstrap/app.php';

$app = app();

// Load routes manually
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

$router = $app->getRouter();

// Get the request path (what the router sees)
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Remove base path
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if (strpos($requestPath, $basePath) === 0) {
    $requestPath = substr($requestPath, strlen($basePath));
}
$requestPath = '/' . ltrim($requestPath, '/');

echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Calculated path: " . $requestPath . "\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n\n";

// Try to find matching route
$reflection = new ReflectionClass($router);
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

$matches = array_filter($routes, function($route) use ($requestPath) {
    return $route['method'] === $_SERVER['REQUEST_METHOD'] && preg_match($route['pattern'], $requestPath);
});

if (count($matches) > 0) {
    echo "FOUND " . count($matches) . " matching route(s):\n";
    foreach ($matches as $match) {
        echo "Path: {$match['path']}\n";
        echo "Handler: " . json_encode($match['handler']) . "\n";
        echo "Middleware: " . json_encode($match['middleware']) . "\n";
    }
} else {
    echo "NO matching routes found\n";
    echo "\nSearching for any /api/v1 routes:\n";
    $apiRoutes = array_filter($routes, fn($r) => strpos($r['path'], '/api/v1') !== false);
    foreach (array_slice($apiRoutes, 0, 5) as $route) {
        echo "{$route['method']} {$route['path']} (Pattern: {$route['pattern']})\n";
    }
}
?>
