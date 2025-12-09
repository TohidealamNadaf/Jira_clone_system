<?php
declare(strict_types=1);

require_once 'bootstrap/autoload.php';
$app = require 'bootstrap/app.php';

use App\Core\Database;

$db = Database::class;

// Get admin user
$adminUser = $db::selectOne('SELECT id FROM users WHERE email = "admin@example.com"');

if (!$adminUser) {
    echo "Admin user not found\n";
    exit;
}

$adminId = $adminUser['id'];
echo "Admin ID: $adminId\n\n";

// Get first 5 unassigned issues
$unassignedIssues = $db::select(
    'SELECT id, issue_key FROM issues WHERE assignee_id IS NULL LIMIT 5'
);

if (empty($unassignedIssues)) {
    echo "No unassigned issues found\n";
    exit;
}

echo "Assigning " . count($unassignedIssues) . " issues to admin user:\n";

foreach ($unassignedIssues as $issue) {
    $db::update(
        'issues',
        ['assignee_id' => $adminId, 'updated_at' => date('Y-m-d H:i:s')],
        'id = ?',
        [$issue['id']]
    );
    echo "  - {$issue['issue_key']} assigned\n";
}

echo "\nDone! Assigned " . count($unassignedIssues) . " issues to admin.\n";
