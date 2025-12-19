<?php
// Verify time tracking modal fix

echo "=== TIME TRACKING MODAL FIX VERIFICATION ===\n\n";

// 1. Check if view file was updated
$viewFile = __DIR__ . '/views/time-tracking/project-report.php';
$content = file_get_contents($viewFile);

if (strpos($content, 'getIssuesByProject') !== false) {
    echo "✓ View file contains getIssuesByProject call\n";
} else if (strpos($content, '$issueService->getIssues(') !== false) {
    echo "✓ View file uses correct getIssues method\n";
} else {
    echo "✗ View file not updated properly\n";
}

// 2. Check if $modalIssues is used
if (strpos($content, 'foreach ($modalIssues as $issue)') !== false) {
    echo "✓ Modal dropdown uses \$modalIssues\n";
} else if (strpos($content, 'foreach ($byIssue as $issue)') !== false) {
    echo "✗ Modal still using \$byIssue (old behavior)\n";
} else {
    echo "? Could not find modal loop\n";
}

// 3. Check for fallback
if (strpos($content, 'array_values($byIssue)') !== false) {
    echo "✓ Fallback to \$byIssue included\n";
} else {
    echo "✗ No fallback mechanism\n";
}

echo "\n=== EXPECTED BEHAVIOR ===\n";
echo "When visiting time-tracking/project/1:\n";
echo "  1. Modal should fetch ALL active issues from project\n";
echo "  2. Dropdown should show all issues (not just those with time logs)\n";
echo "  3. If fetch fails, fallback to issues from time logs\n";

echo "\n=== TO TEST ===\n";
echo "  1. Navigate to: http://localhost:8081/jira_clone_system/public/time-tracking/project/1\n";
echo "  2. Click 'Start Timer' button\n";
echo "  3. Check if issue dropdown is now populated\n";
echo "  4. Select an issue and click 'Start Timer'\n";

?>
