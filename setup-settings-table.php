<?php
/**
 * User Settings Table Setup Script
 * Creates the user_settings table with all required columns for time tracking and preferences
 * 
 * Usage: Visit http://localhost:8081/jira_clone_system/public/setup-settings-table.php
 */

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

try {
    // Get database connection
    $db = Database::getInstance();
    
    // Read the migration file
    $migrationPath = __DIR__ . '/database/migrations/002_create_user_settings_table.sql';
    
    if (!file_exists($migrationPath)) {
        throw new Exception("Migration file not found: {$migrationPath}");
    }
    
    $sql = file_get_contents($migrationPath);
    
    // Split and execute multiple statements
    $statements = array_filter(
        array_map('trim', preg_split('/;[\s]*\n/', $sql)),
        function($stmt) {
            return !empty($stmt) && strpos(trim($stmt), '--') !== 0;
        }
    );
    
    $successCount = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        try {
            if (!empty(trim($statement))) {
                $db->statement($statement);
                $successCount++;
            }
        } catch (Exception $e) {
            // Log error but continue with other statements
            $errors[] = [
                'statement' => substr($statement, 0, 100) . '...',
                'error' => $e->getMessage()
            ];
        }
    }
    
    // Check if table was created
    $tableExists = $db->table('INFORMATION_SCHEMA.TABLES')
        ->where('TABLE_SCHEMA', '=', 'jiira_clonee_system')
        ->where('TABLE_NAME', '=', 'user_settings')
        ->count() > 0;
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Settings Table Setup</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
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
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 700px;
                width: 100%;
                padding: 40px;
            }
            
            h1 {
                color: #333;
                margin-bottom: 10px;
                font-size: 28px;
            }
            
            .subtitle {
                color: #666;
                margin-bottom: 30px;
                font-size: 14px;
            }
            
            .status-box {
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                border-left: 4px solid;
            }
            
            .status-box.success {
                background: #d4edda;
                border-color: #28a745;
                color: #155724;
            }
            
            .status-box.error {
                background: #f8d7da;
                border-color: #dc3545;
                color: #721c24;
            }
            
            .status-box.warning {
                background: #fff3cd;
                border-color: #ffc107;
                color: #856404;
            }
            
            .status-box h3 {
                margin-bottom: 10px;
                font-size: 16px;
            }
            
            .status-box p {
                margin: 8px 0;
                font-size: 14px;
                line-height: 1.5;
            }
            
            .stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
                margin-bottom: 30px;
            }
            
            .stat {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 6px;
                border-left: 3px solid #667eea;
                text-align: center;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: bold;
                color: #667eea;
            }
            
            .stat-label {
                font-size: 12px;
                color: #666;
                margin-top: 5px;
                text-transform: uppercase;
            }
            
            .features {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
            }
            
            .features h3 {
                margin-bottom: 15px;
                color: #333;
                font-size: 14px;
                text-transform: uppercase;
                font-weight: 600;
            }
            
            .features-list {
                list-style: none;
            }
            
            .features-list li {
                padding: 8px 0;
                color: #555;
                font-size: 13px;
                padding-left: 25px;
                position: relative;
            }
            
            .features-list li:before {
                content: "✓";
                position: absolute;
                left: 0;
                color: #28a745;
                font-weight: bold;
            }
            
            .button-group {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            
            .btn {
                padding: 12px 24px;
                border-radius: 6px;
                border: none;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
                transition: all 0.3s ease;
            }
            
            .btn-primary {
                background: #667eea;
                color: white;
            }
            
            .btn-primary:hover {
                background: #5568d3;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
            
            .btn-secondary {
                background: #6c757d;
                color: white;
            }
            
            .btn-secondary:hover {
                background: #5a6268;
                transform: translateY(-2px);
            }
            
            .errors {
                background: #f8d7da;
                border: 1px solid #f5c6cb;
                border-radius: 6px;
                padding: 15px;
                margin-top: 20px;
            }
            
            .errors h4 {
                color: #721c24;
                margin-bottom: 10px;
                font-size: 14px;
            }
            
            .error-item {
                background: white;
                padding: 10px;
                border-radius: 4px;
                margin-bottom: 8px;
                font-size: 12px;
                color: #333;
                font-family: 'Courier New', monospace;
            }
            
            .error-item strong {
                color: #dc3545;
            }
            
            code {
                background: #f5f5f5;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>⚙️ User Settings Table Setup</h1>
            <p class="subtitle">Initialize the database table for user preferences and time tracking rates</p>
            
            <?php if ($tableExists): ?>
                <div class="status-box success">
                    <h3>✓ Setup Successful</h3>
                    <p>The <code>user_settings</code> table has been created successfully!</p>
                    <p>Executed <strong><?php echo $successCount; ?></strong> database statements.</p>
                </div>
                
                <div class="stats">
                    <div class="stat">
                        <div class="stat-value"><?php echo $successCount; ?></div>
                        <div class="stat-label">Statements</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">14+</div>
                        <div class="stat-label">Columns</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">4</div>
                        <div class="stat-label">Indexes</div>
                    </div>
                </div>
                
                <div class="features">
                    <h3>🎯 Supported Features</h3>
                    <ul class="features-list">
                        <li>Language & Timezone preferences</li>
                        <li>Date format customization</li>
                        <li>Pagination settings (items per page)</li>
                        <li>Display preferences (compact view, auto-refresh)</li>
                        <li>Privacy settings (profile, activity, email visibility)</li>
                        <li>Accessibility options (high contrast, reduced motion)</li>
                        <li><strong>Time Tracking Rates (NEW)</strong></li>
                        <li>Automatic rate calculations (hourly, minute, second, daily)</li>
                        <li>Multi-currency support (USD, EUR, GBP, INR)</li>
                    </ul>
                </div>
                
                <div class="button-group">
                    <a href="<?php echo url('/profile/settings'); ?>" class="btn btn-primary">Go to Settings</a>
                    <a href="<?php echo url('/'); ?>" class="btn btn-secondary">Back to Home</a>
                </div>
            <?php else: ?>
                <div class="status-box error">
                    <h3>✗ Setup Failed</h3>
                    <p>The <code>user_settings</code> table could not be created.</p>
                    <p>Please check the error details below and try again.</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="errors">
                        <h4>Database Errors (<?php echo count($errors); ?>):</h4>
                        <?php foreach ($errors as $error): ?>
                            <div class="error-item">
                                <strong>Statement:</strong> <?php echo htmlspecialchars($error['statement']); ?><br>
                                <strong>Error:</strong> <?php echo htmlspecialchars($error['error']); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="button-group" style="margin-top: 20px;">
                    <a href="setup-settings-table.php" class="btn btn-primary">Retry Setup</a>
                    <a href="<?php echo url('/'); ?>" class="btn btn-secondary">Cancel</a>
                </div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    
    <?php
} catch (Exception $e) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Setup Error</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #f5f5f5;
                padding: 40px 20px;
            }
            .error-container {
                background: white;
                border-left: 4px solid #dc3545;
                padding: 30px;
                max-width: 600px;
                margin: 0 auto;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            h1 {
                color: #dc3545;
                margin-bottom: 10px;
            }
            p {
                color: #666;
                line-height: 1.6;
            }
            code {
                background: #f5f5f5;
                padding: 4px 8px;
                border-radius: 4px;
                font-family: 'Courier New', monospace;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>⚠️ Setup Error</h1>
            <p><strong>Error:</strong> <?php echo htmlspecialchars($e->getMessage()); ?></p>
            <p>Please ensure:</p>
            <ul>
                <li>Database connection is configured in <code>config/config.php</code></li>
                <li>Database user has CREATE TABLE permissions</li>
                <li>MySQL server is running</li>
            </ul>
        </div>
    </body>
    </html>
    <?php
}
