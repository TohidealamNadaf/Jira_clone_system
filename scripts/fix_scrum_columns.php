<?php
require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

// Increase time limit for migration
set_time_limit(300);

echo "=== Scrum Board Columns Migration ===\n\n";

try {
    // 1. Ensure all required statuses exist
    $standardColumns = [
        'Open' => 0,
        'To Do' => 1,
        'In Progress' => 2,
        'In Review' => 3,
        'Testing' => 4,
        'Done' => 5,
        'Closed' => 6,
        'Reopened' => 7
    ];

    // Status metadata (match standard categories)
    $statusMeta = [
        'Open' => ['category' => 'todo', 'color' => '#42526E'],
        'To Do' => ['category' => 'todo', 'color' => '#42526E'],
        'In Progress' => ['category' => 'in_progress', 'color' => '#0052CC'],
        'In Review' => ['category' => 'in_progress', 'color' => '#0052CC'],
        'Testing' => ['category' => 'in_progress', 'color' => '#0052CC'],
        'Done' => ['category' => 'done', 'color' => '#00875A'],
        'Closed' => ['category' => 'done', 'color' => '#00875A'],
        'Reopened' => ['category' => 'todo', 'color' => '#42526E']
    ];

    echo "Step 1: Verifying Statuses...\n";
    foreach ($standardColumns as $name => $order) {
        $exists = Database::selectOne("SELECT 1 FROM statuses WHERE name = ?", [$name]);

        if (!$exists) {
            echo "  - Creating missing status: $name\n";
            $meta = $statusMeta[$name] ?? ['category' => 'todo', 'color' => '#42526E'];
            Database::insert('statuses', [
                'name' => $name,
                'description' => "$name Status",
                'category' => $meta['category'],
                'color' => $meta['color'],
                'sort_order' => $order
            ]);
        }
    }
    echo "  ✅ All statuses verified.\n\n";

    // 2. Update Boards
    echo "Step 2: Updating Boards...\n";

    $boards = Database::select("SELECT id, name, project_id FROM boards");
    $updatedCount = 0;
    $skippedCount = 0;

    foreach ($boards as $board) {
        $boardId = $board['id'];
        $boardName = $board['name'];

        $columnCount = Database::selectValue("SELECT COUNT(*) FROM board_columns WHERE board_id = ?", [$boardId]);

        if ($columnCount == 8) {
            echo "  - Board '{$boardName}' (ID: $boardId) has 8 columns. Skipping.\n";
            $skippedCount++;
            continue;
        }

        echo "  - Updating '{$boardName}' (ID: $boardId) - Current columns: $columnCount\n";

        // Clean existing columns
        Database::delete('board_columns', 'board_id = ?', [$boardId]);

        // Insert standard columns
        foreach ($standardColumns as $name => $order) {
            $statusId = Database::selectValue("SELECT id FROM statuses WHERE name = ?", [$name]);

            if ($statusId) {
                Database::insert('board_columns', [
                    'board_id' => $boardId,
                    'name' => $name,
                    'status_ids' => json_encode([$statusId]),
                    'sort_order' => $order
                ]);
            }
        }
        $updatedCount++;
    }

    echo "\nSummary:\n";
    echo "  Updated: $updatedCount boards\n";
    echo "  Skipped: $skippedCount boards\n";
    echo "\n✅ Migration Complete.\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
