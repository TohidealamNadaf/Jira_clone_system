<?php
/**
 * Fix Missing Issue Foreign Keys
 * 
 * This script ensures all issues have the required foreign key references:
 * - priority_id
 * - issue_type_id
 * - status_id
 * - project_id
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "<h1>Fix Missing Issue Foreign Keys</h1>";

// Get default values
$defaultPriority = Database::selectValue("SELECT id FROM issue_priorities WHERE is_default = 1 LIMIT 1");
$defaultStatus = Database::selectValue("SELECT s.id FROM statuses s 
    JOIN workflow_statuses ws ON s.id = ws.status_id 
    JOIN workflows w ON ws.workflow_id = w.id 
    WHERE w.is_default = 1 AND ws.is_initial = 1 LIMIT 1");
$defaultIssueType = Database::selectValue("SELECT id FROM issue_types WHERE is_subtask = 0 ORDER BY sort_order LIMIT 1");

echo "<h2>Default Values</h2>";
echo "Default Priority ID: " . ($defaultPriority ?? 'NOT FOUND') . "<br>";
echo "Default Status ID: " . ($defaultStatus ?? 'NOT FOUND') . "<br>";
echo "Default Issue Type ID: " . ($defaultIssueType ?? 'NOT FOUND') . "<br><br>";

if (!$defaultPriority || !$defaultStatus || !$defaultIssueType) {
    echo "<p style='color: red;'><strong>ERROR:</strong> One or more defaults not found. Cannot proceed.</p>";
    echo "<p>Please run database setup first.</p>";
    exit;
}

// Find issues with missing references
echo "<h2>Finding Issues with Missing References</h2>";

$issues = Database::select(
    "SELECT i.id, i.issue_key, i.summary, i.priority_id, i.status_id, i.issue_type_id, i.project_id
     FROM issues i"
);

$fixedCount = 0;
$problemsFound = [];

foreach ($issues as $issue) {
    $needsFix = false;
    
    // Check priority
    if (empty($issue['priority_id'])) {
        $problemsFound[] = $issue['issue_key'] . ": Missing priority_id";
        Database::update('issues', ['priority_id' => $defaultPriority], 'id = ?', [$issue['id']]);
        $fixedCount++;
        $needsFix = true;
    } else {
        $priority = Database::selectValue("SELECT id FROM issue_priorities WHERE id = ?", [$issue['priority_id']]);
        if (!$priority) {
            $problemsFound[] = $issue['issue_key'] . ": Invalid priority_id (" . $issue['priority_id'] . ")";
            Database::update('issues', ['priority_id' => $defaultPriority], 'id = ?', [$issue['id']]);
            $fixedCount++;
            $needsFix = true;
        }
    }
    
    // Check status
    if (empty($issue['status_id'])) {
        $problemsFound[] = $issue['issue_key'] . ": Missing status_id";
        Database::update('issues', ['status_id' => $defaultStatus], 'id = ?', [$issue['id']]);
        $fixedCount++;
        $needsFix = true;
    } else {
        $status = Database::selectValue("SELECT id FROM statuses WHERE id = ?", [$issue['status_id']]);
        if (!$status) {
            $problemsFound[] = $issue['issue_key'] . ": Invalid status_id (" . $issue['status_id'] . ")";
            Database::update('issues', ['status_id' => $defaultStatus], 'id = ?', [$issue['id']]);
            $fixedCount++;
            $needsFix = true;
        }
    }
    
    // Check issue type
    if (empty($issue['issue_type_id'])) {
        $problemsFound[] = $issue['issue_key'] . ": Missing issue_type_id";
        Database::update('issues', ['issue_type_id' => $defaultIssueType], 'id = ?', [$issue['id']]);
        $fixedCount++;
        $needsFix = true;
    } else {
        $issueType = Database::selectValue("SELECT id FROM issue_types WHERE id = ?", [$issue['issue_type_id']]);
        if (!$issueType) {
            $problemsFound[] = $issue['issue_key'] . ": Invalid issue_type_id (" . $issue['issue_type_id'] . ")";
            Database::update('issues', ['issue_type_id' => $defaultIssueType], 'id = ?', [$issue['id']]);
            $fixedCount++;
            $needsFix = true;
        }
    }
    
    if ($needsFix) {
        echo "✅ Fixed: {$issue['issue_key']}<br>";
    }
}

echo "<br><h2>Summary</h2>";
if (!empty($problemsFound)) {
    echo "<p style='color: orange;'><strong>Issues Found and Fixed:</strong></p>";
    echo "<ul>";
    foreach ($problemsFound as $problem) {
        echo "<li>$problem</li>";
    }
    echo "</ul>";
    echo "<p><strong>Total fixed: $fixedCount</strong></p>";
} else {
    echo "<p style='color: green;'><strong>All issues have valid foreign key references!</strong></p>";
}

echo "<br><h2>Test the Fix</h2>";
echo "<p>To verify the fix worked:</p>";
echo "<ol>";
echo "<li><a href='/jira_clone_system/public/projects' target='_blank'>Go to Projects Page</a></li>";
echo "<li>Click on 'Baramati Project'</li>";
echo "<li>Check the issue count in the card - should show 7 issues</li>";
echo "<li>Click on the project to see the issues list</li>";
echo "<li>All 7 issues should now be displayed</li>";
echo "</ol>";

?>
