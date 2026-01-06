<?php
/**
 * Velocity Chart Debug - Direct Access
 * Access: http://localhost/jira_clone_system/public/velocity-debug.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Load bootstrap
$basePath = dirname(dirname(__FILE__));
require_once $basePath . '/bootstrap/autoload.php';
require_once $basePath . '/bootstrap/app.php';

use App\Core\Database;

echo "<h1>Velocity Chart Debug</h1>";
echo "<hr>";

try {
    // Get board
    echo "<h3>1. Fetching Board...</h3>";
    $board = Database::selectOne(
        "SELECT b.*, p.`key` as project_key FROM boards b JOIN projects p ON b.project_id = p.id WHERE b.id = ?",
        [1]
    );

    if (!$board) {
        echo "<div style='color: red;'><strong>❌ ERROR:</strong> No board found</div>";
        exit;
    }

    echo "<p><strong>✓ Board Found:</strong> " . htmlspecialchars($board['name']) . "</p>";

    // Get statuses
    echo "<h3>2. Fetching Done Statuses...</h3>";
    $completedStatuses = Database::select("SELECT id FROM statuses WHERE category = 'done'");
    $completedStatusIds = array_column($completedStatuses, 'id');
    echo "<p><strong>✓ Done Status IDs:</strong> " . implode(', ', $completedStatusIds) . "</p>";

    // Get sprints
    echo "<h3>3. Fetching Closed Sprints...</h3>";
    $sprints = Database::select(
        "SELECT id, name, start_date, end_date FROM sprints WHERE board_id = ? AND status = 'closed' ORDER BY end_date DESC LIMIT 10",
        [1]
    );
    echo "<p><strong>✓ Closed Sprints Found:</strong> " . count($sprints) . "</p>";

    if (count($sprints) > 0) {
        echo "<ul>";
        foreach ($sprints as $s) {
            echo "<li>" . htmlspecialchars($s['name']) . " (ID: " . $s['id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'><strong>⚠️  WARNING:</strong> No closed sprints found!</p>";
    }

    // Calculate velocity
    echo "<h3>4. Calculating Velocity Data...</h3>";
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

    echo "<p><strong>✓ Velocity Data Calculated</strong></p>";

    // Average
    $averageVelocity = count($velocityData) > 0
        ? array_sum(array_column($velocityData, 'completed')) / count($velocityData)
        : 0;
    echo "<p><strong>✓ Average Velocity:</strong> $averageVelocity</p>";

    // Show data
    echo "<h3>5. Velocity Data (JSON):</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px; overflow-x: auto;'>";
    echo htmlspecialchars(json_encode($velocityData, JSON_PRETTY_PRINT));
    echo "</pre>";

    echo "<h3>6. Summary</h3>";
    echo "<ul>";
    echo "<li>Board: " . htmlspecialchars($board['name']) . "</li>";
    echo "<li>Closed Sprints: " . count($sprints) . "</li>";
    echo "<li>Average Velocity: $averageVelocity</li>";
    echo "<li>Data Ready: " . (count($velocityData) > 0 ? '✓ YES' : '✗ NO') . "</li>";
    echo "</ul>";

    if (count($velocityData) > 0) {
        echo "<div style='color: green; background: #f0f0f0; padding: 10px; margin-top: 20px;'>";
        echo "<strong>✓ SUCCESS:</strong> Velocity chart should work with " . count($velocityData) . " sprints of data!";
        echo "</div>";
    } else {
        echo "<div style='color: orange; background: #f0f0f0; padding: 10px; margin-top: 20px;'>";
        echo "<strong>⚠️ WARNING:</strong> No velocity data. Create closed sprints with issues first.";
        echo "</div>";
    }

} catch (\Exception $e) {
    echo "<div style='color: red;'>";
    echo "<strong>❌ ERROR:</strong> " . htmlspecialchars($e->getMessage());
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='/jira_clone_system/public/reports/velocity/1'>Back to Velocity Chart</a></p>";
