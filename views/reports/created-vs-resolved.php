<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
        <a href="<?= url('/reports') ?>" style="color: var(--jira-blue); text-decoration: none; font-size: 14px; font-weight: 600;">Reports</a>
        <span style="color: #626F86; font-size: 14px;">/</span>
        <span style="color: #161B22; font-size: 14px; font-weight: 600;">Created vs Resolved</span>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin-bottom: 8px;">Created vs Resolved</h1>
        <p style="font-size: 15px; color: #626F86; margin: 0;">Compare issue creation and resolution rates over time</p>
    </div>

    <!-- Filters Section -->
    <div style="display: flex; gap: 24px; margin-bottom: 32px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                Project
            </label>
            <select class="form-select" id="projectFilter" style="width: 240px; height: 40px; border-radius: 4px; border: 1px solid #DFE1E6; font-size: 14px; color: #161B22; padding: 8px 12px; background-color: white; cursor: pointer;">
                <option value="">All Projects</option>
                <?php foreach ($projects ?? [] as $proj): ?>
                <option value="<?= $proj['id'] ?>" <?= ($selectedProject ?? 0) == $proj['id'] ? 'selected' : '' ?>>
                    <?= e($proj['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                Time Period
            </label>
            <select class="form-select" id="daysFilter" style="width: 240px; height: 40px; border-radius: 4px; border: 1px solid #DFE1E6; font-size: 14px; color: #161B22; padding: 8px 12px; background-color: white; cursor: pointer;">
                <option value="7" <?= ($days ?? 30) == 7 ? 'selected' : '' ?>>Last 7 days</option>
                <option value="30" <?= ($days ?? 30) == 30 ? 'selected' : '' ?>>Last 30 days</option>
                <option value="60" <?= ($days ?? 30) == 60 ? 'selected' : '' ?>>Last 60 days</option>
                <option value="90" <?= ($days ?? 30) == 90 ? 'selected' : '' ?>>Last 90 days</option>
                <option value="180" <?= ($days ?? 30) == 180 ? 'selected' : '' ?>>Last 180 days</option>
            </select>
        </div>
    </div>

    <!-- Chart Card -->
    <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 24px; margin-bottom: 32px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <h6 style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px;">Trend</h6>
        <canvas id="createdVsResolvedChart" style="max-height: 400px;"></canvas>
    </div>

    <!-- Metrics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
            <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                <span style="color: var(--jira-blue); margin-right: 8px;">●</span>Total Created
            </p>
            <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                <h2 id="totalCreated" style="font-size: 36px; font-weight: 700; color: #161B22; margin: 0;">0</h2>
                <span style="font-size: 14px; color: #626F86;">issues</span>
            </div>
            <p style="font-size: 12px; color: #626F86; margin: 0;">Last <?= e($days ?? 30) ?> days</p>
        </div>

        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
            <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                <span style="color: #22c55e; margin-right: 8px;">●</span>Total Resolved
            </p>
            <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                <h2 id="totalResolved" style="font-size: 36px; font-weight: 700; color: #161B22; margin: 0;">0</h2>
                <span style="font-size: 14px; color: #626F86;">issues</span>
            </div>
            <p style="font-size: 12px; color: #626F86; margin: 0;">Last <?= e($days ?? 30) ?> days</p>
        </div>

        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
            <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                <span style="color: #FF5630; margin-right: 8px;">●</span>Net Change
            </p>
            <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                <h2 id="netChange" style="font-size: 36px; font-weight: 700; color: #161B22; margin: 0;">0</h2>
                <span style="font-size: 14px; color: #626F86;">issues</span>
            </div>
            <p style="font-size: 12px; color: #626F86; margin: 0;">Created - Resolved</p>
        </div>

        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
            <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                <span style="color: #FFAB00; margin-right: 8px;">●</span>Resolution Rate
            </p>
            <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                <h2 id="resolutionRate" style="font-size: 36px; font-weight: 700; color: #161B22; margin: 0;">0%</h2>
            </div>
            <p style="font-size: 12px; color: #626F86; margin: 0;">Resolved / Created</p>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const reportData = <?= $reportData ?? '[]' ?>;
let chart = null;

function initChart(data) {
    const ctx = document.getElementById('createdVsResolvedChart').getContext('2d');
    
    if (chart) {
        chart.destroy();
    }

    const dates = data.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
    const created = data.map(d => d.created);
    const resolved = data.map(d => d.resolved);

    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Created',
                    data: created,
                    borderColor: '#8B1956',
                    backgroundColor: 'rgba(139, 25, 86, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#8B1956',
                    borderWidth: 2,
                },
                {
                    label: 'Resolved',
                    data: resolved,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#22c55e',
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 12 }
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    updateStats(data);
}

function updateStats(data) {
    const totalCreated = data.reduce((sum, d) => sum + d.created, 0);
    const totalResolved = data.reduce((sum, d) => sum + d.resolved, 0);
    const netChange = totalCreated - totalResolved;
    const resolutionRate = totalCreated > 0 ? Math.round((totalResolved / totalCreated) * 100) : 0;

    document.getElementById('totalCreated').textContent = totalCreated;
    document.getElementById('totalResolved').textContent = totalResolved;
    document.getElementById('netChange').textContent = netChange;
    document.getElementById('resolutionRate').textContent = resolutionRate + '%';
}

// Filter handlers
document.getElementById('projectFilter').addEventListener('change', function() {
    const url = new URL(window.location);
    if (this.value) {
        url.searchParams.set('project_id', this.value);
    } else {
        url.searchParams.delete('project_id');
    }
    window.location = url;
});

document.getElementById('daysFilter').addEventListener('change', function() {
    const url = new URL(window.location);
    url.searchParams.set('days', this.value);
    window.location = url;
});

// Initialize chart on page load
initChart(reportData);
</script>
<?php \App\Core\View::endSection(); ?>
