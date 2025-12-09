-- Fix CASCADE DELETE constraints that are deleting projects
-- This prevents projects from being deleted when issues fail to create

SET FOREIGN_KEY_CHECKS = 0;

-- Fix: issues -> projects (most important - prevents project deletion)
ALTER TABLE `issues` DROP FOREIGN KEY `issues_project_id_fk`;
ALTER TABLE `issues` ADD CONSTRAINT `issues_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Fix: project_members -> projects
ALTER TABLE `project_members` DROP FOREIGN KEY `project_members_project_id_fk`;
ALTER TABLE `project_members` ADD CONSTRAINT `project_members_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Fix: boards -> projects
ALTER TABLE `boards` DROP FOREIGN KEY `boards_project_id_fk`;
ALTER TABLE `boards` ADD CONSTRAINT `boards_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Fix: components -> projects
ALTER TABLE `components` DROP FOREIGN KEY `components_project_id_fk`;
ALTER TABLE `components` ADD CONSTRAINT `components_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Fix: custom_field_contexts -> projects
ALTER TABLE `custom_field_contexts` DROP FOREIGN KEY `custom_field_contexts_project_id_fk`;
ALTER TABLE `custom_field_contexts` ADD CONSTRAINT `custom_field_contexts_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Fix: labels -> projects
ALTER TABLE `labels` DROP FOREIGN KEY `labels_project_id_fk`;
ALTER TABLE `labels` ADD CONSTRAINT `labels_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Fix: user_roles -> projects
ALTER TABLE `user_roles` DROP FOREIGN KEY `user_roles_project_id_fk`;
ALTER TABLE `user_roles` ADD CONSTRAINT `user_roles_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Fix: versions -> projects
ALTER TABLE `versions` DROP FOREIGN KEY `versions_project_id_fk`;
ALTER TABLE `versions` ADD CONSTRAINT `versions_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Keep CASCADE DELETE for issue-related tables (when issue is deleted, children should be deleted)
-- These are safe and desired

SET FOREIGN_KEY_CHECKS = 1;

-- Confirmation
SELECT 'All foreign key constraints updated successfully!' AS status;
