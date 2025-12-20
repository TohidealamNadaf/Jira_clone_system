<?php
declare(strict_types=1);

// This tests the route matching without needing authentication

require_once __DIR__ . '/bootstrap/app.php';

// Simulate the request
$_SERVER['REQUEST_URI'] = '/jira_clone_system/public/time-tracking';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Get router
$router = app()->getRouter();

// Use reflection to test route matching
$reflection = new ReflectionClass($router);

// Get the getUri method
$getUriMethod = $reflection->getMethod('getUri');
$getUriMethod->setAccessible(true);

// Get routes
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

echo "<h1>Route Matching Test</h1>";
echo "<h2>Request Information</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Config app.url: " . config('app.url') . "\n";
echo "</pre>";

echo "<h2>Processed URI</h2>";
$uri = $getUriMethod->invoke($router);
echo "<pre>Processed URI: " . $uri . "</pre>";

echo "<h2>Searching for /time-tracking route</h2>";
echo "<pre>";

$found = false;
foreach ($routes as $i => $route) {
    // Check if this route matches time-tracking
    if (strpos($route['path'], 'time-tracking') !== false) {
        echo "Route #$i:\n";
        echo "  Method: " . $route['method'] . "\n";
        echo "  Path: " . $route['path'] . "\n";
        echo "  Pattern: " . $route['pattern'] . "\n";
        
        // Test if pattern matches current URI
        if (preg_match($route['pattern'], $uri, $matches)) {
            echo "  ✓ MATCHES CURRENT URI!\n";
            $found = true;
        } else {
            echo "  ✗ Does NOT match: $uri\n";
        }
        echo "\n";
    }
}

if (!$found) {
    echo "✗ NO MATCHING ROUTE FOUND for /time-tracking\n\n";
    
    echo "Available routes (first 30):\n";
    foreach ($routes as $i => $route) {
        if ($i < 30) {
            echo "#$i: " . $route['method'] . " " . $route['path'] . "\n";
        }
    }
}

echo "</pre>";

// Now test without the base path
echo "<h2>Testing Alternative URI Formats</h2>";
echo "<pre>";

$testUris = [
    '/time-tracking',
    '/jira_clone_system/public/time-tracking',
    '/jira_clone_system/public/time-tracking/',
];

foreach ($testUris as $testUri) {
    echo "Testing: $testUri\n";
    
    foreach ($routes as $route) {
        if ($route['method'] === 'GET' && strpos($route['path'], 'time-tracking') !== false) {
            if (preg_match($route['pattern'], $testUri, $matches)) {
                echo "  ✓ MATCHES route: " . $route['path'] . "\n";
            }
        }
    }
}

echo "</pre>";
?>
