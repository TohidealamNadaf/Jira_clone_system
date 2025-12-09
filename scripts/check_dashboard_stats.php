<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

echo "Dashboard Stats Debug\n";
echo "=====================\n\n";

$userId = 1; // admin user

echo "User ID: $userId\n\n";

$assigned = Database::selectValue(
    "SELECT COUNT(*) FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND s.category != 'done'",
    [$userId]
);
echo "Assigned to you (not done): $assigned\n";

$reported = Database::selectValue(
    "SELECT COUNT(*) FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.reporter_id = ? AND s.category != 'done'",
    [$userId]
);
echo "Reported by you (not done): $reported\n";

$dueSoon = Database::selectValue(
    "SELECT COUNT(*) FROM issues
     WHERE assignee_id = ? AND due_date IS NOT NULL 
     AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)",
    [$userId]
);
echo "Due this week: $dueSoon\n";

$overdue = Database::selectValue(
    "SELECT COUNT(*) FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND i.due_date < CURDATE() AND s.category != 'done'",
    [$userId]
);
echo "Overdue: $overdue\n";

echo "\n--- Sample issues ---\n";
$issues = Database::select(
    "SELECT id, issue_key, assignee_id, reporter_id, due_date FROM issues ORDER BY id LIMIT 10"
);
foreach ($issues as $i) {
    echo "#{$i['id']} {$i['issue_key']} - Assignee: {$i['assignee_id']}, Reporter: {$i['reporter_id']}, Due: " . ($i['due_date'] ?? 'null') . "\n";
}
