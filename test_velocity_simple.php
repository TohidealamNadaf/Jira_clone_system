<?php
/**
 * Simple velocity chart test
 * Access: http://localhost/jira_clone_system/public/test_velocity_simple.php
 */

require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Velocity Chart Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Velocity Chart Test</h2>
    
    <div class="alert alert-info">
        <strong>Testing velocity chart data and rendering</strong>
        <button class="btn btn-sm btn-primary float-end" onclick="location.reload()">Refresh</button>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h5>Database Data</h5>
            <pre id="dbData" style="background: #f5f5f5; padding: 10px; border-radius: 4px;">Loading...</pre>
        </div>
        <div class="col-md-6">
            <h5>Chart Test</h5>
            <div style="height: 300px;">
                <canvas id="testChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Get data from database
fetch('<?= url('/reports/velocity/1') ?>', {
    method: 'GET',
    headers: {
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.catch(err => {
    console.error('Error fetching JSON:', err);
    // Try to get HTML and extract data
    return fetch('<?= url('/reports/velocity/1') ?>')
        .then(response => response.text())
        .then(html => {
            console.log('Got HTML response');
            return { error: 'Got HTML instead of JSON' };
        });
})
.then(data => {
    console.log('Response data:', data);
    
    if (data.error) {
        document.getElementById('dbData').innerHTML = '<strong style="color: red;">Error:</strong> ' + data.error;
        return;
    }
    
    if (data.velocity) {
        document.getElementById('dbData').innerHTML = '<strong>Velocity Data:</strong><pre>' + JSON.stringify(data.velocity, null, 2) + '</pre>' +
            '<strong>Average Velocity:</strong> ' + data.average_velocity +
            '<br><strong>Sprint Count:</strong> ' + data.sprint_count;
        
        // Draw test chart
        const labels = data.velocity.map(d => d.sprint_name);
        const committed = data.velocity.map(d => d.committed);
        const completed = data.velocity.map(d => d.completed);
        
        const ctx = document.getElementById('testChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Committed',
                        data: committed,
                        backgroundColor: 'rgba(200, 100, 100, 0.7)'
                    },
                    {
                        label: 'Completed',
                        data: completed,
                        backgroundColor: 'rgba(100, 200, 100, 0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    } else {
        document.getElementById('dbData').innerHTML = 'No velocity data in response. Full response:<pre>' + JSON.stringify(data, null, 2) + '</pre>';
    }
})
.catch(err => {
    console.error('Fetch error:', err);
    document.getElementById('dbData').innerHTML = '<strong style="color: red;">Error:</strong> ' + err.message;
});
</script>
</body>
</html>
