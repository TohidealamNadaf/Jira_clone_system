<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<?php $isEdit = isset($role); ?>
<?php $isSystemRole = $isSystemRole ?? false; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                            <li class="breadcrumb-item"><a href="<?= url('/admin/roles') ?>">Roles</a></li>
                            <li class="breadcrumb-item active"><?= $isEdit ? 'Edit Role' : 'Create Role' ?></li>
                        </ol>
                    </nav>
                    <h2 class="mb-0"><?= $isEdit ? 'Edit Role: ' . e($role['name']) : 'Create New Role' ?></h2>
                </div>
            </div>

            <?php if ($isSystemRole): ?>
            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-shield-lock me-2"></i>
                <strong>System Role</strong> - Cannot be modified.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <form action="<?= $isEdit ? url('/admin/roles/' . $role['id']) : url('/admin/roles') ?>" method="POST">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <!-- Basic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Role Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control <?= hasError('name') ? 'is-invalid' : '' ?>" 
                                   value="<?= e($role['name'] ?? old('name')) ?>" 
                                   <?= $isSystemRole ? 'disabled' : '' ?> required>
                            <?php if (hasError('name')): ?>
                            <div class="invalid-feedback"><?= getError('name') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" <?= $isSystemRole ? 'disabled' : '' ?>><?= e($role['description'] ?? old('description')) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Permissions</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($allPermissions)): ?>
                        <p class="text-muted mb-0">No permissions available.</p>
                        <?php else: ?>
                        <?php
                        // Group permissions by category
                        $grouped = [];
                        foreach ($allPermissions as $perm) {
                            $category = $perm['category'] ?? 'General';
                            $grouped[$category][] = $perm;
                        }
                        ?>
                        
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAll()" <?= $isSystemRole ? 'disabled' : '' ?>>Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectNone()" <?= $isSystemRole ? 'disabled' : '' ?>>Select None</button>
                        </div>

                        <?php foreach ($grouped as $category => $perms): ?>
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted mb-3"><?= e(ucwords(str_replace('_', ' ', $category))) ?></h6>
                            <div class="row">
                                <?php foreach ($perms as $perm): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" type="checkbox" 
                                               name="permissions[]" value="<?= $perm['id'] ?>" 
                                               id="perm_<?= $perm['id'] ?>"
                                               <?= in_array($perm['id'], $permissions) ? 'checked' : '' ?>
                                               <?= $isSystemRole ? 'disabled' : '' ?>>
                                        <label class="form-check-label" for="perm_<?= $perm['id'] ?>">
                                            <?= e($perm['name']) ?>
                                            <?php if (!empty($perm['description'])): ?>
                                            <br><small class="text-muted"><?= e($perm['description']) ?></small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between">
                    <a href="<?= url('/admin/roles') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" <?= $isSystemRole ? 'disabled' : '' ?>>
                        <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update Role' : 'Create Role' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
}

function selectNone() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
}
</script>

<?php \App\Core\View::endSection(); ?>
