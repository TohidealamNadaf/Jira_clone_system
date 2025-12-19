<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                    <li class="breadcrumb-item active">Issue Types</li>
                </ol>
            </nav>
            <h2 class="mb-0">Issue Types</h2>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issueTypeModal">
            <i class="bi bi-plus-lg me-1"></i> Add Issue Type
        </button>
    </div>

    <!-- Issue Types Grid -->
    <div class="row">
        <?php if (empty($issueTypes)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No issue types found</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($issueTypes as $type): ?>
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 transition-card">
                        <div class="card-body">
                            <!-- Header with Color and Icon -->
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="flex-shrink-0" style="width: 50px; height: 50px; background-color: <?= e($type['color'] ?? '#4A90D9') ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-<?= e($type['icon'] ?? 'circle') ?> fs-5 text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1"><?= e($type['name']) ?></h5>
                                    <?php if ($type['is_subtask']): ?>
                                        <span class="badge bg-info">Subtask</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Description -->
                            <?php if ($type['description']): ?>
                                <p class="card-text small text-muted mb-3"><?= e($type['description']) ?></p>
                            <?php else: ?>
                                <p class="card-text small text-muted mb-3 fst-italic">No description</p>
                            <?php endif; ?>

                            <!-- Stats -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="bg-light rounded-2 p-3 text-center">
                                        <div class="fs-5 fw-bold text-primary"><?= $type['issue_count'] ?? 0 ?></div>
                                        <small class="text-muted">Issues</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded-2 p-3 text-center">
                                        <div class="fs-5 fw-bold" style="color: <?= e($type['color'] ?? '#4A90D9') ?>">■</div>
                                        <small class="text-muted">Color</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#issueTypeModal" 
                                        onclick="editIssueType(<?= htmlspecialchars(json_encode($type)) ?>)">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <form action="<?= url('/admin/issue-types/' . $type['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" 
                                            onclick="return confirm('Delete this issue type? This cannot be undone.');">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Issue Type Modal -->
<div class="modal fade" id="issueTypeModal" tabindex="-1" aria-labelledby="issueTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="issueTypeModalLabel">Add Issue Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="issueTypeForm" method="POST" action="<?= url('/admin/issue-types') ?>">
                <?= csrf_field() ?>
                <input type="hidden" id="issueTypeId" name="id">
                <input type="hidden" id="issueTypeMethod" name="_method" value="POST">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="issueTypeName" class="form-label">Type Name *</label>
                        <input type="text" class="form-control" id="issueTypeName" name="name" required maxlength="50">
                    </div>

                    <div class="mb-3">
                        <label for="issueTypeDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="issueTypeDescription" name="description" rows="3" maxlength="500"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="issueTypeIcon" class="form-label">Icon (Bootstrap Icon)</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i id="iconPreview" class="bi bi-circle"></i>
                                    </span>
                                    <input type="text" class="form-control" id="issueTypeIcon" name="icon" placeholder="circle" 
                                           onchange="updateIconPreview()" maxlength="50">
                                </div>
                                <small class="text-muted">e.g., circle, square, check, exclamation</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="issueTypeColor" class="form-label">Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="issueTypeColor" name="color" value="#4A90D9">
                                    <input type="text" class="form-control" id="issueTypeColorText" value="#4A90D9" readonly maxlength="7">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="issueTypeSubtask" name="is_subtask" value="1">
                            <label class="form-check-label" for="issueTypeSubtask">
                                This is a Subtask type
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Issue Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.transition-card {
    transition: all 0.3s ease;
}

.transition-card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
    transform: translateY(-2px);
}

.card-body {
    padding: 1.5rem;
}

.bg-light {
    background-color: #f5f7fa !important;
}

.rounded-2 {
    border-radius: 8px;
}
</style>

<script>
function updateIconPreview() {
    const iconName = document.getElementById('issueTypeIcon').value || 'circle';
    document.getElementById('iconPreview').className = 'bi bi-' + iconName;
}

function editIssueType(issueType) {
    document.getElementById('issueTypeModalLabel').textContent = 'Edit Issue Type';
    document.getElementById('issueTypeForm').action = '<?= url('/admin/issue-types') ?>/' + issueType.id;
    document.getElementById('issueTypeId').value = issueType.id;
    document.getElementById('issueTypeMethod').value = 'PUT';
    document.getElementById('issueTypeName').value = issueType.name;
    document.getElementById('issueTypeDescription').value = issueType.description || '';
    document.getElementById('issueTypeIcon').value = issueType.icon || 'circle';
    document.getElementById('issueTypeColor').value = issueType.color || '#4A90D9';
    document.getElementById('issueTypeColorText').value = issueType.color || '#4A90D9';
    document.getElementById('issueTypeSubtask').checked = issueType.is_subtask ? true : false;
    updateIconPreview();
}

// Update color text field when color picker changes
document.getElementById('issueTypeColor')?.addEventListener('change', function() {
    document.getElementById('issueTypeColorText').value = this.value;
});

// Reset form when modal is closed
document.getElementById('issueTypeModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('issueTypeModalLabel').textContent = 'Add Issue Type';
    document.getElementById('issueTypeForm').action = '<?= url('/admin/issue-types') ?>';
    document.getElementById('issueTypeId').value = '';
    document.getElementById('issueTypeMethod').value = 'POST';
    document.getElementById('issueTypeForm').reset();
    document.getElementById('issueTypeColor').value = '#4A90D9';
    document.getElementById('issueTypeColorText').value = '#4A90D9';
    updateIconPreview();
});
</script>

<?php \App\Core\View::endsection(); ?>
