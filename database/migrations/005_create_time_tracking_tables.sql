-- =====================================================
-- TIME TRACKING & COST ANALYSIS MODULE
-- Created: December 2025
-- Purpose: Track time, calculate costs, and analyze budgets
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- USER RATES TABLE
-- =====================================================
-- Stores hourly/minutely/secondly rates for each user
-- Allows per-project rate overrides

CREATE TABLE IF NOT EXISTS `user_rates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED DEFAULT NULL,
    
    -- Rate configuration
    `rate_type` ENUM('hourly', 'minutely', 'secondly') NOT NULL DEFAULT 'hourly',
    `rate_amount` DECIMAL(10, 4) NOT NULL COMMENT 'Amount in currency (e.g., 75.5000 = $75.50/hour)',
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    
    -- Metadata
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `notes` TEXT DEFAULT NULL,
    `effective_from` DATE DEFAULT NULL,
    `effective_until` DATE DEFAULT NULL,
    
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_rates_user_project_unique` (`user_id`, `project_id`),
    KEY `user_rates_user_id_idx` (`user_id`),
    KEY `user_rates_project_id_idx` (`project_id`),
    KEY `user_rates_is_active_idx` (`is_active`),
    
    CONSTRAINT `user_rates_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `user_rates_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ISSUE TIME LOGS TABLE
-- =====================================================
-- Records individual time entries for issues
-- Server-side source of truth for time tracking

CREATE TABLE IF NOT EXISTS `issue_time_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    
    -- Timer state (CRITICAL for accuracy)
    `start_time` DATETIME NOT NULL COMMENT 'When work started (server time)',
    `end_time` DATETIME DEFAULT NULL COMMENT 'When work ended (NULL if running)',
    `pause_count` INT UNSIGNED DEFAULT 0 COMMENT 'Number of times paused',
    `total_paused_seconds` INT UNSIGNED DEFAULT 0 COMMENT 'Total seconds paused',
    
    -- Calculated values (NEVER computed on client)
    `duration_seconds` INT UNSIGNED DEFAULT 0 COMMENT 'Total work duration in seconds',
    `cost_calculated` DECIMAL(12, 2) DEFAULT 0.00 COMMENT 'Cost of this time entry',
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    
    -- Metadata
    `description` TEXT DEFAULT NULL COMMENT 'What work was done',
    `work_date` DATE NOT NULL COMMENT 'Date work was performed',
    `is_billable` TINYINT(1) NOT NULL DEFAULT 1,
    `status` ENUM('running', 'paused', 'stopped', 'archived') NOT NULL DEFAULT 'running',
    
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `issue_time_logs_issue_id_idx` (`issue_id`),
    KEY `issue_time_logs_user_id_idx` (`user_id`),
    KEY `issue_time_logs_work_date_idx` (`work_date`),
    KEY `issue_time_logs_status_idx` (`status`),
    KEY `issue_time_logs_start_time_idx` (`start_time`),
    
    CONSTRAINT `issue_time_logs_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_time_logs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PROJECT BUDGETS TABLE
-- =====================================================
-- Stores budget allocation per project
-- Tracks spending against budget

CREATE TABLE IF NOT EXISTS `project_budgets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    
    -- Budget configuration
    `budget_amount` DECIMAL(12, 2) NOT NULL COMMENT 'Total allocated budget',
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `budget_period` ENUM('monthly', 'quarterly', 'yearly', 'total') NOT NULL DEFAULT 'total',
    `period_start` DATE DEFAULT NULL,
    `period_end` DATE DEFAULT NULL,
    
    -- Spending (calculated, not direct input)
    `amount_spent` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `amount_remaining` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    
    -- Status & alerts
    `status` ENUM('active', 'paused', 'completed', 'exceeded') NOT NULL DEFAULT 'active',
    `alert_threshold` INT UNSIGNED DEFAULT 85 COMMENT 'Alert when spending reaches % of budget',
    `alert_sent` TINYINT(1) NOT NULL DEFAULT 0,
    
    -- Metadata
    `notes` TEXT DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `approved_by` INT UNSIGNED DEFAULT NULL,
    
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `project_budgets_project_unique` (`project_id`),
    KEY `project_budgets_project_id_idx` (`project_id`),
    KEY `project_budgets_status_idx` (`status`),
    
    CONSTRAINT `project_budgets_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `project_budgets_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `project_budgets_approved_by_fk` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ACTIVE TIMERS TABLE
-- =====================================================
-- Tracks currently running timers
-- Prevents overlapping timers per user
-- Used for floating timer state persistence

CREATE TABLE IF NOT EXISTS `active_timers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `issue_id` INT UNSIGNED NOT NULL,
    `time_log_id` INT UNSIGNED NOT NULL,
    
    -- Timer state
    `started_at` DATETIME NOT NULL,
    `last_activity_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `session_token` VARCHAR(255) NOT NULL COMMENT 'Unique token for this browser session',
    
    -- Browser info (for multi-tab detection)
    `browser_tab_id` VARCHAR(255) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `active_timers_user_unique` (`user_id`) COMMENT 'Only one timer per user',
    KEY `active_timers_user_id_idx` (`user_id`),
    KEY `active_timers_issue_id_idx` (`issue_id`),
    KEY `active_timers_time_log_id_idx` (`time_log_id`),
    KEY `active_timers_session_token_idx` (`session_token`),
    
    CONSTRAINT `active_timers_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `active_timers_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `active_timers_time_log_id_fk` FOREIGN KEY (`time_log_id`) REFERENCES `issue_time_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TIME TRACKING REPORTS TABLE
-- =====================================================
-- Pre-calculated reports for performance
-- Refreshed periodically via cron job

CREATE TABLE IF NOT EXISTS `time_tracking_reports` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `report_type` ENUM('user_daily', 'user_weekly', 'project_daily', 'project_weekly', 'budget_status') NOT NULL,
    `report_date` DATE NOT NULL,
    
    -- Entity references
    `user_id` INT UNSIGNED DEFAULT NULL,
    `project_id` INT UNSIGNED DEFAULT NULL,
    
    -- Report data (JSON for flexibility)
    `report_data` JSON NOT NULL,
    
    -- Metadata
    `generated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `cache_valid_until` DATETIME NOT NULL,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `time_tracking_reports_unique` (`report_type`, `report_date`, `user_id`, `project_id`),
    KEY `time_tracking_reports_user_id_idx` (`user_id`),
    KEY `time_tracking_reports_project_id_idx` (`project_id`),
    KEY `time_tracking_reports_report_type_idx` (`report_type`),
    
    CONSTRAINT `time_tracking_reports_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `time_tracking_reports_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Composite indexes for common queries
CREATE INDEX idx_time_logs_user_issue ON `issue_time_logs` (`user_id`, `issue_id`);
CREATE INDEX idx_time_logs_date_range ON `issue_time_logs` (`work_date`, `status`);
CREATE INDEX idx_budgets_period ON `project_budgets` (`budget_period`, `period_start`, `period_end`);

SET FOREIGN_KEY_CHECKS = 1;
