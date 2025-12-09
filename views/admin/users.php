<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
            <h2 class="mb-0">User Management</h2>
        </div>
        <a href="<?= url('/admin/users/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create User
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= url('/admin/users') ?>" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search users..." 
                           value="<?= e($filters['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <?php foreach ($roles ?? [] as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= ($filters['role'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                            <?= e($role['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="<?= url('/admin/users') ?>" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span><?= $totalUsers ?? 0 ?> users found</span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= url('/admin/users/export?format=csv') ?>">Export as CSV</a></li>
                    <li><a class="dropdown-item" href="<?= url('/admin/users/export?format=xlsx') ?>">Export as Excel</a></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users ?? [] as $u): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input user-checkbox" value="<?= $u['id'] ?>">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($u['avatar'] ?? null): ?>
                                <img src="<?= e($u['avatar']) ?>" class="rounded-circle me-2" width="36" height="36">
                                <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                     style="width: 36px; height: 36px; font-size: 0.875rem;">
                                    <?= strtoupper(substr($u['first_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-medium">
                                        <?= e($u['display_name'] ?? $u['first_name'] . ' ' . $u['last_name']) ?>
                                        <?php if ($u['is_admin']): ?>
                                        <span class="badge bg-danger ms-2" title="System Administrator">Admin</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">@<?= e($u['username'] ?? '') ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= e($u['email']) ?></td>
                        <td>
                            <?php 
                            $userRoles = $u['roles'] ?? '';
                            if (is_string($userRoles) && !empty($userRoles)):
                                foreach (explode(',', $userRoles) as $roleName): ?>
                            <span class="badge bg-secondary"><?= e(trim($roleName)) ?></span>
                                <?php endforeach;
                            elseif (is_array($userRoles)):
                                foreach ($userRoles as $role): ?>
                            <span class="badge bg-secondary"><?= e($role['name'] ?? $role) ?></span>
                                <?php endforeach;
                            else: ?>
                            <span class="badge bg-light text-muted">No role</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($u['is_active'])): ?>
                            <span class="badge bg-success">Active</span>
                            <?php elseif (empty($u['email_verified_at'])): ?>
                            <span class="badge bg-warning">Pending</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['last_login_at']): ?>
                            <span title="<?= format_datetime($u['last_login_at']) ?>"><?= time_ago($u['last_login_at']) ?></span>
                            <?php else: ?>
                            <span class="text-muted">Never</span>
                            <?php endif; ?>
                        </td>
                        <td><?= format_date($u['created_at']) ?></td>
                        <td>
                            <?php if ($u['is_admin']): ?>
                            <span class="badge bg-warning text-dark" title="Administrator users cannot be modified">
                                <i class="bi bi-shield-lock me-1"></i> Protected
                            </span>
                            <?php else: ?>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="<?= url('/admin/users/' . $u['id'] . '/edit') ?>">
                                            <i class="bi bi-pencil me-2"></i> Edit
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
                                        <form action="<?= url('/admin/users/' . $u['id'] . '/deactivate') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="dropdown-item text-warning">
                                                <i class="bi bi-pause-circle me-2"></i> Deactivate
                                            </button>
                                        </form>
                                    </li>
                                    <?php else: ?>
                                    <li>
                                        <form action="<?= url('/admin/users/' . $u['id'] . '/activate') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="dropdown-item text-success">
                                                <i class="bi bi-play-circle me-2"></i> Activate
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>
                                    <li>
                                        <form action="<?= url('/admin/users/' . $u['id']) ?>" method="POST"
                                              onsubmit="return confirm('Delete this user? This action cannot be undone.')">
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
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (($totalPages ?? 1) > 1): ?>
        <div class="card-footer bg-transparent">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0">
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

    <!-- Bulk Actions -->
    <div class="card border-0 shadow-sm mt-3 d-none" id="bulkActions">
        <div class="card-body d-flex justify-content-between align-items-center">
            <span><strong id="selectedCount">0</strong> users selected</span>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-success" onclick="bulkActivate()">
                    <i class="bi bi-play-circle me-1"></i> Activate
                </button>
                <button class="btn btn-sm btn-outline-warning" onclick="bulkDeactivate()">
                    <i class="bi bi-pause-circle me-1"></i> Deactivate
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
document.getElementById('selectAll').addEventListener('change', function() {
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
        await api('/admin/users/bulk-activate', {
            method: 'POST',
            body: JSON.stringify({ user_ids: ids })
        });
        location.reload();
    }
}

async function bulkDeactivate() {
    const ids = getSelectedIds();
    if (confirm(`Deactivate ${ids.length} users?`)) {
        await api('/admin/users/bulk-deactivate', {
            method: 'POST',
            body: JSON.stringify({ user_ids: ids })
        });
        location.reload();
    }
}

async function bulkDelete() {
    const ids = getSelectedIds();
    if (confirm(`Delete ${ids.length} users? This cannot be undone.`)) {
        await api('/admin/users/bulk-delete', {
            method: 'POST',
            body: JSON.stringify({ user_ids: ids })
        });
        location.reload();
    }
}
</script>
<?php \App\Core\View::endSection(); ?>
