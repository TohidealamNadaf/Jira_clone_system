<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="admin-dashboard-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Administration</h1>
            <p class="page-subtitle">Manage users, roles, projects, and system settings</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-area">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= e($stats['total_users'] ?? 0) ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <i class="bi bi-folder"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= e($stats['total_projects'] ?? 0) ?></div>
                    <div class="stat-label">Projects</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-teal">
                    <i class="bi bi-list-task"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= e($stats['total_issues'] ?? 0) ?></div>
                    <div class="stat-label">Total Issues</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-orange">
                    <i class="bi bi-hdd"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= e($stats['storage_used'] ?? '0 MB') ?></div>
                    <div class="stat-label">Storage Used</div>
                </div>
            </div>
        </div>

        <!-- Admin Sections & Recent Activity -->
        <div class="dashboard-grid">
            <!-- Left Column: Admin Sections -->
            <div class="admin-sections">
                <!-- User Management -->
                <div class="admin-card">
                    <div class="card-header">
                        <div class="card-title-wrapper">
                            <i class="bi bi-people-fill"></i>
                            <h3 class="card-title">User Management</h3>
                        </div>
                        <p class="card-description">Manage user accounts and access</p>
                    </div>
                    <div class="card-links">
                        <a href="<?= url('/admin/users') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>View All Users</span>
                        </a>
                        <a href="<?= url('/admin/users/create') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>Create User</span>
                        </a>
                        <a href="<?= url('/admin/users?status=inactive') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>Inactive Users</span>
                        </a>
                    </div>
                </div>

                <!-- Roles & Permissions -->
                <div class="admin-card">
                    <div class="card-header">
                        <div class="card-title-wrapper">
                            <i class="bi bi-shield-check"></i>
                            <h3 class="card-title">Roles & Permissions</h3>
                        </div>
                        <p class="card-description">Configure access control and permissions</p>
                    </div>
                    <div class="card-links">
                        <a href="<?= url('/admin/roles') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>Manage Roles</span>
                        </a>
                        <a href="<?= url('/admin/permissions') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>Permission Schemes</span>
                        </a>
                        <a href="<?= url('/admin/global-permissions') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>Global Permissions</span>
                        </a>
                    </div>
                </div>

                <!-- Projects -->
                <div class="admin-card">
                    <div class="card-header">
                        <div class="card-title-wrapper">
                            <i class="bi bi-folder-fill"></i>
                            <h3 class="card-title">Projects</h3>
                        </div>
                        <p class="card-description">Project administration and configuration</p>
                    </div>
                    <div class="card-links">
                        <a href="<?= url('/admin/projects') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>All Projects</span>
                        </a>
                        <a href="<?= url('/admin/project-categories') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>Project Categories</span>
                        </a>
                        <a href="<?= url('/admin/issue-types') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>Issue Types</span>
                        </a>
                        <a href="<?= url('/admin/workflows') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>Workflows</span>
                        </a>
                    </div>
                </div>

                <!-- System -->
                <div class="admin-card">
                    <div class="card-header">
                        <div class="card-title-wrapper">
                            <i class="bi bi-gear-fill"></i>
                            <h3 class="card-title">System</h3>
                        </div>
                        <p class="card-description">System configuration and settings</p>
                    </div>
                    <div class="card-links">
                        <a href="<?= url('/admin/settings') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>General Settings</span>
                        </a>
                        <a href="<?= url('/admin/audit-log') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>Audit Log</span>
                        </a>
                        <a href="<?= url('/admin/system-info') ?>" class="card-link">
                            <i class="bi bi-arrow-right"></i>
                            <span>System Information</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Recent Activity -->
            <div class="activity-sidebar">
                <div class="activity-card">
                    <div class="activity-header">
                        <h3 class="activity-title">Recent Admin Activity</h3>
                        <a href="<?= url('/admin/audit-log') ?>" class="view-all-link">View All</a>
                    </div>
                    <div class="activity-content">
                        <?php if (empty($recentActivity)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">📋</div>
                                <p>No recent activity</p>
                            </div>
                        <?php else: ?>
                            <div class="activity-list">
                                <?php foreach ($recentActivity ?? [] as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <?php
                                            $action = $activity['action'] ?? '';
                                            if (str_contains($action, 'create')):
                                                echo '<i class="bi bi-plus-circle"></i>';
                                            elseif (str_contains($action, 'delete')):
                                                echo '<i class="bi bi-trash"></i>';
                                            elseif (str_contains($action, 'update')):
                                                echo '<i class="bi bi-pencil"></i>';
                                            elseif (str_contains($action, 'login')):
                                                echo '<i class="bi bi-box-arrow-in-right"></i>';
                                            else:
                                                echo '<i class="bi bi-activity"></i>';
                                            endif;
                                            ?>
                                        </div>
                                        <div class="activity-details">
                                            <div class="activity-action">
                                                <?= e(ucwords(str_replace('_', ' ', $action))) ?>
                                                <span class="activity-entity"><?= e($activity['entity_type'] ?? '') ?></span>
                                            </div>
                                            <div class="activity-meta">
                                                <?= e($activity['user_name'] ?? 'System') ?>
                                                <span class="separator">•</span>
                                                <?= time_ago($activity['created_at']) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health Section -->
        <div class="health-sections">
            <!-- System Health -->
            <div class="health-card">
                <div class="health-header">
                    <h3 class="health-title">
                        <i class="bi bi-heart-pulse"></i>
                        System Health
                    </h3>
                </div>
                <div class="health-grid">
                    <div class="health-item">
                        <div class="health-icon health-success">
                            <i class="bi bi-database"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Database</div>
                            <div class="health-status health-success">Connected</div>
                        </div>
                    </div>
                    <div class="health-item">
                        <div class="health-icon health-success">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Email Service</div>
                            <div class="health-status health-success">Operational</div>
                        </div>
                    </div>
                    <div class="health-item">
                        <div class="health-icon health-warning">
                            <i class="bi bi-hdd"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Disk Space</div>
                            <div class="health-status health-warning"><?= e($stats['disk_usage'] ?? '75%') ?> used</div>
                        </div>
                    </div>
                    <div class="health-item">
                        <div class="health-icon health-success">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Background Jobs</div>
                            <div class="health-status health-success">Running</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification System Health -->
            <div class="health-card">
                <div class="health-header">
                    <h3 class="health-title">
                        <i class="bi bi-bell"></i>
                        Notification System Health
                    </h3>
                </div>
                <?php
                $errorStats = \App\Helpers\NotificationLogger::getErrorStats();
                $isOperational = \App\Helpers\NotificationLogger::isLogOperational();
                ?>
                <div class="health-grid">
                    <div class="health-item">
                        <div class="health-icon <?= $isOperational ? 'health-success' : 'health-danger' ?>">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Status</div>
                            <div class="health-status <?= $isOperational ? 'health-success' : 'health-danger' ?>">
                                <?= $isOperational ? 'Operational' : 'Issues Detected' ?>
                            </div>
                        </div>
                    </div>
                    <div class="health-item">
                        <div
                            class="health-icon <?= $errorStats['total_errors'] > 0 ? 'health-danger' : 'health-success' ?>">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Errors (24h)</div>
                            <div
                                class="health-status <?= $errorStats['total_errors'] > 0 ? 'health-danger' : 'health-success' ?>">
                                <?= e($errorStats['total_errors'] ?? 0) ?> errors
                            </div>
                        </div>
                    </div>
                    <div class="health-item">
                        <div class="health-icon health-info-blue">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Retries</div>
                            <div class="health-status health-info-blue"><?= e($errorStats['retry_count'] ?? 0) ?> queued
                            </div>
                        </div>
                    </div>
                    <div class="health-item">
                        <div class="health-icon health-gray">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Log Size</div>
                            <div class="health-status health-gray">
                                <?= \App\Helpers\NotificationLogger::getLogFileSizeFormatted() ?></div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($errorStats['recent_errors'])): ?>
                    <div class="errors-section">
                        <h4 class="errors-title">Recent Errors:</h4>
                        <div class="errors-list">
                            <?php foreach (array_slice($errorStats['recent_errors'], -5) as $error): ?>
                                <div class="error-item">
                                    <i class="bi bi-x-circle"></i>
                                    <span><?= e(substr($error, 0, 80)) ?>...</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --jira-blue: #8B1956;
        --jira-blue-dark: #6F123F;
        --text-primary: #161B22;
        --text-secondary: #626F86;
        --bg-primary: #FFFFFF;
        --bg-secondary: #F7F8FA;
        --border-color: #DFE1E6;
        --green: #216E4E;
        --orange: #974F0C;
        --teal: #0055CC;
        --red: #ED3C32;
        --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
        --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
        --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
        --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .admin-dashboard-wrapper {
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 80px);
    }

    /* Page Header */
    .page-header {
        padding: 24px 32px;
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    .header-left {}

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 8px 0;
        letter-spacing: -0.3px;
    }

    .page-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
        font-weight: 400;
    }

    /* Content Area */
    .content-area {
        flex: 1;
        background: var(--bg-secondary);
        padding: 32px;
        overflow-y: auto;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 16px;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 20px;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition);
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 8px;
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-icon-blue {
        background: rgba(139, 25, 86, 0.1);
        color: var(--jira-blue);
    }

    .stat-icon-green {
        background: rgba(33, 110, 78, 0.1);
        color: var(--green);
    }

    .stat-icon-teal {
        background: rgba(0, 85, 204, 0.1);
        color: var(--teal);
    }

    .stat-icon-orange {
        background: rgba(151, 79, 12, 0.1);
        color: var(--orange);
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-top: 4px;
    }

    /* Dashboard Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 32px;
        margin-bottom: 32px;
    }

    /* Admin Sections */
    .admin-sections {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .admin-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition);
        overflow: hidden;
    }

    .admin-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .card-header {
        padding: 20px;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .card-title-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .card-title-wrapper i {
        font-size: 20px;
        color: var(--jira-blue);
        flex-shrink: 0;
    }

    .card-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .card-description {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
        font-weight: 400;
    }

    .card-links {
        display: flex;
        flex-direction: column;
        padding: 16px 0;
    }

    .card-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        color: var(--jira-blue);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all var(--transition);
        border-left: 3px solid transparent;
    }

    .card-link:hover {
        background: var(--bg-secondary);
        border-left-color: var(--jira-blue);
        padding-left: 17px;
        color: var(--jira-blue-dark);
    }

    .card-link i {
        font-size: 14px;
        flex-shrink: 0;
    }

    .card-link span {
        flex: 1;
    }

    /* Activity Sidebar */
    .activity-sidebar {
        display: flex;
        flex-direction: column;
    }

    .activity-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        height: 100%;
    }

    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-secondary);
    }

    .activity-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .view-all-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: color var(--transition);
    }

    .view-all-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .activity-content {
        flex: 1;
        overflow-y: auto;
        max-height: 500px;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
    }

    .empty-icon {
        font-size: 40px;
        margin-bottom: 12px;
        opacity: 0.6;
    }

    .empty-state p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
    }

    .activity-item {
        display: flex;
        gap: 12px;
        padding: 12px 20px;
        border-bottom: 1px solid var(--border-color);
        transition: background var(--transition);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background: var(--bg-secondary);
    }

    .activity-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-size: 16px;
        flex-shrink: 0;
        background: rgba(139, 25, 86, 0.1);
        color: var(--jira-blue);
    }

    .activity-details {
        flex: 1;
        min-width: 0;
    }

    .activity-action {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .activity-entity {
        color: var(--text-secondary);
    }

    .activity-meta {
        font-size: 12px;
        color: var(--text-secondary);
    }

    .separator {
        margin: 0 4px;
    }

    /* Health Sections */
    .health-sections {
        display: grid;
        gap: 20px;
    }

    .health-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: var(--shadow-sm);
        padding: 24px;
    }

    .health-header {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .health-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .health-title i {
        font-size: 18px;
        color: var(--jira-blue);
    }

    .health-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .health-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .health-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 6px;
        font-size: 18px;
        flex-shrink: 0;
    }

    .health-success {
        background: rgba(33, 110, 78, 0.1);
        color: var(--green);
    }

    .health-warning {
        background: rgba(151, 79, 12, 0.1);
        color: var(--orange);
    }

    .health-danger {
        background: rgba(237, 60, 50, 0.1);
        color: var(--red);
    }

    .health-info-blue {
        background: rgba(139, 25, 86, 0.1);
        color: var(--jira-blue);
    }

    .health-gray {
        background: rgba(98, 111, 134, 0.1);
        color: var(--text-secondary);
    }

    .health-info {
        flex: 1;
    }

    .health-label {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 4px;
    }

    .health-status {
        font-size: 13px;
        font-weight: 500;
    }

    .health-status.health-success {
        color: var(--green);
    }

    .health-status.health-warning {
        color: var(--orange);
    }

    .health-status.health-danger {
        color: var(--red);
    }

    .health-status.health-info-blue {
        color: var(--jira-blue);
    }

    .health-status.health-gray {
        color: var(--text-secondary);
    }

    .errors-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .errors-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin: 0 0 12px 0;
    }

    .errors-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .error-item {
        display: flex;
        gap: 8px;
        font-size: 12px;
        color: var(--red);
        align-items: flex-start;
    }

    .error-item i {
        flex-shrink: 0;
        margin-top: 2px;
    }

    .error-item span {
        word-break: break-word;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .content-area {
            padding: 24px;
        }

        .stats-grid {
            gap: 16px;
            margin-bottom: 24px;
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .activity-sidebar {
            max-height: 400px;
        }

        .health-sections {
            gap: 16px;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 20px 16px;
        }

        .page-title {
            font-size: 24px;
        }

        .page-subtitle {
            font-size: 13px;
        }

        .content-area {
            padding: 16px;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-card {
            padding: 16px;
            gap: 12px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }

        .stat-value {
            font-size: 20px;
        }

        .stat-label {
            font-size: 11px;
        }

        .admin-sections {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .card-header {
            padding: 16px;
        }

        .card-title {
            font-size: 14px;
        }

        .card-description {
            font-size: 12px;
        }

        .card-link {
            padding: 10px 16px;
            font-size: 13px;
        }

        .health-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .health-item {
            gap: 10px;
        }

        .health-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .health-label {
            font-size: 11px;
        }

        .health-status {
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        .page-header {
            padding: 16px 12px;
        }

        .page-title {
            font-size: 20px;
        }

        .page-subtitle {
            font-size: 12px;
        }

        .content-area {
            padding: 12px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .stat-card {
            padding: 12px;
            gap: 10px;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            font-size: 18px;
        }

        .stat-value {
            font-size: 18px;
        }

        .stat-label {
            font-size: 10px;
        }

        .dashboard-grid {
            gap: 12px;
            margin-bottom: 12px;
        }

        .admin-card {
            padding: 0;
        }

        .card-header {
            padding: 12px 16px;
        }

        .card-title {
            font-size: 13px;
        }

        .card-description {
            font-size: 11px;
        }

        .card-link {
            padding: 8px 16px;
            font-size: 12px;
        }

        .health-sections {
            gap: 12px;
        }

        .health-card {
            padding: 16px;
        }

        .health-grid {
            gap: 12px;
        }

        .health-icon {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .health-label {
            font-size: 10px;
        }

        .health-status {
            font-size: 11px;
        }
    }

    @media print {
        .page-header {
            display: none;
        }

        .content-area {
            background: var(--bg-primary);
            padding: 0;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>