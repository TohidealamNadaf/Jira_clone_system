<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-shield-lock"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Roles Management</span>
    </div>

    <!-- Page Header Section -->
    <div class="page-header">
        <div class="header-left">
            <div class="header-icon-badge">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="header-info">
                <h1 class="page-title">Roles Management</h1>
                <p class="page-subtitle">Define and manage user roles and access permissions.</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="<?= url('/admin/roles/create') ?>" class="action-button primary">
                <i class="bi bi-plus-lg"></i>
                <span>Create Role</span>
            </a>
            <button class="action-button" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="page-content">
        <div class="content-left">
            <!-- Roles Card -->
            <div class="enterprise-card">
                <div class="card-header-bar">
                    <h2 class="card-title">All Roles</h2>
                    <span class="badge-count"><?= count($roles ?? []) ?> Total</span>
                </div>
                <div class="table-container">
                    <table class="enterprise-table">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Description</th>
                                <th>Users</th>
                                <th>Permissions</th>
                                <th width="120" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($roles)): ?>
                                <tr>
                                    <td colspan="5" class="empty-state-row">
                                        <div class="empty-state-content">
                                            <i class="bi bi-shield-slash"></i>
                                            <p>No roles found</p>
                                            <p class="text-muted small">Create a role to get started</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($roles as $role): ?>
                                    <tr class="table-row-hover">
                                        <td class="name-cell">
                                            <div class="project-avatar-small"
                                                style="background: linear-gradient(135deg, #EAE6FF, #DFE1E6); color: #403294;">
                                                <i class="bi bi-shield"></i>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="<?= url('/admin/roles/' . $role['id']) ?>" class="project-link">
                                                    <?= e($role['name']) ?>
                                                </a>
                                                <?php if ($role['is_system'] ?? false): ?>
                                                    <span class="status-pill archived"
                                                        style="font-size: 10px; padding: 2px 6px; width: auto;">System</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted small"><?= e($role['description'] ?? '-') ?></span>
                                        </td>
                                        <td>
                                            <span class="stat-badge blue">
                                                <i class="bi bi-people"></i> <?= (int) ($role['user_count'] ?? 0) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="stat-badge teal">
                                                <i class="bi bi-key"></i> <?= (int) ($role['permission_count'] ?? 0) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($role['is_system'] ?? false): ?>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="<?= url('/admin/roles/' . $role['id']) ?>" class="action-button"
                                                        style="padding: 4px 8px;" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <span class="action-button"
                                                        style="padding: 4px 8px; cursor: default; opacity: 0.7;"
                                                        title="Protected System Role">
                                                        <i class="bi bi-lock-fill"></i>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="<?= url('/admin/roles/' . $role['id']) ?>" class="action-button"
                                                        style="padding: 4px 8px;" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= url('/admin/roles/' . $role['id'] . '/edit') ?>"
                                                        class="action-button" style="padding: 4px 8px;" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="<?= url('/admin/roles/' . $role['id']) ?>" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="action-button"
                                                            style="padding: 4px 8px; color: #DC3545 !important;" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="content-right">
            <!-- Sidebar Card -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">About Roles</h3>
                <div class="sidebar-content">
                    <p class="text-muted small mb-3">Roles define access levels for users in the system.</p>
                    <div class="sidebar-links">
                        <div class="sidebar-link-item" style="cursor: default;">
                            <i class="bi bi-shield-lock"></i>
                            <div class="d-flex flex-column">
                                <span style="font-weight: 600; font-size: 13px;">System Roles</span>
                                <span class="text-muted" style="font-size: 11px;">Cannot be deleted</span>
                            </div>
                        </div>
                        <div class="sidebar-link-item" style="cursor: default;">
                            <i class="bi bi-person-plus"></i>
                            <div class="d-flex flex-column">
                                <span style="font-weight: 600; font-size: 13px;">Custom Roles</span>
                                <span class="text-muted" style="font-size: 11px;">Create your own</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Related Settings</h3>
                <div class="sidebar-links">
                    <a href="<?= url('/admin/users') ?>" class="sidebar-link-item">
                        <i class="bi bi-people"></i>
                        <span>Manage Users</span>
                    </a>
                    <a href="<?= url('/admin/global-permissions') ?>" class="sidebar-link-item">
                        <i class="bi bi-lock"></i>
                        <span>Global Permissions</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
   ADMIN PROJECTS / ROLES - ENTERPRISE DESIGN
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
        font-size: 14px;
        color: var(--jira-gray);
        margin: 4px 0 0 0;
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
        color: var(--jira-dark) !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
    }

    .action-button:hover {
        background: #F4F5F7;
        border-color: #B6C2CF;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .action-button.primary {
        background: var(--jira-blue) !important;
        color: var(--white) !important;
        border: none;
    }

    .action-button.primary span {
        color: var(--white) !important;
        opacity: 1 !important;
    }

    .action-button.primary:hover {
        background: var(--jira-blue-dark) !important;
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.3);
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

    .badge-count {
        font-size: 12px;
        font-weight: 600;
        background: #F4F5F7;
        color: var(--jira-gray);
        padding: 4px 10px;
        border-radius: 20px;
    }

    /* Table */
    .table-container {
        overflow-x: auto;
    }

    .enterprise-table {
        width: 100%;
        border-collapse: collapse;
    }

    .enterprise-table th {
        background: #F9FAFB;
        padding: 10px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--jira-border);
    }

    .enterprise-table td {
        padding: 12px 20px;
        border-bottom: 1px solid var(--jira-border);
        font-size: 13px;
        color: var(--jira-dark);
        vertical-align: middle;
    }

    .table-row-hover:hover {
        background: #F9FAFB;
    }

    .name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .project-avatar-small {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #F4F5F7, #DFE1E6);
        color: var(--jira-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        border-radius: 6px;
        flex-shrink: 0;
    }

    .project-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 600;
    }

    .project-link:hover {
        text-decoration: underline;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .stat-badge.blue {
        background: #E9F2FF;
        color: #0052CC;
    }

    .stat-badge.teal {
        background: #E6FCF5;
        color: #08845D;
    }

    .status-pill {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        text-align: center;
    }

    .status-pill.archived {
        background: #FFEBE6;
        color: #BF2600;
    }

    /* Sidebar Cards */
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

    .sidebar-links {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .sidebar-link-item {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--jira-dark);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        padding: 8px 12px;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .sidebar-link-item:hover {
        background: var(--jira-light);
        color: var(--jira-blue);
    }

    .sidebar-link-item i {
        color: var(--jira-gray);
    }

    /* Empty State */
    .empty-state-row {
        padding: 60px 0 !important;
    }

    .empty-state-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        color: #A5ADBA;
    }

    .empty-state-content i {
        font-size: 48px;
    }

    .empty-state-content p {
        font-size: 16px;
        margin: 0;
    }

    /* Responsive Breakpoints */
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