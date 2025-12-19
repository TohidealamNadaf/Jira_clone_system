<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="permissions-wrapper">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav" aria-label="breadcrumb">
        <div class="breadcrumb-item">
            <i class="bi bi-house-door"></i>
            <a href="<?= url('/admin') ?>">Administration</a>
        </div>
        <div class="breadcrumb-separator">/</div>
        <div class="breadcrumb-item active">
            <i class="bi bi-shield-lock"></i>
            Global Permissions
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Global Permissions</h1>
            <p class="page-subtitle">Manage system-wide permissions and roles</p>
        </div>
        <div class="header-right">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                <i class="bi bi-arrow-left"></i>
                Back
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-area">
        <form method="POST" action="<?= url('/admin/global-permissions') ?>" class="permissions-form">
            <?= csrf_token() ?>
            <input type="hidden" name="_method" value="PUT">

            <?php if (empty($grouped)): ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-state-icon">🔐</div>
                    <h3>No permissions found</h3>
                    <p>No global permissions are currently configured in the system.</p>
                </div>
            <?php else: ?>
                <!-- Permission Sections -->
                <div class="permissions-sections">
                    <?php foreach ($grouped as $category => $categoryPermissions): ?>
                        <div class="permission-section">
                            <!-- Section Header -->
                            <div class="section-header">
                                <div class="section-title-wrapper">
                                    <?php
                                    $categoryIcon = match($category ?? 'Other') {
                                        'issues' => 'bi-ticket',
                                        'projects' => 'bi-folder',
                                        'users' => 'bi-people',
                                        'admin' => 'bi-shield-lock',
                                        'reports' => 'bi-graph-up',
                                        'notifications' => 'bi-bell',
                                        default => 'bi-gear'
                                    };
                                    ?>
                                    <i class="bi <?= $categoryIcon ?>"></i>
                                    <h2 class="section-title"><?= htmlspecialchars($category ?? 'Other') ?></h2>
                                </div>
                                <span class="permission-count"><?= count($categoryPermissions) ?> permissions</span>
                            </div>

                            <!-- Permissions Table -->
                            <div class="section-content">
                                <div class="table-wrapper">
                                    <table class="permissions-table">
                                        <thead>
                                            <tr>
                                                <th class="col-permission">Permission</th>
                                                <th class="col-description">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($categoryPermissions as $permission): ?>
                                                <tr class="permission-row">
                                                    <td class="cell-permission">
                                                        <div class="permission-name">
                                                            <code><?= htmlspecialchars($permission['name'] ?? '') ?></code>
                                                        </div>
                                                    </td>
                                                    <td class="cell-description">
                                                        <textarea 
                                                            name="permissions[<?= $permission['id'] ?>][description]"
                                                            class="permission-textarea"
                                                            placeholder="Enter permission description..."
                                                            spellcheck="true"
                                                        ><?= htmlspecialchars($permission['description'] ?? '') ?></textarea>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg"></i>
                        <span>Save Permissions</span>
                    </button>
                    <a href="<?= url('/admin') ?>" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-lg"></i>
                        <span>Cancel</span>
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<style>
:root {
    --jira-blue: #8B1956;
    --jira-blue-dark: #6F123F;
    --jira-blue-light: rgba(139,25,86,0.1);
    --text-primary: #161B22;
    --text-secondary: #626F86;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F8FA;
    --border-color: #DFE1E6;
    --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
    --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.permissions-wrapper {
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 80px);
}

/* Breadcrumb Navigation */
.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    font-size: 13px;
    font-weight: 500;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.breadcrumb-item a {
    color: var(--jira-blue);
    text-decoration: none;
    transition: color var(--transition);
}

.breadcrumb-item a:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: var(--text-secondary);
}

.breadcrumb-separator {
    color: var(--border-color);
    margin: 0 4px;
}

.breadcrumb-item i {
    font-size: 14px;
    opacity: 0.7;
}

/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 24px 32px;
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    gap: 32px;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 8px 0;
    letter-spacing: -0.3px;
}

.page-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
    font-weight: 400;
}

.header-right {
    display: flex;
    gap: 12px;
}

/* Content Area */
.content-area {
    flex: 1;
    background: var(--bg-secondary);
    padding: 32px;
    overflow-y: auto;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 20px;
    text-align: center;
    background: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.empty-state-icon {
    font-size: 56px;
    margin-bottom: 16px;
    opacity: 0.6;
}

.empty-state h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 8px 0;
}

.empty-state p {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
    max-width: 400px;
}

/* Permissions Sections */
.permissions-sections {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.permission-section {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.permission-section:hover {
    box-shadow: var(--shadow-md);
}

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    gap: 16px;
}

.section-title-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.section-title-wrapper i {
    font-size: 20px;
    color: var(--jira-blue);
    flex-shrink: 0;
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    text-transform: capitalize;
}

.permission-count {
    font-size: 12px;
    color: var(--text-secondary);
    background: var(--bg-primary);
    padding: 4px 12px;
    border-radius: 12px;
    white-space: nowrap;
    font-weight: 500;
}

/* Section Content */
.section-content {
    padding: 0;
}

.table-wrapper {
    overflow-x: auto;
}

.permissions-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.permissions-table thead {
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
}

.permissions-table th {
    padding: 12px 24px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-align: left;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.permissions-table tbody tr {
    border-bottom: 1px solid var(--border-color);
    transition: background-color var(--transition);
}

.permissions-table tbody tr:last-child {
    border-bottom: none;
}

.permissions-table tbody tr:hover {
    background-color: var(--bg-secondary);
}

.permissions-table td {
    padding: 20px 24px;
}

.col-permission {
    width: 25%;
    min-width: 180px;
}

.col-description {
    width: 75%;
}

/* Permission Cell */
.cell-permission {
    vertical-align: top;
}

.permission-name code {
    display: inline-block;
    background: var(--jira-blue-light);
    color: var(--jira-blue-dark);
    padding: 6px 12px;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.2px;
    word-break: break-word;
}

/* Description Cell */
.cell-description {
    vertical-align: top;
}

.permission-textarea {
    width: 100%;
    padding: 12px 16px;
    font-size: 14px;
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-family: inherit;
    resize: vertical;
    min-height: 50px;
    max-height: 120px;
    transition: all var(--transition);
}

.permission-textarea:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
    background: var(--bg-primary);
}

.permission-textarea::placeholder {
    color: var(--text-secondary);
    opacity: 0.6;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    justify-content: flex-start;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: all var(--transition);
    text-decoration: none;
    white-space: nowrap;
}

.btn.btn-lg {
    padding: 12px 24px;
    font-size: 15px;
}

.btn-primary {
    background: var(--jira-blue);
    color: white;
}

.btn-primary:hover {
    background: var(--jira-blue-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-secondary {
    background: var(--bg-primary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--bg-secondary);
    border-color: var(--text-secondary);
}

.btn i {
    font-size: 16px;
    flex-shrink: 0;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }

    .header-right {
        justify-content: flex-end;
    }

    .content-area {
        padding: 24px;
    }

    .permissions-sections {
        gap: 24px;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .permission-count {
        align-self: flex-start;
    }
}

@media (max-width: 768px) {
    .breadcrumb-nav {
        flex-wrap: wrap;
        gap: 6px;
        padding: 12px 16px;
    }

    .page-header {
        padding: 20px 16px;
    }

    .page-title {
        font-size: 24px;
    }

    .page-subtitle {
        font-size: 13px;
    }

    .content-area {
        padding: 16px;
    }

    .permissions-sections {
        gap: 16px;
    }

    .section-header {
        padding: 16px 20px;
    }

    .section-title {
        font-size: 14px;
    }

    .permissions-table th,
    .permissions-table td {
        padding: 16px 20px;
    }

    .col-permission {
        width: 35%;
        min-width: 140px;
    }

    .col-description {
        width: 65%;
    }

    .permission-textarea {
        font-size: 14px;
        min-height: 45px;
    }

    .form-actions {
        flex-direction: column;
        gap: 12px;
    }

    .btn,
    .btn.btn-lg {
        width: 100%;
        justify-content: center;
        padding: 12px 16px;
    }
}

@media (max-width: 480px) {
    .breadcrumb-nav {
        font-size: 12px;
    }

    .page-header {
        padding: 16px 12px;
    }

    .page-title {
        font-size: 20px;
    }

    .page-subtitle {
        font-size: 12px;
    }

    .content-area {
        padding: 12px;
    }

    .permissions-sections {
        gap: 12px;
    }

    .section-header {
        padding: 12px 16px;
    }

    .section-title {
        font-size: 13px;
    }

    .section-title-wrapper i {
        font-size: 18px;
    }

    .permission-count {
        font-size: 11px;
        padding: 3px 10px;
    }

    .permissions-table {
        font-size: 12px;
    }

    .permissions-table th,
    .permissions-table td {
        padding: 12px 16px;
    }

    .col-permission {
        width: 40%;
        min-width: 100px;
    }

    .col-description {
        width: 60%;
    }

    .permission-textarea {
        font-size: 13px;
        padding: 10px 12px;
        min-height: 40px;
    }

    .btn,
    .btn.btn-lg {
        font-size: 13px;
        padding: 10px 14px;
    }

    .btn i {
        font-size: 14px;
    }
}

/* Print Styles */
@media print {
    .breadcrumb-nav,
    .page-header,
    .form-actions {
        display: none;
    }

    .content-area {
        background: var(--bg-primary);
        padding: 0;
    }

    .permission-section {
        page-break-inside: avoid;
    }

    .permission-textarea {
        border: none;
        background: transparent;
    }
}
</style>

<?php \App\Core\View::endsection(); ?>
