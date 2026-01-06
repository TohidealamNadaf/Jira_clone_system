<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-xxl py-5">
    <!-- Breadcrumb Navigation -->
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
        <a href="<?= url('/reports') ?>" style="color: var(--jira-blue); text-decoration: none; font-size: 14px; font-weight: 600;">Reports</a>
        <span style="color: #626F86; font-size: 14px;">/</span>
        <span style="color: #161B22; font-size: 14px; font-weight: 600;">Cumulative Flow Diagram</span>
    </div>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-5">
        <div class="flex-grow-1">
            <h1 class="h2 fw-bold mb-1">Cumulative Flow Diagram</h1>
            <p class="text-muted mb-0">
                <span class="badge bg-light text-dark"><?= e($board['project_key']) ?></span>
                <span class="ms-2"><?= e($board['name']) ?></span>
            </p>
        </div>
        <a href="<?= url('/reports/cumulative-flow') ?>" class="btn btn-light border">
            <i class="bi bi-arrow-left-right me-2"></i> Change Board
        </a>
    </div>

    <!-- Controls Section -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 border p-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <div class="row align-items-end g-3">
                    <div class="col-lg-3">
                        <label for="daysInput" class="form-label fw-semibold small text-uppercase text-muted">Date Range</label>
                        <form method="GET" id="daysForm" class="d-flex gap-2">
                            <input type="number" class="form-control" id="daysInput" name="days" 
                                   value="<?= e($days ?? 30) ?>" min="7" max="90" 
                                   onchange="document.getElementById('daysForm').submit()">
                            <span class="text-muted fs-7 align-self-center" style="white-space: nowrap;">days</span>
                        </form>
                        <small class="text-muted d-block mt-2">Last <?= e($days ?? 30) ?> days</small>
                    </div>
                    <div class="col-lg-9">
                        <div class="d-flex gap-2 flex-wrap">
                            <?php foreach ([[7, '1 Week'], [14, '2 Weeks'], [30, '1 Month'], [60, '2 Months'], [90, '3 Months']] as [$val, $label]): ?>
                            <a href="?days=<?= $val ?>" class="btn btn-sm <?= ($days ?? 30) == $val ? 'btn-primary' : 'btn-outline-secondary' ?>">
                                <?= $label ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Chart Section (70%) -->
        <div class="col-lg-8">
            <div class="bg-white rounded-3 border p-4" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-1">Work in Progress Over Time</h5>
                    <p class="text-muted small mb-0">Trend of issues across workflow statuses</p>
                </div>
                <div style="position: relative; height: 450px; width: 100%; min-height: 450px;">
                    <canvas id="cumulativeFlowChart"></canvas>
                </div>
                <div class="text-center text-muted small mt-3 pt-3 border-top">
                    Chart displays cumulative flow of issues from <?= date('M d', strtotime("-{$days} days")) ?> to <?= date('M d') ?>
                </div>
            </div>
        </div>

        <!-- Sidebar (30%) -->
        <div class="col-lg-4">
            <!-- Status Legend -->
            <div class="bg-white rounded-3 border p-4 mb-4" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h5 class="fw-bold text-dark mb-3">Status Legend</h5>
                <div class="d-flex flex-column gap-2" style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($statuses ?? [] as $status): ?>
                    <div class="d-flex align-items-center p-2 rounded-2" style="background: rgba(0,0,0,0.02); transition: background 0.15s;">
                        <div class="flex-shrink-0">
                            <div class="rounded-2" 
                                 style="width: 24px; height: 24px; background-color: <?= e($status['color']) ?>; border: 2px solid rgba(0,0,0,0.1);"></div>
                        </div>
                        <div class="flex-grow-1 ms-3 min-width-0">
                            <div class="fw-semibold text-dark small"><?= e($status['name']) ?></div>
                            <div class="text-muted font-monospace" style="font-size: 11px;"><?= ucfirst(str_replace('_', ' ', $status['category'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-3 border p-4" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h5 class="fw-bold text-dark mb-3">Quick Stats</h5>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Total Issues Tracked</span>
                        <span class="fw-bold fs-5">
                            <?php 
                            $totalCount = 0;
                            foreach ($statuses ?? [] as $status) {
                                $count = \App\Core\Database::selectValue(
                                    "SELECT COUNT(*) FROM issues WHERE status_id = ?",
                                    [$status['id']]
                                ) ?? 0;
                                $totalCount += $count;
                            }
                            echo $totalCount;
                            ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Analysis Period</span>
                        <span class="fw-bold"><?= e($days ?? 30) ?> days</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Data Points</span>
                        <span class="fw-bold"><?= count($flowData ?? []) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rounded-3 {
    border-radius: 12px;
}

.min-width-0 {
    min-width: 0;
}

.font-monospace {
    font-family: 'Monaco', 'Courier New', monospace;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .col-lg-8, .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const flowData = <?= json_encode($flowData ?? []) ?>;
    const statuses = <?= json_encode($statuses ?? []) ?>;
    
    // Debug logging
    console.log('Cumulative Flow Data Loaded');
    console.log('FlowData length:', flowData ? flowData.length : 'null');
    console.log('Statuses count:', statuses ? statuses.length : 'null');
    
    if (!flowData || flowData.length === 0) {
        console.warn('No flow data available');
        const canvas = document.getElementById('cumulativeFlowChart');
        const parent = canvas ? canvas.parentElement : null;
        if (parent) {
            parent.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">No data available for the selected date range.</div>';
        }
        return;
    }

    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js library not loaded');
        return;
    }

    try {
        // Extract dates and prepare datasets
        const dates = flowData.map(d => d.date);
        const statusMap = {};
        
        statuses.forEach(status => {
            statusMap[status['name']] = {
                color: status['color'],
                category: status['category']
            };
        });

        const datasets = statuses.map((status, index) => {
            const data = flowData.map(d => d[status['name']] || 0);
            
            return {
                label: status['name'],
                data: data,
                borderColor: status['color'],
                backgroundColor: status['color'] + '40', // Add transparency
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: status['color'],
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            };
        });

        const canvas = document.getElementById('cumulativeFlowChart');
        if (!canvas) {
            console.error('Canvas element not found');
            return;
        }

        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Unable to get canvas context');
            return;
        }

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'center',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: {
                                size: 13,
                                weight: 500
                            },
                            color: '#333',
                            boxPadding: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        padding: 14,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 12 },
                        bodySpacing: 6,
                        mode: 'index',
                        intersect: false,
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: true,
                        callbacks: {
                            title: function(context) {
                                return 'Date: ' + context[0].label;
                            },
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' issue' + (context.parsed.y !== 1 ? 's' : '');
                            }
                        }
                    },
                    filler: {
                        propagate: true
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 11 },
                            maxRotation: 45,
                            minRotation: 0,
                            color: '#666'
                        }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#666',
                            stepSize: Math.ceil((Math.max(...datasets.flat().map(d => Math.max(...d.data))) / 5)) || 1
                        },
                        title: {
                            display: true,
                            text: 'Number of Issues',
                            font: { size: 12, weight: 500 },
                            color: '#333',
                            padding: 10
                        }
                    }
                }
            }
        });

        console.log('Chart created successfully');
    } catch (error) {
        console.error('Error creating chart:', error);
    }
});
</script>
<?php \App\Core\View::endSection(); ?>
