-- Create roadmap_goals table
CREATE TABLE IF NOT EXISTS `roadmap_goals` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` ENUM('planned', 'in_progress', 'achieved', 'missed') NOT NULL DEFAULT 'planned',
    `progress_percentage` INT UNSIGNED DEFAULT 0,
    `color` VARCHAR(7) DEFAULT '#8b1956',
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `roadmap_goals_project_id_idx` (`project_id`),
    CONSTRAINT `roadmap_goals_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `roadmap_goals_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add goal_id to roadmap_items
ALTER TABLE `roadmap_items` ADD COLUMN `goal_id` INT UNSIGNED DEFAULT NULL AFTER `project_id`;
ALTER TABLE `roadmap_items` ADD KEY `roadmap_items_goal_id_idx` (`goal_id`);
ALTER TABLE `roadmap_items` ADD CONSTRAINT `roadmap_items_goal_id_fk` FOREIGN KEY (`goal_id`) REFERENCES `roadmap_goals` (`id`) ON DELETE SET NULL;
