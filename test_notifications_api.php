<?php
/**
 * Test Notification API Endpoints
 */

declare(strict_types=1);

// Start session to test with current user
session_start();

// Load the app
require 'bootstrap/autoload.php';

$baseUrl = 'http://localhost:8080/jira_clone_system/public';

// Check if user is logged in
$user = $_SESSION['_user'] ?? null;

if (!$user) {
    echo "ERROR: Not logged in. Please log in first.\n";
    exit(1);
}

echo "Testing Notification API for user: {$user['email']}\n";
echo "============================================\n\n";

// Test 1: Get notifications
echo "Test 1: GET /api/v1/notifications\n";
$url = "{$baseUrl}/api/v1/notifications";
echo "URL: {$url}\n";

// Using curl to simulate the request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . session_id());
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
echo "Content-Type: {$contentType}\n";
echo "Response (first 500 chars):\n";
echo substr($response, 0, 500) . "\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if ($data) {
        echo "✓ Valid JSON response\n";
        echo "  - Count: " . ($data['count'] ?? 0) . "\n";
        echo "  - Unread: " . ($data['unread_count'] ?? 0) . "\n";
    } else {
        echo "✗ Invalid JSON\n";
    }
} else {
    echo "✗ HTTP error: {$httpCode}\n";
}

echo "\n";
