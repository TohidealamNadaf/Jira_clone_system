<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ol>
            </nav>
            <h2 class="mb-0">Manage Roles</h2>
        </div>
        <a href="<?= url('/admin/roles/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Role
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($roles)): ?>
            <div class="text-center py-5">
                <i class="bi bi-shield fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">No roles found</h5>
                <p class="text-muted mb-3">Create your first role to get started.</p>
                <a href="<?= url('/admin/roles/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Create Role
                </a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                        <tr>
                            <td>
                                <a href="<?= url('/admin/roles/' . $role['id']) ?>" class="fw-medium text-decoration-none">
                                    <?= e($role['name']) ?>
                                </a>
                                <?php if ($role['is_system'] ?? false): ?>
                                <span class="badge bg-secondary ms-1">System</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= e($role['description'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-light text-dark"><?= (int)($role['user_count'] ?? 0) ?> users</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark"><?= (int)($role['permission_count'] ?? 0) ?> permissions</span>
                            </td>
                            <td class="text-end">
                                <?php if ($role['is_system'] ?? false): ?>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/roles/' . $role['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <span class="badge bg-warning" title="System roles cannot be modified">
                                        <i class="bi bi-shield-lock me-1"></i> Protected
                                    </span>
                                </div>
                                <?php else: ?>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/roles/' . $role['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= url('/admin/roles/' . $role['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= url('/admin/roles/' . $role['id']) ?>" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this role?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Role Info -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About Roles</h5>
        </div>
        <div class="card-body">
            <p class="mb-2">Roles define what users can do in the system. Each role has a set of permissions that control access to features.</p>
            <ul class="mb-0">
                <li><strong>System roles</strong> cannot be edited or deleted. Create custom roles for additional access control.</li>
                <li>Users can have multiple roles assigned to them.</li>
                <li>Permissions are additive - users get all permissions from all their roles.</li>
            </ul>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
