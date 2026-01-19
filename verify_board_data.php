<?php

require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

$boardId = 6;
echo "Checking Board ID: $boardId\n";

$board = Database::selectOne("SELECT * FROM boards WHERE id = ?", [$boardId]);

if (!$board) {
    echo "Board not found!\n";
    exit;
}

echo "Board Found: " . $board['name'] . " (Type: " . $board['type'] . ")\n";

$columns = Database::select("SELECT * FROM board_columns WHERE board_id = ? ORDER BY sort_order ASC", [$boardId]);

echo "Column Count: " . count($columns) . "\n";

foreach ($columns as $col) {
    echo "- Column: " . $col['name'] . " (ID: " . $col['id'] . ", Status IDs: " . $col['status_ids'] . ")\n";
}

if (empty($columns)) {
    echo "ERROR: Board has NO columns.\n";
} else {
    echo "Board seems OK (structure-wise).\n";
}
