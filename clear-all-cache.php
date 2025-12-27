<?php
/**
 * Clear All Cache - Application & Browser
 * Run this to completely reset all caching
 */

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║              CLEARING ALL APPLICATION CACHE                    ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Get cache directory
$cacheDir = __DIR__ . '/storage/cache';

echo "📁 Cache Directory: {$cacheDir}\n\n";

// Clear cache files
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/cache_*');
    $count = 0;
    
    echo "🗑️  Clearing cache files...\n";
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
            echo "  ✓ Deleted: " . basename($file) . "\n";
        }
    }
    
    echo "\n✅ Deleted {$count} cache files\n";
} else {
    echo "⚠️  Cache directory not found\n";
}

echo "\n" . str_repeat("═", 64) . "\n\n";

echo "📋 NEXT STEPS:\n\n";

echo "1️⃣  Browser Cache:\n";
echo "   Windows: Press CTRL + SHIFT + DEL\n";
echo "   Mac: Safari → Preferences → Privacy\n\n";

echo "2️⃣  Hard Refresh:\n";
echo "   Windows: Press CTRL + F5\n";
echo "   Mac: Press CMD + SHIFT + R\n\n";

echo "3️⃣  Test the fix:\n";
echo "   Go to: /projects/CWAYS/roadmap\n";
echo "   Click: Add Item button\n";
echo "   Open Console: Press F12\n\n";

echo str_repeat("═", 64) . "\n";
echo "✅ Application cache cleared\n";
echo "   Now clear browser cache and hard refresh\n";
echo str_repeat("═", 64) . "\n";
?>
