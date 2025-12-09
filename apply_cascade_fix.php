<?php
/**
 * Apply CASCADE DELETE Fix for Projects
 * This script fixes the foreign key constraints that were causing projects to be deleted
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

try {
    echo "Starting CASCADE DELETE fix...\n";
    
    $pdo = Database::getConnection();
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
    // Disable foreign key checks temporarily
    $pdo->query("SET FOREIGN_KEY_CHECKS = 0");
    
    $constraints = [
        ['table' => 'issues', 'fk_name' => 'issues_project_id_fk'],
        ['table' => 'project_members', 'fk_name' => 'project_members_project_id_fk'],
        ['table' => 'boards', 'fk_name' => 'boards_project_id_fk'],
        ['table' => 'components', 'fk_name' => 'components_project_id_fk'],
        ['table' => 'custom_field_contexts', 'fk_name' => 'custom_field_contexts_project_id_fk'],
        ['table' => 'labels', 'fk_name' => 'labels_project_id_fk'],
        ['table' => 'user_roles', 'fk_name' => 'user_roles_project_id_fk'],
        ['table' => 'versions', 'fk_name' => 'versions_project_id_fk'],
    ];
    
    foreach ($constraints as $constraint) {
        $table = $constraint['table'];
        $fk_name = $constraint['fk_name'];
        
        try {
            // Drop the existing foreign key
            $pdo->query("ALTER TABLE `$table` DROP FOREIGN KEY `$fk_name`");
            echo "✓ Dropped FK: $fk_name from $table\n";
        } catch (\Exception $e) {
            echo "⚠ FK already dropped or doesn't exist: $fk_name\n";
        }
    }
    
    // Recreate constraints with ON DELETE RESTRICT
    $constraints_to_add = [
        "ALTER TABLE `issues` ADD CONSTRAINT `issues_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `project_members` ADD CONSTRAINT `project_members_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `boards` ADD CONSTRAINT `boards_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `components` ADD CONSTRAINT `components_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `custom_field_contexts` ADD CONSTRAINT `custom_field_contexts_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `labels` ADD CONSTRAINT `labels_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `user_roles` ADD CONSTRAINT `user_roles_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `versions` ADD CONSTRAINT `versions_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
    ];
    
    foreach ($constraints_to_add as $sql) {
        try {
            $pdo->query($sql);
            preg_match('/`(\w+)`\s+.*FOREIGN KEY/', $sql, $matches);
            $fk_name = $matches[1] ?? 'unknown';
            echo "✓ Created FK with RESTRICT: $fk_name\n";
        } catch (\Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
    
    // Re-enable foreign key checks
    $pdo->query("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n✅ CASCADE DELETE fix applied successfully!\n";
    echo "Your projects are now protected from being accidentally deleted.\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
