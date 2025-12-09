<?php
/**
 * Setup notification tables without foreign key issues
 */

$pdo = new PDO('mysql:host=localhost;port=3306', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Setting up Notification Tables ===\n\n";

try {
    // Use jira_clone database
    $pdo->exec('USE jira_clone');
    echo "✓ Connected to jira_clone database\n\n";
    
    // 1. Create notifications table (no FK issues here)
    echo "1. Creating notifications table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
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
            read_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            KEY notifications_user_unread_idx (user_id, is_read, created_at),
            KEY notifications_actor_user_id_idx (actor_user_id),
            KEY notifications_issue_id_idx (related_issue_id),
            KEY notifications_created_at_idx (created_at),
            KEY notifications_type_idx (type, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ notifications table created\n\n";
    
    // 2. Create notification_preferences table
    echo "2. Creating notification_preferences table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification_preferences (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT UNSIGNED NOT NULL,
            event_type VARCHAR(50) NOT NULL,
            in_app TINYINT(1) DEFAULT 1,
            email TINYINT(1) DEFAULT 1,
            push TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            UNIQUE KEY notification_preferences_user_event_unique (user_id, event_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ notification_preferences table created\n\n";
    
    // 3. Create notification_deliveries table
    echo "3. Creating notification_deliveries table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification_deliveries (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            notification_id INT UNSIGNED NOT NULL,
            channel VARCHAR(20) NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            sent_at TIMESTAMP NULL DEFAULT NULL,
            error_message TEXT,
            retry_count INT UNSIGNED DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            KEY notification_deliveries_status_idx (status, created_at),
            KEY notification_deliveries_notification_id_idx (notification_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ notification_deliveries table created\n\n";
    
    // 4. Create archive table
    echo "4. Creating notifications_archive table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications_archive LIKE notifications");
    echo "   ✓ notifications_archive table created\n\n";
    
    // 5. Add column to users table
    echo "5. Updating users table...\n";
    try {
        $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'unread_notifications_count'");
        if ($result->rowCount() === 0) {
            $pdo->exec("ALTER TABLE users ADD COLUMN unread_notifications_count INT UNSIGNED DEFAULT 0");
            echo "   ✓ Added unread_notifications_count column\n\n";
        } else {
            echo "   ⚠ Column already exists\n\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n\n";
    }
    
    // === VERIFICATION ===
    echo "=== VERIFICATION ===\n\n";
    
    $tables = ['notifications', 'notification_preferences', 'notification_deliveries', 'notifications_archive'];
    
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() > 0) {
                echo "✓ Table: $table\n";
                
                // Get columns
                $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_COLUMN, 0);
                echo "  Columns (" . count($cols) . "): " . implode(", ", $cols) . "\n";
            } else {
                echo "✗ Table: $table (NOT FOUND)\n";
            }
        } catch (Exception $e) {
            echo "✗ Error checking $table: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✅ Notification system setup complete!\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    exit(1);
}
