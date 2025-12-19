<?php
/**
 * CREATE TEST ISSUES FOR CWAYS PROJECT
 * 
 * This script creates 5 test issues in the CWAYS project
 * so you can test the time tracking modal.
 * 
 * Usage: Open in browser at /CREATE_TEST_ISSUES_FOR_CWAYS.php
 */

// Start session and get user
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die('❌ You must be logged in to create issues. <a href="/login">Login here</a>');
}

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Services\IssueService;
use App\Core\Session;

$currentUser = Session::user();
if (!$currentUser) {
    die('❌ Session expired. <a href="/login">Login here</a>');
}

echo "<h2>🚀 Creating Test Issues for CWAYS Project...</h2>";
echo "<pre>";

try {
    $issueService = new IssueService();
    
    // Issue data to create
    $issues = [
        [
            'summary' => 'Create database schema',
            'description' => 'Design and create the main database schema for the system',
            'issue_type_id' => 1,  // Task
            'priority_id' => 2,    // High
            'project_id' => 1,     // CWAYS
            'status_id' => 1,      // Open
        ],
        [
            'summary' => 'Setup authentication system',
            'description' => 'Implement user authentication with JWT tokens',
            'issue_type_id' => 1,  // Task
            'priority_id' => 1,    // Urgent
            'project_id' => 1,     // CWAYS
            'status_id' => 2,      // In Progress
        ],
        [
            'summary' => 'Build dashboard UI',
            'description' => 'Create responsive dashboard with charts and metrics',
            'issue_type_id' => 1,  // Task
            'priority_id' => 2,    // High
            'project_id' => 1,     // CWAYS
            'status_id' => 1,      // Open
        ],
        [
            'summary' => 'Setup API endpoints',
            'description' => 'Create REST API endpoints for core features',
            'issue_type_id' => 1,  // Task
            'priority_id' => 2,    // High
            'project_id' => 1,     // CWAYS
            'status_id' => 1,      // Open
        ],
        [
            'summary' => 'Write documentation',
            'description' => 'Create comprehensive API and user documentation',
            'issue_type_id' => 3,  // Documentation
            'priority_id' => 3,    // Medium
            'project_id' => 1,     // CWAYS
            'status_id' => 1,      // Open
        ],
    ];
    
    // Create each issue
    $created = 0;
    foreach ($issues as $issueData) {
        try {
            $result = $issueService->createIssue($issueData, (int)$currentUser['id']);
            echo "✓ Created: {$result['key']} - {$issueData['summary']}\n";
            $created++;
        } catch (Exception $e) {
            echo "⚠ Skipped: {$issueData['summary']} ({$e->getMessage()})\n";
        }
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✅ Created $created test issues!\n";
    echo "=" . str_repeat("=", 70) . "\n";
    echo "\n📝 NEXT STEPS:\n";
    echo "1. Go to: /time-tracking/project/1\n";
    echo "2. Click 'Start Timer' button\n";
    echo "3. Issue dropdown should now show the created issues ✓\n";
    echo "4. Select an issue and start tracking time\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nTrace:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
echo '<br><a href="/time-tracking/project/1">← Back to Time Tracking</a>';
?>
