<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid" style="background: #FFFFFF; padding: 0; margin: 0;">
    <!-- Header Section -->
    <div style="padding: 32px 40px 24px 40px; border-bottom: 1px solid #DFE1E6;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #161B22; margin: 0 0 4px 0;">Reports</h1>
                <p style="font-size: 14px; color: #626F86; margin: 0;">Analyze your team's progress and performance</p>
            </div>
            <div style="display: flex; gap: 16px; align-items: center;">
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #626F86; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Project</label>
                    <select class="form-select" id="projectFilter" style="width: 280px; height: 36px; border-radius: 3px; border: 1px solid #DFE1E6; font-size: 13px; padding: 6px 12px; background-color: white; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23626F86%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3e%3cpolyline points=%226 9 12 15 18 9%22%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 8px center; background-size: 18px; padding-right: 32px;">
                        <option value="">All Projects</option>
                        <?php foreach ($projects ?? [] as $proj): ?>
                        <option value="<?= $proj['id'] ?>" <?= ($selectedProject ?? 0) == $proj['id'] ? 'selected' : '' ?>>
                            <?= e($proj['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div style="padding: 32px 40px;">

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 40px;">
        <div style="background: #FFFFFF; border: 1px solid #DFE1E6; border-radius: 3px; padding: 16px;">
            <p style="font-size: 11px; font-weight: 600; color: #626F86; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 0.5px;">Total Issues</p>
            <h2 style="font-size: 32px; font-weight: 700; color: #161B22; margin: 0;" id="totalIssues"><?= e($stats['total_issues'] ?? 0) ?></h2>
        </div>
        
        <div style="background: #FFFFFF; border: 1px solid #DFE1E6; border-radius: 3px; padding: 16px;">
            <p style="font-size: 11px; font-weight: 600; color: #626F86; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 0.5px;">Completed</p>
            <h2 style="font-size: 32px; font-weight: 700; color: #216E4E; margin: 0;" id="completedIssues"><?= e($stats['completed_issues'] ?? 0) ?></h2>
        </div>
        
        <div style="background: #FFFFFF; border: 1px solid #DFE1E6; border-radius: 3px; padding: 16px;">
            <p style="font-size: 11px; font-weight: 600; color: #626F86; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 0.5px;">In Progress</p>
            <h2 style="font-size: 32px; font-weight: 700; color: #974F0C; margin: 0;" id="inProgress"><?= e($stats['in_progress'] ?? 0) ?></h2>
        </div>
        
        <div style="background: #FFFFFF; border: 1px solid #DFE1E6; border-radius: 3px; padding: 16px;">
            <p style="font-size: 11px; font-weight: 600; color: #626F86; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 0.5px;">Avg. Velocity</p>
            <h2 style="font-size: 32px; font-weight: 700; color: #0052CC; margin: 0;" id="avgVelocity"><?= e($stats['avg_velocity'] ?? 0) ?></h2>
        </div>
    </div>

    <!-- Report Categories -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
        <!-- Agile Reports -->
        <div>
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 3px; overflow: hidden;">
                <div style="border-bottom: 1px solid #DFE1E6; padding: 12px 16px; background: #F7F8FA;">
                    <h5 style="font-size: 12px; font-weight: 700; color: #161B22; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">⚡ Agile Reports</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (!empty($activeSprints)): ?>
                    <a href="<?= url('/reports/burndown/' . $activeSprints[0]['id']) ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-graph-down fs-4 text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0">Burndown Chart</h6>
                                <small class="text-muted">Track remaining work in a sprint</small>
                            </div>
                        </div>
                    </a>
                    <?php else: ?>
                    <div class="list-group-item disabled">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-graph-down fs-4 text-muted me-3"></i>
                            <div>
                                <h6 class="mb-0">Burndown Chart</h6>
                                <small class="text-muted">No active sprints</small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($boards)): ?>
                    <a href="<?= url('/reports/velocity/' . $boards[0]['id']) ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-speedometer fs-4 text-success me-3"></i>
                            <div>
                                <h6 class="mb-0">Velocity Chart</h6>
                                <small class="text-muted">Measure team velocity over sprints</small>
                            </div>
                        </div>
                    </a>
                    <?php else: ?>
                    <div class="list-group-item disabled">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-speedometer fs-4 text-muted me-3"></i>
                            <div>
                                <h6 class="mb-0">Velocity Chart</h6>
                                <small class="text-muted">No boards available</small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <a href="<?= url('/reports/sprint') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bar-chart fs-4 text-info me-3"></i>
                            <div>
                                <h6 class="mb-0">Sprint Report</h6>
                                <small class="text-muted">Sprint completion and scope change</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= url('/reports/cumulative-flow') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-layers fs-4 text-warning me-3"></i>
                            <div>
                                <h6 class="mb-0">Cumulative Flow Diagram</h6>
                                <small class="text-muted">Visualize work in progress over time</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Issue Reports -->
        <div>
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 3px; overflow: hidden;">
                <div style="border-bottom: 1px solid #DFE1E6; padding: 12px 16px; background: #F7F8FA;">
                    <h5 style="font-size: 12px; font-weight: 700; color: #161B22; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">📊 Issue Reports</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('/reports/created-vs-resolved') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-left-right fs-4 text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0">Created vs Resolved</h6>
                                <small class="text-muted">Compare issue creation and resolution rates</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= url('/reports/resolution-time') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history fs-4 text-success me-3"></i>
                            <div>
                                <h6 class="mb-0">Resolution Time</h6>
                                <small class="text-muted">Average time to resolve issues</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= url('/reports/workload') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people fs-4 text-info me-3"></i>
                            <div>
                                <h6 class="mb-0">Workload Distribution</h6>
                                <small class="text-muted">Issues assigned per team member</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= url('/reports/priority-breakdown') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle fs-4 text-warning me-3"></i>
                            <div>
                                <h6 class="mb-0">Priority Breakdown</h6>
                                <small class="text-muted">Issues by priority level</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Time Reports -->
        <div>
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 3px; overflow: hidden;">
                <div style="border-bottom: 1px solid #DFE1E6; padding: 12px 16px; background: #F7F8FA;">
                    <h5 style="font-size: 12px; font-weight: 700; color: #161B22; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">⏱️ Time Tracking Reports</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('/reports/time-logged') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-stopwatch fs-4 text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0">Time Logged</h6>
                                <small class="text-muted">Total time logged by team</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= url('/reports/time-estimate-accuracy') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bullseye fs-4 text-success me-3"></i>
                            <div>
                                <h6 class="mb-0">Estimate Accuracy</h6>
                                <small class="text-muted">Compare estimates vs actual time</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Version Reports -->
        <div>
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 3px; overflow: hidden;">
                <div style="border-bottom: 1px solid #DFE1E6; padding: 12px 16px; background: #F7F8FA;">
                    <h5 style="font-size: 12px; font-weight: 700; color: #161B22; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">🏷️ Version Reports</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('/reports/version-progress') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-pie-chart fs-4 text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0">Version Progress</h6>
                                <small class="text-muted">Track progress toward releases</small>
                            </div>
                        </div>
                    </a>
                    <a href="<?= url('/reports/release-burndown') ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-graph-down-arrow fs-4 text-success me-3"></i>
                            <div>
                                <h6 class="mb-0">Release Burndown</h6>
                                <small class="text-muted">Work remaining for a release</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<style>
.list-group-item {
    padding: 12px 16px;
    border: none;
    border-bottom: 1px solid #EBECF0;
    background: #FFFFFF;
    text-decoration: none;
    color: inherit;
    transition: background-color 150ms ease;
}

.list-group-item:last-child {
    border-bottom: none;
}

.list-group-item:hover {
    background-color: #F7F8FA;
}

.list-group-item h6 {
    font-size: 13px;
    font-weight: 600;
    color: #0052CC;
    margin: 0 0 2px 0;
}

.list-group-item small {
    font-size: 12px;
    color: #626F86;
}

.list-group-item i {
    font-size: 16px;
    margin-right: 12px;
    color: #626F86;
}
</style>
<script>
document.getElementById('projectFilter').addEventListener('change', function() {
    const url = new URL(window.location);
    if (this.value) {
        url.searchParams.set('project_id', this.value);
    } else {
        url.searchParams.delete('project_id');
    }
    window.location = url;
});

// Real-time stats update
function updateStats() {
    fetch('<?= url('/reports/stats') ?>')
        .then(response => response.json())
        .then(data => {
            // Only update if values changed (to avoid unnecessary DOM updates)
            updateElement('totalIssues', data.total_issues);
            updateElement('completedIssues', data.completed_issues);
            updateElement('inProgress', data.in_progress);
            updateElement('avgVelocity', data.avg_velocity);
        })
        .catch(error => console.error('Error fetching stats:', error));
}

function updateElement(id, newValue) {
    const element = document.getElementById(id);
    if (element && element.textContent !== String(newValue)) {
        element.textContent = newValue;
        // Add a subtle fade animation
        element.style.transition = 'opacity 0.3s ease';
        element.style.opacity = '0.7';
        setTimeout(() => {
            element.style.opacity = '1';
        }, 100);
    }
}

// Update stats every 5 seconds
setInterval(updateStats, 5000);
</script>
<?php \App\Core\View::endSection(); ?>
