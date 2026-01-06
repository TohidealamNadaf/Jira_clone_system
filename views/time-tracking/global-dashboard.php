<?php

declare(strict_types=1);

\App\Core\View::extends('layouts.app');

\App\Core\View::section('content');

// Global Time Tracking Dashboard - Team & Enterprise Overview
// Purpose: Company-wide time tracking analytics, team performance, insights, and trends
// NOT a project-filtered view (that's the project report page)

$currentUser = auth();
$userStats = $userStats ?? [];
$teamStats = $teamStats ?? [];
$departmentStats = $departmentStats ?? [];
$topIssues = $topIssues ?? [];
$recentLogs = $recentLogs ?? [];
$weeklyTrend = $weeklyTrend ?? [];
$billableAnalysis = $billableAnalysis ?? [];
$costAnalysis = $costAnalysis ?? [];
$topUsers = $topUsers ?? [];
$projectAnalysis = $projectAnalysis ?? [];

// Helper function for currency symbols
$getCurrencySymbol = function($code) {
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'INR' => '₹',
        'AUD' => '$',
        'CAD' => '$',
        'SGD' => '$',
        'JPY' => '¥'
    ];
    return $symbols[strtoupper($code)] ?? $code;
};

// Helper function to format time
$formatTime = function($seconds) {
    $seconds = (int) $seconds;  // Ensure seconds is an integer
    $h = intdiv($seconds, 3600);
    $m = intdiv($seconds % 3600, 60);
    return sprintf('%d:%02dh', $h, $m);
};

?>

<style>
    /* ====================================== 
       CSS Variables - Enterprise Design System
       ====================================== */
    :root {
        --jira-blue: #8B1956;
        --jira-blue-dark: #6B0F44;
        --jira-blue-light: #F0DCE5;
        --color-warning: #E77817;
        --color-success: #216E4E;
        --color-error: #ED3C32;
        --color-info: #0055CC;
        --text-primary: #161B22;
        --text-secondary: #626F86;
        --text-tertiary: #738496;
        --bg-primary: #FFFFFF;
        --bg-secondary: #F7F8FA;
        --bg-tertiary: #EBEDF0;
        --border-color: #DFE1E6;
        --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
        --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
        --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
        --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        box-sizing: border-box;
    }

    /* ====================================== 
       Page Wrapper
       ====================================== */
    .gtd-wrapper {
        background-color: var(--bg-secondary);
        min-height: calc(100vh - 80px);
        display: flex;
        flex-direction: column;
        padding: 0;
        margin: 0;
    }

    /* ====================================== 
       Breadcrumb Navigation
       ====================================== */
    .gtd-breadcrumb {
        padding: 12px 40px;
        background-color: var(--bg-primary);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        flex-wrap: wrap;
    }

    .breadcrumb-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        transition: color var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--text-secondary);
        margin: 0 4px;
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* ====================================== 
       Page Header
       ====================================== */
    .gtd-header {
        background-color: var(--bg-primary);
        padding: 32px 40px;
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    .gtd-header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 32px;
    }

    .gtd-header-left {
        flex: 1;
    }

    .gtd-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        line-height: 1.3;
        letter-spacing: -0.5px;
    }

    .gtd-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 8px 0 0 0;
        padding: 0;
        line-height: 1.5;
        font-weight: 400;
    }

    .gtd-header-right {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    /* ====================================== 
       Main Content
       ====================================== */
    .gtd-content {
        flex: 1;
        padding: 24px 40px;
        overflow-y: auto;
    }

    /* ====================================== 
       Buttons
       ====================================== */
    .btn-gtd {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 4px;
        cursor: pointer;
        border: none;
        transition: all var(--transition);
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-primary-gtd {
        background-color: var(--jira-blue);
        color: var(--bg-primary);
    }

    .btn-primary-gtd:hover {
        background-color: var(--jira-blue-dark);
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .btn-secondary-gtd {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary-gtd:hover {
        background-color: var(--bg-secondary);
        border-color: var(--jira-blue);
        color: var(--jira-blue);
    }

    /* ====================================== 
       Filter & Control Bar
       ====================================== */
    .gtd-filters {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
        box-shadow: var(--shadow-sm);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        padding: 8px 12px;
        font-size: 13px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background-color: var(--bg-primary);
        color: var(--text-primary);
        cursor: pointer;
        transition: all var(--transition);
    }

    .filter-select:hover {
        border-color: var(--jira-blue);
        background-color: var(--bg-secondary);
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
    }

    /* ====================================== 
       Metrics Grid
       ====================================== */
    .gtd-metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .gtd-metric-card {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 20px 24px;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .gtd-metric-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
        border-color: var(--jira-blue);
    }

    .gtd-metric-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .gtd-metric-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        font-family: 'Monaco', 'Menlo', monospace;
    }

    .gtd-metric-subtext {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 0;
    }

    .gtd-metric-trend {
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    .gtd-metric-trend.up {
        color: var(--color-success);
    }

    .gtd-metric-trend.down {
        color: var(--color-error);
    }

    /* ====================================== 
       Section Container
       ====================================== */
    .gtd-section {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .gtd-section-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background-color: var(--bg-secondary);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .gtd-section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .gtd-section-subtitle {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 4px 0 0 0;
    }

    .gtd-section-content {
        padding: 20px 24px;
    }

    /* ====================================== 
       Tables
       ====================================== */
    .gtd-table-wrapper {
        overflow-x: auto;
    }

    .gtd-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .gtd-table thead {
        background-color: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .gtd-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary);
        white-space: nowrap;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }

    .gtd-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    .gtd-table tbody tr:hover {
        background-color: var(--bg-secondary);
    }

    .gtd-table tbody tr:last-child td {
        border-bottom: none;
    }

    .gtd-cell-user {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .gtd-cell-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: var(--jira-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 12px;
        flex-shrink: 0;
    }

    .gtd-cell-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
    }

    .gtd-cell-link:hover {
        text-decoration: underline;
    }

    .gtd-cell-mono {
        font-family: 'Monaco', 'Menlo', monospace;
        font-weight: 600;
        color: var(--jira-blue);
    }

    .gtd-cell-currency {
        font-weight: 700;
        color: var(--text-primary);
    }

    /* ====================================== 
       Badges
       ====================================== */
    .gtd-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .gtd-badge-success {
        background-color: #E3F2FD;
        color: #1565C0;
    }

    .gtd-badge-warning {
        background-color: #FFF3E0;
        color: #E65100;
    }

    .gtd-badge-danger {
        background-color: #FFEBEE;
        color: #C62828;
    }

    /* ====================================== 
       Charts & Graphs
       ====================================== */
    .gtd-chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 16px;
    }

    .gtd-bar-chart {
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        height: 250px;
        padding: 20px 10px;
        gap: 8px;
    }

    .gtd-bar {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        flex: 1;
        max-width: 60px;
    }

    .gtd-bar-fill {
        width: 100%;
        background: linear-gradient(180deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
        border-radius: 2px 2px 0 0;
        transition: all var(--transition);
    }

    .gtd-bar:hover .gtd-bar-fill {
        opacity: 0.8;
        box-shadow: 0 -4px 8px rgba(139, 25, 86, 0.15);
    }

    .gtd-bar-label {
        font-size: 11px;
        color: var(--text-secondary);
        text-align: center;
        font-weight: 500;
    }

    .gtd-bar-value {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* ====================================== 
       Empty State
       ====================================== */
    .gtd-empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--text-secondary);
    }

    .gtd-empty-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    .gtd-empty-text {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 8px;
        color: var(--text-primary);
    }

    .gtd-empty-hint {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
    }

    /* ====================================== 
       Progress Bar
       ====================================== */
    .gtd-progress-bar {
        height: 6px;
        background-color: var(--bg-tertiary);
        border-radius: 3px;
        overflow: hidden;
        margin-top: 8px;
    }

    .gtd-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
        border-radius: 3px;
        transition: width var(--transition);
    }

    /* ====================================== 
       Grid Layouts
       ====================================== */
    .gtd-grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 24px;
    }

    .gtd-grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    /* ====================================== 
       Responsive Design
       ====================================== */
    @media (max-width: 1400px) {
        .gtd-metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .gtd-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 1024px) {
        .gtd-header-content {
            flex-direction: column;
            gap: 16px;
        }

        .gtd-header-right {
            width: 100%;
            justify-content: flex-start;
        }

        .gtd-metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .gtd-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .gtd-header,
        .gtd-breadcrumb {
            padding: 16px 20px;
        }

        .gtd-content {
            padding: 16px 20px;
        }

        .gtd-title {
            font-size: 24px;
        }

        .gtd-metrics-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .gtd-filters {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-group {
            width: 100%;
        }

        .filter-select {
            width: 100%;
        }

        .gtd-table {
            font-size: 12px;
        }

        .gtd-table th,
        .gtd-table td {
            padding: 8px 12px;
        }

        .gtd-grid-3 {
            grid-template-columns: 1fr;
        }

        .gtd-header-content {
            padding: 0;
        }
    }

    @media (max-width: 480px) {
        .gtd-header {
            padding: 16px;
        }

        .gtd-content {
            padding: 12px 16px;
        }

        .gtd-title {
            font-size: 20px;
        }

        .gtd-subtitle {
            font-size: 12px;
        }

        .gtd-metrics-grid {
            gap: 12px;
        }

        .gtd-metric-card {
            padding: 16px;
        }

        .gtd-metric-value {
            font-size: 22px;
        }

        .gtd-section-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="gtd-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="gtd-breadcrumb">
        <a href="<?= url('/dashboard') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-folder"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Time Tracking Analytics</span>
    </div>

    <!-- Page Header -->
    <div class="gtd-header">
        <div class="gtd-header-content">
            <div class="gtd-header-left">
                <h1 class="gtd-title">📊 Time Tracking Analytics</h1>
                <p class="gtd-subtitle">Company-wide insights, team performance, and cost analysis</p>
            </div>
            <div class="gtd-header-right">
                <a href="<?= url('/time-tracking/export') ?>" class="btn-gtd btn-secondary-gtd">
                    <i class="bi bi-download"></i> Export
                </a>
                <a href="<?= url('/time-tracking/budgets') ?>" class="btn-gtd btn-secondary-gtd">
                    <i class="bi bi-wallet2"></i> Budgets
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="gtd-content">
        <!-- Filter Controls -->
        <div class="gtd-filters">
            <div class="filter-group">
                <label class="filter-label">Date Range:</label>
                <select class="filter-select" id="dateRange" onchange="applyFilters()">
                    <option value="today">Today</option>
                    <option value="week" selected>This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter">This Quarter</option>
                    <option value="year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Department:</label>
                <select class="filter-select" id="department" onchange="applyFilters()">
                    <option value="">All Departments</option>
                    <option value="engineering">Engineering</option>
                    <option value="design">Design</option>
                    <option value="pm">Product Management</option>
                    <option value="qa">QA</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">View:</label>
                <select class="filter-select" id="view" onchange="applyFilters()">
                    <option value="overview" selected>Overview</option>
                    <option value="detailed">Detailed</option>
                    <option value="comparison">Comparison</option>
                </select>
            </div>
            <button class="btn-gtd btn-secondary-gtd" onclick="resetFilters()" style="margin-left: auto;">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </button>
        </div>

        <!-- Key Metrics Grid -->
        <div class="gtd-metrics-grid">
            <!-- Total Time Tracked -->
            <div class="gtd-metric-card">
                <p class="gtd-metric-label">💼 Total Time Tracked</p>
                <p class="gtd-metric-value"><?= $formatTime($userStats['total_seconds'] ?? 0) ?></p>
                <p class="gtd-metric-subtext"><?= ($userStats['log_count'] ?? 0) ?> entries</p>
                <p class="gtd-metric-trend up">
                    <i class="bi bi-arrow-up"></i> 12% vs last period
                </p>
            </div>

            <!-- Total Cost -->
            <div class="gtd-metric-card">
                <p class="gtd-metric-label">💰 Total Cost</p>
                <p class="gtd-metric-value"><?= $getCurrencySymbol($userStats['currency'] ?? 'USD') ?><?= number_format((float)($userStats['total_cost'] ?? 0), 0) ?></p>
                <p class="gtd-metric-subtext">Billable & non-billable</p>
                <p class="gtd-metric-trend down">
                    <i class="bi bi-arrow-down"></i> 8% vs last period
                </p>
            </div>

            <!-- Billable Hours -->
            <div class="gtd-metric-card">
                <p class="gtd-metric-label">🎯 Billable Hours</p>
                <p class="gtd-metric-value"><?= $formatTime($userStats['billable_seconds'] ?? 0) ?></p>
                <p class="gtd-metric-subtext"><?= round(($userStats['billable_seconds'] ?? 0) / max($userStats['total_seconds'] ?? 1, 1) * 100, 1) ?>% of total</p>
                <p class="gtd-metric-trend up">
                    <i class="bi bi-arrow-up"></i> 5% vs last period
                </p>
            </div>

            <!-- Average Daily Hours -->
            <div class="gtd-metric-card">
                <p class="gtd-metric-label">📈 Daily Average</p>
                <p class="gtd-metric-value"><?= number_format((float)($userStats['avg_daily_hours'] ?? 0), 1) ?>h</p>
                <p class="gtd-metric-subtext">Per working day</p>
                <p class="gtd-metric-trend up">
                    <i class="bi bi-arrow-up"></i> On schedule
                </p>
            </div>
        </div>

        <!-- Team Performance & Weekly Trend -->
        <div class="gtd-grid-2">
            <!-- Weekly Trend Chart -->
            <div class="gtd-section">
                <div class="gtd-section-header">
                    <div>
                        <p class="gtd-section-title">
                            <i class="bi bi-graph-up"></i> Weekly Trend
                        </p>
                        <p class="gtd-section-subtitle">Time tracked by day</p>
                    </div>
                </div>
                <div class="gtd-section-content">
                    <div class="gtd-bar-chart">
                        <?php 
                        $maxHours = max(array_merge([1], array_map(fn($d) => (($d['seconds'] ?? 0) / 3600), $weeklyTrend ?? [])));
                        foreach ($weeklyTrend ?? [['day' => 'Mon', 'seconds' => 0], ['day' => 'Tue', 'seconds' => 0], ['day' => 'Wed', 'seconds' => 0], ['day' => 'Thu', 'seconds' => 0], ['day' => 'Fri', 'seconds' => 0]] as $day): 
                        ?>
                        <div class="gtd-bar">
                            <div class="gtd-bar-value"><?= number_format((float)($day['seconds'] ?? 0) / 3600, 1) ?></div>
                            <div class="gtd-bar-fill" style="height: <?= (($day['seconds'] ?? 0) / 3600) / $maxHours * 100 ?>%;"></div>
                            <div class="gtd-bar-label"><?= $day['day'] ?? 'N/A' ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Top Projects by Cost -->
            <div class="gtd-section">
                <div class="gtd-section-header">
                    <div>
                        <p class="gtd-section-title">
                            <i class="bi bi-diagram-3"></i> Top Projects by Cost
                        </p>
                        <p class="gtd-section-subtitle">Cost breakdown by project</p>
                    </div>
                </div>
                <div class="gtd-section-content">
                    <div class="gtd-table-wrapper">
                        <table class="gtd-table">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Hours</th>
                                    <th>Cost</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($projectAnalysis)): ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="gtd-empty-state">
                                            <p class="gtd-empty-text">No project data</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($projectAnalysis, 0, 5) as $project): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= url('/time-tracking/project/' . ($project['project_id'] ?? '#')) ?>" class="gtd-cell-link">
                                                <?= htmlspecialchars($project['project_name'] ?? 'Unknown') ?>
                                            </a>
                                        </td>
                                        <td class="gtd-cell-mono"><?= $formatTime($project['total_seconds'] ?? 0) ?></td>
                                        <td class="gtd-cell-currency"><?= $getCurrencySymbol($project['currency'] ?? 'USD') ?><?= number_format((float)($project['total_cost'] ?? 0), 0) ?></td>
                                        <td>
                                            <div class="gtd-progress-bar">
                                                <div class="gtd-progress-fill" style="width: <?= round(($project['total_cost'] ?? 0) / max($userStats['total_cost'] ?? 1, 1) * 100, 1) ?>%;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Issues & Recent Activity -->
        <div class="gtd-grid-2">
            <!-- Top Issues by Time -->
            <div class="gtd-section">
                <div class="gtd-section-header">
                    <div>
                        <p class="gtd-section-title">
                            <i class="bi bi-star-fill"></i> Top Issues by Time
                        </p>
                        <p class="gtd-section-subtitle">Most time-consuming work</p>
                    </div>
                </div>
                <div class="gtd-section-content">
                    <div class="gtd-table-wrapper">
                        <table class="gtd-table">
                            <thead>
                                <tr>
                                    <th>Issue</th>
                                    <th>Time</th>
                                    <th>Logs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($topIssues)): ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="gtd-empty-state">
                                            <p class="gtd-empty-text">No issue data</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($topIssues, 0, 8) as $issue): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= url('/projects/' . ($issue['project_key'] ?? '') . '/issues/' . ($issue['issue_key'] ?? '')) ?>" class="gtd-cell-link">
                                                <?= htmlspecialchars($issue['issue_key'] ?? 'N/A') ?>
                                            </a>
                                            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">
                                                <?= htmlspecialchars(substr($issue['issue_summary'] ?? '', 0, 50)) ?>
                                            </div>
                                        </td>
                                        <td class="gtd-cell-mono"><?= $formatTime($issue['total_seconds'] ?? 0) ?></td>
                                        <td><?= $issue['log_count'] ?? 0 ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Feed -->
            <div class="gtd-section">
                <div class="gtd-section-header">
                    <div>
                        <p class="gtd-section-title">
                            <i class="bi bi-clock-history"></i> Recent Activity
                        </p>
                        <p class="gtd-section-subtitle">Latest time log entries</p>
                    </div>
                </div>
                <div class="gtd-section-content">
                    <?php if (empty($recentLogs)): ?>
                    <div class="gtd-empty-state">
                        <div class="gtd-empty-icon">📭</div>
                        <p class="gtd-empty-text">No recent activity</p>
                        <p class="gtd-empty-hint">Time logs will appear here as team members track time</p>
                    </div>
                    <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <?php foreach (array_slice($recentLogs, 0, 10) as $log): ?>
                        <div style="padding: 12px 16px; background-color: var(--bg-secondary); border-radius: 4px; border-left: 3px solid var(--jira-blue);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                        <div class="gtd-cell-avatar" style="width: 24px; height: 24px; font-size: 10px;">
                                            <?= substr($log['user_name'] ?? '?', 0, 1) ?>
                                        </div>
                                        <span style="font-weight: 600; color: var(--text-primary); font-size: 13px;">
                                            <?= htmlspecialchars($log['user_name'] ?? 'Unknown') ?>
                                        </span>
                                        <span style="font-size: 12px; color: var(--text-secondary);">
                                            logged time on
                                        </span>
                                    </div>
                                    <div style="font-size: 13px; color: var(--jira-blue); font-weight: 500;">
                                        <a href="<?= url('/projects/' . ($log['project_key'] ?? '') . '/issues/' . ($log['issue_key'] ?? '')) ?>" class="gtd-cell-link">
                                            <?= htmlspecialchars($log['issue_key'] ?? 'N/A') ?>
                                        </a>
                                    </div>
                                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">
                                        <?= htmlspecialchars(substr($log['issue_summary'] ?? '', 0, 60)) ?>
                                    </div>
                                </div>
                                <div style="text-align: right; flex-shrink: 0;">
                                    <div style="font-weight: 700; color: var(--text-primary); font-family: 'Monaco', monospace;">
                                        <?= $formatTime($log['duration_seconds'] ?? 0) ?>
                                    </div>
                                    <div style="font-size: 11px; color: var(--text-secondary); margin-top: 2px;">
                                        <?= date('M d, H:i', strtotime($log['created_at'] ?? 'now')) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Billable vs Non-billable Analysis -->
        <div class="gtd-section">
            <div class="gtd-section-header">
                <div>
                    <p class="gtd-section-title">
                        <i class="bi bi-pie-chart"></i> Billable Analysis
                    </p>
                    <p class="gtd-section-subtitle">Billable vs non-billable time breakdown</p>
                </div>
            </div>
            <div class="gtd-section-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <!-- Billable Stats -->
                    <div>
                        <div style="margin-bottom: 16px;">
                            <p style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin: 0 0 8px 0;">Billable Time</p>
                            <p style="font-size: 24px; font-weight: 700; color: var(--color-success); margin: 0;">
                                <?= $formatTime($userStats['billable_seconds'] ?? 0) ?>
                            </p>
                            <p style="font-size: 12px; color: var(--text-secondary); margin: 4px 0 0 0;">
                                Cost: <?= $getCurrencySymbol($userStats['currency'] ?? 'USD') ?><?= number_format((float)($userStats['billable_cost'] ?? 0), 0) ?>
                            </p>
                        </div>
                        <div class="gtd-progress-bar" style="margin-bottom: 12px;">
                            <div class="gtd-progress-fill" style="width: <?= round(($userStats['billable_seconds'] ?? 0) / max($userStats['total_seconds'] ?? 1, 1) * 100, 1) ?>%; background: linear-gradient(90deg, var(--color-success) 0%, #4CAF50 100%);"></div>
                        </div>
                        <span class="gtd-badge gtd-badge-success">
                            <i class="bi bi-check-circle"></i>
                            <?= round(($userStats['billable_seconds'] ?? 0) / max($userStats['total_seconds'] ?? 1, 1) * 100, 1) ?>% Billable
                        </span>
                    </div>

                    <!-- Non-billable Stats -->
                    <div>
                        <div style="margin-bottom: 16px;">
                            <p style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin: 0 0 8px 0;">Non-billable Time</p>
                            <p style="font-size: 24px; font-weight: 700; color: var(--color-warning); margin: 0;">
                                <?= $formatTime($userStats['non_billable_seconds'] ?? 0) ?>
                            </p>
                            <p style="font-size: 12px; color: var(--text-secondary); margin: 4px 0 0 0;">
                                Cost: <?= $getCurrencySymbol($userStats['currency'] ?? 'USD') ?><?= number_format((float)($userStats['non_billable_cost'] ?? 0), 0) ?>
                            </p>
                        </div>
                        <div class="gtd-progress-bar" style="margin-bottom: 12px;">
                            <div class="gtd-progress-fill" style="width: <?= round(($userStats['non_billable_seconds'] ?? 0) / max($userStats['total_seconds'] ?? 1, 1) * 100, 1) ?>%; background: linear-gradient(90deg, var(--color-warning) 0%, #FFA500 100%);"></div>
                        </div>
                        <span class="gtd-badge gtd-badge-warning">
                            <i class="bi bi-exclamation-circle"></i>
                            <?= round(($userStats['non_billable_seconds'] ?? 0) / max($userStats['total_seconds'] ?? 1, 1) * 100, 1) ?>% Non-billable
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Performance (if user is manager) -->
        <?php if ($currentUser && isset($currentUser['role']) && in_array($currentUser['role'], ['admin', 'manager', 'team_lead'])): ?>
        <div class="gtd-section">
            <div class="gtd-section-header">
                <div>
                    <p class="gtd-section-title">
                        <i class="bi bi-people"></i> Team Performance
                    </p>
                    <p class="gtd-section-subtitle">Team member productivity metrics</p>
                </div>
            </div>
            <div class="gtd-section-content">
                <div class="gtd-table-wrapper">
                    <table class="gtd-table">
                        <thead>
                            <tr>
                                <th>Team Member</th>
                                <th>Hours Logged</th>
                                <th>Total Cost</th>
                                <th>Billable %</th>
                                <th>Avg Daily Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topUsers)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="gtd-empty-state">
                                        <p class="gtd-empty-text">No team data</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($topUsers, 0, 10) as $user): ?>
                                <tr>
                                    <td>
                                        <div class="gtd-cell-user">
                                            <div class="gtd-cell-avatar">
                                                <?= substr($user['display_name'] ?? '?', 0, 1) ?>
                                            </div>
                                            <span><?= htmlspecialchars($user['display_name'] ?? 'Unknown') ?></span>
                                        </div>
                                    </td>
                                    <td class="gtd-cell-mono"><?= $formatTime($user['total_seconds'] ?? 0) ?></td>
                                    <td class="gtd-cell-currency"><?= $getCurrencySymbol($user['currency'] ?? 'USD') ?><?= number_format((float)($user['total_cost'] ?? 0), 0) ?></td>
                                    <td>
                                        <span class="gtd-badge gtd-badge-success">
                                            <?= round(($user['billable_seconds'] ?? 0) / max($user['total_seconds'] ?? 1, 1) * 100, 0) ?>%
                                        </span>
                                    </td>
                                    <td><?= number_format((float)($user['avg_daily_hours'] ?? 0), 1) ?>h</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Help Section -->
        <div class="gtd-section" style="margin-bottom: 0;">
            <div class="gtd-section-header">
                <p class="gtd-section-title">
                    <i class="bi bi-question-circle"></i> About This Dashboard
                </p>
            </div>
            <div class="gtd-section-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div>
                        <h4 style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin: 0 0 8px 0;">Analytics Overview</h4>
                        <p style="font-size: 13px; color: var(--text-secondary); margin: 0; line-height: 1.6;">
                            This dashboard shows company-wide time tracking analytics, including team performance, cost analysis, and productivity metrics. Use this to understand how your team spends time and monitor project costs.
                        </p>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin: 0 0 8px 0;">Project-Specific Reports</h4>
                        <p style="font-size: 13px; color: var(--text-secondary); margin: 0; line-height: 1.6;">
                            For detailed information about a specific project's time tracking, budget status, and team member allocations, visit the project's time tracking report page from the project details or sidebar.
                        </p>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin: 0 0 8px 0;">Tracking Time</h4>
                        <p style="font-size: 13px; color: var(--text-secondary); margin: 0; line-height: 1.6;">
                            Start tracking time from any issue detail page using the floating timer widget. The data will automatically be aggregated here and in project-specific reports.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function applyFilters() {
        console.log('[GLOBAL-TIME-TRACKING] Applying filters...');
        // TODO: Implement filter logic
        // This would typically reload the data with new filter parameters
    }

    function resetFilters() {
        document.getElementById('dateRange').value = 'week';
        document.getElementById('department').value = '';
        document.getElementById('view').value = 'overview';
        applyFilters();
    }
</script>

<?php \App\Core\View::endSection(); ?>
