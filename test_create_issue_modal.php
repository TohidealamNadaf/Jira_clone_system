<?php
/**
 * Test Create Issue Modal - Debugging Script
 */

// Get base path
$basePath = '';
$scriptPath = dirname($_SERVER['SCRIPT_FILENAME']);
$publicPath = dirname($scriptPath);
$isInPublic = strpos($scriptPath, DIRECTORY_SEPARATOR . 'public') !== false;

if ($isInPublic) {
    $basePath = str_replace($publicPath . DIRECTORY_SEPARATOR . 'public', '', $scriptPath);
}

echo '<h1>Create Issue Modal - Testing</h1>';
echo '<h2>Environment</h2>';
echo '<pre>';
echo "Base Path: " . htmlspecialchars($basePath) . "\n";
echo "Script Path: " . htmlspecialchars($scriptPath) . "\n";
echo "Public Path: " . htmlspecialchars($publicPath) . "\n";
echo "Is in Public: " . ($isInPublic ? 'YES' : 'NO') . "\n";
echo '</pre>';

echo '<h2>Routes Check</h2>';
echo '<pre>';
echo "Routes file: " . (file_exists(__DIR__ . '/../routes/web.php') ? '✅ EXISTS' : '❌ NOT FOUND') . "\n";
echo '</pre>';

// Check if the store method exists
echo '<h2>IssueController Check</h2>';
$controllerFile = __DIR__ . '/../src/Controllers/IssueController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    echo '<pre>';
    if (strpos($content, 'public function store(Request $request): void') !== false) {
        echo "✅ store() method found in IssueController\n";
    } else {
        echo "❌ store() method NOT found in IssueController\n";
    }
    echo '</pre>';
} else {
    echo '<pre>❌ IssueController.php not found</pre>';
}

// Test URL construction
echo '<h2>Test URLs</h2>';
$testUrls = [
    '/issues/store',
    $basePath . '/issues/store',
];

echo '<ul>';
foreach ($testUrls as $url) {
    echo '<li><code>' . htmlspecialchars($url) . '</code></li>';
}
echo '</ul>';

echo '<h2>Test Form Data</h2>';
echo '<form method="post" action="' . htmlspecialchars($basePath) . '/issues/store" id="testForm">';
echo '<input type="hidden" name="project_id" value="1" />';
echo '<input type="hidden" name="issue_type_id" value="1" />';
echo '<input type="hidden" name="summary" value="Test Issue" />';
echo '<input type="hidden" name="description" value="Test Description" />';
echo '<button type="submit">Test Form Submit (POST)</button>';
echo '</form>';

echo '<h2>cURL Test</h2>';
echo '<pre>';
echo "To test via cURL, run:\n";
echo "curl -X POST http://localhost:8081" . htmlspecialchars($basePath) . "/issues/store \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{";
echo '"project_id":1,"issue_type_id":1,"summary":"Test","description":""';
echo "}'\n";
echo '</pre>';

echo '<h2>Network Diagram</h2>';
echo '<img src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22%3E%3Ctext x=%2210%22 y=%2230%22 font-size=%2216%22 font-weight=%22bold%22%3ECreate Issue Modal Flow%3C/text%3E%3Ctext x=%2210%22 y=%2260%22 font-size=%2214%22%3E1. POST /issues/store%3C/text%3E%3Ctext x=%2210%22 y=%2290%22 font-size=%2214%22%3E2. Routes matches auth middleware%3C/text%3E%3Ctext x=%2210%22 y=%22120%22 font-size=%2214%22%3E3. IssueController::store() runs%3C/text%3E%3Ctext x=%2210%22 y=%22150%22 font-size=%2214%22%3E4. JSON response returned%3C/text%3E%3C/svg%3E" />';
?>
