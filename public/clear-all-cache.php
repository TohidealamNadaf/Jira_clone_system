<?php
echo "<h1>Clearing All Caches</h1>";

// Clear PHP cache
$cachePath = __DIR__ . '/../storage/cache';
if (is_dir($cachePath)) {
    foreach (glob($cachePath . '/*') as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "<p>✓ Cleared storage/cache</p>";
}

// Clear opcache if enabled
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p>✓ Reset PHP OPcache</p>";
}

echo "<hr>";
echo "<h2>Now Clear Browser Cache</h2>";
echo "<ol>";
echo "<li>Press <strong>CTRL + SHIFT + DEL</strong></li>";
echo "<li>Select <strong>All time</strong></li>";
echo "<li>Check <strong>Cookies</strong> and <strong>Cached images and files</strong></li>";
echo "<li>Click <strong>Clear data</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='/jira_clone_system/public/time-tracking' style='font-size: 18px; color: blue; font-weight: bold;'>👉 Go to Time Tracking Now</a></p>";
?>
