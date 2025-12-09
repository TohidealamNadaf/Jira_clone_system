<?php
/**
 * Direct test of velocity controller logic
 */

require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "=== VELOCITY CONTROLLER TEST ===\n\n";

// Simulate what ReportController::velocity() does
$boardId = 1;

echo "1. Getting board (ID: $boardId)...\n";
$board = Database::selectOne(
    "SELECT b.*, p.`key` as project_key
     FROM boards b
     JOIN projects p ON b.project_id = p.id
     WHERE b.id = ?",
    [$boardId]
);

if (!$board) {
    die("❌ Board not found\n");
}
echo "✓ Board found: " . $board['name'] . "\n\n";

echo "2. Getting done statuses...\n";
$completedStatuses = Database::select(
    "SELECT id FROM statuses WHERE category = 'done'"
);
$completedStatusIds = array_column($completedStatuses, 'id');
echo "✓ Done statuses found: " . count($completedStatusIds) . "\n";
echo "  IDs: " . implode(', ', $completedStatusIds) . "\n\n";

echo "3. Getting closed sprints...\n";
$sprints = Database::select(
    "SELECT id, name, start_date, end_date
     FROM sprints
     WHERE board_id = ? AND status = 'closed'
     ORDER BY end_date DESC
     LIMIT 10",
    [$boardId]
);
echo "✓ Closed sprints found: " . count($sprints) . "\n";

if (count($sprints) > 0) {
    echo "\nSprints:\n";
    foreach ($sprints as $s) {
        echo "  - " . $s['name'] . " (ID: " . $s['id'] . ")\n";
    }
} else {
    echo "  ⚠️  No closed sprints found!\n";
}

echo "\n4. Calculating velocity data...\n";
$velocityData = [];

foreach (array_reverse($sprints) as $sprint) {
    echo "  Processing: " . $sprint['name'] . "...\n";
    
    $committed = (float) Database::selectValue(
        "SELECT COALESCE(SUM(story_points), 0)
         FROM issues
         WHERE sprint_id = ?",
        [$sprint['id']]
    );
    echo "    Committed: $committed\n";

    $completed = 0;
    if (!empty($completedStatusIds)) {
        $placeholders = implode(',', array_fill(0, count($completedStatusIds), '?'));
        $completed = (float) Database::selectValue(
            "SELECT COALESCE(SUM(story_points), 0)
             FROM issues
             WHERE sprint_id = ? AND status_id IN ($placeholders)",
            array_merge([$sprint['id']], $completedStatusIds)
        );
    }
    echo "    Completed: $completed\n";

    $velocityData[] = [
        'sprint_id' => $sprint['id'],
        'sprint_name' => $sprint['name'],
        'committed' => $committed,
        'completed' => $completed,
        'start_date' => $sprint['start_date'],
        'end_date' => $sprint['end_date'],
    ];
}

echo "\n5. Calculating average velocity...\n";
$averageVelocity = count($velocityData) > 0
    ? array_sum(array_column($velocityData, 'completed')) / count($velocityData)
    : 0;
echo "✓ Average velocity: $averageVelocity\n";

echo "\n6. Final data structure:\n";
echo "Velocity Data (JSON):\n";
echo json_encode($velocityData, JSON_PRETTY_PRINT) . "\n";

echo "\n=== SIMULATION COMPLETE ===\n";
echo "Data would be passed to view as:\n";
echo "  velocityData => " . json_encode($velocityData) . "\n";
echo "  averageVelocity => $averageVelocity\n";
