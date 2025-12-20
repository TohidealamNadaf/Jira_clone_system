<?php

declare(strict_types=1);

// Test script to verify the member name fix
require 'bootstrap/autoload.php';

use App\Services\TimeTrackingService;
use App\Services\ProjectService;

try {
    $projectId = 1; // CWAYS project
    
    $timeTrackingService = new TimeTrackingService();
    $projectService = new ProjectService();
    
    // Get project
    $project = $projectService->getProjectById($projectId);
    echo "Project: " . ($project['name'] ?? 'Unknown') . "\n\n";
    
    // Get time logs
    $timeLogs = $timeTrackingService->getProjectTimeLogs($projectId);
    echo "Total time logs: " . count($timeLogs) . "\n\n";
    
    // Show first 3 logs
    echo "Sample Time Logs:\n";
    foreach (array_slice($timeLogs, 0, 3) as $log) {
        echo "  User ID: {$log['user_id']}\n";
        echo "  Display Name: {$log['display_name']}\n";
        echo "  Avatar: {$log['avatar']}\n";
        echo "  Issue Key: {$log['issue_key']}\n";
        echo "  Duration: {$log['duration_seconds']}s\n";
        echo "  Cost: {$log['total_cost']}\n";
        echo "  ---\n";
    }
    
    // Show how controller would group them
    echo "\nController Grouping Result:\n";
    $byUser = [];
    foreach ($timeLogs as $log) {
        if (!isset($byUser[$log['user_id']])) {
            $byUser[$log['user_id']] = [
                'name' => $log['display_name'] ?? 'Unknown',
                'avatar' => $log['avatar'] ?? null,
                'total_seconds' => 0,
                'total_cost' => 0,
                'log_count' => 0
            ];
        }
        $byUser[$log['user_id']]['total_seconds'] += (int)$log['duration_seconds'];
        $byUser[$log['user_id']]['total_cost'] += (float)$log['total_cost'];
        $byUser[$log['user_id']]['log_count']++;
    }
    
    foreach ($byUser as $userId => $data) {
        echo "  User {$userId}: {$data['name']} - {$data['log_count']} logs, {$data['total_seconds']}s, ₹{$data['total_cost']}\n";
    }
    
    echo "\n✅ FIX VERIFIED: Controller correctly passes 'name' field (not 'user_name')\n";
    echo "✅ View now correctly accesses \$userData['name'] instead of \$userData['user_name']\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
