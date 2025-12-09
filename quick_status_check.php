<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "=== QUICK STATUS CHECK ===\n\n";

// Check statuses
$statuses = Database::select("SELECT id, name FROM statuses");
echo "Statuses (" . count($statuses) . "):\n";
foreach ($statuses as $s) {
    echo "  [{$s['id']}] {$s['name']}\n";
}

// Check issue types
$types = Database::select("SELECT id, name FROM issue_types");
echo "\nIssue Types (" . count($types) . "):\n";
foreach ($types as $t) {
    echo "  [{$t['id']}] {$t['name']}\n";
}

// Check priorities
$priorities = Database::select("SELECT id, name FROM issue_priorities");
echo "\nPriorities (" . count($priorities) . "):\n";
foreach ($priorities as $p) {
    echo "  [{$p['id']}] {$p['name']}\n";
}

// Check workflows
$workflows = Database::select("SELECT id, name, is_default FROM workflows");
echo "\nWorkflows (" . count($workflows) . "):\n";
foreach ($workflows as $w) {
    $default = $w['is_default'] ? ' (DEFAULT)' : '';
    echo "  [{$w['id']}] {$w['name']}{$default}\n";
}

echo "\n";
?>
