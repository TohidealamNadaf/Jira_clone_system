-- Add budget tracking columns to projects table
-- For time tracking and project budget management

ALTER TABLE `projects` 
ADD COLUMN IF NOT EXISTS `budget` DECIMAL(12,2) NULL DEFAULT NULL COMMENT 'Project budget' AFTER `is_archived`,
ADD COLUMN IF NOT EXISTS `budget_currency` VARCHAR(3) DEFAULT 'USD' COMMENT 'Budget currency code (USD, EUR, GBP, INR, etc.)' AFTER `budget`;

-- If the columns already exist, these IF NOT EXISTS clauses will silently skip
