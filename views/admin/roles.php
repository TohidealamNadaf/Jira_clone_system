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
            <h2 class="mb-0">Role Management</h2>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bi bi-plus-lg me-1"></i> Create Role
        </button>
    </div>

    <div class="row">
        <!-- Roles List -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Roles</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($roles ?? [] as $role): ?>
                    <a href="?role=<?= $role['id'] ?>" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= ($selectedRole['id'] ?? null) == $role['id'] ? 'active' : '' ?>">
                        <div>
                            <i class="bi bi-shield me-2"></i>
                            <strong><?= e($role['name']) ?></strong>
                            <?php if ($role['is_default'] ?? false): ?>
                            <span class="badge bg-info ms-1">Default</span>
                            <?php endif; ?>
                            <?php if ($role['is_system'] ?? false): ?>
                            <span class="badge bg-secondary ms-1">System</span>
                            <?php endif; ?>
                            <br>
                            <small class="text-<?= ($selectedRole['id'] ?? null) == $role['id'] ? 'white-50' : 'muted' ?>">
                                <?= e($role['description'] ?? 'No description') ?>
                            </small>
                        </div>
                        <span class="badge bg-<?= ($selectedRole['id'] ?? null) == $role['id'] ? 'white text-dark' : 'secondary' ?>">
                            <?= $role['user_count'] ?? 0 ?>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Role Details / Permissions -->
        <div class="col-lg-8">
            <?php if ($selectedRole ?? null): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= e($selectedRole['name']) ?></h5>
                    <?php if (!($selectedRole['is_system'] ?? false)): ?>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </button>
                        <form action="<?= url('/admin/roles/' . $selectedRole['id']) ?>" method="POST"
                              onsubmit="return confirm('Delete this role?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <p class="text-muted"><?= e($selectedRole['description'] ?? 'No description') ?></p>
                    
                    <form action="<?= url('/admin/roles/' . $selectedRole['id'] . '/permissions') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <h6 class="mb-3">Permissions</h6>
                        
                        <?php 
                        $permissionGroups = [
                            'Projects' => ['view-projects', 'create-projects', 'edit-projects', 'delete-projects', 'manage-project-members'],
                            'Issues' => ['view-issues', 'create-issues', 'edit-issues', 'delete-issues', 'assign-issues', 'transition-issues'],
                            'Comments' => ['view-comments', 'create-comments', 'edit-own-comments', 'edit-all-comments', 'delete-comments'],
                            'Sprints' => ['view-sprints', 'manage-sprints', 'start-sprints', 'complete-sprints'],
                            'Boards' => ['view-boards', 'manage-boards', 'configure-columns'],
                            'Reports' => ['view-reports', 'export-reports'],
                            'Administration' => ['admin-access', 'manage-users', 'manage-roles', 'manage-settings', 'view-audit-log'],
                        ];
                        
                        $rolePermissions = $selectedRole['permissions'] ?? [];
                        ?>
                        
                        <?php foreach ($permissionGroups as $group => $permissions): ?>
                        <div class="mb-4">
                            <h6 class="text-muted mb-2"><?= $group ?></h6>
                            <div class="row g-2">
                                <?php foreach ($permissions as $permission): ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="permissions[]" value="<?= $permission ?>" 
                                               id="perm-<?= $permission ?>"
                                               <?= in_array($permission, $rolePermissions) ? 'checked' : '' ?>
                                               <?= ($selectedRole['is_system'] ?? false) ? 'disabled' : '' ?>>
                                        <label class="form-check-label" for="perm-<?= $permission ?>">
                                            <?= ucwords(str_replace('-', ' ', $permission)) ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if (!($selectedRole['is_system'] ?? false)): ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Permissions
                        </button>
                        <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            System roles cannot be modified.
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Users with this Role -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Users with this Role</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roleUsers ?? [] as $u): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <?= strtoupper(substr($u['first_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <?= e($u['display_name'] ?? $u['first_name'] . ' ' . $u['last_name']) ?>
                                    </div>
                                </td>
                                <td><?= e($u['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($u['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('/admin/users/' . $u['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($roleUsers)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No users with this role</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-shield fs-1 text-muted d-block mb-3"></i>
                    <h5>Select a Role</h5>
                    <p class="text-muted">Choose a role from the list to view and manage its permissions.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/admin/roles') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" id="isDefault">
                        <label class="form-check-label" for="isDefault">
                            Set as default role for new users
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<?php if ($selectedRole ?? null): ?>
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/admin/roles/' . $selectedRole['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" required value="<?= e($selectedRole['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"><?= e($selectedRole['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" id="editIsDefault" 
                               <?= ($selectedRole['is_default'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="editIsDefault">
                            Set as default role for new users
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php \App\Core\View::endSection(); ?>
