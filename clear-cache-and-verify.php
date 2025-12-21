<?php
/**
 * Clear cache and verify fix
 * Run this AFTER restarting Apache
 */

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  Cache Cleaner & Verification Tool - API Lookup Fix            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Step 1: Clear cache directory
echo "Step 1: Clearing application cache...\n";
$cacheDir = __DIR__ . '/storage/cache';
$cleared = 0;

if (is_dir($cacheDir)) {
    $files = array_diff(scandir($cacheDir), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $cacheDir . '/' . $file;
        if (is_file($filePath)) {
            unlink($filePath);
            $cleared++;
        }
    }
    echo "✅ Cleared $cleared cache files\n\n";
} else {
    echo "⚠️  Cache directory not found\n\n";
}

// Step 2: Verify routes file
echo "Step 2: Verifying routes/api.php...\n";
$routesFile = __DIR__ . '/routes/api.php';
if (file_exists($routesFile)) {
    $content = file_get_contents($routesFile);
    
    $checks = [
        "'/issue-types'" => "Issue types route",
        "'/priorities'" => "Priorities route",
        "'/statuses'" => "Statuses route",
        "'/link-types'" => "Link types route",
    ];
    
    foreach ($checks as $search => $label) {
        if (strpos($content, $search) !== false) {
            echo "✅ $label found\n";
        } else {
            echo "❌ $label NOT found\n";
        }
    }
} else {
    echo "❌ routes/api.php not found\n";
}

echo "\n";

// Step 3: Verify controller methods
echo "Step 3: Verifying IssueApiController methods...\n";
$controllerFile = __DIR__ . '/src/Controllers/Api/IssueApiController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $checks = [
        "public function issueTypes(Request \$request): never" => "issueTypes() return type",
        "public function priorities(Request \$request): never" => "priorities() return type",
        "public function statuses(Request \$request): never" => "statuses() return type",
    ];
    
    foreach ($checks as $search => $label) {
        if (strpos($content, $search) !== false) {
            echo "✅ $label is 'never'\n";
        } else {
            echo "❌ $label NOT 'never'\n";
        }
    }
} else {
    echo "❌ IssueApiController.php not found\n";
}

echo "\n";

// Step 4: Display what to do next
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  NEXT STEPS                                                    ║\n";
echo "╠════════════════════════════════════════════════════════════════╣\n";
echo "║                                                                ║\n";
echo "║  IF YOU HAVEN'T RESTARTED APACHE YET:                         ║\n";
echo "║  ─────────────────────────────────────                         ║\n";
echo "║  1. Close all browser tabs/windows                            ║\n";
echo "║  2. Restart Apache:                                           ║\n";
echo "║     • Laragon: Click icon → Apache → Restart                  ║\n";
echo "║     • XAMPP: Control Panel → Apache → Stop then Start         ║\n";
echo "║     • Command: net stop Apache2.4 && net start Apache2.4      ║\n";
echo "║  3. Wait for \"Apache started\" message                         ║\n";
echo "║  4. Open browser and go to this page again                    ║\n";
echo "║  5. Do hard refresh: CTRL+F5                                  ║\n";
echo "║  6. Clear browser cache: CTRL+SHIFT+DEL                       ║\n";
echo "║                                                                ║\n";
echo "║  THEN:                                                         ║\n";
echo "║  ──────                                                        ║\n";
echo "║  7. Test Create Issue Modal                                   ║\n";
echo "║  8. Check console (F12) for:                                  ║\n";
echo "║     ✅ Issue types loaded                                     ║\n";
echo "║     ✅ Loaded priorities                                      ║\n";
echo "║                                                                ║\n";
echo "║  IF STILL NOT WORKING:                                        ║\n";
echo "║  ─────────────────────────                                    ║\n";
echo "║  1. Verify Apache is actually running (green status)          ║\n";
echo "║  2. Check this page shows all ✅ checks pass                  ║\n";
echo "║  3. Look at browser console for actual error message          ║\n";
echo "║                                                                ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";

// Step 5: Test API endpoint directly
echo "\n\nStep 4: Testing API endpoint directly...\n";
echo "─────────────────────────────────────────\n";

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$baseUrl = $protocol . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
$apiUrl = $baseUrl . '/api/v1/issue-types';

echo "Testing URL: $apiUrl\n";
echo "Expected: JSON array of issue types\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ API returned 200 OK\n";
    $data = json_decode($response, true);
    if (is_array($data)) {
        echo "✅ Response is valid JSON\n";
        echo "✅ Found " . count($data) . " issue types\n";
    }
} else {
    echo "❌ API returned HTTP $httpCode\n";
    if ($httpCode === 404) {
        echo "   → Routes not yet registered (Apache may need restart)\n";
    }
}

echo "\n";
?>
