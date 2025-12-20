<?php
echo "<h1>Cache Clear Tool</h1>";

$cachePath = __DIR__ . '/../storage/cache';

if (!is_dir($cachePath)) {
    echo "<p>Cache directory doesn't exist, creating it...</p>";
    mkdir($cachePath, 0755, true);
} else {
    echo "<p>Clearing cache directory: " . $cachePath . "</p>";
    
    $files = glob($cachePath . '/*');
    $count = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    
    echo "<p style='color: green;'>✓ Deleted $count cache files</p>";
}

// Also clear browser cache instruction
echo "<h2>Clear Browser Cache</h2>";
echo "<ol>";
echo "<li>Press <strong>CTRL + SHIFT + DEL</strong></li>";
echo "<li>Select <strong>All time</strong></li>";
echo "<li>Check <strong>Cookies and other site data</strong> and <strong>Cached images and files</strong></li>";
echo "<li>Click <strong>Clear data</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='/jira_clone_system/public/time-tracking' style='font-size: 18px; color: blue;'>👉 Try Time Tracking Again</a></p>";
?>
