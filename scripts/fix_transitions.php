<?php
require_once __DIR__ . '/../bootstrap/app.php';
use App\Core\Database;

echo "=== Fix Workflow Transitions ===\n";

try {
    // 1. Get or Create Default Workflow
    $workflowId = Database::selectValue("SELECT id FROM workflows WHERE is_default = 1");
    if (!$workflowId) {
        echo "Creating default workflow...\n";
        $workflowId = Database::insert('workflows', [
            'name' => 'Default Workflow',
            'description' => 'Standard workflow for all projects',
            'is_default' => 1
        ]);
    }
    echo "Using Workflow ID: $workflowId\n";

    // 2. Clear existing transitions for this workflow
    Database::delete('workflow_transitions', 'workflow_id = ?', [$workflowId]);
    echo "Cleared existing transitions.\n";

    // 3. Define Standard Statuses
    $standardStatuses = [
        'Open',
        'To Do',
        'In Progress',
        'In Review',
        'Testing',
        'Done',
        'Closed',
        'Reopened'
    ];

    // 4. Create Global Transitions (From ANY -> To Status)
    $count = 0;
    foreach ($standardStatuses as $statusName) {
        $statusId = Database::selectValue("SELECT id FROM statuses WHERE name = ?", [$statusName]);

        if ($statusId) {
            // Insert global transition (from_status_id = NULL)
            Database::insert('workflow_transitions', [
                'workflow_id' => $workflowId,
                'name' => $statusName, // Transition name matches status name
                'from_status_id' => null, // Global
                'to_status_id' => $statusId
            ]);
            $count++;
        } else {
            echo "Warning: Status '$statusName' not found.\n";
        }
    }

    echo "✅ Successfully inserted $count global transitions.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
