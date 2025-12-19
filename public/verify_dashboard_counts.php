<?php
/**
 * Dashboard Count Verification - Web Version
 */

declare(strict_types=1);

// Start session
session_start();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Not authenticated']));
}

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Core\Database;

$userId = $_SESSION['user_id'];

// Get user info
$user = Database::selectOne(
    "SELECT id, email, display_name FROM users WHERE id = ?",
    [$userId]
);

if (!$user) {
    http_response_code(404);
    die(json_encode(['error' => 'User not found']));
}

// Collect all statistics
$stats = [
    'user' => [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['display_name'],
    ],
    'date' => date('Y-m-d'),
    'time' => date('H:i:s'),
    'dashboard_display' => [
        'assigned_to_me' => 15,
        'reported_by_me' => 26,
        'in_progress' => 8,
        'due_this_week' => 0,
        'overdue' => 0,
    ],
    'actual_counts' => [],
    'detailed_breakdown' => [],
];

// TEST 1: Assigned to Me
$assigned_count = (int) Database::selectValue(
    "SELECT COUNT(*) FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND s.category != 'done'",
    [$userId]
);

$stats['actual_counts']['assigned_to_me'] = $assigned_count;
$stats['detailed_breakdown']['assigned_to_me'] = Database::select(
    "SELECT i.issue_key, i.summary, s.name as status, pr.name as priority
     FROM issues i
     JOIN statuses s ON i.status_id = s.id
     LEFT JOIN issue_priorities pr ON i.priority_id = pr.id
     WHERE i.assignee_id = ? AND s.category != 'done'
     ORDER BY pr.sort_order ASC, i.updated_at DESC",
    [$userId]
);

// TEST 2: Reported by Me
$reported_count = (int) Database::selectValue(
    "SELECT COUNT(*) FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.reporter_id = ? AND s.category != 'done'",
    [$userId]
);

$stats['actual_counts']['reported_by_me'] = $reported_count;
$stats['detailed_breakdown']['reported_by_me'] = Database::select(
    "SELECT i.issue_key, i.summary, s.name as status
     FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.reporter_id = ? AND s.category != 'done'
     ORDER BY i.updated_at DESC",
    [$userId]
);

// TEST 3: In Progress
$in_progress_count = (int) Database::selectValue(
    "SELECT COUNT(*) FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND s.category = 'in_progress'",
    [$userId]
);

$stats['actual_counts']['in_progress'] = $in_progress_count;
$stats['detailed_breakdown']['in_progress'] = Database::select(
    "SELECT i.issue_key, i.summary
     FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND s.category = 'in_progress'",
    [$userId]
);

// TEST 4: Due This Week
$due_soon_count = (int) Database::selectValue(
    "SELECT COUNT(*) FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND i.due_date IS NOT NULL 
     AND i.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
     AND s.category != 'done'",
    [$userId]
);

$stats['actual_counts']['due_this_week'] = $due_soon_count;
$stats['detailed_breakdown']['due_this_week'] = Database::select(
    "SELECT i.issue_key, i.summary, i.due_date
     FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND i.due_date IS NOT NULL 
     AND i.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
     AND s.category != 'done'",
    [$userId]
);

// TEST 5: Overdue
$overdue_count = (int) Database::selectValue(
    "SELECT COUNT(*) FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND i.due_date < CURDATE() AND s.category != 'done'",
    [$userId]
);

$stats['actual_counts']['overdue'] = $overdue_count;
$stats['detailed_breakdown']['overdue'] = Database::select(
    "SELECT i.issue_key, i.summary, i.due_date
     FROM issues i
     JOIN statuses s ON i.status_id = s.id
     WHERE i.assignee_id = ? AND i.due_date < CURDATE() AND s.category != 'done'",
    [$userId]
);

// Calculate accuracy
$stats['accuracy'] = [];
foreach ($stats['dashboard_display'] as $key => $expected) {
    $actual = $stats['actual_counts'][$key] ?? 0;
    $stats['accuracy'][$key] = [
        'expected' => $expected,
        'actual' => $actual,
        'matches' => $expected === $actual,
        'difference' => $actual - $expected,
    ];
}

// Overall result
$all_match = true;
foreach ($stats['accuracy'] as $result) {
    if (!$result['matches']) {
        $all_match = false;
        break;
    }
}

$stats['overall_accurate'] = $all_match;

// Output JSON
header('Content-Type: application/json');
echo json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
