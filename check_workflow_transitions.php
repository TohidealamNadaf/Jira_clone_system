<?php
declare(strict_types=1);

require_once 'bootstrap/autoload.php';

use App\Core\Database;

echo "=== Checking Workflow Transitions ===\n\n";

// Check if there are any workflow transitions
$count = Database::selectOne("SELECT COUNT(*) as count FROM workflow_transitions");
echo "Total workflow transitions: " . $count['count'] . "\n";

if ($count['count'] == 0) {
    echo "\n❌ NO WORKFLOW TRANSITIONS FOUND!\n";
    echo "This is why the board drag-and-drop is failing.\n";
    echo "\nFix: Run this SQL to populate transitions:\n\n";
    
    $sql = <<<'SQL'
INSERT INTO `workflow_transitions` (`workflow_id`, `name`, `from_status_id`, `to_status_id`) VALUES
(1, 'Open → To Do', 1, 2),
(1, 'To Do → In Progress', 2, 3),
(1, 'In Progress → In Review', 3, 4),
(1, 'In Review → Testing', 4, 5),
(1, 'Testing → Done', 5, 6),
(1, 'Done → Closed', 6, 7),
(1, 'In Progress → To Do', 3, 2),
(1, 'In Review → In Progress', 4, 3),
(1, 'Testing → In Progress', 5, 3),
(1, 'Done → In Progress', 6, 3),
(1, 'Closed → To Do', 7, 2),
(1, 'Open → Closed', 1, 7),
(1, 'To Do → Open', 2, 1);
SQL;

    echo $sql . "\n";
    
} else {
    echo "\n✅ Workflow transitions exist.\n";
    
    // Show sample transitions
    $transitions = Database::select(
        "SELECT wt.id, wt.name, sf.name as from_status, st.name as to_status, w.name as workflow
         FROM workflow_transitions wt
         JOIN workflows w ON wt.workflow_id = w.id
         JOIN statuses sf ON wt.from_status_id = sf.id
         JOIN statuses st ON wt.to_status_id = st.id
         WHERE w.is_default = 1
         LIMIT 10"
    );
    
    echo "\nSample transitions (Default Workflow):\n";
    foreach ($transitions as $t) {
        echo "  - {$t['from_status']} → {$t['to_status']}\n";
    }
}

// Check workflows
echo "\n\n=== Checking Workflows ===\n";
$workflows = Database::select(
    "SELECT id, name, is_default FROM workflows ORDER BY is_default DESC"
);

foreach ($workflows as $w) {
    $transitionCount = Database::selectOne(
        "SELECT COUNT(*) as count FROM workflow_transitions WHERE workflow_id = ?",
        [$w['id']]
    );
    $default = $w['is_default'] ? '(DEFAULT)' : '';
    echo "  {$w['name']} $default: {$transitionCount['count']} transitions\n";
}
