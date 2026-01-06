<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="reports-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="reports-breadcrumb">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Reports</span>
    </div>

    <!-- Page Header -->
    <div class="reports-header">
        <div class="header-left">
            <h1 class="page-title">Project Reports</h1>
            <p class="page-subtitle">Analytics and insights for <?= e($project['name']) ?></p>
        </div>
        <div class="header-right">
            <a href="<?= url("/projects/{$project['key']}") ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back to Project
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="reports-content">
        <!-- Key Metrics Section -->
        <div class="metrics-section">
            <div class="section-header">
                <h2 class="section-title">Key Metrics</h2>
                <p class="section-subtitle">Project overview and statistics</p>
            </div>
            
            <div class="metrics-grid">
                <!-- Total Issues Card -->
                <div class="metric-card">
                    <div class="metric-icon-wrapper total-issues-icon">
                        <i class="bi bi-list-check"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value"><?= $stats['total_issues'] ?></div>
                        <div class="metric-label">Total Issues</div>
                    </div>
                </div>

                <!-- Resolved Issues Card -->
                <div class="metric-card">
                    <div class="metric-icon-wrapper resolved-issues-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value"><?= $stats['resolved_issues'] ?></div>
                        <div class="metric-label">Resolved</div>
                    </div>
                </div>

                <!-- Open Issues Card -->
                <div class="metric-card">
                    <div class="metric-icon-wrapper open-issues-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value"><?= $stats['open_issues'] ?></div>
                        <div class="metric-label">Open</div>
                    </div>
                </div>

                <!-- Resolution Rate Card -->
                <div class="metric-card">
                    <div class="metric-icon-wrapper resolution-rate-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value">
                            <?php 
                            $resolutionRate = $stats['total_issues'] > 0 
                                ? round(($stats['resolved_issues'] / $stats['total_issues']) * 100, 1)
                                : 0;
                            echo $resolutionRate;
                            ?>%
                        </div>
                        <div class="metric-label">Resolution Rate</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolution Progress Section -->
        <div class="progress-section">
            <div class="progress-card">
                <div class="progress-header">
                    <h3 class="progress-title">Resolution Progress</h3>
                    <div class="progress-stats">
                        <span class="stat-badge resolved">
                            <i class="bi bi-check-circle"></i>
                            <?= $stats['resolved_issues'] ?> Resolved
                        </span>
                        <span class="stat-badge open">
                            <i class="bi bi-clock"></i>
                            <?= $stats['open_issues'] ?> Open
                        </span>
                    </div>
                </div>
                
                <div class="progress-bar-wrapper">
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" 
                             style="width: <?= $resolutionRate ?>%"
                             data-percentage="<?= $resolutionRate ?>%"
                             title="<?= $resolutionRate ?>% Complete">
                        </div>
                    </div>
                    <div class="progress-percentage">
                        <span class="percentage-value"><?= $resolutionRate ?>%</span>
                        <span class="percentage-label">Complete</span>
                    </div>
                </div>

                <div class="progress-details">
                    <p class="progress-text">
                        <strong><?= $stats['resolved_issues'] ?></strong> of <strong><?= $stats['total_issues'] ?></strong> issues have been resolved
                    </p>
                </div>
            </div>
        </div>

        <!-- Status Distribution Section -->
        <div class="distribution-section">
            <div class="section-header">
                <h2 class="section-title">Summary</h2>
                <p class="section-subtitle">Project overview and key statistics</p>
            </div>

            <div class="distribution-grid">
                <!-- Resolution Status Card -->
                <div class="distribution-card">
                    <div class="card-header">
                        <h4 class="card-title">Issue Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="status-list">
                            <div class="status-item">
                                <div class="status-info">
                                    <span class="status-dot resolved"></span>
                                    <span class="status-name">Resolved</span>
                                </div>
                                <div class="status-count">
                                    <span class="count-badge"><?= $stats['resolved_issues'] ?></span>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="status-info">
                                    <span class="status-dot open"></span>
                                    <span class="status-name">Open</span>
                                </div>
                                <div class="status-count">
                                    <span class="count-badge"><?= $stats['open_issues'] ?></span>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="status-info">
                                    <span class="status-dot total"></span>
                                    <span class="status-name">Total</span>
                                </div>
                                <div class="status-count">
                                    <span class="count-badge"><?= $stats['total_issues'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="distribution-card">
                    <div class="card-header">
                        <h4 class="card-title">Quick Stats</h4>
                    </div>
                    <div class="card-body">
                        <div class="stats-info">
                            <div class="stat-row">
                                <span class="stat-label">Resolution Rate</span>
                                <span class="stat-value">
                                    <?php 
                                    $resolutionRate = $stats['total_issues'] > 0 
                                        ? round(($stats['resolved_issues'] / $stats['total_issues']) * 100, 1)
                                        : 0;
                                    echo $resolutionRate;
                                    ?>%
                                </span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Issues Remaining</span>
                                <span class="stat-value"><?= $stats['open_issues'] ?></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-label">Completion</span>
                                <span class="stat-value">
                                    <?php 
                                    echo $stats['total_issues'] > 0 ? round(($stats['resolved_issues'] / $stats['total_issues']) * 100, 0) : 0;
                                    ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
       REPORTS PAGE - ENTERPRISE JIRA-LIKE DESIGN
       ============================================ */
    
    :root {
        --jira-blue: #8B1956;
        --jira-blue-dark: #6F123F;
        --jira-blue-light: #f0dce5;
        --text-primary: #161B22;
        --text-secondary: #626F86;
        --bg-primary: #FFFFFF;
        --bg-secondary: #F7F8FA;
        --border-color: #DFE1E6;
        --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
        --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
        --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
        --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        --color-success: #216E4E;
        --color-warning: #e77817;
        --color-danger: #AE2A19;
    }

    /* ============================================
       WRAPPER & LAYOUT
       ============================================ */
    
    .reports-wrapper {
        min-height: calc(100vh - 80px);
        background-color: var(--bg-secondary);
        padding: 0;
        margin: 0;
    }

    /* ============================================
       BREADCRUMB NAVIGATION
       ============================================ */
    
    .reports-breadcrumb {
        display: flex;
        align-items: center;
        gap: 0;
        padding: 12px 32px;
        background-color: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .breadcrumb-link {
        color: var(--jira-blue);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all var(--transition);
    }

    .breadcrumb-link:hover {
        background-color: var(--jira-blue-light);
        color: var(--jira-blue-dark);
    }

    .breadcrumb-separator {
        padding: 0 8px;
        color: var(--text-secondary);
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 700;
        padding: 4px 8px;
    }

    /* ============================================
       PAGE HEADER
       ============================================ */
    
    .reports-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 32px;
        background-color: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    .header-left {
        flex: 1;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 4px 0;
        letter-spacing: -0.3px;
    }

    .page-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.43;
    }

    .header-right {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-secondary {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background-color: #F0F1F3;
        border-color: #B3B9C4;
        color: var(--text-primary);
    }

    /* ============================================
       MAIN CONTENT AREA
       ============================================ */
    
    .reports-content {
        padding: 24px 32px;
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    /* ============================================
       SECTION STRUCTURE
       ============================================ */
    
    .section-header {
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 4px 0;
        letter-spacing: -0.2px;
    }

    .section-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.43;
    }

    /* ============================================
       METRICS SECTION
       ============================================ */
    
    .metrics-section {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }

    .metric-card {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition);
    }

    .metric-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .metric-icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 6px;
        flex-shrink: 0;
        font-size: 24px;
    }

    .total-issues-icon {
        background-color: rgba(139, 25, 86, 0.1);
        color: var(--jira-blue);
    }

    .resolved-issues-icon {
        background-color: rgba(33, 110, 78, 0.1);
        color: var(--color-success);
    }

    .open-issues-icon {
        background-color: rgba(174, 42, 25, 0.1);
        color: var(--color-danger);
    }

    .resolution-rate-icon {
        background-color: rgba(231, 120, 23, 0.1);
        color: var(--color-warning);
    }

    .metric-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .metric-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }

    .metric-label {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* ============================================
       PROGRESS SECTION
       ============================================ */
    
    .progress-section {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .progress-card {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 24px;
        box-shadow: var(--shadow-sm);
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .progress-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .progress-stats {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .stat-badge.resolved {
        background-color: rgba(33, 110, 78, 0.1);
        color: var(--color-success);
    }

    .stat-badge.open {
        background-color: rgba(174, 42, 25, 0.1);
        color: var(--color-danger);
    }

    .progress-bar-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }

    .progress-bar-container {
        flex: 1;
        height: 32px;
        background-color: var(--bg-secondary);
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #8B1956, #6F123F);
        transition: width var(--transition);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #FFFFFF;
    }

    .progress-percentage {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 60px;
    }

    .percentage-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }

    .percentage-label {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-top: 2px;
    }

    .progress-details {
        padding-top: 12px;
        border-top: 1px solid var(--border-color);
    }

    .progress-text {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.43;
    }

    /* ============================================
       DISTRIBUTION SECTION
       ============================================ */
    
    .distribution-section {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .distribution-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .distribution-card {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition);
    }

    .distribution-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .card-header {
        padding: 16px 20px;
        background-color: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .card-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        text-transform: none;
    }

    .card-body {
        padding: 16px;
    }

    /* ============================================
       STATUS LIST
       ============================================ */
    
    .status-list,
    .priority-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .status-item,
    .priority-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
        transition: background-color var(--transition);
    }

    .status-item:last-child,
    .priority-item:last-child {
        border-bottom: none;
    }

    .status-item:hover,
    .priority-item:hover {
        background-color: var(--bg-secondary);
    }

    .status-info,
    .priority-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .status-dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-dot.resolved {
        background-color: #216E4E;
    }

    .status-dot.open {
        background-color: #AE2A19;
    }

    .status-dot.total {
        background-color: #8B1956;
    }

    .priority-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    .status-name,
    .priority-name {
        font-size: 14px;
        color: var(--text-primary);
        font-weight: 500;
    }

    .status-count,
    .priority-count {
        display: flex;
        align-items: center;
    }

    .count-badge {
        background-color: var(--bg-secondary);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    /* ============================================
       EMPTY STATE
       ============================================ */
    
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .empty-icon {
        font-size: 48px;
        opacity: 0.5;
        line-height: 1;
    }

    .empty-text {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
        font-weight: 500;
    }

    /* ============================================
       ACTIVITY SECTION & TABLE
       ============================================ */
    
    .activity-section {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .activity-card {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .issues-table-wrapper {
        overflow-x: auto;
    }

    .issues-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .issues-table thead {
        background-color: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .issues-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .issues-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: background-color var(--transition);
    }

    .issues-table tbody tr:hover {
        background-color: var(--bg-secondary);
    }

    .issues-table tbody tr:last-child {
        border-bottom: none;
    }

    .issues-table td {
        padding: 12px 16px;
        color: var(--text-primary);
    }

    .col-key {
        width: 100px;
    }

    .col-summary {
        width: auto;
        min-width: 200px;
    }

    .col-status {
        width: 140px;
    }

    .col-priority {
        width: 140px;
    }

    .col-assignee {
        width: 180px;
    }

    .issue-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 600;
        transition: color var(--transition);
    }

    .issue-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .summary-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .issue-type-mini {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        color: #FFFFFF;
        font-size: 14px;
        flex-shrink: 0;
    }

    .summary-text {
        color: var(--text-primary);
        line-height: 1.43;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #FFFFFF;
        text-align: center;
    }

    .priority-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #FFFFFF;
        text-align: center;
        min-width: 60px;
    }

    .priority-badge.priority-none {
        background-color: var(--bg-secondary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .assignee-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .assignee-avatar-sm {
        display: inline-block;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid var(--border-color);
    }

    .assignee-avatar-sm {
        font-size: 11px;
        font-weight: 700;
        background-color: var(--jira-blue);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .assignee-name {
        color: var(--text-primary);
        font-size: 13px;
    }

    .assignee-unassigned {
        color: var(--text-secondary);
        font-size: 13px;
    }

    /* ============================================
       STATS INFO SECTION
       ============================================ */
    
    .stats-info {
        display: flex;
        flex-direction: column;
        gap: 0;
        padding: 0;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .stat-row:last-child {
        border-bottom: none;
    }

    .stat-label {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
        flex: 1;
        min-width: 0;
        line-height: 1.4;
    }

    .stat-value {
        font-size: 16px;
        font-weight: 700;
        color: var(--jira-blue);
        min-width: 55px;
        text-align: right;
        margin-left: 12px;
        line-height: 1.4;
    }

    /* ============================================
       RESPONSIVE DESIGN
       ============================================ */
    
    @media (max-width: 1024px) {
        .reports-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-right {
            width: 100%;
        }

        .btn-secondary {
            width: 100%;
            justify-content: center;
        }

        .reports-content {
            padding: 20px 24px;
            gap: 24px;
        }

        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .reports-breadcrumb {
            padding: 12px 16px;
            font-size: 12px;
            overflow-x: auto;
            white-space: nowrap;
        }

        .reports-header {
            padding: 16px 16px;
        }

        .page-title {
            font-size: 24px;
        }

        .page-subtitle {
            font-size: 13px;
        }

        .reports-content {
            padding: 16px 16px;
            gap: 20px;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .metric-card {
            padding: 16px;
        }

        .progress-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .progress-stats {
            width: 100%;
        }

        .progress-bar-wrapper {
            flex-direction: column;
            gap: 12px;
        }

        .distribution-grid {
            grid-template-columns: 1fr;
        }

        .issues-table {
            font-size: 13px;
        }

        .issues-table th {
            padding: 10px 12px;
            font-size: 11px;
        }

        .issues-table td {
            padding: 10px 12px;
        }

        .col-key {
            width: 80px;
        }

        .col-summary {
            min-width: 150px;
        }

        .col-status,
        .col-priority {
            width: 100px;
        }

        .col-assignee {
            width: 120px;
        }

        .summary-text {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .reports-breadcrumb {
            padding: 12px 12px;
            font-size: 11px;
        }

        .reports-header {
            padding: 12px 12px;
        }

        .page-title {
            font-size: 20px;
        }

        .page-subtitle {
            font-size: 12px;
        }

        .reports-content {
            padding: 12px 12px;
            gap: 16px;
        }

        .section-title {
            font-size: 18px;
        }

        .metric-card {
            padding: 12px;
            gap: 12px;
        }

        .metric-value {
            font-size: 24px;
        }

        .metric-label {
            font-size: 12px;
        }

        .progress-title {
            font-size: 15px;
        }

        .progress-card {
            padding: 16px;
        }

        .issues-table {
            font-size: 12px;
        }

        .issues-table th,
        .issues-table td {
            padding: 8px;
        }

        .col-key {
            width: 60px;
        }

        .col-summary {
            display: none;
        }

        .col-status,
        .col-priority,
        .col-assignee {
            width: auto;
        }

        .summary-cell {
            min-width: 0;
        }

        .issue-type-mini {
            width: 24px;
            height: 24px;
            font-size: 12px;
        }
    }

    /* ============================================
       ACCESSIBILITY & FOCUS STATES
       ============================================ */
    
    .breadcrumb-link:focus,
    .issue-link:focus {
        outline: 2px solid var(--jira-blue);
        outline-offset: 2px;
        border-radius: 4px;
    }

    .btn-secondary:focus {
        outline: 2px solid var(--jira-blue);
        outline-offset: 2px;
    }

    .metric-card:focus-within {
        box-shadow: var(--shadow-lg), 0 0 0 2px rgba(0, 82, 204, 0.1);
    }

    /* ============================================
       PRINT STYLES
       ============================================ */
    
    @media print {
        .reports-breadcrumb,
        .header-right {
            display: none;
        }

        .reports-content {
            background-color: #FFFFFF;
        }

        .metric-card,
        .progress-card,
        .distribution-card,
        .activity-card {
            page-break-inside: avoid;
            box-shadow: none;
            border: 1px solid #CCCCCC;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>
