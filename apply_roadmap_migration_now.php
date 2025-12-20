<?php
/**
 * Apply Roadmap Migration
 * Creates roadmap_items and related tables
 */

declare(strict_types=1);

session_start();
require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Apply Roadmap Migration</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #8b1956; border-radius: 4px; }
        .success { background: #f1f8f4; border-left-color: #4caf50; }
        .error { background: #fde8e8; border-left-color: #f44336; }
        .warning { background: #fff3e0; border-left-color: #ff9800; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
        button { background: #8b1956; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        button:hover { background: #6f123f; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Apply Roadmap Migration</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_migration'])) {
            echo '<div class="section success">';
            echo '<h2>✅ Migration Applied</h2>';
            
            try {
                // Read migration file
                $migrationPath = __DIR__ . '/database/migrations/003_create_roadmap_tables.sql';
                
                if (!file_exists($migrationPath)) {
                    throw new Exception('Migration file not found: ' . $migrationPath);
                }
                
                // Read SQL content
                $sql = file_get_contents($migrationPath);
                
                if (!$sql) {
                    throw new Exception('Could not read migration file');
                }
                
                echo '<p>Migration file: <code>' . basename($migrationPath) . '</code></p>';
                echo '<p>Executing SQL statements...</p>';
                
                // Split by semicolon and execute each statement
                $statements = array_filter(
                    array_map('trim', explode(';', $sql)),
                    fn($stmt) => !empty($stmt) && !str_starts_with($stmt, '--') && !str_starts_with($stmt, '/*')
                );
                
                $statementCount = count($statements);
                echo '<p>Found <strong>' . $statementCount . '</strong> SQL statements to execute</p>';
                
                foreach ($statements as $index => $statement) {
                    try {
                        // Skip comments and whitespace-only statements
                        $stmt = trim($statement);
                        if (empty($stmt) || str_starts_with($stmt, '--') || str_starts_with($stmt, '/*')) {
                            continue;
                        }
                        
                        // Execute statement
                        Database::statement($stmt . ';');
                        
                        // Extract table name for feedback
                        if (preg_match('/CREATE TABLE[S]?\s+(?:IF NOT EXISTS\s+)?`?(\w+)`?/i', $stmt, $matches)) {
                            echo '<p>✓ Created table: <code>' . htmlspecialchars($matches[1]) . '</code></p>';
                        }
                    } catch (\Exception $e) {
                        // Some statements might fail silently (e.g., CREATE TABLE IF NOT EXISTS), that's OK
                        if (strpos($e->getMessage(), 'already exists') === false) {
                            echo '<p class="warning">⚠ Warning: ' . htmlspecialchars($e->getMessage()) . '</p>';
                        }
                    }
                }
                
                echo '<h3>✅ Migration completed successfully!</h3>';
                echo '<p>The following tables should now exist:</p>';
                echo '<ul>';
                echo '<li><code>roadmap_items</code> - Main roadmap items table</li>';
                echo '<li><code>roadmap_item_sprints</code> - Links roadmap items to sprints</li>';
                echo '<li><code>roadmap_dependencies</code> - Tracks dependencies between items</li>';
                echo '<li><code>roadmap_item_issues</code> - Links roadmap items to issues</li>';
                echo '</ul>';
                
                echo '<h3>Next Steps:</h3>';
                echo '<ol>';
                echo '<li><a href="' . url('/projects/CWAYS/roadmap') . '">Go to Roadmap Page</a></li>';
                echo '<li>Click "+ Add Item"</li>';
                echo '<li>Fill in the form and submit</li>';
                echo '<li>Your item should now appear in the timeline</li>';
                echo '</ol>';
                
            } catch (\Exception $e) {
                echo '<div class="section error">';
                echo '<h2>❌ Error</h2>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
            
            echo '</div>';
        } else {
            // Show migration details
            echo '<div class="section warning">';
            echo '<h2>⚠️ Roadmap tables may not exist</h2>';
            echo '<p>This script will create the following tables:</p>';
            echo '<ul>';
            echo '<li><strong>roadmap_items</strong> - Core roadmap items with status, dates, progress</li>';
            echo '<li><strong>roadmap_item_sprints</strong> - Links items to sprints for planning</li>';
            echo '<li><strong>roadmap_dependencies</strong> - Tracks dependencies between roadmap items</li>';
            echo '<li><strong>roadmap_item_issues</strong> - Links roadmap items to issues for tracking</li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<form method="POST">';
            echo '<input type="hidden" name="apply_migration" value="1">';
            echo '<button type="submit">Apply Migration Now</button>';
            echo '</form>';
            
            // Check current status
            echo '<div class="section">';
            echo '<h2>Current Database Status</h2>';
            
            try {
                $result = Database::select(
                    "SELECT TABLE_NAME FROM information_schema.TABLES 
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME LIKE 'roadmap%'",
                    ['jiira_clonee_system']
                );
                
                if (!empty($result)) {
                    echo '<p style="color: #4caf50;"><strong>✓</strong> Found ' . count($result) . ' roadmap tables:</p>';
                    echo '<ul>';
                    foreach ($result as $table) {
                        echo '<li><code>' . htmlspecialchars($table['TABLE_NAME']) . '</code></li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p style="color: #f44336;"><strong>✗</strong> No roadmap tables found in database</p>';
                    echo '<p>Click "Apply Migration Now" above to create them</p>';
                }
            } catch (\Exception $e) {
                echo '<p style="color: #ff9800;"><strong>⚠</strong> Could not check database: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
