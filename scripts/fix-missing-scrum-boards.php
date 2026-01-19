<?php
/**
 * CRITICAL MAINTENANCE SCRIPT
 * Fixes missing Scrum boards for existing projects
 * 
 * Ensures ALL projects have at least one Scrum board for consistent backlog routing
 * Real Jira behavior: Every project should have a default board
 */

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/autoload.php';

use App\Core\Database;

$db = new Database();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   FIXING MISSING SCRUM BOARDS FOR EXISTING PROJECTS        ║\n";
echo "║   This ensures consistent backlog routing for all projects ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

try {
    // Step 1: Find all projects WITHOUT a Scrum board
    echo "Step 1️⃣ : Finding projects without Scrum boards...\n";
    
    $projectsWithoutBoards = $db->select("
        SELECT p.id, p.`key`, p.name, p.created_by
        FROM projects p
        WHERE NOT EXISTS (
            SELECT 1 FROM boards b 
            WHERE b.project_id = p.id AND b.type = 'scrum'
        )
        ORDER BY p.id
    ");

    if (empty($projectsWithoutBoards)) {
        echo "✅ All projects already have Scrum boards!\n\n";
        exit(0);
    }

    echo "   Found " . count($projectsWithoutBoards) . " projects without Scrum boards:\n";
    foreach ($projectsWithoutBoards as $project) {
        echo "     • {$project['key']} ({$project['name']}) - ID: {$project['id']}\n";
    }
    echo "\n";

    // Step 2: Create boards for projects
    echo "Step 2️⃣ : Creating default Scrum boards...\n";

    $boardsCreated = 0;
    $columnCreated = 0;

    foreach ($projectsWithoutBoards as $project) {
        try {
            $projectId = (int) $project['id'];
            $projectName = $project['name'];
            $userId = (int) $project['created_by'] ?? 1; // Fallback to user 1 if no creator

            // Create Scrum board
            $boardId = Database::insert('boards', [
                'project_id' => $projectId,
                'name' => "{$projectName} Scrum Board",
                'type' => 'scrum',
                'filter_jql' => null,
                'is_private' => 0,
                'owner_id' => $userId,
            ]);

            echo "   ✓ Created board for {$project['key']} (Board ID: {$boardId})\n";
            $boardsCreated++;

            // Create default columns: To Do, In Progress, Done
            $columns = [
                ['name' => 'To Do', 'sequence' => 1],
                ['name' => 'In Progress', 'sequence' => 2],
                ['name' => 'Done', 'sequence' => 3],
            ];

            foreach ($columns as $column) {
                Database::insert('board_columns', [
                    'board_id' => $boardId,
                    'name' => $column['name'],
                    'sequence' => $column['sequence'],
                ]);
                $columnCreated++;
            }

        } catch (\Exception $e) {
            echo "   ✗ Failed to create board for {$project['key']}: " . $e->getMessage() . "\n";
        }
    }

    echo "\n";
    echo "Step 3️⃣ : Verification...\n";

    // Verify: Check again for projects without boards
    $stillMissing = $db->select("
        SELECT p.id, p.`key`, p.name
        FROM projects p
        WHERE NOT EXISTS (
            SELECT 1 FROM boards b 
            WHERE b.project_id = p.id AND b.type = 'scrum'
        )
    ");

    if (empty($stillMissing)) {
        echo "   ✅ All projects now have Scrum boards!\n\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║                    SUCCESS SUMMARY                         ║\n";
        echo "╠════════════════════════════════════════════════════════════╣\n";
        echo "║  Boards Created: {$boardsCreated}\n";
        echo "║  Columns Created: {$columnCreated}\n";
        echo "║  Status: ✅ ALL PROJECTS FIXED\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";

        echo "🎯 NEXT STEPS:\n";
        echo "   1. Clear browser cache (CTRL+SHIFT+DEL)\n";
        echo "   2. Hard refresh page (CTRL+F5)\n";
        echo "   3. Navigate to any project backlog\n";
        echo "   4. Should now redirect to: /boards/{id}/backlog\n\n";

        exit(0);
    } else {
        echo "   ✗ " . count($stillMissing) . " projects still missing boards:\n";
        foreach ($stillMissing as $project) {
            echo "     • {$project['key']} - ID: {$project['id']}\n";
        }
        echo "\n❌ MIGRATION FAILED\n\n";
        exit(1);
    }

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
