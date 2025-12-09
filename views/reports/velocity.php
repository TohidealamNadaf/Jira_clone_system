<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
console.log('=== VELOCITY CHART SCRIPT LOADED ===');

// Get data from controller
const velocityDataJson = '<?= htmlspecialchars($velocityData ?? '[]', ENT_QUOTES, 'UTF-8') ?>';
console.log('Raw velocity data JSON:', velocityDataJson);

let velocityRaw = [];
try {
    velocityRaw = JSON.parse(velocityDataJson);
    console.log('Parsed velocity data:', velocityRaw);
    console.log('Data is array:', Array.isArray(velocityRaw));
    console.log('Data length:', velocityRaw.length);
} catch (error) {
    console.error('Failed to parse velocity data:', error);
    velocityRaw = [];
}

let velocityChart = null;

function initChart() {
    console.log('Initializing chart...');
    
    if (!velocityRaw || !Array.isArray(velocityRaw) || velocityRaw.length === 0) {
        console.warn('No velocity data available, showing empty state');
        const noDataDiv = document.getElementById('noData');
        const canvas = document.getElementById('velocityChart');
        const sprintCountDisplay = document.getElementById('sprintCountDisplay');
        const sprintTable = document.getElementById('sprintTable');
        
        if (noDataDiv) noDataDiv.style.display = 'block';
        if (canvas) canvas.style.display = 'none';
        if (sprintCountDisplay) sprintCountDisplay.textContent = '0';
        if (sprintTable) sprintTable.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No sprint data available for this board. Create and close some sprints first.</td></tr>';
        
        return;
    }

    // Prepare chart data
    const labels = velocityRaw.map(d => d.sprint_name || 'Unknown');
    const committed = velocityRaw.map(d => parseFloat(d.committed) || 0);
    const completed = velocityRaw.map(d => parseFloat(d.completed) || 0);
    
    const avg = completed.length > 0 
        ? (completed.reduce((a, b) => a + b, 0) / completed.length).toFixed(1)
        : 0;

    console.log('Chart data prepared:', { labels, committed, completed, avg });

    // Get canvas element
    const canvas = document.getElementById('velocityChart');
    if (!canvas) {
        console.error('Canvas element not found!');
        return;
    }

    // Check if chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js library not loaded!');
        return;
    }

    const ctx = canvas.getContext('2d');
    
    try {
        // Destroy existing chart if it exists
        if (velocityChart instanceof Chart) {
            velocityChart.destroy();
        }
        
        velocityChart = new Chart(ctx, {
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
                    },
                    {
                        label: 'Average (' + avg + ')',
                        data: labels.map(() => parseFloat(avg)),
                        type: 'line',
                        borderColor: '#dc3545',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        pointRadius: 3,
                        pointBackgroundColor: '#dc3545'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        
        console.log('✓ Chart created successfully');
        
        const noDataDiv = document.getElementById('noData');
        if (noDataDiv) noDataDiv.style.display = 'none';
        if (canvas) canvas.style.display = 'block';
    } catch (error) {
        console.error('✗ Chart creation failed:', error);
        console.error('Error stack:', error.stack);
    }

    // Update sprint table
    updateTable();
    
    // Update sprint count
    const sprintCountDisplay = document.getElementById('sprintCountDisplay');
    if (sprintCountDisplay) {
        sprintCountDisplay.textContent = velocityRaw.length;
    }
}

function updateTable() {
    const tbody = document.getElementById('sprintTable');
    if (!tbody) return;
    
    tbody.innerHTML = '';

    if (!velocityRaw || velocityRaw.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No sprint data</td></tr>';
        return;
    }

    velocityRaw.forEach(sprint => {
        const comm = parseFloat(sprint.committed) || 0;
        const comp = parseFloat(sprint.completed) || 0;
        const acc = comm > 0 ? Math.round((comp / comm) * 100) : 0;
        const color = acc >= 80 ? 'success' : (acc >= 60 ? 'warning' : 'danger');
        
        const start = new Date(sprint.start_date).toLocaleDateString();
        const end = new Date(sprint.end_date).toLocaleDateString();

        const row = `<tr>
            <td>${sprint.sprint_name}</td>
            <td class="text-center">${comm}</td>
            <td class="text-center text-success fw-bold">${comp}</td>
            <td class="text-center"><span class="badge bg-${color}">${acc}%</span></td>
            <td><small class="text-muted">${start} - ${end}</small></td>
        </tr>`;
        
        tbody.innerHTML += row;
    });
}

function exportChart() {
    console.log('Export button clicked');
    console.log('velocityChart state:', velocityChart);
    
    if (!velocityChart) {
        console.warn('Chart not initialized yet');
        alert('Chart is not ready. Please wait for data to load.');
        return;
    }
    
    try {
        const image = velocityChart.toDataURL('image/png');
        const link = document.createElement('a');
        link.href = image;
        link.download = 'velocity-chart-' + new Date().toISOString().split('T')[0] + '.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        console.log('✓ Chart exported successfully');
    } catch (error) {
        console.error('Export failed:', error);
        alert('Failed to export chart: ' + error.message);
    }
}

// Set up event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    
    const boardSelect = document.getElementById('boardSelect');
    if (boardSelect) {
        boardSelect.addEventListener('change', function() {
            if (this.value) {
                console.log('Board changed to:', this.value);
                window.location = '<?= url('/reports/velocity') ?>/' + this.value;
            }
        });
    }
    
    initChart();
});

// Also try initializing immediately in case DOM is already loaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    console.log('DOM already loaded, initializing chart');
    initChart();
}
</script>

<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/reports') ?>">Reports</a></li>
            <li class="breadcrumb-item active">Velocity Chart</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Velocity Chart</h1>
        <div class="d-flex gap-2 align-items-center">
            <select class="form-select form-select-sm" id="boardSelect" style="width: 180px;">
                <?php foreach ($boards ?? [] as $bd): ?>
                <option value="<?= $bd['id'] ?>" <?= ($selectedBoard ?? '') == $bd['id'] ? 'selected' : '' ?>>
                    <?= e($bd['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-outline-secondary" id="exportBtn" onclick="exportChart()">
                <i class="bi bi-download me-1"></i> Export
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-1">Average Velocity</h5>
                    <h2 class="mb-0"><?= e($averageVelocity ?? 0) ?></h2>
                    <small class="text-muted">points per sprint</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-1">Sprints Analyzed</h5>
                    <h2 class="mb-0" id="sprintCountDisplay">0</h2>
                    <small class="text-muted">closed sprints</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-1">Board</h5>
                    <h2 class="mb-0 text-primary"><?= e($board['name'] ?? '') ?></h2>
                    <small class="text-muted"><?= e($board['project_key'] ?? '') ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Velocity Chart -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">Team Velocity Over Time</h5>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 400px;">
                <canvas id="velocityChart"></canvas>
            </div>
            <div id="noData" style="display: none; text-align: center; padding: 40px; color: #999;">
                <p><i class="bi bi-info-circle" style="font-size: 2rem;"></i></p>
                <p>No sprint data available. Create and complete some sprints first.</p>
            </div>
        </div>
    </div>

    <!-- Sprint Details -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
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
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody id="sprintTable">
                    <tr><td colspan="5" class="text-center text-muted py-4">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
