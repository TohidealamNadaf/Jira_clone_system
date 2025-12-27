-- =====================================================
-- JIRA CLONE DATABASE SCHEMA
-- MySQL 8.0+ Required
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `jiira_clonee_system`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `jiira_clonee_system`;

-- =====================================================
-- USERS & AUTHENTICATION
-- =====================================================

CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `display_name` VARCHAR(200) GENERATED ALWAYS AS (CONCAT(first_name, ' ', last_name)) STORED,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `timezone` VARCHAR(50) DEFAULT 'UTC',
    `locale` VARCHAR(10) DEFAULT 'en',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `last_login_at` TIMESTAMP NULL DEFAULT NULL,
    `last_login_ip` VARCHAR(45) DEFAULT NULL,
    `failed_login_attempts` INT UNSIGNED DEFAULT 0,
    `locked_until` TIMESTAMP NULL DEFAULT NULL,
    `unread_notifications_count` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `users_is_active_idx` (`is_active`),
    KEY `users_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `token` VARCHAR(100) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `used_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `password_resets_user_id_idx` (`user_id`),
    KEY `password_resets_token_idx` (`token`),
    CONSTRAINT `password_resets_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_sessions` (
    `id` VARCHAR(128) NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `last_activity` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_sessions_user_id_idx` (`user_id`),
    CONSTRAINT `user_sessions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token_hash` VARCHAR(64) NOT NULL,
    `abilities` JSON DEFAULT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_hash_unique` (`token_hash`),
    KEY `personal_access_tokens_user_id_idx` (`user_id`),
    CONSTRAINT `personal_access_tokens_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RBAC (Roles, Permissions, Groups)
-- =====================================================

CREATE TABLE `roles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `is_system` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `category` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permissions_slug_unique` (`slug`),
    KEY `permissions_category_idx` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    KEY `role_permissions_permission_id_idx` (`permission_id`),
    CONSTRAINT `role_permissions_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `role_permissions_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_roles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_roles_unique` (`user_id`, `role_id`, `project_id`),
    KEY `user_roles_role_id_idx` (`role_id`),
    KEY `user_roles_project_id_idx` (`project_id`),
    CONSTRAINT `user_roles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `user_roles_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `groups` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `groups_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `group_members` (
    `group_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`group_id`, `user_id`),
    KEY `group_members_user_id_idx` (`user_id`),
    CONSTRAINT `group_members_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
    CONSTRAINT `group_members_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PROJECTS
-- =====================================================

CREATE TABLE `project_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `projects` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(10) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `lead_id` INT UNSIGNED DEFAULT NULL,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `default_assignee` ENUM('project_lead', 'unassigned') DEFAULT 'unassigned',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    `issue_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `budget` DECIMAL(12, 2) DEFAULT 0.00 COMMENT 'Project budget in default currency',
    `budget_currency` VARCHAR(3) DEFAULT 'USD' COMMENT 'Budget currency code',
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `projects_key_unique` (`key`),
    KEY `projects_lead_id_idx` (`lead_id`),
    KEY `projects_category_id_idx` (`category_id`),
    KEY `projects_is_archived_idx` (`is_archived`),
    CONSTRAINT `projects_lead_id_fk` FOREIGN KEY (`lead_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `projects_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `project_categories` (`id`) ON DELETE SET NULL,
    CONSTRAINT `projects_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key for user_roles.project_id
ALTER TABLE `user_roles` ADD CONSTRAINT `user_roles_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

CREATE TABLE `project_members` (
    `project_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`project_id`, `user_id`),
    KEY `project_members_user_id_idx` (`user_id`),
    KEY `project_members_role_id_idx` (`role_id`),
    CONSTRAINT `project_members_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `project_members_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `project_members_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `components` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `lead_id` INT UNSIGNED DEFAULT NULL,
    `default_assignee_id` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `components_project_name_unique` (`project_id`, `name`),
    KEY `components_lead_id_idx` (`lead_id`),
    CONSTRAINT `components_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `components_lead_id_fk` FOREIGN KEY (`lead_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `components_default_assignee_id_fk` FOREIGN KEY (`default_assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `versions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `start_date` DATE DEFAULT NULL,
    `release_date` DATE DEFAULT NULL,
    `released_at` TIMESTAMP NULL DEFAULT NULL,
    `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `versions_project_name_unique` (`project_id`, `name`),
    KEY `versions_released_at_idx` (`released_at`),
    CONSTRAINT `versions_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ISSUE TYPES & WORKFLOWS
-- =====================================================

CREATE TABLE `issue_types` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `icon` VARCHAR(50) DEFAULT 'task',
    `color` VARCHAR(7) DEFAULT '#4A90D9',
    `is_subtask` TINYINT(1) NOT NULL DEFAULT 0,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `issue_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `statuses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `category` ENUM('todo', 'in_progress', 'done') NOT NULL DEFAULT 'todo',
    `color` VARCHAR(7) DEFAULT '#42526E',
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `statuses_name_unique` (`name`),
    KEY `statuses_category_idx` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_priorities` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `icon` VARCHAR(50) DEFAULT 'medium',
    `color` VARCHAR(7) DEFAULT '#FFAB00',
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `issue_priorities_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `workflows` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `workflows_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `workflow_statuses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `workflow_id` INT UNSIGNED NOT NULL,
    `status_id` INT UNSIGNED NOT NULL,
    `is_initial` TINYINT(1) NOT NULL DEFAULT 0,
    `x_position` INT DEFAULT 0,
    `y_position` INT DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `workflow_statuses_unique` (`workflow_id`, `status_id`),
    KEY `workflow_statuses_status_id_idx` (`status_id`),
    CONSTRAINT `workflow_statuses_workflow_id_fk` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE,
    CONSTRAINT `workflow_statuses_status_id_fk` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `workflow_transitions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `workflow_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `from_status_id` INT UNSIGNED DEFAULT NULL,
    `to_status_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `workflow_transitions_workflow_id_idx` (`workflow_id`),
    KEY `workflow_transitions_from_status_id_idx` (`from_status_id`),
    KEY `workflow_transitions_to_status_id_idx` (`to_status_id`),
    CONSTRAINT `workflow_transitions_workflow_id_fk` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE,
    CONSTRAINT `workflow_transitions_from_status_id_fk` FOREIGN KEY (`from_status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE,
    CONSTRAINT `workflow_transitions_to_status_id_fk` FOREIGN KEY (`to_status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BOARDS & SPRINTS
-- =====================================================

CREATE TABLE `boards` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('scrum', 'kanban') NOT NULL DEFAULT 'scrum',
    `filter_jql` TEXT DEFAULT NULL,
    `is_private` TINYINT(1) NOT NULL DEFAULT 0,
    `owner_id` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `boards_project_id_idx` (`project_id`),
    KEY `boards_owner_id_idx` (`owner_id`),
    CONSTRAINT `boards_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `boards_owner_id_fk` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `board_columns` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `board_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `status_ids` JSON DEFAULT NULL,
    `min_issues` INT UNSIGNED DEFAULT NULL,
    `max_issues` INT UNSIGNED DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `board_columns_board_id_idx` (`board_id`),
    CONSTRAINT `board_columns_board_id_fk` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sprints` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `board_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `goal` TEXT DEFAULT NULL,
    `start_date` DATE DEFAULT NULL,
    `end_date` DATE DEFAULT NULL,
    `started_at` TIMESTAMP NULL DEFAULT NULL,
    `completed_at` TIMESTAMP NULL DEFAULT NULL,
    `status` ENUM('future', 'active', 'completed') NOT NULL DEFAULT 'future',
    `velocity` DECIMAL(10,2) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `sprints_board_id_idx` (`board_id`),
    KEY `sprints_status_idx` (`status`),
    CONSTRAINT `sprints_board_id_fk` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ISSUES
-- =====================================================

CREATE TABLE `issues` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    `issue_type_id` INT UNSIGNED NOT NULL,
    `status_id` INT UNSIGNED NOT NULL,
    `priority_id` INT UNSIGNED NOT NULL,
    `issue_key` VARCHAR(20) NOT NULL,
    `issue_number` INT UNSIGNED NOT NULL,
    `summary` VARCHAR(500) NOT NULL,
    `description` LONGTEXT DEFAULT NULL,
    `reporter_id` INT UNSIGNED NOT NULL,
    `assignee_id` INT UNSIGNED DEFAULT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `epic_id` INT UNSIGNED DEFAULT NULL,
    `sprint_id` INT UNSIGNED DEFAULT NULL,
    `story_points` DECIMAL(5,2) DEFAULT NULL,
    `original_estimate` INT UNSIGNED DEFAULT NULL,
    `remaining_estimate` INT UNSIGNED DEFAULT NULL,
    `time_spent` INT UNSIGNED DEFAULT 0,
    `environment` TEXT DEFAULT NULL,
    `due_date` DATE DEFAULT NULL,
    `resolved_at` TIMESTAMP NULL DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `issues_key_unique` (`issue_key`),
    KEY `issues_project_id_idx` (`project_id`),
    KEY `issues_issue_type_id_idx` (`issue_type_id`),
    KEY `issues_status_id_idx` (`status_id`),
    KEY `issues_priority_id_idx` (`priority_id`),
    KEY `issues_reporter_id_idx` (`reporter_id`),
    KEY `issues_assignee_id_idx` (`assignee_id`),
    KEY `issues_parent_id_idx` (`parent_id`),
    KEY `issues_epic_id_idx` (`epic_id`),
    KEY `issues_sprint_id_idx` (`sprint_id`),
    KEY `issues_created_at_idx` (`created_at`),
    KEY `issues_due_date_idx` (`due_date`),
    FULLTEXT KEY `issues_fulltext` (`summary`, `description`),
    CONSTRAINT `issues_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issues_issue_type_id_fk` FOREIGN KEY (`issue_type_id`) REFERENCES `issue_types` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `issues_status_id_fk` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `issues_priority_id_fk` FOREIGN KEY (`priority_id`) REFERENCES `issue_priorities` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `issues_reporter_id_fk` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `issues_assignee_id_fk` FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `issues_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issues_epic_id_fk` FOREIGN KEY (`epic_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL,
    CONSTRAINT `issues_sprint_id_fk` FOREIGN KEY (`sprint_id`) REFERENCES `sprints` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `labels` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(50) NOT NULL,
    `color` VARCHAR(7) DEFAULT '#5243AA',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `labels_project_name_unique` (`project_id`, `name`),
    CONSTRAINT `labels_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_labels` (
    `issue_id` INT UNSIGNED NOT NULL,
    `label_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`issue_id`, `label_id`),
    KEY `issue_labels_label_id_idx` (`label_id`),
    CONSTRAINT `issue_labels_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_labels_label_id_fk` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_components` (
    `issue_id` INT UNSIGNED NOT NULL,
    `component_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`issue_id`, `component_id`),
    KEY `issue_components_component_id_idx` (`component_id`),
    CONSTRAINT `issue_components_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_components_component_id_fk` FOREIGN KEY (`component_id`) REFERENCES `components` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_versions` (
    `issue_id` INT UNSIGNED NOT NULL,
    `version_id` INT UNSIGNED NOT NULL,
    `type` ENUM('affects', 'fix') NOT NULL DEFAULT 'fix',
    PRIMARY KEY (`issue_id`, `version_id`, `type`),
    KEY `issue_versions_version_id_idx` (`version_id`),
    CONSTRAINT `issue_versions_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_versions_version_id_fk` FOREIGN KEY (`version_id`) REFERENCES `versions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_link_types` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `inward_description` VARCHAR(100) NOT NULL,
    `outward_description` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `issue_link_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_links` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `source_issue_id` INT UNSIGNED NOT NULL,
    `target_issue_id` INT UNSIGNED NOT NULL,
    `link_type_id` INT UNSIGNED NOT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `issue_links_unique` (`source_issue_id`, `target_issue_id`, `link_type_id`),
    KEY `issue_links_target_issue_id_idx` (`target_issue_id`),
    KEY `issue_links_link_type_id_idx` (`link_type_id`),
    CONSTRAINT `issue_links_source_issue_id_fk` FOREIGN KEY (`source_issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_links_target_issue_id_fk` FOREIGN KEY (`target_issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_links_link_type_id_fk` FOREIGN KEY (`link_type_id`) REFERENCES `issue_link_types` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_links_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_watchers` (
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`issue_id`, `user_id`),
    KEY `issue_watchers_user_id_idx` (`user_id`),
    CONSTRAINT `issue_watchers_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_watchers_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_votes` (
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`issue_id`, `user_id`),
    KEY `issue_votes_user_id_idx` (`user_id`),
    CONSTRAINT `issue_votes_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_votes_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_history` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `field` VARCHAR(50) NOT NULL,
    `old_value` TEXT DEFAULT NULL,
    `new_value` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `issue_history_issue_id_idx` (`issue_id`),
    KEY `issue_history_created_at_idx` (`created_at`),
    CONSTRAINT `issue_history_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_history_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sprint_issues` (
    `sprint_id` INT UNSIGNED NOT NULL,
    `issue_id` INT UNSIGNED NOT NULL,
    `added_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `removed_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`sprint_id`, `issue_id`),
    KEY `sprint_issues_issue_id_idx` (`issue_id`),
    CONSTRAINT `sprint_issues_sprint_id_fk` FOREIGN KEY (`sprint_id`) REFERENCES `sprints` (`id`) ON DELETE CASCADE,
    CONSTRAINT `sprint_issues_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- COMMENTS & ATTACHMENTS
-- =====================================================

CREATE TABLE `comments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `body` TEXT NOT NULL,
    `is_internal` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `comments_issue_id_idx` (`issue_id`),
    KEY `comments_user_id_idx` (`user_id`),
    KEY `comments_parent_id_idx` (`parent_id`),
    FULLTEXT KEY `comments_body_fulltext` (`body`),
    CONSTRAINT `comments_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `comments_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `comments_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comment_history` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `comment_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `old_body` TEXT NOT NULL,
    `new_body` TEXT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `comment_history_comment_id_idx` (`comment_id`),
    CONSTRAINT `comment_history_comment_id_fk` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `comment_history_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mentions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `mentionable_type` VARCHAR(50) NOT NULL,
    `mentionable_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `mentions_mentionable_idx` (`mentionable_type`, `mentionable_id`),
    KEY `mentions_user_id_idx` (`user_id`),
    CONSTRAINT `mentions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `attachments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `size` INT UNSIGNED NOT NULL,
    `path` VARCHAR(500) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `attachments_issue_id_idx` (`issue_id`),
    KEY `attachments_user_id_idx` (`user_id`),
    CONSTRAINT `attachments_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `attachments_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TIME TRACKING
-- =====================================================

CREATE TABLE `worklogs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `time_spent` INT UNSIGNED NOT NULL,
    `started_at` TIMESTAMP NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `worklogs_issue_id_idx` (`issue_id`),
    KEY `worklogs_user_id_idx` (`user_id`),
    KEY `worklogs_started_at_idx` (`started_at`),
    CONSTRAINT `worklogs_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `worklogs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- NOTIFICATIONS
-- =====================================================

CREATE TABLE `notifications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('issue_created', 'issue_assigned', 'issue_commented', 'issue_status_changed', 'issue_mentioned', 'issue_watched', 'project_created', 'project_member_added', 'comment_reply', 'custom') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT DEFAULT NULL,
    `action_url` VARCHAR(500) DEFAULT NULL,
    `actor_user_id` INT UNSIGNED DEFAULT NULL,
    `related_issue_id` INT UNSIGNED DEFAULT NULL,
    `related_project_id` INT UNSIGNED DEFAULT NULL,
    `priority` ENUM('high', 'normal', 'low') DEFAULT 'normal',
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `notifications_user_unread_idx` (`user_id`, `is_read`, `created_at`),
    KEY `notifications_actor_user_id_idx` (`actor_user_id`),
    KEY `notifications_issue_id_idx` (`related_issue_id`),
    KEY `notifications_created_at_idx` (`created_at`),
    KEY `notifications_type_idx` (`type`, `created_at`),
    CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `notifications_actor_user_id_fk` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `notifications_issue_id_fk` FOREIGN KEY (`related_issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL,
    CONSTRAINT `notifications_project_id_fk` FOREIGN KEY (`related_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notification_preferences` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `event_type` ENUM('issue_created', 'issue_assigned', 'issue_commented', 'issue_status_changed', 'issue_mentioned', 'issue_watched', 'project_created', 'project_member_added', 'comment_reply', 'all') NOT NULL,
    `in_app` TINYINT(1) DEFAULT 1,
    `email` TINYINT(1) DEFAULT 1,
    `push` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `notification_preferences_user_event_unique` (`user_id`, `event_type`),
    CONSTRAINT `notification_preferences_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notification_deliveries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `notification_id` INT UNSIGNED NOT NULL,
    `channel` ENUM('in_app', 'email', 'push') NOT NULL,
    `status` ENUM('pending', 'sent', 'failed', 'bounced') DEFAULT 'pending',
    `sent_at` TIMESTAMP NULL DEFAULT NULL,
    `error_message` TEXT,
    `retry_count` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `notification_deliveries_status_idx` (`status`, `created_at`),
    KEY `notification_deliveries_notification_id_idx` (`notification_id`),
    CONSTRAINT `notification_deliveries_notification_id_fk` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications_archive` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('issue_created', 'issue_assigned', 'issue_commented', 'issue_status_changed', 'issue_mentioned', 'issue_watched', 'project_created', 'project_member_added', 'comment_reply', 'custom') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT DEFAULT NULL,
    `action_url` VARCHAR(500) DEFAULT NULL,
    `actor_user_id` INT UNSIGNED DEFAULT NULL,
    `related_issue_id` INT UNSIGNED DEFAULT NULL,
    `related_project_id` INT UNSIGNED DEFAULT NULL,
    `priority` ENUM('high', 'normal', 'low') DEFAULT 'normal',
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `notifications_archive_user_unread_idx` (`user_id`, `is_read`, `created_at`),
    KEY `notifications_archive_actor_user_id_idx` (`actor_user_id`),
    KEY `notifications_archive_issue_id_idx` (`related_issue_id`),
    KEY `notifications_archive_created_at_idx` (`created_at`),
    KEY `notifications_archive_type_idx` (`type`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `email_queue` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `to_email` VARCHAR(255) NOT NULL,
    `to_name` VARCHAR(255) DEFAULT NULL,
    `subject` VARCHAR(500) NOT NULL,
    `body` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `sent_at` TIMESTAMP NULL DEFAULT NULL,
    `failed_at` TIMESTAMP NULL DEFAULT NULL,
    `error` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `email_queue_sent_at_idx` (`sent_at`),
    KEY `email_queue_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- FILTERS & DASHBOARDS
-- =====================================================

CREATE TABLE `saved_filters` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `jql` TEXT NOT NULL,
    `is_favorite` TINYINT(1) NOT NULL DEFAULT 0,
    `share_type` ENUM('private', 'project', 'global') NOT NULL DEFAULT 'private',
    `shared_with` JSON DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `saved_filters_user_id_idx` (`user_id`),
    CONSTRAINT `saved_filters_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `dashboards` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `share_type` ENUM('private', 'project', 'global') NOT NULL DEFAULT 'private',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `dashboards_user_id_idx` (`user_id`),
    CONSTRAINT `dashboards_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `dashboard_gadgets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `dashboard_id` INT UNSIGNED NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `config` JSON DEFAULT NULL,
    `position_x` INT NOT NULL DEFAULT 0,
    `position_y` INT NOT NULL DEFAULT 0,
    `width` INT NOT NULL DEFAULT 1,
    `height` INT NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `dashboard_gadgets_dashboard_id_idx` (`dashboard_id`),
    CONSTRAINT `dashboard_gadgets_dashboard_id_fk` FOREIGN KEY (`dashboard_id`) REFERENCES `dashboards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CUSTOM FIELDS
-- =====================================================

CREATE TABLE `custom_fields` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `type` ENUM('text', 'textarea', 'number', 'date', 'datetime', 'select', 'multiselect', 'checkbox', 'radio', 'url', 'user') NOT NULL,
    `config` JSON DEFAULT NULL,
    `is_required` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `custom_fields_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `custom_field_contexts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `field_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED DEFAULT NULL,
    `issue_type_id` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `custom_field_contexts_field_id_idx` (`field_id`),
    KEY `custom_field_contexts_project_id_idx` (`project_id`),
    KEY `custom_field_contexts_issue_type_id_idx` (`issue_type_id`),
    CONSTRAINT `custom_field_contexts_field_id_fk` FOREIGN KEY (`field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE,
    CONSTRAINT `custom_field_contexts_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `custom_field_contexts_issue_type_id_fk` FOREIGN KEY (`issue_type_id`) REFERENCES `issue_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `custom_field_values` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `field_id` INT UNSIGNED NOT NULL,
    `issue_id` INT UNSIGNED NOT NULL,
    `value` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `custom_field_values_unique` (`field_id`, `issue_id`),
    KEY `custom_field_values_issue_id_idx` (`issue_id`),
    CONSTRAINT `custom_field_values_field_id_fk` FOREIGN KEY (`field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE,
    CONSTRAINT `custom_field_values_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AUDIT LOG (Immutable)
-- =====================================================

CREATE TABLE `audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT UNSIGNED DEFAULT NULL,
    `old_values` JSON DEFAULT NULL,
    `new_values` JSON DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `audit_logs_user_id_idx` (`user_id`),
    KEY `audit_logs_entity_idx` (`entity_type`, `entity_id`),
    KEY `audit_logs_created_at_idx` (`created_at`),
    KEY `audit_logs_action_idx` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Trigger to prevent updates/deletes on audit_logs
-- Note: Triggers require application-level enforcement
-- These can be added separately if needed
-- CREATE TRIGGER audit_logs_no_update BEFORE UPDATE ON audit_logs
-- FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Audit logs cannot be modified'; END;
-- CREATE TRIGGER audit_logs_no_delete BEFORE DELETE ON audit_logs
-- FOR EACH ROW BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Audit logs cannot be deleted'; END;

-- =====================================================
-- SETTINGS
-- =====================================================

CREATE TABLE `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
