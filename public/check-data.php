<?php
/**
 * Diagnostic script to check database contents
 */

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Diagnostic</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 5px; max-width: 800px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007bff; color: white; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ff9800; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Diagnostic</h1>
        
        <h2>Issue Types</h2>
        <?php
        $types = Database::select("SELECT * FROM issue_types");
        if (empty($types)) {
            echo '<p class="error">❌ No issue types found in database</p>';
        } else {
            echo '<p class="success">✅ Found ' . count($types) . ' issue types</p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th><th>Icon</th><th>Is Subtask</th><th>Sort Order</th></tr>';
            foreach ($types as $type) {
                echo '<tr>';
                echo '<td>' . $type['id'] . '</td>';
                echo '<td>' . $type['name'] . '</td>';
                echo '<td>' . $type['icon'] . '</td>';
                echo '<td>' . $type['is_subtask'] . '</td>';
                echo '<td>' . $type['sort_order'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
        
        <h2>Issue Priorities</h2>
        <?php
        $priorities = Database::select("SELECT * FROM issue_priorities");
        if (empty($priorities)) {
            echo '<p class="error">❌ No priorities found in database</p>';
        } else {
            echo '<p class="success">✅ Found ' . count($priorities) . ' priorities</p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th><th>Icon</th><th>Sort Order</th></tr>';
            foreach ($priorities as $p) {
                echo '<tr>';
                echo '<td>' . $p['id'] . '</td>';
                echo '<td>' . $p['name'] . '</td>';
                echo '<td>' . $p['icon'] . '</td>';
                echo '<td>' . $p['sort_order'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
        
        <h2>Project Categories</h2>
        <?php
        $categories = Database::select("SELECT * FROM project_categories");
        if (empty($categories)) {
            echo '<p class="error">❌ No categories found in database</p>';
        } else {
            echo '<p class="success">✅ Found ' . count($categories) . ' categories</p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th></tr>';
            foreach ($categories as $cat) {
                echo '<tr>';
                echo '<td>' . $cat['id'] . '</td>';
                echo '<td>' . $cat['name'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
        
        <h2>Statuses</h2>
        <?php
        $statuses = Database::select("SELECT * FROM statuses");
        if (empty($statuses)) {
            echo '<p class="error">❌ No statuses found in database</p>';
        } else {
            echo '<p class="success">✅ Found ' . count($statuses) . ' statuses</p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th><th>Category</th><th>Sort Order</th></tr>';
            foreach ($statuses as $s) {
                echo '<tr>';
                echo '<td>' . $s['id'] . '</td>';
                echo '<td>' . $s['name'] . '</td>';
                echo '<td>' . $s['category'] . '</td>';
                echo '<td>' . $s['sort_order'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
        
        <h2>Users</h2>
        <?php
        $users = Database::select("SELECT id, email, display_name, is_active FROM users LIMIT 5");
        if (empty($users)) {
            echo '<p class="error">❌ No users found in database</p>';
        } else {
            echo '<p class="success">✅ Found users</p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Email</th><th>Display Name</th><th>Active</th></tr>';
            foreach ($users as $u) {
                echo '<tr>';
                echo '<td>' . $u['id'] . '</td>';
                echo '<td>' . $u['email'] . '</td>';
                echo '<td>' . $u['display_name'] . '</td>';
                echo '<td>' . ($u['is_active'] ? 'Yes' : 'No') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
        
        <h2>Projects</h2>
        <?php
        $projects = Database::select("SELECT id, `key`, name FROM projects LIMIT 5");
        if (empty($projects)) {
            echo '<p class="warning">⚠️ No projects found (but you can create one)</p>';
        } else {
            echo '<p class="success">✅ Found projects</p>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Key</th><th>Name</th></tr>';
            foreach ($projects as $proj) {
                echo '<tr>';
                echo '<td>' . $proj['id'] . '</td>';
                echo '<td>' . $proj['key'] . '</td>';
                echo '<td>' . $proj['name'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
        
        <hr>
        <p><a href="/jira_clone_system/public/">← Go back to Dashboard</a></p>
    </div>
</body>
</html>
