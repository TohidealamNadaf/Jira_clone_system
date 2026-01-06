<?php
/**
 * Debug Routes - Check if routes are registered
 */

declare(strict_types=1);

// Check if user is logged in first
session_start();

echo "<h1>Route Debug Information</h1>";

echo "<h2>Session Status</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✓ User is logged in (ID: " . $_SESSION['user_id'] . ")</p>";
} else {
    echo "<p style='color: red;'>✗ User NOT logged in - authentication required</p>";
    echo "<p><a href='/jira_clone_system/public/login'>Click here to login first</a></p>";
    exit;
}

// Now check routes
echo "<h2>Current Request</h2>";
echo "<pre>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . PHP_EOL;
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . PHP_EOL;
echo "</pre>";

// Load application to check routes
require_once __DIR__ . '/../bootstrap/app.php';

$router = app()->getRouter();

echo "<h2>Time Tracking Routes (via Reflection)</h2>";
echo "<pre>";

// Try to access router's routes via reflection
$reflection = new ReflectionClass($router);
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

$found = false;
foreach ($routes as $route) {
    if (strpos($route['path'], 'time-tracking') !== false) {
        echo "✓ Found route:\n";
        echo "  Method: " . $route['method'] . "\n";
        echo "  Path: " . $route['path'] . "\n";
        echo "  Handler: " . (is_array($route['handler']) ? $route['handler'][0]::class . '::' . $route['handler'][1] : $route['handler']) . "\n";
        echo "  Pattern: " . $route['pattern'] . "\n";
        echo "  Middleware: " . implode(', ', $route['middleware']) . "\n";
        echo "\n";
        $found = true;
    }
}

if (!$found) {
    echo "✗ NO TIME TRACKING ROUTES FOUND!\n";
    echo "\nAll available routes:\n";
    foreach ($routes as $i => $route) {
        if ($i < 20) { // Show first 20
            echo $route['method'] . " " . $route['path'] . "\n";
        }
    }
    echo "... and " . (count($routes) - 20) . " more routes\n";
}

echo "</pre>";

echo "<h2>Test Access</h2>";
echo "<p><a href='/jira_clone_system/public/time-tracking' style='color: blue; font-weight: bold;'>Click to access /time-tracking</a></p>";

?>
