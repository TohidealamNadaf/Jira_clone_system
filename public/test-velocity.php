<?php
/**
 * Direct velocity chart test - bypasses routing
 */

define('BASE_PATH', dirname(dirname(__FILE__)));

require_once BASE_PATH . '/bootstrap/autoload.php';
require_once BASE_PATH . '/bootstrap/app.php';

use App\Core\Database;
use App\Core\View;

// Set board ID
$boardId = 1;

echo "=== VELOCITY CHART DIRECT TEST ===\n\n";

// Get board
$board = Database::selectOne(
    "SELECT b.*, p.`key` as project_key FROM boards b JOIN projects p ON b.project_id = p.id WHERE b.id = ?",
    [$boardId]
);

if (!$board) {
    die("Board not found");
}

echo "Board: " . $board['name'] . "\n";

// Get statuses
$completedStatuses = Database::select("SELECT id FROM statuses WHERE category = 'done'");
$completedStatusIds = array_column($completedStatuses, 'id');

// Get sprints
$sprints = Database::select(
    "SELECT id, name, start_date, end_date FROM sprints WHERE board_id = ? AND status = 'closed' ORDER BY end_date DESC LIMIT 10",
    [$boardId]
);

echo "Closed sprints: " . count($sprints) . "\n";

// Calculate velocity
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

$averageVelocity = count($velocityData) > 0
    ? array_sum(array_column($velocityData, 'completed')) / count($velocityData)
    : 0;

// Get boards list
$boards = Database::select(
    "SELECT b.id, b.name FROM boards b WHERE b.project_id = ? ORDER BY b.name",
    [$board['project_id']]
);

echo "\n=== RENDERING VIEW ===\n";

// Try to render the view
try {
    $html = View::render('reports.velocity', [
        'board' => $board,
        'velocityData' => json_encode($velocityData),
        'averageVelocity' => round($averageVelocity, 1),
        'boards' => $boards,
        'selectedBoard' => $boardId,
    ]);

    echo "✓ View rendered successfully!\n";
    echo "HTML length: " . strlen($html) . " bytes\n";
    echo "\nFirst 500 characters:\n";
    echo substr($html, 0, 500) . "\n\n";

    // Output full HTML for testing
    echo "\n=== FULL HTML OUTPUT ===\n";
    echo $html;

} catch (\Exception $e) {
    echo "✗ View rendering failed: " . $e->getMessage() . "\n";
}
