<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Tracking Schema Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 700px;
            width: 100%;
            padding: 40px;
        }
        
        h1 {
            color: #1a1a1a;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 15px;
        }
        
        .status-box {
            background: #f5f5f5;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .status-box.error {
            border-left-color: #e74c3c;
            background: #fadbd8;
        }
        
        .status-box.success {
            border-left-color: #27ae60;
            background: #d5f4e6;
        }
        
        .button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .button:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .output {
            background: #1a1a1a;
            color: #0f0;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 12px;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
            display: none;
        }
        
        .output.visible {
            display: block;
        }
        
        .output-line {
            line-height: 1.6;
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
            vertical-align: middle;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .check {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: #27ae60;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            margin-right: 10px;
            vertical-align: middle;
            font-size: 14px;
        }
        
        .cross {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            margin-right: 10px;
            vertical-align: middle;
            font-size: 14px;
        }
        
        .warning {
            color: #e67e22;
            font-weight: 600;
        }
        
        .success-text {
            color: #27ae60;
            font-weight: 600;
        }
        
        .error-text {
            color: #e74c3c;
            font-weight: 600;
        }
        
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            color: #1565c0;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Time Tracking Schema Setup</h1>
        <p class="subtitle">Add missing database columns for timer operations</p>
        
        <div class="info-box">
            <strong>ℹ️ What this does:</strong> Adds 7 missing columns to the issue_time_logs table that are required for timer start/pause/resume/stop operations to work correctly.
        </div>
        
        <div class="status-box" id="status">
            Ready to apply schema migration. Click the button below to start.
        </div>
        
        <button class="button" id="applyBtn" onclick="applyMigration()">
            Apply Schema Migration
        </button>
        
        <div class="output" id="output">
            <div id="outputContent"></div>
        </div>
    </div>
    
    <script>
        function addOutput(message, type = 'info') {
            const output = document.getElementById('output');
            const content = document.getElementById('outputContent');
            
            output.classList.add('visible');
            
            let html = '';
            if (type === 'check') {
                html = `<span class="check">✓</span> <span class="success-text">${message}</span>`;
            } else if (type === 'error') {
                html = `<span class="cross">✕</span> <span class="error-text">${message}</span>`;
            } else if (type === 'warn') {
                html = `<span class="warning">⚠</span> <span class="warning">${message}</span>`;
            } else {
                html = message;
            }
            
            content.innerHTML += `<div class="output-line">${html}</div>`;
            output.scrollTop = output.scrollHeight;
        }
        
        function applyMigration() {
            const btn = document.getElementById('applyBtn');
            const status = document.getElementById('status');
            const output = document.getElementById('output');
            const content = document.getElementById('outputContent');
            
            // Reset
            btn.disabled = true;
            output.classList.add('visible');
            content.innerHTML = '';
            
            btn.innerHTML = '<span class="loading"></span> Applying migration...';
            
            addOutput('Starting schema migration...\n', 'info');
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ action: 'apply_migration' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addOutput('Migration executed successfully!', 'check');
                    addOutput(`\nApplied ${data.statements} SQL statements`, 'check');
                    addOutput('\nVerifying schema...', 'info');
                    
                    if (data.columns && data.columns.length > 0) {
                        addOutput(`\nFound ${data.columns.length} required columns:`, 'check');
                        data.columns.forEach(col => {
                            addOutput(`  • ${col}`, 'info');
                        });
                    }
                    
                    if (data.missing && data.missing.length > 0) {
                        addOutput(`\n⚠️ Still missing ${data.missing.length} columns:`, 'warn');
                        data.missing.forEach(col => {
                            addOutput(`  ✗ ${col}`, 'error');
                        });
                    } else {
                        addOutput('\n✅ All required columns present!', 'check');
                        status.innerHTML = '✅ Schema migration completed successfully!';
                        status.className = 'status-box success';
                    }
                    
                    addOutput('\n\n═══════════════════════════════════════════════════', 'info');
                    addOutput('✅ SCHEMA FIX COMPLETE', 'check');
                    addOutput('═══════════════════════════════════════════════════', 'info');
                    addOutput('\nYou can now:', 'info');
                    addOutput('  1. Go to /time-tracking/project/1', 'info');
                    addOutput('  2. Start a timer', 'info');
                    addOutput('  3. Pause the timer', 'info');
                    addOutput('  4. Resume the timer (should work now!)', 'info');
                    
                    btn.style.display = 'none';
                } else {
                    addOutput(`Error: ${data.message}`, 'error');
                    status.innerHTML = '❌ Migration failed. Check output below.';
                    status.className = 'status-box error';
                    btn.disabled = false;
                    btn.innerHTML = 'Retry';
                }
            })
            .catch(error => {
                addOutput(`Error: ${error.message}`, 'error');
                status.innerHTML = '❌ Request failed. Check console.';
                status.className = 'status-box error';
                btn.disabled = false;
                btn.innerHTML = 'Retry';
            });
        }
    </script>
    
    <?php
    // Handle AJAX request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        
        try {
            require_once '../bootstrap/autoload.php';
            use App\Core\Database;
            
            if ($_REQUEST['action'] === 'apply_migration') {
                // Read migration SQL
                $migrationSql = file_get_contents('../database/migrations/007_fix_time_tracking_schema.sql');
                
                // Split into statements
                $statements = array_filter(
                    array_map('trim', explode(';', $migrationSql)),
                    fn($s) => !empty($s) && !str_starts_with(trim($s), '--')
                );
                
                $count = 0;
                foreach ($statements as $statement) {
                    if (empty(trim($statement))) continue;
                    Database::query($statement);
                    $count++;
                }
                
                // Verify columns
                $columns = Database::select(
                    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_NAME = 'issue_time_logs' 
                     AND TABLE_SCHEMA = DATABASE()"
                );
                
                $existingColumns = array_map(fn($c) => $c['COLUMN_NAME'], $columns);
                $requiredColumns = [
                    'id', 'issue_id', 'user_id', 'project_id',
                    'start_time', 'end_time', 'paused_at', 'resumed_at',
                    'pause_count', 'total_paused_seconds', 'paused_seconds',
                    'duration_seconds', 'cost_calculated', 'total_cost',
                    'currency', 'description', 'work_date', 'is_billable',
                    'user_rate_type', 'user_rate_amount', 'status',
                    'created_at', 'updated_at'
                ];
                
                $missing = array_diff($requiredColumns, $existingColumns);
                
                echo json_encode([
                    'success' => true,
                    'statements' => $count,
                    'columns' => $existingColumns,
                    'missing' => array_values($missing)
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    ?>
</body>
</html>
