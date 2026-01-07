<?php
require_once __DIR__ . '/bootstrap/autoload.php';
$logFile = storage_path('logs/notifications.log');
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    // Add newline before [NOTIFICATION or Notification preference
    $content = preg_replace('/(\[NOTIFICATION|Notification preference)/', "\n$1", $content);
    $content = ltrim($content);
    file_put_contents($logFile, $content);
    echo "Fixed $logFile\n";
} else {
    echo "Log file not found\n";
}
