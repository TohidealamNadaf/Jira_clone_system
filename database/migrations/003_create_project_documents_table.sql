-- =====================================================
-- PROJECT DOCUMENTATION HUB TABLE
-- =====================================================

CREATE TABLE `project_documents` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `size` INT UNSIGNED NOT NULL,
    `path` VARCHAR(500) NOT NULL,
    `category` ENUM('requirement', 'design', 'technical', 'user_guide', 'training', 'report', 'other') DEFAULT 'other',
    `version` VARCHAR(20) DEFAULT '1.0',
    `is_public` BOOLEAN NOT NULL DEFAULT TRUE,
    `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `project_documents_project_id_idx` (`project_id`),
    KEY `project_documents_user_id_idx` (`user_id`),
    KEY `project_documents_category_idx` (`category`),
    KEY `project_documents_created_at_idx` (`created_at`),
    CONSTRAINT `project_documents_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `project_documents_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;