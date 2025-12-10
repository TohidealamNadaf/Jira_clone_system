<?php
/**
 * Fix Board Drag-and-Drop: Populate Workflow Transitions
 * 
 * This script fixes the "This transition is not allowed" error by populating
 * the workflow_transitions table with valid status transitions.
 */

declare(strict_types=1);

require_once 'bootstrap/autoload.php';

use App\Core\Database;

echo "=== Fixing Board Drag-and-Drop: Populating Workflow Transitions ===\n\n";

// Get database connection
$pdo = Database::getConnection();

try {
    // First, check if transitions already exist
    $count = $pdo->query("SELECT COUNT(*) FROM workflow_transitions")->fetchColumn();
    
    if ($count > 0) {
        echo "✅ Workflow transitions already exist ({$count} rows).\n";
        echo "No action needed.\n";
        exit(0);
    }
    
    echo "No workflow transitions found. Populating now...\n\n";
    
    // Get the default workflow ID
    $workflow = Database::selectOne("SELECT id FROM workflows WHERE is_default = 1");
    if (!$workflow) {
        throw new Exception("No default workflow found. Please create one first.");
    }
    
    $workflowId = $workflow['id'];
    echo "Using default workflow ID: {$workflowId}\n";
    
    // Get all statuses
    $statuses = Database::select("SELECT id, name FROM statuses ORDER BY sort_order ASC");
    $statusMap = [];
    foreach ($statuses as $status) {
        $statusMap[$status['name']] = $status['id'];
        echo "  Status: {$status['name']} (ID: {$status['id']})\n";
    }
    
    echo "\nCreating workflow transitions...\n\n";
    
    // Define transitions: from_status => [to_statuses...]
    // NULL from_status means "from any status"
    $transitions = [
        // From Open (1)
        'Open' => ['To Do', 'Closed'],
        
        // From To Do (2)
        'To Do' => ['In Progress', 'Open'],
        
        // From In Progress (3)
        'In Progress' => ['In Review', 'Testing', 'To Do'],
        
        // From In Review (4)
        'In Review' => ['In Progress', 'Testing', 'To Do'],
        
        // From Testing (5)
        'Testing' => ['In Progress', 'Done', 'In Review'],
        
        // From Done (6)
        'Done' => ['Closed', 'In Progress'],
        
        // From Closed (7)
        'Closed' => ['To Do'],
    ];
    
    $inserted = 0;
    
    foreach ($transitions as $fromStatus => $toStatuses) {
        if (!isset($statusMap[$fromStatus])) {
            echo "  ⚠️ Warning: Status '{$fromStatus}' not found\n";
            continue;
        }
        
        $fromStatusId = $statusMap[$fromStatus];
        
        foreach ($toStatuses as $toStatus) {
            if (!isset($statusMap[$toStatus])) {
                echo "  ⚠️ Warning: Status '{$toStatus}' not found\n";
                continue;
            }
            
            $toStatusId = $statusMap[$toStatus];
            
            // Insert transition
            $stmt = $pdo->prepare("
                INSERT INTO workflow_transitions 
                (workflow_id, name, from_status_id, to_status_id) 
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $workflowId,
                "{$fromStatus} → {$toStatus}",
                $fromStatusId,
                $toStatusId
            ]);
            
            echo "  ✅ {$fromStatus} → {$toStatus}\n";
            $inserted++;
        }
    }
    
    echo "\n✅ Successfully inserted {$inserted} workflow transitions!\n";
    echo "\n🚀 Board drag-and-drop should now work correctly.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
