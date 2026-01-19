<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

try {
    echo "Attempting to add 'description' column to 'project_documents'...\n";
    $sql = "ALTER TABLE `project_documents` ADD COLUMN `description` TEXT DEFAULT NULL AFTER `title`";
    Database::statement($sql);
    echo "Success: Column 'description' added.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Info: Column 'description' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
