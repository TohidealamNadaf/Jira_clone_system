<?php
declare(strict_types=1);

require_once 'bootstrap/autoload.php';
$app = require 'bootstrap/app.php';

use App\Core\Database;

echo "=== Assigning Issues to Admin User ===\n\n";

// Update first 5 issues to be assigned to admin (user_id = 1)
$result = Database::update(
    'issues',
    [
        'assignee_id' => 1,
        'updated_at' => date('Y-m-d H:i:s')
    ],
    '1=1 LIMIT 5'
);

// Verify
$count = Database::selectValue('SELECT COUNT(*) FROM issues WHERE assignee_id = 1');
echo "✓ Successfully assigned issues to admin user\n";
echo "✓ Total issues assigned to admin (ID=1): $count\n\n";

// Show the issues
$issues = Database::select(
    'SELECT i.issue_key, i.summary, u.display_name as assignee_name 
     FROM issues i
     JOIN users u ON i.assignee_id = u.id
     WHERE i.assignee_id = 1
     LIMIT 5'
);

echo "Assigned Issues:\n";
foreach ($issues as $issue) {
    echo "  - {$issue['issue_key']}: {$issue['summary']} (assigned to {$issue['assignee_name']})\n";
}

echo "\nNow try the search: http://localhost:8080/jira_clone_system/public/search?assignee=currentUser()\n";
