<?php
require 'bootstrap/app.php';

echo "System date/time:\n";
echo "Now: " . (new \DateTime())->format('Y-m-d H:i:s') . "\n";
echo "30 days ago: " . (new \DateTime())->modify("-30 days")->format('Y-m-d H:i:s') . "\n\n";

// Check database date
$dbDate = \App\Core\Database::selectValue("SELECT NOW()");
echo "Database NOW(): $dbDate\n\n";

// Check the oldest and newest issue creation dates
$oldestIssue = \App\Core\Database::selectOne(
    "SELECT id, issue_key, created_at FROM issues ORDER BY created_at ASC LIMIT 1"
);
$newestIssue = \App\Core\Database::selectOne(
    "SELECT id, issue_key, created_at FROM issues ORDER BY created_at DESC LIMIT 1"
);

echo "Oldest issue:\n";
echo json_encode($oldestIssue, JSON_PRETTY_PRINT) . "\n\n";

echo "Newest issue:\n";
echo json_encode($newestIssue, JSON_PRETTY_PRINT) . "\n";
