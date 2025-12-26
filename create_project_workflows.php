<?php
require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

try {
    echo "Checking 'project_workflows' table...\n";

    // Create table
    Database::query("
        CREATE TABLE IF NOT EXISTS `project_workflows` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `project_id` INT UNSIGNED NOT NULL,
            `workflow_id` INT UNSIGNED NOT NULL,
            `issue_type_id` INT UNSIGNED DEFAULT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `pw_project_type_unique` (`project_id`, `issue_type_id`),
            KEY `pw_workflow_id_idx` (`workflow_id`),
            KEY `pw_issue_type_id_idx` (`issue_type_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table 'project_workflows' created/checked successfully.\n";

    // Get default workflow
    $defaultWorkflow = Database::selectOne("SELECT id FROM workflows WHERE is_default = 1");
    if (!$defaultWorkflow) {
        // Find ANY workflow if no default
        $defaultWorkflow = Database::selectOne("SELECT id FROM workflows LIMIT 1");
    }

    if ($defaultWorkflow) {
        echo "Default workflow ID: {$defaultWorkflow['id']}\n";

        // Link all existing projects to this workflow if not already linked
        $projects = Database::select("SELECT id FROM projects");
        foreach ($projects as $project) {
            $exists = Database::selectOne(
                "SELECT 1 FROM project_workflows WHERE project_id = ? AND issue_type_id IS NULL",
                [$project['id']]
            );

            if (!$exists) {
                Database::insert('project_workflows', [
                    'project_id' => $project['id'],
                    'workflow_id' => $defaultWorkflow['id'],
                    'issue_type_id' => null
                ]);
                echo "Linked project {$project['id']} to workflow {$defaultWorkflow['id']}.\n";
            }
        }
    } else {
        echo "WARNING: No workflows found in system. Cannot seed mappings.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
