<?php
declare(strict_types=1);

require 'bootstrap/autoload.php';

use App\Core\Database;

echo "=== SEEDING CALENDAR DATES ===\n\n";

// Get first 10 issues
$issues = Database::select("SELECT id FROM issues LIMIT 10");
echo "Found " . count($issues) . " issues\n\n";

if (empty($issues)) {
    echo "No issues found. Create some issues first.\n";
    exit(1);
}

// Add due dates to issues
$today = date('Y-m-d');
$dates = [
    date('Y-m-d', strtotime('+5 days')),
    date('Y-m-d', strtotime('+10 days')),
    date('Y-m-d', strtotime('+15 days')),
    date('Y-m-d', strtotime('+20 days')),
    date('Y-m-d', strtotime('+25 days')),
    date('Y-m-d', strtotime('-5 days')),
    date('Y-m-d', strtotime('-10 days')),
    date('Y-m-d', strtotime('+1 day')),
    date('Y-m-d', strtotime('+2 days')),
    date('Y-m-d', strtotime('+3 days')),
];

$updated = 0;
foreach ($issues as $i => $issue) {
    $dueDate = $dates[$i % count($dates)];
    $startDate = date('Y-m-d', strtotime($dueDate . ' -7 days'));
    $endDate = $dueDate;
    
    $sql = "UPDATE issues SET due_date = ?, start_date = ?, end_date = ? WHERE id = ?";
    Database::update($sql, [$dueDate, $startDate, $endDate, $issue['id']]);
    
    echo "Issue ID {$issue['id']}: due={$dueDate}, start={$startDate}, end={$endDate}\n";
    $updated++;
}

echo "\n✓ Updated $updated issues with calendar dates\n";

// Verify
echo "\n=== VERIFICATION ===\n";
$withDates = Database::selectOne("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN due_date IS NOT NULL THEN 1 ELSE 0 END) as with_due,
        SUM(CASE WHEN start_date IS NOT NULL THEN 1 ELSE 0 END) as with_start,
        SUM(CASE WHEN end_date IS NOT NULL THEN 1 ELSE 0 END) as with_end
    FROM issues
");

echo json_encode($withDates, JSON_PRETTY_PRINT) . "\n";

// Show upcoming
echo "\n=== NEXT 30 DAYS ===\n";
$upcoming = Database::select("
    SELECT 
        id, `key`, summary, due_date, start_date, priority
    FROM issues
    WHERE due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY due_date ASC
    LIMIT 5
");
echo json_encode($upcoming, JSON_PRETTY_PRINT) . "\n";
