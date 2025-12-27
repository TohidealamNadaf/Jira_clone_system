<?php
/**
 * Calendar Debug Test
 */

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;
use App\Services\CalendarService;

echo "=== CALENDAR DEBUG TEST ===\n\n";

// 1. Check if issues table has data
echo "1. Checking issues table...\n";
$issues = Database::select("SELECT COUNT(*) as count FROM issues");
echo "Total issues: " . $issues[0]['count'] . "\n\n";

// 2. Check for issues with dates
echo "2. Checking issues with start_date/due_date...\n";
$issuesWithDates = Database::select("
    SELECT COUNT(*) as count FROM issues 
    WHERE start_date IS NOT NULL OR due_date IS NOT NULL
");
echo "Issues with dates: " . $issuesWithDates[0]['count'] . "\n\n";

// 3. Check database connection
echo "3. Testing CalendarService...\n";
$service = new CalendarService();

// Get events for current month
$today = new \DateTime();
$year = (int) $today->format('Y');
$month = (int) $today->format('m');

echo "Fetching events for $month/$year...\n";
$events = $service->getMonthEvents($year, $month);
echo "Events found: " . count($events) . "\n";

if (count($events) > 0) {
    echo "First event: " . json_encode($events[0], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "\nNo events found. Checking raw SQL...\n";
    $startDate = sprintf('%04d-%02d-01', $year, $month);
    $endDate = date('Y-m-t', strtotime($startDate));
    
    echo "Date range: $startDate to $endDate\n\n";
    
    // Check raw query
    $sql = "
        SELECT
            i.id, i.issue_key, i.summary, i.start_date, i.due_date, i.end_date,
            p.name as project_name, s.name as status_name
        FROM issues i
        JOIN projects p ON i.project_id = p.id
        JOIN statuses s ON i.status_id = s.id
        WHERE
            (
                (i.start_date BETWEEN :start1 AND :end1) OR
                (i.end_date BETWEEN :start2 AND :end2) OR
                (i.due_date BETWEEN :start3 AND :end3)
            )
        LIMIT 10
    ";
    
    $params = [
        'start1' => $startDate,
        'end1' => $endDate,
        'start2' => $startDate,
        'end2' => $endDate,
        'start3' => $startDate,
        'end3' => $endDate,
    ];
    
    $rawEvents = Database::select($sql, $params);
    echo "Raw query results: " . count($rawEvents) . "\n";
    
    if (count($rawEvents) > 0) {
        echo json_encode($rawEvents[0], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "No events in date range.\n";
        echo "\nAll issues in database:\n";
        $allIssues = Database::select("
            SELECT i.id, i.issue_key, i.summary, i.start_date, i.due_date, i.end_date
            FROM issues LIMIT 5
        ");
        
        foreach ($allIssues as $issue) {
            echo "- {$issue['issue_key']}: start={$issue['start_date']}, due={$issue['due_date']}, end={$issue['end_date']}\n";
        }
    }
}

echo "\n=== END TEST ===\n";
