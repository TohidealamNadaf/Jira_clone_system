<?php
/**
 * Test if API routes are registered
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

$app = app();
$router = $app->getRouter();

// Use reflection to access private $routes property
$reflection = new ReflectionClass($router);
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

echo "<h1>Checking API Routes</h1>";
echo "<p>Total routes registered: " . count($routes) . "</p>";

// Find API routes
$apiRoutes = array_filter($routes, fn($route) => strpos($route['path'], '/api/v1/') === 0);
echo "<p>API v1 routes: " . count($apiRoutes) . "</p>";

// Find specific endpoints
$issueTypesRoute = array_filter($routes, fn($route) => strpos($route['path'], '/api/v1/issue-types') !== false);
$prioritiesRoute = array_filter($routes, fn($route) => strpos($route['path'], '/api/v1/priorities') !== false);

echo "<h2>Looking for /api/v1/issue-types</h2>";
if (count($issueTypesRoute) > 0) {
    echo "<p><strong>Found!</strong></p>";
    foreach ($issueTypesRoute as $route) {
        echo "<pre>";
        echo "Method: {$route['method']}\n";
        echo "Path: {$route['path']}\n";
        echo "Pattern: {$route['pattern']}\n";
        echo "Handler: " . json_encode($route['handler']) . "\n";
        echo "Middleware: " . json_encode($route['middleware']) . "\n";
        echo "</pre>";
    }
} else {
    echo "<p><strong>NOT FOUND!</strong></p>";
}

echo "<h2>Looking for /api/v1/priorities</h2>";
if (count($prioritiesRoute) > 0) {
    echo "<p><strong>Found!</strong></p>";
    foreach ($prioritiesRoute as $route) {
        echo "<pre>";
        echo "Method: {$route['method']}\n";
        echo "Path: {$route['path']}\n";
        echo "Pattern: {$route['pattern']}\n";
        echo "Handler: " . json_encode($route['handler']) . "\n";
        echo "Middleware: " . json_encode($route['middleware']) . "\n";
        echo "</pre>";
    }
} else {
    echo "<p><strong>NOT FOUND!</strong></p>";
}

// Show all API routes
echo "<h2>All API Routes</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Method</th><th>Path</th><th>Handler</th></tr>";
foreach (array_slice($apiRoutes, 0, 20) as $route) {
    $handler = is_array($route['handler']) ? $route['handler'][0] . '::' . $route['handler'][1] : $route['handler'];
    echo "<tr>";
    echo "<td>{$route['method']}</td>";
    echo "<td>{$route['path']}</td>";
    echo "<td>{$handler}</td>";
    echo "</tr>";
}
echo "</table>";
?>
