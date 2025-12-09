<?php
// Direct test of velocity endpoint

require 'bootstrap/autoload.php';

$boardId = 2;

// Test database connection
try {
    $test = \App\Core\Database::selectOne("SELECT 1");
    echo "✅ Database connection OK\n\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test board exists
$board = \App\Core\Database::selectOne(
    "SELECT b.id, b.name, b.project_id, p.`key` FROM boards b JOIN projects p ON b.project_id = p.id WHERE b.id = ?",
    [$boardId]
);

if (!$board) {
    echo "❌ Board $boardId not found\n";
    exit(1);
}

echo "✅ Board found: {$board['name']} ({$board['key']})\n\n";

// Get completed statuses
$completedStatuses = \App\Core\Database::select("SELECT id FROM statuses WHERE category = 'done'");
$completedStatusIds = array_column($completedStatuses, 'id');

echo "Completed statuses: " . implode(',', $completedStatusIds) . "\n\n";

// Get sprints
$sprints = \App\Core\Database::select(
    "SELECT id, name, start_date, end_date FROM sprints WHERE board_id = ? AND status = 'closed' ORDER BY end_date DESC LIMIT 10",
    [$boardId]
);

echo "Closed sprints: " . count($sprints) . "\n";

if (empty($sprints)) {
    echo "⚠️  No closed sprints found!\n";
    echo "This is why the chart is empty.\n";
    echo "\nHere's what needs to happen for the chart to show:\n";
    echo "1. Create a sprint\n";
    echo "2. Add issues with story points\n";
    echo "3. Mark issues as 'Done'\n";
    echo "4. Close/Complete the sprint\n";
    exit(0);
}

// Build velocity data
$velocityData = [];
foreach (array_reverse($sprints) as $sprint) {
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

    $velocityData[] = [
        'sprint_id' => $sprint['id'],
        'sprint_name' => $sprint['name'],
        'committed' => $committed,
        'completed' => $completed,
        'start_date' => $sprint['start_date'],
        'end_date' => $sprint['end_date'],
    ];
}

echo "\nVelocity data:\n";
echo json_encode($velocityData, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK) . "\n";

$avgVelocity = count($velocityData) > 0
    ? array_sum(array_column($velocityData, 'completed')) / count($velocityData)
    : 0;

echo "\n✅ Average velocity: " . round($avgVelocity, 1) . " points\n";

// Show JSON as it would be passed to JavaScript
echo "\n\nJSON for JavaScript (what gets passed to view):\n";
echo "const velocityRaw = " . json_encode($velocityData) . ";\n";
