<?php
$mod_rewrite = extension_loaded('mod_rewrite') || in_array('mod_rewrite', apache_get_modules());
echo "mod_rewrite enabled: " . ($mod_rewrite ? "YES ✓" : "NO ✗") . "\n";

if (function_exists('apache_get_modules')) {
    echo "\nLoaded Apache Modules:\n";
    foreach (apache_get_modules() as $module) {
        if (strpos($module, 'rewrite') !== false) {
            echo "✓ $module\n";
        }
    }
}
?>
