<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('/admin/roles') ?>">Roles</a></li>
                    <li class="breadcrumb-item active"><?= e($role['name']) ?></li>
                </ol>
            </nav>
            <h2 class="mb-0"><?= e($role['name']) ?></h2>
        </div>
        <div>
            <?php if (!($role['is_system'] ?? false)): ?>
            <a href="<?= url('/admin/roles/' . $role['id'] . '/edit') ?>" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Edit Role
            </a>
            <?php else: ?>
            <span class="badge bg-warning" title="System roles cannot be modified">
                <i class="bi bi-shield-lock me-1"></i> System Role
            </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($role['is_system'] ?? false): ?>
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-shield-lock me-2"></i>
        <strong>System Role</strong> - This role cannot be modified.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Role Details -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Role Details</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Name</dt>
                        <dd><?= e($role['name']) ?></dd>
                        
                        <dt>Description</dt>
                        <dd><?= e($role['description'] ?? 'No description') ?></dd>
                        
                        <dt>Type</dt>
                        <dd>
                            <?php if ($role['is_system'] ?? false): ?>
                            <span class="badge bg-secondary">System Role</span>
                            <?php else: ?>
                            <span class="badge bg-primary">Custom Role</span>
                            <?php endif; ?>
                        </dd>
                        
                        <dt>Created</dt>
                        <dd><?= format_datetime($role['created_at'] ?? '') ?></dd>
                    </dl>
                </div>
            </div>

            <!-- Users with this Role -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Users (<?= count($users) ?>)</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($users)): ?>
                    <div class="p-3 text-muted text-center">
                        No users have this role
                    </div>
                    <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($users as $user): ?>
                        <a href="<?= url('/admin/users/' . $user['id'] . '/edit') ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                 style="width: 32px; height: 32px; font-size: 0.75rem;">
                                <?= strtoupper(substr($user['display_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-medium"><?= e($user['display_name']) ?></div>
                                <small class="text-muted"><?= e($user['email']) ?></small>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Permissions (<?= count($permissions) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($permissions)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-shield-x fs-1 d-block mb-2"></i>
                        No permissions assigned to this role
                    </div>
                    <?php else: ?>
                    <?php
                    // Group permissions by category
                    $grouped = [];
                    foreach ($permissions as $perm) {
                        $category = $perm['category'] ?? 'General';
                        $grouped[$category][] = $perm;
                    }
                    ?>
                    <div class="accordion" id="permissionsAccordion">
                        <?php $index = 0; foreach ($grouped as $category => $perms): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>">
                                    <?= e(ucwords(str_replace('_', ' ', $category))) ?>
                                    <span class="badge bg-primary ms-2"><?= count($perms) ?></span>
                                </button>
                            </h2>
                            <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" 
                                 data-bs-parent="#permissionsAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <?php foreach ($perms as $perm): ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                                <div>
                                                    <div class="fw-medium"><?= e($perm['name']) ?></div>
                                                    <?php if (!empty($perm['description'])): ?>
                                                    <small class="text-muted"><?= e($perm['description']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $index++; endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
