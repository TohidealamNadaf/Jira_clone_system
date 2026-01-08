-- Ensure issue_history table exists
CREATE TABLE IF NOT EXISTS `issue_history` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `field` VARCHAR(50) NOT NULL,
    `old_value` TEXT DEFAULT NULL,
    `new_value` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `issue_history_issue_id_idx` (`issue_id`),
    KEY `issue_history_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure audit_logs table exists
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT UNSIGNED DEFAULT NULL,
    `old_values` JSON DEFAULT NULL,
    `new_values` JSON DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(1000) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `audit_logs_user_id_idx` (`user_id`),
    KEY `audit_logs_entity_idx` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alter columns to be safe (increase size / change type)
ALTER TABLE `audit_logs` MODIFY `user_agent` TEXT DEFAULT NULL;
ALTER TABLE `issue_history` MODIFY `old_value` LONGTEXT DEFAULT NULL;
ALTER TABLE `issue_history` MODIFY `new_value` LONGTEXT DEFAULT NULL;

SELECT "Tables issue_history and audit_logs verified/updated" as status;
