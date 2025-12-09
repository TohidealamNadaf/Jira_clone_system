<?php
declare(strict_types=1);

require 'bootstrap/autoload.php';

echo "Checking Notification Database Tables...\n\n";

$db = app('db');

try {
    // Check if notifications table exists
    $tables = $db->select("
        SELECT TABLE_NAME 
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME IN ('notifications', 'notification_preferences', 'notification_deliveries', 'notifications_archive')
    ");
    
    $tableNames = array_map(fn($t) => $t['TABLE_NAME'], $tables);
    
    echo "Expected tables:\n";
    foreach (['notifications', 'notification_preferences', 'notification_deliveries', 'notifications_archive'] as $table) {
        $exists = in_array($table, $tableNames);
        $status = $exists ? '✓' : '✗';
        echo "  $status $table\n";
    }
    
    if (!in_array('notifications', $tableNames)) {
        echo "\nNotifications table missing!\n";
        echo "Run: php install_notifications.php\n";
        exit(1);
    }
    
    // Check sample data
    echo "\nDatabase content:\n";
    $count = $db->selectOne("SELECT COUNT(*) as count FROM notifications WHERE 1=1");
    echo "  Total notifications: " . ($count['count'] ?? 0) . "\n";
    
    echo "\n✓ Database check passed\n";
    
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit(1);
}
