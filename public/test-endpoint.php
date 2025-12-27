<?php
/**
 * Test actual endpoint
 */
$url = 'http://localhost:8081/jira_clone_system/public/api/v1/issue-types';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "HTTP Status: " . $info['http_code'] . "\n";
echo "Content-Type: " . $info['content_type'] . "\n";
echo "\nResponse:\n";
echo $response . "\n";
?>
