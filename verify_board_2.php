<?php
require 'bootstrap/app.php';

$board_id = 2;

// Check board
$board = \App\Core\Database::selectOne(
    "SELECT b.*, p.id as project_id FROM boards b JOIN projects p ON b.project_id = p.id WHERE b.id = ?",
    [$board_id]
);

echo "=== BOARD INFO ===\n";
if ($board) {
    echo json_encode($board, JSON_PRETTY_PRINT) . "\n\n";
    
    // Count issues in this project
    $issueCount = \App\Core\Database::selectValue(
        "SELECT COUNT(*) FROM issues WHERE project_id = ?",
        [$board['project_id']]
    );
    echo "Total issues in project: $issueCount\n\n";
    
    // Check if there are any issues created within the last 30 days
    $recentIssues = \App\Core\Database::selectValue(
        "SELECT COUNT(*) FROM issues WHERE project_id = ? AND DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
        [$board['project_id']]
    );
    echo "Issues created in last 30 days: $recentIssues\n\n";
    
    // Check all statuses
    $statuses = \App\Core\Database::select(
        "SELECT id, name FROM statuses ORDER BY id"
    );
    echo "=== STATUSES ===\n";
    foreach ($statuses as $s) {
        $count = \App\Core\Database::selectValue(
            "SELECT COUNT(*) FROM issues WHERE project_id = ? AND status_id = ?",
            [$board['project_id'], $s['id']]
        );
        echo "{$s['name']}: $count issues\n";
    }
} else {
    echo "Board not found\n";
}
