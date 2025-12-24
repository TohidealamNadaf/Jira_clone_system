<?php
require_once 'bootstrap/app.php';
use App\Core\Database;

$workflows = Database::select('SELECT id, name, is_default FROM workflows');
echo 'Workflows: ' . count($workflows) . PHP_EOL;
foreach($workflows as $wf) {
    echo '  ' . $wf['id'] . ': ' . $wf['name'] . ' (default: ' . $wf['is_default'] . ')' . PHP_EOL;
}

$workflowStatuses = Database::select('SELECT * FROM workflow_statuses');
echo 'Workflow statuses: ' . count($workflowStatuses) . PHP_EOL;
foreach($workflowStatuses as $ws) {
    echo '  Workflow: ' . $ws['workflow_id'] . ', Status: ' . $ws['status_id'] . ', Initial: ' . $ws['is_initial'] . PHP_EOL;
}

$statuses = Database::select('SELECT id, name FROM statuses ORDER BY id');
echo 'Statuses: ' . count($statuses) . PHP_EOL;
foreach($statuses as $s) {
    echo '  ' . $s['id'] . ': ' . $s['name'] . PHP_EOL;
}
?>





