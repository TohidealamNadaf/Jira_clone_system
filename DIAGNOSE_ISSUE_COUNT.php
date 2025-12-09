<?php
/**
 * Diagnose Issue: 7 Issues in Baramati Project Card but Only 1 Displays in List
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;
use App\Services\IssueService;
use App\Services\ProjectService;

echo "<h1>Diagnosis: Baramati Project Issues Display Problem</h1>";

// Step 1: Get the Baramati project
echo "<h2>Step 1: Get Baramati Project</h2>";
$project = Database::selectOne(
    "SELECT id, key, name, issue_count FROM projects WHERE name = 'Baramati Project' OR key = 'BP'"
);

if (!$project) {
    echo "❌ Baramati project not found<br>";
    exit;
}

echo "✅ Found: {$project['key']} - {$project['name']}<br>";
echo "   Issue count in projects.issue_count: <strong>{$project['issue_count']}</strong><br><br>";

// Step 2: Check actual issues in database
echo "<h2>Step 2: Count Actual Issues in Database</h2>";
$actualCount = Database::selectValue(
    "SELECT COUNT(*) FROM issues WHERE project_id = ?",
    [$project['id']]
);

echo "✅ Actual issues in issues table: <strong>$actualCount</strong><br>";

if ($actualCount != $project['issue_count']) {
    echo "⚠️ MISMATCH: issue_count ({$project['issue_count']}) ≠ actual count ($actualCount)<br><br>";
}

// Step 3: List all issues
echo "<h2>Step 3: List All Issues</h2>";
$allIssues = Database::select(
    "SELECT i.id, i.issue_key, i.summary, i.status_id, s.name as status_name 
     FROM issues i
     LEFT JOIN statuses s ON i.status_id = s.id
     WHERE i.project_id = ? 
     ORDER BY i.id ASC",
    [$project['id']]
);

if (empty($allIssues)) {
    echo "❌ No issues found<br>";
} else {
    echo "Found " . count($allIssues) . " issues:<br>";
    echo "<ul>";
    foreach ($allIssues as $issue) {
        echo "<li>{$issue['issue_key']}: {$issue['summary']} (Status: {$issue['status_name']})</li>";
    }
    echo "</ul>";
}

// Step 4: Test IssueService.getIssues() method
echo "<h2>Step 4: Test IssueService::getIssues() Method</h2>";
$issueService = new IssueService();

$result = $issueService->getIssues(
    ['project_id' => $project['id']],
    'created_at',
    'DESC',
    1,
    25
);

echo "Method returned:<br>";
echo "- Keys in result: " . implode(', ', array_keys($result)) . "<br>";
echo "- 'data' key exists: " . (isset($result['data']) ? 'YES' : 'NO') . "<br>";
echo "- 'data' is array: " . (is_array($result['data']) ? 'YES' : 'NO') . "<br>";
echo "- Count of data: " . count($result['data'] ?? []) . "<br>";
echo "- Total: " . ($result['total'] ?? 'N/A') . "<br>";
echo "- Total pages: " . ($result['total_pages'] ?? 'N/A') . "<br><br>";

// Step 5: Check the SQL being executed
echo "<h2>Step 5: Raw SQL Query Test</h2>";
echo "<pre>";
$pdo = Database::getConnection();
$sql = "SELECT i.*, 
        p.key as project_key, p.name as project_name,
        it.name as issue_type_name, it.icon as issue_type_icon, it.color as issue_type_color,
        s.name as status_name, s.category as status_category, s.color as status_color,
        ip.name as priority_name, ip.icon as priority_icon, ip.color as priority_color,
        reporter.display_name as reporter_name, reporter.avatar as reporter_avatar,
        assignee.display_name as assignee_name, assignee.avatar as assignee_avatar
 FROM issues i
 JOIN projects p ON i.project_id = p.id
 JOIN issue_types it ON i.issue_type_id = it.id
 JOIN statuses s ON i.status_id = s.id
 JOIN issue_priorities ip ON i.priority_id = ip.id
 LEFT JOIN users reporter ON i.reporter_id = reporter.id
 LEFT JOIN users assignee ON i.assignee_id = assignee.id
 WHERE i.project_id = {$project['id']}
 ORDER BY i.created_at DESC
 LIMIT 25 OFFSET 0";
echo "SQL Query:\n" . $sql . "</pre>";

try {
    $stmt = $pdo->query($sql);
    $sqlResult = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    echo "✅ Query executed successfully<br>";
    echo "   Result count: " . count($sqlResult) . "<br><br>";
} catch (\Exception $e) {
    echo "❌ Query failed: " . $e->getMessage() . "<br><br>";
}

// Step 6: Check for JOIN issues
echo "<h2>Step 6: Check for Missing Related Records</h2>";
foreach ($allIssues as $issue) {
    echo "Issue {$issue['issue_key']}:<br>";
    
    // Check issue_type
    $issueType = Database::selectOne(
        "SELECT id, name FROM issue_types WHERE id = (SELECT issue_type_id FROM issues WHERE id = ?)",
        [$issue['id']]
    );
    echo "  - Issue Type: " . ($issueType ? $issueType['name'] : '❌ MISSING') . "<br>";
    
    // Check status
    $status = Database::selectOne(
        "SELECT id, name FROM statuses WHERE id = (SELECT status_id FROM issues WHERE id = ?)",
        [$issue['id']]
    );
    echo "  - Status: " . ($status ? $status['name'] : '❌ MISSING') . "<br>";
    
    // Check priority
    $priority = Database::selectOne(
        "SELECT id, name FROM issue_priorities WHERE id = (SELECT priority_id FROM issues WHERE id = ?)",
        [$issue['id']]
    );
    echo "  - Priority: " . ($priority ? $priority['name'] : '❌ MISSING') . "<br>";
}

echo "<br><h2>Summary</h2>";
echo "Total issues in database: $actualCount<br>";
echo "Issues returned by service: " . count($result['data'] ?? []) . "<br>";
echo "Issues shown in card: {$project['issue_count']}<br>";

if (count($result['data'] ?? []) < $actualCount) {
    echo "<br><strong style='color: red;'>ISSUE IDENTIFIED:</strong> Some issues are being filtered out by the JOIN statements.<br>";
    echo "This usually means some issues are missing related records (issue_type, status, priority, etc.)";
}

?>
