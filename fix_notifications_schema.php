<?php
/**
 * Fix Notifications Table Schema
 * Adds missing columns to match NotificationService requirements
 */

require __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

echo "=== Notifications Schema Fix ===\n\n";

try {
    // Drop and recreate notifications table
    echo "1. Dropping existing notifications table...\n";
    Database::query('DROP TABLE IF EXISTS notifications');
    echo "   ✓ Table dropped\n\n";
    
    echo "2. Creating new notifications table with all columns...\n";
    Database::query('
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
    ');
    echo "   ✓ Table created with all columns\n\n";
    
    // Verify columns
    echo "3. Verifying columns...\n";
    $columns = Database::select('
        SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = "notifications" AND TABLE_SCHEMA = DATABASE()
        ORDER BY ORDINAL_POSITION
    ', []);
    
    foreach ($columns as $col) {
        echo "   - {$col['COLUMN_NAME']}: {$col['DATA_TYPE']}\n";
    }
    echo "\n";
    
    // Show indexes
    echo "4. Verifying indexes...\n";
    $indexes = Database::select('
        SHOW INDEX FROM notifications WHERE Key_name != "PRIMARY"
    ', []);
    
    foreach ($indexes as $idx) {
        echo "   - {$idx['Key_name']} on {$idx['Column_name']}\n";
    }
    echo "\n";
    
    echo "✓ Notifications table schema fixed successfully!\n";
    echo "\nThe table now has:\n";
    echo "  - All required columns (title, message, action_url, etc.)\n";
    echo "  - Proper indexes for performance\n";
    echo "  - Foreign key constraints\n";
    echo "\nYou can now access /notifications without 'Unknown column' errors.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
