<?php \App\Core\View:: extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<div class="ps-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <nav class="ps-breadcrumb" aria-label="Page navigation">
        <a href="<?= url('/dashboard') ?>" class="ps-breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="ps-breadcrumb-sep">/</span>
        <a href="<?= url('/projects') ?>" class="ps-breadcrumb-link">Projects</a>
        <span class="ps-breadcrumb-sep">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="ps-breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="ps-breadcrumb-sep">/</span>
        <span class="ps-breadcrumb-current">Settings</span>
    </nav>

    <!-- Page Header -->
    <div class="ps-page-header">
        <div class="ps-header-left">
            <div class="ps-avatar-container">
                <?php if ($project['avatar'] ?? null): ?>
                    <img src="<?= e(avatar($project['avatar'])) ?>" alt="<?= e($project['name']) ?>" class="ps-avatar-img">
                <?php else: ?>
                    <div class="ps-avatar-placeholder">
                        <?= strtoupper(substr($project['key'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ps-header-content">
                <h1 class="ps-header-title">Project Settings</h1>
                <p class="ps-header-subtitle">Manage project details, access, and configuration</p>
            </div>
        </div>
        <div class="ps-header-right">
            <a href="<?= url("/projects/{$project['key']}") ?>" class="ps-action-btn">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="ps-main-container">
        <!-- Sidebar Navigation -->
        <aside class="ps-sidebar">
            <nav class="ps-nav-menu">
                <a href="#details" class="ps-nav-item active" onclick="showTab('details'); return false;"
                    data-section="details">
                    <i class="bi bi-gear"></i>
                    <span>Details</span>
                </a>
                <a href="#access" class="ps-nav-item" onclick="showTab('access'); return false;" data-section="access">
                    <i class="bi bi-shield-check"></i>
                    <span>Access</span>
                </a>
                <a href="#notifications" class="ps-nav-item" onclick="showTab('notifications'); return false;"
                    data-section="notifications">
                    <i class="bi bi-bell"></i>
                    <span>Notifications</span>
                </a>
                <a href="#components" class="ps-nav-item" onclick="showTab('components'); return false;"
                    data-section="components">
                    <i class="bi bi-box"></i>
                    <span>Components</span>
                </a>
                <a href="#versions" class="ps-nav-item" onclick="showTab('versions'); return false;"
                    data-section="versions">
                    <i class="bi bi-tag"></i>
                    <span>Versions</span>
                </a>
                <a href="#workflows" class="ps-nav-item" onclick="showTab('workflows'); return false;"
                    data-section="workflows">
                    <i class="bi bi-diagram-3"></i>
                    <span>Workflows</span>
                </a>
                <?php if (can('delete-project', $project['id'])): ?>
                    <div class="ps-nav-divider"></div>
                    <a href="#danger" class="ps-nav-item ps-nav-danger" onclick="showTab('danger'); return false;"
                        data-section="danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Danger Zone</span>
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Content Area -->
        <main class="ps-content">

            <!-- Details Tab -->
            <div id="details-tab" class="ps-tab-pane active">
                <div class="ps-card">
                    <div class="ps-card-header">
                        <div>
                            <h2 class="ps-card-title">Project Details</h2>
                            <p class="ps-card-subtitle">Basic information and project identity</p>
                        </div>
                    </div>
                    <div class="ps-card-body">
                        <form action="<?= url("/projects/{$project['key']}") ?>" method="POST"
                            enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <!-- Avatar Section -->
                            <div class="ps-form-section">
                                <div class="ps-avatar-editor">
                                    <div class="ps-avatar-box">
                                        <?php if ($project['avatar'] ?? null): ?>
                                            <img src="<?= e(avatar($project['avatar'])) ?>" alt="Project avatar"
                                                class="ps-avatar-preview-img">
                                        <?php else: ?>
                                            <div class="ps-avatar-placeholder-large">
                                                <?= strtoupper(substr($project['key'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ps-avatar-actions">
                                        <label class="ps-btn ps-btn-secondary ps-btn-sm">
                                            <i class="bi bi-camera"></i> Upload Image
                                            <input type="file" class="ps-file-input" name="avatar" accept="image/*"
                                                onchange="previewAvatar(this)">
                                        </label>
                                        <p class="ps-helper-text">JPG or PNG, max 5MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="ps-form-grid">
                                <div class="ps-form-group">
                                    <label class="ps-form-label" for="name">
                                        Project Name <span class="ps-required">*</span>
                                    </label>
                                    <input type="text" id="name" class="ps-form-control" name="name"
                                        value="<?= e($project['name']) ?>" required>
                                    <p class="ps-helper-text">Human-readable name for the project</p>
                                </div>

                                <div class="ps-form-group">
                                    <label class="ps-form-label" for="key">Project Key</label>
                                    <input type="text" id="key" class="ps-form-control"
                                        value="<?= e($project['key']) ?>" disabled>
                                    <p class="ps-helper-text">Unique identifier (cannot be changed)</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="ps-form-group">
                                <label class="ps-form-label" for="description">Description</label>
                                <textarea id="description" class="ps-form-control ps-form-textarea" name="description"
                                    rows="4"
                                    placeholder="Describe your project..."><?= e($project['description']) ?></textarea>
                                <p class="ps-helper-text">Brief overview of the project purpose and goals</p>
                            </div>

                            <!-- Project Settings -->
                            <div class="ps-form-grid">
                                <div class="ps-form-group">
                                    <label class="ps-form-label" for="category">Category</label>
                                    <select id="category" class="ps-form-control" name="category_id">
                                        <option value="">Select a category...</option>
                                        <?php foreach ($categories ?? [] as $category): ?>
                                            <option value="<?= e($category['id']) ?>"
                                                <?= $project['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                                <?= e($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="ps-form-group">
                                    <label class="ps-form-label" for="lead">Project Lead</label>
                                    <select id="lead" class="ps-form-control" name="lead_id">
                                        <option value="">Unassigned</option>
                                        <?php foreach ($users ?? [] as $u): ?>
                                            <option value="<?= e($u['id']) ?>" <?= $project['lead_id'] == $u['id'] ? 'selected' : '' ?>>
                                                <?= e($u['display_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- URL -->
                            <div class="ps-form-group">
                                <label class="ps-form-label" for="url">Project URL</label>
                                <input type="url" id="url" class="ps-form-control" name="url"
                                    value="<?= e($project['url'] ?? '') ?>" placeholder="https://...">
                                <p class="ps-helper-text">External link to project documentation or website</p>
                            </div>

                            <!-- Actions -->
                            <div class="ps-form-actions">
                                <button type="submit" class="ps-btn ps-btn-primary">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Access Tab -->
            <div id="access-tab" class="ps-tab-pane">
                <div class="ps-card">
                    <div class="ps-card-header">
                        <div>
                            <h2 class="ps-card-title">Access & Visibility</h2>
                            <p class="ps-card-subtitle">Control who can view and interact with this project</p>
                        </div>
                    </div>
                    <div class="ps-card-body">
                        <form action="<?= url("/projects/{$project['key']}/settings/access") ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <!-- Visibility Options -->
                            <div class="ps-form-group">
                                <label class="ps-form-label">Project Visibility</label>
                                <div class="ps-radio-group">
                                    <label class="ps-radio-card">
                                        <input type="radio" name="is_private" value="0" <?= !$project['is_private'] ? 'checked' : '' ?>>
                                        <div class="ps-radio-content">
                                            <i class="bi bi-globe"></i>
                                            <div class="ps-radio-text">
                                                <strong>Public</strong>
                                                <p>Everyone in the organization can view</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="ps-radio-card">
                                        <input type="radio" name="is_private" value="1" <?= $project['is_private'] ? 'checked' : '' ?>>
                                        <div class="ps-radio-content">
                                            <i class="bi bi-lock"></i>
                                            <div class="ps-radio-text">
                                                <strong>Private</strong>
                                                <p>Only project members can view</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Default Assignee -->
                            <div class="ps-form-group">
                                <label class="ps-form-label" for="assignee">Default Assignee</label>
                                <select id="assignee" class="ps-form-control" name="default_assignee">
                                    <option value="unassigned" <?= ($project['default_assignee'] ?? '') === 'unassigned' ? 'selected' : '' ?>>Unassigned</option>
                                    <option value="project_lead" <?= ($project['default_assignee'] ?? '') === 'project_lead' ? 'selected' : '' ?>>Project Lead</option>
                                </select>
                                <p class="ps-helper-text">Default assignee for new issues when not specified</p>
                            </div>

                            <!-- Actions -->
                            <div class="ps-form-actions">
                                <button type="submit" class="ps-btn ps-btn-primary">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div id="notifications-tab" class="ps-tab-pane">
                <div class="ps-card">
                    <div class="ps-card-header">
                        <div>
                            <h2 class="ps-card-title">Email Notifications</h2>
                            <p class="ps-card-subtitle">Configure notification preferences for team members</p>
                        </div>
                    </div>
                    <div class="ps-card-body">
                        <form action="<?= url("/projects/{$project['key']}/settings/notifications") ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <!-- Notification Checkboxes -->
                            <div class="ps-checkbox-group">
                                <label class="ps-checkbox-item">
                                    <input type="checkbox" name="notify_assignee" value="1"
                                        <?= $project['settings']['notify_assignee'] ?? true ? 'checked' : '' ?>>
                                    <span class="ps-checkbox-custom"></span>
                                    <div class="ps-checkbox-text">
                                        <strong>Notify Assignee</strong>
                                        <p>Send email when assigned to an issue</p>
                                    </div>
                                </label>
                                <label class="ps-checkbox-item">
                                    <input type="checkbox" name="notify_reporter" value="1"
                                        <?= $project['settings']['notify_reporter'] ?? true ? 'checked' : '' ?>>
                                    <span class="ps-checkbox-custom"></span>
                                    <div class="ps-checkbox-text">
                                        <strong>Notify Reporter</strong>
                                        <p>Send email when issue is updated or resolved</p>
                                    </div>
                                </label>
                                <label class="ps-checkbox-item">
                                    <input type="checkbox" name="notify_watchers" value="1"
                                        <?= $project['settings']['notify_watchers'] ?? true ? 'checked' : '' ?>>
                                    <span class="ps-checkbox-custom"></span>
                                    <div class="ps-checkbox-text">
                                        <strong>Notify Watchers</strong>
                                        <p>Send email to anyone watching the issue</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Actions -->
                            <div class="ps-form-actions">
                                <button type="submit" class="ps-btn ps-btn-primary">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Components Tab -->
            <div id="components-tab" class="ps-tab-pane">
                <div class="ps-card">
                    <div class="ps-card-header">
                        <div>
                            <h2 class="ps-card-title">Components</h2>
                            <p class="ps-card-subtitle">Organize issues into project components</p>
                        </div>
                    </div>
                    <div class="ps-card-body">
                        <div class="ps-empty-state">
                            <i class="bi bi-box"></i>
                            <h3>Components Coming Soon</h3>
                            <p>Organize issues by backend, frontend, API, and more</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Versions Tab -->
            <div id="versions-tab" class="ps-tab-pane">
                <div class="ps-card">
                    <div class="ps-card-header">
                        <div>
                            <h2 class="ps-card-title">Versions</h2>
                            <p class="ps-card-subtitle">Manage product releases and deliveries</p>
                        </div>
                    </div>
                    <div class="ps-card-body">
                        <div class="ps-empty-state">
                            <i class="bi bi-tag"></i>
                            <h3>Versions Coming Soon</h3>
                            <p>Schedule releases and assign issues to versions</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workflows Tab -->
            <div id="workflows-tab" class="ps-tab-pane">
                <div class="ps-card">
                    <div class="ps-card-header">
                        <div>
                            <h2 class="ps-card-title">Workflows</h2>
                            <p class="ps-card-subtitle">Define the lifecycle of your issues</p>
                        </div>
                    </div>
                    <div class="ps-card-body">
                        <div class="ps-info-block">
                            <i class="bi bi-info-circle"></i>
                            <p>Workflows control valid transitions between statuses (To Do → In Progress → Done).
                                Customize to match your team's process.</p>
                        </div>
                        <div class="ps-mt-4">
                            <a href="<?= url("/projects/{$project['key']}/workflows") ?>"
                                class="ps-btn ps-btn-secondary">
                                <i class="bi bi-diagram-3"></i> Configure Workflows
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone Tab -->
            <?php if (can('delete-project', $project['id'])): ?>
                <div id="danger-tab" class="ps-tab-pane">
                    <div class="ps-card ps-card-danger">
                        <div class="ps-card-header ps-card-header-danger">
                            <div>
                                <h2 class="ps-card-title">Danger Zone</h2>
                                <p class="ps-card-subtitle">Irreversible actions for this project</p>
                            </div>
                        </div>
                        <div class="ps-card-body">
                            <!-- Archive Section -->
                            <div class="ps-danger-item">
                                <div class="ps-danger-info">
                                    <h3>Archive Project</h3>
                                    <p>Archived projects are read-only and hidden from lists. You can unarchive later.</p>
                                </div>
                                <?php if ($project['is_archived'] ?? false): ?>
                                    <form action="<?= url("/projects/{$project['key']}/unarchive") ?>" method="POST"
                                        style="margin: 0;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="ps-btn ps-btn-success">Unarchive Project</button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= url("/projects/{$project['key']}/archive") ?>" method="POST"
                                        style="margin: 0;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="ps-btn ps-btn-warning"
                                            onclick="return confirm('Are you sure you want to archive this project?');">
                                            Archive Project
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>

                            <!-- Delete Section -->
                            <div class="ps-danger-item">
                                <div class="ps-danger-info">
                                    <h3 class="ps-text-danger">Delete Project</h3>
                                    <p>Permanently remove this project and all data. <strong>This cannot be undone.</strong>
                                    </p>
                                </div>
                                <button type="button" onclick="openDeleteModal()" class="ps-btn ps-btn-danger">
                                    <i class="bi bi-trash"></i> Delete Project
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<!-- Delete Project Modal -->
<div id="deleteProjectModal" class="ps-modal-overlay">
    <div class="ps-modal-dialog">
        <div class="ps-modal-header ps-modal-header-danger">
            <h3 class="ps-modal-title">
                <i class="bi bi-exclamation-triangle"></i> Delete Project
            </h3>
            <button type="button" class="ps-modal-close" onclick="closeDeleteModal()">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="ps-modal-body">
            <div class="ps-alert ps-alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <div>
                    <strong>Warning:</strong> This action is permanent. All data will be deleted.
                </div>
            </div>
            <p>Type <strong><?= e($project['key']) ?></strong> to confirm deletion:</p>
            <form action="<?= url("/projects/{$project['key']}") ?>" method="POST" id="deleteProjectForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <input type="text" id="confirmProjectKey" class="ps-form-control"
                    placeholder="Enter project key to confirm" required>
            </form>
        </div>
        <div class="ps-modal-footer">
            <button type="button" class="ps-btn ps-btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button type="submit" form="deleteProjectForm" id="confirmDeleteBtn" class="ps-btn ps-btn-danger" disabled>
                Delete Project
            </button>
        </div>
    </div>
</div>

<script>
    // Tab Switching
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.ps-tab-pane').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show selected tab
        const selectedTab = document.getElementById(tabName + '-tab');
        if (selectedTab) {
            selectedTab.classList.add('active');
        }

        // Update nav items
        document.querySelectorAll('.ps-nav-item').forEach(item => {
            item.classList.remove('active');
        });
        const activeNav = document.querySelector('[data-section="' + tabName + '"]');
        if (activeNav) {
            activeNav.classList.add('active');
        }

        // Update URL hash
        history.pushState(null, null, '#' + tabName);
    }

    // Restore tab from hash on page load
    window.addEventListener('load', function () {
        const hash = window.location.hash.substring(1);
        if (hash && document.getElementById(hash + '-tab')) {
            showTab(hash);
        }
    });

    // Delete Modal
    function openDeleteModal() {
        document.getElementById('deleteProjectModal').classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteProjectModal').classList.remove('active');
    }

    // Delete Confirmation
    const confirmInput = document.getElementById('confirmProjectKey');
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const projectKey = '<?= e($project['key']) ?>';

    if (confirmInput) {
        confirmInput.addEventListener('input', function () {
            if (this.value === projectKey) {
                deleteBtn.removeAttribute('disabled');
            } else {
                deleteBtn.setAttribute('disabled', 'disabled');
            }
        });
    }

    // Avatar Preview
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewBox = document.querySelector('.ps-avatar-box');
                let img = previewBox.querySelector('img');
                if (!img) {
                    previewBox.innerHTML = '<img src="" alt="Avatar" class="ps-avatar-preview-img">';
                    img = previewBox.querySelector('img');
                }
                img.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    /* ============================================
   PROJECT SETTINGS PAGE - ENTERPRISE DESIGN
   ============================================ */

    :root {
        --ps-primary: #8B1956;
        --ps-primary-dark: #6F123F;
        --ps-text-primary: #161B22;
        --ps-text-secondary: #626F86;
        --ps-border: #DFE1E6;
        --ps-bg-light: #F7F8FA;
        --ps-bg-white: #FFFFFF;
        --ps-danger: #DE350B;
        --ps-success: #216E4E;
        --ps-warning: #E77817;
        --ps-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .ps-page-wrapper {
        min-height: 100vh;
        background: var(--ps-bg-light);
    }

    /* Breadcrumb */
    .ps-breadcrumb {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 12px 32px;
        background: var(--ps-bg-white);
        border-bottom: 1px solid var(--ps-border);
        font-size: 13px;
    }

    .ps-breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--ps-primary);
        text-decoration: none;
        transition: color var(--ps-transition);
    }

    .ps-breadcrumb-link:hover {
        color: var(--ps-primary-dark);
        text-decoration: underline;
    }

    .ps-breadcrumb-sep {
        color: var(--ps-text-secondary);
    }

    .ps-breadcrumb-current {
        color: var(--ps-text-primary);
        font-weight: 600;
    }

    /* Page Header */
    .ps-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 32px;
        background: var(--ps-bg-white);
        border-bottom: 1px solid var(--ps-border);
        gap: 24px;
    }

    .ps-header-left {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        flex: 1;
    }

    .ps-avatar-container {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
        border: 1px solid var(--ps-border);
    }

    .ps-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ps-avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--ps-primary), var(--ps-primary-dark));
        color: white;
        font-size: 32px;
        font-weight: 700;
    }

    .ps-header-content {
        flex: 1;
    }

    .ps-header-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--ps-text-primary);
        margin: 0 0 4px 0;
    }

    .ps-header-subtitle {
        font-size: 14px;
        color: var(--ps-text-secondary);
        margin: 0;
    }

    .ps-header-right {
        display: flex;
        gap: 12px;
    }

    .ps-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        background: var(--ps-bg-white);
        border: 1px solid var(--ps-border);
        border-radius: 6px;
        color: var(--ps-text-primary);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all var(--ps-transition);
    }

    .ps-action-btn:hover {
        background: var(--ps-bg-light);
        border-color: var(--ps-text-secondary);
    }

    /* Main Container */
    .ps-main-container {
        display: flex;
        gap: 32px;
        padding: 32px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Sidebar */
    .ps-sidebar {
        flex: 0 0 220px;
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .ps-nav-menu {
        display: flex;
        flex-direction: column;
        gap: 0;
        background: var(--ps-bg-white);
        border: 1px solid var(--ps-border);
        border-radius: 8px;
        overflow: hidden;
    }

    .ps-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: var(--ps-text-primary);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        border-left: 3px solid transparent;
        transition: all var(--ps-transition);
        background: var(--ps-bg-white);
    }

    .ps-nav-item:hover {
        background: var(--ps-bg-light);
        border-left-color: var(--ps-primary);
    }

    .ps-nav-item.active {
        background: #F0DCE5;
        border-left-color: var(--ps-primary);
        color: var(--ps-primary);
    }

    .ps-nav-item i {
        font-size: 16px;
    }

    .ps-nav-item.ps-nav-danger {
        color: var(--ps-danger);
    }

    .ps-nav-item.ps-nav-danger.active {
        background: #FFF0EB;
        color: var(--ps-danger);
    }

    .ps-nav-divider {
        height: 1px;
        background: var(--ps-border);
        margin: 4px 0;
    }

    /* Content Area */
    .ps-content {
        flex: 1;
        min-width: 0;
    }

    .ps-tab-pane {
        display: none;
    }

    .ps-tab-pane.active {
        display: block;
    }

    /* Card */
    .ps-card {
        background: var(--ps-bg-white);
        border: 1px solid var(--ps-border);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .ps-card.ps-card-danger {
        border-color: #FFBDAD;
    }

    .ps-card-header {
        padding: 24px 32px;
        border-bottom: 1px solid var(--ps-border);
        background: var(--ps-bg-white);
    }

    .ps-card-header.ps-card-header-danger {
        background: #FFF0EB;
        border-bottom-color: #FFBDAD;
    }

    .ps-card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--ps-text-primary);
        margin: 0 0 4px 0;
    }

    .ps-card-header.ps-card-header-danger .ps-card-title {
        color: var(--ps-danger);
    }

    .ps-card-subtitle {
        font-size: 13px;
        color: var(--ps-text-secondary);
        margin: 0;
    }

    .ps-card-body {
        padding: 32px;
    }

    /* Form */
    .ps-form-section {
        margin-bottom: 32px;
    }

    .ps-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .ps-form-group {
        margin-bottom: 24px;
    }

    .ps-form-group:last-child {
        margin-bottom: 0;
    }

    .ps-form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--ps-text-secondary);
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .ps-required {
        color: var(--ps-danger);
        margin-left: 2px;
    }

    .ps-form-control {
        display: block;
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border: 1px solid var(--ps-border);
        border-radius: 6px;
        background: var(--ps-bg-white);
        color: var(--ps-text-primary);
        font-family: inherit;
        transition: all var(--ps-transition);
    }

    .ps-form-control:focus {
        outline: none;
        border-color: var(--ps-primary);
        box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.1);
    }

    .ps-form-control:disabled {
        background: var(--ps-bg-light);
        color: var(--ps-text-secondary);
        cursor: not-allowed;
    }

    .ps-form-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .ps-helper-text {
        font-size: 12px;
        color: var(--ps-text-secondary);
        margin: 6px 0 0 0;
    }

    /* Avatar Editor */
    .ps-avatar-editor {
        display: flex;
        gap: 32px;
        align-items: flex-start;
        margin-bottom: 32px;
        padding-bottom: 32px;
        border-bottom: 1px solid var(--ps-border);
    }

    .ps-avatar-box {
        width: 140px;
        height: 140px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
        border: 1px solid var(--ps-border);
        background: var(--ps-bg-light);
    }

    .ps-avatar-preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ps-avatar-placeholder-large {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--ps-primary), var(--ps-primary-dark));
        color: white;
        font-size: 56px;
        font-weight: 700;
    }

    .ps-avatar-actions {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .ps-file-input {
        display: none;
    }

    /* Radio Cards */
    .ps-radio-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .ps-radio-card {
        position: relative;
        cursor: pointer;
    }

    .ps-radio-card input {
        position: absolute;
        opacity: 0;
    }

    .ps-radio-content {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
        border: 2px solid var(--ps-border);
        border-radius: 8px;
        background: var(--ps-bg-white);
        transition: all var(--ps-transition);
    }

    .ps-radio-card input:checked+.ps-radio-content {
        border-color: var(--ps-primary);
        background: #F0DCE5;
    }

    .ps-radio-content i {
        font-size: 24px;
        color: var(--ps-text-secondary);
        flex-shrink: 0;
    }

    .ps-radio-card input:checked+.ps-radio-content i {
        color: var(--ps-primary);
    }

    .ps-radio-text strong {
        display: block;
        color: var(--ps-text-primary);
        margin-bottom: 4px;
        font-size: 14px;
    }

    .ps-radio-text p {
        font-size: 13px;
        color: var(--ps-text-secondary);
        margin: 0;
        line-height: 1.4;
    }

    /* Checkbox Group */
    .ps-checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ps-checkbox-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        cursor: pointer;
        user-select: none;
    }

    .ps-checkbox-item input {
        display: none;
    }

    .ps-checkbox-custom {
        width: 20px;
        height: 20px;
        min-width: 20px;
        border: 2px solid var(--ps-border);
        border-radius: 4px;
        background: var(--ps-bg-white);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--ps-transition);
        margin-top: 2px;
    }

    .ps-checkbox-item input:checked+.ps-checkbox-custom {
        background: var(--ps-primary);
        border-color: var(--ps-primary);
    }

    .ps-checkbox-item input:checked+.ps-checkbox-custom::after {
        content: '';
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .ps-checkbox-text strong {
        display: block;
        font-size: 14px;
        color: var(--ps-text-primary);
        margin-bottom: 2px;
        font-weight: 500;
    }

    .ps-checkbox-text p {
        font-size: 13px;
        color: var(--ps-text-secondary);
        margin: 0;
        line-height: 1.4;
    }

    /* Empty State */
    .ps-empty-state {
        text-align: center;
        padding: 64px 32px;
    }

    .ps-empty-state i {
        font-size: 64px;
        color: var(--ps-text-secondary);
        opacity: 0.3;
        display: block;
        margin-bottom: 20px;
    }

    .ps-empty-state h3 {
        font-size: 16px;
        font-weight: 600;
        color: var(--ps-text-primary);
        margin: 0 0 8px 0;
    }

    .ps-empty-state p {
        font-size: 13px;
        color: var(--ps-text-secondary);
        margin: 0;
    }

    /* Info Block */
    .ps-info-block {
        display: flex;
        gap: 12px;
        padding: 16px;
        background: #F0DCE5;
        border-radius: 6px;
        margin-bottom: 24px;
    }

    .ps-info-block i {
        font-size: 18px;
        color: var(--ps-primary);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .ps-info-block p {
        font-size: 13px;
        color: var(--ps-text-primary);
        margin: 0;
        line-height: 1.5;
    }

    .ps-mt-4 {
        margin-top: 24px;
    }

    /* Alert */
    .ps-alert {
        display: flex;
        gap: 12px;
        padding: 16px;
        border-radius: 6px;
        margin-bottom: 16px;
    }

    .ps-alert i {
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .ps-alert-danger {
        background: #FFF0EB;
        color: var(--ps-danger);
    }

    .ps-alert-danger i {
        color: var(--ps-danger);
    }

    /* Danger Zone */
    .ps-danger-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 0;
        border-bottom: 1px solid var(--ps-border);
        gap: 24px;
    }

    .ps-danger-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .ps-danger-item:first-child {
        padding-top: 0;
    }

    .ps-danger-info h3 {
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 4px 0;
        color: var(--ps-text-primary);
    }

    .ps-danger-info p {
        font-size: 13px;
        color: var(--ps-text-secondary);
        margin: 0;
        line-height: 1.5;
    }

    .ps-text-danger {
        color: var(--ps-danger);
    }

    /* Buttons */
    .ps-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid transparent;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all var(--ps-transition);
        background: var(--ps-bg-white);
        color: var(--ps-text-primary);
        white-space: nowrap;
    }

    .ps-btn:hover {
        transform: translateY(-1px);
    }

    .ps-btn-primary {
        background: var(--ps-primary);
        color: white;
        border-color: var(--ps-primary);
    }

    .ps-btn-primary:hover {
        background: var(--ps-primary-dark);
        border-color: var(--ps-primary-dark);
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.25);
    }

    .ps-btn-secondary {
        background: var(--ps-bg-light);
        border-color: var(--ps-border);
        color: var(--ps-text-primary);
    }

    .ps-btn-secondary:hover {
        background: #EBECF0;
        border-color: #B6C2CF;
    }

    .ps-btn-sm {
        padding: 8px 12px;
        font-size: 12px;
    }

    .ps-btn-success {
        background: var(--ps-success);
        color: white;
        border-color: var(--ps-success);
    }

    .ps-btn-success:hover {
        background: #175A3E;
        border-color: #175A3E;
    }

    .ps-btn-warning {
        background: var(--ps-warning);
        color: white;
        border-color: var(--ps-warning);
    }

    .ps-btn-warning:hover {
        background: #D56C12;
        border-color: #D56C12;
    }

    .ps-btn-danger {
        background: var(--ps-danger);
        color: white;
        border-color: var(--ps-danger);
    }

    .ps-btn-danger:hover {
        background: #C72E0F;
        border-color: #C72E0F;
        box-shadow: 0 4px 12px rgba(222, 53, 11, 0.25);
    }

    .ps-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* Form Actions */
    .ps-form-actions {
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--ps-border);
        display: flex;
        gap: 12px;
    }

    /* Modal */
    .ps-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .ps-modal-overlay.active {
        display: flex;
    }

    .ps-modal-dialog {
        background: var(--ps-bg-white);
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .ps-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--ps-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ps-modal-header.ps-modal-header-danger {
        background: #FFF0EB;
        border-bottom-color: #FFBDAD;
    }

    .ps-modal-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        font-weight: 700;
        color: var(--ps-text-primary);
        margin: 0;
    }

    .ps-modal-header.ps-modal-header-danger .ps-modal-title {
        color: var(--ps-danger);
    }

    .ps-modal-close {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: none;
        background: transparent;
        color: var(--ps-text-secondary);
        cursor: pointer;
        font-size: 20px;
        transition: color var(--ps-transition);
    }

    .ps-modal-close:hover {
        color: var(--ps-text-primary);
    }

    .ps-modal-body {
        padding: 24px;
    }

    .ps-modal-body p {
        font-size: 13px;
        color: var(--ps-text-primary);
        margin: 16px 0 12px 0;
    }

    .ps-modal-body strong {
        font-weight: 600;
    }

    .ps-modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--ps-border);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .ps-main-container {
            gap: 24px;
            padding: 24px;
        }

        .ps-sidebar {
            flex: 0 0 200px;
        }

        .ps-form-grid {
            gap: 20px;
        }
    }

    @media (max-width: 900px) {
        .ps-main-container {
            flex-direction: column;
            gap: 24px;
        }

        .ps-sidebar {
            flex: none;
            position: static;
            top: auto;
            width: 100%;
            height: auto;
        }

        .ps-nav-menu {
            display: flex;
            flex-direction: row;
            border-radius: 8px;
        }

        .ps-nav-item {
            flex: 1;
            justify-content: center;
            padding: 12px 8px;
            border-left: none;
            border-bottom: 3px solid transparent;
            white-space: nowrap;
            font-size: 12px;
        }

        .ps-nav-item:hover {
            border-left: none;
            border-bottom-color: var(--ps-primary);
        }

        .ps-nav-item.active {
            border-left: none;
            border-bottom-color: var(--ps-primary);
        }

        .ps-form-grid {
            grid-template-columns: 1fr;
        }

        .ps-radio-group {
            grid-template-columns: 1fr;
        }

        .ps-danger-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .ps-avatar-editor {
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }
    }

    @media (max-width: 768px) {
        .ps-breadcrumb {
            padding: 12px 16px;
            font-size: 12px;
        }

        .ps-page-header {
            padding: 20px 16px;
            flex-direction: column;
        }

        .ps-header-title {
            font-size: 20px;
        }

        .ps-main-container {
            padding: 16px;
            gap: 16px;
        }

        .ps-card-header {
            padding: 16px;
        }

        .ps-card-body {
            padding: 16px;
        }

        .ps-form-grid {
            gap: 16px;
        }

        .ps-avatar-editor {
            padding-bottom: 16px;
            margin-bottom: 16px;
        }

        .ps-form-actions {
            margin-top: 24px;
            padding-top: 16px;
        }
    }

    @media (max-width: 480px) {
        .ps-breadcrumb {
            padding: 8px 12px;
            font-size: 11px;
        }

        .ps-breadcrumb-link,
        .ps-breadcrumb-sep {
            display: none;
        }

        .ps-breadcrumb-link:first-child,
        .ps-breadcrumb-current {
            display: inline;
        }

        .ps-page-header {
            padding: 16px;
        }

        .ps-header-title {
            font-size: 16px;
        }

        .ps-header-left {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .ps-header-content {
            flex: 1;
        }

        .ps-avatar-container {
            width: 60px;
            height: 60px;
        }

        .ps-header-right {
            width: 100%;
        }

        .ps-action-btn {
            width: 100%;
            justify-content: center;
            font-size: 12px;
        }

        .ps-main-container {
            padding: 12px;
        }

        .ps-nav-item {
            font-size: 11px;
            padding: 10px 6px;
        }

        .ps-nav-item span {
            display: none;
        }

        .ps-card-header {
            padding: 12px;
        }

        .ps-card-body {
            padding: 12px;
        }

        .ps-card-title {
            font-size: 15px;
        }

        .ps-avatar-box {
            width: 100px;
            height: 100px;
        }

        .ps-danger-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .ps-form-actions {
            flex-direction: column;
        }

        .ps-btn {
            width: 100%;
            justify-content: center;
        }

        .ps-modal-dialog {
            width: 95%;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>