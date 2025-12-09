<?php
declare(strict_types=1);

require_once 'bootstrap/autoload.php';
$app = require 'bootstrap/app.php';

use App\Core\Database;
use App\Core\Session;

// Simulate session
session_start();
$_SESSION['user'] = [
    'id' => 1,
    'email' => 'admin@example.com',
    'display_name' => 'System Administrator',
];

$db = Database::class;

echo "=== SEARCH FILTER TEST ===\n\n";

// Get current user ID (simulating what the search controller does)
$currentUserId = $_SESSION['user']['id'] ?? null;
echo "Current User ID: $currentUserId\n";

// Test 1: Check if user has assigned issues
$assignedCount = $db::selectValue(
    'SELECT COUNT(*) FROM issues WHERE assignee_id = ?',
    [$currentUserId]
);
echo "Issues assigned to user: $assignedCount\n\n";

// Test 2: Run the exact query the search would execute
$conditions = ["i.assignee_id = ?"];
$params = [$currentUserId];
$whereClause = implode(' AND ', $conditions);

$countSql = "SELECT COUNT(*) FROM issues i
             JOIN projects p ON i.project_id = p.id
             JOIN statuses s ON i.status_id = s.id
             JOIN issue_types t ON i.issue_type_id = t.id
             LEFT JOIN issue_priorities ip ON i.priority_id = ip.id
             LEFT JOIN users u ON i.assignee_id = u.id
             WHERE {$whereClause}";

$total = (int) $db::selectValue($countSql, $params);
echo "Search query result count: $total\n\n";

// Test 3: Show actual issues assigned to user
if ($assignedCount > 0) {
    echo "Issues assigned to user:\n";
    $issues = $db::select(
        'SELECT i.id, i.issue_key, i.summary, i.assignee_id, u.display_name as assignee_name 
         FROM issues i
         LEFT JOIN users u ON i.assignee_id = u.id
         WHERE i.assignee_id = ?
         LIMIT 10',
        [$currentUserId]
    );
    foreach ($issues as $issue) {
        echo "  - {$issue['issue_key']}: {$issue['summary']} (assignee_id={$issue['assignee_id']}, name={$issue['assignee_name']})\n";
    }
} else {
    echo "No issues assigned to this user.\n";
    echo "\nAll issues in system:\n";
    $allIssues = $db::select('SELECT id, issue_key, summary, assignee_id FROM issues LIMIT 10');
    foreach ($allIssues as $issue) {
        echo "  - {$issue['issue_key']}: {$issue['summary']} (assignee_id={$issue['assignee_id']})\n";
    }
}
