<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="project-settings-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="settings-breadcrumb">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Settings</span>
    </div>

    <!-- Page Header -->
    <div class="settings-header">
        <div class="settings-header-left">
            <h1 class="settings-title">Project Settings</h1>
            <p class="settings-subtitle">Manage project details, access, and configuration</p>
        </div>
    </div>

    <!-- Settings Container -->
    <div class="settings-container">
        <!-- Sidebar Navigation -->
        <div class="settings-sidebar">
            <nav class="settings-nav">
                <a href="#details" class="settings-nav-item active" onclick="showTab('details'); return false;">
                    <i class="bi bi-info-circle"></i>
                    <span>Details</span>
                </a>
                <a href="#access" class="settings-nav-item" onclick="showTab('access'); return false;">
                    <i class="bi bi-shield-lock"></i>
                    <span>Access</span>
                </a>
                <a href="#notifications" class="settings-nav-item" onclick="showTab('notifications'); return false;">
                    <i class="bi bi-bell"></i>
                    <span>Notifications</span>
                </a>
                <a href="#workflows" class="settings-nav-item" onclick="showTab('workflows'); return false;">
                    <i class="bi bi-diagram-3"></i>
                    <span>Workflows</span>
                </a>
                <?php if (can('delete-project', $project['id'])): ?>
                <a href="#danger" class="settings-nav-item danger" onclick="showTab('danger'); return false;">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Danger Zone</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Settings Content -->
        <div class="settings-content">
            <!-- Details Tab -->
            <div id="details-tab" class="settings-tab active">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div>
                            <h3 class="settings-card-title">Project Details</h3>
                            <p class="settings-card-description">Basic information about your project</p>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <form action="<?= url("/projects/{$project['key']}/settings") ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <!-- Project Avatar & Name -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <h4>Basic Information</h4>
                                </div>
                                <div class="form-grid-2">
                                    <div class="avatar-section">
                                        <div class="avatar-preview">
                                            <?php if ($project['avatar'] ?? null): ?>
                                            <img src="<?= e($project['avatar']) ?>" alt="Project avatar">
                                            <?php else: ?>
                                            <div class="avatar-gradient">
                                                <?= strtoupper(substr($project['key'], 0, 1)) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Project Avatar</label>
                                            <input type="file" class="form-control form-control-sm" name="avatar" accept="image/*">
                                            <p class="form-text">PNG, JPG or GIF (max 2MB)</p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="form-group">
                                            <label class="form-label required">Project Name</label>
                                            <input type="text" class="form-control <?= has_error('name') ? 'is-invalid' : '' ?>" 
                                                   name="name" value="<?= e($project['name']) ?>" required>
                                            <?php if (has_error('name')): ?>
                                            <div class="invalid-feedback"><?= e(error('name')) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Project Key</label>
                                            <input type="text" class="form-control" value="<?= e($project['key']) ?>" disabled>
                                            <p class="form-text">Key cannot be changed after creation</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <h4>Description</h4>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" name="description" rows="6"><?= e($project['description']) ?></textarea>
                                    <p class="form-text">Describe what your project is about</p>
                                </div>
                            </div>

                            <!-- Category & Lead -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <h4>Organization</h4>
                                </div>
                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label">Category</label>
                                        <select class="form-control" name="category_id">
                                            <option value="">None</option>
                                            <?php foreach ($categories ?? [] as $category): ?>
                                            <option value="<?= e($category['id']) ?>" <?= $project['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                                <?= e($category['name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Project Lead</label>
                                        <select class="form-control" name="lead_id">
                                            <option value="">Unassigned</option>
                                            <?php foreach ($users ?? [] as $u): ?>
                                            <option value="<?= e($u['id']) ?>" <?= $project['lead_id'] == $u['id'] ? 'selected' : '' ?>>
                                                <?= e($u['display_name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Project URL -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <h4>External Links</h4>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Project URL</label>
                                    <input type="url" class="form-control" name="url" value="<?= e($project['url'] ?? '') ?>" placeholder="https://example.com/project">
                                    <p class="form-text">Link to external project documentation or repository</p>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Access Tab -->
            <div id="access-tab" class="settings-tab">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div>
                            <h3 class="settings-card-title">Access Settings</h3>
                            <p class="settings-card-description">Control who can access this project</p>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <form action="<?= url("/projects/{$project['key']}/settings/access") ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <!-- Visibility -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <h4>Project Visibility</h4>
                                    <p>Choose who can see this project</p>
                                </div>
                                <div class="visibility-options">
                                    <label class="visibility-option">
                                        <input type="radio" name="is_private" value="0" <?= !$project['is_private'] ? 'checked' : '' ?>>
                                        <div class="visibility-option-content">
                                            <div class="visibility-option-title">
                                                <i class="bi bi-globe"></i> Public
                                            </div>
                                            <p class="visibility-option-description">Anyone in the organization can view</p>
                                        </div>
                                    </label>
                                    <label class="visibility-option">
                                        <input type="radio" name="is_private" value="1" <?= $project['is_private'] ? 'checked' : '' ?>>
                                        <div class="visibility-option-content">
                                            <div class="visibility-option-title">
                                                <i class="bi bi-lock"></i> Private
                                            </div>
                                            <p class="visibility-option-description">Only members can view and access</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Default Assignee -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <h4>Default Settings</h4>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Default Assignee for New Issues</label>
                                    <select class="form-control" name="default_assignee">
                                        <option value="unassigned" <?= ($project['default_assignee'] ?? '') === 'unassigned' ? 'selected' : '' ?>>
                                            Unassigned
                                        </option>
                                        <option value="project_lead" <?= ($project['default_assignee'] ?? '') === 'project_lead' ? 'selected' : '' ?>>
                                            Project Lead
                                        </option>
                                    </select>
                                    <p class="form-text">Issues will be auto-assigned to this role</p>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div id="notifications-tab" class="settings-tab">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div>
                            <h3 class="settings-card-title">Notification Settings</h3>
                            <p class="settings-card-description">Configure how team members are notified</p>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <form action="<?= url("/projects/{$project['key']}/settings/notifications") ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <!-- Email Notifications -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <h4>Email Notifications</h4>
                                    <p>When should team members receive email notifications?</p>
                                </div>
                                <div class="checkbox-group">
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="notify_assignee" value="1" <?= $project['settings']['notify_assignee'] ?? true ? 'checked' : '' ?>>
                                        <div class="checkbox-content">
                                            <span class="checkbox-label">Notify assignee on issue assignment</span>
                                            <p class="checkbox-description">Send email when assigned to an issue</p>
                                        </div>
                                    </label>

                                    <label class="checkbox-item">
                                        <input type="checkbox" name="notify_reporter" value="1" <?= $project['settings']['notify_reporter'] ?? true ? 'checked' : '' ?>>
                                        <div class="checkbox-content">
                                            <span class="checkbox-label">Notify reporter on updates</span>
                                            <p class="checkbox-description">Send email when their issue is updated</p>
                                        </div>
                                    </label>

                                    <label class="checkbox-item">
                                        <input type="checkbox" name="notify_watchers" value="1" <?= $project['settings']['notify_watchers'] ?? true ? 'checked' : '' ?>>
                                        <div class="checkbox-content">
                                            <span class="checkbox-label">Notify watchers on updates</span>
                                            <p class="checkbox-description">Send email to all people watching the issue</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Workflows Tab -->
            <div id="workflows-tab" class="settings-tab">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div>
                            <h3 class="settings-card-title">Workflows</h3>
                            <p class="settings-card-description">Configure issue workflows and status transitions</p>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <div class="workflow-info">
                            <i class="bi bi-info-circle"></i>
                            <p>Manage how issues flow through your project with custom statuses and transitions.</p>
                        </div>
                        <a href="<?= url("/projects/{$project['key']}/workflows") ?>" class="btn btn-outline-primary mt-3">
                            <i class="bi bi-diagram-3"></i> Manage Workflows
                        </a>
                    </div>
                </div>
            </div>

            <!-- Danger Zone Tab -->
            <?php if (can('delete-project', $project['id'])): ?>
            <div id="danger-tab" class="settings-tab">
                <div class="settings-card danger-zone">
                    <div class="settings-card-header danger">
                        <div>
                            <h3 class="settings-card-title">
                                <i class="bi bi-exclamation-triangle"></i> Danger Zone
                            </h3>
                            <p class="settings-card-description">Irreversible and destructive actions</p>
                        </div>
                    </div>
                    <div class="settings-card-body">
                        <!-- Archive Section -->
                        <div class="danger-action">
                            <div class="danger-action-content">
                                <h4>Archive Project</h4>
                                <p>Archived projects are read-only and hidden from the main list. You can unarchive later.</p>
                            </div>
                            <?php if ($project['is_archived'] ?? false): ?>
                            <form action="<?= url("/projects/{$project['key']}/unarchive") ?>" method="POST" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-arrow-counterclockwise"></i> Unarchive Project
                                </button>
                            </form>
                            <?php else: ?>
                            <form action="<?= url("/projects/{$project['key']}/archive") ?>" method="POST" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Archive this project? You can unarchive it later.');">
                                    <i class="bi bi-archive"></i> Archive Project
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>

                        <hr class="danger-divider">

                        <!-- Delete Section -->
                        <div class="danger-action">
                            <div class="danger-action-content">
                                <h4>Delete Project</h4>
                                <p>Once deleted, all issues, data, and history will be permanently removed. This cannot be undone.</p>
                            </div>
                            <button type="button" onclick="openDeleteModal()" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Delete Project
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Project Modal -->
<div id="deleteProjectModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header danger">
            <h4 class="modal-title">
                <i class="bi bi-exclamation-triangle"></i> Delete Project
            </h4>
            <button type="button" class="modal-close" onclick="closeDeleteModal()">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Warning:</strong> This action cannot be undone. All data will be permanently deleted.
            </div>
            <p>Type <strong><?= e($project['key']) ?></strong> to confirm deletion:</p>
            <form action="<?= url("/projects/{$project['key']}") ?>" method="POST" id="deleteProjectForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <input type="text" id="confirmProjectKey" class="form-control" placeholder="Project key" required>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" onclick="closeDeleteModal()">
                Cancel
            </button>
            <button type="submit" form="deleteProjectForm" id="confirmDeleteBtn" class="btn btn-danger" disabled>
                Delete Project
            </button>
        </div>
    </div>
</div>

<style>
/* Project Settings Wrapper */
.project-settings-wrapper {
    padding: 2rem;
    background: var(--bg-secondary);
    min-height: calc(100vh - 56px);
}

/* Breadcrumb */
.settings-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    font-size: 14px;
}

.breadcrumb-link {
    color: var(--jira-blue);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.breadcrumb-link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

.breadcrumb-separator {
    color: var(--text-tertiary);
}

.breadcrumb-current {
    color: var(--text-tertiary);
}

/* Page Header */
.settings-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 32px;
}

.settings-header-left h1,
.settings-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px 0;
    letter-spacing: -0.3px;
}

.settings-subtitle {
    font-size: 15px;
    color: var(--text-tertiary);
    margin: 0;
}

/* Settings Container */
.settings-container {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 24px;
    max-width: 1200px;
}

/* Sidebar Navigation */
.settings-sidebar {
    position: sticky;
    top: 90px;
    height: fit-content;
}

.settings-nav {
    display: flex;
    flex-direction: column;
    gap: 4px;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.settings-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: var(--text-primary);
    text-decoration: none;
    transition: all var(--transition-fast);
    border-left: 3px solid transparent;
    font-size: 14px;
    cursor: pointer;
}

.settings-nav-item i {
    font-size: 16px;
    color: var(--jira-blue);
    flex-shrink: 0;
}

.settings-nav-item:hover {
    background-color: var(--bg-secondary);
}

.settings-nav-item.active {
    background-color: var(--jira-blue-lighter);
    border-left-color: var(--jira-blue);
    color: var(--jira-blue-dark);
    font-weight: 500;
}

.settings-nav-item.danger {
    color: #AE2A19;
}

.settings-nav-item.danger i {
    color: #AE2A19;
}

.settings-nav-item.danger:hover {
    background-color: #FFECEB;
}

/* Settings Content */
.settings-content {
    flex: 1;
}

.settings-tab {
    display: none;
}

.settings-tab.active {
    display: block;
}

/* Settings Card */
.settings-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.settings-card.danger-zone {
    border-color: #FFDBDA;
}

.settings-card-header {
    padding: 20px 24px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.settings-card-header.danger {
    background: #FFECEB;
    border-bottom-color: #FFDBDA;
}

.settings-card-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 4px 0;
}

.settings-card-header.danger .settings-card-title {
    color: #AE2A19;
}

.settings-card-description {
    font-size: 13px;
    color: var(--text-tertiary);
    margin: 0;
}

.settings-card-body {
    padding: 24px;
}

/* Form Sections */
.form-section {
    margin-bottom: 32px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.form-section-header {
    margin-bottom: 16px;
}

.form-section-header h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 4px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-section-header p {
    font-size: 13px;
    color: var(--text-tertiary);
    margin: 0;
}

/* Form Groups */
.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 6px;
}

.form-label.required::after {
    content: ' *';
    color: #AE2A19;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-fast);
    background: var(--bg-primary);
}

.form-control:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.1);
}

.form-control:disabled {
    background: var(--bg-secondary);
    color: var(--text-tertiary);
    cursor: not-allowed;
}

.form-control.is-invalid {
    border-color: #AE2A19;
}

.invalid-feedback {
    color: #AE2A19;
    font-size: 12px;
    margin-top: 4px;
}

.form-text {
    font-size: 12px;
    color: var(--text-tertiary);
    margin-top: 4px;
    margin-bottom: 0;
}

/* Grid Layouts */
.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

@media (max-width: 768px) {
    .form-grid-2 {
        grid-template-columns: 1fr;
    }
}

/* Avatar Section */
.avatar-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px;
    background: var(--bg-secondary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.avatar-preview {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 12px;
    background: var(--bg-tertiary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    color: white;
}

.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-gradient {
    background: linear-gradient(135deg, var(--jira-blue), #003D99);
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Visibility Options */
.visibility-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.visibility-option {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.visibility-option:hover {
    background: var(--bg-secondary);
    border-color: var(--jira-blue);
}

.visibility-option input[type="radio"] {
    margin-top: 4px;
    flex-shrink: 0;
}

.visibility-option-content {
    flex: 1;
}

.visibility-option-title {
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.visibility-option-description {
    font-size: 13px;
    color: var(--text-tertiary);
    margin: 0;
}

/* Checkbox Group */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.checkbox-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.checkbox-item:hover {
    background: var(--bg-secondary);
    border-color: var(--jira-blue);
}

.checkbox-item input[type="checkbox"] {
    margin-top: 2px;
    flex-shrink: 0;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-content {
    flex: 1;
}

.checkbox-label {
    display: block;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.checkbox-description {
    font-size: 13px;
    color: var(--text-tertiary);
    margin: 0;
}

/* Workflow Info */
.workflow-info {
    display: flex;
    gap: 12px;
    padding: 12px 16px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    color: var(--text-secondary);
    font-size: 14px;
}

.workflow-info i {
    color: var(--jira-blue);
    flex-shrink: 0;
}

.workflow-info p {
    margin: 0;
}

/* Danger Zone */
.danger-action {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 16px 0;
}

.danger-action-content {
    flex: 1;
}

.danger-action h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 4px 0;
}

.danger-action p {
    font-size: 13px;
    color: var(--text-tertiary);
    margin: 0;
}

.danger-divider {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 16px 0;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid var(--border-color);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-fast);
    text-decoration: none;
}

.btn-primary {
    background: var(--jira-blue);
    color: white;
}

.btn-primary:hover {
    background: var(--jira-blue-dark);
    transform: translateY(-1px);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-outline-primary {
    background: transparent;
    border: 1px solid var(--jira-blue);
    color: var(--jira-blue);
}

.btn-outline-primary:hover {
    background: var(--jira-blue-lighter);
}

.btn-outline-secondary {
    background: transparent;
    border: 1px solid var(--border-color);
    color: var(--text-primary);
}

.btn-outline-secondary:hover {
    background: var(--bg-secondary);
}

.btn-success {
    background: #36B37E;
    color: white;
}

.btn-success:hover {
    background: #2d8659;
}

.btn-warning {
    background: #FFAB00;
    color: white;
}

.btn-warning:hover {
    background: #E59400;
}

.btn-danger {
    background: #AE2A19;
    color: white;
}

.btn-danger:hover {
    background: #8C2417;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-overlay.show {
    display: flex;
}

.modal-content {
    background: var(--bg-primary);
    border-radius: 8px;
    width: 100%;
    max-width: 500px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.modal-header {
    padding: 20px 24px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-header.danger {
    background: #FFECEB;
    border-bottom-color: #FFDBDA;
}

.modal-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-header.danger .modal-title {
    color: #AE2A19;
}

.modal-close {
    background: transparent;
    border: none;
    font-size: 20px;
    color: var(--text-tertiary);
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: var(--text-primary);
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 12px 24px;
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-color);
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

/* Alert */
.alert {
    padding: 12px 16px;
    border-radius: 6px;
    border: 1px solid;
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    font-size: 14px;
}

.alert i {
    flex-shrink: 0;
}

.alert-danger {
    background: #FFECEB;
    border-color: #FFDBDA;
    color: #AE2A19;
}

/* Responsive */
@media (max-width: 1024px) {
    .settings-container {
        grid-template-columns: 1fr;
    }
    
    .settings-sidebar {
        position: relative;
        top: 0;
    }
    
    .settings-nav {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0;
    }
    
    .settings-nav-item {
        border-left: none;
        border-bottom: 1px solid var(--border-color);
    }
    
    .settings-nav-item.active {
        border-left: none;
        border-bottom-color: var(--jira-blue);
    }
}

@media (max-width: 768px) {
    .project-settings-wrapper {
        padding: 1rem;
    }
    
    .settings-header {
        margin-bottom: 24px;
    }
    
    .settings-title {
        font-size: 1.5rem;
    }
    
    .danger-action {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<script>
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.settings-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Remove active from all nav items
        document.querySelectorAll('.settings-nav-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Show selected tab
        const tab = document.getElementById(tabName + '-tab');
        if (tab) {
            tab.classList.add('active');
        }
        
        // Set active nav item
        const navItem = document.querySelector(`.settings-nav-item[href="#${tabName}"]`);
        if (navItem) {
            navItem.classList.add('active');
        }
    }

    function openDeleteModal() {
        document.getElementById('deleteProjectModal').classList.add('show');
    }

    function closeDeleteModal() {
        document.getElementById('deleteProjectModal').classList.remove('show');
    }

    // Enable delete button only when correct key is typed
    document.getElementById('confirmProjectKey')?.addEventListener('input', function() {
        const btn = document.getElementById('confirmDeleteBtn');
        const projectKey = '<?= e($project['key']) ?>';
        const isCorrect = this.value === projectKey;
        btn.disabled = !isCorrect;
    });

    // Close modal when clicking outside
    document.getElementById('deleteProjectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>

<?php \App\Core\View::endSection(); ?>
