<?php
/**
 * Install notification system tables
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'jira_clone';

$pdo = new PDO("mysql:host=$host", $user, $pass);
$pdo->exec("USE $db");
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$pdo->exec("SET AUTOCOMMIT=1");

echo "=== Installing Notification Tables ===\n";

// Drop existing tables first
echo "Cleaning up old tables...\n";
$pdo->exec("DROP TABLE IF EXISTS notification_deliveries");
$pdo->exec("DROP TABLE IF EXISTS notifications_archive");
$pdo->exec("DROP TABLE IF EXISTS notification_preferences");
$pdo->exec("DROP TABLE IF EXISTS notifications");

// 1. Notifications table
echo "Creating notifications table...\n";
$pdo->exec("
    CREATE TABLE notifications (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        type VARCHAR(50) NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT,
        action_url VARCHAR(500),
        actor_user_id INT UNSIGNED,
        related_issue_id INT UNSIGNED,
        related_project_id INT UNSIGNED,
        priority VARCHAR(20) DEFAULT 'normal',
        is_read TINYINT(1) DEFAULT 0,
        read_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        KEY idx_user_unread (user_id, is_read, created_at),
        KEY idx_actor (actor_user_id),
        KEY idx_issue (related_issue_id),
        KEY idx_created (created_at),
        KEY idx_type (type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "✓ notifications created\n";

// 2. Notification preferences table
echo "Creating notification_preferences table...\n";
$pdo->exec("
    CREATE TABLE notification_preferences (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        event_type VARCHAR(50) NOT NULL,
        in_app TINYINT(1) DEFAULT 1,
        email TINYINT(1) DEFAULT 1,
        push TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        UNIQUE KEY uk_user_event (user_id, event_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "✓ notification_preferences created\n";

// 3. Notification deliveries table
echo "Creating notification_deliveries table...\n";
$pdo->exec("
    CREATE TABLE notification_deliveries (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        notification_id INT UNSIGNED NOT NULL,
        channel VARCHAR(20) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        sent_at TIMESTAMP NULL,
        error_message TEXT,
        retry_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        KEY idx_status (status),
        KEY idx_notification (notification_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "✓ notification_deliveries created\n";

// 4. Archive table
echo "Creating notifications_archive table...\n";
$pdo->exec("CREATE TABLE notifications_archive LIKE notifications");
echo "✓ notifications_archive created\n";

// 5. Add column to users
echo "Updating users table...\n";
$cols = $pdo->query("SHOW COLUMNS FROM users LIKE 'unread_notifications_count'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE users ADD COLUMN unread_notifications_count INT UNSIGNED DEFAULT 0");
    echo "✓ Added unread_notifications_count\n";
} else {
    echo "✓ Column already exists\n";
}

$pdo->exec("SET FOREIGN_KEY_CHECKS=1");

echo "\n=== Verification ===\n";
$tables = ['notifications', 'notification_preferences', 'notification_deliveries'];
foreach ($tables as $table) {
    $cnt = $pdo->query("SHOW TABLES LIKE '$table'")->rowCount();
    echo $cnt > 0 ? "✓ $table\n" : "✗ $table\n";
}

echo "\n✅ Installation complete!\n";
