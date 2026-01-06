<?php \App\Core\View:: extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper dashboard-page">
    <!-- Page Header (No Breadcrumb for Dashboard) -->
    <div class="page-header">
        <div class="header-left">
            <div class="dashboard-avatar-container">
                <?php if (($avatarUrl = avatar($currentUser['avatar'] ?? null))): ?>
                    <img src="<?= e($avatarUrl) ?>" alt="<?= e($currentUser['first_name'] ?? 'User') ?>"
                        class="dashboard-user-avatar">
                <?php else: ?>
                    <div class="dashboard-user-avatar-placeholder">
                        <?= strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="dashboard-welcome">
                    <h1 class="page-title">Welcome, <?= e($currentUser['first_name'] ?? 'User') ?></h1>
                    <div class="header-meta">
                        <span class="meta-item"><i class="bi bi-person-check"></i> <?= $stats['assigned_count'] ?? 0 ?>
                            Assigned</span>
                        <span class="meta-item"><i class="bi bi-play-circle"></i> <?= $stats['in_progress'] ?? 0 ?> In
                            Progress</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <button class="action-button btn-primary" data-bs-toggle="modal" data-bs-target="#createIssueModal">
                <i class="bi bi-plus-lg"></i> Create
            </button>
            <a href="<?= url('/search?assignee=currentUser()') ?>" class="action-button">
                <i class="bi bi-search"></i> View All
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="page-content">
        <div class="content-left">
            <!-- Statistics Cards Grid -->
            <div class="stats-grid">
                <a href="<?= url('/search?assignee=currentUser()') ?>" class="stat-card">
                    <div class="stat-value"><?= $stats['assigned_count'] ?? 0 ?></div>
                    <div class="stat-label">Assigned to Me</div>
                    <div class="stat-icon">👤</div>
                </a>
                <a href="<?= url('/search?reporter=currentUser()') ?>" class="stat-card">
                    <div class="stat-value"><?= $stats['reported_count'] ?? 0 ?></div>
                    <div class="stat-label">Reported by Me</div>
                    <div class="stat-icon">📝</div>
                </a>
                <a href="<?= url('/search?assignee=currentUser()&status=In+Progress') ?>" class="stat-card">
                    <div class="stat-value"><?= $stats['in_progress'] ?? 0 ?></div>
                    <div class="stat-label">In Progress</div>
                    <div class="stat-icon">▶️</div>
                </a>
                <a href="<?= url('/search?assignee=currentUser()&due=week') ?>" class="stat-card">
                    <div class="stat-value"><?= $stats['due_soon'] ?? 0 ?></div>
                    <div class="stat-label">Due This Week</div>
                    <div class="stat-icon">⏰</div>
                </a>
            </div>

            <!-- Your Work Card -->
            <div class="card standard-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="bi bi-kanban"></i> Your Work</h2>
                    <a href="<?= url('/search?assignee=currentUser()') ?>" class="card-action-link">View All</a>
                </div>

                <!-- Tabs -->
                <div class="tabs-container">
                    <div class="tabs-list" role="tablist">
                        <button class="tab-btn active" id="assigned-tab" data-bs-toggle="tab"
                            data-bs-target="#assigned-panel" role="tab">
                            Assigned to Me <span class="tab-count"><?= count($assignedIssues ?? []) ?></span>
                        </button>
                        <button class="tab-btn" id="reported-tab" data-bs-toggle="tab" data-bs-target="#reported-panel"
                            role="tab">
                            Reported <span class="tab-count"><?= count($reportedIssues ?? []) ?></span>
                        </button>
                        <button class="tab-btn" id="watched-tab" data-bs-toggle="tab" data-bs-target="#watched-panel"
                            role="tab">
                            Watching <span class="tab-count"><?= count($watchedIssues ?? []) ?></span>
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-bar" id="filterButtonsContainer">
                    <button type="button" class="filter-chip active"
                        onclick="dashboardFilterIssues('all', event);">All</button>
                    <button type="button" class="filter-chip"
                        onclick="dashboardFilterIssues('high-priority', event);">High Priority</button>
                    <button type="button" class="filter-chip" onclick="dashboardFilterIssues('due-soon', event);">Due
                        Soon</button>
                    <button type="button" class="filter-chip"
                        onclick="dashboardFilterIssues('updated-today', event);">Updated Today</button>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content">
                        <!-- Assigned Panel -->
                        <div class="tab-pane fade show active" id="assigned-panel" role="tabpanel">
                            <?php if (empty($assignedIssues)): ?>
                                <div class="empty-placeholder">
                                    <div class="empty-icon">📭</div>
                                    <p>No issues assigned to you</p>
                                    <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal"
                                        data-bs-target="#createIssueModal">Create Issue</button>
                                </div>
                            <?php else: ?>
                                <div class="issue-list-container">
                                    <?php foreach ($assignedIssues as $issue): ?>
                                        <?php include __DIR__ . '/partials/issue_row.php'; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Reported Panel -->
                        <div class="tab-pane fade" id="reported-panel" role="tabpanel">
                            <?php if (empty($reportedIssues)): ?>
                                <div class="empty-placeholder">
                                    <div class="empty-icon">📋</div>
                                    <p>No issues reported by you</p>
                                </div>
                            <?php else: ?>
                                <div class="issue-list-container">
                                    <?php foreach ($reportedIssues as $issue): ?>
                                        <?php include __DIR__ . '/partials/issue_row.php'; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Watched Panel -->
                        <div class="tab-pane fade" id="watched-panel" role="tabpanel">
                            <?php if (empty($watchedIssues)): ?>
                                <div class="empty-placeholder">
                                    <div class="empty-icon">👁️</div>
                                    <p>You aren't watching any issues</p>
                                </div>
                            <?php else: ?>
                                <div class="issue-list-container">
                                    <?php foreach ($watchedIssues as $issue): ?>
                                        <?php include __DIR__ . '/partials/issue_row.php'; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-right">
            <!-- Projects Card -->
            <div class="card standard-card sidebar-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="bi bi-folder"></i> Projects</h2>
                    <a href="<?= url('/projects') ?>" class="card-action-link">All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($projects)): ?>
                        <div class="empty-placeholder small">
                            <p>No projects yet</p>
                        </div>
                    <?php else: ?>
                        <div class="sidebar-list">
                            <?php foreach ($projects as $project): ?>
                                <a href="<?= url('/projects/' . $project['key']) ?>" class="sidebar-list-item">
                                    <div class="project-avatar-small">
                                        <?= strtoupper(substr($project['key'], 0, 2)) ?>
                                    </div>
                                    <div class="item-content">
                                        <div class="item-title"><?= e($project['name']) ?></div>
                                        <div class="item-subtitle"><?= e($project['key']) ?></div>
                                    </div>
                                    <span class="count-badge"><?= $project['open_issues'] ?? 0 ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Workload Card -->
            <div class="card standard-card sidebar-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="bi bi-pie-chart"></i> Workload</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($workloadByProject)): ?>
                        <div class="empty-placeholder small">
                            <p>No open issues.</p>
                        </div>
                    <?php else: ?>
                        <?php $totalWork = array_sum(array_column($workloadByProject, 'issue_count')); ?>
                        <div class="workload-list">
                            <?php foreach ($workloadByProject as $row): ?>
                                <?php $percent = $totalWork > 0 ? round(($row['issue_count'] / $totalWork) * 100) : 0; ?>
                                <div class="workload-item">
                                    <div class="workload-info">
                                        <a href="<?= url('/projects/' . $row['key']) ?>"
                                            class="workload-link"><?= e($row['key']) ?></a>
                                        <span class="workload-val"><?= $row['issue_count'] ?></span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: <?= $percent ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity Stream -->
            <div class="card standard-card sidebar-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="bi bi-activity"></i> Activity</h2>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentActivity)): ?>
                        <div class="empty-placeholder small">
                            <p>No recent activity</p>
                        </div>
                    <?php else: ?>
                        <div class="activity-feed">
                            <?php foreach ($recentActivity as $activity): ?>
                                <?php
                                $field = $activity['field'] ?? '';
                                $icon = 'bi-pencil-square';
                                $color = 'var(--jira-gray)';
                                if ($field === 'status') {
                                    $icon = 'bi-arrow-left-right';
                                    $color = 'var(--jira-blue)';
                                } elseif ($field === 'assignee') {
                                    $icon = 'bi-person-check';
                                    $color = '#216E4E';
                                } elseif ($field === 'comment') {
                                    $icon = 'bi-chat-dots';
                                    $color = '#216E4E';
                                } elseif ($field === 'priority') {
                                    $icon = 'bi-exclamation-triangle';
                                    $color = '#974F0C';
                                } elseif ($field === 'created') {
                                    $icon = 'bi-plus-circle';
                                    $color = '#216E4E';
                                }
                                ?>
                                <div class="activity-item">
                                    <div class="activity-icon" style="color: <?= $color ?>">
                                        <i class="bi <?= $icon ?>"></i>
                                    </div>
                                    <div class="activity-details">
                                        <div class="activity-msg">
                                            <strong><?= e($activity['user_name'] ?? 'System') ?></strong>
                                            <?php if ($field === 'comment'): ?> commented on
                                            <?php elseif ($field === 'created'): ?> created
                                            <?php else: ?> updated <em><?= e($field) ?></em> on
                                            <?php endif; ?>
                                            <a
                                                href="<?= url('/issue/' . $activity['issue_key']) ?>"><?= e($activity['issue_key']) ?></a>
                                        </div>
                                        <div class="activity-time"><?= time_ago($activity['created_at']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Enterprise Jira Design System Overrides for Dashboard */
    :root {
        --jira-blue: #8B1956;
        --jira-blue-dark: #6F123F;
        --jira-dark: #161B22;
        --jira-gray: #626F86;
        --jira-light: #F7F8FA;
        --jira-border: #DFE1E6;
    }

    .page-wrapper {
        background-color: var(--jira-light);
        min-height: 100vh;
    }

    /* Header */
    .page-header {
        background: white;
        padding: 24px 32px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        flex: 1;
    }

    .dashboard-avatar-container {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .dashboard-user-avatar,
    .dashboard-user-avatar-placeholder {
        width: 56px;
        height: 56px;
        border-radius: 8px;
        object-fit: cover;
        background: var(--jira-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
    }

    .page-title {
        font-size: 24px;
        font-weight: 600;
        color: var(--jira-dark);
        margin: 0 0 4px 0;
    }

    .header-meta {
        display: flex;
        gap: 16px;
        font-size: 13px;
        color: var(--jira-gray);
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: white;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        color: var(--jira-dark);
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
    }

    .action-button:hover {
        background: var(--jira-light);
        color: var(--jira-dark);
        border-color: #B6C2CF;
        text-decoration: none;
    }

    .action-button.btn-primary {
        background: var(--jira-blue);
        color: white;
        border-color: var(--jira-blue);
    }

    .action-button.btn-primary:hover {
        background: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
    }

    /* Page Content */
    .page-content {
        display: flex;
        gap: 24px;
        padding: 32px;
        max-width: 1600px;
        margin: 0 auto;
    }

    .content-left {
        flex: 1;
        min-width: 0;
    }

    .content-right {
        width: 320px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: white;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        padding: 24px;
        position: relative;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
        display: block;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--jira-dark);
        line-height: 1;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--jira-gray);
    }

    .stat-icon {
        position: absolute;
        top: 24px;
        right: 24px;
        font-size: 24px;
        opacity: 0.2;
    }

    /* Cards */
    .standard-card {
        background: white;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-action-link {
        font-size: 13px;
        font-weight: 500;
        color: var(--jira-blue);
        text-decoration: none;
    }

    .card-action-link:hover {
        text-decoration: underline;
    }

    /* Tabs */
    .tabs-container {
        padding: 0 24px;
        border-bottom: 1px solid var(--jira-border);
        background: white;
    }

    .tabs-list {
        display: flex;
        gap: 24px;
    }

    .tab-btn {
        padding: 12px 4px;
        border: none;
        background: none;
        border-bottom: 2px solid transparent;
        color: var(--jira-gray);
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .tab-btn:hover {
        color: var(--jira-blue);
    }

    .tab-btn.active {
        color: var(--jira-blue);
        border-bottom-color: var(--jira-blue);
    }

    .tab-count {
        background: var(--jira-light);
        color: var(--jira-dark);
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: 4px;
    }

    /* Filters */
    .filters-bar {
        padding: 12px 24px;
        display: flex;
        gap: 8px;
        border-bottom: 1px solid var(--jira-border);
        background: #FAFBFC;
    }

    .filter-chip {
        padding: 4px 12px;
        border-radius: 12px;
        border: 1px solid var(--jira-border);
        background: white;
        font-size: 12px;
        font-weight: 500;
        color: var(--jira-dark);
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-chip:hover {
        background: var(--jira-light);
    }

    .filter-chip.active {
        background: var(--jira-blue);
        color: white;
        border-color: var(--jira-blue);
    }

    /* Issue List */
    .issue-list-container {
        max-height: 600px;
        overflow-y: auto;
    }

    .issue-row {
        display: flex;
        align-items: center;
        padding: 12px 24px;
        border-bottom: 1px solid var(--jira-border);
        text-decoration: none;
        transition: background 0.1s;
        gap: 12px;
    }

    .issue-row:hover {
        background: var(--jira-light);
    }

    .issue-row:last-child {
        border-bottom: none;
    }

    /* Issue Row Elements */
    .issue-type-icon {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        flex-shrink: 0;
    }

    .issue-key {
        font-size: 13px;
        color: var(--jira-blue);
        font-weight: 500;
        min-width: 60px;
    }

    .issue-summary {
        flex: 1;
        font-size: 14px;
        color: var(--jira-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .issue-priority {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 10px;
        font-weight: bold;
    }

    .issue-status {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge {
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        color: white;
        display: inline-flex;
        align-items: center;
    }

    .badge-danger {
        background-color: #ED3C32;
    }

    .badge-warning {
        background-color: #E77817;
    }

    /* Sidebar Elements */
    .sidebar-list {
        max-height: 400px;
        overflow-y: auto;
    }

    /* Custom Scrollbar for lists */
    .sidebar-list::-webkit-scrollbar,
    .issue-list-container::-webkit-scrollbar,
    .activity-feed::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }

    .sidebar-list::-webkit-scrollbar-track,
    .issue-list-container::-webkit-scrollbar-track,
    .activity-feed::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-list::-webkit-scrollbar-thumb,
    .issue-list-container::-webkit-scrollbar-thumb,
    .activity-feed::-webkit-scrollbar-thumb {
        background: #DFE1E6;
        border-radius: 4px;
    }

    .sidebar-list::-webkit-scrollbar-thumb:hover,
    .issue-list-container::-webkit-scrollbar-thumb:hover,
    .activity-feed::-webkit-scrollbar-thumb:hover {
        background: #B3BAC5;
    }

    .sidebar-list-item {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        border-bottom: 1px solid var(--jira-border);
        text-decoration: none;
        color: inherit;
        gap: 12px;
    }

    .sidebar-list-item:hover {
        background: var(--jira-light);
    }

    .sidebar-list-item:last-child {
        border-bottom: none;
    }

    .project-avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        background: var(--jira-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
    }

    .item-content {
        flex: 1;
        min-width: 0;
    }

    .item-title {
        font-size: 14px;
        font-weight: 500;
        color: var(--jira-dark);
    }

    .item-subtitle {
        font-size: 12px;
        color: var(--jira-gray);
    }

    .count-badge {
        font-size: 12px;
        font-weight: 500;
        color: var(--jira-gray);
        background: var(--jira-light);
        padding: 2px 8px;
        border-radius: 10px;
    }

    /* Workload */
    .workload-list {
        padding: 20px;
    }

    .workload-item {
        margin-bottom: 16px;
    }

    .workload-item:last-child {
        margin-bottom: 0;
    }

    .workload-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
        font-size: 13px;
    }

    .workload-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
    }

    .workload-link:hover {
        text-decoration: underline;
    }

    .progress-track {
        height: 6px;
        background: var(--jira-light);
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: var(--jira-blue);
        border-radius: 3px;
    }

    /* Activity */
    .activity-feed {
        padding: 0;
        max-height: 500px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--jira-border);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        margin-top: 2px;
    }

    .activity-msg {
        font-size: 13px;
        line-height: 1.4;
        color: var(--jira-dark);
        margin-bottom: 4px;
    }

    .activity-msg a {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
    }

    .activity-msg a:hover {
        text-decoration: underline;
    }

    .activity-time {
        font-size: 11px;
        color: var(--jira-gray);
    }

    /* Empty States */
    .empty-placeholder {
        text-align: center;
        padding: 40px 20px;
        color: var(--jira-gray);
    }

    .empty-icon {
        font-size: 32px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .empty-placeholder.small {
        padding: 24px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .page-content {
            flex-direction: column;
            padding: 24px;
        }

        .content-right {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            display: flex;
            gap: 12px;
        }

        .action-button {
            flex: 1;
            justify-content: center;
        }

        .page-content {
            padding: 16px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Dashboard Tab Handling
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Deactivate all buttons
                tabButtons.forEach(btn => btn.classList.remove('active'));
                // Activate clicked button
                this.classList.add('active');

                // Hide all panes
                tabPanes.forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Show target pane
                const targetId = this.getAttribute('data-bs-target').substring(1);
                const targetPane = document.getElementById(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });
    });

    function dashboardFilterIssues(filterType, event) {
        if (event) event.preventDefault();

        // Update active button
        const container = document.getElementById('filterButtonsContainer');
        const buttons = container.querySelectorAll('.filter-chip');
        buttons.forEach(btn => btn.classList.remove('active'));
        if (event && event.currentTarget) event.currentTarget.classList.add('active');

        // Filter issue rows
        const issueRows = document.querySelectorAll('.issue-row');
        issueRows.forEach(row => {
            const priority = row.getAttribute('data-priority') || '';
            const due = row.getAttribute('data-due') || '';
            const updatedToday = row.getAttribute('data-updated-today') === 'true';

            let visible = true;
            switch (filterType) {
                case 'high-priority':
                    visible = (priority === 'high' || priority === 'highest' || priority === 'critical');
                    break;
                case 'due-soon':
                    visible = (due === 'due-soon' || due === 'overdue');
                    break;
                case 'updated-today':
                    visible = updatedToday;
                    break;
                default:
                    visible = true;
            }
            // Use setProperty for display to handle flex layout
            if (visible) {
                row.style.removeProperty('display');
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<?php \App\Core\View::endSection(); ?>