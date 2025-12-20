<?php
/**
 * Create Roadmap Tables
 * Direct database connection - bypasses config loading issues
 */

echo "🚀 Creating Roadmap Tables...\n\n";

try {
    // Direct PDO connection
    $pdo = new PDO(
        'mysql:host=localhost;port=3306;dbname=jiira_clonee_system;charset=utf8',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    $queries = [
        // ROADMAP ITEMS TABLE
        "CREATE TABLE IF NOT EXISTS `roadmap_items` (
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
            CONSTRAINT `roadmap_items_owner_id_fk` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
            CONSTRAINT `roadmap_items_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci",

        // ROADMAP ITEM TO SPRINT MAPPING
        "CREATE TABLE IF NOT EXISTS `roadmap_item_sprints` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `roadmap_item_id` INT UNSIGNED NOT NULL,
            `sprint_id` INT UNSIGNED NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `roadmap_item_sprints_unique` (`roadmap_item_id`, `sprint_id`),
            KEY `roadmap_item_sprints_sprint_id_idx` (`sprint_id`),
            CONSTRAINT `roadmap_item_sprints_item_fk` FOREIGN KEY (`roadmap_item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE,
            CONSTRAINT `roadmap_item_sprints_sprint_fk` FOREIGN KEY (`sprint_id`) REFERENCES `sprints` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci",

        // ROADMAP ITEM DEPENDENCIES
        "CREATE TABLE IF NOT EXISTS `roadmap_dependencies` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci",

        // ROADMAP ITEM ISSUES MAPPING
        "CREATE TABLE IF NOT EXISTS `roadmap_item_issues` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `roadmap_item_id` INT UNSIGNED NOT NULL,
            `issue_id` INT UNSIGNED NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `roadmap_item_issues_unique` (`roadmap_item_id`, `issue_id`),
            KEY `roadmap_item_issues_issue_idx` (`issue_id`),
            CONSTRAINT `roadmap_item_issues_item_fk` FOREIGN KEY (`roadmap_item_id`) REFERENCES `roadmap_items` (`id`) ON DELETE CASCADE,
            CONSTRAINT `roadmap_item_issues_issue_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
    ];

    $count = 0;
    foreach ($queries as $query) {
        $pdo->exec($query);
        $count++;
        echo "  ✅ Created table " . ($count) . "\n";
    }

    echo "\n✨ Roadmap tables created successfully!\n";
    echo "📊 Executed $count table creation queries\n\n";

    // Verify tables exist
    echo "📋 Verifying tables...\n";
    
    $tables = [
        'roadmap_items',
        'roadmap_item_sprints',
        'roadmap_dependencies',
        'roadmap_item_issues'
    ];

    $result = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'jiira_clonee_system'")->fetchAll();
    $existingTables = array_column($result, 'TABLE_NAME');

    $allExists = true;
    foreach ($tables as $table) {
        if (in_array($table, $existingTables)) {
            echo "  ✅ $table\n";
        } else {
            echo "  ❌ $table (MISSING)\n";
            $allExists = false;
        }
    }

    if ($allExists) {
        echo "\n✅ All roadmap tables created successfully!\n";
        echo "📌 You can now access: http://localhost:8081/jira_clone_system/public/projects/CWAYS/roadmap\n";
        echo "\n🎯 Next: Clear your browser cache (CTRL+SHIFT+DEL) and refresh the page\n";
    } else {
        echo "\n⚠️  Some tables are missing. Check the error messages above.\n";
        exit(1);
    }

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
