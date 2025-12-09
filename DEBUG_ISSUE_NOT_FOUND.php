<?php
/**
 * Debug Script - Issue Not Found (404) Error
 * 
 * This script helps diagnose why you're getting "Issue not found" errors
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "=== ISSUE NOT FOUND DIAGNOSTIC ===\n\n";

// 1. Check if issues exist
echo "1. CHECKING FOR ISSUES IN DATABASE:\n";
echo "-------------------------------------\n";

$issues = Database::select(
    "SELECT id, issue_key, summary, project_id FROM issues LIMIT 10"
);

if (empty($issues)) {
    echo "❌ NO ISSUES FOUND IN DATABASE\n";
    echo "   ACTION: Create an issue first\n";
} else {
    echo "✅ Found " . count($issues) . " issues:\n";
    foreach ($issues as $issue) {
        echo "   - {$issue['issue_key']}: {$issue['summary']}\n";
    }
}

echo "\n";

// 2. Check current URL
echo "2. WHAT URL ARE YOU USING?\n";
echo "-------------------------------------\n";
echo "Current URL: " . ($_SERVER['REQUEST_URI'] ?? 'Unable to determine') . "\n";
echo "\nExpected format: /issue/PROJECT-NUMBER\n";
echo "Example: /issue/TEST-1\n";

echo "\n";

// 3. Check projects
echo "3. CHECKING FOR PROJECTS:\n";
echo "-------------------------------------\n";

$projects = Database::select(
    "SELECT id, key, name, issue_count FROM projects LIMIT 10"
);

if (empty($projects)) {
    echo "❌ NO PROJECTS FOUND\n";
    echo "   ACTION: Create a project first\n";
} else {
    echo "✅ Found " . count($projects) . " projects:\n";
    foreach ($projects as $project) {
        echo "   - {$project['key']}: {$project['name']} ({$project['issue_count']} issues)\n";
    }
}

echo "\n";

// 4. Test specific issue key
if (!empty($issues)) {
    echo "4. TESTING ISSUE LOOKUP:\n";
    echo "-------------------------------------\n";
    
    $testKey = $issues[0]['issue_key'];
    echo "Testing with: {$testKey}\n";
    
    $found = Database::selectOne(
        "SELECT id, issue_key FROM issues WHERE issue_key = ?",
        [$testKey]
    );
    
    if ($found) {
        echo "✅ Issue found by key: {$found['issue_key']}\n";
        echo "   Try accessing: /issue/{$found['issue_key']}\n";
    } else {
        echo "❌ Issue not found in database\n";
    }
}

echo "\n";

// 5. Database connection test
echo "5. DATABASE CONNECTION TEST:\n";
echo "-------------------------------------\n";

try {
    $pdo = Database::getConnection();
    echo "✅ Database connected successfully\n";
    
    $result = $pdo->query("SELECT COUNT(*) as count FROM issues");
    $row = $result->fetch(\PDO::FETCH_ASSOC);
    echo "   Total issues: {$row['count']}\n";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";

echo "=== SUMMARY ===\n";
echo "If you see '✅' on items 1 and 3, then:\n";
echo "1. Copy an issue key from the list above (e.g., TEST-1)\n";
echo "2. Visit: http://localhost/issue/{ISSUE_KEY}\n";
echo "3. Replace {ISSUE_KEY} with actual key\n\n";
echo "If all are '❌', you need to:\n";
echo "1. Create a project first\n";
echo "2. Create an issue in that project\n";
echo "3. Then access the issue using its key\n";
?>
