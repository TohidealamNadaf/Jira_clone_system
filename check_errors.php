<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

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

echo "Checking notifications.log content:\n";
$logContext = file_get_contents(__DIR__ . '/storage/logs/notifications.log');
echo strlen($logContext) . " bytes\n";
echo "Error count in log: " . substr_count($logContext, '[NOTIFICATION ERROR]') . "\n";

echo "\nChecking notification_deliveries table failures:\n";
try {
    $failures = Database::select('SELECT COUNT(*) as count FROM notification_deliveries WHERE status = ?', ['failed']);
    echo "Failed deliveries count: " . $failures[0]['count'] . "\n";

    if ($failures[0]['count'] > 0) {
        echo "Example failures:\n";
        $examples = Database::select('SELECT * FROM notification_deliveries WHERE status = ? LIMIT 5', ['failed']);
        print_r($examples);
    }

} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
