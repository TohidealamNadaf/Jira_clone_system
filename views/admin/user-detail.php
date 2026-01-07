<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="ud-page">
    <!-- Breadcrumb Navigation -->
    <nav class="ud-breadcrumb">
        <a href="<?= url('/admin') ?>" class="ud-breadcrumb-link">
            <i class="bi bi-gear"></i> Admin
        </a>
        <span class="ud-breadcrumb-sep">/</span>
        <a href="<?= url('/admin/users') ?>" class="ud-breadcrumb-link">
            Users
        </a>
        <span class="ud-breadcrumb-sep">/</span>
        <span class="ud-breadcrumb-current">
            <?= e($user['display_name'] ?? $user['first_name'] . ' ' . $user['last_name']) ?>
        </span>
    </nav>

    <!-- Page Header Section -->
    <div class="ud-header-section">
        <div class="ud-header-left">
            <!-- Avatar -->
            <div class="ud-avatar-wrapper">
                <?php if (($avatarUrl = avatar($user['avatar'] ?? null))): ?>
                    <img src="<?= e($avatarUrl) ?>" class="ud-avatar" alt="<?= e($user['display_name'] ?? '') ?>">
                <?php else: ?>
                    <div class="ud-avatar ud-avatar-initials">
                        <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) . strtoupper(substr($user['last_name'] ?? '', 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Header Info -->
            <div class="ud-header-info">
                <h1 class="ud-page-title">
                    <?= e($user['display_name'] ?? $user['first_name'] . ' ' . $user['last_name']) ?>
                    <?php if ($user['is_admin']): ?>
                        <span class="ud-badge-admin" title="System Administrator">
                            <i class="bi bi-shield-check"></i> Admin
                        </span>
                    <?php endif; ?>
                </h1>
                <div class="ud-header-meta">
                    <span class="ud-meta-item">
                        <i class="bi bi-envelope"></i> <?= e($user['email']) ?>
                    </span>
                    <span class="ud-meta-item">
                        <i class="bi bi-at"></i> @<?= e($user['username'] ?? '') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="ud-header-actions">
            <a href="<?= url('/admin/users/' . $user['id'] . '/edit') ?>" class="ud-btn ud-btn-primary">
                <i class="bi bi-pencil-square"></i> Edit User
            </a>
            <a href="<?= url('/admin/users') ?>" class="ud-btn ud-btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="ud-content-grid">
        <!-- Left Column: Details -->
        <div class="ud-main">
            <!-- Personal Information Card -->
            <div class="ud-card">
                <div class="ud-card-header">
                    <h2 class="ud-card-title">
                        <i class="bi bi-person-circle"></i> Personal Information
                    </h2>
                </div>
                <div class="ud-card-body">
                    <div class="ud-detail-grid">
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">First Name</div>
                            <div class="ud-detail-value"><?= e($user['first_name'] ?? '-') ?></div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Last Name</div>
                            <div class="ud-detail-value"><?= e($user['last_name'] ?? '-') ?></div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Display Name</div>
                            <div class="ud-detail-value"><?= e($user['display_name'] ?? '-') ?></div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Email</div>
                            <div class="ud-detail-value">
                                <a href="mailto:<?= e($user['email']) ?>" class="ud-email-link">
                                    <?= e($user['email']) ?>
                                </a>
                            </div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Username</div>
                            <div class="ud-detail-value">@<?= e($user['username'] ?? '-') ?></div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Timezone</div>
                            <div class="ud-detail-value"><?= e($user['timezone'] ?? 'UTC') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Status Card -->
            <div class="ud-card">
                <div class="ud-card-header">
                    <h2 class="ud-card-title">
                        <i class="bi bi-info-circle"></i> Account Status
                    </h2>
                </div>
                <div class="ud-card-body">
                    <div class="ud-detail-grid">
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Status</div>
                            <div class="ud-detail-value">
                                <?php if ($user['is_active']): ?>
                                    <span class="ud-badge ud-badge-success">
                                        <span class="ud-status-dot"></span> Active
                                    </span>
                                <?php elseif (empty($user['email_verified_at'])): ?>
                                    <span class="ud-badge ud-badge-warning">
                                        <span class="ud-status-dot"></span> Pending
                                    </span>
                                <?php else: ?>
                                    <span class="ud-badge ud-badge-danger">
                                        <span class="ud-status-dot"></span> Inactive
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Email Verified</div>
                            <div class="ud-detail-value">
                                <?php if ($user['email_verified_at']): ?>
                                    <i class="bi bi-check-circle-fill ud-text-success"></i>
                                    <?= format_datetime($user['email_verified_at']) ?>
                                <?php else: ?>
                                    <i class="bi bi-x-circle-fill ud-text-danger"></i> Not verified
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Account Type</div>
                            <div class="ud-detail-value">
                                <?php if ($user['is_admin']): ?>
                                    <span class="ud-badge ud-badge-admin">
                                        <i class="bi bi-shield-check"></i> System Administrator
                                    </span>
                                <?php else: ?>
                                    <span class="ud-badge ud-badge-secondary">Regular User</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Created At</div>
                            <div class="ud-detail-value"><?= format_datetime($user['created_at'] ?? '') ?></div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Last Updated</div>
                            <div class="ud-detail-value"><?= format_datetime($user['updated_at'] ?? '') ?></div>
                        </div>
                        <div class="ud-detail-item">
                            <div class="ud-detail-label">Last Login</div>
                            <div class="ud-detail-value">
                                <?= ($user['last_login_at'] ? format_datetime($user['last_login_at']) : 'Never') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Roles Card -->
            <div class="ud-card">
                <div class="ud-card-header">
                    <h2 class="ud-card-title">
                        <i class="bi bi-key-fill"></i> Assigned Roles
                    </h2>
                </div>
                <div class="ud-card-body">
                    <?php if (!empty($userRoles)): ?>
                        <div class="ud-roles-container">
                            <?php foreach ($userRoles as $role): ?>
                                <div class="ud-role-card">
                                    <div class="ud-role-header">
                                        <i class="bi bi-briefcase"></i>
                                        <span class="ud-role-name"><?= e($role['name'] ?? '') ?></span>
                                    </div>
                                    <div class="ud-role-desc">
                                        <?= e($role['description'] ?? '') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="ud-empty-state">
                            <div class="ud-empty-icon">👤</div>
                            <p class="ud-empty-text">No roles assigned to this user</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Projects Card -->
            <div class="ud-card">
                <div class="ud-card-header">
                    <h2 class="ud-card-title">
                        <i class="bi bi-folder2"></i> Project Memberships
                    </h2>
                </div>
                <div class="ud-card-body">
                    <?php if (!empty($userProjects)): ?>
                        <div class="ud-projects-list">
                            <?php foreach ($userProjects as $project): ?>
                                <div class="ud-project-item">
                                    <div class="ud-project-key"><?= e($project['key']) ?></div>
                                    <div class="ud-project-info">
                                        <div class="ud-project-name">
                                            <a href="<?= url('/projects/' . e($project['key'])) ?>" class="ud-project-link">
                                                <?= e($project['name']) ?>
                                            </a>
                                        </div>
                                        <div class="ud-project-desc"><?= e(substr($project['description'] ?? '', 0, 80)) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="ud-empty-state">
                            <div class="ud-empty-icon">📁</div>
                            <p class="ud-empty-text">Not a member of any projects</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Issues Card -->
            <div class="ud-card">
                <div class="ud-card-header">
                    <h2 class="ud-card-title">
                        <i class="bi bi-list-check"></i> Recent Issues
                    </h2>
                </div>
                <div class="ud-card-body">
                    <?php if (!empty($recentIssues)): ?>
                        <div class="ud-issues-list">
                            <?php foreach ($recentIssues as $issue): ?>
                                <div class="ud-issue-item">
                                    <div class="ud-issue-key">
                                        <a href="<?= url('/issues/' . e($issue['issue_key'])) ?>" class="ud-issue-link">
                                            <?= e($issue['issue_key']) ?>
                                        </a>
                                    </div>
                                    <div class="ud-issue-info">
                                        <div class="ud-issue-summary"><?= e(substr($issue['summary'], 0, 60)) ?></div>
                                        <div class="ud-issue-meta">
                                            <span class="ud-issue-status"><?= e($issue['status_name'] ?? '') ?></span>
                                            <span class="ud-issue-priority"><?= e($issue['priority_name'] ?? '') ?></span>
                                            <span class="ud-issue-date"><?= format_datetime($issue['created_at']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="ud-empty-state">
                            <div class="ud-empty-icon">📋</div>
                            <p class="ud-empty-text">No recent issues</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Timeline & Actions -->
        <div class="ud-sidebar">
            <!-- Account Timeline Card -->
            <div class="ud-card">
                <div class="ud-card-header">
                    <h2 class="ud-card-title">
                        <i class="bi bi-calendar-event"></i> Account Timeline
                    </h2>
                </div>
                <div class="ud-card-body ud-timeline-body">
                    <?php
                    $timelineEvents = [];
                    if ($user['created_at']) {
                        $timelineEvents[] = [
                            'icon' => 'plus-circle',
                            'color' => 'created',
                            'title' => 'Account Created',
                            'date' => format_datetime($user['created_at'])
                        ];
                    }
                    if ($user['email_verified_at']) {
                        $timelineEvents[] = [
                            'icon' => 'check-circle',
                            'color' => 'verified',
                            'title' => 'Email Verified',
                            'date' => format_datetime($user['email_verified_at'])
                        ];
                    }
                    if ($user['last_login_at']) {
                        $timelineEvents[] = [
                            'icon' => 'box-arrow-in-right',
                            'color' => 'login',
                            'title' => 'Last Login',
                            'date' => format_datetime($user['last_login_at'])
                        ];
                    }
                    if ($user['updated_at'] && $user['updated_at'] !== $user['created_at']) {
                        $timelineEvents[] = [
                            'icon' => 'pencil-square',
                            'color' => 'updated',
                            'title' => 'Last Updated',
                            'date' => format_datetime($user['updated_at'])
                        ];
                    }
                    ?>
                    
                    <?php if (!empty($timelineEvents)): ?>
                        <div class="ud-timeline">
                            <?php foreach ($timelineEvents as $event): ?>
                                <div class="ud-timeline-item">
                                    <div class="ud-timeline-dot ud-timeline-<?= $event['color'] ?>">
                                        <i class="bi bi-<?= $event['icon'] ?>"></i>
                                    </div>
                                    <div class="ud-timeline-content">
                                        <div class="ud-timeline-title"><?= $event['title'] ?></div>
                                        <div class="ud-timeline-date"><?= $event['date'] ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="ud-empty-state">
                            <div class="ud-empty-icon">📅</div>
                            <p class="ud-empty-text">No timeline events</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Statistics Card -->
            <div class="ud-card">
                <div class="ud-card-header">
                    <h2 class="ud-card-title">
                        <i class="bi bi-bar-chart"></i> Activity Summary
                    </h2>
                </div>
                <div class="ud-card-body">
                    <div class="ud-stats-list">
                        <div class="ud-stat-item">
                            <div class="ud-stat-label">
                                <i class="bi bi-file-earmark-plus"></i> Created Issues
                            </div>
                            <div class="ud-stat-value"><?= (int)($stats['created_issues'] ?? 0) ?></div>
                        </div>
                        <div class="ud-stat-item">
                            <div class="ud-stat-label">
                                <i class="bi bi-check-circle"></i> Resolved Issues
                            </div>
                            <div class="ud-stat-value"><?= (int)($stats['resolved_issues'] ?? 0) ?></div>
                        </div>
                        <div class="ud-stat-item">
                            <div class="ud-stat-label">
                                <i class="bi bi-chat-left-dots"></i> Comments Made
                            </div>
                            <div class="ud-stat-value"><?= (int)($stats['comments_count'] ?? 0) ?></div>
                        </div>
                        <div class="ud-stat-item">
                            <div class="ud-stat-label">
                                <i class="bi bi-check2-square"></i> Total Assigned
                            </div>
                            <div class="ud-stat-value"><?= (int)($stats['assigned_to_count'] ?? 0) ?></div>
                        </div>
                        <div class="ud-stat-item">
                            <div class="ud-stat-label">
                                <i class="bi bi-folder2-open"></i> Projects Member
                            </div>
                            <div class="ud-stat-value"><?= (int)($stats['projects_member_of'] ?? 0) ?></div>
                        </div>
                        <div class="ud-stat-item">
                            <div class="ud-stat-label">
                                <i class="bi bi-hourglass-split"></i> Hours Tracked
                            </div>
                            <div class="ud-stat-value"><?= number_format((float)($stats['time_tracked_hours'] ?? 0), 1) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="ud-card ud-card-actions">
                <div class="ud-card-header">
                    <h2 class="ud-card-title">
                        <i class="bi bi-lightning-fill"></i> Actions
                    </h2>
                </div>
                <div class="ud-card-body ud-actions-body">
                    <a href="<?= url('/admin/users/' . $user['id'] . '/edit') ?>" class="ud-action-btn ud-action-edit">
                        <i class="bi bi-pencil"></i> Edit User
                    </a>
                    <?php if ($user['is_active']): ?>
                        <form method="POST" action="<?= url('/admin/users/' . $user['id'] . '/deactivate') ?>" class="ud-action-form">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="ud-action-btn ud-action-warning" onclick="return confirm('Deactivate this user?')">
                                <i class="bi bi-lock"></i> Deactivate User
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?= url('/admin/users/' . $user['id'] . '/activate') ?>" class="ud-action-form">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="ud-action-btn ud-action-success">
                                <i class="bi bi-unlock"></i> Activate User
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="<?= url('/admin/users') ?>" class="ud-action-btn">
                        <i class="bi bi-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ================================================
       USER DETAIL PAGE - ENTERPRISE JIRA DESIGN
       ================================================ */

    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-light: #f0dce5 !important;
        --text-primary: #161B22 !important;
        --text-secondary: #626F86 !important;
        --bg-primary: #FFFFFF !important;
        --bg-secondary: #F7F8FA !important;
        --border-color: #DFE1E6 !important;
        --color-success: #216E4E !important;
        --color-warning: #E77817 !important;
        --color-error: #ED3C32 !important;
    }

    /* Page Layout */
    .ud-page {
        background: var(--bg-secondary);
        min-height: calc(100vh - 80px);
    }

    /* Breadcrumb Navigation */
    .ud-breadcrumb {
        display: flex;
        align-items: center;
        gap: 0;
        padding: 12px 32px;
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        font-size: 13px;
        flex-wrap: wrap;
    }

    .ud-breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--jira-blue);
        text-decoration: none;
        transition: color 0.2s ease;
        font-weight: 500;
    }

    .ud-breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .ud-breadcrumb-sep {
        margin: 0 8px;
        color: var(--text-secondary);
    }

    .ud-breadcrumb-current {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Header Section */
    .ud-header-section {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        padding: 32px;
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
    }

    .ud-header-left {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        flex: 1;
    }

    .ud-avatar-wrapper {
        flex-shrink: 0;
    }

    .ud-avatar {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .ud-avatar-initials {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 28px;
    }

    .ud-header-info {
        flex: 1;
    }

    .ud-page-title {
        margin: 0 0 8px 0;
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 12px;
        line-height: 1.2;
    }

    .ud-badge-admin {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: linear-gradient(135deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
        color: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .ud-header-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 8px;
    }

    .ud-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        color: var(--text-secondary);
    }

    .ud-header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .ud-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 6px;
        border: 1px solid transparent;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
    }

    .ud-btn-primary {
        background: var(--jira-blue);
        color: white;
        border-color: var(--jira-blue);
    }

    .ud-btn-primary:hover {
        background: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.2);
    }

    .ud-btn-secondary {
        background: var(--bg-primary);
        color: var(--text-primary);
        border-color: var(--border-color);
    }

    .ud-btn-secondary:hover {
        background: var(--bg-secondary);
        border-color: var(--text-secondary);
    }

    /* Content Grid */
    .ud-content-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        padding: 32px;
    }

    .ud-main {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .ud-sidebar {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Cards */
    .ud-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }

    .ud-card:hover {
        border-color: var(--jira-blue);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .ud-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-secondary);
    }

    .ud-card-title {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ud-card-title i {
        color: var(--jira-blue);
    }

    .ud-card-body {
        padding: 24px;
    }

    /* Detail Grid */
    .ud-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .ud-detail-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .ud-detail-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .ud-detail-value {
        font-size: 14px;
        color: var(--text-primary);
        word-break: break-word;
    }

    .ud-email-link {
        color: var(--jira-blue);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .ud-email-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    /* Badges */
    .ud-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .ud-badge-success {
        background: rgba(33, 110, 78, 0.15);
        color: var(--color-success);
    }

    .ud-badge-warning {
        background: rgba(231, 120, 23, 0.15);
        color: var(--color-warning);
    }

    .ud-badge-danger {
        background: rgba(237, 60, 50, 0.15);
        color: var(--color-error);
    }

    .ud-badge-admin {
        background: linear-gradient(135deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
        color: white;
    }

    .ud-badge-secondary {
        background: rgba(98, 111, 134, 0.15);
        color: var(--text-secondary);
    }

    .ud-status-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    /* Roles Container */
    .ud-roles-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .ud-role-card {
        padding: 16px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .ud-role-card:hover {
        border-color: var(--jira-blue);
        background: linear-gradient(135deg, var(--jira-light) 0%, transparent 100%);
    }

    .ud-role-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 13px;
        color: var(--text-primary);
    }

    .ud-role-header i {
        color: var(--jira-blue);
    }

    .ud-role-name {
        flex: 1;
    }

    .ud-role-desc {
        font-size: 12px;
        color: var(--text-secondary);
        margin-left: 20px;
    }

    /* Timeline */
    .ud-timeline-body {
        padding: 16px;
    }

    .ud-timeline {
        position: relative;
        padding-left: 0;
    }

    .ud-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 24px;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, var(--jira-blue), transparent);
    }

    .ud-timeline-item {
        display: flex;
        gap: 16px;
        position: relative;
        padding-bottom: 24px;
    }

    .ud-timeline-item:last-child {
        padding-bottom: 0;
    }

    .ud-timeline-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: white;
        font-size: 16px;
        border: 2px solid var(--bg-primary);
    }

    .ud-timeline-created {
        background: linear-gradient(135deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
    }

    .ud-timeline-verified {
        background: linear-gradient(135deg, var(--color-success) 0%, #164b35 100%);
    }

    .ud-timeline-login {
        background: linear-gradient(135deg, #0055cc 0%, #003d82 100%);
    }

    .ud-timeline-updated {
        background: linear-gradient(135deg, #5e4db2 0%, #44546f 100%);
    }

    .ud-timeline-content {
        flex: 1;
        padding-top: 4px;
    }

    .ud-timeline-title {
        font-weight: 600;
        font-size: 13px;
        color: var(--text-primary);
        margin-bottom: 2px;
    }

    .ud-timeline-date {
        font-size: 12px;
        color: var(--text-secondary);
    }

    /* Statistics */
    .ud-stats-list {
        display: flex;
        flex-direction: column;
    }

    .ud-stat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .ud-stat-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .ud-stat-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .ud-stat-label i {
        color: var(--jira-blue);
    }

    .ud-stat-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--jira-blue);
    }

    /* Actions Card */
    .ud-card-actions .ud-card-body {
        padding: 0;
    }

    .ud-actions-body {
        display: flex;
        flex-direction: column;
    }

    .ud-action-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        border: none;
        background: transparent;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        text-decoration: none;
        font-weight: 500;
        font-size: 13px;
        transition: all 0.2s ease;
        text-align: left;
    }

    .ud-action-btn:last-child {
        border-bottom: none;
    }

    .ud-action-btn:hover {
        background: var(--bg-secondary);
    }

    .ud-action-edit:hover {
        color: var(--jira-blue);
        background: linear-gradient(to right, var(--jira-light), transparent);
    }

    .ud-action-success:hover {
        color: var(--color-success);
        background: rgba(33, 110, 78, 0.1);
    }

    .ud-action-warning:hover {
        color: var(--color-warning);
        background: rgba(231, 120, 23, 0.1);
    }

    .ud-action-form {
        display: contents;
    }

    /* Empty State */
    .ud-empty-state {
        text-align: center;
        padding: 40px 20px;
    }

    .ud-empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .ud-empty-text {
        margin: 0;
        font-size: 14px;
        color: var(--text-secondary);
    }

    /* Projects List */
    .ud-projects-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .ud-project-item {
        display: flex;
        gap: 12px;
        padding: 12px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .ud-project-item:hover {
        border-color: var(--jira-blue);
        background: linear-gradient(135deg, var(--jira-light) 0%, transparent 100%);
    }

    .ud-project-key {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 50px;
        padding: 0 8px;
        background: linear-gradient(135deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
        color: white;
        border-radius: 4px;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
    }

    .ud-project-info {
        flex: 1;
        min-width: 0;
    }

    .ud-project-name {
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .ud-project-link {
        color: var(--jira-blue);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .ud-project-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .ud-project-desc {
        font-size: 12px;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Issues List */
    .ud-issues-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .ud-issue-item {
        display: flex;
        gap: 12px;
        padding: 12px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .ud-issue-item:hover {
        border-color: var(--jira-blue);
        background: linear-gradient(135deg, var(--jira-light) 0%, transparent 100%);
    }

    .ud-issue-key {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        min-width: 60px;
        padding: 0 8px;
        background: linear-gradient(135deg, #0055cc 0%, #003d82 100%);
        color: white;
        border-radius: 4px;
        font-weight: 700;
        font-size: 11px;
        flex-shrink: 0;
    }

    .ud-issue-link {
        color: white;
        text-decoration: none;
        transition: color 0.2s ease;
        text-align: center;
    }

    .ud-issue-link:hover {
        text-decoration: underline;
    }

    .ud-issue-info {
        flex: 1;
        min-width: 0;
    }

    .ud-issue-summary {
        font-weight: 600;
        font-size: 13px;
        color: var(--text-primary);
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ud-issue-meta {
        display: flex;
        gap: 8px;
        font-size: 11px;
        flex-wrap: wrap;
    }

    .ud-issue-status {
        padding: 2px 8px;
        background: rgba(33, 110, 78, 0.15);
        color: var(--color-success);
        border-radius: 3px;
        font-weight: 600;
    }

    .ud-issue-priority {
        padding: 2px 8px;
        background: rgba(231, 120, 23, 0.15);
        color: var(--color-warning);
        border-radius: 3px;
        font-weight: 600;
    }

    .ud-issue-date {
        padding: 2px 8px;
        color: var(--text-secondary);
    }

    /* Text Colors */
    .ud-text-success {
        color: var(--color-success) !important;
    }

    .ud-text-danger {
        color: var(--color-error) !important;
    }

    /* ================================================
       RESPONSIVE DESIGN
       ================================================ */

    @media (max-width: 1280px) {
        .ud-content-grid {
            grid-template-columns: 1fr;
        }

        .ud-header-section {
            flex-direction: column;
            gap: 16px;
        }

        .ud-header-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }

    @media (max-width: 1024px) {
        .ud-detail-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .ud-content-grid {
            padding: 20px;
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .ud-breadcrumb {
            padding: 12px 16px;
            font-size: 12px;
        }

        .ud-header-section {
            padding: 20px 16px;
            gap: 16px;
        }

        .ud-avatar {
            width: 64px;
            height: 64px;
        }

        .ud-avatar-initials {
            font-size: 24px;
        }

        .ud-page-title {
            font-size: 24px;
        }

        .ud-header-meta {
            gap: 12px;
        }

        .ud-meta-item {
            font-size: 13px;
        }

        .ud-btn {
            padding: 8px 12px;
            font-size: 13px;
        }

        .ud-content-grid {
            padding: 16px;
            grid-template-columns: 1fr;
        }

        .ud-card-header {
            padding: 16px;
        }

        .ud-card-body {
            padding: 16px;
        }

        .ud-card-title {
            font-size: 14px;
        }

        .ud-detail-label {
            font-size: 11px;
        }

        .ud-detail-value {
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .ud-breadcrumb {
            padding: 10px 12px;
            gap: 0;
        }

        .ud-breadcrumb-sep {
            margin: 0 4px;
        }

        .ud-breadcrumb-link,
        .ud-breadcrumb-current {
            font-size: 11px;
        }

        .ud-header-section {
            padding: 16px;
            flex-direction: column;
            gap: 12px;
        }

        .ud-header-left {
            gap: 12px;
        }

        .ud-avatar {
            width: 56px;
            height: 56px;
        }

        .ud-avatar-initials {
            font-size: 20px;
        }

        .ud-page-title {
            font-size: 20px;
            gap: 8px;
        }

        .ud-badge-admin {
            font-size: 11px;
            padding: 3px 10px;
        }

        .ud-header-meta {
            flex-direction: column;
            gap: 8px;
        }

        .ud-meta-item {
            font-size: 12px;
        }

        .ud-header-actions {
            flex-direction: column;
            gap: 8px;
        }

        .ud-btn {
            width: 100%;
            justify-content: center;
            padding: 10px 12px;
            font-size: 13px;
        }

        .ud-content-grid {
            padding: 12px;
            gap: 12px;
        }

        .ud-card-header {
            padding: 12px;
        }

        .ud-card-body {
            padding: 12px;
        }

        .ud-card-title {
            font-size: 13px;
        }

        .ud-detail-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .ud-stat-item {
            padding: 12px 0;
            font-size: 12px;
        }

        .ud-stat-value {
            font-size: 18px;
        }

        .ud-action-btn {
            padding: 12px;
            font-size: 12px;
        }

        .ud-empty-state {
            padding: 30px 12px;
        }

        .ud-empty-icon {
            font-size: 40px;
        }

        .ud-empty-text {
            font-size: 13px;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>
