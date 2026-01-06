<?php
/**
 * IIS Setup Test Script
 */
echo "=== IIS Setup Diagnostics ===\n\n";

// Test 1: Check if script is running
echo "✓ PHP is working\n";

// Test 2: Check REQUEST_URI
echo "\nRequest Details:\n";
echo "  REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "  SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "  PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "\n";
echo "  QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'NOT SET') . "\n";

// Test 3: Check configuration
echo "\nConfiguration:\n";
try {
    require_once '../bootstrap/app.php';
    echo "  App URL: " . config('app.url') . "\n";
    echo "  Debug Mode: " . (config('app.debug') ? 'ON' : 'OFF') . "\n";
    echo "✓ Configuration loaded\n";
} catch (Exception $e) {
    echo "✗ Configuration error: " . $e->getMessage() . "\n";
}

// Test 4: Check database
echo "\nDatabase:\n";
try {
    $db = \App\Core\Database::getInstance();
    $result = $db->select("SELECT 1 as test");
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// Test 5: Check file permissions
echo "\nFile System:\n";
$dirs = [
    '../storage' => 'Storage',
    '../storage/logs' => 'Logs',
    './uploads' => 'Uploads',
];

foreach ($dirs as $path => $name) {
    if (is_writable($path)) {
        echo "  ✓ $name writable\n";
    } else {
        echo "  ✗ $name NOT writable\n";
    }
}

// Test 6: Check support platform tables
echo "\nSupport Platform Tables:\n";
$tables = [
    'support_tickets',
    'support_team_assignments',
    'ticket_interactions',
    'sla_policies',
    'customer_feedback'
];

try {
    foreach ($tables as $table) {
        $result = \App\Core\Database::select(
            "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
            [$table]
        );
        if (!empty($result)) {
            echo "  ✓ $table exists\n";
        } else {
            echo "  ✗ $table NOT found\n";
        }
    }
} catch (Exception $e) {
    echo "  ✗ Error checking tables: " . $e->getMessage() . "\n";
}

// Test 7: Route testing
echo "\nRoute Testing:\n";
echo "  Accessing: /support/dashboard\n";
echo "  Expected: Support Dashboard Controller\n";
echo "  Current REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";

echo "\n=== Diagnostics Complete ===\n";
echo "\nNext Steps:\n";
echo "1. If all tests pass, refresh your browser\n";
echo "2. If database tests fail, check credentials in config/config.php\n";
echo "3. If file permission tests fail, check IIS app pool identity\n";
echo "4. If routing shows issues, verify web.config files exist\n";
echo "\nAccess points to test:\n";
echo "  - http://localhost/jira_clone_system/public/\n";
echo "  - http://localhost/jira_clone_system/public/dashboard\n";
echo "  - http://localhost/jira_clone_system/public/support/dashboard\n";
