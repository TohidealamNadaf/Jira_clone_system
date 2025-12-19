<?php
/**
 * Test the quick create endpoint
 */
header('Content-Type: application/json');

// Get the base path for URL routing
$basePath = '/jira_clone_system/public';

// Simulate the request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = $basePath . '/projects/quick-create-list';
$_SERVER['SCRIPT_NAME'] = $basePath . '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';

// Include the main app through index.php routing
require 'index.php';
?>
