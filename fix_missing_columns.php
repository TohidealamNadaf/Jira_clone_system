<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "Checking for boards with missing columns...\n";

// Get all boards
$boards = Database::select("SELECT * FROM boards");

foreach ($boards as $board) {
    $columns = Database::select("SELECT * FROM board_columns WHERE board_id = ?", [$board['id']]);

    echo "Board: " . $board['name'] . " (ID: " . $board['id'] . ") - Columns: " . count($columns) . "\n";

    if (count($columns) === 0) {
        echo "  -> FIXING: Creating default columns...\n";

        $statuses = Database::select("SELECT id, name, category FROM statuses ORDER BY sort_order ASC");

        $columnDefs = [
            'todo' => ['name' => 'To Do', 'statuses' => []],
            'in_progress' => ['name' => 'In Progress', 'statuses' => []],
            'done' => ['name' => 'Done', 'statuses' => []],
        ];

        foreach ($statuses as $status) {
            $columnDefs[$status['category']]['statuses'][] = $status['id'];
        }

        $order = 0;
        foreach ($columnDefs as $category => $colDef) {
            Database::insert('board_columns', [
                'board_id' => $board['id'],
                'name' => $colDef['name'],
                'status_ids' => json_encode($colDef['statuses']),
                'sort_order' => $order++,
            ]);
            echo "     - Created column: " . $colDef['name'] . "\n";
        }
        echo "  -> DONE.\n";
    }
}

echo "Check complete.\n";
