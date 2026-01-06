<?php
/**
 * Admin Workflow Show View - Enterprise Jira Design
 */

use App\Core\View;

View::extends('layouts.app');
View::share('title', 'Workflow: ' . $workflow['name']);
View::section('content');
?>

<div class="page-wrapper wf-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= url('/admin') ?>" class="breadcrumb-link">
                        <i class="bi bi-gear"></i> Administration
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= url('/admin/workflows') ?>" class="breadcrumb-link">Workflows</a>
                </li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($workflow['name']) ?></li>
            </ol>
        </nav>
    </div>

    <!-- Page Header Section -->
    <div class="page-header">
        <div class="header-left">
            <div class="workflow-icon-circle">
                <i class="bi bi-diagram-2"></i>
            </div>
            <div class="header-info">
                <h1 class="page-title"><?= htmlspecialchars($workflow['name']) ?></h1>
                <p class="page-meta">
                    <span class="meta-item">
                        <i class="bi bi-circle-fill" style="color: #4CAF50; font-size: 0.5rem; margin-right: 4px;"></i>
                        Active
                    </span>
                    <span class="meta-separator">•</span>
                    <span class="meta-item" id="statusCount">
                        <i class="bi bi-list"></i> 
                        <span><?= count($statuses) ?> statuses</span>
                    </span>
                </p>
            </div>
        </div>
        <div class="header-actions">
            <button class="action-button" data-bs-toggle="modal" data-bs-target="#editWorkflowModal">
                <i class="bi bi-pencil"></i>
                <span>Edit</span>
            </button>
            <button class="action-button btn-danger" 
                    <?= ($workflow['is_default'] || $workflow['project_count'] > 0) ? 'disabled' : '' ?>
                    onclick="confirmDelete(<?= $workflow['id'] ?>, '<?= htmlspecialchars($workflow['name']) ?>')">
                <i class="bi bi-trash"></i>
                <span>Delete</span>
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="page-content">
        <!-- Left Column: Main Content -->
        <div class="content-left">
            <!-- Workflow Overview Card -->
            <div class="card-section">
                <div class="card-header">
                    <h3 class="card-title">Workflow Overview</h3>
                </div>
                <div class="card-body">
                    <div class="overview-grid">
                        <div class="overview-item">
                            <div class="overview-label">Workflow Name</div>
                            <div class="overview-value"><?= htmlspecialchars($workflow['name']) ?></div>
                        </div>
                        <div class="overview-item">
                            <div class="overview-label">Status</div>
                            <div class="overview-value">
                                <span class="status-badge status-active">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Active
                                </span>
                            </div>
                        </div>
                        <?php if ($workflow['description']): ?>
                            <div class="overview-item full-width">
                                <div class="overview-label">Description</div>
                                <div class="overview-value"><?= nl2br(htmlspecialchars($workflow['description'])) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Statuses and Transitions Grid -->
            <div class="workflow-grid">
                <!-- Statuses Card -->
                <div class="card-section">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-list-check"></i>
                            Statuses
                        </h3>
                        <button class="action-button small" data-bs-toggle="modal" data-bs-target="#addStatusModal">
                            <i class="bi bi-plus-lg"></i>
                            <span>Add Status</span>
                        </button>
                    </div>
                    <div class="card-body card-body-table">
                        <?php if (!empty($statuses)): ?>
                            <div class="status-list">
                                <?php foreach ($statuses as $status): ?>
                                    <div class="status-item">
                                        <div class="status-info">
                                            <div class="status-header">
                                                <span class="status-dot" style="background-color: <?= htmlspecialchars($status['color']) ?>"></span>
                                                <span class="status-name"><?= htmlspecialchars($status['name']) ?></span>
                                                <?php if ($status['is_initial'] ?? false): ?>
                                                    <span class="status-badge status-initial">Initial</span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="status-category">
                                                <?= ucfirst(str_replace('_', ' ', $status['category'])) ?>
                                            </span>
                                        </div>
                                        <button type="button" 
                                                class="btn-remove"
                                                onclick="removeStatus(<?= $status['id'] ?>, '<?= htmlspecialchars($status['name']) ?>')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>No statuses defined yet.</p>
                                <p class="small text-muted">Add statuses to create the workflow structure.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Transitions Card -->
                <div class="card-section">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-arrow-left-right"></i>
                            Transitions
                        </h3>
                        <button class="action-button small" data-bs-toggle="modal" data-bs-target="#addTransitionModal">
                            <i class="bi bi-plus-lg"></i>
                            <span>Add Transition</span>
                        </button>
                    </div>
                    <div class="card-body card-body-table">
                        <?php if (!empty($transitions)): ?>
                            <div class="transition-list">
                                <?php foreach ($transitions as $transition): ?>
                                    <div class="transition-item">
                                        <div class="transition-flow">
                                            <div class="transition-from">
                                                <?php if ($transition['from_status_id']): ?>
                                                    <span class="transition-badge">
                                                        <?= htmlspecialchars($transition['from_status_name']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="transition-badge badge-any">Any Status</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="transition-arrow">
                                                <i class="bi bi-arrow-right"></i>
                                            </div>
                                            <div class="transition-to">
                                                <span class="transition-badge">
                                                    <?= htmlspecialchars($transition['to_status_name']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="transition-name">
                                            <?= htmlspecialchars($transition['name']) ?>
                                        </div>
                                        <button type="button" 
                                                class="btn-remove"
                                                onclick="removeTransition(<?= $transition['id'] ?>, '<?= htmlspecialchars($transition['name']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>No transitions defined yet.</p>
                                <p class="small text-muted">Create transitions to define the workflow path.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="content-right">
            <!-- Quick Stats Card -->
            <div class="card-section sidebar-card">
                <div class="card-header">
                    <h3 class="card-title">Quick Stats</h3>
                </div>
                <div class="card-body">
                    <div class="stat-item">
                        <div class="stat-value"><?= count($statuses) ?></div>
                        <div class="stat-label">Statuses</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= count($transitions) ?></div>
                        <div class="stat-label">Transitions</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $workflow['project_count'] ?? 0 ?></div>
                        <div class="stat-label">Used in Projects</div>
                    </div>
                </div>
            </div>

            <!-- Workflow Status Card -->
            <div class="card-section sidebar-card">
                <div class="card-header">
                    <h3 class="card-title">Workflow Status</h3>
                </div>
                <div class="card-body">
                    <div class="status-info-item">
                        <span class="status-info-label">Current Status:</span>
                        <span class="status-info-value">
                            <i class="bi bi-check-circle-fill" style="color: #4CAF50;"></i>
                            Active
                        </span>
                    </div>
                    <div class="status-info-item">
                        <span class="status-info-label">Default Workflow:</span>
                        <span class="status-info-value">
                            <?= $workflow['is_default'] ? '✓ Yes' : '✗ No' ?>
                        </span>
                    </div>
                    <div class="status-info-item">
                        <span class="status-info-label">In Use:</span>
                        <span class="status-info-value">
                            <?= ($workflow['project_count'] ?? 0) > 0 ? 'Yes (' . $workflow['project_count'] . ' projects)' : 'Not used' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card-section sidebar-card">
                <div class="card-header">
                    <h3 class="card-title">Need Help?</h3>
                </div>
                <div class="card-body">
                    <p class="help-text">
                        Create statuses to define the possible states of an issue. Use transitions to define which status changes are allowed.
                    </p>
                    <ul class="help-list">
                        <li>Status: The state of an issue</li>
                        <li>Transition: Allowed change between states</li>
                        <li>Initial Status: Where new issues start</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Workflow Modal -->
<div class="modal fade" id="editWorkflowModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Workflow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/admin/workflows/' . $workflow['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name" class="form-label">Workflow Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($workflow['name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($workflow['description'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Status Modal -->
<div class="modal fade" id="addStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Status to Workflow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/admin/workflows/' . $workflow['id'] . '/statuses') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status_id" class="form-label">Select Status <span class="required">*</span></label>
                        <select class="form-select" id="status_id" name="status_id" required>
                            <option value="">-- Choose a status --</option>
                            <?php
                            $wfStatusIds = array_column($statuses, 'id');
                            foreach ($allStatuses as $s):
                                if (in_array($s['id'], $wfStatusIds))
                                    continue;
                                ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= ucfirst($s['category'] ?? 'todo') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-hint">
                            Can't find the status? <a href="<?= url('/admin/statuses') ?>">Create a new system status</a>.
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_initial" value="1" id="is_initial" <?= empty($statuses) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_initial">
                            Set as initial status
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Transition Modal -->
<div class="modal fade" id="addTransitionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Transition</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/admin/workflows/' . $workflow['id'] . '/transitions') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="t_name" class="form-label">Transition Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="t_name" name="name" required placeholder="e.g. Start Progress">
                    </div>
                    <div class="form-group">
                        <label for="from_status_id" class="form-label">From Status</label>
                        <select class="form-select" id="from_status_id" name="from_status_id">
                            <option value="">Any Status (Global Transition)</option>
                            <?php foreach ($statuses as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="to_status_id" class="form-label">To Status <span class="required">*</span></label>
                        <select class="form-select" id="to_status_id" name="to_status_id" required>
                            <option value="">-- Choose target status --</option>
                            <?php foreach ($statuses as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Transition</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Action Forms -->
<form id="removeStatusForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<form id="removeTransitionForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<form id="deleteWorkflowForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
function removeStatus(statusId, name) {
    if (confirm(`Are you sure you want to remove the status "${name}" from this workflow?`)) {
        const form = document.getElementById('removeStatusForm');
        form.action = `<?= url('/admin/workflows/' . $workflow['id'] . '/statuses/') ?>${statusId}`;
        form.submit();
    }
}

function removeTransition(transitionId, name) {
    if (confirm(`Are you sure you want to delete the transition "${name}"?`)) {
        const form = document.getElementById('removeTransitionForm');
        form.action = `<?= url('/admin/workflows/' . $workflow['id'] . '/transitions/') ?>${transitionId}`;
        form.submit();
    }
}

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
   WORKFLOW PAGE - ENTERPRISE DESIGN
   ============================================ */

:root {
    --wf-primary: #8B1956 !important;
    --wf-primary-dark: #6F123F !important;
    --wf-text-dark: #161B22 !important;
    --wf-text-light: #626F86 !important;
    --wf-bg-light: #F7F8FA !important;
    --wf-border: #DFE1E6 !important;
    --wf-success: #4CAF50 !important;
    --wf-danger: #ED3C32 !important;
    --wf-info: #0052CC !important;
}

.wf-wrapper {
    display: flex;
    flex-direction: column;
}

/* ---- Breadcrumb ---- */
.breadcrumb-section {
    background: white;
    border-bottom: 1px solid var(--wf-border);
    padding: 12px 32px;
    position: sticky;
    top: 0;
    z-index: 50;
}

.breadcrumb {
    margin: 0;
    font-size: 13px;
}

.breadcrumb-item {
    display: inline-flex;
    align-items: center;
}

.breadcrumb-link {
    color: var(--wf-primary);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s;
}

.breadcrumb-link:hover {
    color: var(--wf-primary-dark);
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: var(--wf-text-light);
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    margin: 0 8px;
    color: var(--wf-border);
}

/* ---- Page Header ---- */
.page-header {
    background: white;
    border-bottom: 1px solid var(--wf-border);
    padding: 32px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
}

.header-left {
    display: flex;
    gap: 20px;
    align-items: flex-start;
    flex: 1;
}

.workflow-icon-circle {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    background: linear-gradient(135deg, rgba(139, 25, 86, 0.1), rgba(231, 120, 23, 0.1));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: var(--wf-primary);
    flex-shrink: 0;
}

.header-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    color: var(--wf-text-dark);
    margin: 0;
    line-height: 1.2;
}

.page-meta {
    font-size: 13px;
    color: var(--wf-text-light);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

.meta-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.meta-separator {
    color: var(--wf-border);
}

.header-actions {
    display: flex;
    gap: 12px;
    flex-shrink: 0;
}

.action-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 1px solid var(--wf-border);
    border-radius: 6px;
    color: var(--wf-text-dark);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.action-button:hover:not(:disabled) {
    background: white;
    border-color: var(--wf-primary);
    color: var(--wf-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 25, 86, 0.15);
}

.action-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.action-button.small {
    padding: 8px 12px;
    font-size: 12px;
}

.action-button.btn-danger {
    border-color: #FDD;
    color: var(--wf-danger);
}

.action-button.btn-danger:hover:not(:disabled) {
    border-color: var(--wf-danger);
    background: rgba(237, 60, 50, 0.05);
}

/* ---- Main Content ---- */
.page-content {
    display: flex;
    gap: 24px;
    padding: 32px;
    background: var(--wf-bg-light);
    min-height: calc(100vh - 300px);
}

.content-left {
    flex: 1;
}

.content-right {
    width: 280px;
    flex-shrink: 0;
}

/* ---- Cards ---- */
.card-section {
    background: white;
    border: 1px solid var(--wf-border);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    transition: all 0.2s;
}

.card-section:hover {
    border-color: #B6C2CF;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.card-header {
    background: white;
    border-bottom: 1px solid var(--wf-border);
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.card-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--wf-text-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-body {
    padding: 20px;
}

.card-body-table {
    padding: 0;
}

/* ---- Overview Grid ---- */
.overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.overview-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.overview-item.full-width {
    grid-column: 1 / -1;
}

.overview-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--wf-text-light);
    letter-spacing: 0.5px;
}

.overview-value {
    font-size: 14px;
    font-weight: 500;
    color: var(--wf-text-dark);
}

/* ---- Status List ---- */
.status-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: #FAFBFC;
    border-radius: 6px;
    border: 1px solid var(--wf-border);
    transition: all 0.2s;
}

.status-item:hover {
    background: white;
    border-color: #B6C2CF;
}

.status-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.status-header {
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

.status-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--wf-text-dark);
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    background: #E3F2FD;
    color: #0052CC;
    white-space: nowrap;
}

.status-badge.status-active {
    background: #E8F5E9;
    color: var(--wf-success);
}

.status-badge.status-initial {
    background: #FFF3E0;
    color: #E77817;
}

.status-category {
    font-size: 12px;
    color: var(--wf-text-light);
}

/* ---- Transition List ---- */
.transition-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.transition-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: #FAFBFC;
    border-radius: 6px;
    border: 1px solid var(--wf-border);
    gap: 12px;
    transition: all 0.2s;
}

.transition-item:hover {
    background: white;
    border-color: #B6C2CF;
}

.transition-flow {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.transition-from,
.transition-to {
    flex-shrink: 0;
}

.transition-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    background: #E3F2FD;
    color: #0052CC;
    border: 1px solid #90CAF9;
}

.transition-badge.badge-any {
    background: #F5F5F5;
    color: #666;
    border-color: #D0D0D0;
}

.transition-arrow {
    color: var(--wf-text-light);
    font-size: 16px;
}

.transition-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--wf-text-dark);
}

/* ---- Empty State ---- */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state i {
    font-size: 48px;
    color: var(--wf-border);
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state p {
    color: var(--wf-text-light);
    margin: 8px 0;
}

/* ---- Sidebar ---- */
.sidebar-card {
    margin-bottom: 20px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 16px;
    text-align: center;
    border-bottom: 1px solid var(--wf-border);
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--wf-primary);
}

.stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--wf-text-light);
}

.status-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--wf-border);
    font-size: 13px;
}

.status-info-item:last-child {
    border-bottom: none;
}

.status-info-label {
    color: var(--wf-text-light);
    font-weight: 600;
}

.status-info-value {
    color: var(--wf-text-dark);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.help-text {
    font-size: 13px;
    color: var(--wf-text-light);
    margin-bottom: 12px;
    line-height: 1.5;
}

.help-list {
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 12px;
    color: var(--wf-text-light);
}

.help-list li {
    padding: 6px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.help-list li::before {
    content: "•";
    color: var(--wf-primary);
    font-weight: bold;
}

/* ---- Buttons ---- */
.btn-remove {
    background: none;
    border: none;
    color: var(--wf-text-light);
    cursor: pointer;
    padding: 4px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-remove:hover {
    color: var(--wf-danger);
    transform: scale(1.15);
}

/* ---- Forms ---- */
.form-group {
    margin-bottom: 16px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--wf-text-dark);
    margin-bottom: 6px;
}

.required {
    color: var(--wf-danger);
}

.form-control,
.form-select {
    font-size: 13px;
    border-color: var(--wf-border);
}

.form-control:focus,
.form-select:focus {
    border-color: var(--wf-primary);
    box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
}

.form-hint {
    font-size: 12px;
    color: var(--wf-text-light);
    margin-top: 6px;
}

.form-check-label {
    font-size: 13px;
    color: var(--wf-text-dark);
    cursor: pointer;
}

/* ---- Modal ---- */
.modal-content {
    border: 1px solid var(--wf-border);
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.modal-header {
    background: white;
    border-bottom: 1px solid var(--wf-border);
    padding: 20px;
}

.modal-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--wf-text-dark);
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    background: #FAFBFC;
    border-top: 1px solid var(--wf-border);
    padding: 16px;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.modal-footer .btn {
    font-size: 13px;
    font-weight: 600;
    padding: 8px 16px;
}

.modal-footer .btn-primary {
    background: var(--wf-primary);
    border-color: var(--wf-primary);
}

.modal-footer .btn-primary:hover {
    background: var(--wf-primary-dark);
    border-color: var(--wf-primary-dark);
}

.modal-footer .btn-outline-secondary {
    color: var(--wf-text-light);
    border-color: var(--wf-border);
}

.modal-footer .btn-outline-secondary:hover {
    color: var(--wf-text-dark);
    border-color: var(--wf-text-light);
    background: transparent;
}

/* ---- Grid Layout ---- */
.workflow-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

/* ---- Responsive ---- */
@media (max-width: 1024px) {
    .page-content {
        flex-direction: column;
        padding: 20px;
    }

    .content-right {
        width: 100%;
    }

    .workflow-grid {
        grid-template-columns: 1fr;
    }

    .page-header {
        flex-direction: column;
        padding: 20px;
    }

    .header-actions {
        width: 100%;
    }

    .action-button {
        flex: 1;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .page-header {
        padding: 16px;
    }

    .header-left {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .header-actions {
        width: 100%;
        flex-direction: column;
    }

    .action-button {
        width: 100%;
    }

    .page-content {
        padding: 16px;
        gap: 16px;
    }

    .overview-grid {
        grid-template-columns: 1fr;
    }

    .transition-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .transition-flow {
        width: 100%;
    }

    .breadcrumb-section {
        padding: 12px 16px;
    }
}

@media (max-width: 480px) {
    .page-header {
        padding: 12px;
    }

    .page-title {
        font-size: 24px;
    }

    .workflow-icon-circle {
        width: 48px;
        height: 48px;
        font-size: 24px;
    }

    .page-content {
        padding: 12px;
    }

    .status-item,
    .transition-item {
        padding: 12px;
    }

    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .card-header .action-button {
        width: 100%;
    }
}
</style>

<?php View::endSection() ?>
