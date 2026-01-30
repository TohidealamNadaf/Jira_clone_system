<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

// Database::init(); // Not needed

echo "=== Sample Board Verification ===\n\n";

// Pick a random board (not CWays MIS if possible)
$board = Database::selectOne("SELECT * FROM boards WHERE name NOT LIKE ? LIMIT 1", ['%CWays MIS%']);

if (!$board) {
    echo "No other boards found to verify.\n";
    exit;
}

echo "Verifying Board: {$board['name']} (ID: {$board['id']})\n";

$columns = Database::select("SELECT * FROM board_columns WHERE board_id = ? ORDER BY sort_order", [$board['id']]);

echo "Column Count: " . count($columns) . "\n\n";

if (count($columns) !== 8) {
    echo "❌ ERROR: Expected 8 columns, found " . count($columns) . "\n";
} else {
    echo "✅ Column count is correct.\n";
}

foreach ($columns as $col) {
    echo "  - {$col['name']}\n";
    $statusIds = json_decode($col['status_ids'], true);
    if (empty($statusIds)) {
        echo "    ⚠️ No statuses mapped!\n";
    } else {
        $statusName = Database::selectValue("SELECT name FROM statuses WHERE id = ?", [$statusIds[0]]);
        echo "    ✅ Mapped to Status: $statusName (ID: {$statusIds[0]})\n";
    }
}
