<?php
/**
 * Test if BP-7 exists and why it's not loading
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;
use App\Services\IssueService;

echo "=== BP-7 ISSUE DIAGNOSTIC ===\n\n";

// 1. Check if issue exists
echo "1. Checking if BP-7 exists...\n";
$issue = Database::selectOne(
    "SELECT id, issue_key, summary, project_id FROM issues WHERE issue_key = 'BP-7'"
);

if (!$issue) {
    echo "❌ BP-7 NOT FOUND\n";
    echo "   Need to create this issue first.\n\n";
    
    // Show all issues
    echo "2. All issues in database:\n";
    $allIssues = Database::select(
        "SELECT issue_key, summary FROM issues ORDER BY issue_key"
    );
    
    if (empty($allIssues)) {
        echo "   ❌ No issues found. Create an issue first!\n";
    } else {
        foreach ($allIssues as $i) {
            echo "   - {$i['issue_key']}: {$i['summary']}\n";
        }
    }
    exit;
}

echo "✅ BP-7 FOUND (ID: {$issue['id']})\n";
echo "   Summary: {$issue['summary']}\n";
echo "   Project ID: {$issue['project_id']}\n\n";

// 2. Try to load with IssueService
echo "2. Testing IssueService::getIssueByKey('BP-7')...\n";

try {
    $issueService = new IssueService();
    $fullIssue = $issueService->getIssueByKey('BP-7');
    
    if ($fullIssue) {
        echo "✅ Issue loaded successfully via service\n";
        echo "   - Title: {$fullIssue['summary']}\n";
        echo "   - Status: {$fullIssue['status_name']}\n";
        echo "   - Comments: " . count($fullIssue['comments'] ?? []) . "\n";
    } else {
        echo "❌ IssueService returned null\n";
        echo "   This means the database query failed\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// 3. Test raw database query
echo "\n3. Testing raw database query...\n";

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
        "SELECT i.*, 
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
         WHERE i.issue_key = ?"
    );
    
    $stmt->execute(['BP-7']);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✅ Raw query successful\n";
        echo "   - Issue loaded: {$result['issue_key']}\n";
    } else {
        echo "❌ Raw query returned null\n";
    }
} catch (Exception $e) {
    echo "❌ Query error: " . $e->getMessage() . "\n";
}

// 4. Test comment loading
echo "\n4. Testing comment loading for BP-7...\n";

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->query(
        "SELECT c.*, u.display_name, u.id as user_id
         FROM comments c
         JOIN users u ON c.user_id = u.id
         WHERE c.issue_id = " . (int)$issue['id'] . "
         ORDER BY c.created_at DESC"
    );
    $comments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    echo "✅ Comments loaded: " . count($comments) . " found\n";
} catch (Exception $e) {
    echo "❌ Comment query error: " . $e->getMessage() . "\n";
}

echo "\n=== NEXT STEPS ===\n";
if ($fullIssue) {
    echo "✅ BP-7 is loading correctly. Try accessing:\n";
    echo "   http://localhost:8080/jira_clone_system/public/issue/BP-7\n";
} else {
    echo "❌ BP-7 has a loading issue. Check the errors above.\n";
}
?>
