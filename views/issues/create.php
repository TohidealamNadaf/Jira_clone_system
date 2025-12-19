<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="create-issue-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="issue-breadcrumb">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <?php if ($project): ?>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-separator">/</span>
        <?php endif; ?>
        <span class="breadcrumb-current">Create Issue</span>
    </div>

    <!-- Page Header -->
    <div class="create-header">
        <div class="header-left">
            <h1 class="page-title">Create Issue</h1>
            <p class="page-subtitle">Quickly add a new issue to track work</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="create-form-wrapper">
        <form action="<?= url($project ? "/projects/{$project['key']}/issues" : '/issues') ?>" method="POST" enctype="multipart/form-data" class="create-form" id="createIssueForm">
            <?= csrf_field() ?>

            <!-- Main Content Area -->
            <div class="form-content">
                <div class="form-section">
                    
                    <!-- Project & Issue Type (Compact Row) - MOVED TO TOP -->
                    <div class="form-row-horizontal">
                        <!-- Project Selection (if not specified) -->
                        <?php if (!$project): ?>
                        <div class="form-group-compact">
                            <label class="form-label-compact">
                                <span class="label-text">Project</span>
                                <span class="required-asterisk">*</span>
                            </label>
                            <select class="form-control-compact" name="project_id" required id="createProject">
                                <option value="">Select a project...</option>
                                <?php foreach ($projects ?? [] as $proj): ?>
                                <option value="<?= $proj['id'] ?>" data-key="<?= $proj['key'] ?>">
                                    <?= e($proj['key']) ?> - <?= e($proj['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                        <div class="form-group-compact">
                            <label class="form-label-compact">Project</label>
                            <input type="text" class="form-control-compact" readonly value="<?= e($project['key']) ?> - <?= e($project['name']) ?>">
                        </div>
                        <?php endif; ?>

                        <!-- Issue Type (Required) -->
                        <div class="form-group-compact">
                            <label class="form-label-compact">
                                <span class="label-text">Work Type</span>
                                <span class="required-asterisk">*</span>
                            </label>
                            <select class="form-control-compact" name="issue_type_id" required id="createIssueType">
                                <option value="">Select a project first...</option>
                                <?php if ($project): ?>
                                <?php foreach ($project['issue_types'] ?? [] as $type): ?>
                                <option value="<?= $type['id'] ?>">
                                    <?= e($type['name']) ?>
                                </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Summary Field (Required, Full Width) -->
                    <div class="form-group">
                        <label class="form-label">
                            <span class="label-text">Summary</span>
                            <span class="required-asterisk">*</span>
                        </label>
                        <input type="text" class="form-control form-input-styled" name="summary"
                               value="<?= e(old('summary')) ?>" required maxlength="500" autofocus
                               placeholder="What needs to be done?" id="createSummary">
                        <small class="form-text text-muted d-block mt-1">
                            <span id="summaryCharCount">0</span>/500 characters
                        </small>
                    </div>

                    <!-- Description Field with Quill Rich Text Editor + Inline Attachments -->
                    <div class="form-group">
                        <label class="form-label">
                            <span class="label-text">Description</span>
                        </label>
                        <div style="border: 1px solid #DFE1E6; border-radius: 4px; overflow: hidden;">
                            <!-- Quill Editor Container -->
                            <div id="createIssueDescriptionEditor" style="background: white; min-height: 200px;"></div>
                            <!-- Inline Attachments Section Below Editor -->
                            <div id="descriptionAttachmentsContainer" style="display: none; border-top: 1px solid #DFE1E6; background-color: #F7F8FA; padding: 12px;">
                                <div style="font-weight: 500; font-size: 13px; color: #161B22; margin-bottom: 12px; padding-left: 8px;">Attached files:</div>
                                <div id="descriptionAttachmentsList" style="display: flex; flex-direction: column; gap: 8px;"></div>
                            </div>
                            <!-- Hidden file input for description attachments -->
                            <input type="file" id="descriptionAttachmentInput" multiple style="display: none;" 
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.zip,.mov,.mp4,.webm,.webp">
                        </div>
                        <!-- Hidden textarea to store content -->
                        <textarea name="description" id="createDescription" style="display:none;"></textarea>
                        <small class="form-text text-muted d-block mt-2">
                            <span id="descCharCount">0</span>/5000 characters
                        </small>
                    </div>

                    <!-- Reporter Field (Auto-filled, Read-only) - With Avatar -->
                    <div class="form-group">
                        <label class="form-label">
                            <span class="label-text">Reporter</span>
                        </label>
                        <div class="reporter-field-wrapper-page" style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #DFE1E6; border-radius: 4px; background-color: #F7F8FA;">
                            <div id="createReporterAvatar" style="width: 44px; height: 44px; border-radius: 50%; background-color: #8B1956; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px; flex-shrink: 0;">
                                C
                            </div>
                            <div style="flex: 1;">
                                <div id="createReporterName" style="font-weight: 500; color: #161B22; font-size: 14px;">Current User</div>
                                <small class="form-text text-muted d-block" style="margin-top: 2px;">You are automatically set as the reporter</small>
                            </div>
                        </div>
                    </div>

                    <!-- Assignee Field -->
                     <div class="form-group">
                         <label class="form-label">
                             <span class="label-text">Assignee</span>
                         </label>
                         <div class="assignee-wrapper">
                             <select class="form-control form-select-styled" name="assignee_id" id="createAssignee">
                                 <option value="">Automatic</option>
                             </select>
                             <a href="#" class="assignee-link ms-2" id="assignToMeLink">Assign to me</a>
                         </div>
                     </div>

                     <!-- Status Field -->
                     <div class="form-group">
                         <label class="form-label">
                             <span class="label-text">Status</span>
                         </label>
                         <select class="form-control form-select-styled" name="status_id" id="createStatus">
                             <option value="">Default</option>
                         </select>
                     </div>

                     <!-- Sprint Field -->
                     <div class="form-group">
                         <label class="form-label">
                             <span class="label-text">Sprint</span>
                         </label>
                         <select class="form-control form-select-styled" name="sprint_id" id="createSprint">
                             <option value="">None (Backlog)</option>
                         </select>
                     </div>

                     <!-- Labels Field -->
                     <div class="form-group">
                         <label class="form-label">
                             <span class="label-text">Labels</span>
                         </label>
                         <select class="form-control form-select-styled" name="labels" id="createLabels" 
                                 placeholder="Select labels..." multiple>
                             <option value="">No labels</option>
                         </select>
                         <small class="form-text text-muted d-block mt-1">Hold Ctrl/Cmd to select multiple</small>
                     </div>

                     <!-- Start Date Field -->
                     <div class="form-group">
                         <label class="form-label">
                             <span class="label-text">Start Date</span>
                         </label>
                         <input type="date" class="form-control form-input-styled" name="start_date" 
                                id="createStartDate">
                     </div>

                     <!-- Due Date Field -->
                     <div class="form-group">
                         <label class="form-label">
                             <span class="label-text">Due Date</span>
                         </label>
                         <input type="date" class="form-control form-input-styled" name="due_date" 
                                id="createDueDate">
                     </div>

                     <!-- Attachments Field -->
                    <div class="form-group">
                        <label class="form-label">
                            <span class="label-text">Attachments</span>
                        </label>
                        <div class="attachment-drop-zone" id="createAttachmentZone" style="
                            border: 2px dashed #DFE1E6;
                            border-radius: 8px;
                            padding: 20px;
                            text-align: center;
                            cursor: pointer;
                            transition: all 0.2s ease;
                            background-color: #F7F8FA;
                        ">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: var(--jira-blue); margin-bottom: 8px;"></i>
                            <div style="font-weight: 500; color: #161B22; margin-bottom: 4px;">Drop files here to upload</div>
                            <div style="font-size: 13px; color: #626F86;">Or click to select files (max 10MB per file)</div>
                            <input type="file" id="createAttachmentInput" multiple style="display: none;" 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.zip">
                        </div>
                        <div id="createAttachmentList" style="margin-top: 12px;"></div>
                    </div>

                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?= url($project ? "/projects/{$project['key']}" : '/projects') ?>" class="btn btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary" id="submitCreateBtn">
                    <i class="bi bi-plus-lg me-1"></i> Create
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* ============================================
   CREATE ISSUE PAGE - ENTERPRISE JIRA DESIGN
   Matches Quick Create Modal
   ============================================ */

:root {
    --jira-blue: #8B1956 !important;
    --jira-blue-dark: #6F123F !important;
    --jira-dark: #161B22 !important;
    --jira-gray: #626F86 !important;
    --jira-light: #F7F8FA !important;
    --jira-border: #DFE1E6 !important;
}

.create-issue-wrapper {
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
.issue-breadcrumb {
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
    display: none;
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

/* Form Content - Card */
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

/* Form Group */
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
    box-sizing: border-box;
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
    box-sizing: border-box;
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

.form-textarea-styled {
    resize: vertical;
    min-height: 70px;
    font-family: inherit;
}

/* Quill Editor Styles */
.ql-container {
    font-size: 13px;
    border: none;
}

.ql-editor {
    min-height: 150px;
    padding: 10px;
    font-family: inherit;
}

.ql-toolbar {
    border: 1px solid var(--jira-border);
    border-bottom: none;
    border-radius: 3px 3px 0 0;
    background-color: #F7F8FA;
}

.ql-toolbar.ql-snow {
    padding: 8px;
}

.ql-toolbar.ql-snow .ql-picker-label {
    color: var(--jira-dark);
}

.ql-toolbar.ql-snow .ql-stroke {
    stroke: var(--jira-dark);
}

.ql-toolbar.ql-snow .ql-fill {
    fill: var(--jira-dark);
}

.ql-toolbar.ql-snow button:hover .ql-stroke,
.ql-toolbar.ql-snow button.ql-active .ql-stroke {
    stroke: var(--jira-blue);
}

.ql-toolbar.ql-snow button:hover .ql-fill,
.ql-toolbar.ql-snow button.ql-active .ql-fill {
    fill: var(--jira-blue);
}

.ql-container.ql-snow {
    border: 1px solid var(--jira-border);
    border-radius: 0 0 3px 3px;
}

/* Assignee Wrapper */
.assignee-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.assignee-wrapper select {
    flex: 1;
}

.assignee-link {
    color: var(--jira-blue);
    text-decoration: none;
    font-size: 13px;
    white-space: nowrap;
    transition: all 0.2s ease;
    padding: 4px 8px;
    border-radius: 3px;
}

.assignee-link:hover {
    color: #6F123F;
    background-color: rgba(139, 25, 86, 0.05);
}

/* Attachment Drop Zone */
.attachment-drop-zone {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.attachment-drop-zone:hover {
    background-color: rgba(139, 25, 86, 0.05);
    border-color: var(--jira-blue);
}

.attachment-drop-zone.dragover {
    background-color: rgba(139, 25, 86, 0.1);
    border-color: var(--jira-blue);
}

/* Attachment List Items */
.attachment-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background-color: #F7F8FA;
    border: 1px solid var(--jira-border);
    border-radius: 3px;
    margin-bottom: 8px;
}

.attachment-item-info {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 0;
}

.attachment-item-icon {
    width: 20px;
    height: 20px;
    font-size: 14px;
    flex-shrink: 0;
}

.attachment-item-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.attachment-item-name {
    font-size: 12px;
    font-weight: 500;
    color: var(--jira-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.attachment-item-size {
    font-size: 11px;
    color: var(--jira-gray);
}

.attachment-remove-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background-color: transparent;
    border: none;
    color: #AE2A19;
    cursor: pointer;
    border-radius: 3px;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.attachment-remove-btn:hover {
    background-color: #FFECEB;
    color: #AE2A19;
}

/* Form Actions - Compact */
.form-actions {
    display: flex;
    justify-content: center;
    gap: 8px;
    align-items: center;
    padding: 12px 20px;
    background: #F7F8FA;
    border-top: 1px solid var(--jira-border);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
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

    .issue-breadcrumb {
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
        padding: 12px 16px;
    }

    .page-title {
        font-size: 16px;
    }

    .assignee-wrapper {
        flex-direction: column;
        gap: 8px;
    }

    .assignee-wrapper select {
        width: 100%;
    }

    .assignee-link {
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .create-form-wrapper {
        padding: 8px 12px;
    }

    .create-header {
        padding: 10px 12px;
    }

    .issue-breadcrumb {
        padding: 8px 12px;
        gap: 3px;
    }

    .page-title {
        font-size: 14px;
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
        padding: 10px 12px;
    }

    .btn {
        padding: 6px 12px;
        height: 30px;
        font-size: 12px;
    }

    .editor-buttons {
        gap: 4px;
        padding: 6px;
    }

    .editor-btn {
        width: 28px;
        height: 28px;
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
    box-sizing: border-box;
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

/* Text utilities */
.text-muted {
    color: var(--jira-gray) !important;
}

.d-block {
    display: block !important;
}

.mt-1 {
    margin-top: 4px !important;
}

.ms-2 {
    margin-left: 8px !important;
}

.me-1 {
    margin-right: 4px !important;
}
</style>

<script>
    console.log('[CREATE-PAGE] Script initialization started');

    // Get CSRF token
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';

    // API URLs - Use PHP-generated URLs to handle any deployment path
    const API_USERS_ACTIVE_URL = '<?= url('/users/active') ?>';
    const APP_BASE_PATH = '<?= url('') ?>';

    // ============================================
    // Character Counters for Summary and Description
    // ============================================
    console.log('[COUNTERS] Setting up character counters');
    const summaryInput = document.getElementById('createSummary');
    const summaryCharCount = document.getElementById('summaryCharCount');
    const descriptionInput = document.getElementById('createDescription');
    const descCharCount = document.getElementById('descCharCount');

    if (summaryInput && summaryCharCount) {
        summaryInput.addEventListener('input', function() {
            summaryCharCount.textContent = this.value.length;
        });
    }

    if (descCharCount) {
        // Character count will be updated by Quill when editor is initialized
    }

    // ============================================
    // Initialize Reporter Field with Avatar
    // ============================================
    console.log('[REPORTER] Initializing reporter field');
    function initializeReporterField() {
        const reporterAvatar = document.getElementById('createReporterAvatar');
        const reporterName = document.getElementById('createReporterName');

        if (!reporterAvatar || !reporterName) return;

        try {
            // Get current user data from navbar button
            const userMenuBtn = document.getElementById('userMenu');
            if (!userMenuBtn) {
                console.warn('⚠️ User menu button not found');
                return;
            }

            // Extract user data from navbar button data attributes
            const userName = userMenuBtn.getAttribute('data-user-name') || 'User';
            const userAvatar = userMenuBtn.getAttribute('data-user-avatar') || '';

            // Get user initials from user name
            const initials = userName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);

            console.log('📝 Reporter Field initialized:', { userName, userAvatar, initials });

            // Update reporter name
            reporterName.textContent = userName;

            // Update reporter avatar
            if (userAvatar && userAvatar.includes('http')) {
                // If avatar is a valid image URL, show it as background image
                reporterAvatar.style.backgroundImage = `url(${userAvatar})`;
                reporterAvatar.style.backgroundSize = 'cover';
                reporterAvatar.style.backgroundPosition = 'center';
                reporterAvatar.style.color = 'transparent'; // Hide text
                reporterAvatar.innerHTML = ''; // Clear any content
            } else if (initials) {
                // Show initials if no valid avatar URL
                reporterAvatar.textContent = initials;
                reporterAvatar.style.backgroundImage = 'none';
                reporterAvatar.style.color = 'white';
            }
        } catch (error) {
            console.error('❌ Failed to initialize reporter field:', error);
        }
    }

    // Initialize reporter on page load
    initializeReporterField();

    // ============================================
    // Assign to Me Link Handler
    // ============================================
    console.log('[ASSIGN-ME] Setting up assign-to-me link');
    const assignToMeLink = document.getElementById('assignToMeLink');
    if (assignToMeLink) {
        assignToMeLink.addEventListener('click', function(e) {
            e.preventDefault();
            const assigneeSelect = document.getElementById('createAssignee');
            if (!assigneeSelect) return;

            const currentUserOption = assigneeSelect.querySelector('option[data-is-current="true"]');
            if (currentUserOption) {
                assigneeSelect.value = currentUserOption.value;
                console.log('✅ Assigned to current user:', currentUserOption.textContent);
            } else {
                console.warn('⚠️ Current user option not found');
            }
        });
    }

    // ============================================
    // Attachment Handling (Rich, Production-Ready)
    // ============================================
    console.log('[ATTACHMENTS] Setting up attachment handling');
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip'];
    const attachmentInput = document.getElementById('createAttachmentInput');
    const attachmentZone = document.getElementById('createAttachmentZone');
    const attachmentList = document.getElementById('createAttachmentList');
    const selectedFiles = new Map();

    // Only setup attachment handlers if elements exist
    if (attachmentInput && attachmentZone && attachmentList) {
        // Click to select files
        attachmentZone.addEventListener('click', () => {
            attachmentInput.click();
        });

        // Handle file selection
        attachmentInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        // Drag and drop
        attachmentZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            attachmentZone.classList.add('dragover');
        });

        attachmentZone.addEventListener('dragleave', () => {
            attachmentZone.classList.remove('dragover');
        });

        attachmentZone.addEventListener('drop', (e) => {
            e.preventDefault();
            attachmentZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        function handleFiles(files) {
            for (let file of files) {
                // Validate file size
                if (file.size > MAX_FILE_SIZE) {
                    alert(`File "${file.name}" exceeds 10MB limit`);
                    continue;
                }

                // Validate file type
                const ext = file.name.split('.').pop().toLowerCase();
                if (!ALLOWED_EXTENSIONS.includes(ext)) {
                    alert(`File type ".${ext}" not allowed`);
                    continue;
                }

                // Add to selected files
                const fileId = 'file_' + Math.random().toString(36).substr(2, 9);
                selectedFiles.set(fileId, file);
                addAttachmentItem(fileId, file);
            }
        }

        function addAttachmentItem(fileId, file) {
            const listItem = document.createElement('div');
            listItem.id = fileId;
            listItem.className = 'attachment-item';

            const ext = file.name.split('.').pop().toLowerCase();
            let iconClass = 'bi-file';

            // Determine icon based on file type
            if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                iconClass = 'bi-image';
            } else if (['pdf'].includes(ext)) {
                iconClass = 'bi-file-pdf';
            } else if (['doc', 'docx'].includes(ext)) {
                iconClass = 'bi-file-word';
            } else if (['xls', 'xlsx'].includes(ext)) {
                iconClass = 'bi-file-earmark-spreadsheet';
            } else if (['ppt', 'pptx'].includes(ext)) {
                iconClass = 'bi-file-presentation';
            } else if (['zip'].includes(ext)) {
                iconClass = 'bi-file-zip';
            }

            listItem.innerHTML = `
                <div class="attachment-item-info">
                    <div class="attachment-item-icon">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div class="attachment-item-details">
                        <span class="attachment-item-name" title="${file.name}">${file.name}</span>
                        <span class="attachment-item-size">${formatFileSize(file.size)}</span>
                    </div>
                </div>
                <button type="button" class="attachment-remove-btn" aria-label="Remove file">
                    <i class="bi bi-x"></i>
                </button>
            `;

            const removeBtn = listItem.querySelector('.attachment-remove-btn');
            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                selectedFiles.delete(fileId);
                listItem.remove();
            });

            attachmentList.appendChild(listItem);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    }

    // ============================================
    // Project Change Handler & Assignee Loading
    // ============================================
    console.log('[PROJECT-CHANGE] Setting up project change handler');
    <?php if (!$project): ?>
    const projectSelect = document.getElementById('createProject');
    const issueTypeSelect = document.getElementById('createIssueType');
    const statusSelect = document.getElementById('createStatus');
    const sprintSelect = document.getElementById('createSprint');
    const labelsSelect = document.getElementById('createLabels');

    if (projectSelect) {
        projectSelect.addEventListener('change', async function() {
            const projectId = this.value;
            console.log('🎯 Project changed to:', projectId);

            if (!projectId) {
                if (issueTypeSelect) {
                    issueTypeSelect.innerHTML = '<option value="">Select a project first...</option>';
                }
                const assigneeSelect = document.getElementById('createAssignee');
                if (assigneeSelect) {
                    assigneeSelect.innerHTML = '<option value="">Automatic</option>';
                }
                statusSelect.innerHTML = '<option value="">Default</option>';
                sprintSelect.innerHTML = '<option value="">None (Backlog)</option>';
                labelsSelect.innerHTML = '<option value="">No labels</option>';
                return;
            }

            try {
                // Get project key from selected option
                const projectKey = this.options[this.selectedIndex].getAttribute('data-key');
                const response = await fetch(APP_BASE_PATH + '/projects/' + projectKey, {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                
                // Update issue types
                if (issueTypeSelect) {
                    issueTypeSelect.innerHTML = '<option value="">Select a work type...</option>';
                    if (data.issue_types && Array.isArray(data.issue_types)) {
                        data.issue_types.forEach(type => {
                            const option = document.createElement('option');
                            option.value = type.id;
                            option.textContent = type.name;
                            issueTypeSelect.appendChild(option);
                        });
                    }
                }

                // Update assignee list
                const assigneeSelect = document.getElementById('createAssignee');
                if (assigneeSelect) {
                    assigneeSelect.innerHTML = '<option value="">Automatic</option>';
                    if (data.members && Array.isArray(data.members)) {
                        const currentUserId = parseInt('<?= e($user['id'] ?? '0') ?>') || 0;
                        data.members.forEach(member => {
                            const option = document.createElement('option');
                            option.value = member.id;
                            const isCurrentUser = parseInt(member.id) === currentUserId;
                            option.dataset.isCurrent = isCurrentUser ? 'true' : 'false';
                            option.textContent = member.display_name + (isCurrentUser ? ' (me)' : '');
                            assigneeSelect.appendChild(option);
                        });
                    }
                }

                // Populate statuses
                if (data.statuses && Array.isArray(data.statuses)) {
                    statusSelect.innerHTML = '<option value="">Default</option>';
                    data.statuses.forEach(status => {
                        const option = document.createElement('option');
                        option.value = status.id;
                        option.textContent = status.name;
                        statusSelect.appendChild(option);
                    });
                    console.log('✅ Statuses populated:', data.statuses.length);
                }

                // Populate sprints
                if (data.sprints && Array.isArray(data.sprints)) {
                    sprintSelect.innerHTML = '<option value="">None (Backlog)</option>';
                    data.sprints.forEach(sprint => {
                        const option = document.createElement('option');
                        option.value = sprint.id;
                        option.textContent = sprint.name;
                        sprintSelect.appendChild(option);
                    });
                    console.log('✅ Sprints populated:', data.sprints.length);
                }

                // Populate labels
                if (data.labels && Array.isArray(data.labels)) {
                    labelsSelect.innerHTML = '<option value="">No labels</option>';
                    data.labels.forEach(label => {
                        const option = document.createElement('option');
                        option.value = label.id;
                        option.textContent = label.name;
                        labelsSelect.appendChild(option);
                    });
                    console.log('✅ Labels populated:', data.labels.length);
                }

                console.log('✅ Project data loaded successfully');
            } catch (error) {
                console.error('❌ Failed to load project data:', error);
            }
        });
    }
    <?php else: ?>
    // Pre-populate assignee list, statuses, sprints, labels for existing project
    console.log('[ASSIGNEES] Pre-populating assignee list for project');
    (async function() {
        try {
            const response = await fetch(API_USERS_ACTIVE_URL, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (!response.ok) {
                throw new Error(`Failed to load users: ${response.status}`);
            }

            const users = await response.json();
            const assigneeSelect = document.getElementById('createAssignee');
            
            if (assigneeSelect && Array.isArray(users)) {
                assigneeSelect.innerHTML = '<option value="">Automatic</option>';
                const currentUserId = parseInt('<?= e($user['id'] ?? '0') ?>') || 0;
                
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    const isCurrentUser = parseInt(user.id) === currentUserId;
                    option.dataset.isCurrent = isCurrentUser ? 'true' : 'false';
                    option.textContent = user.display_name + (isCurrentUser ? ' (me)' : '');
                    assigneeSelect.appendChild(option);
                });
                console.log('✅ Assignee list populated with ' + users.length + ' users');
            }

            // Also populate statuses, sprints, and labels for existing project
            const projectKey = '<?= e($project['key'] ?? '') ?>';
            if (projectKey) {
                try {
                    const projectResponse = await fetch(APP_BASE_PATH + '/projects/' + projectKey, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    if (projectResponse.ok) {
                        const projectData = await projectResponse.json();
                        const statusSelect = document.getElementById('createStatus');
                        const sprintSelect = document.getElementById('createSprint');
                        const labelsSelect = document.getElementById('createLabels');

                        // Populate statuses
                        if (statusSelect && projectData.statuses && Array.isArray(projectData.statuses)) {
                            statusSelect.innerHTML = '<option value="">Default</option>';
                            projectData.statuses.forEach(status => {
                                const option = document.createElement('option');
                                option.value = status.id;
                                option.textContent = status.name;
                                statusSelect.appendChild(option);
                            });
                        }

                        // Populate sprints
                        if (sprintSelect && projectData.sprints && Array.isArray(projectData.sprints)) {
                            sprintSelect.innerHTML = '<option value="">None (Backlog)</option>';
                            projectData.sprints.forEach(sprint => {
                                const option = document.createElement('option');
                                option.value = sprint.id;
                                option.textContent = sprint.name;
                                sprintSelect.appendChild(option);
                            });
                        }

                        // Populate labels
                        if (labelsSelect && projectData.labels && Array.isArray(projectData.labels)) {
                            labelsSelect.innerHTML = '<option value="">No labels</option>';
                            projectData.labels.forEach(label => {
                                const option = document.createElement('option');
                                option.value = label.id;
                                option.textContent = label.name;
                                labelsSelect.appendChild(option);
                            });
                        }
                    }
                } catch (error) {
                    console.error('❌ Failed to populate statuses/sprints/labels:', error);
                }
            }
        } catch (error) {
            console.error('❌ Failed to load assignees:', error);
        }
    })();
    <?php endif; ?>

    console.log('[CREATE-PAGE] Script initialization complete');

    // ============================================
    // Quill Rich Text Editor for Create Issue Page
    // ============================================
    console.log('[QUILL] Setting up Quill editor for create issue page');
    let createPageQuillEditor = null;
    let createPageDescriptionAttachments = new Map();

    function initializeCreatePageQuillEditor() {
        try {
            if (createPageQuillEditor) {
                console.log('ℹ️ Quill editor already initialized');
                return;
            }

            const editorDiv = document.getElementById('createIssueDescriptionEditor');
            if (!editorDiv) {
                console.log('ℹ️ Quill editor container not found (may not be on create page)');
                return;
            }

            // Initialize Quill with custom toolbar including attachment button
            createPageQuillEditor = new Quill('#createIssueDescriptionEditor', {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': [1, 2, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['link', 'image'],
                            ['attach-file'], // Custom attachment button
                            ['clean']
                        ],
                        handlers: {
                            'attach-file': function() {
                                document.getElementById('descriptionAttachmentInput').click();
                            }
                        }
                    }
                },
                placeholder: 'Type description here...',
                bounds: '#createIssueDescriptionEditor'
            });

            // Add custom styling to the attach button
            const attachBtn = document.querySelector('#createIssueDescriptionEditor .ql-toolbar .ql-attach-file');
            if (attachBtn) {
                attachBtn.innerHTML = '<i class="bi bi-paperclip" style="font-size: 16px;"></i>';
                attachBtn.setAttribute('title', 'Attach files');
            }

            // Sync Quill content to hidden textarea
            createPageQuillEditor.on('text-change', function () {
                const content = createPageQuillEditor.root.innerHTML;
                document.getElementById('createDescription').value = content;
                
                // Update character count (approximate)
                const textLength = createPageQuillEditor.getText().length - 1; // -1 for trailing newline
                document.getElementById('descCharCount').textContent = Math.max(0, textLength);
            });

            // Setup attachment file input listener
            setupCreatePageDescriptionAttachmentHandlers();

            console.log('✅ Quill editor initialized successfully for create page');
        } catch (error) {
            console.error('❌ Error initializing Quill on create page:', error);
        }
    }

    function setupCreatePageDescriptionAttachmentHandlers() {
        const fileInput = document.getElementById('descriptionAttachmentInput');
        
        // Handle file selection
        fileInput.addEventListener('change', function() {
            const files = Array.from(this.files);
            files.forEach(file => {
                addCreatePageDescriptionAttachment(file);
            });
            this.value = ''; // Reset input
        });

        // Handle drag and drop on description editor
        const editorDiv = document.getElementById('createIssueDescriptionEditor');
        const editorArea = editorDiv.closest('.form-group');
        
        if (editorArea) {
            editorArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                editorDiv.style.backgroundColor = '#F0DCE5';
            });
            
            editorArea.addEventListener('dragleave', () => {
                editorDiv.style.backgroundColor = 'white';
            });
            
            editorArea.addEventListener('drop', (e) => {
                e.preventDefault();
                editorDiv.style.backgroundColor = 'white';
                const files = Array.from(e.dataTransfer.files);
                files.forEach(file => {
                    addCreatePageDescriptionAttachment(file);
                });
            });
        }
    }

    function addCreatePageDescriptionAttachment(file) {
        // Validate file size (10MB max)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
            return;
        }

        // Validate file type
        const allowedTypes = ['application/pdf', 'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/zip',
            'video/mp4', 'video/webm', 'video/quicktime'];
        
        if (!allowedTypes.includes(file.type)) {
            alert(`File type not allowed: ${file.type}`);
            return;
        }

        // Generate unique ID for this file
        const fileId = 'create-desc-attach-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        
        // Store file
        createPageDescriptionAttachments.set(fileId, file);

        // Show container if hidden
        const container = document.getElementById('descriptionAttachmentsContainer');
        container.style.display = 'block';

        // Create file item element
        const fileList = document.getElementById('descriptionAttachmentsList');
        const fileItem = createCreatePageDescriptionFileItem(fileId, file);
        fileList.appendChild(fileItem);
    }

    function createCreatePageDescriptionFileItem(fileId, file) {
        const div = document.createElement('div');
        div.id = fileId;
        div.style.cssText = `
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: white;
            border: 1px solid #DFE1E6;
            border-radius: 4px;
            transition: all 0.2s ease;
        `;

        // File icon based on type
        const icon = getCreatePageFileIcon(file.name);
        const fileSize = formatCreatePageFileSize(file.size);

        div.innerHTML = `
            <div style="font-size: 20px; color: #8B1956; min-width: 24px; text-align: center;">
                ${icon}
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="font-weight: 500; font-size: 13px; color: #161B22; word-break: break-word; white-space: normal;">
                    ${escapeCreatePageHtml(file.name)}
                </div>
                <div style="font-size: 12px; color: #626F86; margin-top: 2px;">
                    ${fileSize}
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-ghost-danger" style="
                border: none;
                background: none;
                color: #ae2a19;
                cursor: pointer;
                padding: 4px 8px;
                font-size: 18px;
                line-height: 1;
                transition: color 0.2s;
            " title="Remove file" onclick="removeCreatePageDescriptionAttachment('${fileId}')">
                ×
            </button>
        `;

        // Hover effect
        div.addEventListener('mouseenter', () => {
            div.style.backgroundColor = '#DEEBFF';
            div.style.borderColor = '#8B1956';
        });
        div.addEventListener('mouseleave', () => {
            div.style.backgroundColor = 'white';
            div.style.borderColor = '#DFE1E6';
        });

        return div;
    }

    function removeCreatePageDescriptionAttachment(fileId) {
        createPageDescriptionAttachments.delete(fileId);
        const fileItem = document.getElementById(fileId);
        if (fileItem) {
            fileItem.remove();
        }
        
        // Hide container if empty
        if (createPageDescriptionAttachments.size === 0) {
            document.getElementById('descriptionAttachmentsContainer').style.display = 'none';
        }
    }

    function getCreatePageFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            'pdf': '📄',
            'doc': '📝', 'docx': '📝',
            'xls': '📊', 'xlsx': '📊',
            'ppt': '📽️', 'pptx': '📽️',
            'txt': '📄',
            'jpg': '🖼️', 'jpeg': '🖼️', 'png': '🖼️', 'gif': '🖼️', 'webp': '🖼️',
            'zip': '📦',
            'mp4': '🎬', 'webm': '🎬', 'mov': '🎬'
        };
        return icons[ext] || '📎';
    }

    function formatCreatePageFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    }

    function escapeCreatePageHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Initialize Quill when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCreatePageQuillEditor);
    } else {
        initializeCreatePageQuillEditor();
    }

    console.log('[QUILL] Setup complete - awaiting initialization');
</script>

<?php \App\Core\View::endSection(); ?>
