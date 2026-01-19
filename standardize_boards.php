<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$logFile = __DIR__ . '/standardize_log.txt';
file_put_contents($logFile, "Starting script...\n");

function logMsg($msg)
{
    global $logFile;
    file_put_contents($logFile, $msg . "\n", FILE_APPEND);
}

try {
    require_once __DIR__ . '/bootstrap/app.php';
    logMsg("Bootstrap loaded.");
} catch (\Throwable $e) {
    logMsg("Bootstrap failed: " . $e->getMessage());
    exit(1);
}

use App\Core\Database;

try {
    // 1. Ensure 'Reopened' status exists
    $reopened = Database::selectOne("SELECT * FROM statuses WHERE name = ?", ['Reopened']);
    if (!$reopened) {
        logMsg("Creating 'Reopened' status...");
        Database::insert('statuses', [
            'name' => 'Reopened',
            'description' => 'Issue reopened',
            'category' => 'todo',
            'color' => '#AB47BC',
            'sort_order' => 8
        ]);
    } else {
        logMsg("'Reopened' status already exists.");
    }

    // 2. Define Standard Columns and their Status mappings
    $standardColumns = [
        'Open' => ['names' => ['Open'], 'category' => 'todo'],
        'To Do' => ['names' => ['To Do'], 'category' => 'todo'],
        'In Progress' => ['names' => ['In Progress'], 'category' => 'in_progress'],
        'In Review' => ['names' => ['In Review'], 'category' => 'in_progress'],
        'Testing' => ['names' => ['Testing'], 'category' => 'in_progress'],
        'Done' => ['names' => ['Done'], 'category' => 'done'],
        'Closed' => ['names' => ['Closed'], 'category' => 'done'],
        'Reopened' => ['names' => ['Reopened'], 'category' => 'todo']
    ];

    // 3. Fetch Status IDs
    $statusMap = []; // Name -> ID
    $allStatuses = Database::select("SELECT * FROM statuses");
    foreach ($allStatuses as $s) {
        $statusMap[$s['name']] = $s['id'];
    }

    // Verify we have all needed IDs
    foreach ($standardColumns as $colName => $config) {
        foreach ($config['names'] as $statusName) {
            if (!isset($statusMap[$statusName])) {
                logMsg("Error: Status '$statusName' not found in database even after check.");
                exit(1);
            }
        }
    }

    // 4. Update All Boards
    $boards = Database::select("SELECT * FROM boards");

    logMsg("Updating " . count($boards) . " boards...");

    foreach ($boards as $board) {
        logMsg("Processing board: {$board['name']} (ID: {$board['id']})...");

        // Delete existing columns
        Database::query("DELETE FROM board_columns WHERE board_id = ?", [$board['id']]);

        // Insert new columns
        $order = 0;
        foreach ($standardColumns as $colName => $config) {
            $statusIds = [];
            foreach ($config['names'] as $statusName) {
                $statusIds[] = (int) $statusMap[$statusName];
            }
            $statusIdsJson = json_encode($statusIds);

            Database::insert('board_columns', [
                'board_id' => $board['id'],
                'name' => $colName,
                'status_ids' => $statusIdsJson,
                'sort_order' => $order++
            ]);
        }
    }

    logMsg("All boards updated successfully.");

} catch (\Throwable $e) {
    logMsg("Fatal Error: " . $e->getMessage());
}
