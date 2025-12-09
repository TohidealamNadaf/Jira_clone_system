<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "=== WORKFLOW SETUP CHECK ===\n\n";

// Check default workflow
$workflow = Database::selectOne("SELECT id, name FROM workflows WHERE is_default = 1");
if (!$workflow) {
    echo "❌ ERROR: No default workflow found!\n";
    exit(1);
}

echo "✓ Default Workflow: " . $workflow['name'] . " (ID: {$workflow['id']})\n\n";

// Check workflow statuses
$statuses = Database::select(
    "SELECT ws.id, s.name, ws.is_initial 
     FROM workflow_statuses ws 
     JOIN statuses s ON ws.status_id = s.id 
     WHERE ws.workflow_id = ? 
     ORDER BY ws.id",
    [$workflow['id']]
);

echo "Workflow Statuses (" . count($statuses) . "):\n";
foreach ($statuses as $s) {
    $initial = $s['is_initial'] ? ' ⭐ INITIAL' : '';
    echo "  - {$s['name']}{$initial}\n";
}

// Check initial status specifically
$initialStatus = Database::selectOne(
    "SELECT s.id, s.name FROM statuses s
     JOIN workflow_statuses ws ON s.id = ws.status_id
     JOIN workflows w ON ws.workflow_id = w.id
     WHERE w.is_default = 1 AND ws.is_initial = 1
     LIMIT 1"
);

if (!$initialStatus) {
    echo "\n❌ ERROR: No initial status set for default workflow!\n";
    echo "   This will cause issue creation to fail.\n";
} else {
    echo "\n✓ Initial Status: " . $initialStatus['name'] . " (ID: {$initialStatus['id']})\n";
}

// Check workflow transitions
$transitions = Database::select(
    "SELECT wt.id, wt.name, s_from.name as from_status, s_to.name as to_status
     FROM workflow_transitions wt
     LEFT JOIN statuses s_from ON wt.from_status_id = s_from.id
     JOIN statuses s_to ON wt.to_status_id = s_to.id
     WHERE wt.workflow_id = ?
     ORDER BY wt.id",
    [$workflow['id']]
);

echo "\nWorkflow Transitions (" . count($transitions) . "):\n";
foreach ($transitions as $t) {
    $from = $t['from_status'] ?? 'ANY';
    echo "  - {$t['name']}: {$from} → {$t['to_status']}\n";
}

echo "\n";
?>
