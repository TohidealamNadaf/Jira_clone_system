<?php \App\Core\View:: extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<div class="jira-project-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="project-breadcrumb">
        <a href="<?= url('/') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Home
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">Projects</a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link"><?= e($project['key']) ?></a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Activity</span>
    </div>

    <!-- Project Header -->
    <div class="project-header">
        <div class="project-header-left">
            <div class="project-avatar-wrapper">
                <?php if ($project['avatar'] ?? null): ?>
                    <img src="<?= e(url($project['avatar'])) ?>" class="project-avatar" alt="<?= e($project['name']) ?>">
                <?php else: ?>
                    <span class="project-avatar-initials">
                        <?= strtoupper(substr($project['key'], 0, 2)) ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="project-info">
                <h1 class="project-title">Project Activity</h1>
                <div class="project-meta">
                    <span class="project-key"><?= e($project['name']) ?></span>
                </div>
            </div>
        </div>
        <div class="project-header-actions">
            <a href="<?= url("/projects/{$project['key']}") ?>" class="action-button">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="project-content">
        <!-- Left Column: Activity Feed -->
        <div class="content-left">
            <div class="activity-card">
                <div class="card-header-bar">
                    <h2 class="card-title">Activity Feed</h2>
                </div>
                <div class="activity-container">
                    <?php if (empty($activities)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📝</div>
                            <p>No activity recorded yet</p>
                        </div>
                    <?php else: ?>
                        <div class="activity-feed">
                            <?php foreach ($activities as $activity): ?>
                                <div class="activity-entry">
                                    <?php
                                    $avatarUrl = avatar($activity['user']['avatar'] ?? null, $activity['user']['display_name'] ?? 'User');
                                    if (!empty($avatarUrl)): ?>
                                        <img src="<?= e($avatarUrl) ?>" class="activity-avatar"
                                            alt="<?= e($activity['user']['display_name']) ?>"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="activity-avatar-placeholder" style="display:none;"
                                            title="<?= e($activity['user']['display_name']) ?>">
                                            <?= avatarInitials($activity['user']['display_name'] ?? 'User', $activity['user']['email'] ?? '') ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="activity-avatar-placeholder"
                                            title="<?= e($activity['user']['display_name']) ?>">
                                            <?= avatarInitials($activity['user']['display_name'] ?? 'User', $activity['user']['email'] ?? '') ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="activity-details">
                                        <div class="activity-header">
                                            <span class="activity-user"><?= e($activity['user']['display_name']) ?></span>
                                            <span class="activity-time"><?= time_ago($activity['created_at']) ?></span>
                                        </div>
                                        <div class="activity-action">
                                            <?php
                                            $icon = '🔄';
                                            $verb = 'updated';
                                            $action = $activity['action'] ?? '';

                                            if (strpos($action, 'created') !== false) {
                                                $icon = '➕';
                                                $verb = 'created';
                                            } elseif (strpos($action, 'updated') !== false) {
                                                $icon = '✏️';
                                                $verb = 'updated';
                                            } elseif (strpos($action, 'deleted') !== false) {
                                                $icon = '🗑️';
                                                $verb = 'deleted';
                                            } elseif (strpos($action, 'comment') !== false) {
                                                $icon = '💬';
                                                $verb = 'commented on';
                                            } elseif (strpos($action, 'assigned') !== false) {
                                                $icon = '👤';
                                                $verb = 'assigned';
                                            } elseif (strpos($action, 'transitioned') !== false) {
                                                $icon = '➡️';
                                                $verb = 'transitioned';
                                            }

                                            echo "<span class='action-icon'>{$icon}</span> " . $verb;
                                            if (!empty($activity['issue'])) {
                                                echo " <a href='" . url("/issue/{$activity['issue']['key']}") . "' class='activity-issue-link'>{$activity['issue']['key']} - " . e(substr($activity['issue']['summary'], 0, 50)) . (strlen($activity['issue']['summary']) > 50 ? '...' : '') . "</a>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="content-right">
            <!-- Reuse sidebar cards style for consistency -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Project Details</h3>
                <div class="sidebar-info-group">
                    <div class="info-label">Key</div>
                    <div class="info-value"><?= e($project['key']) ?></div>
                </div>
                <div class="sidebar-info-group">
                    <div class="info-label">Lead</div>
                    <div class="user-chip">
                        <?php if ($project['lead'] ?? null): ?>
                            <img src="<?= e(avatar($project['lead']['avatar'] ?? null, $project['lead']['display_name'] ?? 'Lead')) ?>"
                                class="chip-avatar" alt="">
                            <span><?= e($project['lead']['display_name']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">Unassigned</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Active Contributors</h3>
                <?php
                // Extract unique users from activities
                $contributors = [];
                foreach ($activities as $act) {
                    if (isset($act['user']['id'])) {
                        $contributors[$act['user']['id']] = $act['user'];
                    }
                }
                $contributors = array_slice($contributors, 0, 8);
                ?>
                <div class="avatar-grid">
                    <?php foreach ($contributors as $user): ?>
                        <div class="avatar-item" title="<?= e($user['display_name']) ?>">
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?= e(avatar($user['avatar'])) ?>" alt="<?= e($user['display_name']) ?>">
                            <?php else: ?>
                                <div class="avatar-text"><?= strtoupper(substr($user['display_name'], 0, 1)) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
   JIRA PROJECT STYLES (Copied from Overview)
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
        width: 64px;
        height: 64px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .project-avatar-initials {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--jira-blue), #6F123F);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .project-info {
        flex: 1;
    }

    .project-title {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: var(--jira-dark);
        letter-spacing: -0.3px;
    }

    .project-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 4px;
    }

    .project-key {
        font-size: 14px;
        color: var(--jira-gray);
    }

    /* Header Actions */
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

    /* Cards */
    .activity-card,
    .sidebar-card {
        background: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .sidebar-card {
        padding: 20px;
    }

    .card-header-bar {
        padding: 20px;
        border-bottom: 1px solid var(--jira-border);
    }

    .card-title,
    .sidebar-card-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--jira-dark);
    }

    .sidebar-card-title {
        margin-bottom: 16px;
    }

    /* Activity Feed Styles */
    .activity-container {
        flex: 1;
        overflow-y: auto;
    }

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

    .activity-avatar-placeholder {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        flex-shrink: 0;
        background: linear-gradient(135deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .activity-details {
        flex: 1;
        min-width: 0;
    }

    .activity-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
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
        line-height: 1.5;
    }

    .action-icon {
        margin-right: 4px;
        font-size: 12px;
    }

    .activity-issue-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 600;
    }

    .activity-issue-link:hover {
        text-decoration: underline;
    }

    /* Sidebar Details */
    .sidebar-info-group {
        margin-bottom: 12px;
    }

    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--jira-gray);
        text-transform: uppercase;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 14px;
        font-weight: 500;
        color: var(--jira-dark);
    }

    .user-chip {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chip-avatar {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Avatar Grid */
    .avatar-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
    }

    .avatar-item {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.2s;
        background: var(--jira-blue-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-item:hover {
        transform: scale(1.1);
    }

    .avatar-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-text {
        color: white;
        font-weight: 700;
        font-size: 12px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .project-content {
            flex-direction: column;
            padding: 16px;
        }

        .content-right {
            width: 100%;
            flex: auto;
            flex-direction: row;
            flex-wrap: wrap;
        }

        .sidebar-card {
            flex: 1;
            min-width: 250px;
        }
    }

    @media (max-width: 768px) {
        .project-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .content-right {
            flex-direction: column;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>