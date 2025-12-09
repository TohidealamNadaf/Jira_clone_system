<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('styles'); ?>
<style>
    .sprint-section {
        background: #f8f9fa;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    .sprint-header {
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    .sprint-header:hover {
        background: #e9ecef;
    }
    .sprint-body {
        padding: 0.5rem;
        min-height: 50px;
    }
    .backlog-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: grab;
    }
    .backlog-item:hover {
        border-color: #0d6efd;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .backlog-item.dragging {
        opacity: 0.5;
    }
    .sprint-body.drag-over {
        background: #e3f2fd;
        border: 2px dashed #0d6efd;
    }
    .issue-checkbox {
        width: 18px;
        height: 18px;
    }
    .bulk-actions {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 1rem;
        border-top: 1px solid #dee2e6;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }
</style>
<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/projects/' . $project['key']) ?>"><?= e($project['key']) ?></a></li>
                    <li class="breadcrumb-item active">Backlog</li>
                </ol>
            </nav>
            <h2 class="mb-0">Backlog</h2>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createSprintModal">
                <i class="bi bi-plus-lg me-1"></i> Create Sprint
            </button>
            <a href="<?= url('/projects/' . $project['key'] . '/issues/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Create Issue
            </a>
        </div>
    </div>

    <!-- Sprints -->
    <?php foreach ($sprints as $sprint): ?>
    <div class="sprint-section" data-sprint-id="<?= $sprint['id'] ?>">
        <div class="sprint-header" data-bs-toggle="collapse" data-bs-target="#sprint-<?= $sprint['id'] ?>">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-chevron-down"></i>
                <div>
                    <strong><?= e($sprint['name']) ?></strong>
                    <span class="badge bg-<?= $sprint['status'] === 'active' ? 'success' : 'secondary' ?> ms-2">
                        <?= ucfirst($sprint['status']) ?>
                    </span>
                </div>
                <span class="text-muted">
                    <?php if ($sprint['start_date'] && $sprint['end_date']): ?>
                    <?= format_date($sprint['start_date']) ?> - <?= format_date($sprint['end_date']) ?>
                    <?php else: ?>
                    Dates not set
                    <?php endif; ?>
                </span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted"><?= count($sprint['issues'] ?? []) ?> issues</span>
                <span class="text-muted"><?= array_sum(array_column($sprint['issues'] ?? [], 'story_points')) ?> points</span>
                <?php if ($sprint['status'] === 'planned' && can('manage-sprints', $project['id'])): ?>
                <button class="btn btn-sm btn-success" onclick="event.stopPropagation(); startSprint(<?= $sprint['id'] ?>)">
                    Start Sprint
                </button>
                <?php endif; ?>
                <div class="dropdown" onclick="event.stopPropagation();">
                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editSprintModal-<?= $sprint['id'] ?>">
                            <i class="bi bi-pencil me-2"></i> Edit Sprint
                        </a></li>
                        <?php if ($sprint['status'] !== 'active'): ?>
                        <li>
                            <form action="<?= url('/projects/' . $project['key'] . '/sprints/' . $sprint['id']) ?>" method="POST" 
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
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox d-block mb-2 fs-4"></i>
                    Drag issues here to plan this sprint
                </div>
                <?php else: ?>
                <?php foreach ($sprint['issues'] as $issue): ?>
                <div class="backlog-item" draggable="true" data-issue-id="<?= $issue['id'] ?>">
                    <input type="checkbox" class="issue-checkbox form-check-input" value="<?= $issue['id'] ?>">
                    <span style="color: <?= e($issue['issue_type_color']) ?>">
                        <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                    </span>
                    <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="text-primary fw-medium text-decoration-none">
                        <?= e($issue['issue_key']) ?>
                    </a>
                    <span class="flex-grow-1"><?= e($issue['summary']) ?></span>
                    <span class="badge" style="background-color: <?= e($issue['priority_color']) ?>">
                        <?= e($issue['priority_name']) ?>
                    </span>
                    <span class="badge" style="background-color: <?= e($issue['status_color']) ?>">
                        <?= e($issue['status_name']) ?>
                    </span>
                    <span class="badge bg-light text-dark"><?= $issue['story_points'] ?? '-' ?> pts</span>
                    <?php if ($issue['assignee_name']): ?>
                    <span class="text-muted small"><?= e($issue['assignee_name']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Backlog (Unassigned Issues) -->
    <div class="sprint-section" data-sprint-id="backlog">
        <div class="sprint-header" data-bs-toggle="collapse" data-bs-target="#backlog-items">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-chevron-down"></i>
                <strong>Backlog</strong>
            </div>
            <span class="text-muted"><?= count($backlogIssues ?? []) ?> issues</span>
        </div>
        <div class="collapse show" id="backlog-items">
            <div class="sprint-body" data-sprint-id="backlog">
                <?php if (empty($backlogIssues)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox d-block mb-2 fs-4"></i>
                    No items in backlog
                </div>
                <?php else: ?>
                <?php foreach ($backlogIssues as $issue): ?>
                <div class="backlog-item" draggable="true" data-issue-id="<?= $issue['id'] ?>">
                    <input type="checkbox" class="issue-checkbox form-check-input" value="<?= $issue['id'] ?>">
                    <span style="color: <?= e($issue['issue_type_color']) ?>">
                        <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                    </span>
                    <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="text-primary fw-medium text-decoration-none">
                        <?= e($issue['issue_key']) ?>
                    </a>
                    <span class="flex-grow-1"><?= e($issue['summary']) ?></span>
                    <span class="badge" style="background-color: <?= e($issue['priority_color']) ?>">
                        <?= e($issue['priority_name']) ?>
                    </span>
                    <span class="badge" style="background-color: <?= e($issue['status_color']) ?>">
                        <?= e($issue['status_name']) ?>
                    </span>
                    <span class="badge bg-light text-dark"><?= $issue['story_points'] ?? '-' ?> pts</span>
                    <?php if ($issue['assignee_name'] ?? null): ?>
                    <span class="text-muted small"><?= e($issue['assignee_name']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar (hidden by default) -->
<div class="bulk-actions d-none" id="bulkActions">
    <div class="d-flex justify-content-between align-items-center">
        <span><strong id="selectedCount">0</strong> issues selected</span>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="bulkMoveTo" style="width: auto;">
                <option value="">Move to sprint...</option>
                <?php foreach ($sprints as $sprint): ?>
                <option value="<?= $sprint['id'] ?>"><?= e($sprint['name']) ?></option>
                <?php endforeach; ?>
                <option value="backlog">Backlog</option>
            </select>
            <button class="btn btn-sm btn-primary" onclick="bulkMove()">Move</button>
            <button class="btn btn-sm btn-secondary" onclick="clearSelection()">Cancel</button>
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
            <form action="<?= url('/projects/' . $project['key'] . '/sprints') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sprint Name</label>
                        <input type="text" name="name" class="form-control" required 
                               value="<?= e($project['key']) ?> Sprint <?= count($sprints) + 1 ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Goal</label>
                        <textarea name="goal" class="form-control" rows="2"></textarea>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Sprint</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let draggedItem = null;

    // Drag and drop for backlog items
    document.querySelectorAll('.backlog-item').forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        item.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            document.querySelectorAll('.sprint-body').forEach(body => body.classList.remove('drag-over'));
        });
    });

    document.querySelectorAll('.sprint-body').forEach(body => {
        body.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        body.addEventListener('dragleave', function(e) {
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('drag-over');
            }
        });

        body.addEventListener('drop', async function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (draggedItem) {
                const issueId = draggedItem.dataset.issueId;
                const sprintId = this.dataset.sprintId;
                
                // Move visually
                this.appendChild(draggedItem);
                
                // Update on server
                try {
                    await api(`/api/v1/issues/${issueId}/sprint`, {
                        method: 'PUT',
                        body: JSON.stringify({ sprint_id: sprintId === 'backlog' ? null : sprintId })
                    });
                } catch (error) {
                    console.error('Failed to move issue:', error);
                    alert('Failed to move issue.');
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
    if (!sprintId) return alert('Please select a sprint');
    
    const issueIds = Array.from(document.querySelectorAll('.issue-checkbox:checked')).map(cb => cb.value);
    
    try {
        await api('/api/v1/issues/bulk-move', {
            method: 'POST',
            body: JSON.stringify({ issue_ids: issueIds, sprint_id: sprintId === 'backlog' ? null : sprintId })
        });
        location.reload();
    } catch (error) {
        alert('Failed to move issues: ' + error.message);
    }
}

async function startSprint(sprintId) {
    if (!confirm('Start this sprint?')) return;
    
    try {
        await api(`/projects/<?= $project['key'] ?>/sprints/${sprintId}/start`, { method: 'POST' });
        location.reload();
    } catch (error) {
        alert('Failed to start sprint: ' + error.message);
    }
}
</script>
<?php \App\Core\View::endSection(); ?>
