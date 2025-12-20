<?php
declare(strict_types=1);

session_start();

echo "<h1>🔍 404 Debug Information</h1>";

// Check login status
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❌ Not logged in!</p>";
    exit;
}

echo "<p style='color: green;'>✓ Logged in as user ID: " . $_SESSION['user_id'] . "</p>";

// Load app
require_once __DIR__ . '/../bootstrap/app.php';

$router = app()->getRouter();

// Simulate what happens when accessing /time-tracking
$_SERVER['REQUEST_URI'] = '/jira_clone_system/public/time-tracking';
$_SERVER['REQUEST_METHOD'] = 'GET';

echo "<h2>Request Details</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Config app.url: " . config('app.url') . "\n";
echo "</pre>";

// Get the getUri method via reflection
$reflection = new ReflectionClass($router);
$getUriMethod = $reflection->getMethod('getUri');
$getUriMethod->setAccessible(true);

$processedUri = $getUriMethod->invoke($router);

echo "<h2>URI Processing</h2>";
echo "<pre>";
echo "Original REQUEST_URI: /jira_clone_system/public/time-tracking\n";
echo "Config app.url base path: /jira_clone_system/public\n";
echo "Processed URI: " . $processedUri . "\n";
echo "</pre>";

// Get all routes
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

echo "<h2>Route Matching Analysis</h2>";
echo "<pre>";

// Find all /time-tracking routes
$timeTrackingRoutes = [];
foreach ($routes as $i => $route) {
    if (strpos($route['path'], 'time-tracking') !== false) {
        $timeTrackingRoutes[] = ['index' => $i, 'route' => $route];
    }
}

if (empty($timeTrackingRoutes)) {
    echo "❌ NO TIME-TRACKING ROUTES FOUND AT ALL!\n";
} else {
    echo "Found " . count($timeTrackingRoutes) . " time-tracking route(s):\n\n";
    
    foreach ($timeTrackingRoutes as $item) {
        $route = $item['route'];
        echo "Route #" . $item['index'] . ":\n";
        echo "  Method: " . $route['method'] . "\n";
        echo "  Path: " . $route['path'] . "\n";
        echo "  Pattern: " . $route['pattern'] . "\n";
        echo "  Middleware: " . implode(', ', $route['middleware']) . "\n";
        
        // Test if it matches
        if ($route['method'] === 'GET') {
            if (preg_match($route['pattern'], $processedUri, $matches)) {
                echo "  ✓ MATCHES processed URI: " . $processedUri . "\n";
                echo "  Parameters: " . json_encode($matches) . "\n";
            } else {
                echo "  ✗ DOES NOT MATCH processed URI: " . $processedUri . "\n";
                
                // Try alternative URIs
                $testUris = ['/time-tracking', '/jira_clone_system/public/time-tracking'];
                foreach ($testUris as $testUri) {
                    if (preg_match($route['pattern'], $testUri)) {
                        echo "    BUT it WOULD match: $testUri\n";
                    }
                }
            }
        }
        echo "\n";
    }
}

echo "</pre>";

echo "<h2>All GET Routes (First 50)</h2>";
echo "<pre>";
$count = 0;
foreach ($routes as $i => $route) {
    if ($route['method'] === 'GET' && $count < 50) {
        echo "#$i: " . $route['path'] . "\n";
        $count++;
    }
}
echo "</pre>";

// Show the solution
echo "<h2>💡 Possible Solutions</h2>";
echo "<ol>";
echo "<li>If NO time-tracking routes found: Routes file not loading properly</li>";
echo "<li>If routes exist but don't match: URI processing issue - pattern mismatch</li>";
echo "<li>Try accessing: <a href='/jira_clone_system/public/time-tracking'>/jira_clone_system/public/time-tracking</a></li>";
echo "<li>Or try: <a href='/jira_clone_system/public/dashboard'>Go back to Dashboard</a></li>";
echo "</ol>";

?>
