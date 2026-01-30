<?php
require 'vendor/autoload.php';

use App\Core\Database;

Database::init();

// Get all boards with project info
$boards = Database::select('
    SELECT b.id, b.name, b.project_id, p.name as project_name 
    FROM boards b 
    JOIN projects p ON b.project_id = p.id
    ORDER BY p.name
');

echo "=== Board Columns Analysis ===\n\n";

foreach ($boards as $board) {
    echo "Board: {$board['name']}\n";
    echo "  Project: {$board['project_name']} (ID: {$board['project_id']})\n";
    echo "  Board ID: {$board['id']}\n";

    $columns = Database::select('
        SELECT * FROM board_columns 
        WHERE board_id = ? 
        ORDER BY sort_order ASC
    ', [$board['id']]);

    echo "  Total Columns: " . count($columns) . "\n";

    if (count($columns) === 0) {
        echo "  ⚠️ WARNING: No columns configured!\n";
    } else {
        foreach ($columns as $col) {
            echo "    - {$col['name']} (status_ids: {$col['status_ids']}, sort_order: {$col['sort_order']})\n";
        }
    }

    echo "\n";
}

// Check statuses
echo "\n=== Available Statuses ===\n";
$statuses = Database::select('SELECT id, name, category FROM statuses ORDER BY category, sort_order');
foreach ($statuses as $status) {
    echo "  - {$status['name']} (ID: {$status['id']}, Category: {$status['category']})\n";
}
