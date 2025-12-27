<?php
/**
 * Test Calendar API Response
 */

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;
use App\Services\CalendarService;

header('Content-Type: application/json');

echo json_encode([
    'debug' => [
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
        'uri' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
        'time' => date('Y-m-d H:i:s'),
        'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB'
    ],
    'test' => 'Calendar API Response'
], JSON_PRETTY_PRINT);

// Get date range from query
$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-t');

echo json_encode([
    'request' => [
        'start' => $start,
        'end' => $end
    ]
], JSON_PRETTY_PRINT);

try {
    // Test service
    $service = new CalendarService();
    
    // Check raw database query
    $sql = "
        SELECT COUNT(*) as total FROM issues i
        WHERE i.start_date IS NOT NULL OR i.due_date IS NOT NULL OR i.end_date IS NOT NULL
    ";
    
    $count = Database::select($sql);
    
    echo json_encode([
        'database' => [
            'total_issues_with_dates' => $count[0]['total'] ?? 0,
            'query_executed' => true
        ]
    ], JSON_PRETTY_PRINT);
    
    // Get actual events
    $events = $service->getDateRangeEvents($start, $end);
    
    echo json_encode([
        'events' => [
            'count' => count($events),
            'sample' => count($events) > 0 ? $events[0] : null
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (\Exception $e) {
    echo json_encode([
        'error' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ], JSON_PRETTY_PRINT);
}
