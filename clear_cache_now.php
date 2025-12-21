<?php
// Clear cache directory
$cacheDir = __DIR__ . '/storage/cache';

if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ Cache cleared successfully\n";
} else {
    echo "⚠️ Cache directory not found\n";
}

// Also clear browser cache headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

echo "✅ Done! Clear your browser cache (CTRL+SHIFT+DEL) and refresh (CTRL+F5)\n";
?>
