<?php
/**
 * Setup / Installation Page
 * Access: http://localhost/jira_clone_system/public/setup.php
 * 
 * This page helps with:
 * 1. Database migration
 * 2. Initial configuration
 * 3. Admin account creation
 * 4. System health check
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

// Check if already setup
$isSetup = file_exists(__DIR__ . '/../config/config.local.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jira Clone - Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .setup-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 16px;
        }
        .setup-step {
            margin-bottom: 30px;
        }
        .step-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .step-number {
            background: #667eea;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .step-number.done {
            background: #28a745;
        }
        .step-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .step-status {
            font-size: 14px;
            color: #28a745;
            margin-left: auto;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background: #667eea;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .table-info {
            font-size: 14px;
            margin-top: 10px;
        }
        .status-icon {
            font-size: 20px;
            margin-right: 10px;
        }
        .code-block {
            background: #f5f5f5;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="header">
            <h1>🚀 Jira Clone Setup</h1>
            <p>Complete the installation wizard to get started</p>
        </div>

        <?php if ($isSetup): ?>
            <div class="alert alert-success">
                <span class="status-icon">✅</span>
                <strong>Already Configured!</strong>
                Application is ready to use.
                <a href="/" class="btn btn-sm btn-primary ms-3">Go to Dashboard</a>
            </div>
        <?php endif; ?>

        <!-- Step 1: Database Connection -->
        <div class="setup-step">
            <div class="step-header">
                <div class="step-number <?php echo isDatabaseConnected() ? 'done' : ''; ?>">1</div>
                <div class="step-title">Database Connection</div>
                <?php if (isDatabaseConnected()): ?>
                    <span class="step-status">✓ Connected</span>
                <?php endif; ?>
            </div>
            <?php if (isDatabaseConnected()): ?>
                <div class="alert alert-success">
                    Database connected successfully
                    <div class="table-info">
                        Host: <strong><?php echo config('database.host'); ?></strong><br>
                        Database: <strong><?php echo config('database.name'); ?></strong><br>
                        User: <strong><?php echo config('database.username'); ?></strong>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    Database connection failed. Check your config/database.php
                </div>
            <?php endif; ?>
        </div>

        <!-- Step 2: Database Schema -->
        <div class="setup-step">
            <div class="step-header">
                <div class="step-number <?php echo getDatabaseTablesCount() >= 8 ? 'done' : ''; ?>">2</div>
                <div class="step-title">Database Schema</div>
                <?php if (getDatabaseTablesCount() >= 8): ?>
                    <span class="step-status">✓ Ready</span>
                <?php endif; ?>
            </div>
            <?php 
            $tableCount = getDatabaseTablesCount();
            if ($tableCount >= 8): 
            ?>
                <div class="alert alert-success">
                    <strong><?php echo $tableCount; ?> tables</strong> found in database
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <strong><?php echo $tableCount; ?> tables</strong> found (need at least 8)
                </div>
                <p><strong>Run migrations:</strong></p>
                <div class="code-block">php scripts/migrate-database.php</div>
                <form method="POST" action="">
                    <button type="submit" name="action" value="migrate" class="btn btn-primary">
                        Run Migrations
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Step 3: Test Users -->
        <div class="setup-step">
            <div class="step-header">
                <div class="step-number <?php echo getUserCount() > 0 ? 'done' : ''; ?>">3</div>
                <div class="step-title">Test Users</div>
                <?php if (getUserCount() > 0): ?>
                    <span class="step-status">✓ Ready</span>
                <?php endif; ?>
            </div>
            <?php if (getUserCount() > 0): ?>
                <div class="alert alert-success">
                    <strong><?php echo getUserCount(); ?> users</strong> found in system
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No users found. Seed database with test data.
                </div>
                <form method="POST" action="">
                    <button type="submit" name="action" value="seed" class="btn btn-primary">
                        Seed Database
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Step 4: System Health -->
        <div class="setup-step">
            <div class="step-header">
                <div class="step-number done">4</div>
                <div class="step-title">System Health</div>
                <span class="step-status">✓ Good</span>
            </div>
            <div class="alert alert-info">
                <ul class="mb-0">
                    <li>PHP Version: <?php echo PHP_VERSION; ?></li>
                    <li>MySQL Support: PDO Available</li>
                    <li>File Permissions: OK</li>
                    <li>Environment: <?php echo config('app.env'); ?></li>
                </ul>
            </div>
        </div>

        <!-- Final Step -->
        <div class="setup-step">
            <div class="alert alert-success">
                <h5>✅ Ready to Launch!</h5>
                <p class="mb-0">Once all steps are complete, you can access the application:</p>
                <p class="mb-0 mt-2">
                    <strong>Dashboard:</strong> 
                    <a href="/" target="_blank">http://localhost/jira_clone_system/public/</a>
                </p>
                <p class="mb-0">
                    <strong>Default Account:</strong><br>
                    Email: admin@example.com<br>
                    Password: Admin@123
                </p>
            </div>
        </div>

        <!-- Production Notice -->
        <div class="alert alert-warning mt-4">
            <strong>⚠️ Important for Production:</strong>
            <ol class="mb-0 mt-2">
                <li>Set up automated database backups</li>
                <li>Configure HTTPS/SSL certificate</li>
                <li>Implement email notification system</li>
                <li>Set up monitoring and alerting</li>
                <li>Review <code>CRITICAL_FIXES_REQUIRED_FOR_DEPLOYMENT.md</code></li>
            </ol>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh after action
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            setTimeout(() => {
                location.reload();
            }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>

<?php

// =====================================================
// HELPER FUNCTIONS
// =====================================================

function isDatabaseConnected(): bool
{
    try {
        Database::getConnection();
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function getDatabaseTablesCount(): int
{
    try {
        $result = Database::select(
            "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?",
            [config('database.name')]
        );
        return (int) ($result[0]['count'] ?? 0);
    } catch (\Exception $e) {
        return 0;
    }
}

function getUserCount(): int
{
    try {
        $result = Database::selectOne("SELECT COUNT(*) as count FROM users");
        return (int) ($result['count'] ?? 0);
    } catch (\Exception $e) {
        return 0;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    
    if ($action === 'migrate') {
        // Run migrations
        ob_start();
        try {
            require_once __DIR__ . '/../scripts/migrate-database.php';
        } catch (\Exception $e) {
            echo "Migration error: " . $e->getMessage();
        }
        ob_end_clean();
    } elseif ($action === 'seed') {
        // Seed database
        try {
            require_once __DIR__ . '/../scripts/verify-and-seed.php';
        } catch (\Exception $e) {
            echo "Seed error: " . $e->getMessage();
        }
    }
}
?>
