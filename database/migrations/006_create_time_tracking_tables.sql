-- =====================================================
-- TIME TRACKING & COST TRACKING MIGRATION
-- Created: December 2025
-- Purpose: Track time spent on issues and calculate costs
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 1;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

USE `cways_prod`;

-- =====================================================
-- USER RATES TABLE
-- Stores hourly/minutely/secondly rates per user
-- =====================================================

CREATE TABLE IF NOT EXISTS `user_rates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `rate_type` ENUM('hourly', 'minutely', 'secondly') NOT NULL DEFAULT 'hourly',
    `rate_amount` DECIMAL(10, 4) NOT NULL COMMENT 'Cost per unit (hour/minute/second)',
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `effective_from` DATE NOT NULL,
    `effective_until` DATE NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_rates_active_unique` (`user_id`, `rate_type`, `is_active`),
    KEY `user_rates_user_id_idx` (`user_id`),
    KEY `user_rates_effective_from_idx` (`effective_from`),
    KEY `user_rates_is_active_idx` (`is_active`),
    
    CONSTRAINT `user_rates_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ISSUE TIME LOGS TABLE
-- Tracks active/paused timers per issue per user
-- =====================================================

CREATE TABLE IF NOT EXISTS `issue_time_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED NOT NULL,
    
    -- Timer state
    `status` ENUM('running', 'paused', 'stopped') NOT NULL DEFAULT 'paused',
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NULL DEFAULT NULL,
    `paused_at` DATETIME NULL DEFAULT NULL,
    `resumed_at` DATETIME NULL DEFAULT NULL,
    
    -- Duration calculations
    `duration_seconds` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total seconds tracked',
    `paused_seconds` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total paused time',
    
    -- Cost calculation (server-side source of truth)
    `user_rate_type` ENUM('hourly', 'minutely', 'secondly') NOT NULL DEFAULT 'hourly',
    `user_rate_amount` DECIMAL(10, 4) NOT NULL,
    `total_cost` DECIMAL(12, 4) NOT NULL DEFAULT 0.00 COMMENT 'Total cost = duration_seconds * rate',
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    
    -- Metadata
    `description` TEXT NULL DEFAULT NULL COMMENT 'What user was working on',
    `is_billable` TINYINT(1) NOT NULL DEFAULT 1,
    `session_id` VARCHAR(128) NULL DEFAULT NULL COMMENT 'Browser session ID',
    
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `issue_time_logs_issue_id_idx` (`issue_id`),
    KEY `issue_time_logs_user_id_idx` (`user_id`),
    KEY `issue_time_logs_project_id_idx` (`project_id`),
    KEY `issue_time_logs_status_idx` (`status`),
    KEY `issue_time_logs_start_time_idx` (`start_time`),
    KEY `issue_time_logs_is_billable_idx` (`is_billable`),
    KEY `issue_time_logs_created_at_idx` (`created_at`),
    
    CONSTRAINT `issue_time_logs_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_time_logs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_time_logs_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ACTIVE TIMERS TABLE
-- Tracks currently running timers (fast lookup)
-- =====================================================

CREATE TABLE IF NOT EXISTS `active_timers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `issue_time_log_id` BIGINT UNSIGNED NOT NULL,
    `issue_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED NOT NULL,
    
    `started_at` DATETIME NOT NULL,
    `last_heartbeat` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `active_timers_user_id_unique` (`user_id`),
    KEY `active_timers_issue_time_log_id_idx` (`issue_time_log_id`),
    KEY `active_timers_issue_id_idx` (`issue_id`),
    KEY `active_timers_last_heartbeat_idx` (`last_heartbeat`),
    
    CONSTRAINT `active_timers_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `active_timers_issue_time_log_id_fk` FOREIGN KEY (`issue_time_log_id`) REFERENCES `issue_time_logs` (`id`) ON DELETE CASCADE,
    CONSTRAINT `active_timers_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `active_timers_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PROJECT BUDGETS TABLE
-- Tracks budget allocation per project
-- =====================================================

CREATE TABLE IF NOT EXISTS `project_budgets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    
    -- Budget allocation
    `total_budget` DECIMAL(12, 2) NOT NULL,
    `allocated_budget` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `remaining_budget` DECIMAL(12, 2) GENERATED ALWAYS AS (total_budget - allocated_budget) STORED,
    
    -- Cost tracking
    `total_cost` DECIMAL(12, 2) NOT NULL DEFAULT 0.00 COMMENT 'Total cost from all time logs',
    `budget_used_percentage` DECIMAL(5, 2) GENERATED ALWAYS AS (
        CASE 
            WHEN total_budget > 0 THEN (total_cost / total_budget) * 100
            ELSE 0
        END
    ) STORED,
    
    -- Status
    `status` ENUM('planning', 'active', 'paused', 'completed', 'exceeded') NOT NULL DEFAULT 'active',
    `alert_threshold` DECIMAL(5, 2) NOT NULL DEFAULT 80.00 COMMENT '% threshold for budget alert',
    `is_locked` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Prevent modifications if locked',
    
    -- Period
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    
    -- Notes
    `description` TEXT NULL DEFAULT NULL,
    
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `project_budgets_project_id_unique` (`project_id`),
    KEY `project_budgets_status_idx` (`status`),
    KEY `project_budgets_start_date_idx` (`start_date`),
    
    CONSTRAINT `project_budgets_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BUDGET ALERTS TABLE
-- Tracks when budget exceeds thresholds
-- =====================================================

CREATE TABLE IF NOT EXISTS `budget_alerts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_budget_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED NOT NULL,
    
    `alert_type` ENUM('warning', 'critical', 'exceeded') NOT NULL,
    `threshold_percentage` DECIMAL(5, 2) NOT NULL,
    `actual_percentage` DECIMAL(5, 2) NOT NULL,
    
    `cost_at_alert` DECIMAL(12, 2) NOT NULL,
    `remaining_budget_at_alert` DECIMAL(12, 2) NOT NULL,
    
    `is_acknowledged` TINYINT(1) NOT NULL DEFAULT 0,
    `acknowledged_by_user_id` INT UNSIGNED NULL DEFAULT NULL,
    `acknowledged_at` DATETIME NULL DEFAULT NULL,
    
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `budget_alerts_project_budget_id_idx` (`project_budget_id`),
    KEY `budget_alerts_project_id_idx` (`project_id`),
    KEY `budget_alerts_alert_type_idx` (`alert_type`),
    KEY `budget_alerts_is_acknowledged_idx` (`is_acknowledged`),
    KEY `budget_alerts_created_at_idx` (`created_at`),
    
    CONSTRAINT `budget_alerts_project_budget_id_fk` FOREIGN KEY (`project_budget_id`) REFERENCES `project_budgets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `budget_alerts_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `budget_alerts_acknowledged_by_user_id_fk` FOREIGN KEY (`acknowledged_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TIME TRACKING SETTINGS TABLE
-- Global configuration for time tracking system
-- =====================================================

CREATE TABLE IF NOT EXISTS `time_tracking_settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `organization_id` INT UNSIGNED NOT NULL DEFAULT 1,
    
    -- Default rates
    `default_hourly_rate` DECIMAL(10, 4) NOT NULL DEFAULT 50.00,
    `default_minutely_rate` DECIMAL(10, 6) NOT NULL DEFAULT 0.833333,
    `default_secondly_rate` DECIMAL(12, 8) NOT NULL DEFAULT 0.01388889,
    
    -- Behavior
    `auto_pause_on_logout` TINYINT(1) NOT NULL DEFAULT 1,
    `require_description_on_stop` TINYINT(1) NOT NULL DEFAULT 0,
    `minimum_trackable_duration_seconds` INT UNSIGNED NOT NULL DEFAULT 60 COMMENT 'Min 60 seconds',
    `max_concurrent_timers_per_user` INT UNSIGNED NOT NULL DEFAULT 1,
    
    -- Rounding
    `round_duration_to_minutes` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=no rounding, 5=round to 5 min',
    
    -- Currency
    `default_currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    
    -- Features
    `enable_billable_flag` TINYINT(1) NOT NULL DEFAULT 1,
    `enable_budget_tracking` TINYINT(1) NOT NULL DEFAULT 1,
    `enable_budget_alerts` TINYINT(1) NOT NULL DEFAULT 1,
    
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT DEFAULT CONFIGURATION
-- =====================================================

INSERT INTO `time_tracking_settings` (
    `organization_id`,
    `default_hourly_rate`,
    `default_minutely_rate`,
    `default_secondly_rate`,
    `auto_pause_on_logout`,
    `require_description_on_stop`,
    `minimum_trackable_duration_seconds`,
    `max_concurrent_timers_per_user`,
    `round_duration_to_minutes`,
    `default_currency`,
    `enable_billable_flag`,
    `enable_budget_tracking`,
    `enable_budget_alerts`
) VALUES (
    1,
    50.00,
    0.833333,
    0.01388889,
    1,
    0,
    60,
    1,
    0,
    'USD',
    1,
    1,
    1
) ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Composite indexes for common queries
CREATE INDEX idx_issue_time_logs_user_issue ON `issue_time_logs` (`user_id`, `issue_id`);
CREATE INDEX idx_issue_time_logs_project_date ON `issue_time_logs` (`project_id`, `created_at`);
CREATE INDEX idx_issue_time_logs_user_date ON `issue_time_logs` (`user_id`, `created_at`);
CREATE INDEX idx_issue_time_logs_billable_status ON `issue_time_logs` (`is_billable`, `status`);

-- =====================================================
-- END MIGRATION
-- =====================================================
