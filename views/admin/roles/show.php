<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-shield-lock"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/admin/roles') ?>" class="breadcrumb-link">Roles</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= e($role['name']) ?></span>
    </div>

    <!-- Page Header Section -->
    <div class="page-header">
        <div class="header-left">
            <div class="header-icon-badge">
                <i class="bi bi-shield-fill"></i>
            </div>
            <div class="header-info">
                <h1 class="page-title"><?= e($role['name']) ?></h1>
                <p class="page-subtitle">
                    <?php if ($role['is_system'] ?? false): ?>
                        <span class="status-pill archived"><i class="bi bi-lock-fill"></i> System Role</span>
                    <?php else: ?>
                        <span class="status-pill blue">Custom Role</span>
                    <?php endif; ?>
                    &middot; Created <?= format_date($role['created_at']) ?>
                </p>
            </div>
        </div>
        <div class="header-actions">
            <?php if (!($role['is_system'] ?? false)): ?>
                <a href="<?= url('/admin/roles/' . $role['id'] . '/edit') ?>" class="action-button primary">
                    <i class="bi bi-pencil"></i>
                    <span>Edit Role</span>
                </a>
                <form action="<?= url('/admin/roles/' . $role['id']) ?>" method="POST" style="display: inline;"
                    onsubmit="return confirm('Are you sure you want to delete this role?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="action-button danger">
                        <i class="bi bi-trash"></i>
                        <span>Delete</span>
                    </button>
                </form>
            <?php else: ?>
                <button class="action-button disabled" disabled title="System roles cannot be deleted">
                    <i class="bi bi-lock"></i>
                    <span>System Protected</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="page-content">
        <div class="content-left">
            <!-- Permissions Card -->
            <div class="enterprise-card">
                <div class="card-header-bar">
                    <h2 class="card-title">Permissions</h2>
                    <span class="badge-count"><?= count($permissions) ?> Active</span>
                </div>
                <div class="card-body-padded">
                    <?php if (empty($permissions)): ?>
                        <div class="empty-state-content py-5">
                            <i class="bi bi-shield-slash" style="font-size: 48px; color: #DFE1E6;"></i>
                            <p class="mt-3 text-muted">No permissions assigned to this role</p>
                            <?php if (!($role['is_system'] ?? false)): ?>
                                <a href="<?= url('/admin/roles/' . $role['id'] . '/edit') ?>" class="action-button mt-2">
                                    Manage Permissions
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php
                        // Group permissions by category
                        $grouped = [];
                        foreach ($permissions as $perm) {
                            $category = $perm['category'] ?? 'General';
                            $grouped[$category][] = $perm;
                        }
                        ksort($grouped);
                        ?>

                        <div class="permission-groups">
                            <?php foreach ($grouped as $category => $perms): ?>
                                <div class="permission-group">
                                    <h3 class="group-title">
                                        <?= e(ucwords(str_replace('_', ' ', $category))) ?>
                                    </h3>
                                    <div class="permission-grid">
                                        <?php foreach ($perms as $perm): ?>
                                            <div class="permission-item">
                                                <div class="perm-icon">
                                                    <i class="bi bi-check-lg"></i>
                                                </div>
                                                <div class="perm-details">
                                                    <span class="perm-name"><?= e($perm['name']) ?></span>
                                                    <?php if (!empty($perm['description'])): ?>
                                                        <span class="perm-desc"><?= e($perm['description']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content-right">
            <!-- About Card -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">About this Role</h3>
                <div class="sidebar-content">
                    <div class="info-row">
                        <label>Description</label>
                        <p><?= e($role['description'] ?? 'No description provided') ?></p>
                    </div>
                </div>
            </div>

            <!-- Users Card -->
            <div class="sidebar-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="sidebar-card-title mb-0">Assigned Users</h3>
                    <span class="badge-count"><?= count($users) ?></span>
                </div>

                <div class="sidebar-list">
                    <?php if (empty($users)): ?>
                        <p class="text-muted small fst-italic">No users assigned</p>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <a href="<?= url('/admin/users/' . $user['id'] . '/edit') ?>" class="user-item">
                                <div class="user-avatar-small">
                                    <?= strtoupper(substr($user['display_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div class="user-info">
                                    <span class="user-name"><?= e($user['display_name']) ?></span>
                                    <span class="user-email"><?= e($user['email']) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
   ADMIN COMPONENT STYLES (Shared)
   ============================================ */

    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-dark: #161B22 !important;
        --jira-gray: #626F86 !important;
        --jira-light: #F7F8FA !important;
        --jira-border: #DFE1E6 !important;
        --white: #FFFFFF;
    }

    .page-wrapper {
        background: var(--jira-light);
        min-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
    }

    /* Breadcrumb */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 32px;
        background: var(--white);
        border-bottom: 1px solid var(--jira-border);
        font-size: 13px;
        margin: 0;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--jira-gray);
    }

    .breadcrumb-current {
        color: var(--jira-dark);
        font-weight: 600;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 32px;
        background: var(--white);
        border-bottom: 1px solid var(--jira-border);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-icon-badge {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--jira-blue), var(--jira-blue-dark));
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.2);
    }

    .page-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
    }

    .page-subtitle {
        font-size: 13px;
        color: var(--jira-gray);
        margin: 4px 0 0 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: var(--white);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        color: var(--jira-dark);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
    }

    .action-button:hover {
        background: #F4F5F7;
        transform: translateY(-1px);
    }

    .action-button.primary {
        background: var(--jira-blue);
        color: var(--white);
        border: none;
    }

    .action-button.primary:hover {
        background: var(--jira-blue-dark);
    }

    .action-button.danger {
        color: #DE350B;
        border-color: #FFBDAD;
    }

    .action-button.danger:hover {
        background: #FFEBE6;
    }

    .action-button.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: #F4F5F7;
    }

    /* Page Content */
    .page-content {
        display: flex;
        gap: 20px;
        padding: 20px 32px;
        flex: 1;
    }

    .content-left {
        flex: 1;
        min-width: 0;
    }

    .content-right {
        width: 280px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Cards */
    .enterprise-card {
        background: var(--white);
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
    }

    .card-header-bar {
        padding: 14px 20px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
    }

    .card-body-padded {
        padding: 24px;
    }

    .badge-count {
        font-size: 12px;
        font-weight: 600;
        background: #F4F5F7;
        color: var(--jira-gray);
        padding: 4px 10px;
        border-radius: 20px;
    }

    /* Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-pill.archived {
        background: #FFEBE6;
        color: #BF2600;
    }

    .status-pill.blue {
        background: #DEEBFF;
        color: #0747A6;
    }

    /* Permissions Styling */
    .permission-group {
        margin-bottom: 24px;
    }

    .permission-group:last-child {
        margin-bottom: 0;
    }

    .group-title {
        font-size: 13px;
        text-transform: uppercase;
        color: var(--jira-gray);
        font-weight: 700;
        border-bottom: 1px solid var(--jira-border);
        padding-bottom: 8px;
        margin-bottom: 16px;
    }

    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
    }

    .permission-item {
        display: flex;
        gap: 12px;
        padding: 12px;
        background: #F9FAFB;
        border: 1px solid var(--jira-border);
        border-radius: 6px;
    }

    .perm-icon {
        color: var(--jira-success, #00875A);
        font-size: 18px;
        padding-top: 2px;
    }

    .perm-name {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: var(--jira-dark);
        margin-bottom: 2px;
    }

    .perm-desc {
        display: block;
        font-size: 12px;
        color: var(--jira-gray);
        line-height: 1.4;
    }

    /* Sidebar */
    .sidebar-card {
        background: var(--white);
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        padding: 20px;
    }

    .sidebar-card-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 16px 0;
    }

    .info-row label {
        display: block;
        font-size: 12px;
        color: var(--jira-gray);
        margin-bottom: 4px;
    }

    .info-row p {
        font-size: 14px;
        color: var(--jira-dark);
        margin: 0;
        line-height: 1.5;
    }

    /* User List in Sidebar */
    .sidebar-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .user-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        border-radius: 6px;
        text-decoration: none;
        transition: background 0.2s;
    }

    .user-item:hover {
        background: #F4F5F7;
    }

    .user-avatar-small {
        width: 28px;
        height: 28px;
        background: var(--jira-blue);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .user-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--jira-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        font-size: 11px;
        color: var(--jira-gray);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .page-content {
            flex-direction: column;
        }

        .content-right {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }

        .header-actions {
            width: 100%;
        }

        .action-button {
            flex: 1;
            justify-content: center;
        }

        .content-right {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>