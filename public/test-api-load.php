<?php
/**
 * Test if API routes load without errors
 */
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

echo "1. App loaded\n";

$app = app();
$router = $app->getRouter();

echo "2. Router obtained\n";

// Try loading routes
try {
    echo "3. About to load web.php\n";
    require_once __DIR__ . '/../routes/web.php';
    echo "4. web.php loaded successfully\n";
} catch (\Throwable $e) {
    echo "ERROR loading web.php: " . $e->getMessage() . "\n";
}

try {
    echo "5. About to load api.php\n";
    require_once __DIR__ . '/../routes/api.php';
    echo "6. api.php loaded successfully\n";
} catch (\Throwable $e) {
    echo "ERROR loading api.php: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
}

// Count routes
$reflection = new ReflectionClass($router);
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

echo "\n7. Total routes after loading: " . count($routes) . "\n";

$apiRoutes = array_filter($routes, fn($r) => strpos($r['path'], '/api/v1') !== false);
echo "8. API routes: " . count($apiRoutes) . "\n";

if (count($apiRoutes) > 0) {
    foreach (array_slice($apiRoutes, 0, 5) as $route) {
        echo "   - {$route['method']} {$route['path']}\n";
    }
}
?>
