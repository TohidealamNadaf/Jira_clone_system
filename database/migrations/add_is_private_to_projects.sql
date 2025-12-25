ALTER TABLE `projects` ADD COLUMN `is_private` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_archived`;
ADD INDEX `projects_is_private_idx` (`is_private`);
