<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
        <a href="<?= url('/reports') ?>" class="breadcrumb-link">
            <i class="bi bi-graph-up"></i> Reports
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Sprint Report</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <div class="header-icon">
                <i class="bi bi-journal-text"></i>
            </div>
            <div class="header-info">
                <h1 class="page-title">Sprint Report</h1>
                <p class="page-subtitle">Sprint completion and scope change analysis</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="<?= url('/reports') ?>" class="action-button">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="page-content">
        <!-- Sidebar (Right Column) - Moved to left visually in code but flex ordered or just placed first for logic? 
             Actually, distinct from the prompt, let's keep the prompt's Left=Main, Right=Sidebar structure.
             Wait, currently selector is on left. Report controls usually go on top or sidebar. 
             I'll put the Selector in the RIGHT SIDEBAR to allow the main content to shine. 
        -->

        <div class="content-left">
            <?php if ($sprint): ?>
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <!-- Status Card -->
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #E3FCEF; color: #006644;">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div class="stat-value">
                            <?php
                            $statusLabel = ucfirst($sprint['status']);
                            echo $statusLabel;
                            ?>
                        </div>
                        <div class="stat-label">Current Status</div>
                    </div>

                    <!-- Duration Card -->
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #DEEBFF; color: #0747A6;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="stat-value">
                            <?php
                            $start = new DateTime($sprint['start_date']);
                            $end = new DateTime($sprint['end_date']);
                            echo $end->diff($start)->days + 1;
                            ?> days
                        </div>
                        <div class="stat-label">
                            <?= $start->format('M d') ?> - <?= $end->format('M d') ?>
                        </div>
                    </div>

                    <!-- Issues Card -->
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #EAE6FF; color: #403294;">
                            <i class="bi bi-list-task"></i>
                        </div>
                        <div class="stat-value"><?= $sprint['total_issues'] ?></div>
                        <div class="stat-label"><?= $sprint['completed_issues'] ?> Completed</div>
                    </div>

                    <!-- Points Card -->
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #FFF0B3; color: #172B4D;">
                            <i class="bi bi-123"></i>
                        </div>
                        <div class="stat-value"><?= number_format($sprint['total_points'], 1) ?></div>
                        <div class="stat-label"><?= number_format($sprint['completed_points'], 1) ?> Completed</div>
                    </div>
                </div>

                <!-- Progress Section -->
                <div class="card content-card">
                    <div class="card-header-custom">
                        <h3 class="card-title">Sprint Progress</h3>
                    </div>
                    <div class="card-body-custom">

                        <!-- Issue Progress -->
                        <div class="progress-item">
                            <div class="progress-header">
                                <span class="progress-label">Issue Completion</span>
                                <span class="progress-value"><?= $sprint['completion_percentage'] ?>%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: <?= $sprint['completion_percentage'] ?>%;"></div>
                            </div>
                            <div class="progress-meta">
                                <span class="success-text"><i class="bi bi-check-circle-fill"></i>
                                    <?= $sprint['completed_issues'] ?> completed</span>
                                <span><?= $sprint['total_issues'] - $sprint['completed_issues'] ?> remaining</span>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <!-- Points Progress -->
                        <div class="progress-item">
                            <div class="progress-header">
                                <span class="progress-label">Story Points Completion</span>
                                <span class="progress-value"><?= $sprint['points_completion_percentage'] ?>%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill success-fill"
                                    style="width: <?= $sprint['points_completion_percentage'] ?>%;"></div>
                            </div>
                            <div class="progress-meta">
                                <span class="success-text"><i class="bi bi-check-circle-fill"></i>
                                    <?= number_format($sprint['completed_points'], 1) ?> completed</span>
                                <span><?= number_format($sprint['total_points'] - $sprint['completed_points'], 1) ?>
                                    remaining</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Related Reports Grid -->
                <div class="related-reports-grid">
                    <a href="<?= url('/reports/burndown/' . $sprint['id']) ?>" class="report-link-card">
                        <div class="report-icon bg-primary-subtle text-primary">
                            <i class="bi bi-graph-down"></i>
                        </div>
                        <div class="report-details">
                            <h4>Burndown Chart</h4>
                            <p>Track remaining work over time</p>
                        </div>
                        <div class="report-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>

                    <a href="<?= url('/reports/velocity/' . $sprint['board_id']) ?>" class="report-link-card">
                        <div class="report-icon bg-success-subtle text-success">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <div class="report-details">
                            <h4>Velocity Chart</h4>
                            <p>Track team velocity over sprints</p>
                        </div>
                        <div class="report-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                </div>

            <?php else: ?>
                <div class="empty-state-card">
                    <div class="empty-icon">
                        <i class="bi bi-arrow-right-circle"></i>
                    </div>
                    <h3>No Sprint Selected</h3>
                    <p>Please select a sprint from the sidebar to view the report.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="content-right">
            <!-- Sprint Selector Card -->
            <div class="card sidebar-card">
                <div class="card-header-sidebar">
                    <h4 class="sidebar-title">Select Sprint</h4>
                </div>
                <div class="card-body-sidebar">
                    <form method="GET" action="<?= url('/reports/sprint') ?>" id="sprintForm">
                        <div class="form-group">
                            <label for="sprintSelect" class="sidebar-label">Sprint</label>
                            <select class="form-select-custom" id="sprintSelect" name="sprintId"
                                onchange="document.getElementById('sprintForm').submit()">
                                <option value="">-- Choose a sprint --</option>
                                <?php foreach ($sprints ?? [] as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($selectedSprint ?? 0) == $s['id'] ? 'selected' : '' ?>>
                                        <?= e($s['name']) ?> (<?= ucfirst($s['status']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($sprint): ?>
                <!-- Project Details Card -->
                <div class="card sidebar-card">
                    <div class="card-header-sidebar">
                        <h4 class="sidebar-title">Sprint Details</h4>
                    </div>
                    <div class="card-body-sidebar">
                        <div class="detail-item">
                            <div class="detail-label">Project</div>
                            <div class="detail-value text-primary font-weight-bold">
                                <span class="project-badge"><?= e($sprint['project_key']) ?></span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Board</div>
                            <div class="detail-value"><?= e($sprint['board_name']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Goal</div>
                            <div class="detail-value text-muted">
                                <?= $sprint['goal'] ? e($sprint['goal']) : 'No goal set' ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* ============================================
   ENTERPRISE REPORT DESIGN (STANDARD SCALE)
   ============================================ */

    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-dark: #172B4D !important;
        --jira-gray: #5E6C84 !important;
        --jira-light: #F4F5F7 !important;
        --jira-border: #DFE1E6 !important;
        --jira-white: #FFFFFF !important;
    }

    /* Page Layout */
    .page-wrapper {
        background-color: var(--jira-light);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Breadcrumbs */
    .breadcrumb {
        background: var(--jira-white);
        padding: 12px 24px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        margin-bottom: 0 !important; /* Override Bootstrap default */
        border-radius: 0; /* Override Bootstrap default */
    }

    .breadcrumb-link {
        color: var(--jira-gray);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: color 0.2s;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue);
    }

    .breadcrumb-separator {
        color: #C1C7D0;
    }

    .breadcrumb-current {
        color: var(--jira-dark);
        font-weight: 600;
    }

    /* Page Header */
    .page-header {
        background: var(--jira-white);
        padding: 24px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-icon {
        width: 40px;
        height: 40px;
        background-color: #EAE6FF;
        color: #403294;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .header-info h1.page-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--jira-dark);
        margin: 0 0 4px 0;
        line-height: 1.25;
    }

    .page-subtitle {
        color: var(--jira-gray);
        margin: 0;
        font-size: 13px;
    }

    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        color: var(--jira-dark);
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s;
    }

    .action-button:hover {
        background: #EBECF0;
        text-decoration: none;
        color: var(--jira-dark);
    }

    /* Main Content Layout */
    .page-content {
        display: flex;
        padding: 24px;
        gap: 24px;
        align-items: flex-start;
    }

    .content-left {
        flex: 1;
        min-width: 0;
    }

    .content-right {
        width: 280px;
        /* Standard Sidebar Width */
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        /* Force 4 columns on desktop */
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        padding: 16px 20px;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(9, 30, 66, 0.08);
    }

    .stat-icon {
        width: 28px;
        height: 28px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .stat-value {
        font-size: 20px;
        /* Reduced from 24px */
        font-weight: 600;
        color: var(--jira-dark);
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--jira-gray);
        font-weight: 700;
    }

    /* Content Cards */
    .card {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(9, 30, 66, 0.08);
        display: flex;
        flex-direction: column;
    }

    .card-header-custom {
        padding: 16px 20px;
        border-bottom: 1px solid var(--jira-border);
    }

    .card-title {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--jira-dark);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-body-custom {
        padding: 20px;
    }

    /* Sidebar Cards */
    .sidebar-card {
        margin-bottom: 0;
    }

    .card-header-sidebar {
        padding: 12px 16px;
        border-bottom: 1px solid var(--jira-border);
        background-color: #FAFBFC;
        /* Light standout header */
    }

    .sidebar-title {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-body-sidebar {
        padding: 16px;
    }

    .sidebar-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--jira-dark);
        margin-bottom: 6px;
    }

    .form-select-custom {
        width: 100%;
        padding: 6px 10px;
        border: 2px solid var(--jira-border);
        border-radius: 4px;
        background-color: var(--jira-white);
        color: var(--jira-dark);
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-select-custom:focus {
        border-color: var(--jira-blue);
        outline: none;
    }

    /* Progress Bars */
    .progress-item {
        margin-bottom: 6px;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 500;
        color: var(--jira-dark);
    }

    .progress-track {
        height: 6px;
        /* Reduced from 8px */
        background-color: #EBECF0;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 6px;
    }

    .progress-fill {
        height: 100%;
        background-color: var(--jira-blue);
        border-radius: 3px;
    }

    .success-fill {
        background-color: #00875A;
    }

    .progress-meta {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: var(--jira-gray);
    }

    .success-text {
        color: #006644;
        font-weight: 500;
    }

    .divider {
        height: 1px;
        background-color: var(--jira-border);
        margin: 20px 0;
    }

    /* Detail Items */
    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--jira-border);
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 12px;
        color: var(--jira-gray);
    }

    .detail-value {
        font-size: 13px;
        color: var(--jira-dark);
        font-weight: 500;
    }

    .project-badge {
        background-color: #EBECF0;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        color: #42526E;
        font-weight: 700;
    }

    /* Related Reports Grid */
    .related-reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        /* Smaller min-width */
        gap: 16px;
        margin-top: 24px;
    }

    .report-link-card {
        display: flex;
        align-items: center;
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        padding: 16px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .report-link-card:hover {
        border-color: #B3BAC5;
        background-color: #FAFBFC;
        transform: translateY(-2px);
        text-decoration: none;
        box-shadow: 0 4px 8px rgba(9, 30, 66, 0.08);
    }

    .report-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-right: 12px;
    }

    .report-details h4 {
        margin: 0 0 2px 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--jira-dark);
    }

    .report-details p {
        margin: 0;
        font-size: 12px;
        color: var(--jira-gray);
    }

    .report-arrow {
        margin-left: auto;
        font-size: 12px;
        color: var(--jira-gray);
    }

    /* Empty State */
    .empty-state-card {
        background: var(--jira-white);
        border: 1px dashed var(--jira-border);
        border-radius: 6px;
        padding: 40px;
        text-align: center;
        color: var(--jira-gray);
    }

    .empty-icon {
        font-size: 32px;
        color: #EBECF0;
        margin-bottom: 12px;
    }

    .empty-state-card h3 {
        font-size: 16px;
        color: var(--jira-dark);
        margin-bottom: 6px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .page-content {
            flex-direction: column;
        }

        .content-right {
            width: 100%;
            order: -1;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .related-reports-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>