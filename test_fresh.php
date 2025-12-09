<?php

echo "Test 1: Direct SQL\n";
$pdo = new PDO('mysql:host=localhost', 'root', '');
$pdo->exec('USE jira_clone');

try {
    $result = $pdo->query('SELECT COUNT(*) as count FROM notifications WHERE user_id = 1 AND is_read = 0');
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "Direct SQL: " . $row['count'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTest 2: Using service\n";
require 'bootstrap/autoload.php';
require 'bootstrap/app.php';

try {
    $count = \App\Services\NotificationService::getUnreadCount(1);
    echo "Service: " . $count . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone\n";
