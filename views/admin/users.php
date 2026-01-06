<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="au-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="au-breadcrumb">
        <a href="<?= url('/admin') ?>" class="au-breadcrumb-link">
            <i class="bi bi-gear"></i> Admin
        </a>
        <span class="au-breadcrumb-separator">/</span>
        <span class="au-breadcrumb-current">User Management</span>
    </div>

    <!-- Page Header -->
    <div class="au-header">
        <div class="au-header-left">
            <h1 class="au-title">User Management</h1>
            <p class="au-subtitle">Manage team members, assign roles, and control access permissions</p>
        </div>
        <div class="au-header-right">
            <a href="<?= url('/admin/users/create') ?>" class="au-btn au-btn-primary">
                <i class="bi bi-plus-lg"></i> Create User
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="au-filters">
        <form action="<?= url('/admin/users') ?>" method="GET" class="au-filters-form">
            <!-- Search Input -->
            <div class="au-filter-group">
                <input type="text" name="search" class="au-filter-input"
                    placeholder="Search by name, email, username..." value="<?= e($filters['search'] ?? '') ?>"
                    aria-label="Search users">
            </div>

            <!-- Status Filter -->
            <div class="au-filter-group">
                <select name="status" class="au-filter-select" aria-label="Filter by status">
                    <option value="">All Status</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive
                    </option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending
                    </option>
                </select>
            </div>

            <!-- Role Filter -->
            <div class="au-filter-group">
                <select name="role" class="au-filter-select" aria-label="Filter by role">
                    <option value="">All Roles</option>
                    <?php foreach ($roles ?? [] as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= ($filters['role'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                            <?= e($role['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="au-filter-actions">
                <button type="submit" class="au-btn au-btn-secondary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="<?= url('/admin/users') ?>" class="au-btn au-btn-outline">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>

            <!-- Export Dropdown -->
            <div class="au-filter-export">
                <div class="au-dropdown">
                    <button type="button" class="au-btn au-btn-outline au-dropdown-toggle" data-bs-toggle="dropdown"
                        aria-label="Export options">
                        <i class="bi bi-download"></i> Export
                    </button>
                    <ul class="au-dropdown-menu">
                        <li><a class="au-dropdown-item" href="<?= url('/admin/users/export?format=csv') ?>">
                                <i class="bi bi-file-text"></i> CSV
                            </a></li>
                        <li><a class="au-dropdown-item" href="<?= url('/admin/users/export?format=xlsx') ?>">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Excel
                            </a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>

    <!-- Content Area -->
    <div class="au-content">
        <!-- Users Table Card -->
        <div class="au-table-card">
            <!-- Card Header -->
            <div class="au-table-header">
                <span class="au-result-count"><?= $totalUsers ?? 0 ?> users found</span>
            </div>

            <!-- Table -->
            <div class="au-table-responsive">
                <table class="au-table">
                    <thead>
                        <tr>
                            <th class="au-col-checkbox">
                                <input type="checkbox" class="au-checkbox" id="selectAll" aria-label="Select all users">
                            </th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Created</th>
                            <th class="au-col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                                <tr class="au-table-row">
                                    <td class="au-col-checkbox">
                                        <input type="checkbox" class="au-checkbox au-user-checkbox" value="<?= $u['id'] ?>"
                                            aria-label="Select <?= e($u['display_name'] ?? $u['first_name']) ?>">
                                    </td>
                                    <td>
                                        <div class="au-user-cell">
                                            <?php if (($avatarUrl = avatar($u['avatar'] ?? null))): ?>
                                                <img src="<?= e($avatarUrl) ?>" class="au-avatar"
                                                    alt="<?= e($u['display_name'] ?? $u['first_name']) ?>">
                                            <?php else: ?>
                                                <div class="au-avatar au-avatar-initials" title="Avatar">
                                                    <?= strtoupper(substr($u['first_name'] ?? 'U', 0, 1)) . strtoupper(substr($u['last_name'] ?? '', 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="au-user-info">
                                                <div class="au-user-name">
                                                    <?= e($u['display_name'] ?? $u['first_name'] . ' ' . $u['last_name']) ?>
                                                    <?php if ($u['is_admin']): ?>
                                                        <span class="au-badge au-badge-admin" title="System Administrator">
                                                            <i class="bi bi-shield-check"></i> Admin
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="au-user-username">@<?= e($u['username'] ?? '') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="au-email"><?= e($u['email']) ?></span>
                                    </td>
                                    <td>
                                        <div class="au-role-badges">
                                            <?php
                                            $userRoles = $u['roles'] ?? '';
                                            if (is_string($userRoles) && !empty($userRoles)):
                                                foreach (explode(',', $userRoles) as $roleName): ?>
                                                    <span class="au-badge au-badge-role"><?= e(trim($roleName)) ?></span>
                                                <?php endforeach;
                                            elseif (is_array($userRoles)):
                                                foreach ($userRoles as $role): ?>
                                                    <span class="au-badge au-badge-role"><?= e($role['name'] ?? $role) ?></span>
                                                <?php endforeach;
                                            else: ?>
                                                <span class="au-badge au-badge-empty">No role</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="au-status">
                                            <?php if (!empty($u['is_active'])): ?>
                                                <span class="au-badge au-badge-active">
                                                    <span class="au-status-dot"></span> Active
                                                </span>
                                            <?php elseif (empty($u['email_verified_at'])): ?>
                                                <span class="au-badge au-badge-pending">
                                                    <span class="au-status-dot"></span> Pending
                                                </span>
                                            <?php else: ?>
                                                <span class="au-badge au-badge-inactive">
                                                    <span class="au-status-dot"></span> Inactive
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="au-last-login">
                                            <?php if ($u['last_login_at']): ?>
                                                <span title="<?= format_datetime($u['last_login_at']) ?>">
                                                    <?= time_ago($u['last_login_at']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="au-text-muted">Never</span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="au-created-date"><?= format_date($u['created_at']) ?></span>
                                    </td>
                                    <td class="au-col-actions">
                                        <?php if ($u['is_admin']): ?>
                                            <span class="au-badge au-badge-protected"
                                                title="Administrator users cannot be modified">
                                                <i class="bi bi-shield-lock"></i> Protected
                                            </span>
                                        <?php else: ?>
                                            <div class="au-actions-menu au-dropdown">
                                                <button class="au-btn-menu" data-bs-toggle="dropdown"
                                                    aria-label="Actions for <?= e($u['display_name'] ?? $u['first_name']) ?>">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="au-dropdown-menu au-dropdown-menu-end" role="menu">
                                                    <li>
                                                        <a class="au-dropdown-item"
                                                            href="<?= url('/admin/users/' . $u['id'] . '/edit') ?>">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="au-dropdown-item" href="<?= url('/admin/users/' . $u['id']) ?>">
                                                            <i class="bi bi-eye"></i> View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="au-dropdown-divider">
                                                    </li>
                                                    <?php if (!empty($u['is_active'])): ?>
                                                        <li>
                                                            <form action="<?= url('/admin/users/' . $u['id'] . '/deactivate') ?>"
                                                                method="POST" class="au-form-inline">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="au-dropdown-item au-dropdown-item-warning">
                                                                    <i class="bi bi-pause-circle"></i> Deactivate
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php else: ?>
                                                        <li>
                                                            <form action="<?= url('/admin/users/' . $u['id'] . '/activate') ?>"
                                                                method="POST" class="au-form-inline">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="au-dropdown-item au-dropdown-item-success">
                                                                    <i class="bi bi-play-circle"></i> Activate
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <form action="<?= url('/admin/users/' . $u['id']) ?>" method="POST"
                                                            onsubmit="return confirm('Delete this user? This action cannot be undone.');"
                                                            class="au-form-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <button type="submit" class="au-dropdown-item au-dropdown-item-danger">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="au-empty-state-cell">
                                    <div class="au-empty-state">
                                        <div class="au-empty-icon">👥</div>
                                        <p class="au-empty-title">No users found</p>
                                        <p class="au-empty-hint">Create your first user to get started</p>
                                        <a href="<?= url('/admin/users/create') ?>" class="au-btn au-btn-primary au-mt-3">
                                            <i class="bi bi-plus-lg"></i> Create User
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (($totalPages ?? 1) > 1): ?>
                <div class="au-table-footer">
                    <nav aria-label="User pagination">
                        <ul class="au-pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="au-page-item <?= $i === ($currentPage ?? 1) ? 'active' : '' ?>">
                                    <a class="au-page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bulk Actions Panel -->
    <div class="au-bulk-actions d-none" id="bulkActions" role="region" aria-live="polite">
        <div class="au-bulk-actions-content">
            <span class="au-bulk-actions-text">
                <strong id="selectedCount">0</strong> users selected
            </span>
            <div class="au-bulk-actions-buttons">
                <button class="au-btn au-btn-sm au-btn-success" onclick="bulkActivate()"
                    aria-label="Bulk activate selected users">
                    <i class="bi bi-play-circle"></i> Activate
                </button>
                <button class="au-btn au-btn-sm au-btn-warning" onclick="bulkDeactivate()"
                    aria-label="Bulk deactivate selected users">
                    <i class="bi bi-pause-circle"></i> Deactivate
                </button>
                <button class="au-btn au-btn-sm au-btn-danger" onclick="bulkDelete()"
                    aria-label="Bulk delete selected users">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
   ADMIN USERS - ENTERPRISE JIRA-LIKE DESIGN
   ============================================ */

    :root {
        --au-primary: #8B1956;
        --au-primary-dark: #6F123F;
        --au-primary-light: rgba(139, 25, 86, 0.1);
        --au-text-primary: #161B22;
        --au-text-secondary: #626F86;
        --au-bg-primary: #FFFFFF;
        --au-bg-secondary: #F7F8FA;
        --au-border: #DFE1E6;
        --au-success: #216E4E;
        --au-warning: #974F0C;
        --au-danger: #ED3C32;
        --au-shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
        --au-shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
        --au-shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
        --au-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .au-wrapper {
        background: var(--au-bg-secondary);
        min-height: 100vh;
    }

    /* ===== BREADCRUMB ===== */
    .au-breadcrumb {
        padding: 8px 20px;
        background: var(--au-bg-primary);
        border-bottom: 1px solid var(--au-border);
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: var(--au-text-secondary);
    }

    .au-breadcrumb-link {
        color: var(--au-primary);
        text-decoration: none;
        transition: color var(--au-transition);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .au-breadcrumb-link:hover {
        color: var(--au-primary-dark);
        text-decoration: underline;
    }

    .au-breadcrumb-separator {
        color: var(--au-border);
    }

    .au-breadcrumb-current {
        color: var(--au-text-primary);
        font-weight: 500;
    }

    /* ===== PAGE HEADER ===== */
    .au-header {
        background: var(--au-bg-primary);
        border-bottom: 1px solid var(--au-border);
        box-shadow: var(--au-shadow-sm);
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .au-header-left {
        flex: 1;
    }

    .au-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--au-text-primary);
        margin: 0 0 4px 0;
        letter-spacing: -0.2px;
    }

    .au-subtitle {
        font-size: 13px;
        color: var(--au-text-secondary);
        margin: 0;
        font-weight: 400;
    }

    .au-header-right {
        flex-shrink: 0;
        min-height: 36px;
        display: flex;
        align-items: center;
    }

    /* ===== FILTERS ===== */
    .au-filters {
        background: var(--au-bg-primary);
        border-bottom: 1px solid var(--au-border);
        padding: 12px 20px;
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .au-filters-form {
        flex: 1;
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .au-filter-group {
        flex: 1;
        min-width: 200px;
    }

    .au-filter-input,
    .au-filter-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--au-border);
        border-radius: 6px;
        font-size: 14px;
        color: var(--au-text-primary);
        background: var(--au-bg-primary);
        transition: all var(--au-transition);
        font-family: inherit;
    }

    .au-filter-input::placeholder {
        color: var(--au-text-secondary);
    }

    .au-filter-input:focus,
    .au-filter-select:focus {
        outline: none;
        border-color: var(--au-primary);
        box-shadow: 0 0 0 2px var(--au-primary-light);
    }

    .au-filter-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .au-filter-export {
        flex-shrink: 0;
        margin-left: auto;
    }

    /* ===== BUTTONS ===== */
    .au-btn {
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all var(--au-transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
        white-space: nowrap;
        color: white !important;
        text-shadow: none;
    }

    .au-btn-primary {
        background: var(--au-primary);
        color: white !important;
        border-color: var(--au-primary);
    }

    .au-btn-primary i {
        color: white !important;
        font-weight: 600;
    }

    .au-btn-primary:hover {
        background: var(--au-primary-dark);
        border-color: var(--au-primary-dark);
        box-shadow: 0 2px 8px rgba(139, 25, 86, 0.15);
        transform: translateY(-2px);
    }

    .au-btn-secondary {
        background: var(--au-bg-secondary);
        color: var(--au-text-primary);
        border: 1px solid var(--au-border);
    }

    .au-btn-secondary:hover {
        background: var(--au-border);
        border-color: var(--au-border);
    }

    .au-btn-outline {
        background: transparent;
        color: var(--au-text-primary);
        border: 1px solid var(--au-border);
    }

    .au-btn-outline:hover {
        background: var(--au-bg-secondary);
        border-color: var(--au-border);
    }

    .au-btn-success {
        background: var(--au-success);
        color: white;
        border-color: var(--au-success);
    }

    .au-btn-success:hover {
        background: #1a5539;
        border-color: #1a5539;
    }

    .au-btn-warning {
        background: var(--au-warning);
        color: white;
        border-color: var(--au-warning);
    }

    .au-btn-warning:hover {
        background: #7a3d09;
        border-color: #7a3d09;
    }

    .au-btn-danger {
        background: var(--au-danger);
        color: white;
        border-color: var(--au-danger);
    }

    .au-btn-danger:hover {
        background: #c92a1f;
        border-color: #c92a1f;
    }

    .au-btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .au-mt-3 {
        margin-top: 16px;
    }

    /* ===== DROPDOWN ===== */
    .au-dropdown {
        position: relative;
        display: inline-block;
    }

    .au-dropdown-toggle::after {
        display: none;
    }

    .au-dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        right: 0;
        background: var(--au-bg-primary);
        border: 1px solid var(--au-border);
        border-radius: 6px;
        box-shadow: var(--au-shadow-lg);
        min-width: 180px;
        list-style: none;
        padding: 8px 0;
        margin: 0;
        z-index: 1050;
        animation: au-dropdown-show 0.15s ease;
    }

    @keyframes au-dropdown-show {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .au-dropdown-menu-end {
        right: 0;
        left: auto;
    }

    .au-dropdown-menu.show {
        display: block;
    }

    .au-dropdown:has([data-bs-toggle="dropdown"]:focus) .au-dropdown-menu,
    .au-dropdown:hover .au-dropdown-menu {
        display: block;
    }

    .au-dropdown-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        color: var(--au-text-primary);
        text-decoration: none;
        cursor: pointer;
        transition: background var(--au-transition);
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-size: 14px;
    }

    .au-dropdown-item:hover {
        background: var(--au-bg-secondary);
    }

    .au-dropdown-item-warning {
        color: var(--au-warning);
    }

    .au-dropdown-item-success {
        color: var(--au-success);
    }

    .au-dropdown-item-danger {
        color: var(--au-danger);
    }

    .au-dropdown-divider {
        margin: 4px 0;
        border: none;
        border-top: 1px solid var(--au-border);
    }

    /* ===== CONTENT ===== */
    .au-content {
        padding: 16px 20px;
    }

    /* ===== TABLE CARD ===== */
    .au-table-card {
        background: var(--au-bg-primary);
        border: 1px solid var(--au-border);
        border-radius: 8px;
        box-shadow: var(--au-shadow-md);
        overflow: hidden;
    }

    .au-table-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--au-border);
        background: var(--au-bg-secondary);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .au-result-count {
        font-size: 14px;
        color: var(--au-text-secondary);
        font-weight: 500;
    }

    /* ===== TABLE ===== */
    .au-table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .au-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .au-table thead {
        background: var(--au-bg-secondary);
        border-bottom: 1px solid var(--au-border);
    }

    .au-table th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: var(--au-text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.2px;
        user-select: none;
    }

    .au-table tbody tr {
        border-bottom: 1px solid var(--au-border);
        transition: background var(--au-transition);
    }

    .au-table tbody tr:hover {
        background: var(--au-bg-secondary);
    }

    .au-table td {
        padding: 12px;
        vertical-align: middle;
    }

    .au-col-checkbox {
        width: 44px;
        padding: 12px 16px;
        text-align: center;
    }

    .au-col-actions {
        width: 160px;
        text-align: center;
    }

    /* ===== CHECKBOXES ===== */
    .au-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--au-primary);
    }

    /* ===== USER CELL ===== */
    .au-user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .au-avatar {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .au-avatar-initials {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--au-primary), var(--au-primary-dark));
        color: white;
        font-weight: 600;
        font-size: 12px;
    }

    .au-user-info {
        flex: 1;
        min-width: 0;
    }

    .au-user-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--au-text-primary);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
    }

    .au-user-username {
        font-size: 12px;
        color: var(--au-text-secondary);
    }

    /* ===== BADGES ===== */
    .au-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.2px;
    }

    .au-badge-admin {
        background: rgba(139, 25, 86, 0.15);
        color: var(--au-primary);
    }

    .au-badge-role {
        background: rgba(139, 25, 86, 0.12);
        color: var(--au-primary);
        text-transform: none;
        font-weight: 500;
        font-size: 12px;
    }

    .au-badge-empty {
        background: var(--au-bg-secondary);
        color: var(--au-text-secondary);
    }

    .au-badge-active {
        background: rgba(33, 110, 78, 0.15);
        color: var(--au-success);
    }

    .au-badge-pending {
        background: rgba(151, 79, 12, 0.15);
        color: var(--au-warning);
    }

    .au-badge-inactive {
        background: rgba(237, 60, 50, 0.15);
        color: var(--au-danger);
    }

    .au-badge-protected {
        background: rgba(120, 120, 130, 0.15);
        color: #505060;
    }

    .au-status-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        margin-right: 2px;
    }

    /* ===== ROLE BADGES ===== */
    .au-role-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    /* ===== EMAIL & META ===== */
    .au-email,
    .au-last-login,
    .au-created-date {
        font-size: 14px;
        color: var(--au-text-primary);
    }

    .au-text-muted {
        color: var(--au-text-secondary);
    }

    /* ===== ACTIONS MENU ===== */
    .au-actions-menu {
        display: inline-block;
        position: relative;
    }

    .au-btn-menu {
        padding: 8px 10px;
        background: transparent;
        border: 1px solid var(--au-border);
        border-radius: 4px;
        cursor: pointer;
        color: var(--au-text-secondary);
        transition: all var(--au-transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        min-height: 36px;
        font-size: 16px;
        position: relative;
    }

    .au-btn-menu:hover {
        background: var(--au-bg-secondary);
        border-color: var(--au-primary);
        color: var(--au-primary);
    }

    .au-btn-menu:focus,
    .au-btn-menu[aria-expanded="true"] {
        background: var(--au-bg-secondary);
        border-color: var(--au-primary);
        color: var(--au-primary);
        outline: none;
    }

    /* ===== FORM INLINE ===== */
    .au-form-inline {
        display: inline;
        margin: 0;
        padding: 0;
        border: none;
        background: none;
    }

    /* ===== EMPTY STATE ===== */
    .au-empty-state-cell {
        padding: 60px 20px !important;
    }

    .au-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        text-align: center;
    }

    .au-empty-icon {
        font-size: 64px;
        opacity: 0.4;
    }

    .au-empty-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--au-text-primary);
        margin: 0;
    }

    .au-empty-hint {
        font-size: 13px;
        color: var(--au-text-secondary);
        margin: 0;
    }

    /* ===== PAGINATION ===== */
    .au-table-footer {
        padding: 16px;
        border-top: 1px solid var(--au-border);
        background: var(--au-bg-secondary);
        text-align: center;
    }

    .au-pagination {
        display: flex;
        gap: 4px;
        justify-content: center;
        margin: 0;
        list-style: none;
        padding: 0;
    }

    .au-page-item {
        display: inline-block;
    }

    .au-page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border: 1px solid var(--au-border);
        border-radius: 4px;
        color: var(--au-primary);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all var(--au-transition);
    }

    .au-page-link:hover {
        background: var(--au-primary-light);
        border-color: var(--au-primary);
    }

    .au-page-item.active .au-page-link {
        background: var(--au-primary);
        border-color: var(--au-primary);
        color: white;
    }

    /* ===== BULK ACTIONS ===== */
    .au-bulk-actions {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--au-bg-primary);
        border-top: 1px solid var(--au-border);
        box-shadow: 0 -2px 8px rgba(9, 30, 66, 0.1);
        z-index: 100;
        animation: au-slide-up 0.2s ease;
    }

    @keyframes au-slide-up {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .au-bulk-actions-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 32px;
        gap: 16px;
    }

    .au-bulk-actions-text {
        font-size: 14px;
        color: var(--au-text-primary);
        flex: 1;
    }

    .au-bulk-actions-buttons {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .d-none {
        display: none !important;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        .au-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 24px;
            gap: 16px;
        }

        .au-header-right {
            width: 100%;
        }

        .au-filters {
            padding: 16px 24px;
            gap: 12px;
        }

        .au-filters-form {
            flex-direction: column;
            width: 100%;
        }

        .au-filter-group {
            width: 100%;
            min-width: unset;
        }

        .au-filter-actions {
            width: 100%;
        }

        .au-filter-export {
            width: 100%;
            margin-left: 0;
        }

        .au-content {
            padding: 20px 24px;
        }

        .au-breadcrumb {
            padding: 12px 24px;
        }
    }

    @media (max-width: 768px) {
        .au-header {
            padding: 20px 16px;
            gap: 12px;
        }

        .au-title {
            font-size: 24px;
        }

        .au-filters {
            flex-direction: column;
            padding: 12px 16px;
            gap: 8px;
        }

        .au-content {
            padding: 16px;
        }

        .au-table th,
        .au-table td {
            padding: 12px;
            font-size: 13px;
        }

        .au-table th {
            font-size: 11px;
        }

        .au-avatar {
            width: 36px;
            height: 36px;
        }

        .au-col-checkbox {
            width: 40px;
            padding: 12px 8px;
        }

        .au-col-actions {
            width: 50px;
        }

        .au-bulk-actions-content {
            flex-direction: column;
            align-items: stretch;
            padding: 12px 16px;
        }

        .au-bulk-actions-text {
            text-align: center;
        }

        .au-bulk-actions-buttons {
            width: 100%;
            justify-content: space-around;
        }

        .au-breadcrumb {
            padding: 8px 16px;
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .au-title {
            font-size: 18px;
        }

        .au-subtitle {
            font-size: 12px;
        }

        .au-table th,
        .au-table td {
            padding: 8px 10px;
            font-size: 12px;
        }

        .au-table th {
            font-size: 10px;
        }

        .au-btn {
            padding: 8px 12px;
            font-size: 12px;
        }

        .au-btn-sm {
            padding: 4px 8px;
            font-size: 11px;
        }

        /* Hide less important columns on mobile */
        .au-table th:nth-child(6),
        .au-table th:nth-child(7),
        .au-table td:nth-child(6),
        .au-table td:nth-child(7) {
            display: none;
        }

        .au-badge {
            font-size: 9px;
            padding: 3px 6px;
        }

        .au-user-name {
            font-size: 13px;
        }

        .au-user-username {
            font-size: 10px;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
    // ===== DROPDOWN MENU INITIALIZATION (Pure Vanilla JS) =====
    console.log('[ADMIN-USERS] Initializing dropdown menus...');

    document.addEventListener('DOMContentLoaded', function () {
        // Find all dropdown buttons
        const dropdownButtons = document.querySelectorAll('.au-dropdown [data-bs-toggle="dropdown"]');
        console.log('[ADMIN-USERS] Found ' + dropdownButtons.length + ' dropdown buttons');

        dropdownButtons.forEach((button) => {
            // Find the menu - it's the next sibling ul
            const menu = button.nextElementSibling;

            if (!menu || !menu.classList.contains('au-dropdown-menu')) {
                console.warn('[ADMIN-USERS] Menu not found for button:', button);
                return;
            }

            // Remove Bootstrap's data attribute to prevent conflicts
            button.removeAttribute('data-bs-toggle');

            button.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('[ADMIN-USERS] Button clicked, toggling menu');

                // Close all other menus
                document.querySelectorAll('.au-dropdown-menu.show').forEach((otherMenu) => {
                    if (otherMenu !== menu) {
                        otherMenu.classList.remove('show');
                        // Find and update the button for this menu
                        const otherBtn = otherMenu.previousElementSibling;
                        if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                // Toggle this menu
                const isOpen = menu.classList.toggle('show');
                button.setAttribute('aria-expanded', isOpen);
                console.log('[ADMIN-USERS] Menu is now ' + (isOpen ? 'open' : 'closed'));
            });

            // Close when clicking menu items
            menu.querySelectorAll('a, button').forEach((item) => {
                item.addEventListener('click', function () {
                    setTimeout(() => {
                        menu.classList.remove('show');
                        button.setAttribute('aria-expanded', 'false');
                    }, 50);
                });
            });
        });

        // Close all menus when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.au-dropdown')) {
                document.querySelectorAll('.au-dropdown-menu.show').forEach((menu) => {
                    menu.classList.remove('show');
                    const btn = menu.previousElementSibling;
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                });
            }
        });

        console.log('[ADMIN-USERS] Dropdown initialization complete');
    });

    // ===== BULK ACTIONS =====
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.au-user-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });

    document.querySelectorAll('.au-user-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checked = document.querySelectorAll('.au-user-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');

        if (checked.length > 0) {
            bulkActions.classList.remove('d-none');
            selectedCount.textContent = checked.length;
        } else {
            bulkActions.classList.add('d-none');
        }
    }

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.au-user-checkbox:checked')).map(cb => cb.value);
    }

    async function bulkActivate() {
        const ids = getSelectedIds();
        if (confirm(`Activate ${ids.length} users?`)) {
            try {
                const response = await fetch('<?= url("/admin/users/bulk-activate") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('[name="_token"]')?.value || ''
                    },
                    body: JSON.stringify({ user_ids: ids })
                });
                if (response.ok) location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to activate users');
            }
        }
    }

    async function bulkDeactivate() {
        const ids = getSelectedIds();
        if (confirm(`Deactivate ${ids.length} users?`)) {
            try {
                const response = await fetch('<?= url("/admin/users/bulk-deactivate") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('[name="_token"]')?.value || ''
                    },
                    body: JSON.stringify({ user_ids: ids })
                });
                if (response.ok) location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to deactivate users');
            }
        }
    }

    async function bulkDelete() {
        const ids = getSelectedIds();
        if (confirm(`Delete ${ids.length} users? This cannot be undone.`)) {
            try {
                const response = await fetch('<?= url("/admin/users/bulk-delete") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('[name="_token"]')?.value || ''
                    },
                    body: JSON.stringify({ user_ids: ids })
                });
                if (response.ok) location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to delete users');
            }
        }
    }
</script>
<?php \App\Core\View::endSection(); ?>