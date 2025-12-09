<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Velocity Chart</h2>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select" id="boardSelect" style="width: auto;"
                onchange="window.location = '<?= url('/reports/velocity') ?>/' + this.value;">
                <?php foreach ($boards ?? [] as $bd): ?>
                    <option value="<?= $bd['id'] ?>" <?= ($selectedBoard ?? '') == $bd['id'] ? 'selected' : '' ?>>
                        <?= e($bd['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-secondary" onclick="exportChart()">
                <i class="bi bi-download me-1"></i> Export
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="text-muted">Average Velocity</h5>
                    <h2><?= e($averageVelocity ?? 0) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Team Velocity Over Time</h5>
        </div>
        <div class="card-body" style="position: relative; height: 400px;">
            <canvas id="velocityChart"></canvas>
        </div>
    </div>

    <!-- Sprint Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Sprint Details</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sprint</th>
                        <th class="text-center">Committed</th>
                        <th class="text-center">Completed</th>
                        <th class="text-center">Accuracy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = json_decode($velocityData ?? '[]', true);
                    if (empty($data)):
                        ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No sprint data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $sprint): ?>
                            <?php
                            $committed = (float) ($sprint['committed'] ?? 0);
                            $completed = (float) ($sprint['completed'] ?? 0);
                            $accuracy = $committed > 0 ? round(($completed / $committed) * 100) : 0;
                            $color = $accuracy >= 80 ? 'success' : ($accuracy >= 60 ? 'warning' : 'danger');
                            ?>
                            <tr>
                                <td><?= e($sprint['sprint_name'] ?? 'Unknown') ?></td>
                                <td class="text-center"><?= e($committed) ?></td>
                                <td class="text-center"><?= e($completed) ?></td>
                                <td class="text-center"><span class="badge bg-<?= $color ?>"><?= $accuracy ?>%</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    console.log('=== VELOCITY CHART SCRIPT LOADED ===');

    const velocityDataJson = '<?= htmlspecialchars($velocityData ?? '[]', ENT_QUOTES, 'UTF-8') ?>';
    console.log('Data JSON:', velocityDataJson);

    let velocityData;
    try {
        velocityData = JSON.parse(velocityDataJson);
        console.log('Parsed data:', velocityData);
        console.log('Data length:', velocityData ? velocityData.length : 'null');
    } catch (error) {
        console.error('Failed to parse velocity data:', error);
        velocityData = [];
    }

    if (velocityData && Array.isArray(velocityData) && velocityData.length > 0) {
        try {
            const labels = velocityData.map(d => d.sprint_name || 'Unknown');
            const committed = velocityData.map(d => parseFloat(d.committed) || 0);
            const completed = velocityData.map(d => parseFloat(d.completed) || 0);

            console.log('Creating chart with', labels.length, 'sprints');
            console.log('Labels:', labels);
            console.log('Committed:', committed);
            console.log('Completed:', completed);

            // Check if Chart.js loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js library not loaded!');
            } else {
                const canvas = document.getElementById('velocityChart');
                if (!canvas) {
                    console.error('Canvas element not found!');
                } else {
                    const ctx = canvas.getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Committed',
                                    data: committed,
                                    backgroundColor: 'rgba(108, 117, 125, 0.6)',
                                    borderColor: '#6c757d',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Completed',
                                    data: completed,
                                    backgroundColor: 'rgba(25, 135, 84, 0.8)',
                                    borderColor: '#198754',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });

                    window.velocityChart = chart;
                    console.log('✓ Chart created successfully');
                }
            }
        } catch (error) {
            console.error('Chart creation failed:', error);
            console.error('Stack:', error.stack);
        }
    } else {
        console.warn('No velocity data to display');
        console.log('velocityData:', velocityData);
        const canvas = document.getElementById('velocityChart');
        if (canvas) {
            canvas.style.display = 'none';
        }
        const parent = canvas?.parentElement;
        if (parent) {
            const msg = document.createElement('div');
            msg.style.textAlign = 'center';
            msg.style.padding = '40px';
            msg.style.color = '#999';
            msg.innerHTML = '<p><i class="bi bi-info-circle" style="font-size: 2rem;"></i></p><p>No sprint data available. Create and complete some sprints first.</p>';
            parent.appendChild(msg);
        }
    }

    function exportChart() {
        console.log('Export button clicked');
        console.log('velocityChart state:', window.velocityChart);

        if (!window.velocityChart) {
            console.warn('Chart not initialized');
            alert('Chart is not ready yet. Please wait for data to load.');
            return;
        }

        try {
            const link = document.createElement('a');
            link.href = window.velocityChart.toDataURL();
            link.download = 'velocity-chart-' + new Date().toISOString().split('T')[0] + '.png';
            link.click();
            console.log('✓ Chart exported successfully');
        } catch (error) {
            console.error('Export failed:', error);
            alert('Failed to export chart: ' + error.message);
        }
    }
</script>

<?php \App\Core\View::endSection(); ?>