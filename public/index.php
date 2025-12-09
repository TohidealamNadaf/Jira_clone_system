<?php
/**
 * Front Controller
 * All requests are routed through this file
 */

declare(strict_types=1);

// DISABLE CSP COMPLETELY - no restrictions for development
ini_set('expose_php', '0');

// Suppress array key warnings for development (these are benign and handled with null coalescing)
ini_set('error_reporting', E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

// Register output buffer to remove any CSP headers that might be added
ob_start(function($buffer) {
    return $buffer;
});

// Set CSP header FIRST before any output
if (!headers_sent()) {
    // Remove any existing CSP restrictions
    header_remove('Content-Security-Policy');
    header_remove('Content-Security-Policy-Report-Only');
    
    // Set completely permissive CSP for development
    header("Content-Security-Policy: *", true);
}

// Define start time for performance monitoring
define('APP_START', microtime(true));

// Load application bootstrap
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Run the application
$app->run();

// Debug info (only in development)
if (config('app.debug') && !is_ajax() && !wants_json()) {
    $time = round((microtime(true) - APP_START) * 1000, 2);
    $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
    $queries = count(\App\Core\Database::getQueryLog());
    $queryTime = round(\App\Core\Database::getTotalQueryTime(), 2);
    
    echo "<!-- Page generated in {$time}ms | Memory: {$memory}MB | Queries: {$queries} ({$queryTime}ms) -->";
}
