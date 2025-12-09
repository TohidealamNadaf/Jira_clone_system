<?php
// Capture the actual HTML response
$url = 'http://localhost:8080/jira_clone_system/public/reports/cumulative-flow/2';
echo "Fetching: $url\n\n";

try {
    $output = @file_get_contents($url);
    
    if ($output === false) {
        echo "Failed to fetch. Trying alternative port...\n";
        $url2 = 'http://localhost:80/jira_clone_system/public/reports/cumulative-flow/2';
        $output = @file_get_contents($url2);
    }
    
    if ($output === false) {
        echo "Unable to fetch page\n";
        exit;
    }
    
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
        echo "Page length: " . strlen($output) . "\n";
        echo "First 1000 chars:\n";
        echo substr($output, 0, 1000) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
