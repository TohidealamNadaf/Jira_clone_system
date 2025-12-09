<?php
/**
 * Test velocity chart data rendering
 * Access at: http://localhost/jira_clone_system/public/test_velocity_raw.php
 */

require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

// Get first board
$board = Database::selectOne("SELECT b.*, p.`key` as project_key FROM boards b JOIN projects p ON b.project_id = p.id LIMIT 1");

if (!$board) {
    die(json_encode(['error' => 'No boards found']));
}

$boardId = $board['id'];

// Get closed sprints
$sprints = Database::select(
    "SELECT id, name, start_date, end_date
     FROM sprints
     WHERE board_id = ? AND status = 'closed'
     ORDER BY end_date DESC
     LIMIT 10",
    [$boardId]
);

// Get done statuses
$completedStatuses = Database::select("SELECT id FROM statuses WHERE category = 'done'");
$completedStatusIds = array_column($completedStatuses, 'id');

// Process sprints
$velocityData = [];
foreach (array_reverse($sprints) as $sprint) {
    $committed = (float) Database::selectValue(
        "SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ?",
        [$sprint['id']]
    );

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

    $velocityData[] = [
        'sprint_id' => $sprint['id'],
        'sprint_name' => $sprint['name'],
        'committed' => $committed,
        'completed' => $completed,
        'start_date' => $sprint['start_date'],
        'end_date' => $sprint['end_date'],
    ];
}

$averageVelocity = count($velocityData) > 0
    ? array_sum(array_column($velocityData, 'completed')) / count($velocityData)
    : 0;

echo json_encode([
    'board' => $board,
    'sprint_count' => count($velocityData),
    'average_velocity' => round($averageVelocity, 1),
    'velocity_data' => $velocityData,
], JSON_PRETTY_PRINT);
