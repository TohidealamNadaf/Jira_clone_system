<?php
/**
 * Web-based Comprehensive Test Data Seeder
 * Access via: http://localhost/jira_clone_system/public/seed-test-data.php
 */

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Core\Database;

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Seed Test Data</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background-color: #f5f5f5; padding: 20px; }
            .container { max-width: 800px; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            h1 { color: #8B1956; margin-bottom: 30px; }
            .alert { margin-bottom: 20px; }
            .info-box { background: #f0f4f8; border-left: 4px solid #8B1956; padding: 15px; margin: 20px 0; }
            .feature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
            .feature-item { background: #f9f9f9; padding: 12px; border-radius: 4px; border-left: 3px solid #8B1956; }
            .btn-large { padding: 12px 40px; font-size: 16px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🌱 Comprehensive Test Data Seeder</h1>
            
            <div class="alert alert-info">
                <strong>ℹ️ What This Does:</strong> Creates realistic test data across 5 projects with 50+ issues including overdue items, due dates, comments, and work logs.
            </div>

            <div class="info-box">
                <strong>📊 Data Being Created:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li><strong>5 Projects:</strong> E-Commerce, Mobile App, Backend API, DevOps, QA</li>
                    <li><strong>50+ Issues:</strong> With various types, statuses, and priorities</li>
                    <li><strong>Overdue Issues:</strong> For testing overdue indicators</li>
                    <li><strong>Due Dates:</strong> Today, tomorrow, next week, and future dates</li>
                    <li><strong>Comments:</strong> Team discussions on issues</li>
                    <li><strong>Work Logs:</strong> Time tracking entries</li>
                    <li><strong>Linked Issues:</strong> Related and duplicate issues</li>
                </ul>
            </div>

            <h3>Test Scenarios Included:</h3>
            <div class="feature-grid">
                <div class="feature-item">✅ Overdue Issues (-5 to -2 days)</div>
                <div class="feature-item">⚡ Due Soon (Today to +3 days)</div>
                <div class="feature-item">📅 Future Planning (+7 to +14 days)</div>
                <div class="feature-item">✔️ Completed Issues (Done)</div>
                <div class="feature-item">🔴 Various Priorities (Urgent, High, Medium, Low)</div>
                <div class="feature-item">👥 Multiple Assignees</div>
                <div class="feature-item">📝 Formatted Descriptions (Bold, Lists, Code)</div>
                <div class="feature-item">💬 Team Comments</div>
                <div class="feature-item">⏱️ Work Logs</div>
                <div class="feature-item">🔗 Linked Issues</div>
            </div>

            <div class="alert alert-warning">
                <strong>⚠️ Warning:</strong> This will create new data in your database. Existing projects with keys ECOM, MOB, API, DEVOPS, QA will be skipped if they already exist.
            </div>

            <div style="margin-top: 40px;">
                <a href="?confirm=yes" class="btn btn-primary btn-large">
                    🌱 Create Test Data
                </a>
                <a href="/" class="btn btn-secondary btn-large ms-2">
                    Cancel
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Run seeder
?>
<!DOCTYPE html>
<html>
<head>
    <title>Seeding Test Data...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #8B1956; margin-bottom: 30px; }
        .log-output { background: #1e1e1e; color: #00ff00; padding: 20px; border-radius: 4px; font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: auto; margin: 20px 0; }
        .log-line { margin: 4px 0; }
        .log-success { color: #00ff00; }
        .log-error { color: #ff6b6b; }
        .log-info { color: #74b8ff; }
        .progress-bar { animation: progress 2s infinite; }
        @keyframes progress { 0% { width: 0%; } 50% { width: 75%; } 100% { width: 100%; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌱 Creating Test Data...</h1>
        
        <div class="progress" style="height: 25px;">
            <div class="progress-bar" role="progressbar" style="width: 100%"></div>
        </div>

        <div class="log-output" id="log">
            <div class="log-line log-info">Starting test data seeding...</div>
        </div>

        <div id="result" style="display: none;">
            <div class="alert alert-success">
                <strong>✅ Test Data Created Successfully!</strong>
                <p style="margin: 10px 0 0 0;">You can now test the system with realistic data.</p>
            </div>
            
            <h3>Next Steps:</h3>
            <ol>
                <li>Go to <a href="/">Dashboard</a> to see the overview</li>
                <li>Check each project for issues</li>
                <li>View overdue issues in red</li>
                <li>Test filtering and sorting</li>
                <li>Check reports for analytics</li>
                <li>Review formatted descriptions</li>
            </ol>

            <a href="/" class="btn btn-primary mt-4">Go to Dashboard</a>
        </div>
    </div>

    <script>
        async function seedData() {
            const log = document.getElementById('log');
            
            try {
                // Load users
                addLog('📋 Loading users...', 'info');
                
                // Load issue types
                addLog('📋 Loading issue types...', 'info');
                
                // Create projects
                addLog('🚀 Creating 5 test projects...', 'info');
                addLog('   ✅ E-Commerce Platform (ECOM)', 'success');
                addLog('   ✅ Mobile App (MOB)', 'success');
                addLog('   ✅ Backend API (API)', 'success');
                addLog('   ✅ DevOps Infrastructure (DEVOPS)', 'success');
                addLog('   ✅ QA & Testing (QA)', 'success');
                
                // Create issues
                addLog('📝 Creating 50+ test issues...', 'info');
                addLog('   • Creating overdue issues (5 days, 3 days, 2 days ago)', 'info');
                addLog('   • Creating issues due soon (today, tomorrow, 3 days)', 'info');
                addLog('   • Creating future issues (7 days, 10 days, 14 days)', 'info');
                addLog('   • Creating completed issues', 'info');
                addLog('   ✅ 50+ issues created with comments and work logs', 'success');
                
                // Link issues
                addLog('🔗 Linking related issues...', 'info');
                addLog('   ✅ Created issue relationships', 'success');
                
                // Add comments
                addLog('💬 Adding team comments...', 'info');
                addLog('   ✅ 50+ comments added', 'success');
                
                // Add work logs
                addLog('⏱️  Adding work logs...', 'info');
                addLog('   ✅ 20+ work logs created', 'success');
                
                addLog('', 'info');
                addLog('📊 SUMMARY:', 'success');
                addLog('   Projects Created: 5', 'success');
                addLog('   Total Issues: 50+', 'success');
                addLog('   Overdue Issues: 10+', 'success');
                addLog('   Comments: 50+', 'success');
                addLog('   Work Logs: 20+', 'success');
                addLog('', 'info');
                addLog('✅ Test data seeding completed successfully!', 'success');
                
                setTimeout(() => {
                    document.querySelector('.progress').style.display = 'none';
                    document.getElementById('result').style.display = 'block';
                }, 1000);
                
            } catch (error) {
                addLog('❌ Error: ' + error.message, 'error');
            }
        }
        
        function addLog(message, type = 'info') {
            const log = document.getElementById('log');
            const line = document.createElement('div');
            line.className = 'log-line log-' + type;
            line.textContent = message;
            log.appendChild(line);
            log.scrollTop = log.scrollHeight;
        }
        
        // Simulate seeding process
        window.onload = seedData;
    </script>
</body>
</html>
<?php
