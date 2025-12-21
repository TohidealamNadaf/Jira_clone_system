<?php
/**
 * Simple test to see what's happening with the API routes
 */

declare(strict_types=1);

echo "Testing API routing...\n\n";

// Load the app
$app = require_once __DIR__ . '/bootstrap/app.php';

// Get the router using reflection
$reflClass = new ReflectionClass($app->getRouter());
$routesProp = $reflClass->getProperty('routes');
$routesProp->setAccessible(true);
$routes = $routesProp->getValue($app->getRouter());

// Count routes
echo "Total routes: " . count($routes) . "\n";

// Find issue-types routes
$issueTypeRoutes = array_filter($routes, fn($r) => strpos($r['path'], 'issue-types') !== false);
echo "Issue-types routes: " . count($issueTypeRoutes) . "\n\n";

if (count($issueTypeRoutes) > 0) {
    echo "Issue-types routes found:\n";
    foreach ($issueTypeRoutes as $route) {
        echo "  " . $route['method'] . " " . $route['path'] . "\n";
        echo "    Handler: ";
        if (is_array($route['handler'])) {
            echo $route['handler'][0] . "@" . $route['handler'][1];
        } else {
            echo $route['handler'];
        }
        echo "\n";
        echo "    Middleware: " . implode(", ", $route['middleware']) . "\n";
    }
} else {
    echo "❌ No issue-types routes found!\n\n";
    
    // Show all API routes
    $apiRoutes = array_filter($routes, fn($r) => strpos($r['path'], '/api') === 0);
    echo "Total API routes: " . count($apiRoutes) . "\n";
    
    echo "\nFirst 10 API routes:\n";
    $count = 0;
    foreach ($apiRoutes as $route) {
        if ($count++ >= 10) break;
        echo "  " . $route['method'] . " " . $route['path'] . "\n";
    }
}

echo "\n\nChecking if controller exists and method is callable...\n";

try {
    $controller = new \App\Controllers\Api\IssueApiController();
    echo "✅ IssueApiController found\n";
    
    // Check if issueTypes method exists
    if (method_exists($controller, 'issueTypes')) {
        echo "✅ issueTypes() method exists\n";
        
        // Get reflection
        $method = new ReflectionMethod($controller, 'issueTypes');
        echo "   Return type: " . $method->getReturnType() . "\n";
    } else {
        echo "❌ issueTypes() method not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n\nDone.\n";
?>
