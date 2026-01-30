<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

Database::init();

$boardId = 6; // CWays MIS Scrum Board
echo "Analyzing Board ID: $boardId (CWays MIS)\n";

$columns = Database::select("SELECT * FROM board_columns WHERE board_id = ? ORDER BY sort_order ASC", [$boardId]);

if (empty($columns)) {
    echo "No columns found for Board $boardId\n";
    // Try to find the board by name if ID 6 is wrong
    $board = Database::selectOne("SELECT * FROM boards WHERE name LIKE ? LIMIT 1", ['%CWays MIS%']);
    if ($board) {
        echo "Found board: {$board['name']} (ID: {$board['id']})\n";
        $columns = Database::select("SELECT * FROM board_columns WHERE board_id = ? ORDER BY sort_order ASC", [$board['id']]);
    }
}

foreach ($columns as $col) {
    echo "Column: {$col['name']}\n";
    echo "  Status IDs: {$col['status_ids']}\n";

    $statusIds = json_decode($col['status_ids'] ?? '[]', true);

    if (is_array($statusIds) && !empty($statusIds)) {
        // Create placeholders for IN clause
        $placeholders = implode(',', array_fill(0, count($statusIds), '?'));

        $sql = "SELECT * FROM statuses WHERE id IN ($placeholders)";
        $statuses = Database::select($sql, $statusIds);

        foreach ($statuses as $status) {
            echo "    - Status: {$status['name']} (ID: {$status['id']})\n";
        }
    } else {
        echo "    - No statuses mapped\n";
    }
    echo "\n";
}
