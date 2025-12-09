<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Sprint Report</h2>
            <p class="text-muted mb-0">Sprint completion and scope change analysis</p>
        </div>
        <a href="<?= url('/reports') ?>" class="btn btn-outline-secondary">Back to Reports</a>
    </div>

    <div class="row g-4">
        <!-- Sprint Selector -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Select Sprint</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('/reports/sprint') ?>" id="sprintForm">
                        <div class="mb-3">
                            <label for="sprintSelect" class="form-label">Sprint</label>
                            <select class="form-select" id="sprintSelect" name="sprintId" onchange="document.getElementById('sprintForm').submit()">
                                <option value="">-- Choose a sprint --</option>
                                <?php foreach ($sprints ?? [] as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= ($selectedSprint ?? 0) == $s['id'] ? 'selected' : '' ?>>
                                    <?= e($s['name']) ?> 
                                    <span class="text-muted">(<?= $s['status'] ?>)</span>
                                    - <?= e($s['project_key']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">View Sprint Report</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sprint Details -->
        <div class="col-lg-8">
            <?php if ($sprint): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><?= e($sprint['name']) ?></h5>
                    <small class="text-muted"><?= e($sprint['project_key']) ?> - <?= e($sprint['board_name']) ?></small>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-sm-6 col-lg-3">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1">Duration</div>
                                <div class="fs-5 fw-semibold">
                                    <?php
                                    $start = new DateTime($sprint['start_date']);
                                    $end = new DateTime($sprint['end_date']);
                                    echo $end->diff($start)->days + 1;
                                    ?>
                                    days
                                </div>
                                <small class="text-muted d-block">
                                    <?= $start->format('M d') ?> - <?= $end->format('M d, Y') ?>
                                </small>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1">Status</div>
                                <div class="fs-5 fw-semibold">
                                    <?php
                                    $statusClass = match($sprint['status']) {
                                        'planning' => 'badge-info',
                                        'active' => 'badge-success',
                                        'closed' => 'badge-secondary',
                                        default => 'badge-light'
                                    };
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst($sprint['status']) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1">Total Points</div>
                                <div class="fs-5 fw-semibold"><?= number_format($sprint['total_points'], 1) ?></div>
                                <small class="text-muted d-block">
                                    Completed: <strong><?= number_format($sprint['completed_points'], 1) ?></strong>
                                </small>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1">Issues</div>
                                <div class="fs-5 fw-semibold"><?= $sprint['total_issues'] ?></div>
                                <small class="text-muted d-block">
                                    Completed: <strong><?= $sprint['completed_issues'] ?></strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completion Metrics -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">Issue Completion</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-muted">Progress</span>
                                <span class="fw-semibold"><?= $sprint['completion_percentage'] ?>%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: <?= $sprint['completion_percentage'] ?>%;" 
                                     aria-valuenow="<?= $sprint['completion_percentage'] ?>" 
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-3 small">
                                <span><i class="bi bi-check-circle text-success me-1"></i><?= $sprint['completed_issues'] ?> completed</span>
                                <span><i class="bi bi-circle text-secondary me-1"></i><?= $sprint['total_issues'] - $sprint['completed_issues'] ?> remaining</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">Story Points Completion</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-muted">Progress</span>
                                <span class="fw-semibold"><?= $sprint['points_completion_percentage'] ?>%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?= $sprint['points_completion_percentage'] ?>%;" 
                                     aria-valuenow="<?= $sprint['points_completion_percentage'] ?>" 
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-3 small">
                                <span><i class="bi bi-check-circle text-success me-1"></i><?= number_format($sprint['completed_points'], 1) ?> completed</span>
                                <span><i class="bi bi-circle text-secondary me-1"></i><?= number_format($sprint['total_points'] - $sprint['completed_points'], 1) ?> remaining</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Related Reports</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('/reports/burndown/' . $sprint['id']) ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-graph-down me-2 text-primary"></i>
                        View Burndown Chart
                        <i class="bi bi-chevron-right float-end text-muted"></i>
                    </a>
                    <a href="<?= url('/reports/velocity/' . $sprint['board_id']) ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer me-2 text-success"></i>
                        View Velocity Chart
                        <i class="bi bi-chevron-right float-end text-muted"></i>
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>No sprint selected</strong>
                <p class="mb-0">Please select a sprint from the list on the left to view its report.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
