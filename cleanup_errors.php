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

try {
    echo "Clearing failed deliveries from database...\n";
    $count = Database::delete('notification_deliveries', 'status = ?', ['failed']);
    echo "Deleted $count failed delivery records.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
