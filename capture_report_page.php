<?php
// Capture the actual HTML response
$output = file_get_contents('http://localhost/jira_clone_system/public/reports/cumulative-flow/2');

// Find the script section with the chart data
if (preg_match('/const flowData = (\[.*?\]);/s', $output, $matches)) {
    echo "=== Found flowData in page ===\n";
    $json = $matches[1];
    echo "Length: " . strlen($json) . "\n";
    echo "First 300 chars:\n";
    echo substr($json, 0, 300) . "\n\n";
    
    // Try to decode it
    $decoded = json_decode($json);
    echo "Decoded OK: " . ($decoded ? "YES" : "NO") . "\n";
    if ($decoded) {
        echo "Item count: " . count($decoded) . "\n";
    }
} else {
    echo "flowData not found in page\n";
    echo "Looking for script tag...\n";
    if (preg_match('/<script[^>]*>(.*?)<\/script>/s', $output, $matches)) {
        echo "Found script tag:\n";
        echo substr($matches[1], 0, 500) . "\n";
    }
}
