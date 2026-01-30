<?php
require_once __DIR__ . '/../bootstrap/app.php';
use App\Core\Database;

$transitions = Database::select("SELECT wt.*, s_from.name as from_name, s_to.name as to_name 
                                FROM workflow_transitions wt 
                                LEFT JOIN statuses s_from ON wt.from_status_id = s_from.id
                                LEFT JOIN statuses s_to ON wt.to_status_id = s_to.id
                                JOIN workflows w ON wt.workflow_id = w.id
                                WHERE w.is_default = 1");

echo "Count: " . count($transitions) . "\n";
foreach ($transitions as $t) {
    echo ($t['from_name'] ?? 'NULL') . " -> " . $t['to_name'] . "\n";
}
