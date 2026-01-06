-- Fix Notifications Table Schema
-- This adds missing columns that NotificationService expects

SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing notifications table
DROP TABLE IF EXISTS `notifications`;

-- Create new notifications table with all required columns
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
    `priority` VARCHAR(20) DEFAULT 'normal',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
