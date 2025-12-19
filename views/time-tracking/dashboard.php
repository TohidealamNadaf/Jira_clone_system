<?php

declare(strict_types=1);

// Time Tracking Dashboard - Main Overview
// Shows user's timers, statistics, and quick actions

$currentUser = auth();
$timeLogs = $timeLogs ?? [];
$activeTimer = $activeTimer ?? null;
$todayStats = $today_stats ?? [];
$todayLogs = $today_logs ?? [];

// Calculate totals from today's logs
$totalSeconds = array_sum(array_map(fn($log) => $log['duration_seconds'] ?? 0, $todayLogs));
$totalCost = array_sum(array_map(fn($log) => $log['total_cost'] ?? 0, $todayLogs));
$billableEntries = count(array_filter($todayLogs, fn($log) => $log['is_billable'] === 1));

?>

<div class="container-fluid time-tracking-dashboard">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Time Tracking</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="time-tracking-header mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="time-tracking-title">⏱️ Time Tracking Dashboard</h1>
                <p class="time-tracking-subtitle">Track your time and monitor project costs in real-time</p>
            </div>
            <a href="<?= url('/time-tracking/logs') ?>" class="btn btn-primary btn-lg">
                <i class="bi bi-bar-chart"></i> View All Logs
            </a>
        </div>
    </div>

    <!-- Active Timer Alert -->
    <?php if ($activeTimer): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>⏱️ Active Timer</strong>
                <p class="mb-0">
                    Currently tracking time on 
                    <a href="<?= url('/projects/' . $activeTimer['project_key'] . '/issues/' . $activeTimer['issue_key']) ?>">
                        <?= htmlspecialchars($activeTimer['issue_key']) ?>
                    </a>
                    for the last 
                    <span id="activeTimerDuration">0s</span>
                </p>
            </div>
        </div>
    </div>
    <script>
        // Update active timer display
        const startTime = new Date('<?= $activeTimer['start_time'] ?>').getTime();
        setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;
            const duration = hours > 0 
                ? `${hours}h ${minutes}m` 
                : minutes > 0 
                    ? `${minutes}m ${seconds}s`
                    : `${seconds}s`;
            document.getElementById('activeTimerDuration').textContent = duration;
        }, 1000);
    </script>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="time-tracking-stats-grid mb-5">
        <!-- Total Time Tracked -->
        <div class="stat-card stat-card--time">
            <div class="stat-card__icon">⏱️</div>
            <div class="stat-card__content">
                <p class="stat-card__label">Total Time Tracked</p>
                <h3 class="stat-card__value">
                    <?php
                    $hours = intdiv($totalSeconds, 3600);
                    $minutes = intdiv($totalSeconds % 3600, 60);
                    echo sprintf('%d:%02dh', $hours, $minutes);
                    ?>
                </h3>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="stat-card stat-card--cost">
            <div class="stat-card__icon">💰</div>
            <div class="stat-card__content">
                <p class="stat-card__label">Total Cost</p>
                <h3 class="stat-card__value">$<?= number_format($totalCost, 2) ?></h3>
            </div>
        </div>

        <!-- Time Entries -->
        <div class="stat-card stat-card--entries">
            <div class="stat-card__icon">📝</div>
            <div class="stat-card__content">
                <p class="stat-card__label">Time Entries (Today)</p>
                <h3 class="stat-card__value"><?= $todayStats['log_count'] ?? count($todayLogs) ?></h3>
            </div>
        </div>

        <!-- Billable Entries -->
        <div class="stat-card stat-card--billable">
            <div class="stat-card__icon">✓</div>
            <div class="stat-card__content">
                <p class="stat-card__label">Billable Entries</p>
                <h3 class="stat-card__value"><?= $billableEntries ?>/<?= $todayStats['log_count'] ?? count($todayLogs) ?></h3>
            </div>
        </div>
    </div>

    <!-- Time Logs Table -->
    <div class="time-tracking-logs mb-5">
        <div class="time-tracking-logs__header mb-3">
            <div>
                <h4 class="time-tracking-logs__title">Recent Time Logs</h4>
                <p class="time-tracking-logs__subtitle">Last 30 days</p>
            </div>
        </div>

        <div class="time-tracking-logs__table-wrapper">
            <table class="time-tracking-logs__table">
                <thead class="time-tracking-logs__thead">
                    <tr>
                        <th class="time-tracking-logs__th">Issue</th>
                        <th class="time-tracking-logs__th">Date</th>
                        <th class="time-tracking-logs__th">Duration</th>
                        <th class="time-tracking-logs__th">Cost</th>
                        <th class="time-tracking-logs__th">Billable</th>
                        <th class="time-tracking-logs__th">Description</th>
                    </tr>
                </thead>
                <tbody class="time-tracking-logs__tbody">
                    <?php if (empty($todayLogs)): ?>
                    <tr class="time-tracking-logs__empty">
                        <td colspan="6">
                            <div class="time-tracking-logs__empty-state">
                                <p class="time-tracking-logs__empty-text">📭 No time logs yet today.</p>
                                <small class="time-tracking-logs__empty-hint">Start tracking time from any issue page.</small>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach (array_slice($todayLogs, 0, 20) as $log): ?>
                        <tr class="time-tracking-logs__row">
                            <td class="time-tracking-logs__cell time-tracking-logs__cell--issue">
                                <a href="<?= url('/projects/' . ($log['project_key'] ?? 'N/A') . '/issues/' . ($log['issue_key'] ?? 'N/A')) ?>" 
                                   class="time-tracking-logs__link">
                                     <strong><?= htmlspecialchars($log['issue_key'] ?? 'N/A') ?></strong>
                                 </a>
                                 <p class="time-tracking-logs__summary"><?= htmlspecialchars(substr($log['issue_summary'] ?? '', 0, 50)) ?></p>
                             </td>
                             <td class="time-tracking-logs__cell"><?= date('M d, Y H:i', strtotime($log['created_at'])) ?></td>
                             <td class="time-tracking-logs__cell time-tracking-logs__cell--mono">
                                 <?php
                                 $h = intdiv($log['duration_seconds'] ?? 0, 3600);
                                 $m = intdiv(($log['duration_seconds'] ?? 0) % 3600, 60);
                                 echo sprintf('%d:%02d', $h, $m);
                                 ?>
                             </td>
                             <td class="time-tracking-logs__cell time-tracking-logs__cell--cost">
                                 <strong>$<?= number_format((float)($log['total_cost'] ?? 0), 2) ?></strong>
                             </td>
                             <td class="time-tracking-logs__cell">
                                 <?php if ($log['is_billable'] === 1): ?>
                                     <span class="badge badge-billable">Yes</span>
                                 <?php else: ?>
                                     <span class="badge badge-non-billable">No</span>
                                 <?php endif; ?>
                             </td>
                             <td class="time-tracking-logs__cell time-tracking-logs__cell--description">
                                 <?php if (!empty($log['description'])): ?>
                                     <small><?= htmlspecialchars(substr($log['description'], 0, 40)) ?></small>
                                 <?php else: ?>
                                     <small class="text-muted">—</small>
                                 <?php endif; ?>
                             </td>
                         </tr>
                         <?php endforeach; ?>
                    <?php endif; ?>
                 </tbody>
            </table>
        </div>

        <?php if (count($todayLogs) > 20): ?>
        <div class="time-tracking-logs__footer">
            <a href="<?= url('/time-tracking/logs') ?>" class="btn btn-outline-primary btn-sm">
                View All <?= count($todayLogs) ?> Entries →
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Help Section -->
    <div class="time-tracking-help">
        <h4 class="time-tracking-help__title">❓ How to Use Time Tracking</h4>
        <ol class="time-tracking-help__list">
            <li>Go to any issue in your project</li>
            <li>Click the <strong>"Start Timer"</strong> button (appears in the floating widget)</li>
            <li>The timer widget shows elapsed time and calculated cost</li>
            <li>Click <strong>"Stop"</strong> when done and add a description</li>
            <li>Your time is automatically logged and visible here</li>
            <li>View reports and budgets at <a href="<?= url('/time-tracking/budgets') ?>" class="time-tracking-help__link">Budgets</a></li>
        </ol>
    </div>
</div>
