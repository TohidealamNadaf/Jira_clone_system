<?php
require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "=== VELOCITY CHART DIAGNOSTICS ===\n\n";

// Check boards
$boards = Database::select("SELECT id, name, project_id FROM boards LIMIT 5");
echo "Boards found: " . count($boards) . "\n";
foreach ($boards as $board) {
    echo "  - " . $board['name'] . " (ID: " . $board['id'] . ")\n";
}

if (empty($boards)) {
    die("\nNo boards found. Create a board first.\n");
}

echo "\n";

// Check sprints
foreach ($boards as $board) {
    $sprints = Database::select(
        "SELECT id, name, status FROM sprints WHERE board_id = ?",
        [$board['id']]
    );
    echo "Board: " . $board['name'] . "\n";
    echo "  Total sprints: " . count($sprints) . "\n";
    
    $closedCount = count(array_filter($sprints, fn($s) => $s['status'] === 'closed'));
    echo "  Closed sprints: " . $closedCount . "\n";
    
    if ($closedCount > 0) {
        $closedSprints = Database::select(
            "SELECT id, name FROM sprints WHERE board_id = ? AND status = 'closed' ORDER BY end_date DESC LIMIT 5",
            [$board['id']]
        );
        
        foreach ($closedSprints as $sprint) {
            $issues = Database::selectOne(
                "SELECT COUNT(*) as count, COALESCE(SUM(story_points), 0) as points FROM issues WHERE sprint_id = ?",
                [$sprint['id']]
            );
            echo "    - Sprint: " . $sprint['name'] . " (Issues: " . $issues['count'] . ", Points: " . $issues['points'] . ")\n";
        }
    }
    echo "\n";
}
