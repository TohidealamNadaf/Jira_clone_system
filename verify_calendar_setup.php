<?php
/**
 * Verify Calendar System Setup
 */

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

echo "=== CALENDAR SYSTEM VERIFICATION ===\n\n";

// 1. Check if issues table has start_date and end_date columns
echo "1. Checking issues table schema...\n";
$schema = Database::select("DESCRIBE issues");
$hasStartDate = false;
$hasEndDate = false;

foreach ($schema as $col) {
    if ($col['Field'] === 'start_date') $hasStartDate = true;
    if ($col['Field'] === 'end_date') $hasEndDate = true;
}

echo "   - start_date column: " . ($hasStartDate ? "✓ EXISTS" : "✗ MISSING") . "\n";
echo "   - end_date column: " . ($hasEndDate ? "✓ EXISTS" : "✗ MISSING") . "\n";

// 2. Check test data
echo "\n2. Checking test data...\n";
$issuesCount = Database::select("SELECT COUNT(*) as cnt FROM issues");
echo "   - Total issues: " . $issuesCount[0]['cnt'] . "\n";

$issuesWithStartDate = Database::select("SELECT COUNT(*) as cnt FROM issues WHERE start_date IS NOT NULL");
echo "   - Issues with start_date: " . $issuesWithStartDate[0]['cnt'] . "\n";

$issuesWithDueDate = Database::select("SELECT COUNT(*) as cnt FROM issues WHERE due_date IS NOT NULL");
echo "   - Issues with due_date: " . $issuesWithDueDate[0]['cnt'] . "\n";

$issuesWithEndDate = Database::select("SELECT COUNT(*) as cnt FROM issues WHERE end_date IS NOT NULL");
echo "   - Issues with end_date: " . $issuesWithEndDate[0]['cnt'] . "\n";

// 3. Sample data
echo "\n3. Sample issues:\n";
$samples = Database::select("
    SELECT issue_key, summary, start_date, due_date, end_date
    FROM issues
    WHERE start_date IS NOT NULL OR due_date IS NOT NULL OR end_date IS NOT NULL
    LIMIT 5
");

if (count($samples) > 0) {
    foreach ($samples as $issue) {
        echo "   - {$issue['issue_key']}: start={$issue['start_date']}, due={$issue['due_date']}, end={$issue['end_date']}\n";
    }
} else {
    echo "   - No issues with dates found\n";
    
    echo "\n   Showing any issues:\n";
    $any = Database::select("
        SELECT issue_key, summary, start_date, due_date, end_date
        FROM issues
        LIMIT 5
    ");
    
    foreach ($any as $issue) {
        echo "   - {$issue['issue_key']}: start={$issue['start_date']}, due={$issue['due_date']}, end={$issue['end_date']}\n";
    }
}

// 4. Test CalendarService
echo "\n4. Testing CalendarService...\n";
try {
    $service = new \App\Services\CalendarService();
    
    $today = new \DateTime();
    $year = (int) $today->format('Y');
    $month = (int) $today->format('m');
    
    $events = $service->getMonthEvents($year, $month);
    echo "   - Events returned: " . count($events) . "\n";
    
    if (count($events) > 0) {
        echo "   - Sample event: " . json_encode($events[0], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
    }
} catch (\Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
    echo "   - File: {$e->getFile()}:{$e->getLine()}\n";
}

// 5. Check Calendar Controller
echo "\n5. Checking CalendarController...\n";
if (class_exists('App\\Controllers\\CalendarController')) {
    echo "   ✓ CalendarController class exists\n";
} else {
    echo "   ✗ CalendarController class NOT FOUND\n";
}

echo "\n=== END VERIFICATION ===\n";
