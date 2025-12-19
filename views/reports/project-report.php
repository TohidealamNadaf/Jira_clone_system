<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<style>
    /* ====================================== 
       CSS Variables - Enterprise Design System
       ====================================== */
    :root {
        --jira-blue: #0052CC;
        --jira-blue-dark: #003DA5;
        --jira-blue-light: #DEEBFF;
        --text-primary: #161B22;
        --text-secondary: #626F86;
        --bg-primary: #FFFFFF;
        --bg-secondary: #F7F8FA;
        --border-color: #DFE1E6;
        --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
        --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
        --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
        --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        --color-success: #216E4E;
        --color-error: #ED3C32;
        --color-warning: #974F0C;
        --color-info: #0055CC;
    }

    * {
        box-sizing: border-box;
    }

    /* ====================================== 
       Page Wrapper - Full Screen Layout
       ====================================== */
    .project-report-wrapper {
        background-color: var(--bg-secondary);
        min-height: calc(100vh - 80px);
        display: flex;
        flex-direction: column;
        padding: 0;
        margin: 0;
    }

    /* ====================================== 
       Breadcrumb Navigation - Professional
       ====================================== */
    .report-breadcrumb {
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
        font-weight: 400;
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* ====================================== 
       Page Header - Professional Title Section
       ====================================== */
    .report-header {
        background-color: var(--bg-primary);
        padding: 32px 40px;
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 32px;
    }

    .report-header-left {
        flex: 1;
    }

    .report-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        line-height: 1.3;
        letter-spacing: -0.5px;
    }

    .report-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 8px 0 0 0;
        padding: 0;
        line-height: 1.5;
        font-weight: 400;
    }

    .report-header-right {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    /* ====================================== 
       Main Content Area
       ====================================== */
    .report-content {
        flex: 1;
        padding: 24px 40px;
        overflow-y: auto;
    }

    /* ====================================== 
       Buttons - Professional Styling
       ====================================== */
    .btn-report {
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

    .btn-primary-report {
        background-color: var(--jira-blue);
        color: var(--bg-primary);
    }

    .btn-primary-report:hover {
        background-color: var(--jira-blue-dark);
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .btn-secondary-report {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary-report:hover {
        background-color: var(--bg-secondary);
        border-color: var(--jira-blue);
        color: var(--jira-blue);
    }

    /* ====================================== 
       Filters Section - Horizontal Layout
       ====================================== */
    .report-filters {
        display: flex;
        gap: 20px;
        margin-bottom: 1.5rem;
        align-items: flex-end;
        flex-wrap: wrap;
        background-color: var(--bg-primary);
        padding: 20px 24px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        box-shadow: var(--shadow-sm);
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        width: 240px;
        height: 40px;
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 13px;
        color: var(--text-primary);
        background-color: var(--bg-primary);
        cursor: pointer;
        transition: all var(--transition);
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23626F86' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px;
        font-weight: 500;
    }

    .filter-select:hover {
        border-color: var(--jira-blue);
        background-color: var(--jira-blue-light);
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 2px rgba(0, 82, 204, 0.15);
    }

    /* ====================================== 
       Metrics Cards Grid - Professional Design
       ====================================== */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 1.5rem;
    }

    .metric-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-left: 4px solid var(--jira-blue);
        border-radius: 4px;
        padding: 20px;
        box-shadow: var(--shadow-md);
        transition: all var(--transition);
        cursor: pointer;
    }

    .metric-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
        border-left-color: var(--jira-blue-dark);
    }

    .metric-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .metric-label-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .metric-value {
        font-size: 40px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        line-height: 1.1;
        letter-spacing: -1px;
    }

    .metric-unit {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
        margin: 8px 0 0 0;
        display: block;
    }

    .metric-footer {
        border-top: 1px solid #EBECF0;
        padding-top: 12px;
        margin-top: 12px;
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    .metric-footer strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* ====================================== 
        Charts Section
        ====================================== */
    .charts-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 24px;
        margin-bottom: 1.5rem;
    }

    .chart-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 24px;
        box-shadow: var(--shadow-md);
        transition: all var(--transition);
    }

    .chart-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .chart-card.timeline-chart {
        grid-column: 1 / -1;
    }

    .chart-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 20px 0;
        padding: 0;
        padding-bottom: 12px;
        border-bottom: 1px solid #EBECF0;
        letter-spacing: -0.2px;
    }

    .chart-wrapper {
        position: relative;
        min-height: 300px;
    }

    /* ====================================== 
       Section Headers
       ====================================== */
    .section-header {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 20px 0;
        padding: 0 0 12px 0;
        border-bottom: 2px solid var(--jira-blue);
        display: inline-block;
        letter-spacing: -0.3px;
    }

    /* ====================================== 
       Data Tables
       ====================================== */
    .data-table {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        margin-bottom: 1.5rem;
    }

    .data-table table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        padding: 0;
    }

    .data-table thead {
        background-color: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .data-table th {
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        text-align: left;
        letter-spacing: 0.5px;
        vertical-align: middle;
    }

    .data-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: background-color var(--transition);
    }

    .data-table tbody tr:hover {
        background-color: var(--bg-secondary);
    }

    .data-table tbody tr:last-child {
        border-bottom: none;
    }

    .data-table td {
        padding: 12px 16px;
        font-size: 13px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .data-table td.text-secondary {
        color: var(--text-secondary);
        font-weight: 400;
    }

    .data-table a {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 600;
        transition: all var(--transition);
    }

    .data-table a:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    /* ====================================== 
       Status Badges
       ====================================== */
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border: 1px solid;
    }

    /* ====================================== 
       Progress Bar
       ====================================== */
    .progress-bar-container {
        height: 6px;
        background-color: #EBECF0;
        border-radius: 3px;
        overflow: hidden;
        margin: 8px 0;
    }

    .progress-bar-fill {
        height: 100%;
        background-color: var(--jira-blue);
        transition: width 0.3s ease;
    }

    /* ====================================== 
       Empty State
       ====================================== */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        color: var(--text-secondary);
    }

    .empty-state-icon {
        font-size: 56px;
        margin-bottom: 16px;
        opacity: 0.6;
        display: block;
    }

    .empty-state-text {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-secondary);
        margin: 0;
        padding: 0;
    }

    /* ====================================== 
       Responsive Design - Tablets
       ====================================== */
    @media (max-width: 1024px) {
        .report-header {
            padding: 24px 32px;
            gap: 24px;
        }

        .report-content {
            padding: 20px 32px;
        }

        .report-breadcrumb {
            padding: 10px 32px;
        }

        .report-title {
            font-size: 28px;
        }

        .charts-container {
            grid-template-columns: 1fr;
        }

        .metrics-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
    }

    /* ====================================== 
       Responsive Design - Mobile
       ====================================== */
    @media (max-width: 768px) {
        .report-breadcrumb {
            padding: 10px 20px;
            font-size: 12px;
            gap: 6px;
        }

        .report-header {
            padding: 20px 20px;
            flex-direction: column;
            gap: 16px;
        }

        .report-title {
            font-size: 24px;
        }

        .report-subtitle {
            font-size: 13px;
        }

        .report-content {
            padding: 16px 20px;
        }

        .report-filters {
            flex-direction: column;
            gap: 12px;
            padding: 16px 20px;
        }

        .filter-group {
            width: 100%;
        }

        .filter-select {
            width: 100%;
            min-height: 44px;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .metric-value {
            font-size: 32px;
        }

        .report-header-right {
            width: 100%;
            flex-direction: column;
        }

        .btn-report {
            width: 100%;
            justify-content: center;
            min-height: 44px;
        }

        .charts-container {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .data-table {
            overflow-x: auto;
        }

        .data-table th,
        .data-table td {
            padding: 10px 12px;
            font-size: 12px;
        }

        .chart-wrapper {
            min-height: 250px;
        }

        .section-header {
            font-size: 15px;
        }
    }

    /* ====================================== 
       Responsive Design - Small Mobile
       ====================================== */
    @media (max-width: 480px) {
        .report-breadcrumb {
            padding: 8px 16px;
            font-size: 11px;
        }

        .report-header {
            padding: 16px 16px;
        }

        .report-title {
            font-size: 20px;
        }

        .report-content {
            padding: 12px 16px;
        }

        .metrics-grid {
            gap: 12px;
        }

        .metric-value {
            font-size: 28px;
        }

        .metric-label {
            font-size: 10px;
        }

        .filter-select {
            font-size: 12px;
        }

        .report-filters {
            padding: 12px 16px;
            gap: 10px;
        }
    }

    /* ====================================== 
       Remove Bootstrap Overrides
       ====================================== */
    .project-report-wrapper .btn {
        all: revert;
    }

    .project-report-wrapper select,
    .project-report-wrapper input {
        font-family: system-ui, -apple-system, sans-serif;
    }
</style>

<div class="project-report-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="report-breadcrumb">
        <a href="<?= url('/') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door" style="font-size: 12px;"></i> Home
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">Projects</a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Report</span>
    </div>

    <!-- Page Header -->
    <div class="report-header">
        <div class="report-header-left">
            <h1 class="report-title">Project Report</h1>
            <p class="report-subtitle">Comprehensive analytics and metrics for <?= e($project['name']) ?></p>
        </div>
        <div class="report-header-right">
            <button class="btn-report btn-secondary-report" onclick="exportReport()" title="Export report data">
                <i class="bi bi-download"></i> Export
            </button>
            <a href="<?= url("/projects/{$project['key']}") ?>" class="btn-report btn-secondary-report" title="Back to project">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="report-content">
        <!-- Filters Section -->
        <div class="report-filters">
            <div class="filter-group">
                <label class="filter-label">Time Period</label>
                <select class="filter-select" id="timeFilter" onchange="applyFilters()">
                    <option value="7">Last 7 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="60">Last 60 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="180">Last 180 days</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Issue Type</label>
                <select class="filter-select" id="issueTypeFilter" onchange="applyFilters()">
                    <option value="">All Types</option>
                    <?php if (!empty($issueTypes)): ?>
                        <?php foreach ($issueTypes as $type): ?>
                        <option value="<?= $type['id'] ?>">
                            <?= e($type['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select class="filter-select" id="statusFilter" onchange="applyFilters()">
                    <option value="">All Statuses</option>
                    <?php if (!empty($statuses)): ?>
                        <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status['id'] ?>">
                            <?= e($status['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">
                    <span class="metric-label-dot" style="background-color: #0052CC;"></span>
                    Total Issues
                </div>
                <div class="metric-value"><?= $summary['total_issues'] ?? 0 ?></div>
                <div class="metric-footer">
                    <strong><?= isset($summary['open_issues']) ? $summary['open_issues'] : 0 ?></strong> open
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <span class="metric-label-dot" style="background-color: #216E4E;"></span>
                    Resolved Issues
                </div>
                <div class="metric-value"><?= $summary['resolved_issues'] ?? 0 ?></div>
                <div class="metric-footer">
                    <strong><?= isset($summary['total_issues']) && $summary['total_issues'] > 0 ? round(($summary['resolved_issues'] ?? 0) / $summary['total_issues'] * 100) : 0 ?>%</strong> of total
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <span class="metric-label-dot" style="background-color: #974F0C;"></span>
                    Avg Resolution Time
                </div>
                <div class="metric-value"><?= $summary['avg_resolution_days'] ?? 0 ?></div>
                <span class="metric-unit">days</span>
                <div class="metric-footer">
                    Median: <strong><?= $summary['median_resolution_days'] ?? 0 ?> days</strong>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <span class="metric-label-dot" style="background-color: #ED3C32;"></span>
                    Overdue Issues
                </div>
                <div class="metric-value"><?= $summary['overdue_issues'] ?? 0 ?></div>
                <div class="metric-footer">
                    Past due date
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <span class="metric-label-dot" style="background-color: #003DA5;"></span>
                    Team Members
                </div>
                <div class="metric-value"><?= $summary['team_members'] ?? 0 ?></div>
                <div class="metric-footer">
                    Active contributors
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <span class="metric-label-dot" style="background-color: #974F0C;"></span>
                    Avg Priority
                </div>
                <div class="metric-value">
                    <span style="font-size: 28px;">
                        <?php 
                        $avgPriority = $summary['avg_priority_level'] ?? 3;
                        echo $avgPriority == 1 ? '🔴' : ($avgPriority == 2 ? '🟠' : ($avgPriority == 3 ? '🟡' : '🔵'));
                        ?>
                    </span>
                </div>
                <div class="metric-footer">
                    <strong><?= e($summary['avg_priority_name'] ?? 'Medium') ?></strong>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-container">
            <!-- Issue Status Distribution -->
            <div class="chart-card">
                <h3 class="chart-title">Issue Status Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Issues by Priority -->
            <div class="chart-card">
                <h3 class="chart-title">Issues by Priority</h3>
                <div class="chart-wrapper">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>

            <!-- Issues Created vs Resolved -->
            <div class="chart-card timeline-chart">
                <h3 class="chart-title">Issues Created vs Resolved (Last <?= isset($_GET['days']) ? $_GET['days'] : 30 ?> days)</h3>
                <div class="chart-wrapper" style="position: relative; min-height: 350px;">
                    <canvas id="createdVsResolvedChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Team Performance Table -->
        <?php if (!empty($teamPerformance)): ?>
        <div style="margin-bottom: 1.5rem;">
            <h3 class="section-header">Team Performance</h3>
             <div style="margin-top: 16px;">
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Team Member</th>
                                <th>Issues Assigned</th>
                                <th>Issues Resolved</th>
                                <th>Avg Resolution Time</th>
                                <th>Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teamPerformance as $member): ?>
                            <tr>
                                <td>
                                    <strong><?= e($member['name'] ?? 'Unknown') ?></strong>
                                </td>
                                <td class="text-secondary"><?= $member['assigned'] ?? 0 ?></td>
                                <td class="text-secondary"><?= $member['resolved'] ?? 0 ?></td>
                                <td class="text-secondary">
                                    <?php 
                                    $avgTime = $member['avg_time'] ?? 0;
                                    echo is_numeric($avgTime) ? round($avgTime, 1) : $avgTime;
                                    ?> days
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div class="progress-bar-container" style="width: 80px; flex-shrink: 0;">
                                            <div class="progress-bar-fill" style="width: <?= isset($member['assigned']) && $member['assigned'] > 0 ? round(($member['resolved'] ?? 0) / $member['assigned'] * 100) : 0 ?>%;"></div>
                                        </div>
                                        <span style="font-size: 12px; color: #626F86; font-weight: 600; min-width: 30px;">
                                            <?= isset($member['assigned']) && $member['assigned'] > 0 ? round(($member['resolved'] ?? 0) / $member['assigned'] * 100) : 0 ?>%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Issues Table -->
        <?php if (!empty($recentIssues)): ?>
        <div style="margin-bottom: 1.5rem;">
            <h3 class="section-header">Recent Issues</h3>
             <div style="margin-top: 16px;">
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Summary</th>
                                <th>Status</th>
                                <th>Assignee</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentIssues as $issue): ?>
                            <tr>
                                <td>
                                    <a href="<?= url("/issue/{$issue['issue_key']}") ?>">
                                        <?= e($issue['issue_key']) ?>
                                    </a>
                                </td>
                                <td><?= e(substr($issue['summary'], 0, 50)) ?><?= strlen($issue['summary']) > 50 ? '...' : '' ?></td>
                                <td>
                                    <span class="status-badge" style="background-color: <?= e($issue['status_color'] ?? '#DFE1E6') ?>20; color: <?= e($issue['status_color'] ?? '#626F86') ?>; border-color: <?= e($issue['status_color'] ?? '#DFE1E6') ?>;">
                                        <?= e($issue['status_name']) ?>
                                    </span>
                                </td>
                                <td class="text-secondary"><?= e($issue['assignee_name'] ?? 'Unassigned') ?></td>
                                <td class="text-secondary"><?= date('M d, Y', strtotime($issue['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="empty-state" style="margin-top: 1.5rem;">
            <span class="empty-state-icon">📊</span>
            <p class="empty-state-text">No issues found for the selected filters</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart data from controller
    const statusData = <?= json_encode($statusDistribution ?? []) ?>;
    const priorityData = <?= json_encode($priorityDistribution ?? []) ?>;
    const timelineData = <?= json_encode($timelineData ?? []) ?>;

    function initCharts() {
        initStatusChart();
        initPriorityChart();
        initTimelineChart();
    }

    function initStatusChart() {
        if (statusData.length === 0) return;
        const ctx = document.getElementById('statusChart');
        if (!ctx) return;

        const labels = statusData.map(d => d.status);
        const counts = statusData.map(d => d.count);
        const colors = ['#216E4E', '#974F0C', '#ED3C32', '#0052CC'];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: colors.slice(0, labels.length),
                    borderColor: '#FFFFFF',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, font: { size: 12, weight: '500' }, color: '#626F86' }
                    }
                }
            }
        });
    }

    function initPriorityChart() {
        if (priorityData.length === 0) return;
        const ctx = document.getElementById('priorityChart');
        if (!ctx) return;

        const labels = priorityData.map(d => d.priority);
        const counts = priorityData.map(d => d.count);
        const colors = ['#ED3C32', '#974F0C', '#003DA5', '#0052CC', '#626F86'];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Issues',
                    data: counts,
                    backgroundColor: colors.slice(0, labels.length),
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#626F86', font: { size: 11, weight: '500' } },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    y: {
                        ticks: { color: '#626F86', font: { size: 11, weight: '500' } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function initTimelineChart() {
        if (timelineData.length === 0) return;
        const ctx = document.getElementById('createdVsResolvedChart');
        if (!ctx) return;

        const dates = timelineData.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        const created = timelineData.map(d => d.created);
        const resolved = timelineData.map(d => d.resolved);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Created',
                        data: created,
                        borderColor: '#0052CC',
                        backgroundColor: 'rgba(0, 82, 204, 0.08)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#0052CC',
                        borderWidth: 2
                    },
                    {
                        label: 'Resolved',
                        data: resolved,
                        borderColor: '#216E4E',
                        backgroundColor: 'rgba(33, 110, 78, 0.08)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#216E4E',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, padding: 15, font: { size: 12, weight: '500' }, color: '#626F86' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#626F86', font: { size: 11, weight: '500' } },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#626F86', font: { size: 11, weight: '500' } }
                    }
                }
            }
        });
    }

    function applyFilters() {
        const time = document.getElementById('timeFilter').value;
        const issueType = document.getElementById('issueTypeFilter').value;
        const status = document.getElementById('statusFilter').value;
        const params = new URLSearchParams();
        params.append('days', time);
        if (issueType) params.append('issue_type', issueType);
        if (status) params.append('status', status);
        window.location.href = '<?= url("/projects/{$project['key']}/report") ?>' + '?' + params.toString();
    }

    function exportReport() {
        alert('Export functionality coming soon!');
    }

    document.addEventListener('DOMContentLoaded', initCharts);
</script>
<?php \App\Core\View::endSection(); ?>
