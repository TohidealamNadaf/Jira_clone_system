-- Assign some issues to admin user for testing currentUser() search
USE `jiira_clonee_system`;

-- Update first 5 issues to be assigned to admin (user_id = 1)
UPDATE `issues` 
SET `assignee_id` = 1, `updated_at` = NOW() 
WHERE `id` IN (SELECT `id` FROM `issues` LIMIT 5);

-- Verify the changes
SELECT COUNT(*) as total_assigned_to_admin FROM `issues` WHERE `assignee_id` = 1;
