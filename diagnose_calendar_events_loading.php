<?php
/**
 * Calendar Events Loading Diagnostic
 * Checks why events are not loading in the calendar
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;
use App\Services\CalendarService;

echo "=== CALENDAR EVENTS LOADING DIAGNOSTIC ===\n\n";

// 1. Check database connection
echo "1. DATABASE CONNECTION\n";
try {
    $result = Database::select("SELECT 1 as test");
    echo "   ✓ Database connection OK\n\n";
} catch (Exception $e) {
    echo "   ✗ Database connection FAILED: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Check issues table structure
echo "2. ISSUES TABLE STRUCTURE\n";
try {
    $columns = Database::select("DESCRIBE issues");
    $dateColumns = array_filter($columns, function($col) {
        return in_array($col['Field'], ['start_date', 'end_date', 'due_date']);
    });
    
    foreach ($dateColumns as $col) {
        echo "   ✓ Column: {$col['Field']} ({$col['Type']})\n";
    }
    
    if (empty($dateColumns)) {
        echo "   ⚠ WARNING: No date columns found!\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// 3. Check total issues in database
echo "3. ISSUES SUMMARY\n";
try {
    $total = Database::selectOne("SELECT COUNT(*) as count FROM issues");
    echo "   Total Issues: {$total['count']}\n";
    
    if ($total['count'] == 0) {
        echo "   ⚠ WARNING: No issues in database! Calendar will be empty.\n\n";
    } else {
        echo "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// 4. Check issues with date data
echo "4. ISSUES WITH DATE DATA\n";
try {
    $withDates = Database::select("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN start_date IS NOT NULL THEN 1 ELSE 0 END) as with_start_date,
            SUM(CASE WHEN end_date IS NOT NULL THEN 1 ELSE 0 END) as with_end_date,
            SUM(CASE WHEN due_date IS NOT NULL THEN 1 ELSE 0 END) as with_due_date
        FROM issues
    ");
    
    $stats = $withDates[0];
    echo "   Total Issues: {$stats['total']}\n";
    echo "   With start_date: {$stats['with_start_date']}\n";
    echo "   With end_date: {$stats['with_end_date']}\n";
    echo "   With due_date: {$stats['with_due_date']}\n";
    
    if ($stats['with_start_date'] == 0 && $stats['with_end_date'] == 0 && $stats['with_due_date'] == 0) {
        echo "   ⚠ CRITICAL: No issues have ANY date data!\n";
        echo "   This is why calendar is empty. Need to seed dates.\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// 5. Sample issues data
echo "5. SAMPLE ISSUES (First 5)\n";
try {
    $samples = Database::select("
        SELECT 
            id, 
            issue_key,
            summary,
            start_date,
            end_date,
            due_date,
            status_id,
            project_id
        FROM issues
        LIMIT 5
    ");
    
    if (empty($samples)) {
        echo "   No issues found\n";
    } else {
        foreach ($samples as $issue) {
            echo "   - {$issue['issue_key']}: {$issue['summary']}\n";
            echo "     start_date: {$issue['start_date']}\n";
            echo "     end_date: {$issue['end_date']}\n";
            echo "     due_date: {$issue['due_date']}\n";
        }
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// 6. Test CalendarService
echo "6. TESTING CALENDAR SERVICE\n";
try {
    $service = new CalendarService();
    
    // Get current month
    $today = date('Y-m-d');
    $year = (int) date('Y');
    $month = (int) date('m');
    
    echo "   Testing getMonthEvents for {$year}-{$month}...\n";
    $events = $service->getMonthEvents($year, $month);
    
    echo "   Events returned: " . count($events) . "\n";
    
    if (count($events) > 0) {
        echo "   ✓ Calendar service is working!\n";
        echo "   First event: " . json_encode($events[0], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "   ⚠ Calendar service returned 0 events\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// 7. Check API endpoint directly
echo "7. SIMULATING API CALL\n";
try {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/api/v1/calendar/events?start=' . date('Y-m-01') . '&end=' . date('Y-m-t');
    
    $service = new CalendarService();
    $start = date('Y-m-01');
    $end = date('Y-m-t');
    
    $events = $service->getDateRangeEvents($start, $end);
    
    echo "   Date Range: {$start} to {$end}\n";
    echo "   Events returned: " . count($events) . "\n";
    
    if (count($events) > 0) {
        echo "   ✓ API simulation successful!\n";
    } else {
        echo "   ⚠ API simulation returned 0 events\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// 8. Recommendations
echo "8. RECOMMENDATIONS\n";
echo "   If calendar shows empty:\n";
echo "   - Check if issues table has start_date/end_date/due_date values\n";
echo "   - Run seed script to populate calendar dates\n";
echo "   - Check browser console (F12) for API errors\n";
echo "   - Check Network tab to see actual API response\n";
echo "\n";

// 9. Check if we need to seed data
$needsSeed = false;
try {
    $total = Database::selectOne("SELECT COUNT(*) as count FROM issues WHERE due_date IS NOT NULL");
    if ($total['count'] == 0) {
        $needsSeed = true;
    }
} catch (Exception $e) {
    $needsSeed = true;
}

if ($needsSeed) {
    echo "9. SEED CALENDAR DATES\n";
    echo "   Run this command to populate calendar dates:\n";
    echo "   php scripts/seed_calendar_dates.php\n";
    echo "\n";
}

echo "=== DIAGNOSTIC COMPLETE ===\n";
