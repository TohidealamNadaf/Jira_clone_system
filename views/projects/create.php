<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="create-project-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="project-breadcrumb">
        <a href="<?= url('/') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Home
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">Projects</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Create Project</span>
    </div>

    <!-- Page Header -->
    <div class="create-header">
        <div class="header-left">
            <h1 class="page-title">Create Project</h1>
            <p class="page-subtitle">Set up a new project to track work and collaborate with your team</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="create-form-wrapper">
        <form action="<?= url('/projects') ?>" method="POST" enctype="multipart/form-data" class="create-form">
            <?= csrf_field() ?>

            <!-- Main Content Area -->
            <div class="form-content">
                <div class="form-section">
                    
                    <!-- Row 1: Name, Key, Category, Lead -->
                    <div class="form-row-horizontal">
                        <!-- Project Name -->
                        <div class="form-group-compact">
                            <label class="form-label-compact">
                                <span class="label-text">Name</span>
                                <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" class="form-control-compact <?= has_error('name') ? 'form-input-error' : '' ?>" 
                                   name="name" value="<?= e(old('name')) ?>" required maxlength="255" autofocus
                                   placeholder="Project name">
                            <?php if (has_error('name')): ?>
                            <div class="form-error"><?= e(error('name')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Project Key -->
                        <div class="form-group-compact">
                            <label class="form-label-compact">
                                <span class="label-text">Key</span>
                                <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" class="form-control-compact <?= has_error('key') ? 'form-input-error' : '' ?>" 
                                   name="key" value="<?= e(old('key')) ?>" required maxlength="10" 
                                   placeholder="e.g., APP"
                                   pattern="[A-Z0-9]+" style="text-transform: uppercase;">
                            <?php if (has_error('key')): ?>
                            <div class="form-error"><?= e(error('key')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Category -->
                        <div class="form-group-compact">
                            <label class="form-label-compact">Category</label>
                            <select class="form-control-compact <?= has_error('category_id') ? 'form-input-error' : '' ?>" 
                                    name="category_id">
                                <option value="">—</option>
                                <?php foreach ($categories ?? [] as $category): ?>
                                <option value="<?= e($category['id']) ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                    <?= e($category['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (has_error('category_id')): ?>
                            <div class="form-error"><?= e(error('category_id')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Project Lead -->
                        <div class="form-group-compact">
                            <label class="form-label-compact">Lead</label>
                            <select class="form-control-compact <?= has_error('lead_id') ? 'form-input-error' : '' ?>" 
                                    name="lead_id">
                                <option value="">Unassigned</option>
                                <?php foreach ($users ?? [] as $u): ?>
                                <option value="<?= e($u['id']) ?>" <?= old('lead_id') == $u['id'] ? 'selected' : '' ?>>
                                    <?= e($u['display_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (has_error('lead_id')): ?>
                            <div class="form-error"><?= e(error('lead_id')) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Row 2: Description (Full Width) -->
                    <div class="form-group">
                        <label class="form-label">
                            <span class="label-text">Description</span>
                        </label>
                        <textarea class="form-control form-textarea-styled <?= has_error('description') ? 'form-input-error' : '' ?>" 
                                  name="description" rows="3" maxlength="5000"
                                  placeholder="Describe the project's purpose and goals (optional)..."><?= e(old('description')) ?></textarea>
                        <?php if (has_error('description')): ?>
                        <div class="form-error"><?= e(error('description')) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Row 3: Default Assignee, Privacy -->
                    <div class="form-row-horizontal">
                        <!-- Default Assignee -->
                        <div class="form-group-compact">
                            <label class="form-label-compact">Default Assignee</label>
                            <select class="form-control-compact <?= has_error('default_assignee') ? 'form-input-error' : '' ?>" 
                                    name="default_assignee">
                                <option value="unassigned" <?= old('default_assignee', 'unassigned') === 'unassigned' ? 'selected' : '' ?>>
                                    Unassigned
                                </option>
                                <option value="project_lead" <?= old('default_assignee') === 'project_lead' ? 'selected' : '' ?>>
                                    Project Lead
                                </option>
                            </select>
                            <?php if (has_error('default_assignee')): ?>
                            <div class="form-error"><?= e(error('default_assignee')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Privacy Setting -->
                        <div class="form-checkbox-compact">
                            <label class="checkbox-label-compact">
                                <input type="checkbox" name="is_private" value="1" class="checkbox-input-compact"
                                       <?= old('is_private') ? 'checked' : '' ?>>
                                <span class="checkbox-text-compact">Private Project</span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?= url('/projects') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-folder-plus"></i> Create
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* ============================================
   CREATE PROJECT PAGE - ULTRA-COMPACT DESIGN
   Matches Create Issue page design
   ============================================ */

:root {
    --jira-blue: #8B1956 !important;
    --jira-blue-dark: #6F123F !important;
    --jira-dark: #161B22 !important;
    --jira-gray: #626F86 !important;
    --jira-light: #F7F8FA !important;
    --jira-border: #DFE1E6 !important;
}

.create-project-wrapper {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #F7F8FA;
    padding: 0;
    overflow: hidden;
    margin-top: -1.5rem;
    padding-top: 1.5rem;
}

/* Breadcrumb Navigation - Compact */
.project-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    font-size: 12px;
    flex-shrink: 0;
}

.breadcrumb-link {
    display: flex;
    align-items: center;
    gap: 4px;
    color: var(--jira-blue);
    text-decoration: none;
    transition: all 0.2s ease;
    font-weight: 500;
}

.breadcrumb-link:hover {
    color: #6F123F;
    text-decoration: underline;
}

.breadcrumb-separator {
    color: var(--jira-gray);
    font-weight: 300;
}

.breadcrumb-current {
    color: var(--text-primary);
    font-weight: 500;
}

/* Page Header - Ultra Compact */
.create-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13);
    flex-shrink: 0;
}

.header-left {
    flex: 1;
}

.page-title {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: var(--jira-dark);
    letter-spacing: -0.2px;
}

.page-subtitle {
    margin: 2px 0 0 0;
    font-size: 12px;
    font-weight: 400;
    color: var(--jira-gray);
}

/* Form Wrapper */
.create-form-wrapper {
    flex: 1;
    overflow-y: auto;
    padding: 16px 20px;
    background: #F7F8FA;
    display: flex;
    justify-content: flex-start;
}

.create-form {
    width: 100%;
}

/* Form Content - Ultra Compact Card */
.form-content {
    background: #FFFFFF;
    border: 1px solid var(--jira-border);
    border-radius: 4px;
    padding: 16px;
    box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13);
    margin-bottom: 12px;
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Form Group - Standard Full Width */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

/* Horizontal Row Container */
.form-row-horizontal {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 10px;
}

/* Full Width Labels */
.form-label {
    display: flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    color: var(--jira-dark);
    margin: 0;
    letter-spacing: -0.2px;
    text-transform: capitalize;
}

/* Compact Labels for Horizontal Row */
.form-label-compact {
    display: flex;
    align-items: center;
    font-size: 11px;
    font-weight: 600;
    color: var(--jira-dark);
    margin: 0;
    letter-spacing: -0.1px;
    text-transform: capitalize;
    white-space: nowrap;
}

.label-text {
    display: block;
}

.required-asterisk {
    color: #FF5630;
    margin-left: 3px;
    font-weight: 700;
    font-size: 0.9em;
}

.form-help-text {
    font-size: 11px;
    color: var(--jira-gray);
    margin-top: 3px;
}

/* Form Controls - Standard Size */
.form-control {
    font-family: inherit;
    font-size: 13px;
    padding: 8px 10px;
    border: 1px solid var(--jira-border);
    border-radius: 3px;
    background-color: #FFFFFF;
    color: var(--jira-dark);
    transition: all 0.2s ease;
    line-height: 1.4;
    width: 100%;
}

/* Compact Controls for Horizontal Row */
.form-control-compact {
    font-family: inherit;
    font-size: 12px;
    padding: 6px 8px;
    border: 1px solid var(--jira-border);
    border-radius: 3px;
    background-color: #FFFFFF;
    color: var(--jira-dark);
    transition: all 0.2s ease;
    line-height: 1.3;
    width: 100%;
    height: 32px;
}

.form-input-styled,
.form-select-styled,
.form-textarea-styled {
    padding: 8px 10px;
}

.form-control:focus,
.form-control-compact:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.08);
    background-color: #FFFFFF;
}

.form-control::placeholder {
    color: var(--jira-gray);
    opacity: 0.6;
}

.form-input-error {
    border-color: #FF5630;
}

.form-input-error:focus {
    box-shadow: 0 0 0 2px rgba(255, 86, 48, 0.08);
}

.form-error {
    font-size: 11px;
    color: #FF5630;
    margin-top: 2px;
    font-weight: 500;
}

.form-textarea-styled {
    resize: vertical;
    min-height: 70px;
    font-family: inherit;
}

/* Checkbox Styling */
.form-checkbox-compact {
    display: flex;
    align-items: center;
}

.checkbox-label-compact {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    user-select: none;
    font-size: 12px;
    font-weight: 500;
    color: var(--jira-dark);
}

.checkbox-input-compact {
    appearance: none;
    -webkit-appearance: none;
    width: 16px;
    height: 16px;
    border: 1px solid var(--jira-border);
    border-radius: 3px;
    background: #FFFFFF;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.checkbox-input-compact:hover {
    border-color: #B6C2CF;
    background: #F7F8FA;
}

.checkbox-input-compact:checked {
    background: var(--jira-blue);
    border-color: var(--jira-blue);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='white' d='M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 11-1.06-1.06l7.25-7.25a.75.75 0 011.06 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    background-size: 10px;
}

.checkbox-input-compact:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.08);
    border-color: var(--jira-blue);
}

.checkbox-text-compact {
    display: block;
}

/* Form Actions - Compact */
.form-actions {
    display: flex;
    justify-content: center;
    gap: 8px;
    align-items: center;
    padding-top: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 3px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
    height: 32px;
}

.btn-primary {
    background-color: var(--jira-blue);
    color: #FFFFFF;
    border-color: var(--jira-blue);
}

.btn-primary:hover {
    background-color: #6F123F;
    border-color: #6F123F;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(139, 25, 86, 0.25);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-secondary {
    background-color: #FFFFFF;
    color: var(--jira-dark);
    border-color: var(--jira-border);
}

.btn-secondary:hover {
    background-color: #F7F8FA;
    border-color: #B6C2CF;
}

.btn-secondary:active {
    background-color: #ECEDF0;
}

.btn i {
    font-size: 13px;
}

/* Responsive Design - Mobile Optimization */
@media (max-width: 1200px) {
    .form-row-horizontal {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    }
}

@media (max-width: 768px) {
    .create-form-wrapper {
        padding: 12px 16px;
    }

    .create-header {
        padding: 12px 16px;
    }

    .project-breadcrumb {
        padding: 8px 16px;
        font-size: 11px;
        gap: 4px;
    }

    .form-content {
        padding: 12px;
    }

    .form-row-horizontal {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 8px;
    }

    .form-section {
        gap: 10px;
    }

    .form-group {
        gap: 4px;
    }

    .form-actions {
        flex-direction: row;
        justify-content: center;
    }

    .page-title {
        font-size: 16px;
    }

    .page-subtitle {
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .create-form-wrapper {
        padding: 8px 12px;
    }

    .create-header {
        padding: 10px 12px;
    }

    .project-breadcrumb {
        padding: 8px 12px;
        gap: 3px;
    }

    .page-title {
        font-size: 14px;
    }

    .page-subtitle {
        font-size: 10px;
        margin-top: 0;
    }

    .form-content {
        padding: 10px;
        border-radius: 3px;
    }

    .form-row-horizontal {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .form-control-compact {
        height: auto;
    }

    .form-label-compact {
        font-size: 10px;
    }

    .form-actions {
        gap: 6px;
    }

    .btn {
        padding: 6px 12px;
        height: 30px;
        font-size: 12px;
    }
}

/* ============================================
   NATIVE HTML5 DATE INPUT STYLING
   Clean, minimal, modern
   ============================================ */

input[type="date"] {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    font-family: inherit;
    font-size: 12px;
    padding: 6px 8px;
    border: 1px solid var(--jira-border);
    border-radius: 3px;
    background-color: #FFFFFF;
    color: var(--jira-dark);
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
}

input[type="date"]:hover {
    border-color: #B6C2CF;
    background-color: #FFFFFF;
}

input[type="date"]:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.08);
    background-color: #FFFFFF;
}

/* Calendar icon styling */
input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    border-radius: 4px;
    margin-right: 2px;
    opacity: 0.6;
    filter: invert(0.3);
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Mobile date picker */
@media (max-width: 768px) {
    input[type="date"] {
        font-size: 11px;
        padding: 6px 6px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate project key from project name
    const nameInput = document.querySelector('[name="name"]');
    const keyInput = document.querySelector('[name="key"]');
    let userEditedKey = false;

    if (nameInput && keyInput) {
        nameInput.addEventListener('input', function() {
            if (!userEditedKey) {
                const key = this.value
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 10);
                keyInput.value = key;
            }
        });

        keyInput.addEventListener('input', function() {
            userEditedKey = true;
            this.value = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .substring(0, 10);
        });
    }
});
</script>

<?php \App\Core\View::endSection(); ?>
