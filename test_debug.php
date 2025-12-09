<?php

require 'bootstrap/autoload.php';
require 'bootstrap/app.php';

// Enable query logging
\App\Core\Database::enableLogging();

try {
    $count = \App\Services\NotificationService::getUnreadCount(1);
    echo "Service returned: " . $count . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Show all queries that were logged
echo "\n=== Query Log ===\n";
foreach (\App\Core\Database::getQueryLog() as $query) {
    echo "SQL: " . $query['sql'] . "\n";
    echo "Params: " . json_encode($query['params']) . "\n";
    echo "Time: " . $query['time'] . "ms\n\n";
}
