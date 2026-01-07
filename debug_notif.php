<?php

require_once __DIR__ . '/vendor/autoload.php';

// Mock Config
function config($key, $default = null)
{
    if ($key === 'database') {
        return [
            'host' => 'localhost',
            'port' => 3306,
            'name' => 'jira_clone',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ];
    }
    if ($key === 'app.debug')
        return true;
    return $default;
}

use App\Core\Database;
use App\Services\NotificationService;

try {
    echo "Testing NotificationService::updatePreference...\n";
    // Test case from NotificationController Line 370
    // [
    //     'user_id' => $userId,
    //     'event_type' => $eventType,
    //     'in_app' => (int) $inApp,
    //     'email' => (int) $email,
    //     'push' => (int) $push,
    // ]

    // Simulate data
    $userId = 1;
    $eventType = 'issue_created';
    $inApp = 1;
    $email = 1;
    $push = 0;

    $data = [
        'user_id' => $userId,
        'event_type' => $eventType,
        'in_app' => (int) $inApp,
        'email' => (int) $email,
        'push' => (int) $push,
    ];
    $uniqueKeys = ['user_id', 'event_type'];

    // Manually call insertOrUpdate to see where it fails
    // We can't easily reproduce the exact PDO state without full environment, 
    // but we can check the SQL generation.

    // Using reflection to test private methods or just test public method
    $result = Database::insertOrUpdate('notification_preferences', $data, $uniqueKeys);
    echo "Result: " . ($result ? 'Success' : 'Failure') . "\n";

} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n--------------------------------------------------\n";

// Search for 'key, title' recursively in PHP files to find the rogue query
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/src'));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'key, title') !== false) {
            echo "Found 'key, title' in: " . $file->getPathname() . "\n";
        }
    }
}
