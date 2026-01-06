<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="admin-issue-types-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="admin-breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-gear"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Issue Types</span>
    </div>

    <!-- Page Header -->
    <div class="admin-page-header">
        <div class="header-left">
            <h1 class="page-title">Issue Types</h1>
            <p class="page-subtitle">Manage issue types used across all projects</p>
        </div>
        <div class="header-actions">
            <button type="button" class="btn btn-add-type" data-bs-toggle="modal" data-bs-target="#issueTypeModal">
                <i class="bi bi-plus-lg"></i> Add Issue Type
            </button>
        </div>
    </div>

    <!-- Quick Stats Bar -->
    <div class="quick-stats-bar">
        <div class="stat-item">
            <span class="stat-value"><?= count($issueTypes ?? []) ?></span>
            <span class="stat-label">Total Types</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?= array_sum(array_column($issueTypes ?? [], 'issue_count')) ?></span>
            <span class="stat-label">Issues Created</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?= count(array_filter($issueTypes ?? [], fn($t) => $t['is_subtask'])) ?></span>
            <span class="stat-label">Subtask Types</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-content-area">
        <?php if (empty($issueTypes)): ?>
            <!-- Empty State -->
            <div class="empty-state-card">
                <div class="empty-icon">📋</div>
                <h3 class="empty-title">No Issue Types Yet</h3>
                <p class="empty-text">Get started by creating your first issue type</p>
                <button type="button" class="btn btn-add-type" data-bs-toggle="modal" data-bs-target="#issueTypeModal">
                    <i class="bi bi-plus-lg"></i> Create First Issue Type
                </button>
            </div>
        <?php else: ?>
            <!-- Issue Types Grid -->
            <div class="issue-types-grid">
                <?php foreach ($issueTypes as $type): ?>
                    <div class="issue-type-card">
                        <!-- Card Header with Icon & Color -->
                        <div class="card-header-top">
                            <div class="icon-badge" style="background-color: <?= e($type['color'] ?? '#4A90D9') ?>;">
                                <i class="bi bi-<?= e($type['icon'] ?? 'circle') ?>"></i>
                            </div>
                            <div class="type-name-section">
                                <h3 class="type-name"><?= e($type['name']) ?></h3>
                                <?php if ($type['is_subtask']): ?>
                                    <span class="subtask-badge">Subtask</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body-section">
                            <!-- Description -->
                            <p class="type-description">
                                <?php if ($type['description']): ?>
                                    <?= e($type['description']) ?>
                                <?php else: ?>
                                    <span class="text-empty">No description provided</span>
                                <?php endif; ?>
                            </p>

                            <!-- Metadata -->
                            <div class="card-metadata">
                                <div class="meta-item">
                                    <span class="meta-label">Issues</span>
                                    <span class="meta-value"><?= $type['issue_count'] ?? 0 ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Color</span>
                                    <div class="color-indicator" style="background-color: <?= e($type['color'] ?? '#4A90D9') ?>;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="card-actions">
                            <button type="button" class="action-btn edit-btn" 
                                    data-bs-toggle="modal" data-bs-target="#issueTypeModal"
                                    onclick="editIssueType(<?= htmlspecialchars(json_encode($type)) ?>)">
                                <i class="bi bi-pencil"></i>
                                <span>Edit</span>
                            </button>
                            <form action="<?= url('/admin/issue-types/' . $type['id']) ?>" method="POST" class="delete-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="action-btn delete-btn"
                                        onclick="return confirm('Delete this issue type? Issues of this type will remain but the type definition will be removed.');">
                                    <i class="bi bi-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Issue Type Modal -->
<div class="modal fade" id="issueTypeModal" tabindex="-1" aria-labelledby="issueTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="issueTypeModalLabel">Add Issue Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <form id="issueTypeForm" method="POST" action="<?= url('/admin/issue-types') ?>">
                <?= csrf_field() ?>
                <input type="hidden" id="issueTypeId" name="id">
                <input type="hidden" id="issueTypeMethod" name="_method" value="POST">

                <div class="modal-body">
                    <!-- Type Name -->
                    <div class="form-group">
                        <label for="issueTypeName" class="form-label">Type Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="issueTypeName" name="name" 
                               required maxlength="50" placeholder="e.g., Bug, Feature, Task">
                        <small class="form-helper">The name of this issue type (max 50 characters)</small>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="issueTypeDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="issueTypeDescription" name="description" 
                                  rows="3" maxlength="500" placeholder="Describe what this issue type is used for..."></textarea>
                        <small class="form-helper">Optional description (max 500 characters)</small>
                    </div>

                    <!-- Icon & Color Row -->
                    <div class="form-row">
                        <!-- Icon Selection -->
                        <div class="form-column">
                            <label for="issueTypeIcon" class="form-label">Icon</label>
                            <div class="icon-input-group">
                                <div class="icon-preview-box">
                                    <i id="iconPreview" class="bi bi-circle"></i>
                                </div>
                                <input type="text" class="form-control" id="issueTypeIcon" name="icon" 
                                       placeholder="circle" maxlength="50" onchange="updateIconPreview()">
                            </div>
                            <small class="form-helper">Bootstrap icon name (e.g., circle, square, check)</small>
                        </div>

                        <!-- Color Selection -->
                        <div class="form-column">
                            <label for="issueTypeColor" class="form-label">Color</label>
                            <div class="color-input-group">
                                <input type="color" class="form-control form-control-color" 
                                       id="issueTypeColor" name="color" value="#4A90D9" 
                                       onchange="updateColorText()">
                                <input type="text" class="form-control color-hex-input" 
                                       id="issueTypeColorText" value="#4A90D9" readonly>
                            </div>
                            <small class="form-helper">Choose a color for this type</small>
                        </div>
                    </div>

                    <!-- Subtask Checkbox -->
                    <div class="form-group checkbox-group">
                        <input type="checkbox" class="form-check-input" id="issueTypeSubtask" 
                               name="is_subtask" value="1">
                        <label class="form-check-label" for="issueTypeSubtask">
                            <strong>Subtask Type</strong>
                            <span class="checkbox-helper">This issue type is a subtask and appears in subtask dropdowns</span>
                        </label>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Save Issue Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ============================================
   ISSUE TYPES ADMIN PAGE - ENTERPRISE DESIGN
   ============================================ */

:root {
    --jira-blue: #8B1956 !important;
    --jira-blue-dark: #6F123F !important;
    --jira-dark: #161B22 !important;
    --jira-gray: #626F86 !important;
    --jira-light: #F7F8FA !important;
    --jira-border: #DFE1E6 !important;
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.admin-issue-types-wrapper {
    background-color: var(--jira-light);
    min-height: 100vh;
}

/* Breadcrumb Navigation */
.admin-breadcrumb {
    background-color: white;
    padding: 12px 32px;
    border-bottom: 1px solid var(--jira-border);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.breadcrumb-link {
    color: var(--jira-blue);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color var(--transition);
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
.admin-page-header {
    background-color: white;
    padding: 32px;
    border-bottom: 1px solid var(--jira-border);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    color: var(--jira-dark);
    margin: 0 0 8px 0;
}

.page-subtitle {
    font-size: 15px;
    color: var(--jira-gray);
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 12px;
}

.btn-add-type {
    background-color: var(--jira-blue);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all var(--transition);
    white-space: nowrap;
}

.btn-add-type:hover {
    background-color: var(--jira-blue-dark);
    box-shadow: 0 4px 12px rgba(139, 25, 86, 0.15);
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}

/* Quick Stats Bar */
.quick-stats-bar {
    background-color: white;
    padding: 20px 32px;
    border-bottom: 1px solid var(--jira-border);
    display: flex;
    gap: 32px;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--jira-blue);
}

.stat-label {
    font-size: 13px;
    color: var(--jira-gray);
    font-weight: 500;
}

/* Main Content Area */
.admin-content-area {
    padding: 32px;
}

/* Empty State */
.empty-state-card {
    background-color: white;
    border: 2px dashed var(--jira-border);
    border-radius: 12px;
    padding: 60px 32px;
    text-align: center;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.4;
}

.empty-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--jira-dark);
    margin-bottom: 8px;
}

.empty-text {
    font-size: 15px;
    color: var(--jira-gray);
    margin-bottom: 24px;
}

/* Issue Types Grid */
.issue-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
}

.issue-type-card {
    background-color: white;
    border: 1px solid var(--jira-border);
    border-radius: 12px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    transition: all var(--transition);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.issue-type-card:hover {
    border-color: var(--jira-blue);
    box-shadow: 0 4px 12px rgba(139, 25, 86, 0.1);
    transform: translateY(-2px);
}

/* Card Header */
.card-header-top {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
}

.icon-badge {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
}

.type-name-section {
    flex: 1;
}

.type-name {
    font-size: 18px;
    font-weight: 700;
    color: var(--jira-dark);
    margin: 0 0 8px 0;
}

.subtask-badge {
    display: inline-block;
    background-color: #E8F2FF;
    color: #0052CC;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

/* Card Body */
.card-body-section {
    flex: 1;
    margin-bottom: 16px;
}

.type-description {
    font-size: 14px;
    color: var(--jira-gray);
    margin: 0 0 16px 0;
    line-height: 1.5;
}

.text-empty {
    color: #B6C2CF;
    font-style: italic;
}

/* Card Metadata */
.card-metadata {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    padding: 16px 0;
    border-top: 1px solid var(--jira-border);
    border-bottom: 1px solid var(--jira-border);
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.meta-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--jira-gray);
    text-transform: uppercase;
}

.meta-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--jira-dark);
}

.color-indicator {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 2px solid var(--jira-border);
}

/* Card Actions */
.card-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 16px;
}

.action-btn {
    padding: 10px 16px;
    border-radius: 6px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all var(--transition);
}

.edit-btn {
    background-color: #E8F2FF;
    color: #0052CC;
}

.edit-btn:hover {
    background-color: #D0E5FF;
    transform: translateY(-1px);
}

.delete-btn {
    background-color: #FFECEB;
    color: #AE2A19;
}

.delete-form {
    display: contents;
}

.delete-btn:hover {
    background-color: #FFD5D0;
    transform: translateY(-1px);
}

/* Modal Styling */
.modal-content {
    border: 1px solid var(--jira-border);
    border-radius: 12px;
    box-shadow: 0 12px 24px rgba(9, 30, 66, 0.15);
}

.modal-header {
    background-color: white;
    border-bottom: 1px solid var(--jira-border);
    padding: 24px;
}

.modal-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--jira-dark);
}

.modal-body {
    padding: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--jira-dark);
    margin-bottom: 8px;
    display: block;
}

.required {
    color: #AE2A19;
}

.form-control {
    border: 1px solid var(--jira-border);
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 14px;
    color: var(--jira-dark);
    transition: all var(--transition);
}

.form-control:focus {
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.1);
    outline: none;
}

.form-helper {
    display: block;
    font-size: 12px;
    color: var(--jira-gray);
    margin-top: 6px;
}

/* Icon Input Group */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-column {
    display: flex;
    flex-direction: column;
}

.icon-input-group {
    display: flex;
    gap: 12px;
}

.icon-preview-box {
    width: 44px;
    height: 44px;
    border: 1px solid var(--jira-border);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.icon-input-group .form-control {
    flex: 1;
}

/* Color Input Group */
.color-input-group {
    display: flex;
    gap: 12px;
}

.form-control-color {
    width: 44px !important;
    height: 44px;
    padding: 4px !important;
    cursor: pointer;
    flex-shrink: 0;
}

.color-hex-input {
    flex: 1;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
}

/* Checkbox Group */
.checkbox-group {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.form-check-input {
    margin-top: 4px;
    width: 20px;
    height: 20px;
    border: 1px solid var(--jira-border);
    cursor: pointer;
    accent-color: var(--jira-blue);
}

.form-check-label {
    display: flex;
    flex-direction: column;
    gap: 4px;
    cursor: pointer;
}

.checkbox-helper {
    display: block;
    font-size: 12px;
    color: var(--jira-gray);
    font-weight: 400;
}

/* Modal Footer */
.modal-footer {
    border-top: 1px solid var(--jira-border);
    padding: 16px 24px;
    background-color: #F7F8FA;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-secondary {
    background-color: white;
    color: var(--jira-dark);
    border: 1px solid var(--jira-border);
}

.btn-secondary:hover {
    background-color: #F7F8FA;
    border-color: var(--jira-gray);
}

.btn-primary {
    background-color: var(--jira-blue);
    color: white;
}

.btn-primary:hover {
    background-color: var(--jira-blue-dark);
    box-shadow: 0 4px 12px rgba(139, 25, 86, 0.15);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .admin-page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .issue-types-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .quick-stats-bar {
        gap: 24px;
    }
}

@media (max-width: 768px) {
    .admin-breadcrumb {
        padding: 12px 16px;
    }

    .admin-page-header {
        padding: 20px 16px;
    }

    .page-title {
        font-size: 24px;
    }

    .admin-content-area {
        padding: 16px;
    }

    .issue-types-grid {
        grid-template-columns: 1fr;
    }

    .card-metadata {
        grid-template-columns: 1fr;
    }

    .card-actions {
        grid-template-columns: 1fr;
    }

    .quick-stats-bar {
        flex-direction: column;
        gap: 16px;
    }

    .modal-body {
        padding: 16px;
    }

    .modal-header {
        padding: 16px;
    }

    .modal-footer {
        padding: 12px 16px;
        flex-direction: column-reverse;
    }

    .btn, .btn-primary, .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .admin-breadcrumb {
        font-size: 12px;
        padding: 10px 12px;
    }

    .admin-page-header {
        padding: 16px 12px;
    }

    .page-title {
        font-size: 20px;
    }

    .page-subtitle {
        font-size: 13px;
    }

    .admin-content-area {
        padding: 12px;
    }

    .issue-type-card {
        padding: 16px;
    }

    .icon-badge {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }

    .type-name {
        font-size: 16px;
    }
}
</style>

<script>
function updateIconPreview() {
    const iconName = document.getElementById('issueTypeIcon').value || 'circle';
    document.getElementById('iconPreview').className = 'bi bi-' + iconName;
}

function updateColorText() {
    document.getElementById('issueTypeColorText').value = document.getElementById('issueTypeColor').value;
}

function editIssueType(issueType) {
    document.getElementById('issueTypeModalLabel').textContent = 'Edit Issue Type';
    document.getElementById('issueTypeForm').action = '<?= url('/admin/issue-types') ?>/' + issueType.id;
    document.getElementById('issueTypeId').value = issueType.id;
    document.getElementById('issueTypeMethod').value = 'PUT';
    document.getElementById('issueTypeName').value = issueType.name;
    document.getElementById('issueTypeDescription').value = issueType.description || '';
    document.getElementById('issueTypeIcon').value = issueType.icon || 'circle';
    document.getElementById('issueTypeColor').value = issueType.color || '#4A90D9';
    document.getElementById('issueTypeColorText').value = issueType.color || '#4A90D9';
    document.getElementById('issueTypeSubtask').checked = issueType.is_subtask ? true : false;
    updateIconPreview();
}

// Update color text field when color picker changes
document.getElementById('issueTypeColor')?.addEventListener('change', updateColorText);

// Reset form when modal is closed
document.getElementById('issueTypeModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('issueTypeModalLabel').textContent = 'Add Issue Type';
    document.getElementById('issueTypeForm').action = '<?= url('/admin/issue-types') ?>';
    document.getElementById('issueTypeId').value = '';
    document.getElementById('issueTypeMethod').value = 'POST';
    document.getElementById('issueTypeForm').reset();
    document.getElementById('issueTypeColor').value = '#4A90D9';
    document.getElementById('issueTypeColorText').value = '#4A90D9';
    updateIconPreview();
});
</script>

<?php \App\Core\View::endsection(); ?>
