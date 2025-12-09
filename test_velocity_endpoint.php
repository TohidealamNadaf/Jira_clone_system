<?php

require 'bootstrap/autoload.php';

// Check if board 2 exists
$board = \App\Core\Database::selectOne('SELECT * FROM boards WHERE id = 2');
echo "Board 2: " . json_encode($board) . "\n\n";

// Check sprints for board 2
$sprints = \App\Core\Database::select(
    "SELECT * FROM sprints WHERE board_id = 2 AND status = 'closed' LIMIT 5"
);
echo "Sprints for board 2: " . json_encode($sprints) . "\n\n";

// Check if there are any completed statuses
$completedStatuses = \App\Core\Database::select(
    "SELECT id FROM statuses WHERE category = 'done'"
);
echo "Completed statuses: " . json_encode($completedStatuses) . "\n\n";

// Check issues in closed sprints
if (!empty($sprints)) {
    foreach ($sprints as $sprint) {
        $issues = \App\Core\Database::select(
            "SELECT id, key, story_points FROM issues WHERE sprint_id = ?",
            [$sprint['id']]
        );
        echo "Issues in sprint {$sprint['id']}: " . json_encode($issues) . "\n";
    }
}
