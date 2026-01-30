<?php
require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

// Increase time limit for migration
set_time_limit(300);

echo "=== Scrum Board Columns Migration ===\n\n";

try {
    // Database::init(); // Connection is lazy loaded


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

    // Status metadata (color match based on standard categories)
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

    // Get all boards
    $boards = Database::select("SELECT id, name, project_id FROM boards");
    $updatedCount = 0;
    $skippedCount = 0;

    foreach ($boards as $board) {
        $boardId = $board['id'];
        $boardName = $board['name'];

        // Check current column count
        $columnCount = Database::selectValue("SELECT COUNT(*) FROM board_columns WHERE board_id = ?", [$boardId]);

        // CWays MIS (ID 6) might already be correct, but let's standardize ALL to be safe
        // Unless it has significantly modified columns? 
        // Logic: If column count is NOT 8, or if it is 3 (the broken state), fix it.
        // Actually, let's enforce 8 columns for consistency as requested.

        if ($columnCount == 8) {
            // Check if they are the standard ones? or just skip?
            // Let's assume if 8, it's likely correct or manually customized. 
            // Better to only fix those with < 8 columns (likely the broken 3-column ones)
            // or explicitly broken logic.
            echo "  - Board '{$boardName}' (ID: $boardId) has 8 columns. Skipping (assumed correct).\n";
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
