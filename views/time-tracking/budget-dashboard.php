<?php

declare(strict_types=1);

// Budget Dashboard - Overview of all project budgets
// Shows budget status, remaining budget, alerts, and cost trends

$projects = $projects ?? [];
$alerts = $alerts ?? [];

// Calculate total budget stats
$totalBudget = array_sum(array_map(fn($p) => $p['total_budget'] ?? 0, $projects));
$totalCost = array_sum(array_map(fn($p) => $p['total_cost'] ?? 0, $projects));
$remainingBudget = $totalBudget - $totalCost;

?>

<div class="container-fluid py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h2 mb-1">💼 Project Budget Dashboard</h1>
            <p class="text-muted">Monitor project budgets across all projects</p>
        </div>
    </div>

    <!-- Active Alerts -->
    <?php if (!empty($alerts)): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h5 class="alert-heading">⚠️ Active Budget Alerts</h5>
                <p class="mb-2"><?= count($alerts) ?> project(s) have exceeded their budget thresholds:</p>
                <ul class="mb-0 ps-3">
                    <?php foreach (array_slice($alerts, 0, 5) as $alert): ?>
                    <li>
                        <strong><?= htmlspecialchars($alert['project_name']) ?></strong> 
                        - <?= number_format($alert['actual_percentage'], 1) ?>% used 
                        (<?= htmlspecialchars($alert['alert_type']) ?> at <?= number_format($alert['threshold_percentage'], 1) ?>%)
                    </li>
                    <?php endforeach; ?>
                    <?php if (count($alerts) > 5): ?>
                    <li>... and <?= count($alerts) - 5 ?> more</li>
                    <?php endif; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Budget</p>
                    <h4 class="mb-0">${number_format($totalBudget, 2)}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Cost</p>
                    <h4 class="mb-0">${number_format($totalCost, 2)}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Remaining</p>
                    <h4 class="mb-0 text-<?= $remainingBudget >= 0 ? 'success' : 'danger' ?>">
                        $<?= number_format($remainingBudget, 2) ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Overall Usage</p>
                    <div class="progress" style="height: 25px;">
                        <?php 
                        $overallPercent = $totalBudget > 0 ? ($totalCost / $totalBudget) * 100 : 0;
                        $bgClass = $overallPercent > 100 ? 'bg-danger' : ($overallPercent > 80 ? 'bg-warning' : 'bg-success');
                        ?>
                        <div class="progress-bar <?= $bgClass ?>" role="progressbar" 
                             style="width: <?= min($overallPercent, 100) ?>%"
                             aria-valuenow="<?= $overallPercent ?>" aria-valuemin="0" aria-valuemax="100">
                            <?= number_format($overallPercent, 1) ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Cards Grid -->
    <div class="row">
        <?php if (empty($projects)): ?>
        <div class="col-md-12">
            <div class="alert alert-info">
                <p class="mb-0">No project budgets configured yet. <a href="<?= url('/admin/projects') ?>">Set up budgets</a></p>
            </div>
        </div>
        <?php else: ?>
            <?php 
            // Sort by usage percentage (descending) to show most critical first
            usort($projects, fn($a, $b) => ($b['budget_used_percentage'] ?? 0) <=> ($a['budget_used_percentage'] ?? 0));
            foreach ($projects as $project): 
            ?>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?= htmlspecialchars($project['name']) ?></h5>
                        <span class="badge bg-<?php 
                            $status = $project['status'] ?? 'active';
                            if ($status === 'exceeded') echo 'danger';
                            elseif ($status === 'completed') echo 'success';
                            elseif ($status === 'paused') echo 'secondary';
                            else echo 'primary';
                        ?>">
                            <?= ucfirst($status) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <!-- Budget Info Row -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="text-muted small mb-1">Total Budget</p>
                                <p class="h6 mb-0">${number_format($project['total_budget'] ?? 0, 2)}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-muted small mb-1">Total Cost</p>
                                <p class="h6 mb-0">${number_format($project['total_cost'] ?? 0, 2)}</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Budget Usage</small>
                                <small class="fw-bold">
                                    <?php 
                                    $percent = $project['budget_used_percentage'] ?? 0;
                                    echo number_format($percent, 1) . '%';
                                    ?>
                                </small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <?php 
                                $bgClass = $percent > 100 ? 'bg-danger' : ($percent > 80 ? 'bg-warning' : 'bg-success');
                                ?>
                                <div class="progress-bar <?= $bgClass ?>" role="progressbar" 
                                     style="width: <?= min($percent, 100) ?>%"
                                     aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <!-- Remaining Budget -->
                        <div class="row">
                            <div class="col-6">
                                <p class="text-muted small mb-1">Remaining</p>
                                <p class="h6 mb-0 text-<?= ($project['total_budget'] ?? 0) - ($project['total_cost'] ?? 0) >= 0 ? 'success' : 'danger' ?>">
                                    $<?= number_format(($project['total_budget'] ?? 0) - ($project['total_cost'] ?? 0), 2) ?>
                                </p>
                            </div>
                            <div class="col-6 text-end">
                                <a href="<?= url('/time-tracking/project/' . $project['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    📊 View Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Budget Alerts History -->
    <?php if (!empty($alerts) && count($alerts) > 10): ?>
    <div class="row mt-5">
        <div class="col-md-12">
            <h5 class="mb-3">📋 All Budget Alerts</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Alert Type</th>
                            <th>Threshold</th>
                            <th>Actual</th>
                            <th>Cost at Alert</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alerts as $alert): ?>
                        <tr>
                            <td><?= htmlspecialchars($alert['project_name']) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $alert['alert_type'] === 'exceeded' ? 'danger' : 
                                    ($alert['alert_type'] === 'critical' ? 'warning' : 'info')
                                ?>">
                                    <?= ucfirst($alert['alert_type']) ?>
                                </span>
                            </td>
                            <td><?= number_format($alert['threshold_percentage'] ?? 0, 1) ?>%</td>
                            <td><?= number_format($alert['actual_percentage'] ?? 0, 1) ?>%</td>
                            <td>$<?= number_format($alert['cost_at_alert'] ?? 0, 2) ?></td>
                            <td>
                                <?php if ($alert['is_acknowledged']): ?>
                                    <span class="badge bg-success">Acknowledged</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Help Section -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title">💡 Budget Management Tips</h5>
                    <ul class="mb-0 ps-3">
                        <li><strong>Green progress bar</strong> (0-79%): Budget is healthy</li>
                        <li><strong>Yellow progress bar</strong> (80-99%): Budget approaching limit, monitor closely</li>
                        <li><strong>Red progress bar</strong> (100%+): Budget exceeded, immediate action needed</li>
                        <li>Click <strong>"View Report"</strong> on any project to see detailed time tracking</li>
                        <li>Adjust rates or team size to better manage budgets for future projects</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }
</style>
