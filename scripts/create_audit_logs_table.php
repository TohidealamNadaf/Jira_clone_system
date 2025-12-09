<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

echo "Creating audit_logs table...\n";

try {
    Database::query("
        CREATE TABLE IF NOT EXISTS `audit_logs` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✅ audit_logs table created successfully!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
