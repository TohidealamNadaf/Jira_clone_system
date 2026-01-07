-- =====================================================
-- Add Budget Columns to Projects Table
-- Created: December 2025
-- Purpose: Enable project budget tracking
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 1;

USE `cways_mis`;

-- Check if columns exist before adding (prevents errors on re-run)
ALTER TABLE `projects` 
ADD COLUMN IF NOT EXISTS `budget` DECIMAL(12, 2) DEFAULT 0.00 COMMENT 'Project budget in default currency',
ADD COLUMN IF NOT EXISTS `budget_currency` VARCHAR(3) DEFAULT 'USD' COMMENT 'Budget currency code';

-- Add index for budget queries
CREATE INDEX IF NOT EXISTS idx_projects_budget ON `projects` (`budget`);

-- =====================================================
-- END MIGRATION
-- ===================================================== 
