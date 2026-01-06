<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
        <a href="<?= url('/reports') ?>" style="color: var(--jira-blue); text-decoration: none; font-size: 14px; font-weight: 600;">Reports</a>
        <span style="color: #626F86; font-size: 14px;">/</span>
        <span style="color: #161B22; font-size: 14px; font-weight: 600;">Release Burndown</span>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin-bottom: 8px;">Release Burndown</h1>
        <p style="font-size: 15px; color: #626F86; margin: 0;">Work remaining for a release</p>
    </div>

    <!-- Filters Section -->
    <div style="display: flex; gap: 24px; margin-bottom: 32px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                Version
            </label>
            <select class="form-select" id="versionFilter" style="width: 240px; height: 40px; border-radius: 4px; border: 1px solid #DFE1E6; font-size: 14px; color: #161B22; padding: 8px 12px; background-color: white; cursor: pointer;">
                <option value="">Select a version</option>
                <?php foreach ($versions ?? [] as $ver): ?>
                <option value="<?= $ver['id'] ?>" <?= ($selectedVersion ?? 0) == $ver['id'] ? 'selected' : '' ?>>
                    <?= e($ver['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if ($version): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 32px;">
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    Version
                </p>
                <h6 style="font-size: 14px; font-weight: 600; color: #161B22; margin: 0;"><?= e($version['name']) ?></h6>
            </div>
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    Project
                </p>
                <h6 style="font-size: 14px; font-weight: 600; color: #161B22; margin: 0;"><?= e($version['project_name'] ?? '') ?></h6>
            </div>
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    Release Date
                </p>
                <h6 style="font-size: 14px; font-weight: 600; color: #161B22; margin: 0;"><?= !empty($version['release_date']) ? date('M d, Y', strtotime($version['release_date'])) : 'N/A' ?></h6>
            </div>
        </div>

        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 24px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
            <canvas id="releaseBurndownChart" style="max-height: 400px;"></canvas>
        </div>
    <?php else: ?>
        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 40px 20px; text-align: center;">
            <p style="font-size: 48px; margin: 0 0 16px 0;">📭</p>
            <p style="color: #626F86; margin: 0;">Select a version to view the release burndown chart</p>
        </div>
    <?php endif; ?>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const burndownData = <?= $burndownData ?? '[]' ?>;

function initChart(data) {
    if (data.length === 0) {
        return;
    }

    const ctx = document.getElementById('releaseBurndownChart').getContext('2d');
    
    // Group data by date
    const dateGroups = {};
    let minDate = null;
    let maxDate = null;

    data.forEach(issue => {
        const createdDate = new Date(issue.created_at).toISOString().split('T')[0];
        if (!dateGroups[createdDate]) {
            dateGroups[createdDate] = { created: 0, completed: 0 };
        }
        dateGroups[createdDate].created++;
        
        if (issue.completed_at) {
            const completedDate = new Date(issue.completed_at).toISOString().split('T')[0];
            if (!dateGroups[completedDate]) {
                dateGroups[completedDate] = { created: 0, completed: 0 };
            }
            dateGroups[completedDate].completed++;
        }

        if (!minDate || createdDate < minDate) minDate = createdDate;
        if (!maxDate || createdDate > maxDate) maxDate = createdDate;
    });

    // Generate dates between min and max
    const dates = [];
    const remaining = [];
    let remainingCount = 0;

    let current = new Date(minDate);
    const end = new Date(maxDate);

    while (current <= end) {
        const dateStr = current.toISOString().split('T')[0];
        dates.push(new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        
        if (dateGroups[dateStr]) {
            remainingCount += dateGroups[dateStr].created - dateGroups[dateStr].completed;
        }
        remaining.push(Math.max(0, remainingCount));
        
        current.setDate(current.getDate() + 1);
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Issues Remaining',
                data: remaining,
                borderColor: '#8B1956',
                backgroundColor: 'rgba(139, 25, 86, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#8B1956',
                borderWidth: 2,
            }]
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
}

document.getElementById('versionFilter').addEventListener('change', function() {
    const url = new URL(window.location);
    if (this.value) {
        url.searchParams.set('version_id', this.value);
    } else {
        url.searchParams.delete('version_id');
    }
    window.location = url;
});

<?php if ($version): ?>
    initChart(burndownData);
<?php endif; ?>
</script>
<?php \App\Core\View::endSection(); ?>
