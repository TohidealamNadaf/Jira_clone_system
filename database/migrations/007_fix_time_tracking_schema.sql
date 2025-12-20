-- =====================================================
-- FIX TIME TRACKING SCHEMA
-- December 19, 2025
-- Purpose: Add missing columns to issue_time_logs table
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Add missing columns to issue_time_logs
ALTER TABLE `issue_time_logs` ADD COLUMN IF NOT EXISTS `project_id` INT UNSIGNED DEFAULT NULL AFTER `issue_id`;
ALTER TABLE `issue_time_logs` ADD COLUMN IF NOT EXISTS `paused_at` DATETIME DEFAULT NULL AFTER `end_time`;
ALTER TABLE `issue_time_logs` ADD COLUMN IF NOT EXISTS `paused_seconds` INT UNSIGNED DEFAULT 0 AFTER `total_paused_seconds`;
ALTER TABLE `issue_time_logs` ADD COLUMN IF NOT EXISTS `user_rate_type` ENUM('hourly', 'minutely', 'secondly') DEFAULT 'hourly' AFTER `is_billable`;
ALTER TABLE `issue_time_logs` ADD COLUMN IF NOT EXISTS `user_rate_amount` DECIMAL(10, 4) DEFAULT 0.0000 AFTER `user_rate_type`;
ALTER TABLE `issue_time_logs` ADD COLUMN IF NOT EXISTS `total_cost` DECIMAL(12, 2) DEFAULT 0.00 AFTER `cost_calculated`;
ALTER TABLE `issue_time_logs` ADD COLUMN IF NOT EXISTS `resumed_at` DATETIME DEFAULT NULL AFTER `paused_at`;

-- Add work_date if it doesn't have a value (set to start_time date for existing records)
UPDATE `issue_time_logs` SET `work_date` = DATE(`start_time`) WHERE `work_date` IS NULL OR `work_date` = '0000-00-00';

-- Add missing foreign key for project_id
ALTER TABLE `issue_time_logs` ADD CONSTRAINT `issue_time_logs_project_id_fk` 
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) 
    ON DELETE SET NULL;

-- Add indexes for performance
ALTER TABLE `issue_time_logs` ADD INDEX `idx_time_logs_project_id` (`project_id`);
ALTER TABLE `issue_time_logs` ADD INDEX `idx_time_logs_user_status` (`user_id`, `status`);

SET FOREIGN_KEY_CHECKS = 1;
