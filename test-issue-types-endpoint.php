<?php
/**
 * Direct Test of /api/v1/issue-types Endpoint
 */

// Test 1: Check if Controller method exists
echo "<h2>🔍 Issue Types Endpoint Diagnostic</h2>";
echo "<hr>";

// Test 2: Direct database query
try {
    require_once __DIR__ . '/bootstrap/autoload.php';
    require_once __DIR__ . '/bootstrap/app.php';
    
    echo "<h3>✅ Framework loaded</h3>";
    
    // Test direct database query
    echo "<h3>Testing direct database query:</h3>";
    $sql = "SELECT * FROM issue_types WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
    $result = \App\Core\Database::select($sql);
    
    echo "<pre>";
    echo "SQL: " . $sql . "\n\n";
    echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
    echo "</pre>";
    
    if (empty($result)) {
        echo "<p style='color: red;'><strong>⚠️ No issue types found in database!</strong></p>";
    } else {
        echo "<p style='color: green;'><strong>✅ Found " . count($result) . " issue types</strong></p>";
    }
    
    // Test 3: Check Controller method
    echo "<h3>Checking IssueApiController::issueTypes method:</h3>";
    $reflectionClass = new ReflectionClass('App\Controllers\Api\IssueApiController');
    $method = $reflectionClass->getMethod('issueTypes');
    $returnType = $method->getReturnType();
    
    echo "<p><strong>Return Type:</strong> " . (string)$returnType . "</p>";
    
    if ($returnType && $returnType->getName() === 'void') {
        echo "<p style='color: green;'><strong>✅ Return type is VOID (correct!)</strong></p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Return type is NOT void: " . (string)$returnType . "</strong></p>";
    }
    
    // Test 4: Check API route
    echo "<h3>Testing API endpoint directly:</h3>";
    echo "<p>Visit this URL in your browser to see raw API response:</p>";
    echo "<code>" . $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/api/v1/issue-types</code>";
    echo "<p><a href='/api/v1/issue-types' target='_blank'>Click here to test endpoint</a></p>";
    
    // Test 5: Check if endpoint is properly routed
    echo "<h3>Router Configuration:</h3>";
    echo "<p>Checking if route exists in API routes...</p>";
    $routeFile = file_get_contents(__DIR__ . '/routes/api.php');
    if (strpos($routeFile, "'issue-types'") !== false && strpos($routeFile, 'issueTypes') !== false) {
        echo "<p style='color: green;'><strong>✅ Route /api/v1/issue-types is configured</strong></p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Route not found in api.php</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h3>📋 Checklist:</h3>";
echo "<ul>";
echo "<li>Check Network tab in DevTools (F12)</li>";
echo "<li>Look for /api/v1/issue-types request</li>";
echo "<li>Check Status Code (should be 200)</li>";
echo "<li>Check Response (should be JSON array)</li>";
echo "<li>Check Console (any red errors?)</li>";
echo "</ul>";

echo "<h3>🔗 Direct Links to Test:</h3>";
echo "<ul>";
echo "<li><a href='/api/v1/issue-types' target='_blank'>Test /api/v1/issue-types endpoint</a></li>";
echo "<li><a href='/api/v1/priorities' target='_blank'>Test /api/v1/priorities endpoint</a></li>";
echo "</ul>";

?>
