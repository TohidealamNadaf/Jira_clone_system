<?php
/**
 * Diagnose Quick Create Modal Issue
 * 
 * Tests if the issue creation is actually working
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

$db = app()->getDatabase();

echo "=== QUICK CREATE MODAL DIAGNOSTIC ===\n\n";

// 1. Check if issues are being created
echo "1. CHECKING RECENT ISSUES:\n";
$result = $db->select(
    "SELECT id, issue_key, summary, project_id, created_at FROM issues ORDER BY created_at DESC LIMIT 5"
);
echo "Recent issues count: " . count($result) . "\n";
foreach ($result as $issue) {
    echo "  - {$issue['issue_key']}: {$issue['summary']} (created at {$issue['created_at']})\n";
}

// 2. Check if the form is sending the right data
echo "\n2. CHECKING FORM REQUEST DATA:\n";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            echo "  {$key}: Array (" . count($value) . " items)\n";
        } else {
            echo "  {$key}: " . substr((string) $value, 0, 100) . "\n";
        }
    }
    
    echo "\nFILES data received:\n";
    if (!empty($_FILES)) {
        foreach ($_FILES as $key => $files) {
            if (is_array($files['name'])) {
                echo "  {$key}: Array (" . count($files['name']) . " files)\n";
                foreach ($files['name'] as $i => $name) {
                    echo "    [$i] {$name} (" . $files['size'][$i] . " bytes)\n";
                }
            } else {
                echo "  {$key}: {$files['name']} (" . $files['size'] . " bytes)\n";
            }
        }
    } else {
        echo "  No files uploaded\n";
    }
}

// 3. Check projects in database
echo "\n3. CHECKING PROJECTS:\n";
$projects = $db->select("SELECT id, `key`, name FROM projects LIMIT 10");
echo "Projects count: " . count($projects) . "\n";
foreach ($projects as $proj) {
    echo "  - {$proj['key']}: {$proj['name']} (ID: {$proj['id']})\n";
}

// 4. Check issue types
echo "\n4. CHECKING ISSUE TYPES:\n";
$types = $db->select("SELECT id, name FROM issue_types LIMIT 10");
echo "Issue types count: " . count($types) . "\n";
foreach ($types as $type) {
    echo "  - ID {$type['id']}: {$type['name']}\n";
}

// 5. Test issue creation directly
echo "\n5. TEST DIRECT ISSUE CREATION:\n";
if ($_POST && isset($_POST['summary'])) {
    try {
        $projectId = (int) ($_POST['project_id'] ?? 0);
        $issueTypeId = (int) ($_POST['issue_type_id'] ?? 0);
        $summary = $_POST['summary'] ?? '';
        $description = $_POST['description'] ?? '';
        
        echo "Attempting to create issue:\n";
        echo "  Project ID: $projectId\n";
        echo "  Issue Type ID: $issueTypeId\n";
        echo "  Summary: " . substr($summary, 0, 50) . "\n";
        echo "  Description: " . substr($description, 0, 50) . "\n";
        
        if ($projectId && $issueTypeId && $summary) {
            // Get the next issue key
            $projectKey = $db->select(
                "SELECT `key` FROM projects WHERE id = ?",
                [$projectId]
            );
            if ($projectKey) {
                $count = $db->selectOne(
                    "SELECT COUNT(*) as cnt FROM issues WHERE project_id = ?",
                    [$projectId]
                );
                $nextNum = ($count['cnt'] ?? 0) + 1;
                $newKey = $projectKey[0]['key'] . '-' . $nextNum;
                
                echo "\n✓ Would create issue: $newKey\n";
            }
        } else {
            echo "\n✗ Missing required fields!\n";
        }
    } catch (\Exception $e) {
        echo "\n✗ Error: " . $e->getMessage() . "\n";
    }
}

// 6. Check last error in PHP logs
echo "\n6. RECENT PHP ERRORS:\n";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $lines = file($errorLog);
    $recent = array_slice($lines, -10);
    foreach ($recent as $line) {
        echo "  " . trim($line) . "\n";
    }
} else {
    echo "  No error log file found\n";
}

echo "\n=== END DIAGNOSTIC ===\n";
?>
