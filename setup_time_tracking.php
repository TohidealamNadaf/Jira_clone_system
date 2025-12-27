<?php
/**
 * Time Tracking Setup - Create all required tables
 */

echo "Time Tracking System Setup\n";
echo "=========================\n\n";

try {
    $conn = new PDO(
        "mysql:host=localhost:3306;charset=utf8mb4",
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✓ Connected to MySQL\n";
    
    $dbName = 'jiira_clonee_system';
    $conn->exec("USE `$dbName`");
    echo "✓ Using database: $dbName\n\n";
    
    // Create tables in order (respecting foreign keys)
    $tables = [
        'user_rates' => "
            CREATE TABLE IF NOT EXISTS `user_rates` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `rate_type` ENUM('hourly', 'minutely', 'secondly') NOT NULL DEFAULT 'hourly',
                `rate_amount` DECIMAL(10, 4) NOT NULL,
                `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `effective_from` DATE NOT NULL,
                `effective_until` DATE NULL DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `user_rates_active_unique` (`user_id`, `rate_type`, `is_active`),
                KEY `user_rates_user_id_idx` (`user_id`),
                KEY `user_rates_is_active_idx` (`is_active`),
                CONSTRAINT `user_rates_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'issue_time_logs' => "
            CREATE TABLE IF NOT EXISTS `issue_time_logs` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `issue_id` INT UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `project_id` INT UNSIGNED NOT NULL,
                `status` ENUM('running', 'paused', 'stopped') NOT NULL DEFAULT 'paused',
                `start_time` DATETIME NOT NULL,
                `end_time` DATETIME NULL DEFAULT NULL,
                `paused_at` DATETIME NULL DEFAULT NULL,
                `resumed_at` DATETIME NULL DEFAULT NULL,
                `duration_seconds` INT UNSIGNED NOT NULL DEFAULT 0,
                `paused_seconds` INT UNSIGNED NOT NULL DEFAULT 0,
                `user_rate_type` ENUM('hourly', 'minutely', 'secondly') NOT NULL DEFAULT 'hourly',
                `user_rate_amount` DECIMAL(10, 4) NOT NULL,
                `total_cost` DECIMAL(12, 4) NOT NULL DEFAULT 0.00,
                `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
                `description` TEXT NULL DEFAULT NULL,
                `is_billable` TINYINT(1) NOT NULL DEFAULT 1,
                `session_id` VARCHAR(128) NULL DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `issue_time_logs_issue_id_idx` (`issue_id`),
                KEY `issue_time_logs_user_id_idx` (`user_id`),
                KEY `issue_time_logs_project_id_idx` (`project_id`),
                KEY `issue_time_logs_status_idx` (`status`),
                KEY `issue_time_logs_created_at_idx` (`created_at`),
                CONSTRAINT `issue_time_logs_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE,
                CONSTRAINT `issue_time_logs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                CONSTRAINT `issue_time_logs_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'active_timers' => "
            CREATE TABLE IF NOT EXISTS `active_timers` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `issue_time_log_id` BIGINT UNSIGNED NOT NULL,
                `issue_id` INT UNSIGNED NOT NULL,
                `project_id` INT UNSIGNED NOT NULL,
                `started_at` DATETIME NOT NULL,
                `last_heartbeat` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `active_timers_user_id_unique` (`user_id`),
                KEY `active_timers_issue_time_log_id_idx` (`issue_time_log_id`),
                CONSTRAINT `active_timers_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                CONSTRAINT `active_timers_issue_time_log_id_fk` FOREIGN KEY (`issue_time_log_id`) REFERENCES `issue_time_logs` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'project_budgets' => "
            CREATE TABLE IF NOT EXISTS `project_budgets` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_id` INT UNSIGNED NOT NULL,
                `total_budget` DECIMAL(12, 2) NOT NULL,
                `allocated_budget` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
                `total_cost` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
                `status` ENUM('planning', 'active', 'paused', 'completed', 'exceeded') NOT NULL DEFAULT 'active',
                `alert_threshold` DECIMAL(5, 2) NOT NULL DEFAULT 80.00,
                `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
                `start_date` DATE NOT NULL,
                `end_date` DATE NOT NULL,
                `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
                `description` TEXT NULL DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `project_budgets_project_id_unique` (`project_id`),
                CONSTRAINT `project_budgets_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'budget_alerts' => "
            CREATE TABLE IF NOT EXISTS `budget_alerts` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `project_budget_id` INT UNSIGNED NOT NULL,
                `project_id` INT UNSIGNED NOT NULL,
                `alert_type` ENUM('warning', 'critical', 'exceeded') NOT NULL,
                `threshold_percentage` DECIMAL(5, 2) NOT NULL,
                `actual_percentage` DECIMAL(5, 2) NOT NULL,
                `cost_at_alert` DECIMAL(12, 2) NOT NULL,
                `remaining_budget_at_alert` DECIMAL(12, 2) NOT NULL,
                `is_acknowledged` TINYINT(1) NOT NULL DEFAULT 0,
                `acknowledged_by_user_id` INT UNSIGNED NULL DEFAULT NULL,
                `acknowledged_at` DATETIME NULL DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `budget_alerts_project_budget_id_idx` (`project_budget_id`),
                CONSTRAINT `budget_alerts_project_budget_id_fk` FOREIGN KEY (`project_budget_id`) REFERENCES `project_budgets` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'time_tracking_settings' => "
            CREATE TABLE IF NOT EXISTS `time_tracking_settings` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `organization_id` INT UNSIGNED NOT NULL DEFAULT 1,
                `default_hourly_rate` DECIMAL(10, 4) NOT NULL DEFAULT 50.00,
                `default_minutely_rate` DECIMAL(10, 6) NOT NULL DEFAULT 0.833333,
                `default_secondly_rate` DECIMAL(12, 8) NOT NULL DEFAULT 0.01388889,
                `auto_pause_on_logout` TINYINT(1) NOT NULL DEFAULT 1,
                `require_description_on_stop` TINYINT(1) NOT NULL DEFAULT 0,
                `minimum_trackable_duration_seconds` INT UNSIGNED NOT NULL DEFAULT 60,
                `max_concurrent_timers_per_user` INT UNSIGNED NOT NULL DEFAULT 1,
                `round_duration_to_minutes` INT UNSIGNED NOT NULL DEFAULT 0,
                `default_currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
                `enable_billable_flag` TINYINT(1) NOT NULL DEFAULT 1,
                `enable_budget_tracking` TINYINT(1) NOT NULL DEFAULT 1,
                `enable_budget_alerts` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        "
    ];
    
    echo "Creating tables:\n";
    echo str_repeat("-", 50) . "\n";
    
    foreach ($tables as $name => $sql) {
        try {
            $conn->exec($sql);
            echo "✓ Created table: $name\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "✓ Table exists: $name\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo str_repeat("-", 50) . "\n\n";
    
    // Insert default settings
    echo "Setting up configuration:\n";
    $conn->exec("
        INSERT IGNORE INTO `time_tracking_settings` 
        (`organization_id`, `default_hourly_rate`, `enable_budget_tracking`) 
        VALUES (1, 50.00, 1)
    ");
    echo "✓ Default settings configured\n\n";
    
    echo "✅ Time tracking system setup complete!\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
