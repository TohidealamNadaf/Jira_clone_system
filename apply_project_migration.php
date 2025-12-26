<?php
require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "Checking 'projects' table schema...\n";

try {
    // Check if column exists
    $columns = Database::select("SHOW COLUMNS FROM projects LIKE 'is_private'");

    if (empty($columns)) {
        echo "Adding 'is_private' column to projects table...\n";

        // Add column
        Database::query("ALTER TABLE `projects` ADD COLUMN `is_private` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_archived`");

        // Add index
        Database::query("ALTER TABLE `projects` ADD INDEX `projects_is_private_idx` (`is_private`)");

        echo "Successfully added 'is_private' column.\n";
    } else {
        echo "Column 'is_private' already exists. Skipping.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
