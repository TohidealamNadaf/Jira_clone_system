<?php
require 'bootstrap/app.php';

$boardId = 2;

$board = \App\Core\Database::selectOne(
    "SELECT b.*, p.id as project_id, p.`key` as project_key
    FROM boards b
    JOIN projects p ON b.project_id = p.id
    WHERE b.id = ?",
    [$boardId]
);

if (!$board) {
    die("Board not found");
}

$statuses = \App\Core\Database::select(
    "SELECT id, name, color, category
    FROM statuses
    ORDER BY sort_order"
);

$startDate = (new \DateTime())->modify("-30 days");
$endDate = new \DateTime();
$current = clone $startDate;

echo "Board Project ID: {$board['project_id']}\n\n";

// Test flow data generation
$flowData = [];
$dayCount = 0;

while ($current <= $endDate && $dayCount < 5) {
    $dateStr = $current->format('Y-m-d');
    $dayData = ['date' => $dateStr];
    
    echo "=== DATE: $dateStr ===\n";
    
    foreach ($statuses as $status) {
        // First query - with history
        $count = (int) \App\Core\Database::selectValue(
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
        
        // Fallback query - from current status
        if ($count === 0) {
            $count = (int) \App\Core\Database::selectValue(
                "SELECT COUNT(*)
                FROM issues
                WHERE project_id = ?
                AND status_id = ?
                AND DATE(created_at) <= ?",
                [$board['project_id'], $status['id'], $dateStr]
            );
        }
        
        echo "{$status['name']}: $count\n";
        $dayData[$status['name']] = $count;
    }
    
    $flowData[] = $dayData;
    $current->modify('+1 day');
    $dayCount++;
    echo "\n";
}

echo "=== FINAL FLOW DATA ===\n";
echo json_encode($flowData, JSON_PRETTY_PRINT);
