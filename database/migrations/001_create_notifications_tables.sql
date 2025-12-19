-- Notifications System Tables
-- Supports 100+ developers with optimized indexing for performance

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
    -- Optimized for list queries by user with filtering
    KEY `notifications_user_unread_idx` (`user_id`, `is_read`, `created_at`),
    -- Optimized for finding actor's notifications
    KEY `notifications_actor_user_id_idx` (`actor_user_id`),
    -- Optimized for issue-related lookups
    KEY `notifications_issue_id_idx` (`related_issue_id`),
    -- Optimized for time-based queries (cleanup/archival)
    KEY `notifications_created_at_idx` (`created_at`),
    -- Type-based filtering
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
    -- Ensure one preference per user per event type
    UNIQUE KEY `notification_preferences_user_event_unique` (`user_id`, `event_type`),
    
    CONSTRAINT `notification_preferences_user_id_fk` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Notification delivery tracking (for email/push channels)
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
    -- Optimized for finding pending deliveries
    KEY `notification_deliveries_status_idx` (`status`, `created_at`),
    -- Optimized for notification cleanup
    KEY `notification_deliveries_notification_id_idx` (`notification_id`),
    
    CONSTRAINT `notification_deliveries_notification_id_fk` FOREIGN KEY (`notification_id`) 
        REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Add tracking columns to users table for performance
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `unread_notifications_count` INT UNSIGNED DEFAULT 0;

-- 5. Partition strategy for large-scale deployments (optional, for 100K+ notifications)
-- ALTER TABLE `notifications` PARTITION BY RANGE (YEAR(created_at)) (
--     PARTITION p2024 VALUES LESS THAN (2025),
--     PARTITION p2025 VALUES LESS THAN (2026),
--     PARTITION p2026 VALUES LESS THAN (2027),
--     PARTITION pmax VALUES LESS THAN MAXVALUE
-- );

-- 6. Archive table for old notifications (move notifications older than 90 days)
CREATE TABLE IF NOT EXISTS `notifications_archive` LIKE `notifications`;

-- Seed default preferences for existing users (run after notifications tables exist)
-- This ensures all users have preferences even if they don't explicitly create them
-- INSERT INTO `notification_preferences` (user_id, event_type, in_app, email, push)
-- SELECT DISTINCT id, 'issue_assigned', 1, 1, 0 FROM `users` 
-- WHERE NOT EXISTS (SELECT 1 FROM notification_preferences WHERE user_id = users.id AND event_type = 'issue_assigned');
