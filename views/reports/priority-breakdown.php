<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
        <a href="<?= url('/reports') ?>" style="color: var(--jira-blue); text-decoration: none; font-size: 14px; font-weight: 600;">Reports</a>
        <span style="color: #626F86; font-size: 14px;">/</span>
        <span style="color: #161B22; font-size: 14px; font-weight: 600;">Priority Breakdown</span>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin-bottom: 8px;">Priority Breakdown</h1>
        <p style="font-size: 15px; color: #626F86; margin: 0;">Issues distributed by priority level</p>
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
    </div>

    <!-- Chart Card -->
    <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 24px; margin-bottom: 32px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <h6 style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px;">Distribution</h6>
        <canvas id="priorityChart" style="max-height: 400px;"></canvas>
    </div>

    <!-- Priority Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $priority): ?>
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                <h6 style="font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 16px;">
                    <?= e($priority['name'] ?? 'Unknown') ?>
                </h6>
                
                <!-- Total Issues -->
                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                        <span style="color: #626F86;">Total Issues</span>
                        <strong style="color: #161B22;"><?= e($priority['count'] ?? 0) ?></strong>
                    </div>
                    <div style="height: 8px; background-color: #EBECF0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: 100%; background-color: var(--jira-blue);"></div>
                    </div>
                </div>

                <!-- Completed -->
                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                        <span style="color: #626F86;">Completed</span>
                        <strong style="color: #161B22;"><?= e($priority['completed'] ?? 0) ?></strong>
                    </div>
                    <?php 
                    $percentage = ($priority['count'] ?? 0) > 0 ? round((($priority['completed'] ?? 0) / ($priority['count'] ?? 1)) * 100) : 0;
                    ?>
                    <div style="height: 8px; background-color: #EBECF0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: <?= $percentage ?>%; background-color: #22c55e;"></div>
                    </div>
                </div>

                <div style="border-top: 1px solid #EBECF0; padding-top: 12px; margin-top: 12px;">
                    <p style="font-size: 12px; color: #626F86; margin: 0;">
                        Completion: <strong style="color: #161B22;"><?= $percentage ?>%</strong>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; color: #626F86; padding: 40px 20px;">
                <p style="font-size: 48px; margin: 0 0 16px 0;">📭</p>
                <p style="margin: 0;">No priority data available</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const priorityData = <?= json_encode($data ?? []) ?>;

function initChart(data) {
    const ctx = document.getElementById('priorityChart').getContext('2d');
    
    const labels = data.map(p => p.name);
    const counts = data.map(p => p.count || 0);
    const colors = [
        '#FF4444', // Highest - Red
        '#FF9933', // High - Orange
        '#FFCC00', // Medium - Yellow
        '#8B1956', // Low - Blue
        '#666666'  // Lowest - Gray
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: 'white',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 }
                    }
                }
            }
        }
    });
}

document.getElementById('projectFilter').addEventListener('change', function() {
    const url = new URL(window.location);
    if (this.value) {
        url.searchParams.set('project_id', this.value);
    } else {
        url.searchParams.delete('project_id');
    }
    window.location = url;
});

initChart(priorityData);
</script>
<?php \App\Core\View::endSection(); ?>
