<?php
/**
 * Clear Cache
 */

$cacheDir = __DIR__ . '/storage/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }
    echo "✅ Cache cleared<br>";
} else {
    echo "Cache directory not found<br>";
}

// Also output success message
echo "API fix deployed! The json() helper function has been added.<br>";
echo "<br>";
echo "Try accessing: <a href='/jira_clone_system/public/api/v1/issue-types' target='_blank'>/jira_clone_system/public/api/v1/issue-types</a><br>";
echo "<br>";
echo "The Create Issue Modal should now load issue types correctly.";
?>
