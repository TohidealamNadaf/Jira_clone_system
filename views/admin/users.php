<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-shield-lock"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">User Management</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <div class="header-icon-badge">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="header-info">
                <h1 class="page-title">User Management</h1>
                <p class="page-subtitle">Manage system access, roles, and user accounts.</p>
            </div>
        </div>
        <div class="header-actions">
            <!-- Export Dropdown -->
            <div class="dropdown d-inline-block">
                <button type="button" class="action-button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i>
                    <span>Export</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1">
                    <li><a class="dropdown-item py-2" href="<?= url('/admin/users/export?format=csv') ?>">
                            <i class="bi bi-file-text me-2"></i>Export as CSV
                        </a></li>
                    <li><a class="dropdown-item py-2" href="<?= url('/admin/users/export?format=xlsx') ?>">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export as Excel
                        </a></li>
                </ul>
            </div>

            <a href="<?= url('/admin/users/create') ?>" class="action-button primary">
                <i class="bi bi-plus-lg"></i>
                <span>Create User</span>
            </a>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">
        <div class="content-left">
            <!-- Users Table Card -->
            <div class="enterprise-card">
                <div class="card-header-bar">
                    <h2 class="card-title">All Users</h2>
                    <span class="badge-count"><?= $totalUsers ?? 0 ?> Total</span>
                </div>

                <!-- Bulk Actions Toolbar (Hidden by default) -->
                <div id="bulkActions" class="bulk-actions-toolbar" style="display: none;">
                    <span class="bulk-selected-count"><span id="selectedCount">0</span> users selected</span>
                    <div class="bulk-actions-buttons">
                        <button class="action-button small success" onclick="bulkActivate()">
                            <i class="bi bi-play-circle-fill"></i> Activate
                        </button>
                        <button class="action-button small warning" onclick="bulkDeactivate()">
                            <i class="bi bi-pause-circle-fill"></i> Deactivate
                        </button>
                        <button class="action-button small danger" onclick="bulkDelete()">
                            <i class="bi bi-trash-fill"></i> Delete
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="enterprise-table">
                        <thead>
                            <tr>
                                <th class="col-checkbox">
                                    <input type="checkbox" class="custom-checkbox" id="selectAll">
                                </th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th width="80"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="empty-state-row">
                                        <div class="empty-state-content">
                                            <i class="bi bi-people" style="font-size: 48px; color: #DFE1E6;"></i>
                                            <p class="mt-3 text-muted">No users found matching your criteria</p>
                                            <a href="<?= url('/admin/users/create') ?>" class="action-button small">
                                                Create User
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                    <tr class="table-row-hover">
                                        <td class="col-checkbox">
                                            <input type="checkbox" class="custom-checkbox user-checkbox"
                                                value="<?= $u['id'] ?>">
                                        </td>
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar">
                                                    <?php if (($avatarUrl = avatar($u['avatar'] ?? null))): ?>
                                                        <img src="<?= e($avatarUrl) ?>" alt="<?= e($u['display_name']) ?>">
                                                    <?php else: ?>
                                                        <div class="avatar-initials">
                                                            <?= strtoupper(substr($u['first_name'] ?? 'U', 0, 1) . substr($u['last_name'] ?? '', 0, 1)) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="user-info">
                                                    <a href="<?= url('/admin/users/' . $u['id'] . '/edit') ?>"
                                                        class="user-name-link">
                                                        <?= e($u['display_name'] ?? $u['first_name'] . ' ' . $u['last_name']) ?>
                                                    </a>
                                                    <span class="user-username">@<?= e($u['username'] ?? '') ?></span>
                                                </div>
                                                <?php if ($u['is_admin']): ?>
                                                    <span class="admin-badge" title="System Administrator">
                                                        <i class="bi bi-shield-fill"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-muted small"><?= e($u['email']) ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php
                                                $userRoles = $u['roles'] ?? '';
                                                if (is_string($userRoles) && !empty($userRoles)):
                                                    foreach (explode(',', $userRoles) as $roleName): ?>
                                                        <span class="role-badge"><?= e(trim($roleName)) ?></span>
                                                    <?php endforeach;
                                                elseif (is_array($userRoles)):
                                                    foreach ($userRoles as $role): ?>
                                                        <span class="role-badge"><?= e($role['name'] ?? $role) ?></span>
                                                    <?php endforeach;
                                                else: ?>
                                                    <span class="text-muted small italic">No role</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($u['is_active'])): ?>
                                                <span class="status-pill success">Active</span>
                                            <?php elseif (empty($u['email_verified_at'])): ?>
                                                <span class="status-pill warning">Pending</span>
                                            <?php else: ?>
                                                <span class="status-pill inactive">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small">
                                            <?php if ($u['last_login_at']): ?>
                                                <span title="<?= format_datetime($u['last_login_at']) ?>">
                                                    <?= time_ago($u['last_login_at']) ?>
                                                </span>
                                            <?php else: ?>
                                                Never
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-link btn-sm text-secondary p-0" type="button"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="<?= url('/admin/users/' . $u['id'] . '/edit') ?>">
                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= url('/admin/users/' . $u['id']) ?>">
                                                            <i class="bi bi-eye me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <?php if (!$u['is_admin']): ?>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <?php if (!empty($u['is_active'])): ?>
                                                            <li>
                                                                <form action="<?= url('/admin/users/' . $u['id'] . '/deactivate') ?>"
                                                                    method="POST">
                                                                    <?= csrf_field() ?>
                                                                    <button type="submit" class="dropdown-item text-warning">
                                                                        <i class="bi bi-pause-circle me-2"></i>Deactivate
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        <?php else: ?>
                                                            <li>
                                                                <form action="<?= url('/admin/users/' . $u['id'] . '/activate') ?>"
                                                                    method="POST">
                                                                    <?= csrf_field() ?>
                                                                    <button type="submit" class="dropdown-item text-success">
                                                                        <i class="bi bi-play-circle me-2"></i>Activate
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li>
                                                            <form action="<?= url('/admin/users/' . $u['id']) ?>" method="POST"
                                                                onsubmit="return confirm('Delete this user? This action cannot be undone.');">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="_method" value="DELETE">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-trash me-2"></i>Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (($totalPages ?? 1) > 1): ?>
                    <div class="card-footer-pagination">
                        <span class="text-muted small">
                            Showing page <?= $currentPage ?> of <?= $totalPages ?>
                        </span>
                        <div class="pagination-buttons">
                            <?php if ($currentPage > 1): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>"
                                    class="pagination-btn prev">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            <?php else: ?>
                                <button class="pagination-btn prev disabled" disabled><i
                                        class="bi bi-chevron-left"></i></button>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                                        class="pagination-btn <?= $i === $currentPage ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php elseif (abs($i - $currentPage) == 3): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>"
                                    class="pagination-btn next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <button class="pagination-btn next disabled" disabled><i
                                        class="bi bi-chevron-right"></i></button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-right">
            <!-- Filter Card -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Filter Users</h3>
                <form action="<?= url('/admin/users') ?>" method="GET">
                    <div class="form-group mb-3">
                        <label class="form-label small text-muted">Search</label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-search input-icon"></i>
                            <input type="text" name="search" class="form-control-enterprise"
                                placeholder="Name, email, username..." value="<?= e($filters['search'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label small text-muted">Status</label>
                        <select name="status" class="form-select-enterprise">
                            <option value="">All Statuses</option>
                            <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active
                            </option>
                            <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>
                                Inactive</option>
                            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>
                                Pending</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label small text-muted">Role</label>
                        <select name="role" class="form-select-enterprise">
                            <option value="">All Roles</option>
                            <?php foreach ($roles ?? [] as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= ($filters['role'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                    <?= e($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="action-button primary w-100 justify-content-center">
                            Apply Filters
                        </button>
                        <?php if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['role'])): ?>
                            <a href="<?= url('/admin/users') ?>" class="action-button w-100 justify-content-center">
                                Clear Filters
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const selectAll = document.getElementById('selectAll');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        const table = document.querySelector('.enterprise-table');

        // Update UI based on selection
        function updateSelectionState() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
            const count = checkedCheckboxes.length;

            // Update count text
            if (selectedCount) {
                selectedCount.textContent = count;
            }

            // Show/Hide Toolbar
            if (bulkActions) {
                if (count > 0) {
                    bulkActions.style.display = 'flex';
                } else {
                    bulkActions.style.display = 'none';
                }
            }

            // Update Select All checkbox state
            if (selectAll) {
                if (count === 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                } else if (count === checkboxes.length) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                }
            }
        }

        // Handle Select All click
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const isChecked = this.checked;
                const checkboxes = document.querySelectorAll('.user-checkbox');
                checkboxes.forEach(cb => cb.checked = isChecked);
                updateSelectionState();
            });
        }

        // Handle Individual Checkbox clicks via Delegation
        if (table) {
            table.addEventListener('change', function (e) {
                if (e.target.classList.contains('user-checkbox')) {
                    updateSelectionState();
                }
            });
        }
    });

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    }

    function bulkAction(action, endpoint) {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        if (!confirm(`Are you sure you want to ${action} ${ids.length} users?`)) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = endpoint;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_csrf_token';
        csrf.value = '<?= csrf_token() ?>';
        form.appendChild(csrf);

        const idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'ids';
        idsInput.value = JSON.stringify(ids);
        form.appendChild(idsInput);

        document.body.appendChild(form);
        form.submit();
    }

    function bulkActivate() { bulkAction('activate', '<?= url("/admin/users/bulk-activate") ?>'); }
    function bulkDeactivate() { bulkAction('deactivate', '<?= url("/admin/users/bulk-deactivate") ?>'); }
    function bulkDelete() {
        if (!confirm('This will explicitly delete selected users. Continue?')) return;
        bulkAction('delete', '<?= url("/admin/users/bulk-delete") ?>');
    }
</script>

<style>
    /* ============================================
   ADMIN COMPONENT STYLES (Shared & Users Specific)
   ============================================ */

    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-dark: #161B22 !important;
        --jira-gray: #626F86 !important;
        --jira-light: #F7F8FA !important;
        --jira-border: #DFE1E6 !important;
        --white: #FFFFFF;
        --jira-green: #216E4E;
        --jira-yellow: #FFAB00;
        --jira-red: #DE350B;
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
    }

    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
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
        color: #FFFFFF !important;
        border: none;
    }

    .action-button.primary:hover {
        background: var(--jira-blue-dark);
        color: #FFFFFF !important;
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
        overflow: hidden;
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

    /* User Cell */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        flex-shrink: 0;
    }

    .user-avatar img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .avatar-initials {
        width: 32px;
        height: 32px;
        background: var(--jira-blue);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name-link {
        color: var(--jira-blue) !important;
        font-weight: 600;
        text-decoration: none;
    }

    .user-name-link:hover {
        text-decoration: underline;
    }

    .user-username {
        color: var(--jira-gray);
        font-size: 11px;
    }

    .admin-badge {
        color: #FF991F;
        font-size: 14px;
        margin-left: 4px;
    }

    /* Badges & Pills */
    .role-badge {
        background: #E9F2FF;
        color: #0052CC;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-pill {
        display: inline-flex;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-pill.success {
        background: #E3FCEF;
        color: #006644;
    }

    .status-pill.warning {
        background: #FFF0B3;
        color: #172B4D;
    }

    .status-pill.inactive {
        background: #DFE1E6;
        color: #42526E;
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
        margin-bottom: 20px;
    }

    /* Forms */
    .form-control-enterprise,
    .form-select-enterprise {
        width: 100%;
        padding: 8px 12px;
        border: 2px solid var(--jira-border);
        border-radius: 4px;
        font-size: 14px;
        color: var(--jira-dark);
        background: #F4F5F7;
        transition: all 0.2s;
    }

    .form-control-enterprise:focus,
    .form-select-enterprise:focus {
        background: var(--white);
        border-color: var(--jira-blue);
        outline: none;
    }

    .input-icon-wrapper {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--jira-gray);
    }

    .input-icon-wrapper input {
        padding-left: 32px;
    }

    /* Bulk Actions */
    .bulk-actions-toolbar {
        background: #E3FCEF;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ABF5D1;
    }

    .bulk-selected-count {
        font-weight: 600;
        color: #006644;
        font-size: 13px;
    }

    .bulk-actions-buttons {
        display: flex;
        gap: 8px;
    }

    .action-button.small {
        padding: 4px 10px;
        font-size: 12px;
    }

    .action-button.success {
        background: white;
        color: var(--jira-green);
        border-color: var(--jira-green);
    }

    .action-button.warning {
        background: white;
        color: var(--jira-yellow);
        border-color: var(--jira-yellow);
    }

    .action-button.danger {
        background: white;
        color: var(--jira-red);
        border-color: var(--jira-red);
    }

    /* Pagination */
    .card-footer-pagination {
        padding: 16px 20px;
        border-top: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #F9FAFB;
    }

    .pagination-buttons {
        display: flex;
        gap: 4px;
    }

    .pagination-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        background: var(--white);
        color: var(--jira-dark);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
    }

    .pagination-btn:hover {
        background: #EBECF0;
    }

    .pagination-btn.active {
        background: var(--jira-blue);
        color: white;
        border-color: var(--jira-blue);
    }

    .pagination-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Checkboxes */
    .custom-checkbox {
        width: 16px;
        height: 16px;
        accent-color: var(--jira-blue);
        cursor: pointer;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .page-content {
            flex-direction: column;
        }

        .content-right {
            width: 100%;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>