<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="admin-users-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">User Management</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">User Management</h1>
            <p class="page-subtitle">Manage team members and access permissions</p>
        </div>
        <div class="header-right">
            <a href="<?= url('/admin/users/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i> Create User
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <form action="<?= url('/admin/users') ?>" method="GET" class="filters-form">
            <div class="filter-group">
                <input type="text" name="search" class="filter-input" placeholder="Search by name, email, or username..." 
                       value="<?= e($filters['search'] ?? '') ?>" aria-label="Search users">
            </div>
            
            <div class="filter-group">
                <select name="status" class="filter-select" aria-label="Filter by status">
                    <option value="">All Status</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
            </div>

            <div class="filter-group">
                <select name="role" class="filter-select" aria-label="Filter by role">
                    <option value="">All Roles</option>
                    <?php foreach ($roles ?? [] as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= ($filters['role'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                        <?= e($role['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="<?= url('/admin/users') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </a>
            </div>
        </form>

        <div class="filter-export">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-label="Export options">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= url('/admin/users/export?format=csv') ?>">
                        <i class="bi bi-file-text me-2"></i> Export as CSV
                    </a></li>
                    <li><a class="dropdown-item" href="<?= url('/admin/users/export?format=xlsx') ?>">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export as Excel
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">
        <!-- Users Table Card -->
        <div class="table-card">
            <!-- Table Header -->
            <div class="table-card-header">
                <span class="result-count"><?= $totalUsers ?? 0 ?> users found</span>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th class="checkbox-col">
                                <input type="checkbox" class="form-check-input" id="selectAll" aria-label="Select all users">
                            </th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Created</th>
                            <th class="actions-col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                            <tr class="user-row">
                                <td class="checkbox-col">
                                    <input type="checkbox" class="form-check-input user-checkbox" value="<?= $u['id'] ?>" 
                                           aria-label="Select <?= e($u['display_name'] ?? $u['first_name']) ?>">
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <?php if ($u['avatar'] ?? null): ?>
                                        <img src="<?= e($u['avatar']) ?>" class="user-avatar" alt="<?= e($u['display_name'] ?? $u['first_name']) ?>">
                                        <?php else: ?>
                                        <div class="user-avatar-initials" title="Avatar">
                                            <?= strtoupper(substr($u['first_name'] ?? 'U', 0, 1)) . strtoupper(substr($u['last_name'] ?? '', 0, 1)) ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="user-info">
                                            <div class="user-name">
                                                <?= e($u['display_name'] ?? $u['first_name'] . ' ' . $u['last_name']) ?>
                                                <?php if ($u['is_admin']): ?>
                                                <span class="badge badge-admin" title="System Administrator">
                                                    <i class="bi bi-shield-check me-1"></i> Admin
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="user-username">@<?= e($u['username'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="email-cell"><?= e($u['email']) ?></span>
                                </td>
                                <td>
                                    <div class="role-badges">
                                        <?php 
                                        $userRoles = $u['roles'] ?? '';
                                        if (is_string($userRoles) && !empty($userRoles)):
                                            foreach (explode(',', $userRoles) as $roleName): ?>
                                        <span class="badge badge-role"><?= e(trim($roleName)) ?></span>
                                            <?php endforeach;
                                        elseif (is_array($userRoles)):
                                            foreach ($userRoles as $role): ?>
                                        <span class="badge badge-role"><?= e($role['name'] ?? $role) ?></span>
                                            <?php endforeach;
                                        else: ?>
                                        <span class="badge badge-empty">No role</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="status-cell">
                                        <?php if (!empty($u['is_active'])): ?>
                                        <span class="badge badge-active"><i class="bi bi-check-circle-fill me-1"></i> Active</span>
                                        <?php elseif (empty($u['email_verified_at'])): ?>
                                        <span class="badge badge-pending"><i class="bi bi-clock-fill me-1"></i> Pending</span>
                                        <?php else: ?>
                                        <span class="badge badge-inactive"><i class="bi bi-x-circle-fill me-1"></i> Inactive</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="last-login-cell">
                                        <?php if ($u['last_login_at']): ?>
                                        <span title="<?= format_datetime($u['last_login_at']) ?>"><?= time_ago($u['last_login_at']) ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">Never</span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="created-date"><?= format_date($u['created_at']) ?></span>
                                </td>
                                <td class="actions-col">
                                    <?php if ($u['is_admin']): ?>
                                    <span class="badge badge-protected" title="Administrator users cannot be modified">
                                        <i class="bi bi-shield-lock me-1"></i> Protected
                                    </span>
                                    <?php else: ?>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-actions" data-bs-toggle="dropdown" 
                                                aria-label="Actions for <?= e($u['display_name'] ?? $u['first_name']) ?>">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="<?= url('/admin/users/' . $u['id'] . '/edit') ?>">
                                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="<?= url('/admin/users/' . $u['id']) ?>">
                                                    <i class="bi bi-eye me-2"></i> View Details
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <?php if (!empty($u['is_active'])): ?>
                                            <li>
                                                <form action="<?= url('/admin/users/' . $u['id'] . '/deactivate') ?>" method="POST" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="dropdown-item text-warning">
                                                        <i class="bi bi-pause-circle me-2"></i> Deactivate
                                                    </button>
                                                </form>
                                            </li>
                                            <?php else: ?>
                                            <li>
                                                <form action="<?= url('/admin/users/' . $u['id'] . '/activate') ?>" method="POST" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="dropdown-item text-success">
                                                        <i class="bi bi-play-circle me-2"></i> Activate
                                                    </button>
                                                </form>
                                            </li>
                                            <?php endif; ?>
                                            <li>
                                                <form action="<?= url('/admin/users/' . $u['id']) ?>" method="POST"
                                                      onsubmit="return confirm('Delete this user? This action cannot be undone.');"
                                                      style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i> Delete
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
                            <td colspan="8" class="empty-state">
                                <div class="empty-state-content">
                                    <div class="empty-state-icon">👥</div>
                                    <p class="empty-state-text">No users found</p>
                                    <p class="empty-state-hint">Create your first user to get started</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (($totalPages ?? 1) > 1): ?>
            <div class="table-card-footer">
                <nav aria-label="User pagination">
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === ($currentPage ?? 1) ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
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
    <div class="bulk-actions-panel d-none" id="bulkActions" role="region" aria-live="polite">
        <div class="bulk-actions-content">
            <span class="bulk-actions-text">
                <strong id="selectedCount">0</strong> users selected
            </span>
            <div class="bulk-actions-buttons">
                <button class="btn btn-sm btn-outline-success" onclick="bulkActivate()" aria-label="Bulk activate selected users">
                    <i class="bi bi-play-circle me-1"></i> Activate
                </button>
                <button class="btn btn-sm btn-outline-warning" onclick="bulkDeactivate()" aria-label="Bulk deactivate selected users">
                    <i class="bi bi-pause-circle me-1"></i> Deactivate
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()" aria-label="Bulk delete selected users">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --jira-blue: #8B1956;
    --jira-dark: #6F123F;
    --jira-light-blue: rgba(139,25,86,0.1);
    --text-primary: #161B22;
    --text-secondary: #626F86;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F8FA;
    --border-color: #DFE1E6;
    --danger-color: #ED3C32;
    --success-color: #216E4E;
    --warning-color: #974F0C;
    --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
    --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.admin-users-wrapper {
    background: var(--bg-secondary);
    min-height: 100vh;
}

/* Breadcrumb Navigation */
.breadcrumb-nav {
    padding: 12px 32px;
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--text-secondary);
}

.breadcrumb-link {
    color: var(--jira-blue);
    text-decoration: none;
    transition: color var(--transition);
    display: flex;
    align-items: center;
    gap: 4px;
}

.breadcrumb-link:hover {
    color: var(--jira-dark);
    text-decoration: underline;
}

.breadcrumb-separator {
    color: var(--border-color);
}

.breadcrumb-current {
    color: var(--text-primary);
    font-weight: 500;
}

/* Page Header */
.page-header {
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    padding: 24px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px 0;
    letter-spacing: -0.3px;
}

.page-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
}

.header-right {
    flex-shrink: 0;
}

/* Filters Section */
.filters-section {
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    padding: 20px 32px;
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
}

.filters-form {
    flex: 1;
    display: flex;
    gap: 12px;
    align-items: center;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-input,
.filter-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
    color: var(--text-primary);
    background: var(--bg-primary);
    transition: all var(--transition);
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

.filter-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

.filter-export {
    flex-shrink: 0;
    margin-left: auto;
}

/* Content Area */
.content-area {
    padding: 24px 32px;
}

/* Table Card */
.table-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.table-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-secondary);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.result-count {
    font-size: 14px;
    color: var(--text-secondary);
    font-weight: 500;
}

/* Table Styles */
.table-responsive {
    overflow-x: auto;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.users-table thead {
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
}

.users-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.users-table tbody tr {
    border-bottom: 1px solid var(--border-color);
    transition: background var(--transition);
}

.users-table tbody tr:hover {
    background: var(--bg-secondary);
}

.users-table td {
    padding: 12px 16px;
    color: var(--text-primary);
    vertical-align: middle;
}

.checkbox-col {
    width: 44px;
    padding: 12px 8px;
    text-align: center;
}

.actions-col {
    width: 100px;
    text-align: right;
}

/* User Cell */
.user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
}

.user-avatar-initials {
    width: 36px;
    height: 36px;
    border-radius: 6px;
    background: var(--jira-blue);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-weight: 500;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.user-username {
    font-size: 12px;
    color: var(--text-secondary);
    margin-top: 2px;
}

/* Email Cell */
.email-cell {
    color: var(--text-primary);
    word-break: break-word;
}

/* Status Badges */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.badge-active {
    background: rgba(33, 110, 78, 0.1);
    color: var(--success-color);
}

.badge-inactive {
    background: rgba(237, 60, 50, 0.1);
    color: var(--danger-color);
}

.badge-pending {
    background: rgba(151, 79, 12, 0.1);
    color: var(--warning-color);
}

.badge-role {
    background: rgba(139, 25, 86, 0.1);
    color: var(--jira-blue);
}

.badge-empty {
    background: var(--bg-secondary);
    color: var(--text-secondary);
}

.badge-admin {
    background: rgba(237, 60, 50, 0.1);
    color: var(--danger-color);
}

.badge-protected {
    background: rgba(151, 79, 12, 0.1);
    color: var(--warning-color);
}

/* Role Badges */
.role-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

/* Status Cell */
.status-cell {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Last Login Cell */
.last-login-cell {
    color: var(--text-secondary);
    font-size: 13px;
}

/* Created Date */
.created-date {
    color: var(--text-secondary);
    font-size: 13px;
}

/* Actions Button */
.btn-actions {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    padding: 6px 8px;
    border-radius: 4px;
    transition: all var(--transition);
    cursor: pointer;
}

.btn-actions:hover {
    background: var(--border-color);
    border-color: var(--text-secondary);
}

/* Dropdown Menu */
.dropdown-menu {
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-lg);
    border-radius: 6px;
}

.dropdown-item {
    padding: 8px 12px;
    font-size: 13px;
    color: var(--text-primary);
    transition: background var(--transition);
}

.dropdown-item:hover {
    background: var(--bg-secondary);
    color: var(--text-primary);
}

.dropdown-item.text-warning {
    color: var(--warning-color) !important;
}

.dropdown-item.text-success {
    color: var(--success-color) !important;
}

.dropdown-item.text-danger {
    color: var(--danger-color) !important;
}

/* Empty State */
.empty-state {
    padding: 60px 20px !important;
    text-align: center;
}

.empty-state-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.empty-state-icon {
    font-size: 56px;
    opacity: 0.5;
    line-height: 1;
}

.empty-state-text {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-secondary);
    margin: 0;
}

.empty-state-hint {
    font-size: 12px;
    color: var(--text-secondary);
    margin: 0;
}

/* Pagination */
.table-card-footer {
    padding: 16px;
    border-top: 1px solid var(--border-color);
    background: var(--bg-secondary);
    text-align: center;
}

.pagination {
    display: flex;
    gap: 4px;
    justify-content: center;
    margin: 0;
    list-style: none;
    padding: 0;
}

.page-item {
    display: inline-block;
}

.page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    color: var(--jira-blue);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all var(--transition);
}

.page-link:hover {
    background: var(--jira-light-blue);
    border-color: var(--jira-blue);
}

.page-item.active .page-link {
    background: var(--jira-blue);
    border-color: var(--jira-blue);
    color: white;
}

/* Bulk Actions Panel */
.bulk-actions-panel {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--bg-primary);
    border-top: 1px solid var(--border-color);
    box-shadow: 0 -2px 8px rgba(9, 30, 66, 0.1);
    z-index: 100;
    animation: slideUp 0.2s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.bulk-actions-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 32px;
    gap: 16px;
}

.bulk-actions-text {
    font-size: 14px;
    color: var(--text-primary);
    flex: 1;
}

.bulk-actions-buttons {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

/* Buttons */
.btn {
    padding: 10px 20px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    white-space: nowrap;
}

.btn-primary {
    background: var(--jira-blue);
    color: white;
}

.btn-primary:hover {
    background: var(--jira-dark);
}

.btn-secondary {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--border-color);
}

.btn-outline-secondary {
    background: var(--bg-primary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-outline-secondary:hover {
    background: var(--bg-secondary);
}

.btn-outline-success {
    color: var(--success-color);
    border: 1px solid var(--success-color);
    background: transparent;
}

.btn-outline-success:hover {
    background: rgba(33, 110, 78, 0.1);
}

.btn-outline-warning {
    color: var(--warning-color);
    border: 1px solid var(--warning-color);
    background: transparent;
}

.btn-outline-warning:hover {
    background: rgba(151, 79, 12, 0.1);
}

.btn-outline-danger {
    color: var(--danger-color);
    border: 1px solid var(--danger-color);
    background: transparent;
}

.btn-outline-danger:hover {
    background: rgba(237, 60, 50, 0.1);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 20px 24px;
    }

    .header-right {
        width: 100%;
    }

    .filters-section {
        padding: 16px 24px;
        gap: 12px;
    }

    .filter-actions {
        margin-left: auto;
    }

    .content-area {
        padding: 20px 24px;
    }

    .breadcrumb-nav {
        padding: 12px 24px;
    }
}

@media (max-width: 768px) {
    .page-header {
        padding: 16px 16px;
        gap: 12px;
    }

    .page-title {
        font-size: 22px;
    }

    .filters-section {
        flex-direction: column;
        padding: 12px 16px;
        gap: 8px;
    }

    .filters-form {
        flex-direction: column;
        width: 100%;
    }

    .filter-group {
        width: 100%;
        min-width: unset;
    }

    .filter-actions {
        width: 100%;
        margin-left: 0;
    }

    .filter-actions .btn {
        flex: 1;
    }

    .filter-export {
        width: 100%;
        margin-left: 0;
    }

    .content-area {
        padding: 16px;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .users-table {
        font-size: 13px;
    }

    .users-table th,
    .users-table td {
        padding: 10px 12px;
    }

    .user-cell {
        gap: 8px;
    }

    .user-avatar,
    .user-avatar-initials {
        width: 32px;
        height: 32px;
        font-size: 11px;
    }

    .bulk-actions-content {
        flex-direction: column;
        align-items: stretch;
        padding: 12px 16px;
    }

    .bulk-actions-text {
        text-align: center;
    }

    .bulk-actions-buttons {
        width: 100%;
        justify-content: space-around;
    }

    .bulk-actions-panel:not(.d-none) {
        bottom: 0;
    }

    .breadcrumb-nav {
        padding: 8px 16px;
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .page-title {
        font-size: 18px;
    }

    .page-subtitle {
        font-size: 12px;
    }

    .filter-input,
    .filter-select {
        font-size: 13px;
        padding: 8px 10px;
    }

    .users-table th,
    .users-table td {
        padding: 8px 10px;
        font-size: 12px;
    }

    .users-table th {
        font-size: 11px;
    }

    .checkbox-col {
        width: 36px;
        padding: 8px 4px;
    }

    .user-name {
        font-size: 13px;
    }

    .user-username {
        font-size: 11px;
    }

    .badge {
        font-size: 10px;
        padding: 3px 6px;
    }

    .btn {
        padding: 8px 16px;
        font-size: 12px;
    }

    .btn-sm {
        padding: 4px 8px;
        font-size: 11px;
    }

    .bulk-actions-buttons .btn {
        font-size: 11px;
        padding: 6px 12px;
    }

    /* Hide less important columns on mobile */
    .last-login-cell,
    .created-date {
        display: none;
    }

    .users-table th:nth-child(6),
    .users-table th:nth-child(7),
    .users-table td:nth-child(6),
    .users-table td:nth-child(7) {
        display: none;
    }
}

.d-none {
    display: none !important;
}
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
    updateBulkActions();
});

document.querySelectorAll('.user-checkbox').forEach(cb => {
    cb.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checked = document.querySelectorAll('.user-checkbox:checked');
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
    return Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
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
