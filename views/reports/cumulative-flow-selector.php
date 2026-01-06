<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-xxl py-5">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-5">
        <div class="flex-grow-1">
            <h1 class="h2 fw-bold mb-1">Cumulative Flow Diagram</h1>
            <p class="text-muted mb-0">Visualize work in progress over time by selecting a board</p>
        </div>
        <a href="<?= url('/reports') ?>" class="btn btn-light border">
            <i class="bi bi-arrow-left me-2"></i> Back to Reports
        </a>
    </div>

    <div class="row g-4">
        <!-- Selection Panel -->
        <div class="col-lg-5">
            <div class="bg-white rounded-3 border p-4" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h5 class="fw-bold text-dark mb-4">Select Board</h5>
                
                <?php if (empty($boards)): ?>
                    <div class="alert alert-info border-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>No boards available</strong>
                        <p class="mb-0 mt-2 small">Please create a board first to view the cumulative flow diagram.</p>
                    </div>
                <?php else: ?>
                    <form method="GET" action="<?= url('/reports/cumulative-flow') ?>" id="boardForm">
                        <div class="mb-4">
                            <label for="boardSelect" class="form-label fw-semibold small text-uppercase text-muted">Board</label>
                            <select class="form-select form-select-lg" id="boardSelect" name="boardId" onchange="selectBoard(this.value)" style="border-color: #e0e0e0;">
                                <option value="">-- Choose a board --</option>
                                <?php foreach ($boards ?? [] as $b): ?>
                                <option value="<?= $b['id'] ?>">
                                    <?= e($b['name']) ?> <span class="text-muted">(<?= e($b['project_key']) ?>)</span>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted d-block mt-2">Select a board to view its workflow trends</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-graph-up me-2"></i> View Diagram
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-7">
            <div class="bg-white rounded-3 border p-4" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h5 class="fw-bold text-dark mb-3">About Cumulative Flow Diagram</h5>
                <p class="text-muted small mb-3">
                    A cumulative flow diagram (CFD) displays how work accumulates and flows through different workflow statuses over time. It helps you visualize:
                </p>
                <ul class="text-muted small" style="padding-left: 1.5rem;">
                    <li><strong>Work Trends:</strong> How many issues are in each status at any point in time</li>
                    <li><strong>Bottlenecks:</strong> Stages where work accumulates and slows down</li>
                    <li><strong>Progress:</strong> Overall movement from todo to done</li>
                    <li><strong>Velocity:</strong> How fast issues move through your workflow</li>
                </ul>
                
                <div class="border-top mt-4 pt-4">
                    <h6 class="fw-semibold text-dark mb-3">Available Boards</h6>
                    <?php if (empty($boards)): ?>
                        <p class="text-muted small">No boards available.</p>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($boards ?? [] as $b): ?>
                            <div class="p-3 rounded-2" style="background: rgba(0,0,0,0.02);">
                                <div class="fw-semibold text-dark"><?= e($b['name']) ?></div>
                                <small class="text-muted">Project: <?= e($b['project_key']) ?> • <?= e($b['project_name']) ?></small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rounded-3 {
    border-radius: 12px;
}

.form-select-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
}
</style>

<script>
function selectBoard(boardId) {
    if (boardId) {
        window.location.href = '<?= url('/reports/cumulative-flow') ?>/' + boardId;
    }
}
</script>

<?php \App\Core\View::endSection(); ?>
