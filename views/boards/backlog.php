<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="jira-project-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="project-breadcrumb">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/projects/' . $project['key']) ?>" class="breadcrumb-link">
            <?= e($project['key']) ?>
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Backlog</span>
    </div>

    <!-- Page Header -->
    <div class="project-header">
        <div class="project-header-left">
            <div class="project-info">
                <h1 class="project-title">Backlog</h1>
                <div class="project-meta">
                    <span class="project-key"><?= e($board['name']) ?></span>
                    <span class="project-category"><?= ucfirst($board['type']) ?> Board</span>
                </div>
            </div>
        </div>
        <div class="project-header-actions">
            <button class="action-button" data-bs-toggle="modal" data-bs-target="#createSprintModal">
                <i class="bi bi-plus-lg"></i>
                <span>Create Sprint</span>
            </button>
            <a href="<?= url('/projects/' . $project['key'] . '/issues/create') ?>" class="btn-quick-action primary">
                <i class="bi bi-plus-lg"></i>
                <span>Create Issue</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="project-content">
        <!-- Left Column: Sprints & Backlog -->
        <div class="content-left">

            <!-- Sprints List -->
            <?php foreach ($sprints as $sprint): ?>
                <div class="sprint-section card-box" data-sprint-id="<?= $sprint['id'] ?>">
                    <div class="sprint-header card-header-bar" data-bs-toggle="collapse"
                        data-bs-target="#sprint-<?= $sprint['id'] ?>">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-chevron-down toggle-icon"></i>
                            <div>
                                <strong class="card-title"><?= e($sprint['name']) ?></strong>
                                <span
                                    class="status-badge bg-<?= $sprint['status'] === 'active' ? 'success' : 'secondary' ?> ms-2">
                                    <?= ucfirst($sprint['status']) ?>
                                </span>
                            </div>
                            <span class="text-muted small ms-2">
                                <?php if ($sprint['start_date'] && $sprint['end_date']): ?>
                                    <?= format_date($sprint['start_date']) ?> - <?= format_date($sprint['end_date']) ?>
                                <?php else: ?>
                                    Dates not set
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="sprint-stats">
                                <span class="badge bg-light text-dark border"><?= count($sprint['issues'] ?? []) ?>
                                    issues</span>
                                <span
                                    class="badge bg-light text-dark border"><?= array_sum(array_column($sprint['issues'] ?? [], 'story_points')) ?>
                                    pts</span>
                            </div>

                            <?php if ($sprint['status'] === 'planned' && can('manage-sprints', $project['id'])): ?>
                                <button class="btn btn-sm btn-success"
                                    onclick="event.stopPropagation(); startSprint(<?= $sprint['id'] ?>)">
                                    Start Sprint
                                </button>
                            <?php endif; ?>

                            <div class="dropdown" onclick="event.stopPropagation();">
                                <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target="#editSprintModal-<?= $sprint['id'] ?>">
                                            <i class="bi bi-pencil me-2"></i> Edit Sprint
                                        </a></li>
                                    <?php if ($sprint['status'] !== 'active'): ?>
                                        <li>
                                            <form
                                                action="<?= url('/projects/' . $project['key'] . '/sprints/' . $sprint['id']) ?>"
                                                method="POST"
                                                onsubmit="return confirm('Delete this sprint? Issues will be moved to backlog.')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i> Delete Sprint
                                                </button>
                                            </form>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="collapse show" id="sprint-<?= $sprint['id'] ?>">
                        <div class="sprint-body" data-sprint-id="<?= $sprint['id'] ?>">
                            <?php if (empty($sprint['issues'])): ?>
                                <div class="empty-drop-zone">
                                    <i class="bi bi-inbox fs-4 mb-2"></i>
                                    <p>Plan this sprint by dragging issues here</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($sprint['issues'] as $issue): ?>
                                    <div class="backlog-item issue-item" draggable="true" data-issue-id="<?= $issue['id'] ?>">
                                        <div class="issue-item-left">
                                            <i class="bi bi-grip-vertical text-muted me-2" style="cursor: grab;"></i>
                                            <input type="checkbox" class="issue-checkbox form-check-input me-3"
                                                value="<?= $issue['id'] ?>">
                                            <span class="issue-type-icon me-2" style="color: <?= e($issue['issue_type_color']) ?>">
                                                <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                                            </span>
                                            <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="issue-key-link me-2">
                                                <?= e($issue['issue_key']) ?>
                                            </a>
                                            <span class="issue-summary"><?= e($issue['summary']) ?></span>
                                        </div>
                                        <div class="issue-item-right d-flex align-items-center gap-3">
                                            <span class="priority-badge"
                                                style="background-color: <?= e($issue['priority_color']) ?>">
                                                <?= e($issue['priority_name']) ?>
                                            </span>
                                            <span class="status-badge-sm"
                                                style="background-color: <?= e($issue['status_color']) ?>">
                                                <?= e($issue['status_name']) ?>
                                            </span>
                                            <span class="points-badge"><?= $issue['story_points'] ?? '-' ?></span>
                                            <?php if ($issue['assignee_name']): ?>
                                                <div class="avatar-circle" title="<?= e($issue['assignee_name']) ?>">
                                                    <?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Backlog Section -->
            <div class="sprint-section card-box" data-sprint-id="backlog">
                <div class="sprint-header card-header-bar" data-bs-toggle="collapse" data-bs-target="#backlog-items">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-chevron-down toggle-icon"></i>
                        <div>
                            <strong class="card-title">Backlog</strong>
                            <span class="project-badge ms-2">Unscheduled</span>
                        </div>
                    </div>
                    <span class="text-muted fw-medium"><?= count($backlogIssues ?? []) ?> issues</span>
                </div>

                <div class="collapse show" id="backlog-items">
                    <div class="sprint-body" data-sprint-id="backlog">
                        <?php if (empty($backlogIssues)): ?>
                            <div class="empty-drop-zone">
                                <i class="bi bi-layers fs-4 mb-2 opacity-50"></i>
                                <p>Your backlog is empty</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($backlogIssues as $issue): ?>
                                <div class="backlog-item issue-item" draggable="true" data-issue-id="<?= $issue['id'] ?>">
                                    <div class="issue-item-left">
                                        <i class="bi bi-grip-vertical text-muted me-2" style="cursor: grab;"></i>
                                        <input type="checkbox" class="issue-checkbox form-check-input me-3"
                                            value="<?= $issue['id'] ?>">
                                        <span class="issue-type-icon me-2" style="color: <?= e($issue['issue_type_color']) ?>">
                                            <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                                        </span>
                                        <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="issue-key-link me-2">
                                            <?= e($issue['issue_key']) ?>
                                        </a>
                                        <span class="issue-summary"><?= e($issue['summary']) ?></span>
                                    </div>
                                    <div class="issue-item-right d-flex align-items-center gap-3">
                                        <span class="priority-badge"
                                            style="background-color: <?= e($issue['priority_color']) ?>">
                                            <?= e($issue['priority_name']) ?>
                                        </span>
                                        <span class="status-badge-sm"
                                            style="background-color: <?= e($issue['status_color']) ?>">
                                            <?= e($issue['status_name']) ?>
                                        </span>
                                        <span class="points-badge"><?= $issue['story_points'] ?? '-' ?></span>
                                        <?php if ($issue['assignee_name'] ?? null): ?>
                                            <div class="avatar-circle" title="<?= e($issue['assignee_name']) ?>">
                                                <?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="content-right">
            <!-- Board Details -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Board Details</h3>
                <div class="details-list">
                    <div class="detail-item">
                        <span class="detail-label">Board Name</span>
                        <span class="detail-value"><?= e($board['name']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Project</span>
                        <span class="detail-value"><?= e($project['name']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Owner</span>
                        <span class="detail-value">
                            <?php if ($board['owner_name']): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs"><?= strtoupper(substr($board['owner_name'], 0, 1)) ?></div>
                                    <?= e($board['owner_name']) ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">Unassigned</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Sprint Count</span>
                        <span class="detail-value"><?= count($sprints) ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Backlog Health</h3>
                <div class="p-3">
                    <p class="text-muted small mb-0">
                        Total Issues:
                        <strong><?= count($backlogIssues) + array_sum(array_map(fn($s) => count($s['issues'] ?? []), $sprints)) ?></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar (Sticky) -->
<div class="bulk-actions d-none" id="bulkActions">
    <div class="d-flex justify-content-between align-items-center w-100">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary rounded-pill"><span id="selectedCount">0</span></span>
            <span class="fw-medium text-dark">Issues Selected</span>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm border-secondary" id="bulkMoveTo"
                style="width: auto; min-width: 200px;">
                <option value="">Move to sprint...</option>
                <?php foreach ($sprints as $sprint): ?>
                    <option value="<?= $sprint['id'] ?>"><?= e($sprint['name']) ?></option>
                <?php endforeach; ?>
                <option value="backlog">Backlog</option>
            </select>
            <button class="btn btn-sm btn-primary" onclick="bulkMove()">Move Issues</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">Cancel</button>
        </div>
    </div>
</div>

<!-- Create Sprint Modal -->
<div class="modal fade" id="createSprintModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Sprint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createSprintForm" action="<?= url('/projects/' . $project['key'] . '/sprints') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sprint Name</label>
                        <input type="text" name="name" class="form-control" required
                            value="<?= e($project['key']) ?> Sprint <?= count($sprints) + 1 ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Goal</label>
                        <textarea name="goal" class="form-control" rows="2"
                            placeholder="What do you want to achieve?"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Sprint</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- STYLES -->
<style>
    /* ============================================
    JIRA BACKLOG - ENTERPRISE DESIGN
    ============================================ */
    :root {
        --jira-blue: #8B1956;
        --jira-blue-dark: #6F123F;
        --jira-dark: #161B22;
        --jira-gray: #626F86;
        --jira-light: #F7F8FA;
        --jira-border: #DFE1E6;
    }

    /* Layout & Wrapper */
    .jira-project-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #F7F8FA;
        overflow: hidden;
        margin-top: -1.5rem;
        /* Offset parent padding */
        padding-top: 1.5rem;
    }

    /* Breadcrumbs */
    .project-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 32px;
        background: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        flex-shrink: 0;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
        font-size: 13px;
    }

    .breadcrumb-separator {
        color: var(--jira-gray);
        font-size: 13px;
    }

    .breadcrumb-current {
        color: var(--jira-dark);
        font-weight: 600;
        font-size: 13px;
    }

    /* Header */
    .project-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 32px;
        background: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
    }

    .project-title {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: var(--jira-dark);
    }

    .project-meta {
        margin-top: 6px;
        display: flex;
        gap: 8px;
    }

    .project-key {
        background: var(--jira-light);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        color: var(--jira-gray);
        border: 1px solid var(--jira-border);
    }

    .project-category {
        font-size: 13px;
        color: var(--jira-gray);
    }

    .project-header-actions {
        display: flex;
        gap: 12px;
    }

    .action-button {
        background: white;
        border: 1px solid var(--jira-border);
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        color: var(--jira-dark);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .action-button:hover {
        background: var(--jira-light);
    }

    .btn-quick-action.primary {
        background-color: var(--jira-blue) !important;
        color: white !important;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        border: none;
    }

    .btn-quick-action.primary span {
        color: white !important;
    }

    .btn-quick-action.primary:hover {
        background-color: var(--jira-blue-dark) !important;
        color: white !important;
    }


    /* Main Content */
    .project-content {
        display: flex;
        gap: 24px;
        padding: 24px 32px;
        overflow-y: auto;
        flex: 1;
    }

    .content-left {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .content-right {
        width: 280px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Cards & Sprints */
    .card-box {
        background: white;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        overflow: hidden;
    }

    .card-header-bar {
        padding: 12px 16px;
        background: #F4F5F7;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s;
    }

    .card-header-bar:hover {
        background: #EBECF0;
    }

    .card-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--jira-dark);
    }

    .status-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
        text-transform: uppercase;
        font-weight: 700;
        color: white;
    }

    .bg-success {
        background-color: #00875A !important;
    }

    .bg-secondary {
        background-color: #626F86 !important;
    }

    /* Drag & Drop Zone */
    .sprint-body {
        padding: 4px;
        min-height: 80px;
        transition: all 0.2s;
        border-radius: 0 0 8px 8px;
    }

    .sprint-body.drag-over {
        background: #E6FCFF;
        box-shadow: inset 0 0 0 2px #00B8D9;
    }

    .empty-drop-zone {
        padding: 32px;
        text-align: center;
        border: 2px dashed var(--jira-border);
        border-radius: 6px;
        margin: 12px;
        color: var(--jira-gray);
    }

    /* Backlog/Issue Items */
    .backlog-item {
        background: white;
        border: 1px solid var(--jira-border);
        padding: 10px 16px;
        margin: 4px 0;
        border-radius: 3px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: grab;
        transition: all 0.1s;
    }

    .backlog-item:hover {
        background: #F4F5F7;
        border-color: #B6C2CF;
    }

    .backlog-item.dragging {
        opacity: 0.5;
        background: #E3FCEF;
        border-color: #00875A;
        transform: scale(0.99);
    }

    .issue-item-left {
        display: flex;
        align-items: center;
        flex: 1;
        min-width: 0;
    }

    .issue-key-link {
        font-family: monospace;
        font-weight: 600;
        color: var(--jira-gray);
        text-decoration: none;
        font-size: 12px;
    }

    .issue-key-link:hover {
        color: var(--jira-blue);
        text-decoration: underline;
    }

    .issue-summary {
        font-size: 14px;
        color: var(--jira-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding-right: 12px;
    }

    .priority-badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 3px;
        color: white;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-badge-sm {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 3px;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
    }

    .points-badge {
        background: #DFE1E6;
        color: var(--jira-dark);
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 600;
        min-width: 24px;
        text-align: center;
    }

    .avatar-circle {
        width: 24px;
        height: 24px;
        background: var(--jira-blue);
        color: white;
        border-radius: 50%;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .avatar-xs {
        width: 20px;
        height: 20px;
        background: var(--jira-gray);
        color: white;
        border-radius: 50%;
        font-size: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Sidebar */
    .sidebar-card {
        background: white;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        overflow: hidden;
    }

    .sidebar-card-title {
        padding: 16px;
        margin: 0;
        background: white;
        border-bottom: 1px solid var(--jira-border);
        font-size: 14px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .details-list {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }

    .detail-label {
        color: var(--jira-gray);
        font-weight: 500;
    }

    .detail-value {
        color: var(--jira-dark);
        font-weight: 600;
    }

    /* Bulk Actions */
    .bulk-actions {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        border: 1px solid var(--jira-border);
        z-index: 1000;
        width: 80%;
        max-width: 800px;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translate(-50%, 100%);
        }

        to {
            transform: translate(-50%, 0);
        }
    }

    .btn-icon {
        color: var(--jira-gray);
        padding: 4px 8px;
    }

    .btn-icon:hover {
        color: var(--jira-blue);
        background: #DEEAFE;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .content-right {
            display: none;
        }

        /* Hide sidebar on small screens for simple focus */
        .bulk-actions {
            width: 95%;
            bottom: 12px;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let draggedItem = null;

        // Drag and drop for backlog items
        document.querySelectorAll('.backlog-item').forEach(item => {
            item.addEventListener('dragstart', function (e) {
                draggedItem = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });

            item.addEventListener('dragend', function () {
                this.classList.remove('dragging');
                document.querySelectorAll('.sprint-body').forEach(body => body.classList.remove('drag-over'));
            });
        });

        document.querySelectorAll('.sprint-body').forEach(body => {
            body.addEventListener('dragover', function (e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });

            body.addEventListener('dragleave', function (e) {
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('drag-over');
                }
            });

            body.addEventListener('drop', async function (e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                if (draggedItem) {
                    const issueId = draggedItem.dataset.issueId;
                    const sprintId = this.dataset.sprintId;

                    // Remove empty state if present
                    const emptyState = this.querySelector('.empty-drop-zone');
                    if (emptyState) emptyState.remove();

                    // Move visually
                    this.appendChild(draggedItem);

                    // Update on server
                    try {
                        await api.put(`/api/v1/issues/${issueId}/sprint`, {
                            sprint_id: sprintId === 'backlog' ? null : sprintId
                        });
                        toast.show('Issue moved successfully', 'success');
                    } catch (error) {
                        console.error('Failed to move issue:', error);
                        toast.show('Failed to move issue', 'error');
                        location.reload(); // Revert on error
                    }
                }
            });
        });

        // Checkbox selection
        document.querySelectorAll('.issue-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });
    });

    function updateBulkActions() {
        const checked = document.querySelectorAll('.issue-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');

        if (checked.length > 0) {
            bulkActions.classList.remove('d-none');
            selectedCount.textContent = checked.length;
        } else {
            bulkActions.classList.add('d-none');
        }
    }

    function clearSelection() {
        document.querySelectorAll('.issue-checkbox:checked').forEach(cb => cb.checked = false);
        updateBulkActions();
    }

    async function bulkMove() {
        const sprintId = document.getElementById('bulkMoveTo').value;
        if (!sprintId) return toast.show('Please select a sprint', 'warning');

        const issueIds = Array.from(document.querySelectorAll('.issue-checkbox:checked')).map(cb => cb.value);
        if (issueIds.length === 0) return toast.show('Please select at least one issue', 'warning');

        try {
            const response = await api.post('/api/v1/issues/bulk-move', {
                issue_ids: issueIds,
                sprint_id: sprintId === 'backlog' ? null : sprintId
            });
            toast.show(response.message || 'Issues moved successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } catch (error) {
            console.error('Bulk move failed:', error);
            const message = error.data?.error || 'Failed to move issues';
            const details = error.data?.details ? '\n' + error.data.details.join('\n') : '';
            toast.show(message + details, 'error');
        }
    }

    // AJAX Sprint Creation
    const createSprintForm = document.getElementById('createSprintForm');
    if (createSprintForm) {
        createSprintForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';

            try {
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                // Ensure empty strings are treated as null/empty
                if (!data.goal) delete data.goal;
                if (!data.start_date) delete data.start_date;
                if (!data.end_date) delete data.end_date;

                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    location.reload();
                } else {
                    const result = await response.json();
                    alert(result.error || 'Failed to create sprint');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while creating the sprint');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    async function startSprint(sprintId) {
        if (!confirm('Start this sprint?')) return;

        try {
            await api.post(`/api/v1/sprints/${sprintId}/start`);
            toast.show('Sprint started', 'success');
            setTimeout(() => location.reload(), 1000);
        } catch (error) {
            toast.show('Failed to start sprint', 'error');
        }
    }


</script>
<?php \App\Core\View::endSection(); ?>