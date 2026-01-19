<?php
require 'bootstrap/autoload.php';
use App\Core\Database;

echo "=== BOARD COLUMNS CHECK ===" . PHP_EOL . PHP_EOL;

// Check board columns for board ID 6
$columns = Database::select('SELECT * FROM board_columns WHERE board_id = 6');
echo 'Board 6 columns found: ' . count($columns) . PHP_EOL;
if ($columns) {
    foreach ($columns as $col) {
        echo '- ' . $col['name'] . ' (status_ids: ' . $col['status_ids'] . ')' . PHP_EOL;
    }
} else {
    echo '❌ Board 6 has NO columns!' . PHP_EOL;
}

echo PHP_EOL . "=== BOARD INFO ===" . PHP_EOL;
// Check if board exists
$board = Database::selectOne('SELECT * FROM boards WHERE id = 6');
echo 'Board exists: ' . ($board ? 'YES ✅' : 'NO ❌') . PHP_EOL;
if ($board) {
    echo 'Board name: ' . $board['name'] . PHP_EOL;
    echo 'Board type: ' . $board['type'] . PHP_EOL;
    echo 'Project ID: ' . $board['project_id'] . PHP_EOL;
}

echo PHP_EOL . "=== AVAILABLE STATUSES ===" . PHP_EOL;
// Check available statuses
$statuses = Database::select('SELECT id, name, category FROM statuses ORDER BY id');
echo 'Available statuses: ' . count($statuses) . PHP_EOL;
foreach ($statuses as $s) {
    echo '- ID ' . $s['id'] . ': ' . $s['name'] . ' (' . $s['category'] . ')' . PHP_EOL;
}

echo PHP_EOL . "=== ISSUES IN BOARD ===" . PHP_EOL;
// Check issues that should be in this board
$issues = Database::select(
    'SELECT COUNT(*) as cnt FROM issues WHERE project_id = (SELECT project_id FROM boards WHERE id = 6)'
);
echo 'Total issues in project: ' . ($issues[0]['cnt'] ?? 0) . PHP_EOL;

echo PHP_EOL . "RECOMMENDATION:" . PHP_EOL;
if (!$columns) {
    echo "1. Board 6 has no columns configured" . PHP_EOL;
    echo "2. Need to create default columns (To Do, In Progress, Done)" . PHP_EOL;
    echo "3. Run: php create_board_columns.php" . PHP_EOL;
}
