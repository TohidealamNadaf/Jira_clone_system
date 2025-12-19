-- Fix Notifications System Tables
-- Directly create tables if they don't exist

-- 1. Main notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('issue_created', 'issue_assigned', 'issue_commented', 
                'issue_status_changed', 'issue_mentioned', 'issue_watched',
                'project_created', 'project_member_added', 'comment_reply',
                'custom') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT,
    `action_url` VARCHAR(500),
    `actor_user_id` INT UNSIGNED,
    `related_issue_id` INT UNSIGNED,
    `related_project_id` INT UNSIGNED,
    `priority` ENUM('high', 'normal', 'low') DEFAULT 'normal',
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `notifications_user_unread_idx` (`user_id`, `is_read`, `created_at`),
    KEY `notifications_actor_user_id_idx` (`actor_user_id`),
    KEY `notifications_issue_id_idx` (`related_issue_id`),
    KEY `notifications_created_at_idx` (`created_at`),
    KEY `notifications_type_idx` (`type`, `created_at`),
    
    CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `notifications_actor_user_id_fk` FOREIGN KEY (`actor_user_id`) 
        REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `notifications_issue_id_fk` FOREIGN KEY (`related_issue_id`) 
        REFERENCES `issues` (`id`) ON DELETE SET NULL,
    CONSTRAINT `notifications_project_id_fk` FOREIGN KEY (`related_project_id`) 
        REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. User notification preferences
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

-- 3. Notification delivery tracking
CREATE TABLE IF NOT EXISTS `notification_deliveries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `notification_id` INT UNSIGNED NOT NULL,
    `channel` ENUM('in_app', 'email', 'push') NOT NULL,
    `status` ENUM('pending', 'sent', 'failed', 'bounced') DEFAULT 'pending',
    `sent_at` TIMESTAMP NULL DEFAULT NULL,
    `error_message` TEXT,
    `retry_count` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `notification_deliveries_status_idx` (`status`, `created_at`),
    KEY `notification_deliveries_notification_id_idx` (`notification_id`),
    
    CONSTRAINT `notification_deliveries_notification_id_fk` FOREIGN KEY (`notification_id`) 
        REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Archive table for old notifications
CREATE TABLE IF NOT EXISTS `notifications_archive` LIKE `notifications`;

-- 5. Add column to users table if not exists
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `unread_notifications_count` INT UNSIGNED DEFAULT 0;
