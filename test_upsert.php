<?php
require_once __DIR__ . '/bootstrap/autoload.php';
use App\Core\Database;

try {
    echo "Testing insertOrUpdate...\n";
    $result = Database::insertOrUpdate(
        'notification_preferences',
        [
            'user_id' => 1,
            'event_type' => 'issue_created',
            'in_app' => 1,
            'email' => 1,
            'push' => 0,
        ],
        ['user_id', 'event_type']
    );
    echo "Result: " . ($result ? "Success" : "No change/Duplicate") . "\n";
} catch (\Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
