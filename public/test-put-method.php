<?php
/**
 * Test if PUT requests work
 * Visit: http://localhost:8080/jira_clone_system/public/test-put-method.php
 */

header('Content-Type: application/json');

echo json_encode([
    'status' => 'test_page_loaded',
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI'],
    'message' => 'If you see this, PHP received the request successfully'
], JSON_PRETTY_PRINT);
