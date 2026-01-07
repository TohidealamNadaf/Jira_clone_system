<?php
require_once __DIR__ . '/src/Helpers/functions.php';

// Define BASE_PATH manually since we are not going through index.php
// We assume this script is in project root
define('BASE_PATH', __DIR__);

echo "Project Root: " . __DIR__ . "\n";
echo "Storage Path: " . storage_path() . "\n";
echo "Log File: " . storage_path('logs/notifications.log') . "\n";

$logFile = storage_path('logs/notifications.log');

if (file_exists($logFile)) {
    echo "File exists.\n";
    echo "Size: " . filesize($logFile) . " bytes.\n";
    $content = file_get_contents($logFile);
    echo "Content Length: " . strlen($content) . "\n";
    echo "Error Count: " . substr_count($content, '[NOTIFICATION ERROR]') . "\n";
} else {
    echo "File DOES NOT exist.\n";
}
