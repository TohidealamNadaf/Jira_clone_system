<?php
/**
 * Diagnostic Tool - Time Tracking 404 Error
 */

declare(strict_types=1);

// Check current request
echo "<h2>Request Information</h2>";
echo "<pre>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . PHP_EOL;
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . PHP_EOL;
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . PHP_EOL;
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'N/A') . PHP_EOL;
echo "</pre>";

// Load config
echo "<h2>Application Configuration</h2>";
$config = require_once '../config/config.php';
echo "<pre>";
echo "Config app.url: " . ($config['app']['url'] ?? 'N/A') . PHP_EOL;
echo "Config app.debug: " . ($config['app']['debug'] ? 'true' : 'false') . PHP_EOL;
echo "</pre>";

// Check if TimeTra ckingController exists
echo "<h2>Controller Check</h2>";
$controllerPath = '../src/Controllers/TimeTrackingController.php';
echo "<pre>";
echo "TimeTrackingController exists: " . (file_exists($controllerPath) ? 'YES ✓' : 'NO ✗') . PHP_EOL;
echo "</pre>";

// Check route
echo "<h2>Route Check</h2>";
$routePath = '../routes/web.php';
echo "<pre>";
$content = file_get_contents($routePath);
if (strpos($content, "/time-tracking") !== false) {
    echo "Time tracking route found in web.php: YES ✓" . PHP_EOL;
    // Extract the line
    $lines = explode("\n", $content);
    foreach ($lines as $lineNum => $line) {
        if (strpos($line, "/time-tracking") !== false && strpos($line, "dashboard") !== false) {
            echo "Line " . ($lineNum + 1) . ": " . trim($line) . PHP_EOL;
        }
    }
} else {
    echo "Time tracking route NOT found: NO ✗" . PHP_EOL;
}
echo "</pre>";

// Check views
echo "<h2>View Check</h2>";
echo "<pre>";
$viewPath = '../views/time-tracking/dashboard.php';
echo "Time-tracking dashboard view exists: " . (file_exists($viewPath) ? 'YES ✓' : 'NO ✗') . PHP_EOL;
echo "</pre>";

// URL Calculation
echo "<h2>URL Processing</h2>";
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$basePath = parse_url($config['app']['url'], PHP_URL_PATH) ?? '';
echo "<pre>";
echo "REQUEST_URI: " . $uri . PHP_EOL;
echo "Parsed base path: " . $basePath . PHP_EOL;

if ($basePath && strpos($uri, $basePath) === 0) {
    $processedUri = substr($uri, strlen($basePath));
    echo "After removing base path: " . $processedUri . PHP_EOL;
} else {
    echo "Base path NOT found in request URI!" . PHP_EOL;
    echo "This might be because:" . PHP_EOL;
    echo "  - Config app.url uses 'localhost' but you're accessing via 'localhost:8081'" . PHP_EOL;
    echo "  - OR the base path calculation is incorrect" . PHP_EOL;
}
echo "</pre>";

echo "<h2>SOLUTION</h2>";
echo "<p style='color: red; font-weight: bold;'>";
echo "Update config/config.php line 15 to include the port number:" . PHP_EOL;
echo "'url' => 'http://localhost:8081/jira_clone_system/public',";
echo "</p>";

echo "<h2>QUICK FIX</h2>";
echo "<p>Or access via: <a href='http://localhost/jira_clone_system/public/time-tracking'>http://localhost/jira_clone_system/public/time-tracking</a></p>";
?>
