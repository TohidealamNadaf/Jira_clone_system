<?php
// Simple test to verify velocity data

require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

// Get first board
$board = Database::selectOne("SELECT b.id, b.name FROM boards b LIMIT 1");

if (!$board) {
    die("No boards found");
}

$boardId = $board['id'];
echo "Testing board: " . $board['name'] . " (ID: $boardId)\n\n";

// Get closed sprints
$sprints = Database::select(
    "SELECT id, name, start_date, end_date FROM sprints WHERE board_id = ? AND status = 'closed' ORDER BY end_date DESC LIMIT 10",
    [$boardId]
);

echo "Closed sprints found: " . count($sprints) . "\n";
print_r($sprints);

if (!empty($sprints)) {
    echo "\n\nSprint details:\n";
    foreach ($sprints as $sprint) {
        $committed = Database::selectValue(
            "SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ?",
            [$sprint['id']]
        );
        
        $completedStatuses = Database::select("SELECT id FROM statuses WHERE category = 'done'");
        $statusIds = array_column($completedStatuses, 'id');
        $completed = 0;
        
        if (!empty($statusIds)) {
            $placeholders = implode(',', array_fill(0, count($statusIds), '?'));
            $completed = Database::selectValue(
                "SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ? AND status_id IN ($placeholders)",
                array_merge([$sprint['id']], $statusIds)
            );
        }
        
        echo "Sprint: " . $sprint['name'] . " | Committed: $committed | Completed: $completed\n";
    }
} else {
    echo "\nNo closed sprints found. Create some sprints and close them first.\n";
}
