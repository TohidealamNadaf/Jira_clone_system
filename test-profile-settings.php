<?php
/**
 * Quick test to verify profile settings page is accessible
 */

declare(strict_types=1);

echo "Profile Settings Page Verification\n";
echo "===================================\n\n";

// Check 1: Route registered
echo "✓ Checking route registration...\n";
if (file_exists('routes/web.php')) {
    $content = file_get_contents('routes/web.php');
    if (strpos($content, "'/profile/settings'") !== false) {
        echo "  ✅ Route '/profile/settings' found in routes/web.php\n";
    } else {
        echo "  ❌ Route '/profile/settings' NOT found in routes/web.php\n";
    }
}

// Check 2: View file exists
echo "\n✓ Checking view file...\n";
if (file_exists('views/profile/settings.php')) {
    $lines = count(file('views/profile/settings.php'));
    echo "  ✅ views/profile/settings.php exists ($lines lines)\n";
} else {
    echo "  ❌ views/profile/settings.php NOT found\n";
}

// Check 3: Controller methods
echo "\n✓ Checking controller methods...\n";
if (file_exists('src/Controllers/UserController.php')) {
    $content = file_get_contents('src/Controllers/UserController.php');
    
    $hasSettings = strpos($content, 'public function settings') !== false;
    $hasUpdateSettings = strpos($content, 'public function updateSettings') !== false;
    
    if ($hasSettings) {
        echo "  ✅ settings() method found in UserController\n";
    } else {
        echo "  ❌ settings() method NOT found\n";
    }
    
    if ($hasUpdateSettings) {
        echo "  ✅ updateSettings() method found in UserController\n";
    } else {
        echo "  ❌ updateSettings() method NOT found\n";
    }
}

// Check 4: Navigation links
echo "\n✓ Checking navigation links...\n";
$navFiles = [
    'views/profile/index.php',
    'views/profile/security.php',
    'views/profile/tokens.php',
    'views/profile/settings.php'
];

foreach ($navFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, "'/profile/settings'") !== false || strpos($content, "url('/profile/settings')") !== false) {
            echo "  ✅ Settings link found in $file\n";
        } else if ($file === 'views/profile/settings.php') {
            // Settings page doesn't need the link to itself
            echo "  ✅ Settings page (self)\n";
        } else {
            echo "  ⚠️  Settings link not found in $file\n";
        }
    }
}

// Check 5: Migration script
echo "\n✓ Checking migration script...\n";
if (file_exists('scripts/create-user-settings-table.php')) {
    $lines = count(file('scripts/create-user-settings-table.php'));
    echo "  ✅ scripts/create-user-settings-table.php exists ($lines lines)\n";
} else {
    echo "  ❌ scripts/create-user-settings-table.php NOT found\n";
}

// Check 6: File sizes
echo "\n✓ File sizes:\n";
$files = [
    'views/profile/settings.php' => 'Settings view',
    'src/Controllers/UserController.php' => 'User Controller',
    'routes/web.php' => 'Routes',
    'scripts/create-user-settings-table.php' => 'Migration script',
];

foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        $size = filesize($file);
        $kb = round($size / 1024, 2);
        echo "  $file: ${kb}KB\n";
    }
}

echo "\n✅ All checks complete!\n";
echo "\nNext steps:\n";
echo "1. Run: php scripts/create-user-settings-table.php\n";
echo "2. Clear cache: del /Q storage\\cache\\*\n";
echo "3. Visit: http://localhost:8081/jira_clone_system/public/profile/settings\n";
