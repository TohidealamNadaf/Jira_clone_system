<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active">Board</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kanban Board</h1>
            <p class="text-muted mb-0"><?= e($project['name']) ?></p>
        </div>
        <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Issue
        </a>
    </div>

    <!-- Kanban Board -->
    <div class="row g-3">
        <?php foreach ($statuses as $status): ?>
        <?php 
            $statusIssues = array_filter($issues, function($issue) use ($status) {
                return $issue['status_id'] == $status['id'];
            });
        ?>
        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <span class="badge" style="background-color: <?= e($status['color']) ?>">
                            <?= e($status['name']) ?>
                        </span>
                    </h6>
                    <span class="badge bg-secondary"><?= count($statusIssues) ?></span>
                </div>
                <div class="card-body p-2 board-column" 
                     data-status-id="<?= e($status['id']) ?>" 
                     style="min-height: 400px; overflow-y: auto;">
                    <?php if (empty($statusIssues)): ?>
                    <p class="text-muted text-center small py-4">No issues</p>
                    <?php else: ?>
                    <?php foreach ($statusIssues as $issue): ?>
                    <div class="card mb-2 board-card" 
                         draggable="true" 
                         data-issue-id="<?= e($issue['id']) ?>"
                         data-issue-key="<?= e($issue['issue_key']) ?>"
                         style="border-left: 3px solid <?= e($issue['priority_color']) ?>; cursor: move; transition: all 0.2s;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge" style="background-color: <?= e($issue['issue_type_color']) ?>">
                                    <i class="bi bi-<?= e($issue['issue_type_icon']) ?>" style="font-size: 0.75rem;"></i>
                                    <?= e(substr($issue['issue_type_name'], 0, 3)) ?>
                                </span>
                                <small class="text-muted fw-medium"><?= e($issue['issue_key']) ?></small>
                            </div>
                            <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="text-decoration-none">
                                <p class="mb-2 small text-dark">
                                    <?= e(substr($issue['summary'], 0, 50)) ?>
                                </p>
                            </a>
                            <?php if ($issue['assignee_name']): ?>
                            <div class="d-flex align-items-center mt-2">
                                <img src="<?= e($issue['assignee_avatar'] ?? '/images/default-avatar.png') ?>" 
                                     class="rounded-circle" width="20" height="20" 
                                     title="<?= e($issue['assignee_name']) ?>" style="margin-right: -8px; border: 2px solid white;">
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
    </div>
</div>

<style>
.board-card {
    cursor: move;
}

.board-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.board-card.dragging {
    opacity: 0.5;
}

.board-column {
    transition: background-color 0.2s;
}

.board-column.drag-over {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
const projectKey = '<?= e($project['key']) ?>';
let draggedCard = null;

// Drag start
document.querySelectorAll('.board-card').forEach(card => {
    card.addEventListener('dragstart', (e) => {
        draggedCard = card;
        card.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', card.innerHTML);
    });

    card.addEventListener('dragend', (e) => {
        card.classList.remove('dragging');
        document.querySelectorAll('.board-column').forEach(col => {
            col.classList.remove('drag-over');
        });
    });
});

// Column drag over/drop handlers
document.querySelectorAll('.board-column').forEach(column => {
    column.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        column.classList.add('drag-over');
    });

    column.addEventListener('dragleave', (e) => {
        if (e.target === column) {
            column.classList.remove('drag-over');
        }
    });

    column.addEventListener('drop', async (e) => {
        e.preventDefault();
        column.classList.remove('drag-over');

        if (!draggedCard) return;

        const issueId = draggedCard.dataset.issueId;
        const issueKey = draggedCard.dataset.issueKey;
        const statusId = column.dataset.statusId;
        const currentStatusId = draggedCard.closest('.board-column').dataset.statusId;

        // Don't move to same status
        if (statusId === currentStatusId) {
            return;
        }

        // Move card in UI (optimistic update)
        column.appendChild(draggedCard);

        // Send to server
        try {
            const response = await fetch('<?= url("/api/v1/issues/") ?>' + issueKey + '/transitions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    status_id: statusId
                })
            });

            if (!response.ok) {
                const error = await response.json();
                alert('Failed to move issue: ' + (error.error || 'Unknown error'));
                location.reload();
            }
        } catch (error) {
            console.error('Error moving issue:', error);
            alert('Error moving issue. Please try again.');
            location.reload();
        }
    });
});
</script>
<?php \App\Core\View::endSection(); ?>
