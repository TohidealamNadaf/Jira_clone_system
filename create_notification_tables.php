<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

try {
    echo "Creating notification tables...\n\n";

    // Table 1: notification_preferences
    $sql1 = <<<SQL
    CREATE TABLE IF NOT EXISTS `notification_preferences` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT UNSIGNED NOT NULL,
        `event_type` ENUM('issue_created', 'issue_assigned', 'issue_commented',
                          'issue_status_changed', 'issue_mentioned', 'issue_watched',
                          'project_created', 'project_member_added', 'comment_reply',
                          'all') NOT NULL,
        `in_app` TINYINT(1) DEFAULT 1,
        `email` TINYINT(1) DEFAULT 1,
        `push` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `notification_preferences_user_event_unique` (`user_id`, `event_type`),
        CONSTRAINT `notification_preferences_user_id_fk` FOREIGN KEY (`user_id`) 
            REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    SQL;

    Database::query($sql1);
    echo "✅ Table 'notification_preferences' created successfully.\n";

    // Table 2: notifications_archive
    $sql2 = <<<SQL
    CREATE TABLE IF NOT EXISTS `notifications_archive` (
        `id` BIGINT UNSIGNED NOT NULL,
        `user_id` INT UNSIGNED NOT NULL,
        `type` VARCHAR(100) NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT DEFAULT NULL,
        `action_url` VARCHAR(500) DEFAULT NULL,
        `actor_user_id` INT UNSIGNED DEFAULT NULL,
        `related_issue_id` INT UNSIGNED DEFAULT NULL,
        `related_project_id` INT UNSIGNED DEFAULT NULL,
        `priority` VARCHAR(20) DEFAULT 'normal',
        `is_read` TINYINT(1) DEFAULT 0,
        `read_at` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `archived_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `notifications_archive_user_id_idx` (`user_id`),
        KEY `notifications_archive_created_at_idx` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    SQL;

    Database::query($sql2);
    echo "✅ Table 'notifications_archive' created successfully.\n\n";

    // Verify tables
    echo "Verifying tables...\n";
    $tables = Database::select("SHOW TABLES LIKE 'notification%'", []);
    
    echo "Tables created:\n";
    foreach ($tables as $table) {
        echo "  - " . reset($table) . "\n";
    }

    echo "\n✅ All notification tables created successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
