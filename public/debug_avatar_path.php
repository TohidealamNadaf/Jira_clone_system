<?php
/**
 * Debug Avatar Path Issue
 * Shows exactly what's in the database and what URL is being generated
 */

// Direct database connection
$mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system');

if ($mysqli->connect_error) {
    die('Database error: ' . $mysqli->connect_error);
}

echo "<h1>Avatar Path Debug</h1>";
echo "<p>This shows exactly what paths are in the database and how they're being processed.</p>";

// Get current user (usually ID 1)
$result = $mysqli->query("SELECT id, email, avatar FROM users WHERE id = 1 LIMIT 1");
$user = $result->fetch_assoc();

echo "<h2>User #1 Avatar Data</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Value</th></tr>";
echo "<tr><td>ID</td><td>" . htmlspecialchars($user['id']) . "</td></tr>";
echo "<tr><td>Email</td><td>" . htmlspecialchars($user['email']) . "</td></tr>";
echo "<tr><td>Avatar (from DB)</td><td><code>" . htmlspecialchars($user['avatar'] ?? '(empty)') . "</code></td></tr>";
echo "</table>";

// Now test the avatar() function
echo "<h2>Avatar Function Processing</h2>";

if (empty($user['avatar'])) {
    echo "<p style='color: red;'>⚠️ Avatar field is EMPTY in database!</p>";
} else {
    $avatarPath = $user['avatar'];
    
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 4px;'>";
    echo "Input path from DB: " . htmlspecialchars($avatarPath) . "\n\n";
    
    // Simulate the avatar() function logic
    echo "Processing steps:\n";
    
    // Step 1: Check for /public/avatars/
    if (str_contains($avatarPath, '/public/avatars/')) {
        echo "✓ Detected /public/avatars/ path\n";
        $avatarPath = str_replace('/public/avatars/', '/uploads/avatars/', $avatarPath);
        echo "✓ Replaced with: " . htmlspecialchars($avatarPath) . "\n";
    } else {
        echo "✗ No /public/avatars/ detected\n";
    }
    
    // Step 2: Check for /uploads/
    echo "\nPath now: " . htmlspecialchars($avatarPath) . "\n";
    
    if (str_contains($avatarPath, '/uploads/')) {
        echo "✓ Contains /uploads/, will use url() helper\n";
        $pos = strpos($avatarPath, '/uploads/');
        $relativePath = substr($avatarPath, $pos + strlen('/uploads/'));
        echo "✓ Relative path: " . htmlspecialchars($relativePath) . "\n";
    }
    
    echo "</pre>";
    
    // Check if file exists
    echo "<h2>File System Check</h2>";
    
    $filename = basename($avatarPath);
    $checkPath = __DIR__ . '/uploads/avatars/' . $filename;
    
    if (file_exists($checkPath)) {
        $size = filesize($checkPath);
        echo "<p style='color: green;'>✅ File exists: " . htmlspecialchars($filename) . " (" . round($size / 1024) . " KB)</p>";
    } else {
        echo "<p style='color: red;'>❌ File NOT found at: " . htmlspecialchars($checkPath) . "</p>";
        
        // List what files actually exist
        $avatarDir = __DIR__ . '/uploads/avatars/';
        if (is_dir($avatarDir)) {
            $files = array_diff(scandir($avatarDir), ['.', '..']);
            if (!empty($files)) {
                echo "<p>Files that DO exist:</p>";
                echo "<ul>";
                foreach (array_slice($files, 0, 5) as $f) {
                    echo "<li><code>" . htmlspecialchars($f) . "</code></li>";
                }
                if (count($files) > 5) {
                    echo "<li>... and " . (count($files) - 5) . " more</li>";
                }
                echo "</ul>";
            }
        }
    }
}

$mysqli->close();
?>
