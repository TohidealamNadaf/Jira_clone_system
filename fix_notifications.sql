CREATE TABLE IF NOT EXISTS `notification_dispatch_log` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `dispatch_id` VARCHAR(255) UNIQUE NOT NULL,
    `dispatch_type` ENUM('comment_added', 'status_changed', 'other') NOT NULL,
    `issue_id` INT UNSIGNED NOT NULL,
    `comment_id` INT UNSIGNED NULL,
    `actor_user_id` INT UNSIGNED NOT NULL,
    `recipients_count` INT UNSIGNED DEFAULT 0,
    `status` ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `completed_at` TIMESTAMP NULL,
    `error_message` TEXT NULL,
    INDEX `idx_dispatch_id` (`dispatch_id`),
    INDEX `idx_issue_id` (`issue_id`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT "Table notification_dispatch_log created or already exists" as status;
