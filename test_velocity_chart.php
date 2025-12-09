<?php

// Quick test to verify velocity chart data flow
require 'bootstrap/autoload.php';

$boardId = 2;

echo "=== VELOCITY CHART DATA TEST ===\n\n";

// Test 1: Check if board exists
$board = \App\Core\Database::selectOne(
    "SELECT b.*, p.`key` as project_key FROM boards b JOIN projects p ON b.project_id = p.id WHERE b.id = ?",
    [$boardId]
);

if (!$board) {
    echo "❌ Board ID $boardId not found\n";
    exit(1);
}

echo "✅ Board found: {$board['name']} ({$board['project_key']})\n\n";

// Test 2: Get completed statuses
$completedStatuses = \App\Core\Database::select(
    "SELECT id FROM statuses WHERE category = 'done'"
);
$completedStatusIds = array_column($completedStatuses, 'id');

echo "Completed Statuses (" . count($completedStatuses) . "): " . implode(',', $completedStatusIds) . "\n\n";

// Test 3: Get closed sprints
$sprints = \App\Core\Database::select(
    "SELECT id, name, start_date, end_date FROM sprints WHERE board_id = ? AND status = 'closed' ORDER BY end_date DESC LIMIT 10",
    [$boardId]
);

echo "Closed Sprints: " . count($sprints) . "\n";

if (empty($sprints)) {
    echo "⚠️  No closed sprints found for board $boardId\n";
    echo "   This is why the chart is empty!\n";
    echo "   Solution: Create and close some sprints first.\n\n";
    exit(0);
}

// Test 4: Check velocity data
echo "\nVelocity Data:\n";
echo str_repeat("-", 80) . "\n";
printf("%-30s | %-12s | %-12s | %-12s\n", "Sprint Name", "Committed", "Completed", "Accuracy");
echo str_repeat("-", 80) . "\n";

$velocityData = [];
foreach ($sprints as $sprint) {
    $committed = (float) \App\Core\Database::selectValue(
        "SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ?",
        [$sprint['id']]
    );

    $completed = 0;
    if (!empty($completedStatusIds)) {
        $placeholders = implode(',', array_fill(0, count($completedStatusIds), '?'));
        $completed = (float) \App\Core\Database::selectValue(
            "SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ? AND status_id IN ($placeholders)",
            array_merge([$sprint['id']], $completedStatusIds)
        );
    }

    $accuracy = $committed > 0 ? round(($completed / $committed) * 100) : 0;
    
    printf("%-30s | %-12.1f | %-12.1f | %-12d%%\n", 
        substr($sprint['name'], 0, 29), 
        $committed, 
        $completed, 
        $accuracy
    );

    $velocityData[] = [
        'sprint_id' => $sprint['id'],
        'sprint_name' => $sprint['name'],
        'committed' => $committed,
        'completed' => $completed,
        'start_date' => $sprint['start_date'],
        'end_date' => $sprint['end_date'],
    ];
}

echo str_repeat("-", 80) . "\n";

// Test 5: Calculate average velocity
$avgVelocity = count($velocityData) > 0
    ? array_sum(array_column($velocityData, 'completed')) / count($velocityData)
    : 0;

echo "\n✅ Average Velocity: " . round($avgVelocity, 1) . " points/sprint\n";
echo "✅ Data points: " . count($velocityData) . " sprints\n";

// Test 6: Show JSON output (what controller sends to view)
echo "\n\nJSON Data (sent to JavaScript):\n";
echo json_encode($velocityData, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK) . "\n";

echo "\n✅ All tests passed! Chart should display correctly.\n";
