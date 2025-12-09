<?php
require 'bootstrap/app.php';

$boardId = 2;

// Check if board exists
$board = \App\Core\Database::selectOne(
    "SELECT b.*, p.id as project_id, p.`key` as project_key
    FROM boards b
    JOIN projects p ON b.project_id = p.id
    WHERE b.id = ?",
    [$boardId]
);

echo "=== BOARD ===\n";
echo json_encode($board, JSON_PRETTY_PRINT) . "\n\n";

if ($board) {
    // Check if there are issues
    $issues = \App\Core\Database::select(
        "SELECT id, issue_key, status_id, created_at FROM issues WHERE project_id = ? LIMIT 10",
        [$board['project_id']]
    );
    echo "=== ISSUES ===\n";
    echo json_encode($issues, JSON_PRETTY_PRINT) . "\n\n";
    
    // Check statuses
    $statuses = \App\Core\Database::select(
        "SELECT id, name, color FROM statuses ORDER BY sort_order"
    );
    echo "=== STATUSES ===\n";
    echo json_encode($statuses, JSON_PRETTY_PRINT) . "\n\n";
    
    // Check issue_history
    $history = \App\Core\Database::select(
        "SELECT id, issue_id, field, old_value, new_value, created_at FROM issue_history WHERE field = 'status_id' LIMIT 20"
    );
    echo "=== ISSUE HISTORY ===\n";
    echo json_encode($history, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test the query
    echo "=== TESTING FLOW DATA QUERY ===\n";
    $startDate = (new \DateTime())->modify("-30 days");
    $endDate = new \DateTime();
    $current = clone $startDate;
    
    $dateStr = $current->format('Y-m-d');
    
    if (!empty($statuses)) {
        $status = $statuses[0];
        echo "Testing status: {$status['name']} (ID: {$status['id']})\n";
        
        $count = \App\Core\Database::selectValue(
            "SELECT COUNT(*)
            FROM issues i
            JOIN issue_history h ON i.id = h.issue_id
            WHERE i.project_id = ?
            AND h.field = 'status_id'
            AND h.new_value = ?
            AND DATE(h.created_at) <= ?
            AND (
                NOT EXISTS (
                    SELECT 1 FROM issue_history h2
                    WHERE h2.issue_id = i.id
                    AND h2.field = 'status_id'
                    AND h2.created_at > h.created_at
                    AND DATE(h2.created_at) <= ?
                )
            )",
            [$board['project_id'], $status['id'], $dateStr, $dateStr]
        );
        echo "Count from history: $count\n";
        
        $fallback = \App\Core\Database::selectValue(
            "SELECT COUNT(*)
            FROM issues
            WHERE project_id = ?
            AND status_id = ?
            AND DATE(created_at) <= ?",
            [$board['project_id'], $status['id'], $dateStr]
        );
        echo "Count from current status: $fallback\n";
    }
}
