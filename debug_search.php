<?php
declare(strict_types=1);

require_once 'bootstrap/autoload.php';
$app = require 'bootstrap/app.php';

use App\Core\Database;
use App\Core\Session;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = Database::class;

echo "=== SEARCH DEBUGGING ===\n\n";

// Check total issues
$totalIssues = $db::selectValue('SELECT COUNT(*) FROM issues');
echo "Total Issues in DB: $totalIssues\n";

// Check if there are any assigned issues
$assignedIssues = $db::selectValue('SELECT COUNT(*) FROM issues WHERE assignee_id IS NOT NULL');
echo "Assigned Issues: $assignedIssues\n\n";

// Check all users
$users = $db::select('SELECT id, display_name, email FROM users LIMIT 10');
echo "Users in System:\n";
foreach ($users as $user) {
    $count = $db::selectValue('SELECT COUNT(*) FROM issues WHERE assignee_id = ?', [$user['id']]);
    echo "  - ID {$user['id']}: {$user['display_name']} ({$user['email']}) - Issues: $count\n";
}

echo "\n=== ISSUE DETAILS ===\n";
$issues = $db::select('SELECT id, issue_key, summary, assignee_id FROM issues LIMIT 5');
foreach ($issues as $issue) {
    echo "Issue {$issue['issue_key']}: assignee_id={$issue['assignee_id']}\n";
}
