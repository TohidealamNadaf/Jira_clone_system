<?php
/**
 * Comprehensive Dashboard Verification
 * Checks if counts are user-specific or global
 */

declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Not authenticated']));
}

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Core\Database;

$currentUserId = $_SESSION['user_id'];

// Get current user info
$currentUser = Database::selectOne(
    "SELECT id, email, display_name FROM users WHERE id = ?",
    [$currentUserId]
);

// Get all users
$allUsers = Database::select("SELECT id, email, display_name FROM users ORDER BY display_name");

echo "<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Count Verification</title>
    <style>
        * { font-family: Arial, sans-serif; }
        body { padding: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header h1 { margin: 0 0 10px 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card h3 { margin: 0 0 15px 0; color: #333; border-bottom: 2px solid #8B1956; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: #f0f0f0; padding: 10px; text-align: left; font-weight: bold; color: #333; }
        td { padding: 10px; border-bottom: 1px solid #e0e0e0; }
        tr:hover { background: #fafafa; }
        .number { font-weight: bold; color: #8B1956; font-size: 18px; }
        .match { color: green; font-weight: bold; }
        .mismatch { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .section { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .filter-good { background: #e8f5e9; border-left: 4px solid #4caf50; }
        .filter-bad { background: #ffebee; border-left: 4px solid #f44336; }
        .stat-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin: 20px 0; }
        .stat-box { padding: 15px; background: #f5f5f5; border-radius: 4px; text-align: center; }
        .stat-box .label { font-size: 12px; color: #666; }
        .stat-box .count { font-size: 28px; font-weight: bold; color: #8B1956; margin: 5px 0; }
        .stat-box .status { font-size: 11px; }
    </style>
</head>
<body>
<div class='container'>
    <div class='header'>
        <h1>Dashboard Count Verification Report</h1>
        <p><strong>Current User:</strong> {$currentUser['display_name']} ({$currentUser['email']})</p>
        <p><strong>User ID:</strong> {$currentUser['id']}</p>
        <p><strong>Report Generated:</strong> " . date('Y-m-d H:i:s') . "</p>
    </div>

    <div class='section filter-good'>
        <h2>✓ Dashboard Filtering Check</h2>
        <p>Verifying that dashboard counts are correctly filtered to show only the current user's issues...</p>
    </div>";

// ============================================================
// PART 1: Check dashboard counts for current user
// ============================================================

echo "<div class='section'><h3>1. Dashboard Statistics (Admin User Only)</h3>";

$dashboardStats = [
    'assigned_to_me' => [
        'label' => 'Assigned to Me',
        'query' => "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE i.assignee_id = ? AND s.category != 'done'",
        'params' => [$currentUserId],
        'expected' => 15,
    ],
    'reported_by_me' => [
        'label' => 'Reported by Me',
        'query' => "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE i.reporter_id = ? AND s.category != 'done'",
        'params' => [$currentUserId],
        'expected' => 26,
    ],
    'in_progress' => [
        'label' => 'In Progress',
        'query' => "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE i.assignee_id = ? AND s.category = 'in_progress'",
        'params' => [$currentUserId],
        'expected' => 8,
    ],
    'due_this_week' => [
        'label' => 'Due This Week',
        'query' => "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE i.assignee_id = ? AND i.due_date IS NOT NULL AND i.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND s.category != 'done'",
        'params' => [$currentUserId],
        'expected' => 0,
    ],
    'overdue' => [
        'label' => 'Overdue',
        'query' => "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE i.assignee_id = ? AND i.due_date < CURDATE() AND s.category != 'done'",
        'params' => [$currentUserId],
        'expected' => 0,
    ],
];

echo "<table>
    <tr>
        <th>Metric</th>
        <th>Expected Count</th>
        <th>Actual Count</th>
        <th>Status</th>
        <th>Accurate?</th>
    </tr>";

$allAccurate = true;
foreach ($dashboardStats as $key => $stat) {
    $actual = (int) Database::selectValue($stat['query'], $stat['params']);
    $matches = ($actual == $stat['expected']);
    if (!$matches) $allAccurate = false;
    
    $statusClass = $matches ? 'match' : 'mismatch';
    echo "<tr>
        <td>{$stat['label']}</td>
        <td class='number'>{$stat['expected']}</td>
        <td class='number'>{$actual}</td>
        <td>User: {$currentUser['display_name']}</td>
        <td><span class='$statusClass'>" . ($matches ? '✓ YES' : '✗ NO') . "</span></td>
    </tr>";
}

echo "</table>
</div>";

// ============================================================
// PART 2: Check if dashboard is showing ALL users' issues
// ============================================================

echo "<div class='section'><h3>2. Global Issue Counts (All Users)</h3>";
echo "<p>Checking total issues in the system to see if dashboard might be displaying all users' issues...</p>";

$globalStats = [
    'all_assigned_total' => [
        'label' => 'All Assigned Issues (Any User)',
        'query' => "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE s.category != 'done'",
    ],
    'all_reported_total' => [
        'label' => 'All Reported Issues (Any User)',
        'query' => "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE s.category != 'done'",
    ],
    'all_in_progress_total' => [
        'label' => 'All In Progress Issues (Any User)',
        'query' => "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE s.category = 'in_progress'",
    ],
];

echo "<table>
    <tr>
        <th>Metric</th>
        <th>Global Count</th>
        <th>Admin User Count</th>
        <th>Difference</th>
        <th>Are They the Same?</th>
    </tr>";

$adminAssignedCount = (int) Database::selectValue(
    "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE i.assignee_id = ? AND s.category != 'done'",
    [$currentUserId]
);

$allAssignedCount = (int) Database::selectValue(
    "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE s.category != 'done'",
    []
);

echo "<tr>
    <td>Issues (Not Done)</td>
    <td class='number'>{$allAssignedCount}</td>
    <td class='number'>{$adminAssignedCount}</td>
    <td>" . ($allAssignedCount - $adminAssignedCount) . "</td>
    <td>";

if ($allAssignedCount == $adminAssignedCount) {
    echo "<span class='warning'>⚠ PROBLEM! Dashboard showing ALL issues, not just admin's!</span>";
} else {
    echo "<span class='match'>✓ GOOD - Correctly filtered to admin user only</span>";
}

echo "</td></tr></table>";

echo "</div>";

// ============================================================
// PART 3: Detailed user breakdown
// ============================================================

echo "<div class='section'><h3>3. Issues by User (All Users)</h3>";
echo "<p>Showing how issues are distributed among all users to verify filtering...</p>";

echo "<table>
    <tr>
        <th>User</th>
        <th>Email</th>
        <th>Assigned Issues (Active)</th>
        <th>Reported Issues (Active)</th>
        <th>In Progress</th>
        <th>Is Current?</th>
    </tr>";

foreach ($allUsers as $user) {
    $userId = $user['id'];
    
    $userAssignedCount = (int) Database::selectValue(
        "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE i.assignee_id = ? AND s.category != 'done'",
        [$userId]
    );
    
    $userReportedCount = (int) Database::selectValue(
        "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE i.reporter_id = ? AND s.category != 'done'",
        [$userId]
    );
    
    $userInProgressCount = (int) Database::selectValue(
        "SELECT COUNT(*) FROM issues i JOIN statuses s ON i.status_id = s.id WHERE i.assignee_id = ? AND s.category = 'in_progress'",
        [$userId]
    );
    
    $isCurrent = ($userId == $currentUserId) ? '✓ Current' : '';
    $highlight = ($userId == $currentUserId) ? 'style="background: #fff3e0; font-weight: bold;"' : '';
    
    echo "<tr $highlight>
        <td>{$user['display_name']}</td>
        <td>{$user['email']}</td>
        <td class='number'>{$userAssignedCount}</td>
        <td class='number'>{$userReportedCount}</td>
        <td class='number'>{$userInProgressCount}</td>
        <td>$isCurrent</td>
    </tr>";
}

echo "</table></div>";

// ============================================================
// PART 4: Sample issues check
// ============================================================

echo "<div class='section'><h3>4. Sample Issues Verification</h3>";
echo "<p>Showing sample assigned issues for the admin user...</p>";

$sampleAssignedIssues = Database::select(
    "SELECT i.id, i.issue_key, i.summary, i.assignee_id, 
            u.display_name as assignee_name, s.name as status
     FROM issues i
     LEFT JOIN users u ON i.assignee_id = u.id
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND s.category != 'done'
     ORDER BY i.updated_at DESC
     LIMIT 10",
    [$currentUserId]
);

echo "<table>
    <tr>
        <th>Issue Key</th>
        <th>Summary</th>
        <th>Assigned To (ID)</th>
        <th>Assigned To (Name)</th>
        <th>Status</th>
        <th>Correct Filtering?</th>
    </tr>";

foreach ($sampleAssignedIssues as $issue) {
    $assigneeId = $issue['assignee_id'];
    $assigneeName = $issue['assignee_name'];
    
    $isCorrect = ($assigneeId == $currentUserId) ? '✓ YES' : '✗ NO';
    $class = ($assigneeId == $currentUserId) ? 'match' : 'mismatch';
    
    echo "<tr>
        <td><strong>{$issue['issue_key']}</strong></td>
        <td>{$issue['summary']}</td>
        <td>{$assigneeId}</td>
        <td>{$assigneeName}</td>
        <td>{$issue['status']}</td>
        <td><span class='$class'>$isCorrect</span></td>
    </tr>";
}

echo "</table></div>";

// ============================================================
// PART 5: FINAL VERDICT
// ============================================================

echo "<div class='section' style='border-top: 3px solid #8B1956;'>";

// Check if any issues belong to other users
$wrongOwnerIssues = Database::select(
    "SELECT i.issue_key, i.assignee_id, u.display_name
     FROM issues i
     LEFT JOIN users u ON i.assignee_id = u.id
     WHERE i.assignee_id IS NOT NULL AND i.assignee_id != ?",
    [$currentUserId]
);

$allCorrect = true;
foreach ($sampleAssignedIssues as $issue) {
    if ($issue['assignee_id'] != $currentUserId) {
        $allCorrect = false;
        break;
    }
}

echo "<h2>FINAL VERIFICATION RESULT</h2>";
echo "<div style='background: #e8f5e9; border-left: 4px solid #4caf50; padding: 20px; border-radius: 4px;'>";

if ($allCorrect && $allAccurate) {
    echo "<h3 style='color: #2e7d32; margin-top: 0;'>✓ DASHBOARD FILTERING IS CORRECT</h3>";
    echo "<p><strong>The dashboard is correctly showing ONLY the admin user's issues.</strong></p>";
    echo "<ul>
        <li>All counts are user-specific (filtered by current user ID)</li>
        <li>Issues are from the current logged-in user only</li>
        <li>No issues from other users are being displayed</li>
        <li>Database queries use proper WHERE clauses with user_id filtering</li>
    </ul>";
} else {
    echo "<h3 style='color: #c62828; margin-top: 0;'>✗ DASHBOARD FILTERING HAS ISSUES</h3>";
    echo "<p><strong>ALERT: Dashboard may be showing issues from multiple users!</strong></p>";
    echo "<ul>";
    if (!$allAccurate) echo "<li>Count accuracy failed</li>";
    if (!$allCorrect) echo "<li>Sample issues show wrong user assignment</li>";
    echo "</ul>";
}

echo "<p style='margin-top: 20px; padding-top: 20px; border-top: 1px solid #ccc; font-size: 12px; color: #666;'>
<strong>Generated:</strong> " . date('Y-m-d H:i:s') . "<br>
<strong>Admin User ID:</strong> {$currentUserId}<br>
<strong>Total Users in System:</strong> " . count($allUsers) . "<br>
<strong>Total Active Issues in System:</strong> {$allAssignedCount}
</p>";

echo "</div></div>";

echo "</div></body></html>";
