<?php
/**
 * Diagnose Roadmap Issue
 * Check database tables, data, and service methods
 */

declare(strict_types=1);

session_start();
require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;
use App\Services\RoadmapService;
use App\Services\ProjectService;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Roadmap Diagnosis</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1, h2 { color: #333; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #8b1956; border-radius: 4px; }
        .test { margin: 10px 0; padding: 10px; background: white; border-left: 3px solid #ccc; }
        .test.success { border-left-color: #4caf50; background: #f1f8f4; }
        .test.error { border-left-color: #f44336; background: #fde8e8; }
        .test.warning { border-left-color: #ff9800; background: #fff3e0; }
        code { background: #eee; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        .check { margin: 5px 0; }
        .icon { margin-right: 5px; }
        .good { color: #4caf50; }
        .bad { color: #f44336; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Roadmap Issue Diagnosis</h1>
        
        <!-- Step 1: Check Database Table -->
        <div class="section">
            <h2>Step 1: Check Database Table</h2>
            <?php
            try {
                // Check if table exists
                $result = Database::select(
                    "SELECT TABLE_NAME FROM information_schema.TABLES 
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'roadmap_items'",
                    ['jiira_clonee_system']
                );
                
                if (!empty($result)) {
                    echo '<div class="test success">';
                    echo '<span class="icon good">✓</span> <strong>roadmap_items table exists</strong>';
                    echo '</div>';
                    
                    // Get table structure
                    $columns = Database::select("DESCRIBE roadmap_items");
                    echo '<h3>Table Structure:</h3>';
                    echo '<table>';
                    echo '<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
                    foreach ($columns as $col) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($col['Field']) . '</td>';
                        echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
                        echo '<td>' . htmlspecialchars($col['Null']) . '</td>';
                        echo '<td>' . htmlspecialchars($col['Key'] ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($col['Default'] ?? '') . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<div class="test error">';
                    echo '<span class="icon bad">✗</span> <strong>roadmap_items table does NOT exist</strong>';
                    echo '<p>Need to run migration: <code>database/migrations/003_create_roadmap_tables.sql</code></p>';
                    echo '</div>';
                }
            } catch (\Exception $e) {
                echo '<div class="test error">';
                echo '<span class="icon bad">✗</span> <strong>Error checking table:</strong><br>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Step 2: Check Data in Table -->
        <div class="section">
            <h2>Step 2: Check Data in roadmap_items Table</h2>
            <?php
            try {
                $count = Database::select("SELECT COUNT(*) as count FROM roadmap_items");
                $totalItems = $count[0]['count'] ?? 0;
                
                if ($totalItems > 0) {
                    echo '<div class="test success">';
                    echo '<span class="icon good">✓</span> <strong>Found ' . intval($totalItems) . ' roadmap items in database</strong>';
                    echo '</div>';
                    
                    // List all items
                    $items = Database::select("SELECT id, project_id, title, type, status, progress_percentage FROM roadmap_items ORDER BY created_at DESC LIMIT 20");
                    echo '<h3>Recent Roadmap Items:</h3>';
                    echo '<table>';
                    echo '<tr><th>ID</th><th>Project ID</th><th>Title</th><th>Type</th><th>Status</th><th>Progress</th></tr>';
                    foreach ($items as $item) {
                        echo '<tr>';
                        echo '<td>' . intval($item['id']) . '</td>';
                        echo '<td>' . intval($item['project_id']) . '</td>';
                        echo '<td>' . htmlspecialchars($item['title']) . '</td>';
                        echo '<td>' . htmlspecialchars($item['type']) . '</td>';
                        echo '<td>' . htmlspecialchars($item['status']) . '</td>';
                        echo '<td>' . intval($item['progress_percentage']) . '%</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<div class="test warning">';
                    echo '<span class="icon">⚠</span> <strong>No roadmap items found in database</strong>';
                    echo '<p>This is why the page shows "No roadmap items". Try creating one to test.</p>';
                    echo '</div>';
                }
            } catch (\Exception $e) {
                echo '<div class="test error">';
                echo '<span class="icon bad">✗</span> <strong>Error querying table:</strong><br>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Step 3: Check Project CWAYS -->
        <div class="section">
            <h2>Step 3: Check Project "CWAYS"</h2>
            <?php
            try {
                $cways = Database::select(
                    "SELECT id, name, key FROM projects WHERE key = ?",
                    ['CWAYS']
                );
                
                if (!empty($cways)) {
                    $projectId = $cways[0]['id'];
                    echo '<div class="test success">';
                    echo '<span class="icon good">✓</span> <strong>Found project CWAYS</strong> (ID: ' . intval($projectId) . ')';
                    echo '</div>';
                    
                    // Check items for this project
                    $projectItems = Database::select(
                        "SELECT id, title, type, status FROM roadmap_items WHERE project_id = ?",
                        [$projectId]
                    );
                    
                    if (!empty($projectItems)) {
                        echo '<div class="test success">';
                        echo '<span class="icon good">✓</span> <strong>Found ' . count($projectItems) . ' roadmap items for CWAYS</strong>';
                        echo '<h3>Items for CWAYS:</h3>';
                        echo '<table>';
                        echo '<tr><th>ID</th><th>Title</th><th>Type</th><th>Status</th></tr>';
                        foreach ($projectItems as $item) {
                            echo '<tr>';
                            echo '<td>' . intval($item['id']) . '</td>';
                            echo '<td>' . htmlspecialchars($item['title']) . '</td>';
                            echo '<td>' . htmlspecialchars($item['type']) . '</td>';
                            echo '<td>' . htmlspecialchars($item['status']) . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                        echo '</div>';
                    } else {
                        echo '<div class="test warning">';
                        echo '<span class="icon">⚠</span> <strong>No roadmap items for CWAYS project</strong>';
                        echo '<p>Try creating a roadmap item to test if the issue is in data retrieval or submission.</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="test error">';
                    echo '<span class="icon bad">✗</span> <strong>Project CWAYS not found</strong>';
                    echo '<p>Check that the project exists: <code>/projects</code></p>';
                    echo '</div>';
                }
            } catch (\Exception $e) {
                echo '<div class="test error">';
                echo '<span class="icon bad">✗</span> <strong>Error checking project:</strong><br>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Step 4: Test Service Methods -->
        <div class="section">
            <h2>Step 4: Test RoadmapService Methods</h2>
            <?php
            try {
                $roadmapService = new RoadmapService();
                $projectService = new ProjectService();
                
                // Get project
                $project = $projectService->getProjectByKey('CWAYS');
                if ($project) {
                    $projectId = $project['id'];
                    
                    // Test getProjectRoadmap
                    $items = $roadmapService->getProjectRoadmap($projectId);
                    echo '<div class="test">';
                    echo '<strong>getProjectRoadmap() returned: ' . count($items) . ' items</strong>';
                    if (!empty($items)) {
                        echo '<pre>' . htmlspecialchars(json_encode($items[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
                    }
                    echo '</div>';
                    
                    // Test getRoadmapSummary
                    $summary = $roadmapService->getRoadmapSummary($projectId);
                    echo '<div class="test">';
                    echo '<strong>getRoadmapSummary() returned:</strong>';
                    echo '<pre>' . htmlspecialchars(json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
                    echo '</div>';
                    
                    // Test getTimelineRange
                    $timeline = $roadmapService->getTimelineRange($projectId);
                    echo '<div class="test">';
                    echo '<strong>getTimelineRange() returned:</strong>';
                    echo '<pre>' . htmlspecialchars(json_encode($timeline, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
                    echo '</div>';
                }
            } catch (\Exception $e) {
                echo '<div class="test error">';
                echo '<span class="icon bad">✗</span> <strong>Error testing service:</strong><br>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Step 5: Test Item Creation -->
        <div class="section">
            <h2>Step 5: Try Creating a Test Item</h2>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_create'])) {
                try {
                    $roadmapService = new RoadmapService();
                    $projectService = new ProjectService();
                    
                    $project = $projectService->getProjectByKey('CWAYS');
                    if ($project) {
                        $item = $roadmapService->createRoadmapItem(
                            $project['id'],
                            [
                                'title' => 'Test Roadmap Item - ' . date('Y-m-d H:i:s'),
                                'description' => 'Testing roadmap creation',
                                'type' => 'feature',
                                'start_date' => date('Y-m-d'),
                                'end_date' => date('Y-m-d', strtotime('+7 days')),
                                'status' => 'planned',
                                'priority' => 'medium',
                                'progress' => 0,
                                'color' => '#8b1956'
                            ],
                            $_SESSION['user_id'] ?? 1  // Use logged-in user or default to 1
                        );
                        
                        echo '<div class="test success">';
                        echo '<span class="icon good">✓</span> <strong>Successfully created roadmap item!</strong>';
                        echo '<p>Item ID: ' . intval($item['id']) . '</p>';
                        echo '<p><a href="' . url('/projects/CWAYS/roadmap') . '">View Roadmap</a></p>';
                        echo '</div>';
                    }
                } catch (\Exception $e) {
                    echo '<div class="test error">';
                    echo '<span class="icon bad">✗</span> <strong>Error creating item:</strong><br>';
                    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                    echo '</div>';
                }
            }
            ?>
            
            <form method="POST">
                <input type="hidden" name="test_create" value="1">
                <button type="submit" style="background: #8b1956; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                    Create Test Item
                </button>
            </form>
        </div>

        <!-- Summary -->
        <div class="section" style="background: #e8f5e9; border-left-color: #4caf50;">
            <h2>Summary & Next Steps</h2>
            <div class="check">
                <strong>Most likely issue:</strong> The <code>roadmap_items</code> table migration hasn't been applied yet.
            </div>
            <div class="check">
                <strong>Solution:</strong> Run the migration to create the table.
            </div>
            <div class="check">
                <strong>Alternative:</strong> If data isn't showing after creation, it's a data retrieval issue in <code>RoadmapService::getProjectRoadmap()</code>
            </div>
        </div>
    </div>
</body>
</html>
