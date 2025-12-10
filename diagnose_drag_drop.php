<?php
/**
 * Diagnostic script to test board drag and drop
 */
declare(strict_types=1);

require 'bootstrap/autoload.php';

$app = require 'bootstrap/app.php';
$db = app()->getContainer()->get('database');

echo "=== BOARD DRAG & DROP DIAGNOSTIC ===\n\n";

// 1. Check if workflow_transitions table exists and has data
echo "1. Workflow Transitions Table:\n";
try {
    $transitions = $db->select("SELECT COUNT(*) as count FROM workflow_transitions");
    echo "   ✓ Table exists\n";
    echo "   - Total transitions: " . $transitions[0]['count'] . "\n";
    
    if ($transitions[0]['count'] == 0) {
        echo "   ⚠ WARNING: No transitions configured. Fallback will allow any transition.\n";
    }
} catch (Exception $e) {
    echo "   ✗ Table does not exist: " . $e->getMessage() . "\n";
}

// 2. Check statuses exist
echo "\n2. Available Statuses:\n";
try {
    $statuses = $db->select("SELECT id, name, color FROM statuses ORDER BY id");
    echo "   ✓ Found " . count($statuses) . " statuses:\n";
    foreach ($statuses as $status) {
        echo "     - ID {$status['id']}: {$status['name']} ({$status['color']})\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// 3. Check if issues exist for board
echo "\n3. Issues for Test Project (BP):\n";
try {
    $issues = $db->select("
        SELECT i.id, i.issue_key, i.summary, i.status_id, s.name as status_name
        FROM issues i
        LEFT JOIN statuses s ON i.status_id = s.id
        WHERE i.project_id = (SELECT id FROM projects WHERE `key` = 'BP')
        LIMIT 5
    ");
    echo "   ✓ Found " . count($issues) . " issues:\n";
    foreach ($issues as $issue) {
        echo "     - {$issue['issue_key']}: {$issue['summary']} (Status: {$issue['status_name']})\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// 4. Test the API endpoint structure
echo "\n4. API Route Check:\n";
$apiRoute = '/api/v1/issues/{key}/transitions';
echo "   - Route: POST $apiRoute\n";
echo "   - Expected body: {\"status_id\": <int>}\n";
echo "   - Middleware: api, throttle:300,1\n";

// 5. Check IssueService methods exist
echo "\n5. IssueService Methods:\n";
$serviceFile = 'src/Services/IssueService.php';
if (file_exists($serviceFile)) {
    $content = file_get_contents($serviceFile);
    
    $hasTransition = strpos($content, 'public function transitionIssue') !== false;
    $hasIsAllowed = strpos($content, 'private function isTransitionAllowed') !== false;
    $hasAvailable = strpos($content, 'public function getAvailableTransitions') !== false;
    
    echo "   ✓ transitionIssue(): " . ($hasTransition ? "✓" : "✗") . "\n";
    echo "   ✓ isTransitionAllowed(): " . ($hasIsAllowed ? "✓" : "✗") . "\n";
    echo "   ✓ getAvailableTransitions(): " . ($hasAvailable ? "✓" : "✗") . "\n";
} else {
    echo "   ✗ Service file not found\n";
}

// 6. Check board.php for JavaScript
echo "\n6. Board View JavaScript:\n";
$boardFile = 'views/projects/board.php';
if (file_exists($boardFile)) {
    $content = file_get_contents($boardFile);
    
    $hasDragstart = strpos($content, 'dragstart') !== false;
    $hasDrop = strpos($content, 'drop') !== false;
    $hasApi = strpos($content, '/api/v1/issues') !== false;
    
    echo "   ✓ dragstart listener: " . ($hasDragstart ? "✓" : "✗") . "\n";
    echo "   ✓ drop listener: " . ($hasDrop ? "✓" : "✗") . "\n";
    echo "   ✓ API call: " . ($hasApi ? "✓" : "✗") . "\n";
} else {
    echo "   ✗ Board view not found\n";
}

// 7. Check database connectivity
echo "\n7. Database Connection Test:\n";
try {
    $test = $db->selectOne("SELECT 1 as test");
    echo "   ✓ Database connected\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== END DIAGNOSTIC ===\n";
