# Time Tracking Module - View Examples

**Sample view files to help you get started**

---

## 📁 Create These View Files

Create the following files in `views/time-tracking/` directory:

---

## 1. Dashboard View

**File**: `views/time-tracking/dashboard.php`

```php
<?php
declare(strict_types=1);

use App\Helpers\Helpers;

$current_user = $GLOBALS['user'] ?? [];
$active_timer = $active_timer ?? null;
$today_logs = $today_logs ?? [];
$today_stats = $today_stats ?? [];
?>

<!-- Dashboard Layout -->
<div class="container-fluid py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2">⏱ Time Tracking Dashboard</h1>
            <p class="text-muted">Track time spent on issues and monitor project costs</p>
        </div>
    </div>

    <!-- Active Timer Widget -->
    <?php if ($active_timer): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>⚠ Active Timer</strong>
        <p>You have a running timer. Check the floating widget in the bottom-right corner.</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Today's Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">Today's Time</h6>
                    <h2 class="h1"><?= formatDuration($today_stats['total_seconds'] ?? 0) ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">Today's Cost</h6>
                    <h2 class="h1">$<?= number_format($today_stats['total_cost'] ?? 0, 2) ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">Time Entries</h6>
                    <h2 class="h1"><?= $today_stats['log_count'] ?? 0 ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <a href="<?= url('/settings/rate') ?>" class="btn btn-sm btn-primary w-100">
                        Set Your Rate
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Time Logs Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Today's Time Logs</h5>
        </div>
        <div class="card-body p-0">
            <?php if (count($today_logs) > 0): ?>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Issue</th>
                        <th>Duration</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($today_logs as $log): ?>
                    <tr>
                        <td>
                            <a href="<?= url('/issues/' . $log['issue_key']) ?>">
                                <strong><?= htmlspecialchars($log['issue_key']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars(substr($log['issue_summary'], 0, 50)) ?></small>
                            </a>
                        </td>
                        <td><?= formatDuration((int)$log['duration_seconds']) ?></td>
                        <td><strong>$<?= number_format((float)$log['total_cost'], 2) ?></strong></td>
                        <td>
                            <span class="badge bg-<?= $log['status'] === 'stopped' ? 'success' : 'warning' ?>">
                                <?= ucfirst($log['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted"><?= date('H:i', strtotime($log['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert alert-info m-0">
                No time logged today. 
                <a href="<?= url('/projects') ?>">Start tracking time on an issue!</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Helper Function -->
<?php
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $secs);
}
?>

<style>
.card {
    border-top: 3px solid #8b1956;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-body h2 {
    color: #8b1956;
    font-weight: 700;
}

.table tr:hover {
    background-color: #f9f9f9;
}
</style>
```

---

## 2. Issue Timer Widget

**File**: `views/time-tracking/issue-timer.php`

```php
<?php
declare(strict_types=1);

$active_timer = $active_timer ?? null;
$time_logs = $time_logs ?? [];
$totals = $totals ?? [];
$current_user_id = $current_user_id ?? 0;
?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">⏱ Time Tracking</h5>
        <button class="btn btn-sm btn-primary" 
                onclick="FloatingTimer.startTimer(<?= $issue['id'] ?>, <?= $issue['project_id'] ?>, '<?= addslashes($issue['summary']) ?>', '<?= $issue['key'] ?>')">
            <i class="bi bi-play-fill"></i> Start Timer
        </button>
    </div>

    <div class="card-body">
        <!-- Summary Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-label">Total Time</div>
                    <div class="stat-value"><?= formatDuration($totals['total_seconds'] ?? 0) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-label">Total Cost</div>
                    <div class="stat-value">$<?= number_format($totals['total_cost'] ?? 0, 2) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-label">Entries</div>
                    <div class="stat-value"><?= count($time_logs) ?></div>
                </div>
            </div>
        </div>

        <!-- Time Logs by User -->
        <?php if (!empty($totals['by_user']) && is_array($totals['by_user'])): ?>
        <div class="mb-4">
            <h6 class="mb-3">Time by User</h6>
            <div class="row">
                <?php foreach ($totals['by_user'] as $userId => $userTotal): ?>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <?php if (!empty($userTotal['avatar'])): ?>
                            <img src="<?= htmlspecialchars($userTotal['avatar']) ?>" 
                                 alt="<?= htmlspecialchars($userTotal['name']) ?>"
                                 class="rounded-circle me-2"
                                 width="32" height="32">
                        <?php endif; ?>
                        <div>
                            <strong><?= htmlspecialchars($userTotal['name']) ?></strong><br>
                            <small class="text-muted">
                                <?= formatDuration($userTotal['total_seconds']) ?> 
                                @ $<?= number_format($userTotal['total_cost'], 2) ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Time Logs Table -->
        <?php if (count($time_logs) > 0): ?>
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Duration</th>
                    <th>Cost</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($time_logs as $log): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <?php if (!empty($log['avatar'])): ?>
                                <img src="<?= htmlspecialchars($log['avatar']) ?>" 
                                     alt="<?= htmlspecialchars($log['display_name']) ?>"
                                     class="rounded-circle me-2"
                                     width="24" height="24">
                            <?php endif; ?>
                            <?= htmlspecialchars($log['display_name']) ?>
                        </div>
                    </td>
                    <td><?= formatDuration((int)$log['duration_seconds']) ?></td>
                    <td>$<?= number_format((float)$log['total_cost'], 2) ?></td>
                    <td>
                        <span class="badge bg-<?= $log['status'] === 'stopped' ? 'success' : 'warning' ?>">
                            <?= ucfirst($log['status']) ?>
                        </span>
                    </td>
                    <td class="text-muted"><?= date('Y-m-d H:i', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-info mb-0">
            No time logged yet. Click "Start Timer" above to begin tracking!
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.stat-box {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 24px;
    color: #8b1956;
    font-weight: 700;
}
</style>

<?php
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $secs);
}
?>
```

---

## 3. Project Report View

**File**: `views/time-tracking/project-report.php`

```php
<?php
declare(strict_types=1);

$project = $project ?? [];
$budget_summary = $budget_summary ?? [];
$time_logs = $time_logs ?? [];
$statistics = $statistics ?? [];
$by_user = $by_user ?? [];
?>

<div class="container-fluid py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2"><?= htmlspecialchars($project['name']) ?> - Time Tracking Report</h1>
            <p class="text-muted">Project cost analysis and budget overview</p>
        </div>
        <div class="col-auto">
            <a href="<?= url('/projects/' . $project['key']) ?>" class="btn btn-outline-secondary">
                Back to Project
            </a>
        </div>
    </div>

    <!-- Budget Summary -->
    <?php if (!empty($budget_summary)): ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">💰 Budget Overview</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-label">Total Budget</div>
                        <div class="stat-value">$<?= number_format((float)$budget_summary['total_budget'], 2) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-label">Total Cost</div>
                        <div class="stat-value">$<?= number_format((float)$budget_summary['total_cost'], 2) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-label">Remaining</div>
                        <div class="stat-value">$<?= number_format((float)$budget_summary['remaining_budget'], 2) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-label">Used</div>
                        <div class="stat-value"><?= number_format((float)$budget_summary['budget_used_percentage'], 1) ?>%</div>
                    </div>
                </div>
            </div>

            <!-- Budget Progress Bar -->
            <div class="mt-3">
                <div class="progress" style="height: 25px;">
                    <?php 
                    $percentage = (float)$budget_summary['budget_used_percentage'];
                    $color = $percentage >= 100 ? 'danger' : ($percentage >= 90 ? 'warning' : 'success');
                    ?>
                    <div class="progress-bar bg-<?= $color ?>" 
                         role="progressbar" 
                         style="width: <?= min($percentage, 100) ?>%">
                        <?= number_format($percentage, 1) ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics -->
    <?php if (!empty($statistics)): ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">📊 Statistics</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Total Logs:</strong> <?= $statistics['total_logs'] ?? 0 ?>
                </div>
                <div class="col-md-3">
                    <strong>Total Time:</strong> <?= formatDuration($statistics['total_seconds'] ?? 0) ?>
                </div>
                <div class="col-md-3">
                    <strong>Average Cost/Log:</strong> $<?= number_format((float)($statistics['avg_cost_per_log'] ?? 0), 2) ?>
                </div>
                <div class="col-md-3">
                    <strong>Team Members:</strong> <?= $statistics['unique_users'] ?? 0 ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- By User Breakdown -->
    <?php if (!empty($by_user)): ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">👥 Time by User</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Time Logged</th>
                        <th>Total Cost</th>
                        <th>Entries</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($by_user as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($user['avatar']) ?>" 
                                         alt="<?= htmlspecialchars($user['name']) ?>"
                                         class="rounded-circle me-2"
                                         width="32" height="32">
                                <?php endif; ?>
                                <?= htmlspecialchars($user['name']) ?>
                            </div>
                        </td>
                        <td><?= formatDuration($user['total_seconds']) ?></td>
                        <td><strong>$<?= number_format($user['total_cost'], 2) ?></strong></td>
                        <td><?= $user['log_count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- All Time Logs -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">📋 All Time Logs</h5>
        </div>
        <div class="card-body p-0">
            <?php if (count($time_logs) > 0): ?>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Issue</th>
                        <th>User</th>
                        <th>Duration</th>
                        <th>Cost</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($time_logs as $log): ?>
                    <tr>
                        <td>
                            <a href="<?= url('/issues/' . $log['issue_key']) ?>">
                                <?= htmlspecialchars($log['issue_key']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($log['display_name']) ?></td>
                        <td><?= formatDuration((int)$log['duration_seconds']) ?></td>
                        <td>$<?= number_format((float)$log['total_cost'], 2) ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($log['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert alert-info m-0">
                No time logged for this project yet.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $secs);
}
?>

<style>
.stat-box {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 28px;
    color: #8b1956;
    font-weight: 700;
}

.table tr:hover {
    background-color: #f9f9f9;
}
</style>
```

---

## Usage

1. Copy the code examples above
2. Create files in `views/time-tracking/` directory:
   - `dashboard.php`
   - `issue-timer.php`
   - `project-report.php`

3. Update your controllers to use these views:

```php
// In TimeTrackingController
public function dashboard(): string {
    return $this->view('time-tracking.dashboard', $data);
}

public function projectReport(int $projectId): string {
    return $this->view('time-tracking.project-report', $data);
}
```

4. Include the views in issue detail page:

```php
<!-- In views/issues/show.php -->
<?php include 'time-tracking/issue-timer.php'; ?>
```

---

**These are starter templates. Customize to match your design system!** 🎨

All examples follow your existing code style and architecture.
