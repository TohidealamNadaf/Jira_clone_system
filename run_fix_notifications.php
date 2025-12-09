<?php
/**
 * Run Notifications Schema Fix
 * Direct MySQL connection
 */

try {
    $config = require __DIR__ . '/config/config.php';
    
    // Get database config
    $dbConfig = $config['database'];
    
    // Create connection
    $conn = new mysqli(
        $dbConfig['host'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['name']
    );
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<h2>Notifications Schema Fix</h2>\n";
    echo "<pre>\n";
    echo "Database: " . $dbConfig['name'] . "\n\n";
    
    // Turn off foreign key checks
    $conn->query('SET FOREIGN_KEY_CHECKS = 0');
    echo "1. Disabled foreign key checks\n";
    
    // Drop table
    $conn->query('DROP TABLE IF EXISTS `notifications`');
    echo "2. Dropped existing notifications table\n";
    
    // Create new table
    $sql = '
        CREATE TABLE `notifications` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED NOT NULL,
            `type` VARCHAR(100) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `message` TEXT DEFAULT NULL,
            `action_url` VARCHAR(500) DEFAULT NULL,
            `actor_user_id` INT UNSIGNED DEFAULT NULL,
            `related_issue_id` INT UNSIGNED DEFAULT NULL,
            `related_project_id` INT UNSIGNED DEFAULT NULL,
            `priority` VARCHAR(20) DEFAULT "normal",
            `is_read` TINYINT(1) DEFAULT 0,
            `read_at` TIMESTAMP NULL DEFAULT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `notifications_user_id_idx` (`user_id`),
            KEY `notifications_user_is_read_idx` (`user_id`, `is_read`, `created_at`),
            KEY `notifications_read_at_idx` (`read_at`),
            KEY `notifications_created_at_idx` (`created_at`),
            KEY `notifications_type_idx` (`type`),
            KEY `notifications_actor_user_id_idx` (`actor_user_id`),
            KEY `notifications_related_issue_id_idx` (`related_issue_id`),
            CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `notifications_actor_user_id_fk` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
            CONSTRAINT `notifications_related_issue_id_fk` FOREIGN KEY (`related_issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ';
    
    if (!$conn->query($sql)) {
        throw new Exception("Create table failed: " . $conn->error);
    }
    echo "3. Created new notifications table with all columns\n\n";
    
    // Re-enable foreign key checks
    $conn->query('SET FOREIGN_KEY_CHECKS = 1');
    echo "4. Enabled foreign key checks\n\n";
    
    // Verify columns
    echo "5. Verifying table structure:\n\n";
    $result = $conn->query("
        SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'notifications' AND TABLE_SCHEMA = DATABASE()
        ORDER BY ORDINAL_POSITION
    ");
    
    while ($row = $result->fetch_assoc()) {
        echo "   - {$row['COLUMN_NAME']}: {$row['DATA_TYPE']}\n";
    }
    
    echo "\n✓ SUCCESS! Notifications table is now ready.\n";
    echo "\nYou can now access: http://localhost:8080/jira_clone_system/public/notifications\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<h2>Error</h2>\n";
    echo "<pre>\n";
    echo "✗ " . $e->getMessage() . "\n";
    echo "</pre>\n";
}
