<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="jira-project-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="project-breadcrumb">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= e($project['name']) ?></span>
    </div>

    <!-- Project Header Section -->
    <div class="project-header">
        <!-- Left: Project Avatar + Info -->
        <div class="project-header-left">
            <div class="project-avatar-wrapper">
                <?php if ($project['avatar'] ?? null): ?>
                <img src="<?= e($project['avatar']) ?>" class="project-avatar" alt="<?= e($project['name']) ?>">
                <?php else: ?>
                <div class="project-avatar project-avatar-initials">
                    <?= strtoupper(substr($project['key'], 0, 2)) ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="project-info">
                <h1 class="project-title"><?= e($project['name']) ?></h1>
                <div class="project-meta">
                    <span class="project-key"><?= e($project['key']) ?></span>
                    <?php if ($project['category_name'] ?? null): ?>
                    <span class="project-category"><?= e($project['category_name']) ?></span>
                    <?php endif; ?>
                    <?php if ($project['is_archived'] ?? false): ?>
                    <span class="project-badge archived">Archived</span>
                    <?php endif; ?>
                </div>
                <?php if ($project['description'] ?? null): ?>
                <p class="project-description"><?= e($project['description']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Navigation Buttons -->
        <div class="project-header-actions">
            <a href="<?= url("/projects/{$project['key']}/board") ?>" class="action-button">
                <i class="bi bi-kanban"></i>
                <span>Board</span>
            </a>
            <a href="<?= url("/projects/{$project['key']}/issues") ?>" class="action-button">
                <i class="bi bi-list-ul"></i>
                <span>Issues</span>
            </a>
            <a href="<?= url("/projects/{$project['key']}/backlog") ?>" class="action-button">
                <i class="bi bi-inbox"></i>
                <span>Backlog</span>
            </a>
            <a href="<?= url("/projects/{$project['key']}/sprints") ?>" class="action-button">
                <i class="bi bi-lightning-charge"></i>
                <span>Sprints</span>
            </a>
            <a href="<?= url("/projects/{$project['key']}/reports") ?>" class="action-button">
                <i class="bi bi-bar-chart"></i>
                <span>Reports</span>
            </a>
            <?php if (can('edit-project', $project['id'])): ?>
            <a href="<?= url("/projects/{$project['key']}/settings") ?>" class="action-button">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="quick-actions-bar">
        <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn-quick-action primary">
            <i class="bi bi-plus-lg"></i> Create Issue
        </a>
    </div>

    <!-- Main Content -->
    <div class="project-content">
        <!-- Left Column: Stats & Issues -->
        <div class="content-left">
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= e($stats['total_issues'] ?? 0) ?></div>
                    <div class="stat-label">Total Issues</div>
                    <div class="stat-icon">📊</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= e($stats['open_issues'] ?? 0) ?></div>
                    <div class="stat-label">Open</div>
                    <div class="stat-icon">🔵</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= e($stats['in_progress'] ?? 0) ?></div>
                    <div class="stat-label">In Progress</div>
                    <div class="stat-icon">⏳</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= e($stats['done_issues'] ?? 0) ?></div>
                    <div class="stat-label">Done</div>
                    <div class="stat-icon">✅</div>
                </div>
            </div>

            <!-- Recent Issues -->
            <div class="issues-card">
                <div class="card-header-bar">
                    <h2 class="card-title">Recent Issues</h2>
                    <a href="<?= url("/projects/{$project['key']}/issues") ?>" class="header-link">View All</a>
                </div>
                <div class="issues-container">
                    <?php if (empty($recentIssues)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <p>No issues yet</p>
                        <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="empty-link">Create the first one</a>
                    </div>
                    <?php else: ?>
                    <div class="issues-list">
                        <?php foreach (array_slice($recentIssues, 0, 6) as $issue): ?>
                        <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="issue-item">
                            <div class="issue-item-left">
                                <span class="issue-key"><?= e($issue['issue_key']) ?></span>
                                <span class="issue-summary"><?= e(substr($issue['summary'], 0, 60)) ?><?= strlen($issue['summary']) > 60 ? '...' : '' ?></span>
                            </div>
                            <div class="issue-item-right">
                                <span class="status-badge" style="background-color: <?= e($issue['status']['color'] ?? '#DFE1E6') ?>">
                                    <?= e($issue['status']['name'] ?? 'Open') ?>
                                </span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="activity-card">
                <div class="card-header-bar">
                    <h2 class="card-title">Recent Activity</h2>
                </div>
                <div class="activity-container">
                    <?php if (empty($activities)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📝</div>
                        <p>No recent activity</p>
                    </div>
                    <?php else: ?>
                    <div class="activity-feed">
                        <?php foreach (array_slice($activities, 0, 6) as $activity): ?>
                        <div class="activity-entry">
                            <img src="<?= e($activity['user']['avatar'] ?? '/images/default-avatar.png') ?>" 
                                 class="activity-avatar" alt="<?= e($activity['user']['display_name']) ?>">
                            <div class="activity-details">
                                <div class="activity-header">
                                    <span class="activity-user"><?= e($activity['user']['display_name']) ?></span>
                                    <span class="activity-time"><?= time_ago($activity['created_at']) ?></span>
                                </div>
                                <div class="activity-action">
                                    <?php 
                                    if (strpos($activity['action'], 'created') !== false) {
                                        $icon = '➕';
                                        $verb = 'created';
                                    } elseif (strpos($activity['action'], 'updated') !== false) {
                                        $icon = '✏️';
                                        $verb = 'updated';
                                    } elseif (strpos($activity['action'], 'deleted') !== false) {
                                        $icon = '🗑️';
                                        $verb = 'deleted';
                                    } elseif (strpos($activity['action'], 'comment') !== false) {
                                        $icon = '💬';
                                        $verb = 'commented';
                                    } else {
                                        $icon = '🔄';
                                        $verb = 'updated';
                                    }
                                    echo $icon . ' ' . $verb;
                                    if ($activity['issue']) {
                                        echo " <a href='" . url("/issue/{$activity['issue']['key']}") . "' class='activity-issue-link'>{$activity['issue']['key']}</a>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($activities) > 6): ?>
                    <div class="activity-footer">
                        <a href="<?= url("/projects/{$project['key']}/activity") ?>" class="view-more-link">
                            View All Activity
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="content-right">
            <!-- Project Details -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Project Details</h3>
                <div class="details-list">
                    <div class="detail-item">
                        <span class="detail-label">Type</span>
                        <span class="detail-value"><?= e(ucfirst($project['project_type'] ?? 'Software')) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Lead</span>
                        <span class="detail-value">
                            <?php if ($project['lead'] ?? null): ?>
                            <img src="<?= e($project['lead']['avatar'] ?? '/images/default-avatar.png') ?>" 
                                 class="detail-avatar" alt="">
                            <?= e($project['lead']['display_name']) ?>
                            <?php else: ?>
                            <span class="text-muted">Unassigned</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Created</span>
                        <span class="detail-value"><?= date('M j, Y', strtotime($project['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <h3 class="sidebar-card-title">Team Members</h3>
                    <?php if (can('manage-members', $project['id'])): ?>
                    <a href="<?= url("/projects/{$project['key']}/members") ?>" class="sidebar-link">Manage</a>
                    <?php endif; ?>
                </div>
                <?php if (empty($members ?? [])): ?>
                <p class="no-data">No team members</p>
                <?php else: ?>
                <div class="members-grid">
                    <?php foreach (array_slice($members ?? [], 0, 6) as $member): ?>
                    <img src="<?= e($member['avatar'] ?? '/images/default-avatar.png') ?>" 
                         class="member-avatar" title="<?= e($member['display_name']) ?>">
                    <?php endforeach; ?>
                    <?php if (count($members ?? []) > 6): ?>
                    <div class="member-avatar more-count">+<?= count($members ?? []) - 6 ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Links -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Quick Links</h3>
                <div class="quick-links">
                    <a href="<?= url("/projects/{$project['key']}/issues?status=open") ?>" class="quick-link-item">
                        <span>Open Issues</span>
                        <span class="link-badge"><?= e($stats['open_issues'] ?? 0) ?></span>
                    </a>
                    <a href="<?= url("/projects/{$project['key']}/issues?priority=highest") ?>" class="quick-link-item">
                        <span>High Priority</span>
                        <span class="link-badge"><?= e($stats['high_priority'] ?? 0) ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================
   JIRA PROJECT OVERVIEW - ENTERPRISE DESIGN
   ============================================ */

:root {
    --jira-blue: #8B1956 !important;
    --jira-blue-dark: #6F123F !important;
    --jira-dark: #161B22 !important;
    --jira-gray: #626F86 !important;
    --jira-light: #F7F8FA !important;
    --jira-border: #DFE1E6 !important;
}

/* Main Wrapper */
.jira-project-wrapper {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #F7F8FA;
    overflow: hidden;
    margin-top: -1.5rem;
    padding-top: 1.5rem;
    max-width: 100%;
    width: 100%;
}

/* Breadcrumb */
.project-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 32px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    font-size: 13px;
    flex-shrink: 0;
}

.breadcrumb-link {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--jira-blue);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.breadcrumb-link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

.breadcrumb-separator {
    color: var(--jira-gray);
    font-weight: 300;
}

.breadcrumb-current {
    color: var(--jira-dark);
    font-weight: 600;
}

/* Project Header */
.project-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 32px;
    padding: 32px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    flex-shrink: 0;
}

.project-header-left {
    display: flex;
    align-items: center;
    gap: 24px;
    flex: 1;
}

.project-avatar-wrapper {
    flex-shrink: 0;
}

.project-avatar {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.project-avatar-initials {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--jira-blue), #6F123F);
    color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.project-info {
    flex: 1;
}

.project-title {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    color: var(--jira-dark);
    letter-spacing: -0.3px;
}

.project-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 8px;
}

.project-key {
    display: inline-block;
    padding: 4px 12px;
    background: var(--jira-light);
    border: 1px solid var(--jira-border);
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    color: var(--jira-gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.project-category {
    font-size: 13px;
    color: var(--jira-gray);
}

.project-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #FFF3C1;
    color: #974F0C;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.project-description {
    margin: 12px 0 0 0;
    font-size: 15px;
    color: var(--jira-gray);
    line-height: 1.5;
}

/* Header Actions */
.project-header-actions {
    display: flex;
    gap: 12px;
    flex-shrink: 0;
}

.action-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 16px;
    background: #FFFFFF;
    border: 1px solid var(--jira-border);
    border-radius: 6px;
    color: var(--jira-dark);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.action-button:hover {
    background: var(--jira-light);
    border-color: #B6C2CF;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
}

/* Quick Actions Bar */
.quick-actions-bar {
    padding: 16px 32px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    flex-shrink: 0;
}

.btn-quick-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 6px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
    line-height: 1;
    transition: all 0.2s ease;
}

.btn-quick-action.primary {
    background: var(--jira-blue);
    color: #FFFFFF !important;
}

.btn-quick-action.primary:hover {
    background: var(--jira-blue-dark);
    box-shadow: 0 4px 12px rgba(139, 25, 86, 0.3);
    color: #FFFFFF !important;
}

.btn-quick-action i {
    color: #FFFFFF !important;
    font-size: 16px;
}

/* Main Content Area */
.project-content {
    display: flex;
    gap: 24px;
    padding: 24px 24px;
    overflow-y: auto;
    flex: 1;
    overflow-x: hidden;
}

.content-left {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.content-right {
    flex: 0 0 280px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
}

.stat-card {
    background: #FFFFFF;
    border: 1px solid var(--jira-border);
    border-radius: 8px;
    padding: 24px;
    position: relative;
    overflow: hidden;
    transition: all 0.2s ease;
}

.stat-card:hover {
    border-color: #B6C2CF;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--jira-dark);
    margin-bottom: 8px;
}

.stat-label {
    font-size: 13px;
    color: var(--jira-gray);
    font-weight: 600;
}

.stat-icon {
    position: absolute;
    top: 16px;
    right: 16px;
    font-size: 24px;
    opacity: 0.3;
}

/* Cards */
.issues-card,
.activity-card,
.sidebar-card {
    background: #FFFFFF;
    border: 1px solid var(--jira-border);
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.card-header-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border-bottom: 1px solid var(--jira-border);
    flex-shrink: 0;
}

.card-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--jira-dark);
}

.header-link {
    color: var(--jira-blue);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.2s ease;
}

.header-link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

/* Issues Container */
.issues-container,
.activity-container {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.issues-list {
    display: flex;
    flex-direction: column;
}

.issue-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--jira-border);
    text-decoration: none;
    color: var(--jira-dark);
    transition: all 0.15s ease;
}

.issue-item:hover {
    background: var(--jira-light);
}

.issue-item-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    min-width: 0;
}

.issue-key {
    font-size: 12px;
    font-weight: 700;
    color: var(--jira-gray);
    white-space: nowrap;
    text-transform: uppercase;
}

.issue-summary {
    font-size: 14px;
    font-weight: 500;
    color: var(--jira-dark);
    text-decoration: none;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.issue-item-right {
    flex-shrink: 0;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    color: #FFFFFF;
    text-transform: uppercase;
}

/* Activity Feed */
.activity-feed {
    display: flex;
    flex-direction: column;
}

.activity-entry {
    display: flex;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--jira-border);
}

.activity-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    flex-shrink: 0;
    object-fit: cover;
}

.activity-details {
    flex: 1;
    min-width: 0;
}

.activity-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}

.activity-user {
    font-size: 13px;
    font-weight: 600;
    color: var(--jira-dark);
}

.activity-time {
    font-size: 12px;
    color: var(--jira-gray);
}

.activity-action {
    font-size: 13px;
    color: var(--jira-gray);
}

.activity-issue-link {
    color: var(--jira-blue);
    text-decoration: none;
    font-weight: 600;
}

.activity-issue-link:hover {
    text-decoration: underline;
}

.activity-footer {
    padding: 12px 20px;
    border-top: 1px solid var(--jira-border);
    text-align: center;
}

.view-more-link {
    color: var(--jira-blue);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: color 0.2s ease;
}

.view-more-link:hover {
    color: var(--jira-blue-dark);
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    color: var(--jira-gray);
    text-align: center;
}

.empty-icon {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.6;
}

.empty-state p {
    margin: 0 0 12px 0;
    font-size: 14px;
    font-weight: 600;
}

.empty-link {
    color: var(--jira-blue);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
}

.empty-link:hover {
    text-decoration: underline;
}

/* Sidebar Cards */
.sidebar-card {
    padding: 0;
}

.sidebar-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border-bottom: 1px solid var(--jira-border);
    gap: 12px;
}

.sidebar-card-title {
    margin: 0;
    padding: 15px;
    font-size: 14px;
    font-weight: 700;
    color: var(--jira-dark);
    flex: 1;
}

.sidebar-link {
    color: var(--jira-blue);
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
}

.sidebar-link:hover {
    text-decoration: underline;
}

/* Details List */
.details-list {
    padding: 0 20px 20px 20px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 16px 0;
    border-bottom: 1px solid var(--jira-border);
}

.detail-item:first-child {
    padding-top: 8px;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--jira-gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 14px;
    font-weight: 500;
    color: var(--jira-dark);
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
}

/* Members Grid */
.members-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    padding: 24px;
}

.member-avatar {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid var(--jira-border);
    transition: all 0.2s ease;
}

.member-avatar:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.member-avatar.more-count {
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--jira-light);
    color: var(--jira-gray);
    font-size: 14px;
    font-weight: 700;
}

/* Quick Links */
.quick-links {
    display: flex;
    flex-direction: column;
}

.quick-link-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    border-bottom: 1px solid var(--jira-border);
    color: var(--jira-dark);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.15s ease;
}

.quick-link-item:hover {
    background: var(--jira-light);
}

.quick-link-item:last-child {
    border-bottom: none;
}

.link-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 8px;
    background: #E3F2FD;
    color: var(--jira-blue);
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
}

.no-data {
    padding: 24px;
    color: var(--jira-gray);
    font-size: 13px;
    margin: 0;
}

/* Responsive */
@media (max-width: 1400px) {
    .project-header {
        gap: 24px;
    }
}

@media (max-width: 1200px) {
    .content-right {
        flex: 0 0 280px;
    }
}

@media (max-width: 1024px) {
    .project-content {
        flex-direction: column;
        gap: 24px;
    }
    
    .content-right {
        flex: 1;
    }
    
    .project-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .project-header-actions {
        width: 100%;
        flex-wrap: wrap;
    }
}

@media (max-width: 768px) {
    .project-header {
        padding: 20px;
        gap: 16px;
    }
    
    .project-content {
        padding: 16px;
        gap: 16px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .stat-card {
        padding: 16px;
    }
    
    .project-title {
        font-size: 24px;
    }
    
    .project-avatar,
    .project-avatar-initials {
        width: 64px;
        height: 64px;
        font-size: 24px;
    }
}
</style>

<?php \App\Core\View::endSection(); ?>
