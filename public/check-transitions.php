<?php
declare(strict_types=1);

require_once '../bootstrap/autoload.php';

use App\Core\Database;

echo "=== Checking Workflow Transitions ===\n\n";

// Check if there are any workflow transitions
$count = Database::selectOne("SELECT COUNT(*) as count FROM workflow_transitions");
echo "Total workflow transitions: " . $count['count'] . "\n";

if ($count['count'] == 0) {
    echo "\n❌ NO WORKFLOW TRANSITIONS FOUND!\n";
    echo "This is why the board drag-and-drop is failing.\n";
} else {
    echo "\n✅ Workflow transitions exist.\n";
    
    // Show transitions for default workflow
    $transitions = Database::select(
        "SELECT wt.id, wt.name, sf.name as from_status, st.name as to_status, w.name as workflow
         FROM workflow_transitions wt
         JOIN workflows w ON wt.workflow_id = w.id
         LEFT JOIN statuses sf ON wt.from_status_id = sf.id
         JOIN statuses st ON wt.to_status_id = st.id
         WHERE w.is_default = 1
         LIMIT 15"
    );
    
    echo "\nTransitions in Default Workflow:\n";
    foreach ($transitions as $t) {
        $from = $t['from_status'] ?? 'ANY';
        echo "  - {$from} → {$t['to_status']}\n";
    }
}
