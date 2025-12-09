<?php
/**
 * Create notification tables directly
 */

try {
    $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '');
    
    echo "=== Creating Notification Tables ===\n\n";
    
    // First, determine which database to use
    $databases = $pdo->query("SHOW DATABASES LIKE '%jira%'")->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($databases)) {
        echo "No Jira database found. Checking all databases:\n";
        $all = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
        echo implode(", ", $all) . "\n";
        exit(1);
    }
    
    $database = $databases[0];
    echo "Using database: $database\n\n";
    
    $pdo->exec("USE $database");
    
    // 1. Create notifications table
    echo "1. Creating notifications table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT UNSIGNED NOT NULL,
            type ENUM('issue_created', 'issue_assigned', 'issue_commented', 
                        'issue_status_changed', 'issue_mentioned', 'issue_watched',
                        'project_created', 'project_member_added', 'comment_reply',
                        'custom') NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT,
            action_url VARCHAR(500),
            actor_user_id INT UNSIGNED,
            related_issue_id INT UNSIGNED,
            related_project_id INT UNSIGNED,
            priority ENUM('high', 'normal', 'low') DEFAULT 'normal',
            is_read TINYINT(1) DEFAULT 0,
            read_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            KEY notifications_user_unread_idx (user_id, is_read, created_at),
            KEY notifications_actor_user_id_idx (actor_user_id),
            KEY notifications_issue_id_idx (related_issue_id),
            KEY notifications_created_at_idx (created_at),
            KEY notifications_type_idx (type, created_at),
            
            CONSTRAINT notifications_user_id_fk FOREIGN KEY (user_id) 
                REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT notifications_actor_user_id_fk FOREIGN KEY (actor_user_id) 
                REFERENCES users (id) ON DELETE SET NULL,
            CONSTRAINT notifications_issue_id_fk FOREIGN KEY (related_issue_id) 
                REFERENCES issues (id) ON DELETE SET NULL,
            CONSTRAINT notifications_project_id_fk FOREIGN KEY (related_project_id) 
                REFERENCES projects (id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ notifications table created\n";
    
    // 2. Create notification_preferences table
    echo "2. Creating notification_preferences table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification_preferences (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT UNSIGNED NOT NULL,
            event_type ENUM('issue_created', 'issue_assigned', 'issue_commented',
                              'issue_status_changed', 'issue_mentioned', 'issue_watched',
                              'project_created', 'project_member_added', 'comment_reply',
                              'all') NOT NULL,
            in_app TINYINT(1) DEFAULT 1,
            email TINYINT(1) DEFAULT 1,
            push TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            UNIQUE KEY notification_preferences_user_event_unique (user_id, event_type),
            
            CONSTRAINT notification_preferences_user_id_fk FOREIGN KEY (user_id) 
                REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ notification_preferences table created\n";
    
    // 3. Create notification_deliveries table
    echo "3. Creating notification_deliveries table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification_deliveries (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            notification_id INT UNSIGNED NOT NULL,
            channel ENUM('in_app', 'email', 'push') NOT NULL,
            status ENUM('pending', 'sent', 'failed', 'bounced') DEFAULT 'pending',
            sent_at TIMESTAMP NULL DEFAULT NULL,
            error_message TEXT,
            retry_count INT UNSIGNED DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            KEY notification_deliveries_status_idx (status, created_at),
            KEY notification_deliveries_notification_id_idx (notification_id),
            
            CONSTRAINT notification_deliveries_notification_id_fk FOREIGN KEY (notification_id) 
                REFERENCES notifications (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ notification_deliveries table created\n";
    
    // 4. Create archive table
    echo "4. Creating notifications_archive table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications_archive LIKE notifications");
    echo "   ✓ notifications_archive table created\n";
    
    // 5. Add column to users table
    echo "5. Adding unread_notifications_count to users table...\n";
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN unread_notifications_count INT UNSIGNED DEFAULT 0");
        echo "   ✓ Column added\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "   ⚠ Column already exists\n";
        } else {
            throw $e;
        }
    }
    
    echo "\n=== Verification ===\n\n";
    
    // Verify tables
    $tables = ['notifications', 'notification_preferences', 'notification_deliveries'];
    
    foreach ($tables as $table) {
        $count = $pdo->query("SHOW TABLES LIKE '$table'")->rowCount();
        if ($count > 0) {
            echo "✓ $table\n";
            $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_COLUMN, 0);
            echo "  Columns: " . implode(", ", $cols) . "\n";
        } else {
            echo "✗ $table NOT FOUND\n";
        }
    }
    
    echo "\n✅ All tables created successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
