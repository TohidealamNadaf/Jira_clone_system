<?php

declare(strict_types=1);

\App\Core\View::extends('layouts.app');

\App\Core\View::section('content');

// Project Time Tracking Report
// Shows aggregated time and cost data for a project

$project = $project ?? null;
$timeLogs = $timeLogs ?? [];
$budget = $budget ?? null;
$statistics = $statistics ?? [];

if (!$project) {
    echo '<div class="alert alert-danger">Project not found</div>';
    return;
}

// Calculate totals and aggregates
$totalSeconds = array_sum(array_map(fn($log) => $log['duration_seconds'] ?? 0, $timeLogs));
$totalCost = array_sum(array_map(fn($log) => $log['total_cost'] ?? 0, $timeLogs));

// Group by user
$byUser = [];
foreach ($timeLogs as $log) {
    $userId = $log['user_id'];
    if (!isset($byUser[$userId])) {
        $byUser[$userId] = [
            'user_name' => $log['user_name'] ?? 'Unknown',
            'total_seconds' => 0,
            'total_cost' => 0,
            'count' => 0
        ];
    }
    $byUser[$userId]['total_seconds'] += $log['duration_seconds'] ?? 0;
    $byUser[$userId]['total_cost'] += $log['total_cost'] ?? 0;
    $byUser[$userId]['count']++;
}

// Group by issue (FOR REPORTING - from time logs)
$byIssue = [];
foreach ($timeLogs as $log) {
    $issueId = $log['issue_id'];
    if (!isset($byIssue[$issueId])) {
        $byIssue[$issueId] = [
            'issue_key' => $log['issue_key'],
            'issue_summary' => $log['issue_summary'],
            'total_seconds' => 0,
            'total_cost' => 0,
            'count' => 0
        ];
    }
    $byIssue[$issueId]['total_seconds'] += $log['duration_seconds'] ?? 0;
    $byIssue[$issueId]['total_cost'] += $log['total_cost'] ?? 0;
    $byIssue[$issueId]['count']++;
}

// Get ALL ACTIVE ISSUES for the timer modal dropdown (includes issues with NO time logs)
$modalIssues = [];
try {
    $issueService = new \App\Services\IssueService();
    $projectId = (int)$project['id'];
    
    // Debug: Log the project ID we're looking for
    error_log('TIME_TRACKING: Loading issues for projectId=' . $projectId);
    
    // Get all issues for this project (filter by project_id)
    // Note: getIssues returns paginated response with 'data' key
    $response = $issueService->getIssues(
        ['project_id' => $projectId],
        'key',
        'ASC',
        1,
        1000  // Get up to 1000 issues
    );
    
    // Extract issues from paginated response
    $allIssues = $response['data'] ?? [];
    
    // Debug: Log what we got back
    error_log('TIME_TRACKING: getIssues returned ' . count($allIssues) . ' issues (total: ' . ($response['total'] ?? 0) . ')');
    
    // Include ALL issues regardless of status (users might want to log time on any issue)
    foreach ($allIssues as $issue) {
        if (!empty($issue['key']) && !empty($issue['summary'])) {
            $modalIssues[] = [
                'issue_key' => $issue['key'],
                'issue_summary' => $issue['summary'],
                'issue_id' => $issue['id'],
                'status_name' => $issue['status_name'] ?? 'Unknown'
            ];
        }
    }
    
    // Sort by issue key
    if (!empty($modalIssues)) {
        usort($modalIssues, fn($a, $b) => strnatcmp($a['issue_key'] ?? '', $b['issue_key'] ?? ''));
    }
    
    error_log('TIME_TRACKING: Modal loaded with ' . count($modalIssues) . ' formatted issues');
    
} catch (Exception $e) {
    // Log the full error for debugging
    error_log('TIME_TRACKING: IssueService error: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
    // Fallback: use issues from time logs if service fails
    $modalIssues = array_values($byIssue);
    error_log('TIME_TRACKING: Falling back to time logs. Found ' . count($modalIssues) . ' issues');
}

?>

<div class="page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-sep">/</span>
        <a href="<?= url('/projects/' . $project['key']) ?>" class="breadcrumb-link">
            <?= htmlspecialchars($project['name']) ?>
        </a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Time Tracking</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">⏱️ Time Tracking</h1>
            <p class="page-subtitle">Project time and cost analysis</p>
        </div>
        <div class="header-actions">
            <a href="<?= url('/projects/' . $project['key']) ?>" class="action-button">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
        </div>
    </div>

    <!-- Quick Timer Widget (Sticky) -->
    <div class="quick-timer-banner">
        <div class="timer-container">
            <div class="timer-icon">⏱️</div>
            <div class="timer-info">
                <h3 class="timer-title">Start Logging Time</h3>
                <p class="timer-description">Select an issue to start tracking time on this project</p>
            </div>
            <div class="timer-controls">
                <button class="timer-btn timer-btn-primary" onclick="openTimerModal()">
                    <i class="bi bi-play-fill"></i> Start Timer
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="page-content">
        <!-- Left Column: Main Content -->
        <div class="content-left">
            <!-- Budget Status Card -->
            <?php if ($budget && $budget['id']): ?>
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">💰 Budget Status</h3>
                </div>
                <div class="card-body">
                    <div class="budget-grid">
                        <div class="budget-item">
                            <p class="budget-label">Total Budget</p>
                            <h4 class="budget-value"><?= number_format($budget['total_budget'] ?? 0, 2) ?></h4>
                        </div>
                        <div class="budget-item">
                            <p class="budget-label">Total Cost</p>
                            <h4 class="budget-value"><?= number_format($budget['total_cost'] ?? 0, 2) ?></h4>
                        </div>
                        <div class="budget-item">
                            <p class="budget-label">Remaining</p>
                            <h4 class="budget-value <?= ($budget['total_budget'] ?? 0) - ($budget['total_cost'] ?? 0) > 0 ? 'text-success' : 'text-danger' ?>">
                                <?= number_format(($budget['total_budget'] ?? 0) - ($budget['total_cost'] ?? 0), 2) ?>
                            </h4>
                        </div>
                        <div class="budget-item">
                            <p class="budget-label">Usage</p>
                            <div class="budget-progress">
                                <?php 
                                $percent = ($budget['total_budget'] ?? 0) > 0 
                                    ? (($budget['total_cost'] ?? 0) / ($budget['total_budget'] ?? 0)) * 100 
                                    : 0;
                                $barColor = $percent > 100 ? '#d32f2f' : ($percent > 80 ? '#f57c00' : '#388e3c');
                                ?>
                                <div class="progress-bar" style="width: <?= min($percent, 100) ?>%; background-color: <?= $barColor ?>"></div>
                                <span class="progress-text"><?= number_format($percent, 1) ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Statistics Cards Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">⏱️</div>
                    <div class="stat-content">
                        <h4 class="stat-value">
                            <?php
                            $hours = intdiv($totalSeconds, 3600);
                            $minutes = intdiv($totalSeconds % 3600, 60);
                            echo sprintf('%d:%02dh', $hours, $minutes);
                            ?>
                        </h4>
                        <p class="stat-label">Total Time</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">💵</div>
                    <div class="stat-content">
                        <h4 class="stat-value"><?= number_format($totalCost, 2) ?></h4>
                        <p class="stat-label">Total Cost</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📝</div>
                    <div class="stat-content">
                        <h4 class="stat-value"><?= count($timeLogs) ?></h4>
                        <p class="stat-label">Entries</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⌛</div>
                    <div class="stat-content">
                        <h4 class="stat-value">
                            <?php
                            if (count($timeLogs) > 0) {
                                $avgSeconds = intdiv($totalSeconds, count($timeLogs));
                                $mins = intdiv($avgSeconds, 60);
                                echo $mins . 'm';
                            } else {
                                echo '—';
                            }
                            ?>
                        </h4>
                        <p class="stat-label">Avg/Entry</p>
                    </div>
                </div>
            </div>

            <!-- Time Logs by Team Member -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">👥 Time by Team Member</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($byUser)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <p class="empty-text">No time logs yet</p>
                    </div>
                    <?php else: ?>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th class="text-right">Hours</th>
                                    <th class="text-right">Cost</th>
                                    <th class="text-center">Entries</th>
                                    <th class="text-right">Avg</th>
                                    <th class="text-right">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($byUser as $userData): ?>
                                <tr>
                                    <td><?= htmlspecialchars($userData['user_name']) ?></td>
                                    <td class="text-right"><?= number_format($userData['total_seconds'] / 3600, 2) ?></td>
                                    <td class="text-right"><?= number_format($userData['total_cost'], 2) ?></td>
                                    <td class="text-center"><span class="badge badge-sm"><?= $userData['count'] ?></span></td>
                                    <td class="text-right">
                                        <?php 
                                        $avgSecs = intdiv($userData['total_seconds'], $userData['count']);
                                        $avgMins = intdiv($avgSecs, 60);
                                        echo $avgMins . 'm';
                                        ?>
                                    </td>
                                    <td class="text-right">
                                        <?php 
                                        $pct = $totalCost > 0 ? ($userData['total_cost'] / $totalCost) * 100 : 0;
                                        echo number_format($pct, 1) . '%';
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Time Logs by Issue -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">🎯 Time by Issue</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($byIssue)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">🔍</div>
                        <p class="empty-text">No time logs yet</p>
                    </div>
                    <?php else: ?>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Issue</th>
                                    <th class="text-right">Hours</th>
                                    <th class="text-right">Cost</th>
                                    <th class="text-center">Entries</th>
                                    <th class="text-right">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Sort by cost (descending)
                                usort($byIssue, fn($a, $b) => $b['total_cost'] <=> $a['total_cost']);
                                foreach (array_slice($byIssue, 0, 50) as $issue): 
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?= url('/projects/' . $project['key'] . '/issues/' . $issue['issue_key']) ?>" 
                                           class="issue-key">
                                            <?= htmlspecialchars($issue['issue_key']) ?>
                                        </a>
                                        <span class="issue-summary"><?= htmlspecialchars(substr($issue['issue_summary'] ?? '', 0, 50)) ?></span>
                                    </td>
                                    <td class="text-right"><?= number_format($issue['total_seconds'] / 3600, 2) ?></td>
                                    <td class="text-right"><?= number_format($issue['total_cost'], 2) ?></td>
                                    <td class="text-center"><span class="badge badge-sm"><?= $issue['count'] ?></span></td>
                                    <td class="text-right">
                                        <?php 
                                        $pct = $totalCost > 0 ? ($issue['total_cost'] / $totalCost) * 100 : 0;
                                        echo number_format($pct, 1) . '%';
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="content-right">
            <!-- Project Summary Card -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h4 class="card-title">📌 Summary</h4>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Key</span>
                        <span class="info-value"><?= htmlspecialchars($project['key']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Issues</span>
                        <span class="info-value"><?= htmlspecialchars($project['issues_count'] ?? '0') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Category</span>
                        <span class="info-value"><?= htmlspecialchars($project['category'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h4 class="card-title">📊 Stats</h4>
                </div>
                <div class="card-body">
                    <div class="quick-stat">
                        <span class="stat-label">Members</span>
                        <span class="stat-number"><?= count($byUser) ?></span>
                    </div>
                    <div class="quick-stat">
                        <span class="stat-label">Hours</span>
                        <span class="stat-number"><?= intdiv($totalSeconds, 3600) ?></span>
                    </div>
                    <div class="quick-stat">
                        <span class="stat-label">Avg/Hour</span>
                        <span class="stat-number">
                            <?php 
                            if ($totalSeconds > 0) {
                                $costPerHour = $totalCost / ($totalSeconds / 3600);
                                echo '$' . number_format($costPerHour, 2);
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h4 class="card-title">🔧 Actions</h4>
                </div>
                <div class="card-body">
                    <div class="action-list">
                        <a href="<?= url('/time-tracking/dashboard') ?>" class="action-link">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="<?= url('/projects/' . $project['key'] . '/board') ?>" class="action-link">
                            <i class="bi bi-kanban"></i>
                            <span>Board</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================
   TIME TRACKING PROJECT REPORT - COMPACT DESIGN
   ============================================ */

:root {
    --jira-blue: #8B1956;
    --jira-blue-dark: #6F123F;
    --jira-dark: #161B22;
    --jira-gray: #626F86;
    --jira-gray-light: #738496;
    --jira-light: #F7F8FA;
    --jira-border: #DFE1E6;
    --jira-white: #FFFFFF;
}

/* ===== PAGE WRAPPER ===== */
.page-wrapper {
    background: var(--jira-light);
    min-height: 100vh;
}

/* ===== BREADCRUMB NAVIGATION ===== */
.breadcrumb-nav {
    background: var(--jira-white);
    border-bottom: 1px solid var(--jira-border);
    padding: 8px 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
}

.breadcrumb-link {
    color: var(--jira-blue);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: color 0.2s;
}

.breadcrumb-link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

.breadcrumb-sep {
    color: var(--jira-border);
}

.breadcrumb-current {
    color: var(--jira-gray);
    font-weight: 600;
}

/* ===== PAGE HEADER ===== */
.page-header {
    background: var(--jira-white);
    border-bottom: 1px solid var(--jira-border);
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 22px;
    font-weight: 700;
    color: var(--jira-dark);
    margin: 0 0 4px 0;
    letter-spacing: -0.2px;
}

.page-subtitle {
    font-size: 13px;
    color: var(--jira-gray);
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 8px;
}

.action-button {
    background: var(--jira-white);
    border: 1px solid var(--jira-border);
    color: var(--jira-gray);
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s;
    white-space: nowrap;
}

.action-button:hover {
    background: var(--jira-light);
    border-color: var(--jira-blue);
    color: var(--jira-blue);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

/* ===== PAGE CONTENT ===== */
.page-content {
    display: flex;
    gap: 16px;
    padding: 16px 20px;
}

.content-left {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.content-right {
    width: 240px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* ===== CONTENT CARDS ===== */
.content-card,
.sidebar-card {
    background: var(--jira-white);
    border: 1px solid var(--jira-border);
    border-radius: 6px;
    overflow: hidden;
    transition: all 0.2s;
}

.content-card:hover,
.sidebar-card:hover {
    border-color: #b6c2cf;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.card-header {
    background: var(--jira-light);
    border-bottom: 1px solid var(--jira-border);
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--jira-dark);
    margin: 0;
}

.card-body {
    padding: 12px 14px;
}

/* ===== BUDGET STATUS ===== */
.budget-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 12px;
}

.budget-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.budget-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--jira-gray);
    text-transform: uppercase;
    margin: 0;
    letter-spacing: 0.3px;
}

.budget-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--jira-dark);
    margin: 0;
}

.text-success {
    color: #388e3c !important;
}

.text-danger {
    color: #d32f2f !important;
}

.budget-progress {
    display: flex;
    align-items: center;
    gap: 8px;
}

.progress-bar {
    flex: 1;
    height: 5px;
    background: #e0e0e0;
    border-radius: 2px;
    overflow: hidden;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 10px;
    font-weight: 700;
    color: var(--jira-gray);
    min-width: 35px;
    text-align: right;
}

/* ===== STATISTICS GRID ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
}

.stat-card {
    background: var(--jira-white);
    border: 1px solid var(--jira-border);
    border-radius: 6px;
    padding: 12px;
    display: flex;
    gap: 8px;
    align-items: flex-start;
    transition: all 0.2s;
}

.stat-card:hover {
    border-color: #b6c2cf;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transform: translateY(-1px);
}

.stat-icon {
    font-size: 24px;
    opacity: 0.25;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-value {
    font-size: 18px;
    font-weight: 700;
    color: var(--jira-dark);
    margin: 0 0 2px 0;
    line-height: 1.2;
}

.stat-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--jira-gray);
    margin: 0;
    text-transform: capitalize;
}

/* ===== TABLE STYLING ===== */
.table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.data-table thead {
    background: var(--jira-light);
    border-bottom: 1px solid var(--jira-border);
}

.data-table th {
    padding: 8px 10px;
    text-align: left;
    font-weight: 700;
    color: var(--jira-gray);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.2px;
}

.data-table tbody tr {
    border-bottom: 1px solid var(--jira-border);
    transition: background-color 0.2s;
}

.data-table tbody tr:hover {
    background-color: rgba(139, 25, 86, 0.02);
}

.data-table td {
    padding: 10px;
    color: var(--jira-dark);
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

/* ===== ISSUE CELL ===== */
.issue-key {
    font-size: 12px;
    font-weight: 700;
    color: var(--jira-blue);
    text-decoration: none;
    transition: color 0.2s;
}

.issue-key:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

.issue-summary {
    font-size: 11px;
    color: var(--jira-gray);
    margin-left: 4px;
}

/* ===== BADGES ===== */
.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(98, 111, 134, 0.15);
    color: var(--jira-gray-light);
}

.badge-sm {
    padding: 2px 6px;
    font-size: 10px;
}

/* ===== EMPTY STATE ===== */
.empty-state {
    text-align: center;
    padding: 24px 12px;
    color: var(--jira-gray);
}

.empty-icon {
    font-size: 36px;
    margin-bottom: 8px;
    opacity: 0.4;
}

.empty-text {
    font-size: 13px;
    font-weight: 600;
    margin: 0;
    color: var(--jira-dark);
}

/* ===== SIDEBAR CARD ===== */
.sidebar-card {
    background: var(--jira-white);
    border: 1px solid var(--jira-border);
    border-radius: 6px;
    overflow: hidden;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 8px 0;
    border-bottom: 1px solid var(--jira-border);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--jira-gray);
    text-transform: uppercase;
    letter-spacing: 0.2px;
}

.info-value {
    font-size: 12px;
    font-weight: 600;
    color: var(--jira-dark);
}

/* ===== QUICK STATS ===== */
.quick-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid var(--jira-border);
}

.quick-stat:last-child {
    border-bottom: none;
}

.stat-label {
    font-size: 12px;
    color: var(--jira-gray);
    font-weight: 500;
}

.stat-number {
    font-size: 16px;
    font-weight: 700;
    color: var(--jira-blue);
}

/* ===== ACTION LIST ===== */
.action-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.action-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 8px;
    color: var(--jira-gray);
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.2s;
    font-size: 12px;
}

.action-link:hover {
    background: var(--jira-light);
    color: var(--jira-blue);
}

.action-link i {
    font-size: 14px;
    flex-shrink: 0;
}

/* ===== RESPONSIVE DESIGN ===== */

@media (max-width: 1024px) {
    .page-content {
        flex-direction: column;
    }

    .content-right {
        width: 100%;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .budget-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .page-content {
        padding: 12px 16px;
        gap: 12px;
    }

    .breadcrumb-nav {
        padding: 8px 16px;
        font-size: 11px;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .stat-card {
        padding: 10px;
    }

    .stat-value {
        font-size: 16px;
    }

    .budget-grid {
        grid-template-columns: 1fr;
    }

    .data-table {
        font-size: 11px;
    }

    .data-table th,
    .data-table td {
        padding: 8px 6px;
    }

    .action-button {
        font-size: 11px;
        padding: 5px 10px;
    }
}

@media (max-width: 480px) {
    .page-header {
        padding: 12px 16px;
    }

    .page-title {
        font-size: 18px;
    }

    .page-subtitle {
        font-size: 12px;
    }

    .page-content {
        padding: 12px 16px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 8px;
    }

    .stat-value {
        font-size: 16px;
    }

    .data-table {
        font-size: 10px;
    }

    .data-table th,
    .data-table td {
        padding: 6px 4px;
    }

    .breadcrumb-nav {
        font-size: 10px;
    }

    .card-title {
        font-size: 12px;
    }

    .content-right {
        width: 100%;
    }
}
</style>

<!-- Timer Modal -->
<div id="timerModal" class="timer-modal">
    <div class="timer-modal-content">
        <div class="timer-modal-header">
            <h2 class="timer-modal-title">Start Time Tracking</h2>
            <button class="timer-modal-close" onclick="closeTimerModal()">&times;</button>
        </div>
        <div class="timer-modal-body">
            <div class="form-group">
                <label class="form-label">Select Issue:</label>
                <select id="issueSelect" class="form-control" onchange="loadIssueDetails()">
                    <option value="">-- Choose an issue --</option>
                    <?php foreach ($modalIssues as $issue): ?>
                    <option value="<?= htmlspecialchars($issue['issue_key']) ?>" data-issue-id="<?= htmlspecialchars($issue['issue_id'] ?? '') ?>">
                        <?= htmlspecialchars($issue['issue_key']) ?> - <?= htmlspecialchars(substr($issue['issue_summary'] ?? '', 0, 60)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="issueDetails" class="issue-details" style="display: none;">
                <div class="details-item">
                    <span class="details-label">Issue Key</span>
                    <span class="details-value" id="detailKey">—</span>
                </div>
                <div class="details-item">
                    <span class="details-label">Summary</span>
                    <span class="details-value" id="detailSummary">—</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description (optional):</label>
                <textarea id="timerDescription" class="form-control" placeholder="What are you working on?" rows="3"></textarea>
            </div>

            <div class="timer-instructions">
                <div class="instruction-item">
                    <i class="bi bi-play-circle"></i>
                    <span>Click "Start" to begin tracking time</span>
                </div>
                <div class="instruction-item">
                    <i class="bi bi-pause-circle"></i>
                    <span>Pause anytime and resume later</span>
                </div>
                <div class="instruction-item">
                    <i class="bi bi-stop-circle"></i>
                    <span>Stop when you're done working</span>
                </div>
            </div>
        </div>
        <div class="timer-modal-footer">
            <button class="btn-secondary" onclick="closeTimerModal()">Cancel</button>
            <button class="btn-primary" id="startTimerBtn" onclick="startTimer()" disabled>
                <i class="bi bi-play-fill"></i> Start Timer
            </button>
        </div>
    </div>
</div>

<style>
/* ===== TIMER BANNER ===== */
.quick-timer-banner {
    background: linear-gradient(135deg, #8B1956 0%, #6F123F 100%);
    color: white;
    padding: 14px 20px;
    margin: 0;
    border-bottom: none;
}

.timer-container {
    display: flex;
    align-items: center;
    gap: 14px;
    max-width: 1400px;
    margin: 0 auto;
}

.timer-icon {
    font-size: 26px;
    flex-shrink: 0;
    line-height: 1;
}

.timer-info {
    flex: 1;
}

.timer-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 2px 0;
    color: #FFFFFF;
    letter-spacing: -0.2px;
}

.timer-description {
    font-size: 12px;
    margin: 0;
    color: rgba(255, 255, 255, 0.95);
    font-weight: 400;
}

.timer-controls {
    flex-shrink: 0;
}

.timer-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 7px 16px;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.timer-btn-primary {
    background: white;
    color: #8B1956;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.timer-btn-primary:hover {
    background: #f5f5f5;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.timer-btn-primary:active {
    transform: translateY(0);
}

/* ===== TIMER MODAL ===== */
.timer-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.timer-modal.active {
    display: flex;
}

.timer-modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 500px;
    overflow: hidden;
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.timer-modal-header {
    background: var(--jira-light);
    border-bottom: 1px solid var(--jira-border);
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.timer-modal-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--jira-dark);
    margin: 0;
}

.timer-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: var(--jira-gray);
    cursor: pointer;
    transition: color 0.2s;
    padding: 0;
    width: 30px;
    height: 30px;
}

.timer-modal-close:hover {
    color: var(--jira-dark);
}

.timer-modal-body {
    padding: 20px;
}

.timer-modal-footer {
    padding: 16px 20px;
    border-top: 1px solid var(--jira-border);
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

/* ===== FORM ELEMENTS ===== */
.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--jira-dark);
    margin-bottom: 6px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--jira-border);
    border-radius: 4px;
    font-size: 13px;
    font-family: inherit;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
}

textarea.form-control {
    resize: vertical;
}

/* ===== ISSUE DETAILS ===== */
.issue-details {
    background: var(--jira-light);
    border: 1px solid var(--jira-border);
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 16px;
}

.details-item {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid var(--jira-border);
}

.details-item:last-child {
    border-bottom: none;
}

.details-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--jira-gray);
}

.details-value {
    font-size: 12px;
    color: var(--jira-dark);
    font-weight: 500;
}

/* ===== INSTRUCTIONS ===== */
.timer-instructions {
    background: rgba(139, 25, 86, 0.05);
    border: 1px solid rgba(139, 25, 86, 0.2);
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 16px;
}

.instruction-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
    font-size: 12px;
    color: var(--jira-gray);
}

.instruction-item i {
    color: var(--jira-blue);
    font-size: 14px;
}

/* ===== BUTTONS ===== */
.btn-primary,
.btn-secondary {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: var(--jira-blue);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: var(--jira-blue-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(139, 25, 86, 0.2);
}

.btn-primary:disabled {
    background: #ccc;
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-secondary {
    background: var(--jira-light);
    color: var(--jira-gray);
    border: 1px solid var(--jira-border);
}

.btn-secondary:hover {
    background: #f0f0f0;
    border-color: var(--jira-gray);
}

@media (max-width: 768px) {
    .timer-container {
        flex-wrap: wrap;
        gap: 12px;
    }

    .timer-info {
        flex-basis: 100%;
    }

    .timer-controls {
        width: 100%;
    }

    .timer-btn {
        width: 100%;
        justify-content: center;
    }

    .timer-modal-content {
        width: 95%;
    }
}
</style>

<script>
// Timer Modal Functions
function openTimerModal() {
    document.getElementById('timerModal').classList.add('active');
    // Load issues dynamically if dropdown is empty
    loadIssuesForModal();
}

// Load issues via API if server-side population failed
function loadIssuesForModal() {
    const issueSelect = document.getElementById('issueSelect');
    const projectId = '<?= $project['id'] ?? 0 ?>';
    const projectKey = '<?= htmlspecialchars($project['key']) ?>';
    
    // Check if we already have issues (besides the default "-- Choose --" option)
    const optionCount = issueSelect.options.length;
    if (optionCount > 1) {
        console.log('[TIMER] Issues already loaded (' + (optionCount - 1) + ' options)');
        return;  // Already loaded
    }
    
    console.log('[TIMER] Modal issues dropdown is empty, loading via API...');
    
    // Show loading state
    const originalText = issueSelect.innerHTML;
    issueSelect.innerHTML = '<option value="">Loading issues...</option>';
    issueSelect.disabled = true;
    
    // Fetch issues from API
    const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
    const apiUrl = basePath.replace(/\/$/, '') + '/api/v1/issues?project_id=' + projectId + '&per_page=1000&order_by=key&order=ASC';
    
    console.log('[TIMER] Fetching issues from: ' + apiUrl);
    
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json'
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        console.log('[TIMER] API Response:', data);
        
        if (!data.data || data.data.length === 0) {
            console.warn('[TIMER] No issues found in API response');
            issueSelect.innerHTML = originalText;  // Restore
            issueSelect.disabled = false;
            issueSelect.innerHTML += '<option value="" style="color: red;">No issues found for this project</option>';
            return;
        }
        
        // Clear dropdown and rebuild
        issueSelect.innerHTML = '<option value="">-- Choose an issue --</option>';
        
        // Add issues from API response
        let issueCount = 0;
        data.data.forEach(issue => {
            const option = document.createElement('option');
            
            // Issue key might be in 'key' or 'issue_key' property
            const issueKey = issue.key || issue.issue_key || 'UNKNOWN';
            const issueSummary = issue.summary || issue.issue_summary || '';
            const issueId = issue.id || issue.issue_id || '';
            
            console.log('[TIMER] Adding issue:', {key: issueKey, summary: issueSummary, id: issueId});
            
            option.value = issueKey;
            option.textContent = issueKey + ' - ' + issueSummary.substring(0, 60);
            option.dataset.issueId = issueId;
            option.dataset.issueSummary = issueSummary;
            
            issueSelect.appendChild(option);
            issueCount++;
        });
        
        console.log('[TIMER] Loaded ' + issueCount + ' issues from API');
        issueSelect.disabled = false;
    })
    .catch(error => {
        console.error('[TIMER] Error loading issues:', error);
        issueSelect.innerHTML = originalText;  // Restore
        issueSelect.disabled = false;
        issueSelect.innerHTML += '<option value="" style="color: red;">Error loading issues</option>';
    });
}

function closeTimerModal() {
    document.getElementById('timerModal').classList.remove('active');
    document.getElementById('issueSelect').value = '';
    document.getElementById('timerDescription').value = '';
    document.getElementById('issueDetails').style.display = 'none';
    document.getElementById('startTimerBtn').disabled = true;
}

function loadIssueDetails() {
    const issueSelect = document.getElementById('issueSelect');
    const selectedIndex = issueSelect.selectedIndex;
    const issueOption = issueSelect.options[selectedIndex];
    const issueKey = issueSelect.value;
    const startBtn = document.getElementById('startTimerBtn');
    
    console.log('[TIMER] loadIssueDetails called:',  {
        selectedIndex: selectedIndex,
        value: issueKey,
        optionText: issueOption ? issueOption.textContent : 'no option',
        dataIssueId: issueOption ? issueOption.dataset.issueId : 'none'
    });
    
    if (!issueKey || issueKey === '') {
        console.log('[TIMER] No issue selected');
        document.getElementById('issueDetails').style.display = 'none';
        startBtn.disabled = true;
        return;
    }

    if (!issueOption) {
        console.warn('[TIMER] Selected option not found');
        document.getElementById('issueDetails').style.display = 'none';
        startBtn.disabled = true;
        return;
    }
    
    // Get elements
    const detailKeyEl = document.getElementById('detailKey');
    const detailSummaryEl = document.getElementById('detailSummary');
    
    if (!detailKeyEl || !detailSummaryEl) {
        console.warn('[TIMER] Detail elements not found in DOM');
        return;
    }
    
    // Set the issue key
    detailKeyEl.textContent = issueKey;
    console.log('[TIMER] ✓ Set detail key to:', issueKey);
    
    // Extract summary from option text (format: "KEY - Summary")
    const optionText = issueOption.textContent;
    const summaryParts = optionText.split(' - ');
    const summary = summaryParts.length > 1 ? summaryParts.slice(1).join(' - ') : optionText;
    
    detailSummaryEl.textContent = summary || 'N/A';
    console.log('[TIMER] ✓ Set detail summary to:', summary);
    
    document.getElementById('issueDetails').style.display = 'block';
    startBtn.disabled = false;
    
    console.log('[TIMER] ✓ Issue details loaded successfully');
}

function startTimer() {
    const issueSelect = document.getElementById('issueSelect');
    const issueKey = issueSelect.value;
    const issueId = issueSelect.options[issueSelect.selectedIndex]?.dataset?.issueId || null;
    const description = document.getElementById('timerDescription').value;
    const projectKey = '<?= htmlspecialchars($project['key']) ?>';
    const projectId = '<?= $project['id'] ?? 0 ?>';

    if (!issueKey) {
        alert('Please select an issue');
        return;
    }

    // Show loading state
    const btn = document.getElementById('startTimerBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Starting...';

    // Call API to start timer - use deployment-aware base path from meta tag
    const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
    const apiUrl = basePath.replace(/\/$/, '') + '/api/v1/time-tracking/start';
    
    console.log('[TIMER] Base path:', basePath);
    console.log('[TIMER] Starting timer for issue:', issueKey, '(ID: ' + issueId + ')');
    console.log('[TIMER] API URL:', apiUrl);
    
    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({
            issue_key: issueKey,
            issue_id: issueId,
            description: description,
            project_key: projectKey,
            project_id: projectId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.status === 'running') {
            // Close modal
            closeTimerModal();
            // Show success message
            showNotification('Timer started! You are now tracking time on ' + issueKey, 'success');
            // Reload page after 1 second
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification('Error: ' + (data.message || data.error || 'Unknown error'), 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-fill"></i> Start Timer';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error starting timer: ' + error.message, 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-play-fill"></i> Start Timer';
    });
}

// Helper function to show notifications
function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.setAttribute('role', 'alert');
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at top of page
    const pageContent = document.querySelector('.page-content');
    if (pageContent) {
        pageContent.parentElement.insertBefore(alert, pageContent);
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}

// Close modal on outside click
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('timerModal');
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeTimerModal();
        }
    });
});
</script>

<?php \App\Core\View::endSection(); ?>
