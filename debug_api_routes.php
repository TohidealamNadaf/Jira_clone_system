<?php
/**
 * Debug API Route Registration
 */

declare(strict_types=1);

echo "<h1>API Route Debug</h1>";

// Start output buffer
ob_start();

try {
    // Initialize the app
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    // Get router
    $router = $app->getRouter();
    
    // Use reflection to get routes
    $reflection = new \ReflectionClass($router);
    $routesProperty = $reflection->getProperty('routes');
    $routesProperty->setAccessible(true);
    $allRoutes = $routesProperty->getValue($router);
    
    echo "Total routes registered: " . count($allRoutes) . "<br><br>";
    
    // Filter API routes
    $apiRoutes = array_filter($allRoutes, fn($r) => strpos($r['path'], '/api') === 0);
    echo "API routes: " . count($apiRoutes) . "<br>";
    
    // Filter issue-type routes
    $issueTypeRoutes = array_filter($allRoutes, fn($r) => strpos($r['path'], 'issue-types') !== false);
    echo "Issue-types routes: " . count($issueTypeRoutes) . "<br><br>";
    
    if (count($issueTypeRoutes) > 0) {
        echo "Issue-types routes found:<br><pre>";
        foreach ($issueTypeRoutes as $i => $route) {
            echo ($i+1) . ". ";
            echo $route['method'] . " " . $route['path'];
            echo " -> " . (is_array($route['handler']) ? $route['handler'][0] . '@' . $route['handler'][1] : $route['handler']);
            echo "<br>";
        }
        echo "</pre>";
    } else {
        echo "<strong>No issue-types routes found!</strong><br><br>";
        
        echo "Sample API routes:<br><pre>";
        $count = 0;
        foreach ($allRoutes as $route) {
            if (strpos($route['path'], '/api') === 0 && $count < 10) {
                echo $route['method'] . " " . $route['path'] . "<br>";
                $count++;
            }
        }
        echo "</pre>";
    }
    
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

ob_end_flush();
?>
