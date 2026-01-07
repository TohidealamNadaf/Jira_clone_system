<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<?php $isEdit = isset($editUser); ?>

<div class="admin-user-form-wrapper">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-gear"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/admin/users') ?>" class="breadcrumb-link">
            Users
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= $isEdit ? e($editUser['first_name'] . ' ' . ($editUser['last_name'] ?? '')) : 'Create User' ?></span>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="header-content">
            <div class="header-left">
                <?php if ($isEdit && isset($editUser)): ?>
                    <div class="header-avatar">
                        <?php if (!empty($editUser['avatar'])): ?>
                            <img src="<?= e(avatar($editUser['avatar'])) ?>" alt="<?= e($editUser['first_name']) ?>" class="avatar-image">
                        <?php else: ?>
                            <div class="avatar-initials">
                                <?= strtoupper(substr($editUser['first_name'], 0, 1) . substr($editUser['last_name'] ?? '', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="header-info">
                        <h1 class="header-title"><?= e($editUser['first_name'] . ' ' . ($editUser['last_name'] ?? '')) ?></h1>
                        <div class="header-meta">
                            <span class="meta-email"><i class="bi bi-envelope"></i> <?= e($editUser['email']) ?></span>
                        </div>
                        <div class="header-badges">
                            <?php
                            $userRoleId = $editUser['role_id'] ?? null;
                            if ($userRoleId !== null) {
                                foreach ($roles ?? [] as $role) {
                                    if ($role['id'] == $userRoleId) {
                                        echo '<span class="badge-role">' . e($role['name']) . '</span>';
                                        break;
                                    }
                                }
                            }
                            ?>
                            <?php if ($editUser['is_admin'] ?? false): ?>
                                <span class="badge-admin">Administrator</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="header-info">
                        <h1 class="header-title">
                            <i class="bi bi-person-plus"></i> Create New User
                        </h1>
                        <p class="header-subtitle">Add a new user to your system</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="header-actions">
                <button type="submit" form="user-form" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?= $isEdit ? 'Update User' : 'Create User' ?>
                </button>
                <a href="<?= url('/admin/users') ?>" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Cancel
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="page-body">
        <form id="user-form" action="<?= $isEdit ? url('/admin/users/' . $editUser['id']) : url('/admin/users') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="content-grid">
                <!-- Left Column: Form Fields -->
                <div class="content-main">
                    <!-- Basic Information Card -->
                    <section class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="bi bi-person-fill"></i> Basic Information
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group-row">
                                <div class="form-group">
                                    <label class="form-label">
                                        First name <span class="required">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="first_name" 
                                        class="form-input <?= hasError('first_name') ? 'error' : '' ?>"
                                        value="<?= e($editUser['first_name'] ?? old('first_name')) ?>" 
                                        <?= ($editUser['is_admin'] ?? false) ? 'disabled' : '' ?> 
                                        required>
                                    <?php if (hasError('first_name')): ?>
                                        <div class="form-error"><?= getError('first_name') ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        Last name <span class="required">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="last_name" 
                                        class="form-input <?= hasError('last_name') ? 'error' : '' ?>"
                                        value="<?= e($editUser['last_name'] ?? old('last_name')) ?>"
                                        <?= ($editUser['is_admin'] ?? false) ? 'disabled' : '' ?> 
                                        required>
                                    <?php if (hasError('last_name')): ?>
                                        <div class="form-error"><?= getError('last_name') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Email address <span class="required">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    class="form-input <?= hasError('email') ? 'error' : '' ?>"
                                    value="<?= e($editUser['email'] ?? old('email')) ?>"
                                    <?= ($editUser['is_admin'] ?? false) ? 'disabled' : '' ?> 
                                    required>
                                <?php if (hasError('email')): ?>
                                    <div class="form-error"><?= getError('email') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group-row">
                                <div class="form-group">
                                    <label class="form-label">Timezone</label>
                                    <select name="timezone" class="form-select" <?= ($editUser['is_admin'] ?? false) ? 'disabled' : '' ?>>
                                        <?php foreach ($timezones ?? ['UTC'] as $tz): ?>
                                            <option value="<?= $tz ?>" <?= ($editUser['timezone'] ?? old('timezone', 'UTC')) === $tz ? 'selected' : '' ?>>
                                                <?= e($tz) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" <?= ($editUser['is_admin'] ?? false) ? 'disabled' : '' ?>>
                                        <option value="active" <?= ($editUser['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= ($editUser['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Security & Password Card -->
                    <section class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="bi bi-shield-lock"></i> Security & Password
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group-row">
                                <div class="form-group">
                                    <label class="form-label">
                                        Password <?= $isEdit ? '' : '<span class="required">*</span>' ?>
                                    </label>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        class="form-input <?= hasError('password') ? 'error' : '' ?>"
                                        <?= $isEdit ? '' : 'required' ?> 
                                        <?= ($editUser['is_admin'] ?? false) ? 'disabled' : '' ?> 
                                        minlength="8">
                                    <?php if (hasError('password')): ?>
                                        <div class="form-error"><?= getError('password') ?></div>
                                    <?php endif; ?>
                                    <div class="form-hint">
                                        <?php if ($isEdit && !($editUser['is_admin'] ?? false)): ?>
                                            Leave blank to keep current password
                                        <?php elseif ($editUser['is_admin'] ?? false): ?>
                                            Cannot be changed for administrator accounts
                                        <?php else: ?>
                                            Minimum 8 characters
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        Confirm password <?= $isEdit ? '' : '<span class="required">*</span>' ?>
                                    </label>
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        class="form-input"
                                        <?= $isEdit ? '' : 'required' ?> 
                                        <?= ($editUser['is_admin'] ?? false) ? 'disabled' : '' ?>>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Role & Permissions Card -->
                    <section class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="bi bi-person-badge"></i> Role & Permissions
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">
                                    User role <span class="required">*</span>
                                </label>
                                <select 
                                    name="role_id" 
                                    class="form-select <?= hasError('role_id') ? 'error' : '' ?>"
                                    <?= ($editUser['is_admin'] ?? false) ? 'disabled' : '' ?> 
                                    required>
                                    <option value="">Select role...</option>
                                    <?php foreach ($roles ?? [] as $role): ?>
                                        <option value="<?= $role['id'] ?>" 
                                            <?= ($editUser['role_id'] ?? old('role_id')) == $role['id'] ? 'selected' : '' ?>>
                                            <?= e($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (hasError('role_id')): ?>
                                    <div class="form-error"><?= getError('role_id') ?></div>
                                <?php endif; ?>
                            </div>

                            <?php if ($editUser['is_admin'] ?? false): ?>
                                <div class="alert alert-warning">
                                    <div class="alert-icon">
                                        <i class="bi bi-shield-exclamation"></i>
                                    </div>
                                    <div class="alert-body">
                                        <h4 class="alert-title">Protected Account</h4>
                                        <p class="alert-message">This is an administrator account and cannot be modified for security reasons.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>

                <!-- Right Column: Sidebar -->
                <aside class="content-sidebar">
                    <!-- Account Information Card -->
                    <?php if ($isEdit): ?>
                        <section class="card card-sidebar">
                            <div class="card-header">
                                <h3 class="card-title">Account Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="info-grid">
                                    <div class="info-row">
                                        <span class="info-label">User ID</span>
                                        <span class="info-value"><?= e($editUser['id'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Status</span>
                                        <span class="info-value">
                                            <span class="badge badge-<?= ($editUser['status'] ?? 'active') === 'active' ? 'success' : 'error' ?>">
                                                <?= ucfirst($editUser['status'] ?? 'active') ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Admin</span>
                                        <span class="info-value">
                                            <?php if ($editUser['is_admin'] ?? false): ?>
                                                <span class="badge badge-warning">Yes</span>
                                            <?php else: ?>
                                                <span class="badge badge-info">No</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Created</span>
                                        <span class="info-value"><?= e($editUser['created_at'] ?? 'N/A') ?></span>
                                    </div>
                                    <?php if ($editUser['updated_at'] ?? null): ?>
                                        <div class="info-row">
                                            <span class="info-label">Updated</span>
                                            <span class="info-value"><?= e($editUser['updated_at']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Help & Information Card -->
                    <section class="card card-sidebar">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-info-circle"></i> Help
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="help-item">
                                <h4 class="help-title">
                                    <i class="bi bi-person-badge"></i> User Roles
                                </h4>
                                <p class="help-text">Each user must be assigned a role that determines their permissions and access level across the system.</p>
                            </div>
                            <div class="help-item">
                                <h4 class="help-title">
                                    <i class="bi bi-shield-lock"></i> Password Security
                                </h4>
                                <p class="help-text">Use a strong password with at least 8 characters for better security.</p>
                            </div>
                            <div class="help-item">
                                <h4 class="help-title">
                                    <i class="bi bi-globe"></i> Timezone
                                </h4>
                                <p class="help-text">The timezone is used for displaying dates and times accurately.</p>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
        </form>
    </main>
</div>

<style>
:root {
    --color-primary: #8B1956;
    --color-primary-dark: #6F123F;
    --color-primary-light: #F0DCE5;
    --color-text: #161B22;
    --color-text-secondary: #626F86;
    --color-border: #DFE1E6;
    --color-background: #F7F8FA;
    --color-white: #FFFFFF;
    --color-error: #ED3C32;
    --color-warning: #E77817;
    --color-success: #216E4E;
    --color-info: #0052CC;
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ============================================
   MAIN WRAPPER
   ============================================ */
.admin-user-form-wrapper {
    background-color: var(--color-white);
    min-height: 100vh;
}

/* ============================================
   BREADCRUMB NAVIGATION
   ============================================ */
.breadcrumb-nav {
    background-color: var(--color-white);
    padding: 10px 20px;
    border-bottom: 1px solid var(--color-border);
    font-size: 12px;
    color: var(--color-text-secondary);
    display: flex;
    align-items: center;
    gap: 0;
}

.breadcrumb-link {
    color: var(--color-primary);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color var(--transition);
    font-weight: 500;
}

.breadcrumb-link:hover {
    color: var(--color-primary-dark);
    text-decoration: underline;
}

.breadcrumb-separator {
    color: var(--color-border);
    margin: 0 8px;
}

.breadcrumb-current {
    color: var(--color-text);
    font-weight: 600;
}

/* ============================================
   PAGE HEADER
   ============================================ */
.page-header {
    background-color: var(--color-white);
    padding: 20px;
    border-bottom: 1px solid var(--color-border);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 16px;
    flex: 1;
}

.header-avatar {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(139, 25, 86, 0.12);
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-initials {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-white);
}

.header-info {
    flex: 1;
}

.header-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-text);
    margin: 0 0 4px 0;
    letter-spacing: -0.2px;
}

.header-subtitle {
    font-size: 13px;
    color: var(--color-text-secondary);
    margin: 0;
}

.header-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
    flex-wrap: wrap;
}

.meta-email {
    font-size: 12px;
    color: var(--color-text-secondary);
    display: flex;
    align-items: center;
    gap: 4px;
}

.header-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 4px;
}

.badge-role,
.badge-admin {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-role {
    background-color: rgba(139, 25, 86, 0.1);
    color: var(--color-primary);
}

.badge-admin {
    background-color: rgba(231, 120, 23, 0.1);
    color: var(--color-warning);
}

.header-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* ============================================
   BUTTONS
   ============================================ */
.btn {
    padding: 8px 16px;
    border-radius: 6px;
    border: 1px solid var(--color-border);
    background-color: var(--color-white);
    color: var(--color-text);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition);
    font-family: inherit;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.btn-primary {
    background-color: var(--color-primary) !important;
    color: var(--color-white) !important;
    border-color: var(--color-primary) !important;
    font-weight: 600 !important;
    text-decoration: none !important;
}

.btn-primary:hover {
    background-color: var(--color-primary-dark) !important;
    color: var(--color-white) !important;
}

.btn-primary i,
.btn-primary svg {
    color: var(--color-white) !important;
    fill: var(--color-white) !important;
}

.btn-secondary {
    background-color: var(--color-white) !important;
    color: var(--color-text) !important;
    border-color: var(--color-border) !important;
}

.btn-secondary:hover {
    background-color: var(--color-background) !important;
    color: var(--color-text) !important;
}

/* ============================================
   PAGE BODY
   ============================================ */
.page-body {
    padding: 20px;
    background-color: var(--color-background);
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 16px;
}

.content-main {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.content-sidebar {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* ============================================
   CARDS
   ============================================ */
.card {
    background-color: var(--color-white);
    border: 1px solid var(--color-border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: all var(--transition);
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.card-header {
    padding: 16px 18px;
    border-bottom: 1px solid var(--color-border);
    background-color: var(--color-white);
}

.card-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--color-text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.card-title i {
    font-size: 16px;
    color: var(--color-primary);
}

.card-body {
    padding: 16px 18px;
}

/* ============================================
   FORM ELEMENTS
   ============================================ */
.form-group {
    display: flex;
    flex-direction: column;
}

.form-group-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 14px;
}

.form-group-row .form-group {
    margin-bottom: 0;
}

.form-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text);
    margin-bottom: 6px;
    display: block;
}

.required {
    color: var(--color-error);
}

.form-input,
.form-select {
    border: 1px solid var(--color-border);
    border-radius: 4px;
    padding: 8px 10px;
    font-size: 13px;
    color: var(--color-text);
    background-color: var(--color-white);
    transition: all var(--transition);
    font-family: inherit;
}

.form-input:focus,
.form-select:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.1);
    outline: none;
}

.form-input:disabled,
.form-select:disabled {
    background-color: var(--color-background);
    color: var(--color-text-secondary);
    cursor: not-allowed;
    opacity: 0.6;
}

.form-input.error,
.form-select.error {
    border-color: var(--color-error);
    background-color: rgba(237, 60, 50, 0.05);
}

.form-error {
    font-size: 11px;
    color: var(--color-error);
    margin-top: 4px;
    font-weight: 500;
}

.form-hint {
    font-size: 11px;
    color: var(--color-text-secondary);
    margin-top: 4px;
    font-style: italic;
}

/* ============================================
   ALERTS
   ============================================ */
.alert {
    padding: 12px;
    border-radius: 4px;
    display: flex;
    gap: 10px;
    margin-top: 12px;
}

.alert-warning {
    background-color: rgba(231, 120, 23, 0.1);
    border: 1px solid rgba(231, 120, 23, 0.3);
}

.alert-icon {
    color: var(--color-warning);
    font-size: 16px;
    flex-shrink: 0;
    margin-top: 1px;
}

.alert-body {
    flex: 1;
}

.alert-title {
    font-size: 12px;
    font-weight: 600;
    color: var(--color-warning);
    margin: 0 0 2px 0;
}

.alert-message {
    font-size: 11px;
    color: var(--color-text-secondary);
    margin: 0;
    line-height: 1.4;
}

/* ============================================
   SIDEBAR INFO
   ============================================ */
.info-grid {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--color-border);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--color-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.info-value {
    font-size: 12px;
    color: var(--color-text);
    font-weight: 500;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success {
    background-color: rgba(33, 110, 78, 0.1);
    color: var(--color-success);
}

.badge-error {
    background-color: rgba(237, 60, 50, 0.1);
    color: var(--color-error);
}

.badge-warning {
    background-color: rgba(231, 120, 23, 0.1);
    color: var(--color-warning);
}

.badge-info {
    background-color: rgba(139, 25, 86, 0.1);
    color: var(--color-primary);
}

/* ============================================
   HELP SECTION
   ============================================ */
.help-item {
    margin-bottom: 12px;
}

.help-item:last-child {
    margin-bottom: 0;
}

.help-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--color-text);
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.help-title i {
    font-size: 13px;
    color: var(--color-primary);
}

.help-text {
    font-size: 11px;
    color: var(--color-text-secondary);
    margin: 0;
    line-height: 1.4;
}

/* ============================================
   RESPONSIVE DESIGN
   ============================================ */
@media (max-width: 1200px) {
    .content-grid {
        grid-template-columns: 1fr;
    }

    .content-sidebar {
        order: 2;
    }
}

@media (max-width: 1024px) {
    .page-header {
        padding: 16px;
    }

    .header-content {
        flex-direction: column;
        gap: 16px;
    }

    .header-left {
        flex-direction: column;
        gap: 12px;
        width: 100%;
    }

    .header-actions {
        width: 100%;
    }

    .page-body {
        padding: 16px;
    }

    .form-group-row {
        grid-template-columns: 1fr;
        gap: 12px;
    }
}

@media (max-width: 768px) {
    .breadcrumb-nav {
        padding: 8px 12px;
        font-size: 11px;
    }

    .page-header {
        padding: 14px 12px;
    }

    .header-title {
        font-size: 18px;
    }

    .header-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        padding: 7px 14px;
        font-size: 12px;
    }

    .page-body {
        padding: 12px;
    }

    .header-avatar {
        width: 56px;
        height: 56px;
    }

    .avatar-initials {
        font-size: 20px;
    }

    .card-header {
        padding: 12px 14px;
    }

    .card-body {
        padding: 14px;
    }
}

@media (max-width: 480px) {
    .breadcrumb-nav {
        padding: 6px 10px;
        font-size: 10px;
    }

    .page-header {
        padding: 12px 10px;
    }

    .header-title {
        font-size: 16px;
    }

    .header-left {
        gap: 10px;
    }

    .page-body {
        padding: 10px;
    }

    .card-header {
        padding: 10px 12px;
    }

    .card-body {
        padding: 12px;
    }

    .header-avatar {
        width: 48px;
        height: 48px;
    }

    .avatar-initials {
        font-size: 18px;
    }

    .form-group-row {
        gap: 10px;
        margin-bottom: 12px;
    }

    .form-input,
    .form-select {
        padding: 7px 9px;
        font-size: 12px;
    }

    .info-row {
        padding: 8px 0;
    }

    .info-label {
        font-size: 9px;
    }

    .info-value {
        font-size: 11px;
    }
}
</style>

<?php \App\Core\View::endSection(); ?>
