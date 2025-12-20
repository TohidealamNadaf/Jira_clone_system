<?php
// Test script for time tracking project report

require_once __DIR__ . '/bootstrap/app.php';

$projectId = 1;

try {
    $projectService = new \App\Services\ProjectService();
    $timeTrackingService = new \App\Services\TimeTrackingService();

    echo "=== Testing Time Tracking Project Report ===\n";
    
    // Test 1: Get project
    echo "\n1. Testing ProjectService::getProjectById($projectId)...\n";
    $project = $projectService->getProjectById($projectId);
    if ($project) {
        echo "✓ Project found: {$project['name']}\n";
        echo "  ID: {$project['id']}, Key: {$project['key']}\n";
    } else {
        echo "✗ Project not found\n";
    }

    // Test 2: Get budget status
    echo "\n2. Testing ProjectService::getBudgetStatus($projectId)...\n";
    try {
        $budgetStatus = $projectService->getBudgetStatus($projectId);
        echo "✓ Budget status retrieved\n";
        if (!empty($budgetStatus)) {
            echo "  Keys: " . implode(', ', array_keys($budgetStatus)) . "\n";
        }
    } catch (\Exception $e) {
        echo "✗ Error: {$e->getMessage()}\n";
    }

    // Test 3: Get project time logs
    echo "\n3. Testing TimeTrackingService::getProjectTimeLogs($projectId)...\n";
    try {
        $timeLogs = $timeTrackingService->getProjectTimeLogs($projectId);
        echo "✓ Time logs retrieved: " . count($timeLogs) . " records\n";
        if (!empty($timeLogs)) {
            echo "  Sample keys: " . implode(', ', array_keys($timeLogs[0])) . "\n";
        }
    } catch (\Exception $e) {
        echo "✗ Error: {$e->getMessage()}\n";
    }

    // Test 4: Get cost statistics
    echo "\n4. Testing TimeTrackingService::getCostStatistics($projectId)...\n";
    try {
        $stats = $timeTrackingService->getCostStatistics($projectId);
        echo "✓ Statistics retrieved\n";
        if (!empty($stats)) {
            echo "  Keys: " . implode(', ', array_keys($stats)) . "\n";
        }
    } catch (\Exception $e) {
        echo "✗ Error: {$e->getMessage()}\n";
    }

    echo "\n=== All Tests Complete ===\n";
} catch (\Exception $e) {
    echo "FATAL ERROR: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}\n";
    echo "Line: {$e->getLine()}\n";
}
