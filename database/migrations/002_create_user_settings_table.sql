CREATE TABLE IF NOT EXISTS `user_settings` (
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
