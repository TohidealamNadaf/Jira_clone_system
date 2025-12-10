<?php
/**
 * Populate workflow transitions - IMMEDIATE FIX
 * Run this once to populate all standard Jira-like transitions
 */
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "=== POPULATING WORKFLOW TRANSITIONS ===\n\n";

// 1. Get default workflow
$workflow = Database::selectOne("SELECT id FROM workflows WHERE is_default = 1");
if (!$workflow) {
    echo "✗ ERROR: No default workflow found\n";
    exit(1);
}
$workflowId = $workflow['id'];
echo "✓ Found default workflow (ID: $workflowId)\n";

// 2. Get all statuses
$statuses = Database::select("SELECT id, name FROM statuses ORDER BY id");
echo "✓ Found " . count($statuses) . " statuses\n";

if (count($statuses) < 2) {
    echo "✗ ERROR: Need at least 2 statuses\n";
    exit(1);
}

// 3. Create a map of status IDs for reference
echo "\n   Status IDs:\n";
$statusMap = [];
foreach ($statuses as $status) {
    $statusMap[$status['name']] = $status['id'];
    echo "   - {$status['name']}: {$status['id']}\n";
}

// 4. Clear existing transitions
$existingCount = Database::selectOne(
    "SELECT COUNT(*) as count FROM workflow_transitions WHERE workflow_id = ?",
    [$workflowId]
);

if ($existingCount['count'] > 0) {
    echo "\n⚠ Clearing existing transitions (" . $existingCount['count'] . ")...\n";
    Database::delete('workflow_transitions', 'workflow_id = ?', [$workflowId]);
}

// 5. Insert standard transitions - Allow transitions FROM ANY status TO any other status
// This is the most permissive approach - suitable for setup phase
echo "\n📝 Creating transitions...\n";

$transitions = [];
foreach ($statuses as $fromStatus) {
    foreach ($statuses as $toStatus) {
        // Don't create transition to self
        if ($fromStatus['id'] === $toStatus['id']) {
            continue;
        }

        $transitions[] = [
            'workflow_id' => $workflowId,
            'name' => "{$fromStatus['name']} → {$toStatus['name']}",
            'from_status_id' => $fromStatus['id'],
            'to_status_id' => $toStatus['id'],
        ];
    }
}

echo "   Inserting " . count($transitions) . " transition paths...\n";

foreach ($transitions as $trans) {
    Database::insert('workflow_transitions', $trans);
}

// 6. Verify
$finalCount = Database::selectOne(
    "SELECT COUNT(*) as count FROM workflow_transitions WHERE workflow_id = ?",
    [$workflowId]
);

echo "\n✓ SUCCESS: Created " . $finalCount['count'] . " transitions\n";
echo "\n   Workflow transitions are now configured!\n";
echo "   You can now drag and drop issues between any statuses.\n";

// 7. Test a transition
echo "\n📊 Test Transition:\n";
if (count($statuses) >= 2) {
    $testTrans = Database::selectOne(
        "SELECT 1 FROM workflow_transitions 
         WHERE workflow_id = ? 
         AND from_status_id = ? 
         AND to_status_id = ?",
        [$workflowId, $statuses[0]['id'], $statuses[1]['id']]
    );

    $from = $statuses[0]['name'];
    $to = $statuses[1]['name'];
    
    if ($testTrans) {
        echo "   ✓ Transition {$from} → {$to}: ALLOWED\n";
    } else {
        echo "   ✗ Transition {$from} → {$to}: NOT FOUND\n";
    }
}

echo "\n=== COMPLETE ===\n";
echo "Try dragging an issue on the board now!\n";
