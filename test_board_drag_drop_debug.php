<?php
/**
 * Debug Board Drag & Drop Issues
 */
require 'bootstrap/app.php';

$db = \App\Core\Database::class;

// Test 1: Check default avatar issue
echo "=== AVATAR PATH TEST ===" . PHP_EOL;
echo "Current code uses: /images/default-avatar.png" . PHP_EOL;
echo "File exists at: " . (file_exists('public/images/default-avatar.png') ? 'YES' : 'NO') . PHP_EOL;
echo "Correct path should be: /public/uploads/avatars/default.png or similar" . PHP_EOL;
echo "Solution: Fallback to Bootstrap Avatar SVG or create default-avatar.png" . PHP_EOL;
echo PHP_EOL;

// Test 2: Check transition workflow
echo "=== WORKFLOW TRANSITIONS TEST ===" . PHP_EOL;
$transitionCount = \App\Core\Database::selectOne(
    "SELECT COUNT(*) as count FROM workflow_transitions"
);
echo "Total workflow transitions configured: " . $transitionCount['count'] . PHP_EOL;

if ($transitionCount['count'] == 0) {
    echo "Status: NO TRANSITIONS CONFIGURED (using fallback)" . PHP_EOL;
    echo "This means all transitions should be ALLOWED" . PHP_EOL;
} else {
    echo "Status: Workflow transitions are configured" . PHP_EOL;
}
echo PHP_EOL;

// Test 3: Check statuses
echo "=== STATUSES ===" . PHP_EOL;
$statuses = \App\Core\Database::select("SELECT id, name FROM statuses LIMIT 10");
foreach ($statuses as $status) {
    echo "ID: {$status['id']}, Name: {$status['name']}" . PHP_EOL;
}
echo PHP_EOL;

// Test 4: Check issues on BP board
echo "=== BP PROJECT ISSUES ===" . PHP_EOL;
$issues = \App\Core\Database::select(
    "SELECT i.id, i.issue_key, i.status_id, s.name as status_name
     FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.project_id = (SELECT id FROM projects WHERE `key` = 'BP')
     LIMIT 5"
);
echo "Found " . count($issues) . " issues" . PHP_EOL;
foreach ($issues as $issue) {
    echo "- {$issue['issue_key']}: {$issue['status_name']} (status_id: {$issue['status_id']})" . PHP_EOL;
}
echo PHP_EOL;

echo "=== DIAGNOSIS ===" . PHP_EOL;
echo "1. AVATAR 404: Fix the image path in board.php line 72" . PHP_EOL;
echo "2. DRAG-DROP: Should work if workflow fallback is active" . PHP_EOL;
echo "3. Check browser console for actual errors" . PHP_EOL;
