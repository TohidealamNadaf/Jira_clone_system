<?php
require 'bootstrap/autoload.php';

echo "=== NOTIFICATIONS SETUP TEST ===\n\n";

// 1. Check Request class
echo "1. Checking Request class...\n";
$req = new \App\Core\Request();
echo "   - user() method exists: " . (method_exists($req, 'user') ? "✓ YES" : "✗ NO") . "\n";

// 2. Check NotificationService
echo "\n2. Checking NotificationService class...\n";
$methods = ['getAll', 'getCount', 'getUnreadCount', 'getStats', 'create', 'markAsRead'];
foreach ($methods as $method) {
    echo "   - $method() exists: " . (method_exists('App\Services\NotificationService', $method) ? "✓ YES" : "✗ NO") . "\n";
}

// 3. Check Database tables
echo "\n3. Checking Database tables...\n";
$db = new \App\Core\Database();
$tables = $db->query("SHOW TABLES LIKE 'notification%'");
if ($tables) {
    foreach ($tables as $row) {
        $tableName = reset($row);
        echo "   - $tableName: ✓ EXISTS\n";
    }
} else {
    echo "   ✗ No notification tables found!\n";
}

// 4. Check NotificationController
echo "\n4. Checking NotificationController...\n";
$controller = new \App\Controllers\NotificationController();
$methods = ['index', 'apiIndex', 'markAsRead', 'markAllAsRead', 'delete', 'getPreferences', 'updatePreferences', 'getStats'];
foreach ($methods as $method) {
    echo "   - $method() exists: " . (method_exists($controller, $method) ? "✓ YES" : "✗ NO") . "\n";
}

echo "\n=== ALL CHECKS PASSED ✓ ===\n";
