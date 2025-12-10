<?php
/**
 * Check workflow transitions in database
 */
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "=== WORKFLOW TRANSITIONS CHECK ===\n\n";

// 1. Get statuses
echo "1. Available Statuses:\n";
$statuses = Database::select("SELECT id, name FROM statuses ORDER BY id");
$statusMap = [];
foreach ($statuses as $status) {
    $statusMap[$status['id']] = $status['name'];
    echo "   {$status['id']}: {$status['name']}\n";
}

// 2. Get workflows
echo "\n2. Workflows:\n";
$workflows = Database::select("SELECT id, name, is_default FROM workflows");
foreach ($workflows as $workflow) {
    $marker = $workflow['is_default'] ? ' (DEFAULT)' : '';
    echo "   {$workflow['id']}: {$workflow['name']}$marker\n";
}

// 3. Get transitions
echo "\n3. Configured Transitions:\n";
$transitions = Database::select("
    SELECT wt.id, wt.name, wt.from_status_id, wt.to_status_id, w.name as workflow_name
    FROM workflow_transitions wt
    JOIN workflows w ON wt.workflow_id = w.id
    ORDER BY wt.from_status_id, wt.to_status_id
");

if (count($transitions) == 0) {
    echo "   ✗ NO TRANSITIONS CONFIGURED\n";
    echo "   ℹ Run: php scripts/populate-workflow-transitions.php\n";
} else {
    echo "   Total: " . count($transitions) . " transitions\n\n";
    foreach ($transitions as $trans) {
        $from = $trans['from_status_id'] ? $statusMap[$trans['from_status_id']] : 'ANY';
        $to = $statusMap[$trans['to_status_id']];
        echo "   {$from} → {$to} ({$trans['workflow_name']})\n";
    }
}

// 4. Test specific transition
echo "\n4. Testing Transitions:\n";
if (count($statuses) >= 2) {
    $status1 = $statuses[0]['id'];
    $status2 = $statuses[1]['id'];
    
    $allowed = Database::selectOne("
        SELECT 1 FROM workflow_transitions wt
        JOIN workflows w ON wt.workflow_id = w.id
        WHERE w.is_default = 1 
        AND (wt.from_status_id = ? OR wt.from_status_id IS NULL)
        AND wt.to_status_id = ?
    ", [$status1, $status2]);
    
    $from = $statusMap[$status1];
    $to = $statusMap[$status2];
    $result = $allowed ? '✓ ALLOWED' : '✗ NOT ALLOWED';
    echo "   {$from} → {$to}: $result\n";
}

// 5. Count transitions per workflow
echo "\n5. Transitions by Workflow:\n";
$byWorkflow = Database::select("
    SELECT w.name, COUNT(*) as count
    FROM workflow_transitions wt
    JOIN workflows w ON wt.workflow_id = w.id
    GROUP BY wt.workflow_id, w.name
");

if (count($byWorkflow) == 0) {
    echo "   No workflows with transitions\n";
} else {
    foreach ($byWorkflow as $row) {
        echo "   {$row['name']}: {$row['count']} transitions\n";
    }
}

// 6. Solution
echo "\n6. Solution:\n";
if (count($transitions) == 0) {
    echo "   Run this command:\n";
    echo "   php scripts/populate-workflow-transitions.php\n";
    echo "\n   This will create standard Jira-like transitions.\n";
} else {
    echo "   Transitions exist but not all paths are configured.\n";
    echo "   Edit manually or run:\n";
    echo "   php scripts/populate-workflow-transitions.php\n";
}
