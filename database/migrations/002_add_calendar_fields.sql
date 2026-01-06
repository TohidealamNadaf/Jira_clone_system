-- =====================================================
-- MIGRATION 002: Add Calendar Fields for Calendar & Roadmap Views
-- Date: December 15, 2025
-- Purpose: Add start_date and end_date fields to support calendar and roadmap features
-- =====================================================

-- Add date fields for calendar and roadmap functionality
ALTER TABLE issues 
ADD COLUMN start_date DATE DEFAULT NULL AFTER due_date,
ADD COLUMN end_date DATE DEFAULT NULL AFTER start_date;

-- Add indexes for performance on these frequently-queried columns
ALTER TABLE issues 
ADD INDEX idx_issues_start_date (start_date),
ADD INDEX idx_issues_end_date (end_date);

-- Update existing issues with calculated dates based on due_date
-- Set start_date to 7 days before due_date
UPDATE issues 
SET start_date = DATE_SUB(due_date, INTERVAL 7 DAY) 
WHERE due_date IS NOT NULL AND start_date IS NULL;

-- Set end_date to due_date for consistency
UPDATE issues 
SET end_date = due_date 
WHERE due_date IS NOT NULL AND end_date IS NULL;

-- Verify migration completed
SELECT COUNT(*) as total_issues,
       COUNT(CASE WHEN start_date IS NOT NULL THEN 1 END) as issues_with_start_date,
       COUNT(CASE WHEN end_date IS NOT NULL THEN 1 END) as issues_with_end_date,
       COUNT(CASE WHEN due_date IS NOT NULL THEN 1 END) as issues_with_due_date
FROM issues;
