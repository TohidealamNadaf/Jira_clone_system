<?php
/**
 * Test API Issue Types Endpoint
 */

declare(strict_types=1);

// Get the application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Check if we can manually load the issue types
$router = $app->getRouter();

// Log all registered routes
echo "<h2>Registered API Routes:</h2>";
echo "<pre>";

// We need to use reflection to access private routes property
$reflection = new \ReflectionClass($router);
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

$apiRoutes = array_filter($routes, function($route) {
    return strpos($route['path'], '/api') === 0;
});

echo "Total routes: " . count($routes) . "\n";
echo "API routes: " . count($apiRoutes) . "\n\n";

// Show issue-types routes
echo "Issue Types Routes:\n";
$issueTypeRoutes = array_filter($apiRoutes, function($route) {
    return strpos($route['path'], 'issue-types') !== false;
});

foreach ($issueTypeRoutes as $route) {
    echo $route['method'] . " " . $route['path'] . "\n";
}

echo "</pre>";

// Now test the endpoint directly
echo "<h2>Testing Endpoint:</h2>";

// Simulate the request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/jira_clone_system/public/api/v1/issue-types';

try {
    // Get the router and dispatch
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $basePath = parse_url(config('app.url'), PHP_URL_PATH) ?? '';
    
    if ($basePath && str_starts_with($uri, $basePath)) {
        $uri = substr($uri, strlen($basePath));
    }
    
    $cleanUri = '/' . trim($uri, '/') ?: '/';
    
    echo "Base path: " . $basePath . "<br>";
    echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
    echo "Clean URI: " . $cleanUri . "<br><br>";
    
    // Check which routes match
    echo "Matching routes:<br>";
    $matched = false;
    foreach ($apiRoutes as $route) {
        if ($route['method'] === 'GET' && preg_match($route['pattern'], $cleanUri)) {
            echo "✓ " . $route['method'] . " " . $route['path'] . "<br>";
            $matched = true;
        }
    }
    
    if (!$matched) {
        echo "✗ No routes matched<br><br>";
        
        // Show potential matches
        echo "All /api routes:<br>";
        foreach ($apiRoutes as $route) {
            echo "- " . $route['method'] . " " . $route['path'] . " (pattern: " . $route['pattern'] . ")<br>";
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Test the controller directly
echo "<h2>Testing Controller Directly:</h2>";

try {
    $controller = new \App\Controllers\Api\IssueApiController();
    
    // Create a mock request
    $request = new \App\Core\Request([]);
    
    // Call issueTypes using reflection
    $reflection = new \ReflectionMethod($controller, 'issueTypes');
    
    // We can't call this since it has return type "never" and will exit
    echo "Controller method exists and is callable.<br>";
    echo "Return type: never (calls \$this->json() and exits)<br>";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
