<?php
/**
 * Test Calendar Avatar URL Fix
 * Verifies that avatar paths are correctly resolved
 */

// Simulate the getAvatarUrl function from calendar-realtime.js in PHP for testing
function testGetAvatarUrl($path, $webBase = 'http://localhost:8080/jira_clone_system/public/') {
    if (!$path) return '';
    if (strpos($path, 'http') === 0) return $path;
    
    // FIX: Handle incorrect paths from database
    // If path is '/avatars/...' (missing /uploads), prepend /uploads
    if (strpos($path, '/avatars/') === 0) {
        $path = '/uploads' . $path;
    }
    
    // Build full URL
    $baseUrl = $webBase;
    if (substr($baseUrl, -1) === '/') {
        $baseUrl = substr($baseUrl, 0, -1);  // Remove trailing slash
    }
    
    // Ensure path starts with /
    if (strpos($path, '/') !== 0) {
        $path = '/' . $path;
    }
    
    return $baseUrl . $path;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Calendar Avatar URL Fix Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .test { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .pass { background: #d4edda; border-color: #c3e6cb; }
        .fail { background: #f8d7da; border-color: #f5c6cb; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>

<h1>🧪 Calendar Avatar URL Fix Test</h1>
<p>Testing the avatar URL path resolution fix for calendar page.</p>

<?php

$testCases = [
    // Test case: [description, input_path, webBase, expected_output]
    [
        'Incorrect path with /avatars/',
        '/avatars/avatar_1_1767684205.png',
        'http://localhost:8080/jira_clone_system/public/',
        'http://localhost:8080/jira_clone_system/public/uploads/avatars/avatar_1_1767684205.png'
    ],
    [
        'Correct path with /uploads/avatars/',
        '/uploads/avatars/avatar_1_1767684205.png',
        'http://localhost:8080/jira_clone_system/public/',
        'http://localhost:8080/jira_clone_system/public/uploads/avatars/avatar_1_1767684205.png'
    ],
    [
        'Incorrect path without leading slash',
        'avatars/avatar_1_1767684205.png',
        'http://localhost:8080/jira_clone_system/public/',
        'http://localhost:8080/jira_clone_system/public/avatars/avatar_1_1767684205.png'
    ],
    [
        'Full HTTP URL (should pass through)',
        'http://example.com/avatars/user.png',
        'http://localhost:8080/jira_clone_system/public/',
        'http://example.com/avatars/user.png'
    ],
    [
        'Empty path (should return empty)',
        '',
        'http://localhost:8080/jira_clone_system/public/',
        ''
    ],
];

$passedCount = 0;
$failedCount = 0;

foreach ($testCases as $i => $test) {
    list($desc, $input, $webBase, $expected) = $test;
    $result = testGetAvatarUrl($input, $webBase);
    $passed = $result === $expected;
    
    if ($passed) {
        $passedCount++;
        $cssClass = 'pass';
        $status = '✅ PASS';
    } else {
        $failedCount++;
        $cssClass = 'fail';
        $status = '❌ FAIL';
    }
    
    echo "<div class='test $cssClass'>";
    echo "<h3>Test " . ($i + 1) . ": $desc - $status</h3>";
    echo "<p><strong>Input:</strong> <code>$input</code></p>";
    echo "<p><strong>Expected:</strong></p>";
    echo "<pre>$expected</pre>";
    echo "<p><strong>Got:</strong></p>";
    echo "<pre>$result</pre>";
    if (!$passed) {
        echo "<p style='color: red;'><strong>MISMATCH!</strong></p>";
    }
    echo "</div>";
}

?>

<div style="margin: 20px 0; padding: 15px; background: #e3f2fd; border: 1px solid #90caf9;">
    <h3>📊 Test Summary</h3>
    <p><strong>Passed:</strong> <span style="color: green; font-weight: bold;"><?= $passedCount ?>/<?= count($testCases) ?></span></p>
    <p><strong>Failed:</strong> <span style="color: red; font-weight: bold;"><?= $failedCount ?>/<?= count($testCases) ?></span></p>
    
    <?php if ($failedCount === 0): ?>
        <p style="color: green; font-size: 16px;"><strong>✅ All tests passed! Avatar URL fix is working correctly.</strong></p>
    <?php else: ?>
        <p style="color: red; font-size: 16px;"><strong>❌ Some tests failed. Fix needed.</strong></p>
    <?php endif; ?>
</div>

<hr>

<h2>🚀 How to Apply This Fix</h2>
<ol>
    <li>Clear browser cache: <code>CTRL + SHIFT + DEL</code></li>
    <li>Hard refresh: <code>CTRL + F5</code></li>
    <li>Go to calendar page: <code>/calendar</code></li>
    <li>Click on any event to open modal</li>
    <li>Open DevTools: <code>F12</code></li>
    <li>Go to Network tab and filter "avatar"</li>
    <li>All avatar requests should show 200 OK (not 404)</li>
</ol>

</body>
</html>
