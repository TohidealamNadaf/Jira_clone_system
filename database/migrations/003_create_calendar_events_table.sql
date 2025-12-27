-- =====================================================
-- CALENDAR EVENTS TABLE
-- =====================================================

CREATE TABLE `calendar_events` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_type` ENUM('issue', 'sprint', 'milestone', 'reminder', 'meeting') NOT NULL DEFAULT 'reminder',
    `project_id` INT UNSIGNED DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `start_date` DATETIME NOT NULL,
    `end_date` DATETIME NOT NULL,
    `priority_id` INT UNSIGNED DEFAULT NULL,
    `attendees` TEXT DEFAULT NULL COMMENT 'Comma-separated user emails or JSON',
    `reminders` JSON DEFAULT NULL COMMENT 'Reminder settings array',
    `recurring_type` ENUM('none', 'daily', 'weekly', 'monthly', 'yearly', 'custom') NOT NULL DEFAULT 'none',
    `recurring_interval` INT UNSIGNED DEFAULT NULL COMMENT 'Interval for recurring events',
    `recurring_ends` ENUM('never', 'after', 'on') DEFAULT NULL,
    `recurring_end_date` DATE DEFAULT NULL COMMENT 'End date for recurring events',
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `calendar_events_event_type_idx` (`event_type`),
    KEY `calendar_events_project_id_idx` (`project_id`),
    KEY `calendar_events_priority_id_idx` (`priority_id`),
    KEY `calendar_events_created_by_idx` (`created_by`),
    KEY `calendar_events_start_date_idx` (`start_date`),
    KEY `calendar_events_end_date_idx` (`end_date`),
    KEY `calendar_events_created_at_idx` (`created_at`),
    CONSTRAINT `calendar_events_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
    CONSTRAINT `calendar_events_priority_id_fk` FOREIGN KEY (`priority_id`) REFERENCES `issue_priorities` (`id`) ON DELETE SET NULL,
    CONSTRAINT `calendar_events_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Calendar events separate from issues for meetings, reminders, etc.';