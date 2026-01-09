-- =====================================================
-- JIRA CLONE COMPREHENSIVE DATABASE SCHEMA
-- Generated: January 2026
-- MySQL 8.0+ Required
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `cways_prod`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `cways_prod`;

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
    `created_at?` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
    `abilities` LONGTEXT DEFAULT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `expires_at?` TIMESTAMP NULL DEFAULT NULL,
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
    `description?` TEXT DEFAULT NULL,
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
-- PROJECTS & MEMBERSHIP
-- =====================================================

CREATE TABLE `project_workflows` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` int UNSIGNED NOT NULL,
  `workflow_id` int UNSIGNED NOT NULL,
  `issue_type_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `project_workflows_backup_placeholder` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    `is_private` TINYINT(1) NOT NULL DEFAULT 0,
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
    `status_ids` LONGTEXT DEFAULT NULL,
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
    `start_date` DATE DEFAULT NULL,
    `end_date` DATE DEFAULT NULL,
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
    KEY `idx_issues_start_date` (`start_date`),
    KEY `idx_issues_end_date` (`end_date`),
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

-- =====================================================
-- ISSUE RELATIONSHIPS & METADATA
-- =====================================================

CREATE TABLE `labels` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(50) NOT NULL,
    `color` VARCHAR(7) DEFAULT '#42526E',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `labels_project_name_unique` (`project_id`, `name`),
    CONSTRAINT `labels_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_labels` (
    `issue_id` INT UNSIGNED NOT NULL,
    `label_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`issue_id`, `label_id`),
    CONSTRAINT `issue_labels_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_labels_label_id_fk` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_components` (
    `issue_id` INT UNSIGNED NOT NULL,
    `component_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`issue_id`, `component_id`),
    CONSTRAINT `issue_components_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_components_component_id_fk` FOREIGN KEY (`component_id`) REFERENCES `components` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_versions` (
    `issue_id` INT UNSIGNED NOT NULL,
    `version_id` INT UNSIGNED NOT NULL,
    `type` ENUM('affect', 'fix') NOT NULL DEFAULT 'fix',
    PRIMARY KEY (`issue_id`, `version_id`, `type`),
    CONSTRAINT `issue_versions_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_versions_version_id_fk` FOREIGN KEY (`version_id`) REFERENCES `versions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_link_types` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `outward` VARCHAR(50) NOT NULL,
    `inward` VARCHAR(50) NOT NULL,
    `outward_description` VARCHAR(255) DEFAULT NULL,
    `inward_description` VARCHAR(255) DEFAULT NULL,
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
    KEY `issue_links_target_idx` (`target_issue_id`),
    KEY `issue_links_created_by_idx` (`created_by`),
    CONSTRAINT `issue_links_source_fk` FOREIGN KEY (`source_issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_links_target_fk` FOREIGN KEY (`target_issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_links_type_fk` FOREIGN KEY (`link_type_id`) REFERENCES `issue_link_types` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_links_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_watchers` (
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`issue_id`, `user_id`),
    CONSTRAINT `issue_watchers_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_watchers_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_votes` (
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`issue_id`, `user_id`),
    CONSTRAINT `issue_votes_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_votes_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_history` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `field` VARCHAR(50) NOT NULL,
    `old_value` TEXT DEFAULT NULL,
    `new_value` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `issue_history_issue_id_idx` (`issue_id`),
    KEY `issue_history_created_at_idx` (`created_at`),
    CONSTRAINT `issue_history_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_history_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sprint_issues` (
    `sprint_id` INT UNSIGNED NOT NULL,
    `issue_id` INT UNSIGNED NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
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
    `body` LONGTEXT NOT NULL,
    `is_internal` TINYINT(1) DEFAULT 0,
    `edit_count` INT DEFAULT 0,
    `is_deleted` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `comments_issue_id_idx` (`issue_id`),
    KEY `comments_user_id_idx` (`user_id`),
    KEY `comments_created_at_idx` (`created_at`),
    CONSTRAINT `comments_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `comments_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `comments_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comment_history` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `comment_id` INT UNSIGNED NOT NULL,
    `edited_by` INT UNSIGNED NOT NULL,
    `old_body` LONGTEXT,
    `new_body` LONGTEXT,
    `edited_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `change_reason` VARCHAR(255),
    FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_comment_id` (`comment_id`),
    INDEX `idx_edited_at` (`edited_at`),
    INDEX `idx_edited_by` (`edited_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mentions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `comment_id` INT UNSIGNED DEFAULT NULL,
    `issue_id` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `mentions_user_id_idx` (`user_id`),
    KEY `mentions_comment_id_idx` (`comment_id`),
    KEY `mentions_issue_id_idx` (`issue_id`),
    CONSTRAINT `mentions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `mentions_comment_id_fk` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `mentions_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE
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
    CONSTRAINT `attachments_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `attachments_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `issue_attachments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `filename` VARCHAR(255) NOT NULL COMMENT 'Random secure filename',
    `original_name` VARCHAR(255) NOT NULL COMMENT 'Original filename uploaded by user',
    `mime_type` VARCHAR(100) NOT NULL DEFAULT 'application/octet-stream',
    `file_size` INT UNSIGNED NOT NULL DEFAULT 0,
    `file_path` VARCHAR(500) NOT NULL COMMENT 'Relative path in storage directory',
    `uploaded_by` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `issue_attachments_issue_id_idx` (`issue_id`),
    KEY `issue_attachments_uploaded_by_idx` (`uploaded_by`),
    KEY `issue_attachments_created_at_idx` (`created_at`),
    CONSTRAINT `issue_attachments_issue_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_attachments_user_fk` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- NOTIFICATIONS
-- =====================================================

CREATE TABLE `notifications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('issue_created', 'issue_assigned', 'issue_commented', 
                'issue_status_changed', 'issue_mentioned', 'issue_watched',
                'project_created', 'project_member_added', 'comment_reply',
                'custom') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT,
    `action_url` VARCHAR(500),
    `actor_user_id` INT UNSIGNED,
    `related_issue_id` INT UNSIGNED,
    `related_project_id` INT UNSIGNED,
    `priority` ENUM('high', 'normal', 'low') DEFAULT 'normal',
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `dispatch_id` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_notifications_dispatch_id` (`dispatch_id`),
    KEY `notifications_user_unread_idx` (`user_id`, `is_read`, `created_at`),
    KEY `notifications_actor_user_id_idx` (`actor_user_id`),
    KEY `notifications_issue_id_idx` (`related_issue_id`),
    KEY `notifications_dispatch_id_idx` (`dispatch_id`),
    CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `notifications_actor_user_id_fk` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `notifications_issue_id_fk` FOREIGN KEY (`related_issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL,
    CONSTRAINT `notifications_project_id_fk` FOREIGN KEY (`related_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notification_preferences` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `event_type` ENUM('issue_created', 'issue_assigned', 'issue_commented',
                      'issue_status_changed', 'issue_mentioned', 'issue_watched',
                      'project_created', 'project_member_added', 'comment_reply',
                      'all') NOT NULL,
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

CREATE TABLE `push_device_tokens` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `platform` ENUM('ios', 'android', 'web') NOT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `push_device_tokens_user_id_idx` (`user_id`),
    CONSTRAINT `push_device_tokens_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `notifications_archive` LIKE `notifications`;

CREATE TABLE `notification_dispatch_log` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `dispatch_id` VARCHAR(255) UNIQUE NOT NULL,
    `dispatch_type` ENUM('comment_added', 'status_changed', 'other') NOT NULL,
    `issue_id` INT UNSIGNED NOT NULL,
    `comment_id` INT UNSIGNED NULL,
    `actor_user_id` INT UNSIGNED NOT NULL,
    `recipients_count` INT UNSIGNED DEFAULT 0,
    `status` ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `completed_at` TIMESTAMP NULL,
    `error_message` TEXT NULL,
    INDEX `idx_dispatch_id` (`dispatch_id`),
    INDEX `idx_issue_id` (`issue_id`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ROADMAP
-- =====================================================

CREATE TABLE `roadmap_items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `type` ENUM('epic', 'feature', 'milestone') NOT NULL DEFAULT 'feature',
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` ENUM('planned', 'in_progress', 'on_track', 'at_risk', 'delayed', 'completed') NOT NULL DEFAULT 'planned',
    `priority` ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
    `owner_id` INT UNSIGNED DEFAULT NULL,
    `estimated_hours` DECIMAL(10, 2) DEFAULT 0.00,
    `actual_hours` DECIMAL(10, 2) DEFAULT 0.00,
    `progress_percentage` INT UNSIGNED DEFAULT 0,
    `color` VARCHAR(7) DEFAULT '#8b1956',
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `roadmap_items_project_id_idx` (`project_id`),
    KEY `roadmap_items_owner_id_idx` (`owner_id`),
    KEY `roadmap_items_status_idx` (`status`),
    KEY `roadmap_items_start_date_idx` (`start_date`),
    KEY `roadmap_items_end_date_idx` (`end_date`),
    KEY `roadmap_items_created_by_idx` (`created_by`),
    CONSTRAINT `roadmap_items_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `roadmap_items_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roadmap_item_sprints` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `roadmap_item_id` INT UNSIGNED NOT NULL,
    `sprint_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roadmap_item_sprints_unique` (`roadmap_item_id`, `sprint_id`),
    KEY `roadmap_item_sprints_sprint_id_idx` (`sprint_id`),
    CONSTRAINT `roadmap_item_sprints_item_fk` FOREIGN KEY (`roadmap_item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE,
    CONSTRAINT `roadmap_item_sprints_sprint_fk` FOREIGN KEY (`sprint_id`) REFERENCES `sprints` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roadmap_dependencies` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` INT UNSIGNED NOT NULL,
    `depends_on_item_id` INT UNSIGNED NOT NULL,
    `dependency_type` ENUM('blocks', 'depends_on', 'relates_to') NOT NULL DEFAULT 'depends_on',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roadmap_dependencies_unique` (`item_id`, `depends_on_item_id`),
    KEY `roadmap_dependencies_depends_on_idx` (`depends_on_item_id`),
    CONSTRAINT `roadmap_dependencies_item_fk` FOREIGN KEY (`item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE,
    CONSTRAINT `roadmap_dependencies_depends_on_fk` FOREIGN KEY (`depends_on_item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roadmap_item_issues` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `roadmap_item_id` INT UNSIGNED NOT NULL,
    `issue_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roadmap_item_issues_unique` (`roadmap_item_id`, `issue_id`),
    KEY `roadmap_item_issues_issue_idx` (`issue_id`),
    CONSTRAINT `roadmap_item_issues_item_fk` FOREIGN KEY (`roadmap_item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE,
    CONSTRAINT `roadmap_item_issues_issue_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- USER SETTINGS & RATES
-- =====================================================

CREATE TABLE `user_settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL UNIQUE,
    `language` VARCHAR(10) DEFAULT 'en',
    `timezone` VARCHAR(50) DEFAULT 'UTC',
    `date_format` VARCHAR(20) DEFAULT 'MM/DD/YYYY',
    `items_per_page` INT UNSIGNED DEFAULT 25,
    `compact_view` TINYINT(1) DEFAULT 0,
    `auto_refresh` TINYINT(1) DEFAULT 0,
    `show_profile` TINYINT(1) DEFAULT 1,
    `show_activity` TINYINT(1) DEFAULT 1,
    `show_email` TINYINT(1) DEFAULT 0,
    `high_contrast` TINYINT(1) DEFAULT 0,
    `reduce_motion` TINYINT(1) DEFAULT 0,
    `large_text` TINYINT(1) DEFAULT 0,
    `annual_package` DECIMAL(15, 2) DEFAULT NULL,
    `rate_currency` VARCHAR(3) DEFAULT 'USD',
    `hourly_rate` DECIMAL(10, 4) GENERATED ALWAYS AS (
        IF(annual_package IS NULL, NULL, annual_package / 2210)
    ) STORED,
    `minute_rate` DECIMAL(12, 6) GENERATED ALWAYS AS (
        IF(annual_package IS NULL, NULL, (annual_package / 2210) / 60)
    ) STORED,
    `second_rate` DECIMAL(14, 8) GENERATED ALWAYS AS (
        IF(annual_package IS NULL, NULL, ((annual_package / 2210) / 60) / 60)
    ) STORED,
    `daily_rate` DECIMAL(10, 2) GENERATED ALWAYS AS (
        IF(annual_package IS NULL, NULL, (annual_package / 2210) * 8.5)
    ) STORED,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_settings_user_id_idx` (`user_id`),
    KEY `user_settings_currency_idx` (`rate_currency`),
    KEY `user_settings_created_at_idx` (`created_at`),
    CONSTRAINT `user_settings_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_rates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `rate_type` ENUM('hourly', 'minutely', 'secondly') NOT NULL DEFAULT 'hourly',
    `rate_amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(3) DEFAULT 'USD',
    `is_active` TINYINT(1) DEFAULT 1,
    `effective_from` DATE NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_rates_user_id_idx` (`user_id`),
    CONSTRAINT `user_rates_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TIME TRACKING
-- =====================================================

CREATE TABLE `issue_time_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED DEFAULT NULL,
    `status` ENUM('running', 'paused', 'stopped', 'manual') NOT NULL DEFAULT 'manual',
    `work_date` DATE NOT NULL DEFAULT (CURRENT_DATE),
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME DEFAULT NULL,
    `resumed_at` DATETIME DEFAULT NULL,
    `paused_at` DATETIME DEFAULT NULL,
    `duration_seconds` INT UNSIGNED DEFAULT 0,
    `paused_seconds` INT UNSIGNED DEFAULT 0,
    `description` TEXT,
    `is_billable` TINYINT(1) DEFAULT 1,
    `user_rate_type` ENUM('hourly', 'minutely', 'secondly') DEFAULT 'hourly',
    `user_rate_amount` DECIMAL(10, 2) DEFAULT 0.00,
    `total_cost` DECIMAL(12, 2) DEFAULT 0.00,
    `currency` VARCHAR(3) DEFAULT 'USD',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `issue_time_logs_issue_id_idx` (`issue_id`),
    KEY `issue_time_logs_user_id_idx` (`user_id`),
    KEY `issue_time_logs_project_id_idx` (`project_id`),
    KEY `idx_time_logs_user_issue` (`user_id`, `issue_id`),
    KEY `idx_time_logs_date_range` (`work_date`, `status`),
    CONSTRAINT `issue_time_logs_issue_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_time_logs_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `issue_time_logs_project_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `worklogs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `time_spent` INT NOT NULL COMMENT 'Time spent in seconds',
    `started_at` DATETIME NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `worklogs_issue_id_idx` (`issue_id`),
    KEY `worklogs_user_id_idx` (`user_id`),
    CONSTRAINT `worklogs_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `worklogs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `active_timers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `issue_time_log_id` INT UNSIGNED NOT NULL,
    `issue_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED NOT NULL,
    `started_at` DATETIME NOT NULL,
    `last_heartbeat` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `session_token` VARCHAR(255) DEFAULT NULL,
    `browser_tab_id` VARCHAR(255) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `active_timers_user_unique` (`user_id`),
    CONSTRAINT `active_timers_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `active_timers_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
    CONSTRAINT `active_timers_time_log_id_fk` FOREIGN KEY (`issue_time_log_id`) REFERENCES `issue_time_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `project_budgets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL UNIQUE,
    `total_budget` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_cost` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(3) DEFAULT 'USD',
    `budget_type` ENUM('fixed', 'monthly', 'quarterly', 'yearly') NOT NULL DEFAULT 'fixed',
    `budget_period` ENUM('one_time', 'recurring') NOT NULL DEFAULT 'one_time',
    `period_start` DATE DEFAULT NULL,
    `period_end` DATE DEFAULT NULL,
    `alert_threshold` DECIMAL(5,2) NOT NULL DEFAULT 80.00 COMMENT 'Percentage used to trigger alert',
    `remaining_budget` DECIMAL(15,2) GENERATED ALWAYS AS (total_budget - total_cost) STORED,
    `budget_used_percentage` DECIMAL(5,2) GENERATED ALWAYS AS (
        IF(total_budget > 0, (total_cost / total_budget) * 100, 0)
    ) STORED,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `project_budgets_project_id_idx` (`project_id`),
    KEY `idx_budgets_period` (`budget_period`, `period_start`, `period_end`),
    CONSTRAINT `project_budgets_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `budget_alerts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_budget_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED NOT NULL,
    `alert_type` ENUM('warning', 'critical', 'exceeded') NOT NULL,
    `threshold_percentage` DECIMAL(5,2) NOT NULL,
    `actual_percentage` DECIMAL(5,2) NOT NULL,
    `cost_at_alert` DECIMAL(15,2) NOT NULL,
    `remaining_budget_at_alert` DECIMAL(15,2) NOT NULL,
    `is_acknowledged` TINYINT(1) DEFAULT 0,
    `acknowledged_by` INT UNSIGNED DEFAULT NULL,
    `acknowledged_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `budget_alerts_project_budget_id_idx` (`project_budget_id`),
    KEY `budget_alerts_project_id_idx` (`project_id`),
    CONSTRAINT `budget_alerts_project_budget_id_fk` FOREIGN KEY (`project_budget_id`) REFERENCES `project_budgets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `budget_alerts_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `budget_alerts_acknowledged_by_fk` FOREIGN KEY (`acknowledged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `time_tracking_settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT,
    `data_type` ENUM('string', 'integer', 'float', 'boolean', 'json') DEFAULT 'string',
    `group` VARCHAR(50) DEFAULT 'general',
    `description` TEXT,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `time_tracking_reports` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `report_type` ENUM('user_daily', 'user_weekly', 'project_daily', 'project_weekly', 'budget_status') NOT NULL,
    `report_date` DATE NOT NULL,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `project_id` INT UNSIGNED DEFAULT NULL,
    `report_data` JSON NOT NULL,
    `generated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `cache_valid_until` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `time_tracking_reports_unique` (`report_type`, `report_date`, `user_id`, `project_id`),
    CONSTRAINT `time_tracking_reports_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `time_tracking_reports_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
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
-- AUDIT LOG & SETTINGS
-- =====================================================

CREATE TABLE `audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT UNSIGNED DEFAULT NULL,
    `old_values` LONGTEXT DEFAULT NULL,
    `new_values` LONGTEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `audit_logs_user_id_idx` (`user_id`),
    KEY `audit_logs_entity_idx` (`entity_type`, `entity_id`),
    KEY `audit_logs_created_at_idx` (`created_at`),
    KEY `audit_logs_action_idx` (`action`),
    CONSTRAINT `audit_logs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `email_queue` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `to_email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `body` LONGTEXT NOT NULL,
    `status` ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    `sent_at` TIMESTAMP NULL DEFAULT NULL,
    `failed_at` TIMESTAMP NULL DEFAULT NULL,
    `error` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `email_queue_sent_at_idx` (`sent_at`),
    KEY `email_queue_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `type` VARCHAR(50) DEFAULT 'general',
    `category` VARCHAR(50) DEFAULT NULL,
    `unit_price` DECIMAL(12, 2) DEFAULT 0.00,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `items_created_by_idx` (`created_by`),
    CONSTRAINT `items_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CALENDAR & DOCUMENTS
-- =====================================================

CREATE TABLE `calendar_events` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_type` ENUM('issue', 'sprint', 'milestone', 'reminder', 'meeting') NOT NULL DEFAULT 'reminder',
    `project_id` INT UNSIGNED DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `start_date` DATETIME NOT NULL,
    `end_date` DATETIME NOT NULL,
    `priority_id` INT UNSIGNED DEFAULT NULL,
    `attendees` TEXT DEFAULT NULL COMMENT 'Comma-separated user emails or JSON',
    `reminders` JSON DEFAULT NULL COMMENT 'Reminder settings array',
    `recurring_type` ENUM('none', 'daily', 'weekly', 'monthly', 'yearly', 'custom') NOT NULL DEFAULT 'none',
    `recurring_interval` INT UNSIGNED DEFAULT NULL COMMENT 'Interval for recurring events',
    `recurring_ends` ENUM('never', 'after', 'on') DEFAULT NULL,
    `recurring_end_date` DATE DEFAULT NULL COMMENT 'End date for recurring events',
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `calendar_events_event_type_idx` (`event_type`),
    KEY `calendar_events_project_id_idx` (`project_id`),
    KEY `calendar_events_priority_id_idx` (`priority_id`),
    KEY `calendar_events_created_by_idx` (`created_by`),
    KEY `calendar_events_start_date_idx` (`start_date`),
    KEY `calendar_events_end_date_idx` (`end_date`),
    KEY `calendar_events_created_at_idx` (`created_at`),
    CONSTRAINT `calendar_events_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
    CONSTRAINT `calendar_events_priority_id_fk` FOREIGN KEY (`priority_id`) REFERENCES `issue_priorities` (`id`) ON DELETE SET NULL,
    CONSTRAINT `calendar_events_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `project_documents` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `size` INT UNSIGNED NOT NULL,
    `path` VARCHAR(500) NOT NULL,
    `category` ENUM('specification', 'design', 'requirement', 'other') DEFAULT 'other',
    `version` VARCHAR(20) DEFAULT '1.0.0',
    `is_public` TINYINT(1) DEFAULT 0,
    `download_count` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `project_documents_project_id_idx` (`project_id`),
    KEY `project_documents_user_id_idx` (`user_id`),
    CONSTRAINT `project_documents_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `project_documents_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
