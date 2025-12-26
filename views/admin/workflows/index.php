<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="workflows-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="workflows-breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-gear"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Workflows</span>
    </div>

    <!-- Page Header Section -->
    <div class="workflows-header">
        <!-- Left: Title + Subtitle -->
        <div class="workflows-header-left">
            <h1 class="workflows-title">Workflows</h1>
            <p class="workflows-subtitle">Workflows define the paths an issue can take through its lifecycle</p>
        </div>

        <!-- Right: Action Button -->
        <div class="workflows-header-actions">
            <button class="action-button primary" data-bs-toggle="modal" data-bs-target="#createWorkflowModal">
                <i class="bi bi-plus-lg"></i>
                <span>Create Workflow</span>
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="workflows-content">
        <!-- Workflows Card -->
        <div class="workflows-card">
            <div class="card-header-bar">
                <h2 class="card-title">All Workflows</h2>
            </div>

            <div class="workflows-container">
                <?php if (empty($workflows)): ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="empty-icon">🔄</div>
                        <h3 class="empty-title">No workflows yet</h3>
                        <p class="empty-text">Get started by creating your first workflow to manage your issue lifecycle.</p>
                        <button class="btn-create-empty" data-bs-toggle="modal" data-bs-target="#createWorkflowModal">
                            <i class="bi bi-plus-lg"></i> Create Workflow
                        </button>
                    </div>
                <?php else: ?>
                    <!-- Workflows Table -->
                    <div class="table-wrapper">
                        <table class="workflows-table">
                            <thead>
                                <tr>
                                    <th class="col-name">Name</th>
                                    <th class="col-projects">Projects</th>
                                    <th class="col-type">Type</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-actions text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($workflows as $workflow): ?>
                                    <tr class="workflow-row">
                                        <!-- Name -->
                                        <td class="col-name">
                                            <div class="workflow-name-cell">
                                                <div class="workflow-icon">
                                                    <i class="bi bi-diagram-3"></i>
                                                </div>
                                                <div class="workflow-info">
                                                    <div class="workflow-name"><?= htmlspecialchars($workflow['name']) ?></div>
                                                    <div class="workflow-description"><?= htmlspecialchars($workflow['description'] ?? 'No description') ?></div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Projects Count -->
                                        <td class="col-projects">
                                            <span class="count-badge">
                                                <?= $workflow['project_count'] ?> <?= $workflow['project_count'] === 1 ? 'Project' : 'Projects' ?>
                                            </span>
                                        </td>

                                        <!-- Type Badge -->
                                        <td class="col-type">
                                            <?php if ($workflow['is_default']): ?>
                                                <span class="type-badge default">
                                                    <i class="bi bi-star-fill"></i> Default
                                                </span>
                                            <?php else: ?>
                                                <span class="type-badge custom">
                                                    Custom
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Status -->
                                        <td class="col-status">
                                            <span class="status-badge active">
                                                <span class="status-dot"></span>
                                                Active
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="col-actions">
                                            <div class="action-buttons">
                                                <a href="<?= url('/admin/workflows/' . $workflow['id']) ?>" class="icon-button" title="View workflow">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if (!$workflow['is_default'] && $workflow['project_count'] == 0): ?>
                                                    <button type="button" class="icon-button danger" title="Delete workflow"
                                                        onclick="confirmDelete(<?= $workflow['id'] ?>, '<?= htmlspecialchars($workflow['name']) ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create Workflow Modal -->
<div class="modal fade" id="createWorkflowModal" tabindex="-1" aria-labelledby="createWorkflowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createWorkflowModalLabel">
                    <i class="bi bi-plus-lg"></i> Create New Workflow
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('/admin/workflows') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <!-- Workflow Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">
                            Workflow Name <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name" required
                            placeholder="e.g. Software Development Workflow"
                            maxlength="255">
                        <div class="form-hint">Give your workflow a clear, descriptive name</div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            placeholder="Describe what this workflow is used for..."
                            maxlength="1000"></textarea>
                        <div class="form-hint">Optional: Provide context about when to use this workflow</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Create Workflow
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteWorkflowForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
    function confirmDelete(id, name) {
        if (confirm(`Are you sure you want to delete the workflow "${name}"? This action cannot be undone.`)) {
            const form = document.getElementById('deleteWorkflowForm');
            form.action = `<?= url('/admin/workflows/') ?>${id}`;
            form.submit();
        }
    }
</script>

<style>
    /* ============================================
    WORKFLOWS ADMIN PAGE - ENTERPRISE DESIGN
    ============================================ */

    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-dark: #161B22 !important;
        --jira-gray: #626F86 !important;
        --jira-light: #F7F8FA !important;
        --jira-border: #DFE1E6 !important;
        --color-success: #216E4E !important;
        --color-error: #ED3C32 !important;
    }

    /* Main Wrapper */
    .workflows-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: var(--jira-light);
        overflow: hidden;
        margin-top: -1.5rem;
        padding-top: 1.5rem;
        max-width: 100%;
        width: 100%;
    }

    /* Breadcrumb Navigation */
    .workflows-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 32px;
        background: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        font-size: 13px;
        flex-shrink: 0;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--jira-gray);
        font-weight: 300;
    }

    .breadcrumb-current {
        color: var(--jira-dark);
        font-weight: 600;
    }

    /* Page Header */
    .workflows-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 32px;
        padding: 32px;
        background: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        flex-shrink: 0;
    }

    .workflows-header-left {
        flex: 1;
    }

    .workflows-title {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        color: var(--jira-dark);
        letter-spacing: -0.3px;
    }

    .workflows-subtitle {
        margin: 8px 0 0 0;
        font-size: 15px;
        color: var(--jira-gray);
    }

    /* Header Actions */
    .workflows-header-actions {
        display: flex;
        gap: 12px;
        flex-shrink: 0;
    }

    .action-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 20px;
        background: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        color: var(--jira-dark);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .action-button:hover {
        background: var(--jira-light);
        border-color: var(--jira-blue);
        color: var(--jira-blue);
    }

    .action-button.primary {
        background: var(--jira-blue);
        color: #FFFFFF;
        border-color: var(--jira-blue);
    }

    .action-button.primary:hover {
        background: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Main Content Area */
    .workflows-content {
        flex: 1;
        overflow-y: auto;
        padding: 32px;
        display: flex;
        flex-direction: column;
    }

    /* Card Styling */
    .workflows-card {
        background: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .card-header-bar {
        padding: 20px;
        border-bottom: 1px solid var(--jira-border);
        flex-shrink: 0;
    }

    .card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--jira-dark);
    }

    /* Workflows Container */
    .workflows-container {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 80px 20px;
        color: var(--jira-gray);
        text-align: center;
    }

    .empty-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.7;
    }

    .empty-title {
        margin: 0 0 12px 0;
        font-size: 18px;
        font-weight: 600;
        color: var(--jira-dark);
    }

    .empty-text {
        margin: 0 0 24px 0;
        font-size: 14px;
        color: var(--jira-gray);
        max-width: 400px;
    }

    .btn-create-empty {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--jira-blue);
        color: #FFFFFF;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-create-empty:hover {
        background: var(--jira-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Table Wrapper */
    .table-wrapper {
        overflow-x: auto;
        flex: 1;
    }

    .workflows-table {
        width: 100%;
        border-collapse: collapse;
    }

    .workflows-table thead {
        background: #F7F8FA;
        position: sticky;
        top: 0;
        z-index: 5;
    }

    .workflows-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--jira-border);
    }

    /* Table Columns */
    .col-name {
        width: 40%;
    }

    .col-projects {
        width: 20%;
    }

    .col-type {
        width: 15%;
    }

    .col-status {
        width: 15%;
    }

    .col-actions {
        width: 10%;
        text-align: right;
    }

    /* Table Rows */
    .workflow-row {
        border-bottom: 1px solid var(--jira-border);
        transition: all 0.15s ease;
    }

    .workflow-row:hover {
        background: var(--jira-light);
    }

    .workflows-table td {
        padding: 16px;
        vertical-align: middle;
        font-size: 14px;
        color: var(--jira-dark);
    }

    /* Workflow Name Cell */
    .workflow-name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .workflow-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--jira-blue), var(--jira-blue-dark));
        color: #FFFFFF;
        border-radius: 8px;
        font-size: 18px;
        flex-shrink: 0;
    }

    .workflow-info {
        flex: 1;
        min-width: 0;
    }

    .workflow-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--jira-dark);
        margin-bottom: 4px;
    }

    .workflow-description {
        font-size: 12px;
        color: var(--jira-gray);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Badges */
    .count-badge {
        display: inline-block;
        padding: 4px 12px;
        background: var(--jira-light);
        border: 1px solid var(--jira-border);
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        color: var(--jira-gray);
    }

    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .type-badge.default {
        background: #E3F2FD;
        color: var(--jira-blue);
    }

    .type-badge.custom {
        background: #F3E5F5;
        color: #6A1B9A;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: #E8F5E9;
        color: var(--color-success);
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        background: var(--color-success);
        border-radius: 50%;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: flex-end;
    }

    .icon-button {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        color: var(--jira-gray);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 16px;
    }

    .icon-button:hover {
        background: var(--jira-light);
        border-color: var(--jira-blue);
        color: var(--jira-blue);
    }

    .icon-button.danger:hover {
        border-color: var(--color-error);
        color: var(--color-error);
        background: #ffebee;
    }

    /* Modal Styles */
    .modal-dialog {
        max-width: 500px;
    }

    .modal-content {
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--jira-border);
        background: #FFFFFF;
    }

    .modal-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        font-weight: 700;
        color: var(--jira-dark);
    }

    .btn-close {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        color: var(--jira-gray);
        cursor: pointer;
        opacity: 1;
        transition: all 0.2s ease;
    }

    .btn-close:hover {
        color: var(--jira-dark);
    }

    /* Form Groups */
    .modal-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        color: var(--jira-dark);
    }

    .required {
        color: var(--color-error);
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        font-size: 14px;
        color: var(--jira-dark);
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
    }

    .form-hint {
        margin-top: 6px;
        font-size: 12px;
        color: var(--jira-gray);
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--jira-border);
        background: #FFFFFF;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-secondary {
        background: var(--jira-light);
        color: var(--jira-dark);
        border: 1px solid var(--jira-border);
    }

    .btn-secondary:hover {
        background: #E4E7EB;
    }

    .btn-primary {
        background: var(--jira-blue);
        color: #FFFFFF;
    }

    .btn-primary:hover {
        background: var(--jira-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .workflows-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }

        .workflows-header-actions {
            width: 100%;
        }

        .col-name {
            width: 35%;
        }

        .col-projects {
            width: 22%;
        }

        .col-type {
            width: 18%;
        }

        .col-status {
            width: 15%;
        }

        .col-actions {
            width: 10%;
        }
    }

    @media (max-width: 768px) {
        .workflows-wrapper {
            margin-top: -1rem;
            padding-top: 1rem;
        }

        .workflows-breadcrumb {
            padding: 12px 16px;
            font-size: 12px;
        }

        .workflows-header {
            padding: 20px 16px;
            gap: 16px;
        }

        .workflows-title {
            font-size: 24px;
        }

        .workflows-subtitle {
            font-size: 13px;
        }

        .workflows-content {
            padding: 16px;
        }

        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .workflows-table {
            min-width: 600px;
        }

        .col-name {
            width: 40%;
        }

        .col-projects {
            width: 20%;
        }

        .col-type {
            width: 20%;
        }

        .col-status {
            display: none;
        }

        .col-actions {
            width: 20%;
        }

        .workflow-description {
            display: none;
        }

        .card-header-bar {
            padding: 16px;
        }

        .empty-state {
            padding: 60px 16px;
        }

        .modal-dialog {
            margin: 0 16px;
        }
    }

    @media (max-width: 480px) {
        .workflows-title {
            font-size: 20px;
        }

        .workflows-subtitle {
            font-size: 12px;
        }

        .action-button {
            padding: 8px 12px;
            font-size: 12px;
        }

        .workflows-table th,
        .workflows-table td {
            padding: 12px 8px;
            font-size: 12px;
        }

        .col-name {
            width: 50%;
        }

        .col-projects {
            width: 25%;
        }

        .col-type {
            display: none;
        }

        .col-status {
            display: none;
        }

        .col-actions {
            width: 25%;
        }

        .workflow-icon {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .icon-button {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .modal-dialog {
            max-width: 100%;
            margin: 0 12px;
        }

        .modal-header {
            padding: 16px;
        }

        .modal-body {
            padding: 16px;
        }

        .modal-footer {
            padding: 12px 16px;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>
