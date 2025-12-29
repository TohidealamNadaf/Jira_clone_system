<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Services\CalendarService;

$service = new CalendarService();
$events = $service->getDateRangeEvents('2025-12-01', '2026-01-31');

echo "Total Events: " . count($events) . "\n";

$counts = [];
foreach ($events as $event) {
    $id = $event['id'];
    $key = $event['extendedProps']['key'];
    if (!isset($counts[$key])) {
        $counts[$key] = 0;
    }
    $counts[$key]++;
}

$duplicates = array_filter($counts, function ($count) {
    return $count > 1; });

if (empty($duplicates)) {
    echo "No duplicates found in API response.\n";
} else {
    echo "Duplicates found:\n";
    print_r($duplicates);
}

// Check CWAYS-12 specifically
foreach ($events as $event) {
    if ($event['extendedProps']['key'] === 'CWAYS-12') {
        echo "Found CWAYS-12: id={$event['id']}, start={$event['start']}, end={$event['end']}\n";
    }
}
