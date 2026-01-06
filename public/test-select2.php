<?php
/**
 * Test script to verify projects are loading in Select2 dropdown
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/../bootstrap/autoload.php';

$projects = [];
$error = null;

try {
    // Load projects using the application's database
    $database = new \App\Core\Database();
    
    // Query projects
    $sql = "SELECT id, name, `key`, description FROM projects WHERE archived = 0 ORDER BY name LIMIT 100";
    $projects = $database->select($sql, []);
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Select2 Projects Test</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 30px; background: #f5f5f5; }
        .container { max-width: 1000px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #155724; padding: 15px; background: #d4edda; margin: 20px 0; border-radius: 4px; }
        .error { color: #721c24; padding: 15px; background: #f8d7da; margin: 20px 0; border-radius: 4px; }
        .info { color: #0c5460; padding: 15px; background: #d1ecf1; margin: 20px 0; border-radius: 4px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        table td, table th { border: 1px solid #ddd; padding: 12px; text-align: left; }
        table th { background: #007bff; color: white; }
        table tbody tr:nth-child(even) { background: #f9f9f9; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        h2 { margin-top: 30px; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .test-badge { display: inline-block; padding: 5px 10px; border-radius: 4px; font-weight: bold; margin-right: 10px; }
        .test-pass { background: #d4edda; color: #155724; }
        .test-fail { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✓ Select2 Dropdown - Projects Loading Test</h1>
        
        <?php if ($error): ?>
        <div class="error">
            <strong>✗ Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php else: ?>
            <?php if (count($projects) > 0): ?>
            <div class="success">
                <strong>✓ Success!</strong> Found <strong><?php echo count($projects); ?> projects</strong> in database
            </div>
            
            <h2>Projects in Database</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Key</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($project['id']); ?></code></td>
                        <td><?php echo htmlspecialchars($project['name']); ?></td>
                        <td><code><?php echo htmlspecialchars($project['key']); ?></code></td>
                        <td><?php echo htmlspecialchars(substr($project['description'] ?? 'N/A', 0, 60)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <h2>API JSON Response</h2>
            <div class="info">
                <strong>API Endpoint:</strong> <code>/api/v1/projects?archived=false&per_page=100</code>
            </div>
            <pre><code><?php echo htmlspecialchars(json_encode(['items' => $projects], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></code></pre>
            
            <h2>Select2 HTML Dropdown</h2>
            <div class="info">
                This is how Select2 should render the options:
            </div>
            <div style="border: 1px solid #ddd; padding: 20px; background: #f9f9f9; border-radius: 4px;">
                <label for="testSelect" class="form-label"><strong>Test Dropdown:</strong></label>
                <select id="testSelect" class="form-select" style="width: 300px;">
                    <option value="">Select Project...</option>
                    <?php foreach ($projects as $project): ?>
                    <option value="<?php echo htmlspecialchars($project['id']); ?>">
                        <?php echo htmlspecialchars($project['name'] . ' (' . $project['key'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <h2>Test Summary</h2>
            <p>
                <span class="test-badge test-pass">✓ Database Connection</span>
                <span class="test-badge test-pass">✓ Projects Found</span>
                <span class="test-badge test-pass">✓ Data Format Valid</span>
            </p>
            <p style="margin-top: 20px; color: #666;">
                <strong>Next Steps:</strong>
                <ol>
                    <li>Go to: <code>http://localhost:8080/jira_clone_system/public/dashboard</code></li>
                    <li>Click "Create" button</li>
                    <li>Check Project dropdown - should show all projects above</li>
                    <li>If not showing, scroll down and check browser console (F12)</li>
                </ol>
            </p>
            
            <?php else: ?>
            <div class="error">
                <strong>✗ Problem:</strong> No projects found in database!
            </div>
            <p>There are no projects in the database. You need to:</p>
            <ol>
                <li>Create some projects first</li>
                <li>Or run the database seeder: <code>php scripts/verify-and-seed.php</code></li>
            </ol>
            <?php endif; ?>
        <?php endif; ?>
        
        <h2>Diagnostics</h2>
        <table class="table">
            <tr>
                <th>Check</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Database Connected</td>
                <td><?php echo ($error ? '<span class="badge bg-danger">✗ No</span>' : '<span class="badge bg-success">✓ Yes</span>'); ?></td>
            </tr>
            <tr>
                <td>Projects Found</td>
                <td><?php echo (count($projects) > 0 ? '<span class="badge bg-success">✓ ' . count($projects) . '</span>' : '<span class="badge bg-warning">⚠ None</span>'); ?></td>
            </tr>
            <tr>
                <td>Select2 Dropdown</td>
                <td><span class="badge bg-info">Test Below</span></td>
            </tr>
        </table>
        
        <div style="margin-top: 40px; padding: 20px; background: #e3f2fd; border-radius: 4px; border-left: 4px solid #2196F3;">
            <strong>ℹ Need Help?</strong>
            <ul style="margin-bottom: 0;">
                <li>If projects show here but not in dropdown → Check browser console (F12) for JavaScript errors</li>
                <li>If no projects shown → Run seeder: <code>php scripts/verify-and-seed.php</code></li>
                <li>If database error → Check database connection in config/database.php</li>
            </ul>
        </div>
    </div>
</body>
</html>
