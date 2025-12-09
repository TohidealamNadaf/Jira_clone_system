<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<?php $isEdit = isset($editUser); ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                            <li class="breadcrumb-item"><a href="<?= url('/admin/users') ?>">Users</a></li>
                            <li class="breadcrumb-item active"><?= $isEdit ? 'Edit User' : 'Create User' ?></li>
                        </ol>
                    </nav>
                    <h2 class="mb-0"><?= $isEdit ? 'Edit User' : 'Create New User' ?></h2>
                </div>
            </div>

            <form action="<?= $isEdit ? url('/admin/users/' . $editUser['id']) : url('/admin/users') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <!-- Basic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                             <div class="col-md-6">
                                 <label class="form-label">First Name <span class="text-danger">*</span></label>
                                 <input type="text" name="first_name" class="form-control <?= hasError('first_name') ? 'is-invalid' : '' ?>" 
                                        value="<?= e($editUser['first_name'] ?? old('first_name')) ?>" 
                                        <?= ($isAdmin ?? false) ? 'disabled' : '' ?> required>
                                 <?php if (hasError('first_name')): ?>
                                 <div class="invalid-feedback"><?= getError('first_name') ?></div>
                                 <?php endif; ?>
                             </div>
                             <div class="col-md-6">
                                 <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                 <input type="text" name="last_name" class="form-control <?= hasError('last_name') ? 'is-invalid' : '' ?>" 
                                        value="<?= e($editUser['last_name'] ?? old('last_name')) ?>"
                                        <?= ($isAdmin ?? false) ? 'disabled' : '' ?> required>
                                 <?php if (hasError('last_name')): ?>
                                 <div class="invalid-feedback"><?= getError('last_name') ?></div>
                                 <?php endif; ?>
                             </div>
                             <div class="col-md-6">
                                 <label class="form-label">Email <span class="text-danger">*</span></label>
                                 <input type="email" name="email" class="form-control <?= hasError('email') ? 'is-invalid' : '' ?>" 
                                        value="<?= e($editUser['email'] ?? old('email')) ?>"
                                        <?= ($isAdmin ?? false) ? 'disabled' : '' ?> required>
                                 <?php if (hasError('email')): ?>
                                 <div class="invalid-feedback"><?= getError('email') ?></div>
                                 <?php endif; ?>
                             </div>
                             <div class="col-md-6">
                                 <label class="form-label">Timezone</label>
                                 <select name="timezone" class="form-select" <?= ($isAdmin ?? false) ? 'disabled' : '' ?>>
                                     <?php foreach ($timezones ?? ['UTC'] as $tz): ?>
                                     <option value="<?= $tz ?>" <?= ($editUser['timezone'] ?? old('timezone', 'UTC')) === $tz ? 'selected' : '' ?>>
                                         <?= e($tz) ?>
                                     </option>
                                     <?php endforeach; ?>
                                 </select>
                             </div>
                         </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><?= $isEdit ? 'Change Password' : 'Password' ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                             <div class="col-md-6">
                                 <label class="form-label">
                                     Password <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?>
                                 </label>
                                 <input type="password" name="password" class="form-control <?= hasError('password') ? 'is-invalid' : '' ?>" 
                                        <?= $isEdit ? '' : 'required' ?> <?= ($isAdmin ?? false) ? 'disabled' : '' ?> minlength="8">
                                 <?php if (hasError('password')): ?>
                                 <div class="invalid-feedback"><?= getError('password') ?></div>
                                 <?php endif; ?>
                                 <?php if ($isEdit && !($isAdmin ?? false)): ?>
                                 <small class="text-muted">Leave blank to keep current password</small>
                                 <?php elseif ($isAdmin ?? false): ?>
                                 <small class="text-muted">Cannot be changed for administrator accounts</small>
                                 <?php endif; ?>
                             </div>
                             <div class="col-md-6">
                                 <label class="form-label">
                                     Confirm Password <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?>
                                 </label>
                                 <input type="password" name="password_confirmation" class="form-control" 
                                        <?= $isEdit ? '' : 'required' ?> <?= ($isAdmin ?? false) ? 'disabled' : '' ?>>
                             </div>
                         </div>
                    </div>
                </div>

                <!-- Role & Permissions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">Role & Permissions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                             <div class="col-md-6">
                                 <label class="form-label">Role <span class="text-danger">*</span></label>
                                 <select name="role_id" class="form-select <?= hasError('role_id') ? 'is-invalid' : '' ?>" 
                                         <?= ($isAdmin ?? false) ? 'disabled' : '' ?> required>
                                     <option value="">Select Role...</option>
                                     <?php foreach ($roles ?? [] as $role): ?>
                                     <option value="<?= $role['id'] ?>" 
                                             <?= ($editUser['role_id'] ?? old('role_id')) == $role['id'] ? 'selected' : '' ?>>
                                         <?= e($role['name']) ?>
                                     </option>
                                     <?php endforeach; ?>
                                 </select>
                                 <?php if (hasError('role_id')): ?>
                                 <div class="invalid-feedback"><?= getError('role_id') ?></div>
                                 <?php endif; ?>
                             </div>
                             <div class="col-md-6 d-flex align-items-end">
                                 <?php if ($isAdmin ?? false): ?>
                                 <div class="alert alert-warning mb-0 w-100">
                                     <i class="bi bi-shield-lock me-1"></i>
                                     <strong>Administrator Account</strong> - Cannot be modified.
                                 </div>
                                 <?php endif; ?>
                             </div>
                         </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between">
                    <a href="<?= url('/admin/users') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" <?= ($isAdmin ?? false) ? 'disabled' : '' ?>>
                        <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Update User' : 'Create User' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
