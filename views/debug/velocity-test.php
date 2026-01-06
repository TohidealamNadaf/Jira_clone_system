<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container">
    <h2>Velocity Chart Debug Test</h2>
    
    <div class="alert alert-info">
        <strong>Open DevTools (F12)</strong> and check the Console tab to see debug information.
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <h5>Test Data</h5>
            <pre><code><?php
// Simulate what the controller does
require_once __DIR__ . '/../../bootstrap/autoload.php';
require_once __DIR__ . '/../../bootstrap/app.php';

use App\Core\Database;

$boardId = isset($_GET['board_id']) ? (int)$_GET['board_id'] : 1;
$board = Database::selectOne("SELECT id, name FROM boards WHERE id = ?", [$boardId]);

if ($board) {
    $sprints = Database::select(
        "SELECT id, name FROM sprints WHERE board_id = ? AND status = 'closed' ORDER BY end_date DESC LIMIT 3",
        [$boardId]
    );
    
    $velocityData = [];
    foreach ($sprints as $sprint) {
        $committed = Database::selectValue("SELECT COALESCE(SUM(story_points), 0) FROM issues WHERE sprint_id = ?", [$sprint['id']]);
        $velocityData[] = [
            'sprint_name' => $sprint['name'],
            'committed' => $committed,
            'completed' => $committed * 0.8
        ];
    }
    
    echo "Board: " . htmlspecialchars($board['name']) . "\n";
    echo "Closed Sprints: " . count($sprints) . "\n\n";
    echo json_encode($velocityData, JSON_PRETTY_PRINT);
} else {
    echo "No board found";
}
            ?></code></pre>
        </div>
        
        <div class="col-md-6">
            <h5>Chart Test</h5>
            <canvas id="testChart" height="200"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
console.log('=== VELOCITY CHART DEBUG ===');

// Test data
const testData = {
    labels: ['Sprint 1', 'Sprint 2', 'Sprint 3'],
    committed: [20, 25, 22],
    completed: [18, 24, 20]
};

console.log('Test data:', testData);

try {
    const ctx = document.getElementById('testChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: testData.labels,
            datasets: [
                {
                    label: 'Committed',
                    data: testData.committed,
                    backgroundColor: 'rgba(108, 117, 125, 0.6)',
                    borderColor: '#6c757d',
                    borderWidth: 1
                },
                {
                    label: 'Completed',
                    data: testData.completed,
                    backgroundColor: 'rgba(25, 135, 84, 0.8)',
                    borderColor: '#198754',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
    console.log('✅ Test chart created successfully');
} catch (error) {
    console.error('❌ Error creating test chart:', error);
}
</script>

<?php \App\Core\View::endSection(); ?>
