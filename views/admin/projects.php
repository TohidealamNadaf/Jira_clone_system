<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-shield-lock"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Projects Management</span>
    </div>

    <!-- Page Header Section -->
    <div class="page-header">
        <div class="header-left">
            <div class="header-icon-badge">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
            <div class="header-info">
                <h1 class="page-title">Projects Management</h1>
                <p class="page-subtitle">Oversee and manage all projects across the enterprise system.</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="<?= url('/projects/create') ?>" class="action-button primary">
                <i class="bi bi-plus-lg"></i>
                <span>Create Project</span>
            </a>
            <button class="action-button" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- Toolbar Section -->
    <div class="page-toolbar">
        <form action="<?= url('/admin/projects') ?>" method="GET" class="search-toolbar">
            <div class="search-input-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="toolbar-input" placeholder="Search by project name or key..."
                    value="<?= e($search ?? '') ?>">
            </div>
            <div class="toolbar-actions">
                <button type="submit" class="btn-toolbar-primary">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="<?= url('/admin/projects') ?>" class="btn-toolbar-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Main Content Area -->
    <div class="page-content">
        <div class="content-left">
            <!-- Projects Card -->
            <div class="enterprise-card">
                <div class="card-header-bar">
                    <h2 class="card-title">All Projects</h2>
                    <span class="badge-count"><?= $pagination['total'] ?> Total</span>
                </div>
                <div class="table-container">
                    <table class="enterprise-table">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Key</th>
                                <th>Lead</th>
                                <th>Issues</th>
                                <th>Members</th>
                                <th>Created</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projects)): ?>
                                <tr>
                                    <td colspan="7" class="empty-state-row">
                                        <div class="empty-state-content">
                                            <i class="bi bi-inbox"></i>
                                            <p>No projects found matching your criteria</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($projects as $project): ?>
                                    <tr class="table-row-hover">
                                        <td class="name-cell">
                                            <div class="project-avatar-small">
                                                <?= strtoupper(substr($project['key'], 0, 2)) ?>
                                            </div>
                                            <a href="<?= url('/projects/' . $project['key']) ?>" class="project-link">
                                                <?= e($project['name']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="project-key-badge"><?= e($project['key']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($project['lead_id']): ?>
                                                <?php
                                                $lead = \App\Core\Database::selectOne(
                                                    "SELECT display_name FROM users WHERE id = ?",
                                                    [$project['lead_id']]
                                                );
                                                ?>
                                                <div class="lead-info">
                                                    <i class="bi bi-person-circle"></i>
                                                    <?= e($lead['display_name'] ?? 'N/A') ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-unassigned">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="stat-badge blue">
                                                <i class="bi bi-list-check"></i> <?= $project['issue_count'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="stat-badge teal">
                                                <i class="bi bi-people"></i> <?= $project['member_count'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td class="date-cell">
                                            <?= format_date($project['created_at']) ?>
                                        </td>
                                        <td>
                                            <?php if ($project['is_archived']): ?>
                                                <span class="status-pill archived">Archived</span>
                                            <?php else: ?>
                                                <span class="status-pill active">Active</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                <?php if ($pagination['last_page'] > 1): ?>
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Showing page <?= $pagination['current_page'] ?> of <?= $pagination['last_page'] ?>
                        </div>
                        <ul class="enterprise-pagination">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="<?= url('/admin/projects?page=1' . ($search ? '&search=' . urlencode($search) : '')) ?>">
                                        <i class="bi bi-chevron-double-left"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="<?= url('/admin/projects?page=' . ($pagination['current_page'] - 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['last_page'], $pagination['current_page'] + 2);

                            for ($i = $start; $i <= $end; $i++):
                                ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="<?= url('/admin/projects?page=' . $i . ($search ? '&search=' . urlencode($search) : '')) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="<?= url('/admin/projects?page=' . ($pagination['current_page'] + 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="<?= url('/admin/projects?page=' . $pagination['last_page'] . ($search ? '&search=' . urlencode($search) : '')) ?>">
                                        <i class="bi bi-chevron-double-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-right">
            <!-- Summary Sidebar Card -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Management Overview</h3>
                <div class="sidebar-content">
                    <div class="sidebar-stat-item">
                        <span class="sidebar-stat-label">Total Projects</span>
                        <span class="sidebar-stat-value"><?= $pagination['total'] ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Tips Card -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Admin Quick Links</h3>
                <div class="sidebar-links">
                    <a href="<?= url('/admin/project-categories') ?>" class="sidebar-link-item">
                        <i class="bi bi-tags"></i>
                        <span>Project Categories</span>
                    </a>
                    <a href="<?= url('/admin/global-permissions') ?>" class="sidebar-link-item">
                        <i class="bi bi-lock"></i>
                        <span>Global Permissions</span>
                    </a>
                    <a href="<?= url('/admin/settings') ?>" class="sidebar-link-item">
                        <i class="bi bi-gear"></i>
                        <span>System Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
   ADMIN PROJECTS - ENTERPRISE DESIGN
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

    /* Toolbar */
    .page-toolbar {
        padding: 12px 32px;
        background: var(--white);
        border-bottom: 1px solid var(--jira-border);
    }

    .search-toolbar {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
        max-width: 400px;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--jira-gray);
    }

    .toolbar-input {
        width: 100%;
        padding: 8px 12px 8px 40px;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s;
    }

    .toolbar-input:focus {
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
    }

    .toolbar-actions {
        display: flex;
        gap: 8px;
    }

    .btn-toolbar-primary {
        padding: 8px 16px;
        background: var(--jira-blue);
        color: var(--white);
        border: none;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-toolbar-secondary {
        padding: 8px 16px;
        background: var(--white);
        color: var(--jira-dark);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
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
        font-size: 12px;
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

    .project-key-badge {
        background: #F4F5F7;
        color: var(--jira-gray);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .lead-info {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--jira-gray);
    }

    .lead-info i {
        font-size: 16px;
    }

    .text-unassigned {
        color: #A5ADBA;
        font-style: italic;
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
        width: 80px;
    }

    .status-pill.active {
        background: #E3FCEF;
        color: #006644;
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

    .sidebar-stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
    }

    .sidebar-stat-label {
        font-size: 14px;
        color: var(--jira-gray);
    }

    .sidebar-stat-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--jira-dark);
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

    /* Pagination */
    .pagination-container {
        padding: 20px 24px;
        border-top: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination-info {
        font-size: 13px;
        color: var(--jira-gray);
    }

    .enterprise-pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 4px;
    }

    .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        background: var(--white);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        color: var(--jira-dark);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .page-item.active .page-link {
        background: var(--jira-blue);
        color: var(--white);
        border-color: var(--jira-blue);
    }

    .page-item:not(.active) .page-link:hover {
        background: #F4F5F7;
        border-color: #B6C2CF;
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

        .search-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input-wrapper {
            max-width: none;
        }

        .content-right {
            grid-template-columns: 1fr;
        }

        .page-content {
            padding: 16px;
        }

        .page-header {
            padding: 24px 16px;
        }

        .page-toolbar {
            padding: 16px;
        }

        .breadcrumb {
            padding: 12px 16px;
        }
    }
</style>

<?php \App\Core\View::endsection(); ?>