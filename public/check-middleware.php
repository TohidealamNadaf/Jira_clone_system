<?php
/**
 * Check middleware for issue-types route
 */
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$app = app();
$router = $app->getRouter();

// Load routes
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

$reflection = new ReflectionClass($router);
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

$issueTypesRoutes = array_filter($routes, fn($r) => 
    $r['method'] === 'GET' && strpos($r['path'], '/api/v1/issue-types') !== false
);

echo "All /api/v1/issue-types routes:\n\n";
foreach ($issueTypesRoutes as $idx => $route) {
    echo "Route #" . ($idx + 1) . ":\n";
    echo "  Path: {$route['path']}\n";
    echo "  Method: {$route['method']}\n";
    echo "  Handler: " . json_encode($route['handler']) . "\n";
    echo "  Middleware: " . json_encode($route['middleware']) . "\n";
    echo "  Pattern: {$route['pattern']}\n";
    echo "\n";
}
?>
