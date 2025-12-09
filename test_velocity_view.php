<?php
/**
 * Test velocity view rendering
 */

require 'bootstrap/autoload.php';

// Simulate the velocity controller
$boardId = 2;

$board = \App\Core\Database::selectOne(
    "SELECT b.*, p.`key` as project_key FROM boards b JOIN projects p ON b.project_id = p.id WHERE b.id = ?",
    [$boardId]
);

if (!$board) {
    die("Board not found");
}

$sprints = \App\Core\Database::select(
    "SELECT id, name, start_date, end_date FROM sprints WHERE board_id = ? AND status = 'closed' ORDER BY end_date DESC LIMIT 10",
    [$boardId]
);

$velocityData = [];
foreach ($sprints as $sprint) {
    $velocityData[] = [
        'sprint_id' => $sprint['id'],
        'sprint_name' => $sprint['name'],
        'committed' => 10,
        'completed' => 10,
    ];
}

// Pass data to view (simulating what controller does)
$velocityDataJson = json_encode($velocityData);

echo "==== DEBUG INFO ====\n";
echo "Board: {$board['name']}\n";
echo "Velocity Data JSON: {$velocityDataJson}\n";
echo "PHP Variable pass test:\n";
echo "Line 108 would output: const velocityRaw = {$velocityDataJson};\n";
echo "\n==== RENDERING PARTIAL VIEW ====\n";
echo "Starting to render views/reports/velocity.php...\n";

// Try to render the view manually
$view = new \App\Core\View();

// Render the velocity view with test data
try {
    $output = $view->render('reports.velocity', [
        'board' => $board,
        'velocityData' => $velocityDataJson,
        'averageVelocity' => 10,
        'projects' => [],
        'boards' => [\$board],
        'selectedBoard' => $boardId,
    ]);
    
    // Check if scripts section was rendered
    if (strpos($output, 'VELOCITY SCRIPT LOADED') !== false) {
        echo "✅ VELOCITY SCRIPT LOADED found in output\n";
    } else {
        echo "❌ VELOCITY SCRIPT LOADED NOT found in output\n";
        echo "This means the scripts section didn't render!\n";
    }
    
    // Check if the javascript is there
    if (strpos($output, 'velocityRaw') !== false) {
        echo "✅ velocityRaw variable found in output\n";
    } else {
        echo "❌ velocityRaw variable NOT found in output\n";
    }
    
    // Check for export button
    if (strpos($output, 'exportBtn') !== false) {
        echo "✅ exportBtn found in output\n";
    } else {
        echo "❌ exportBtn NOT found in output\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR rendering view: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
}
