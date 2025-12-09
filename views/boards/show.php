<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('styles'); ?>
<style>
    .board-container {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding-bottom: 1rem;
        min-height: calc(100vh - 250px);
    }
    .board-column {
        min-width: 300px;
        max-width: 300px;
        background: #f8f9fa;
        border-radius: 0.5rem;
        display: flex;
        flex-direction: column;
    }
    .column-header {
        padding: 0.75rem 1rem;
        font-weight: 600;
        border-bottom: 2px solid;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .column-body {
        padding: 0.5rem;
        flex: 1;
        overflow-y: auto;
        min-height: 100px;
    }
    .issue-card {
        background: white;
        border-radius: 0.375rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        cursor: grab;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .issue-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .issue-card.dragging {
        opacity: 0.5;
        transform: rotate(3deg);
    }
    .column-body.drag-over {
        background: #e3f2fd;
    }
    .issue-key {
        font-size: 0.8rem;
        font-weight: 500;
    }
    .issue-summary {
        font-size: 0.875rem;
        margin: 0.25rem 0;
    }
    .issue-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
    }
    .swimlane {
        margin-bottom: 1.5rem;
    }
    .swimlane-header {
        padding: 0.5rem 1rem;
        background: #e9ecef;
        border-radius: 0.375rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
</style>
<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Board Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/projects/' . $project['key']) ?>"><?= e($project['key']) ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= url('/projects/' . $project['key'] . '/boards') ?>">Boards</a></li>
                    <li class="breadcrumb-item active"><?= e($board['name']) ?></li>
                </ol>
            </nav>
            <h2 class="mb-0"><?= e($board['name']) ?></h2>
        </div>
        <div class="d-flex gap-2">
            <?php if ($board['type'] === 'scrum' && !empty($activeSprint)): ?>
            <div class="btn-group">
                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-lightning me-1"></i> <?= e($activeSprint['name']) ?>
                </button>
                <ul class="dropdown-menu">
                    <?php foreach ($sprints as $sprint): ?>
                    <li><a class="dropdown-item <?= $sprint['id'] === ($activeSprint['id'] ?? null) ? 'active' : '' ?>" 
                           href="?sprint=<?= $sprint['id'] ?>"><?= e($sprint['name']) ?></a></li>
                    <?php endforeach; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= url('/projects/' . $project['key'] . '/backlog') ?>">
                        <i class="bi bi-list-ul me-2"></i> View Backlog
                    </a></li>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="btn-group">
                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 250px;">
                    <li class="px-3 py-2">
                        <input type="text" class="form-control form-control-sm" id="quickFilter" placeholder="Quick filter...">
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="?assignee=me">Only My Issues</a></li>
                    <li><a class="dropdown-item" href="?">Clear Filters</a></li>
                </ul>
            </div>
            
            <?php if (can('manage-boards', $project['id'])): ?>
            <a href="<?= url('/projects/' . $project['key'] . '/boards/' . $board['id'] . '/settings') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-gear"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sprint Info (for Scrum boards) -->
    <?php if ($board['type'] === 'scrum' && !empty($activeSprint)): ?>
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
        <div>
            <strong><?= e($activeSprint['name']) ?></strong>
            <span class="ms-3 text-muted">
                <?= format_date($activeSprint['start_date']) ?> - <?= format_date($activeSprint['end_date']) ?>
            </span>
            <span class="ms-3">
                <?php $daysRemaining = max(0, (strtotime($activeSprint['end_date']) - time()) / 86400); ?>
                <?= round($daysRemaining) ?> days remaining
            </span>
        </div>
        <?php if (can('manage-sprints', $project['id'])): ?>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#completeSprintModal">
            Complete Sprint
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Board Columns -->
    <div class="board-container" id="boardContainer">
        <?php foreach ($columns as $column): ?>
        <div class="board-column" data-column-id="<?= $column['id'] ?>">
            <div class="column-header" style="border-color: <?= e($column['color'] ?? '#6c757d') ?>">
                <span>
                    <?= e($column['name']) ?>
                    <span class="badge bg-secondary ms-2"><?= count($column['issues'] ?? []) ?></span>
                </span>
                <?php if (!empty($column['wip_limit'])): ?>
                <span class="badge <?= count($column['issues'] ?? []) > $column['wip_limit'] ? 'bg-danger' : 'bg-light text-dark' ?>">
                    WIP: <?= $column['wip_limit'] ?>
                </span>
                <?php endif; ?>
            </div>
            <div class="column-body" data-column-id="<?= $column['id'] ?>" data-status-id="<?= $column['status_id'] ?>">
                <?php foreach ($column['issues'] ?? [] as $issue): ?>
                <div class="issue-card" draggable="true" data-issue-id="<?= $issue['id'] ?>" data-issue-key="<?= e($issue['issue_key']) ?>">
                    <div class="d-flex align-items-center mb-1">
                        <span class="me-2" style="color: <?= e($issue['issue_type_color']) ?>">
                            <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                        </span>
                        <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="issue-key text-primary text-decoration-none">
                            <?= e($issue['issue_key']) ?>
                        </a>
                        <span class="ms-auto badge" style="background-color: <?= e($issue['priority_color']) ?>; font-size: 0.65rem;">
                            <?= e($issue['priority_name']) ?>
                        </span>
                    </div>
                    <div class="issue-summary"><?= e($issue['summary']) ?></div>
                    <div class="issue-meta text-muted">
                        <span>
                            <?php if ($issue['story_points']): ?>
                            <span class="badge bg-light text-dark"><?= $issue['story_points'] ?> pts</span>
                            <?php endif; ?>
                        </span>
                        <?php if ($issue['assignee_avatar'] ?? null): ?>
                        <img src="<?= e($issue['assignee_avatar']) ?>" class="rounded-circle" width="24" height="24" 
                             title="<?= e($issue['assignee_name']) ?>">
                        <?php elseif ($issue['assignee_name'] ?? null): ?>
                        <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center" 
                             style="width: 24px; height: 24px; font-size: 0.7rem;" title="<?= e($issue['assignee_name']) ?>">
                            <?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Complete Sprint Modal -->
<?php if ($board['type'] === 'scrum' && can('manage-sprints', $project['id'])): ?>
<div class="modal fade" id="completeSprintModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Sprint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/projects/' . $project['key'] . '/sprints/' . ($activeSprint['id'] ?? '') . '/complete') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php 
                    $incompleteCount = count(array_filter($columns, fn($c) => ($c['category'] ?? '') !== 'done'));
                    ?>
                    <?php if ($incompleteCount > 0): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        There are <?= $incompleteCount ?> incomplete issues in this sprint.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Move incomplete issues to:</label>
                        <select name="move_to" class="form-select">
                            <option value="backlog">Backlog</option>
                            <?php foreach ($sprints as $sprint): ?>
                            <?php if ($sprint['id'] !== ($activeSprint['id'] ?? null) && $sprint['status'] !== 'completed'): ?>
                            <option value="<?= $sprint['id'] ?>"><?= e($sprint['name']) ?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <p>All issues in this sprint are complete. Ready to close the sprint?</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Complete Sprint</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const boardContainer = document.getElementById('boardContainer');
    let draggedCard = null;

    // Drag start
    boardContainer.addEventListener('dragstart', function(e) {
        if (e.target.classList.contains('issue-card')) {
            draggedCard = e.target;
            e.target.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', e.target.dataset.issueId);
        }
    });

    // Drag end
    boardContainer.addEventListener('dragend', function(e) {
        if (e.target.classList.contains('issue-card')) {
            e.target.classList.remove('dragging');
            document.querySelectorAll('.column-body').forEach(col => col.classList.remove('drag-over'));
            draggedCard = null;
        }
    });

    // Drag over column
    boardContainer.addEventListener('dragover', function(e) {
        e.preventDefault();
        const column = e.target.closest('.column-body');
        if (column) {
            e.dataTransfer.dropEffect = 'move';
            column.classList.add('drag-over');
        }
    });

    // Drag leave column
    boardContainer.addEventListener('dragleave', function(e) {
        const column = e.target.closest('.column-body');
        if (column && !column.contains(e.relatedTarget)) {
            column.classList.remove('drag-over');
        }
    });

    // Drop on column
    boardContainer.addEventListener('drop', async function(e) {
        e.preventDefault();
        const column = e.target.closest('.column-body');
        if (column && draggedCard) {
            column.classList.remove('drag-over');
            
            const issueId = draggedCard.dataset.issueId;
            const issueKey = draggedCard.dataset.issueKey;
            const newStatusId = column.dataset.statusId;
            
            // Move card visually
            column.appendChild(draggedCard);
            
            // Update on server
            try {
                await api(`/api/v1/issues/${issueKey}/transitions`, {
                    method: 'POST',
                    body: JSON.stringify({ status_id: newStatusId })
                });
                
                // Update column counts
                updateColumnCounts();
            } catch (error) {
                console.error('Failed to update issue status:', error);
                alert('Failed to move issue. Please refresh the page.');
            }
        }
    });

    // Update column badge counts
    function updateColumnCounts() {
        document.querySelectorAll('.board-column').forEach(column => {
            const count = column.querySelector('.column-body').children.length;
            const badge = column.querySelector('.column-header .badge.bg-secondary');
            if (badge) {
                badge.textContent = count;
            }
        });
    }

    // Quick filter
    const quickFilter = document.getElementById('quickFilter');
    if (quickFilter) {
        quickFilter.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.issue-card').forEach(card => {
                const key = card.dataset.issueKey.toLowerCase();
                const summary = card.querySelector('.issue-summary').textContent.toLowerCase();
                card.style.display = (key.includes(filter) || summary.includes(filter)) ? '' : 'none';
            });
        });
    }
});
</script>
<?php \App\Core\View::endSection(); ?>
