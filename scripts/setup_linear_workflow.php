<?php
require_once __DIR__ . '/../bootstrap/app.php';
use App\Core\Database;

echo "=== Setup Linear Workflow ===\n";

try {
    // 1. Get Default Workflow
    $workflowId = Database::selectValue("SELECT id FROM workflows WHERE is_default = 1");
    if (!$workflowId) {
        die("❌ Default workflow not found.\n");
    }
    echo "Using Workflow ID: $workflowId\n";

    // 2. Clear existing transitions
    Database::delete('workflow_transitions', 'workflow_id = ?', [$workflowId]);
    echo "Cleared existing transitions.\n";

    // 3. Define Logic Flow
    // Status Name => [Next Statuses]
    $workflowMap = [
        'Open' => ['To Do'],
        'To Do' => ['In Progress'],
        'In Progress' => ['In Review', 'To Do'], // Allow moving back to To Do
        'In Review' => ['Testing', 'In Progress'], // Allow moving back
        'Testing' => ['Done', 'In Progress'],
        'Done' => ['Closed', 'Reopened'],
        'Closed' => ['Reopened'],
        'Reopened' => ['To Do', 'Closed']
    ];

    $count = 0;
    foreach ($workflowMap as $fromName => $toNames) {
        $fromId = Database::selectValue("SELECT id FROM statuses WHERE name = ?", [$fromName]);

        if (!$fromId) {
            echo "Warning: Source status '$fromName' not found.\n";
            continue;
        }

        foreach ($toNames as $toName) {
            $toId = Database::selectValue("SELECT id FROM statuses WHERE name = ?", [$toName]);

            if ($toId) {
                Database::insert('workflow_transitions', [
                    'workflow_id' => $workflowId,
                    'name' => $toName,
                    'from_status_id' => $fromId,
                    'to_status_id' => $toId
                ]);
                echo "  - Added: $fromName -> $toName\n";
                $count++;
            } else {
                echo "Warning: Target status '$toName' not found.\n";
            }
        }
    }

    echo "✅ Successfully inserted $count linear transitions.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
