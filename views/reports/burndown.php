<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb & Title -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/reports') ?>">Reports</a></li>
            <li class="breadcrumb-item active">Burndown Chart</li>
        </ol>
    </nav>
    
    <!-- Page Header with Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Burndown Chart</h1>
        <div class="d-flex gap-2 align-items-center">
            <select class="form-select form-select-sm" id="projectSelect" style="width: 180px;">
                <?php foreach ($projects ?? [] as $proj): ?>
                <option value="<?= $proj['key'] ?>" <?= ($selectedProject ?? '') === $proj['key'] ? 'selected' : '' ?>>
                    <?= e($proj['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select class="form-select form-select-sm" id="sprintSelect" style="width: 150px;">
                <?php foreach ($sprints ?? [] as $sprint): ?>
                <option value="<?= $sprint['id'] ?>" <?= ($selectedSprint ?? '') == $sprint['id'] ? 'selected' : '' ?>>
                    <?= e($sprint['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-outline-secondary" id="exportBtn">
                <i class="bi bi-download me-1"></i> Export
            </button>
        </div>
    </div>

    <!-- Sprint Summary -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-1">Total Points</h5>
                    <h2 class="mb-0"><?= e($sprintData['total_points'] ?? 0) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-1">Completed</h5>
                    <h2 class="mb-0 text-success"><?= e($sprintData['completed_points'] ?? 0) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-1">Remaining</h5>
                    <h2 class="mb-0 text-warning"><?= e($sprintData['remaining_points'] ?? 0) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-1">Days Left</h5>
                    <h2 class="mb-0"><?= e($sprintData['days_remaining'] ?? 0) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Burndown Chart -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sprint Burndown</h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary active" data-metric="points">Story Points</button>
                <button class="btn btn-outline-secondary" data-metric="issues">Issue Count</button>
            </div>
        </div>
        <div class="card-body">
            <canvas id="burndownChart" height="100"></canvas>
        </div>
    </div>

    <!-- Issues Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sprint Issues</h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary active" data-filter="all">All</button>
                <button class="btn btn-outline-secondary" data-filter="todo">To Do</button>
                <button class="btn btn-outline-secondary" data-filter="progress">In Progress</button>
                <button class="btn btn-outline-secondary" data-filter="done">Done</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Key</th>
                        <th>Summary</th>
                        <th>Assignee</th>
                        <th>Status</th>
                        <th class="text-center">Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sprintIssues ?? [] as $issue): ?>
                    <tr data-status="<?= e($issue['status_category'] ?? 'todo') ?>">
                        <td>
                            <span style="color: <?= e($issue['issue_type_color']) ?>" class="me-1">
                                <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                            </span>
                            <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="text-decoration-none">
                                <?= e($issue['issue_key']) ?>
                            </a>
                        </td>
                        <td><?= e($issue['summary']) ?></td>
                        <td><?= e($issue['assignee_name'] ?? 'Unassigned') ?></td>
                        <td>
                            <span class="badge" style="background-color: <?= e($issue['status_color']) ?>">
                                <?= e($issue['status_name']) ?>
                            </span>
                        </td>
                        <td class="text-center"><?= e($issue['story_points'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
console.log('BURNDOWN SCRIPT LOADED');

// Parse burndown data from controller
const burndownRaw = <?= $burndownData ?? '[]' ?>;
const idealRaw = <?= $idealBurndown ?? '[]' ?>;

console.log('=== BURNDOWN CHART DEBUG ===');
console.log('Raw burndown data:', burndownRaw);
console.log('Raw ideal data:', idealRaw);
console.log('Burndown length:', burndownRaw.length);
console.log('Ideal length:', idealRaw.length);

// If no data, use sample data for demo
let labels, idealData, actualData;

if (!idealRaw || idealRaw.length === 0) {
    console.warn('⚠️ No data received! Using sample data for demo');
    labels = ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7', 'Day 8', 'Day 9', 'Day 10'];
    idealData = [100, 90, 80, 70, 60, 50, 40, 30, 20, 10];
    actualData = [100, 95, 88, 75, 68, 55, 48, 35, 28, 15];
} else {
    // Extract labels and data
    labels = idealRaw.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
    idealData = idealRaw.map(d => d.points);
    actualData = burndownRaw.map(d => d.remaining);
}

console.log('Processed labels:', labels);
console.log('Processed ideal:', idealData);
console.log('Processed actual:', actualData);

const ctx = document.getElementById('burndownChart').getContext('2d');
let burndownChart = null;

try {
    burndownChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Ideal Burndown',
                    data: idealData,
                    borderColor: '#6c757d',
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0,
                    pointRadius: 0
                },
                {
                    label: 'Actual Remaining',
                    data: actualData,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.1
                }
            ]
        },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                mode: 'index',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Story Points'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Sprint Day'
                }
            }
        }
    }
    });
    console.log('Chart initialized:', burndownChart);
} catch (error) {
    console.error('Chart initialization failed:', error);
}

// Filter controls
document.querySelectorAll('[data-filter]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        document.querySelectorAll('tbody tr').forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else {
                row.style.display = row.dataset.status === filter ? '' : 'none';
            }
        });
    });
});

// Metric toggle
document.querySelectorAll('[data-metric]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-metric]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        // Would reload chart with different metric
    });
});

// Project/Sprint filter
document.getElementById('projectSelect').addEventListener('change', function() {
    window.location = `?project=${this.value}`;
});

document.getElementById('sprintSelect').addEventListener('change', function() {
    const project = document.getElementById('projectSelect').value;
    window.location = `?project=${project}&sprint=${this.value}`;
});

// Export button handler
const exportBtn = document.getElementById('exportBtn');
if (exportBtn) {
    exportBtn.addEventListener('click', function(e) {
        e.preventDefault();
        exportChart();
    });
}

function exportChart() {
    console.log('Export clicked, burndownChart:', burndownChart);
    
    if (!burndownChart) {
        alert('Chart is not loaded yet. Please wait a moment and try again.');
        return;
    }
    
    try {
        // Chart.js provides toBase64Image() method
        const imageUrl = burndownChart.toBase64Image();
        
        const link = document.createElement('a');
        link.href = imageUrl;
        link.download = 'burndown-chart-' + new Date().toISOString().split('T')[0] + '.png';
        link.click();
        
        console.log('Chart exported successfully');
    } catch (error) {
        console.error('Export failed:', error);
        alert('Failed to export chart: ' + error.message);
    }
}
</script>

<?php \App\Core\View::endSection(); ?>
