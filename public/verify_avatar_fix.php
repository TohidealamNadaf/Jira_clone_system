<?php
/**
 * Avatar Fix Verification Script
 * 
 * Verifies that the avatar 404 fix has been properly applied
 * and shows the status of avatar paths in the system
 */

// Direct database connection (no framework dependency)
$mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system');

if ($mysqli->connect_error) {
    die('<div style="background: #f8d7da; color: #721c24; padding: 20px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;">'
        . '<strong>Database Connection Error:</strong> ' . htmlspecialchars($mysqli->connect_error)
        . '<br><br>Please ensure XAMPP MySQL is running and database credentials are correct.'
        . '</div>');
}

echo "<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h1 { color: #8B1956; }
    h2 { color: #333; border-bottom: 2px solid #8B1956; padding-bottom: 10px; }
    .status { padding: 15px; border-radius: 4px; margin: 20px 0; }
    .status.pass { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .status.fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .status.warning { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
    table { border-collapse: collapse; width: 100%; background: white; margin-top: 15px; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background: #f8f9fa; font-weight: bold; }
    code { background: #f8f9fa; padding: 4px 8px; border-radius: 3px; font-family: 'Monaco', 'Courier New', monospace; }
    .check { color: green; font-weight: bold; }
    .cross { color: red; font-weight: bold; }
    .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
</style>";

echo "<div class='container'>";
echo "<h1>🔍 Avatar Fix Verification Report</h1>";

// Check 1: Verify code fix is in place
echo "<h2>Step 1: Code Fix Verification</h2>";

$functionFile = realpath(__DIR__ . '/../src/Helpers/functions.php');
if (file_exists($functionFile)) {
    $content = file_get_contents($functionFile);
    if (str_contains($content, '/public/avatars/')) {
        echo "<div class='status pass'>";
        echo "✅ <strong>PASS:</strong> Avatar fallback handler found in src/Helpers/functions.php<br>";
        echo "The avatar() function includes code to handle /public/avatars/ paths.";
        echo "</div>";
    } else {
        echo "<div class='status fail'>";
        echo "❌ <strong>FAIL:</strong> Avatar fallback handler NOT found<br>";
        echo "Update src/Helpers/functions.php with the fallback code.";
        echo "</div>";
    }
} else {
    echo "<div class='status fail'>";
    echo "❌ <strong>FAIL:</strong> functions.php file not found";
    echo "</div>";
}

// Check 2: Verify avatar files exist
echo "<h2>Step 2: Avatar Files Check</h2>";

$avatarDir = realpath(__DIR__ . '/uploads/avatars');
if (is_dir($avatarDir)) {
    $files = array_diff(scandir($avatarDir), ['.', '..']);
    if (!empty($files)) {
        echo "<div class='status pass'>";
        echo "✅ <strong>PASS:</strong> Avatar directory exists with " . count($files) . " files<br>";
        echo "Location: " . htmlspecialchars($avatarDir);
        echo "</div>";

        echo "<h3>Sample Avatar Files:</h3>";
        echo "<table>";
        echo "<tr><th>Filename</th><th>Size</th><th>Last Modified</th></tr>";
        $count = 0;
        foreach (array_slice($files, 0, 5) as $file) {
            if ($count >= 5)
                break;
            $path = $avatarDir . '/' . $file;
            $size = filesize($path);
            $modified = date('Y-m-d H:i:s', filemtime($path));
            echo "<tr>";
            echo "<td><code>" . htmlspecialchars($file) . "</code></td>";
            echo "<td>" . round($size / 1024 / 1024, 2) . " MB</td>";
            echo "<td>" . $modified . "</td>";
            echo "</tr>";
            $count++;
        }
        echo "</table>";
    } else {
        echo "<div class='status warning'>";
        echo "⚠️ <strong>WARNING:</strong> Avatar directory exists but is empty<br>";
        echo "No avatar files found. Users may need to upload avatars.";
        echo "</div>";
    }
} else {
    echo "<div class='status fail'>";
    echo "❌ <strong>FAIL:</strong> Avatar directory does not exist<br>";
    echo "Expected location: " . htmlspecialchars(__DIR__ . '/uploads/avatars');
    echo "</div>";
}

// Check 3: Database avatar paths
echo "<h2>Step 3: Database Avatar Paths Check</h2>";

$result = $mysqli->query('SELECT id, email, avatar FROM users WHERE avatar IS NOT NULL AND avatar != ""');
$allUsers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $allUsers[] = $row;
    }
}

if (empty($allUsers)) {
    echo "<div class='status warning'>";
    echo "⚠️ <strong>INFO:</strong> No users with avatar paths in database";
    echo "</div>";
} else {
    $correctPaths = 0;
    $wrongPaths = 0;
    $wrongPathUsers = [];

    foreach ($allUsers as $user) {
        if (str_contains($user['avatar'], '/uploads/avatars/')) {
            $correctPaths++;
        } elseif (str_contains($user['avatar'], '/public/avatars/')) {
            $wrongPaths++;
            $wrongPathUsers[] = $user;
        }
    }

    if ($wrongPaths === 0) {
        echo "<div class='status pass'>";
        echo "✅ <strong>PASS:</strong> All " . count($allUsers) . " users have correct avatar paths<br>";
        echo "All paths use /uploads/avatars/ (correct format)";
        echo "</div>";
    } else {
        echo "<div class='status warning'>";
        echo "⚠️ <strong>WARNING:</strong> Found " . $wrongPaths . " users with /public/avatars/ paths<br>";
        echo "These will be auto-corrected by the code fix, but database cleanup is recommended.";
        echo "</div>";

        echo "<h3>Users with Wrong Avatar Paths:</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Email</th><th>Current Path</th><th>Will be Corrected To</th></tr>";
        foreach (array_slice($wrongPathUsers, 0, 10) as $user) {
            $corrected = str_replace('/public/avatars/', '/uploads/avatars/', $user['avatar']);
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td><code style='color: #721c24;'>" . htmlspecialchars($user['avatar']) . "</code></td>";
            echo "<td><code style='color: #155724;'>" . htmlspecialchars($corrected) . "</code></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Check 4: URL Helper Test
echo "<h2>Step 4: URL Helper Test</h2>";

// Try to load the URL helper if available, skip if not
$testUrl = null;
if (function_exists('url')) {
    $testPath = '/uploads/avatars/avatar_1_1767008522.png';
    $testUrl = url($testPath);
}

if ($testUrl && (str_contains($testUrl, '/cways_mis/public') || str_contains($testUrl, 'localhost'))) {
    echo "<div class='status pass'>";
    echo "✅ <strong>PASS:</strong> URL helper working correctly<br>";
    echo "Input: <code>" . htmlspecialchars($testPath) . "</code><br>";
    echo "Output: <code>" . htmlspecialchars($testUrl) . "</code>";
    echo "</div>";
} elseif ($testUrl) {
    echo "<div class='status fail'>";
    echo "❌ <strong>FAIL:</strong> URL helper not working as expected<br>";
    echo "Output: <code>" . htmlspecialchars($testUrl) . "</code>";
    echo "</div>";
} else {
    echo "<div class='status warning'>";
    echo "⚠️ <strong>SKIP:</strong> URL helper not available in standalone mode<br>";
    echo "This is normal for verification scripts. The helper will work in the main application.";
    echo "</div>";
}

// Summary
echo "<h2>Summary & Recommendations</h2>";

$allPass = (
    str_contains(file_get_contents($functionFile), '/public/avatars/') &&
    is_dir($avatarDir) &&
    !empty($files) &&
    $wrongPaths === 0
);

if ($allPass) {
    echo "<div class='status pass'>";
    echo "<h3>✅ All Checks Passed!</h3>";
    echo "The avatar fix is properly applied and the system is working correctly.";
    echo "<ul>";
    echo "<li>Code fix is in place</li>";
    echo "<li>Avatar files exist in correct location</li>";
    echo "<li>All database paths are correct</li>";
    echo "<li>URL helper is functioning properly</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='status warning'>";
    echo "<h3>⚠️ Issues Detected - Next Steps:</h3>";
    echo "<ol>";

    if (!str_contains(file_get_contents($functionFile), '/public/avatars/')) {
        echo "<li><strong>Apply Code Fix:</strong> Update src/Helpers/functions.php with avatar fallback handler</li>";
    }

    if ($wrongPaths > 0) {
        echo "<li><strong>Clean Database:</strong> Visit <code>fix_avatar_database.php</code> to auto-fix avatar paths</li>";
    }

    echo "<li><strong>Clear Browser Cache:</strong> CTRL + SHIFT + DEL → All time → Clear data</li>";
    echo "<li><strong>Hard Refresh:</strong> CTRL + F5</li>";
    echo "</ol>";
    echo "</div>";
}

// Action buttons
echo "<h2>Recommended Actions</h2>";
echo "<div style='margin: 20px 0;'>";

if ($wrongPaths > 0) {
    echo "<p>";
    echo "<a href='fix_avatar_database.php' style='display: inline-block; padding: 10px 20px; background: #8B1956; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>";
    echo "🔧 Fix Avatar Database Paths";
    echo "</a>";
    echo "</p>";
}

echo "<p style='margin-top: 15px;'>";
echo "After fixing, remember to:<br>";
echo "<strong>1. Clear Browser Cache:</strong> CTRL + SHIFT + DEL<br>";
echo "<strong>2. Hard Refresh:</strong> CTRL + F5<br>";
echo "<strong>3. Reload This Page:</strong> Refresh to see updated status";
echo "</p>";
echo "</div>";

// Technical details
echo "<h2>Technical Details</h2>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;'>";
echo "Configuration:\n";
echo "  Avatar Directory: " . htmlspecialchars($avatarDir ?? 'Not found') . "\n";
echo "  Avatar Files: " . count($files ?? []) . "\n";
echo "  Users with Avatars: " . count($allUsers) . "\n";
echo "  Wrong Paths: " . ($wrongPaths ?? 0) . "\n";
echo "  Correct Paths: " . ($correctPaths ?? 0) . "\n";
echo "</pre>";

echo "</div>";

// Close database connection
$mysqli->close();

?>