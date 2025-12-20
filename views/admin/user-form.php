<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<?php $isEdit = isset($editUser); ?>

<div class="page-wrapper">

    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/admin/users') ?>" class="breadcrumb-link">
            Users
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= $isEdit ? 'Edit User' : 'Create User' ?></span>
    </div>

    <!-- Page Header Section -->
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="bi bi-person-plus"></i>
                <?= $isEdit ? 'Edit User' : 'Create User' ?>
            </h1>
            <div class="header-actions">
                <button type="submit" form="user-form" class="btn-primary">
                    <i class="bi bi-check-lg"></i> <?= $isEdit ? 'Update User' : 'Create User' ?>
                </button>
                <a href="<?= url('/admin/users') ?>" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
            </div>
        </div>
        <?php if ($isEdit && isset($editUser)): ?>
        <div class="user-meta">
            <div class="user-avatar">
                <?php if (!empty($editUser['avatar'])): ?>
                <img src="<?= url($editUser['avatar']) ?>" alt="<?= e($editUser['first_name']) ?>" class="avatar-image">
                <?php else: ?>
                <div class="avatar-initials">
                    <?= strtoupper(substr($editUser['first_name'], 0, 1) . substr($editUser['last_name'] ?? '', 0, 1)) ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="user-details">
                <div class="user-name"><?= e($editUser['first_name'] . ' ' . ($editUser['last_name'] ?? '')) ?></div>
                <div class="user-info">
                    <?= e($editUser['email']) ?> •
                    <?php
                    $roleName = '';
                    foreach ($roles ?? [] as $role) {
                        if ($role['id'] == $editUser['role_id']) {
                            $roleName = $role['name'];
                            break;
                        }
                    }
                    echo e($roleName);
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
            </div>

    <!-- Main Content -->
    <div class="page-content">
        <form id="user-form" action="<?= $isEdit ? url('/admin/users/' . $editUser['id']) : url('/admin/users') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <!-- Basic Information -->
            <div class="form-section">
                <div class="section-header">
                    <h2 class="section-title">Basic information</h2>
                    </div>
                <div class="section-content">
                    <div class="form-row">
                        <div class="form-field">
                            <label class="field-label">
                                First name <span class="required">*</span>
                            </label>
                            <input type="text" name="first_name" class="field-input <?= hasError('first_name') ? 'field-error' : '' ?>"
                                        value="<?= e($editUser['first_name'] ?? old('first_name')) ?>" 
                                        <?= ($isAdmin ?? false) ? 'disabled' : '' ?> required>
                                 <?php if (hasError('first_name')): ?>
                            <div class="field-error-message"><?= getError('first_name') ?></div>
                                 <?php endif; ?>
                             </div>

                        <div class="form-field">
                            <label class="field-label">
                                Last name <span class="required">*</span>
                            </label>
                            <input type="text" name="last_name" class="field-input <?= hasError('last_name') ? 'field-error' : '' ?>"
                                        value="<?= e($editUser['last_name'] ?? old('last_name')) ?>"
                                        <?= ($isAdmin ?? false) ? 'disabled' : '' ?> required>
                                 <?php if (hasError('last_name')): ?>
                            <div class="field-error-message"><?= getError('last_name') ?></div>
                                 <?php endif; ?>
                             </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label class="field-label">
                                Email address <span class="required">*</span>
                            </label>
                            <input type="email" name="email" class="field-input <?= hasError('email') ? 'field-error' : '' ?>"
                                        value="<?= e($editUser['email'] ?? old('email')) ?>"
                                        <?= ($isAdmin ?? false) ? 'disabled' : '' ?> required>
                                 <?php if (hasError('email')): ?>
                            <div class="field-error-message"><?= getError('email') ?></div>
                                 <?php endif; ?>
                             </div>

                        <div class="form-field">
                            <label class="field-label">Timezone</label>
                            <select name="timezone" class="field-select" <?= ($isAdmin ?? false) ? 'disabled' : '' ?>>
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
            <div class="form-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="bi bi-shield-lock"></i>
                        <?= $isEdit ? 'Change password' : 'Password' ?>
                    </h2>
                    </div>
                <div class="section-content">
                    <div class="form-row">
                        <div class="form-field">
                            <label class="field-label">
                                Password <?= $isEdit ? '' : '<span class="required">*</span>' ?>
                                 </label>
                            <input type="password" name="password" class="field-input <?= hasError('password') ? 'field-error' : '' ?>"
                                        <?= $isEdit ? '' : 'required' ?> <?= ($isAdmin ?? false) ? 'disabled' : '' ?> minlength="8">
                                 <?php if (hasError('password')): ?>
                            <div class="field-error-message"><?= getError('password') ?></div>
                                 <?php endif; ?>
                                 <?php if ($isEdit && !($isAdmin ?? false)): ?>
                            <div class="field-help">Leave blank to keep current password</div>
                                 <?php elseif ($isAdmin ?? false): ?>
                            <div class="field-help">Cannot be changed for administrator accounts</div>
                                 <?php endif; ?>
                             </div>

                        <div class="form-field">
                            <label class="field-label">
                                Confirm password <?= $isEdit ? '' : '<span class="required">*</span>' ?>
                                 </label>
                            <input type="password" name="password_confirmation" class="field-input"
                                        <?= $isEdit ? '' : 'required' ?> <?= ($isAdmin ?? false) ? 'disabled' : '' ?>>
                             </div>
                         </div>
                    </div>
                </div>

                <!-- Role & Permissions -->
            <div class="form-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="bi bi-person-badge"></i>
                        Role and permissions
                    </h2>
                    </div>
                <div class="section-content">
                    <div class="form-row">
                        <div class="form-field">
                            <label class="field-label">
                                User role <span class="required">*</span>
                            </label>
                            <select name="role_id" class="field-select <?= hasError('role_id') ? 'field-error' : '' ?>"
                                         <?= ($isAdmin ?? false) ? 'disabled' : '' ?> required>
                                <option value="">Select role...</option>
                                     <?php foreach ($roles ?? [] as $role): ?>
                                     <option value="<?= $role['id'] ?>" 
                                             <?= ($editUser['role_id'] ?? old('role_id')) == $role['id'] ? 'selected' : '' ?>>
                                         <?= e($role['name']) ?>
                                     </option>
                                     <?php endforeach; ?>
                                 </select>
                                 <?php if (hasError('role_id')): ?>
                            <div class="field-error-message"><?= getError('role_id') ?></div>
                                 <?php endif; ?>
                             </div>

                                 <?php if ($isAdmin ?? false): ?>
                        <div class="form-field">
                            <div class="admin-notice">
                                <i class="bi bi-info-circle"></i>
                                <span>This account cannot be modified for security reasons.</span>
                            </div>
                                 </div>
                                 <?php endif; ?>
                             </div>
                         </div>
                    </div>
        </form>
                </div>

    </div>
</div>

<link rel="stylesheet" href="<?= url('/assets/css/user-form.css') ?>">

<?php \App\Core\View::endSection(); ?>
