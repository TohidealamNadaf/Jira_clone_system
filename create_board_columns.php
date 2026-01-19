<?php
/**
 * Create Default Board Columns
 * 
 * This script creates default columns (To Do, In Progress, Done) for boards that are missing them.
 * These columns are required for the Kanban board view to display properly.
 */

require 'bootstrap/autoload.php';
use App\Core\Database;

echo "=== CREATE BOARD COLUMNS ===" . PHP_EOL . PHP_EOL;

// Get all boards without columns
$boardsWithoutColumns = Database::select(
    "SELECT DISTINCT b.id, b.name, b.project_id, b.type
     FROM boards b
     LEFT JOIN board_columns bc ON b.id = bc.board_id
     WHERE bc.id IS NULL
     ORDER BY b.id"
);

if (!$boardsWithoutColumns) {
    echo "✅ All boards have columns. No action needed." . PHP_EOL;
    exit(0);
}

echo "Found " . count($boardsWithoutColumns) . " board(s) without columns:" . PHP_EOL;
foreach ($boardsWithoutColumns as $board) {
    echo "- Board ID {$board['id']}: {$board['name']} (Type: {$board['type']})" . PHP_EOL;
}

echo PHP_EOL . "Creating default columns..." . PHP_EOL;

// Get all statuses to map to columns
$statuses = Database::select('SELECT id, name, category FROM statuses ORDER BY id');
$statusMap = [];
foreach ($statuses as $status) {
    $statusMap[$status['category']][] = $status['id'];
}

echo "Status mapping:" . PHP_EOL;
foreach ($statusMap as $category => $ids) {
    echo "- {$category}: [" . implode(',', $ids) . "]" . PHP_EOL;
}

// Create columns for each board
$created = 0;
foreach ($boardsWithoutColumns as $board) {
    echo PHP_EOL . "Creating columns for board {$board['id']} ({$board['name']})..." . PHP_EOL;
    
    // Default column structure for Scrum/Kanban
    $columns = [
        [
            'name' => 'To Do',
            'status_ids' => isset($statusMap['todo']) ? json_encode($statusMap['todo']) : json_encode([1, 2, 3]),
            'color' => '#e5e5e5',
            'wip_limit' => null,
        ],
        [
            'name' => 'In Progress',
            'status_ids' => isset($statusMap['in_progress']) ? json_encode($statusMap['in_progress']) : json_encode([4]),
            'color' => '#ffe58f',
            'wip_limit' => 5,
        ],
        [
            'name' => 'Done',
            'status_ids' => isset($statusMap['done']) ? json_encode($statusMap['done']) : json_encode([5, 6]),
            'color' => '#95de64',
            'wip_limit' => null,
        ],
    ];
    
    foreach ($columns as $columnData) {
        try {
            Database::insert('board_columns', [
                'board_id' => $board['id'],
                'name' => $columnData['name'],
                'status_ids' => $columnData['status_ids'],
                'color' => $columnData['color'],
                'wip_limit' => $columnData['wip_limit'],
                'sort_order' => ($created % 3) + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $created++;
            echo "  ✅ Created column: {$columnData['name']}" . PHP_EOL;
        } catch (Exception $e) {
            echo "  ❌ Failed to create column {$columnData['name']}: " . $e->getMessage() . PHP_EOL;
        }
    }
}

echo PHP_EOL . "=== RESULT ===" . PHP_EOL;
echo "✅ Created $created columns for " . count($boardsWithoutColumns) . " board(s)" . PHP_EOL;

// Verify
$allBoardsNow = Database::select('SELECT DISTINCT b.id, b.name, COUNT(bc.id) as column_count FROM boards b LEFT JOIN board_columns bc ON b.id = bc.board_id GROUP BY b.id ORDER BY b.id');
echo PHP_EOL . "=== VERIFICATION ===" . PHP_EOL;
foreach ($allBoardsNow as $board) {
    $status = $board['column_count'] > 0 ? '✅' : '❌';
    echo "$status Board {$board['id']}: {$board['name']} - {$board['column_count']} columns" . PHP_EOL;
}

echo PHP_EOL . "Done! Boards should now display correctly." . PHP_EOL;
