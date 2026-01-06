<?php
/**
 * Test Admin Access
 * This page helps diagnose and fix admin access issues
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Access Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; }
        .success { color: green; background: #e8f5e9; padding: 10px; border-left: 4px solid green; margin: 10px 0; }
        .error { color: red; background: #ffebee; padding: 10px; border-left: 4px solid red; margin: 10px 0; }
        .info { color: blue; background: #e3f2fd; padding: 10px; border-left: 4px solid blue; margin: 10px 0; }
        code { background: #f5f5f5; padding: 2px 6px; }
    </style>
</head>
<body>
    <h1>Admin Access Diagnostic Tool</h1>
    
    <?php
    session_start();
    $user = $_SESSION['user'] ?? null;
    
    if (!$user) {
        echo '<div class="error"><strong>Not Logged In</strong><br>Please log in first. <a href="' . str_replace('/test-admin-access.php', '', $_SERVER['REQUEST_URI']) . '/login">Go to Login</a></div>';
        exit;
    }
    
    echo '<div class="info"><strong>Current User:</strong><br>';
    echo 'Email: ' . htmlspecialchars($user['email'] ?? 'Unknown') . '<br>';
    echo 'ID: ' . htmlspecialchars($user['id'] ?? 'Unknown') . '<br>';
    echo 'is_admin: ' . ($user['is_admin'] ? '<strong>YES ✓</strong>' : 'NO ✗') . '<br>';
    echo 'role_slug: ' . htmlspecialchars($user['role_slug'] ?? 'Unknown') . '<br>';
    echo '</div>';
    
    // Check if user is admin
    $isAdmin = ($user['is_admin'] ?? false) || ($user['role_slug'] ?? '') === 'admin';
    
    if (!$isAdmin) {
        echo '<div class="error"><strong>Not Admin User</strong><br>';
        echo 'Your account is not marked as administrator. The PHP middleware will still block you.';
        echo '</div>';
    } else {
        echo '<div class="success"><strong>✓ Admin Status Confirmed</strong><br>';
        echo 'If you are still seeing 403 error, it is an Apache .htaccess issue, not PHP authorization.';
        echo '</div>';
    }
    
    // Try to access admin area
    echo '<h2>Tests</h2>';
    
    // Check .htaccess existence
    echo '<h3>1. .htaccess Configuration</h3>';
    if (file_exists('.htaccess')) {
        echo '<div class="success">✓ .htaccess file exists in /public/</div>';
    } else {
        echo '<div class="error">✗ .htaccess file missing in /public/</div>';
    }
    
    // Check admin directory
    echo '<h3>2. Admin Directory</h3>';
    if (is_dir('admin')) {
        echo '<div class="success">✓ /public/admin/ directory exists</div>';
    } else {
        echo '<div class="error">✗ /public/admin/ directory does not exist</div>';
    }
    
    // Suggest next steps
    echo '<h2>Next Steps</h2>';
    echo '<ol>';
    echo '<li>Close this tab completely</li>';
    echo '<li>Clear browser cache: <code>CTRL+SHIFT+DEL</code></li>';
    echo '<li>Try accessing: <code>http://localhost:8080/jira_clone_system/public/admin/</code></li>';
    echo '<li>If still 403, restart Apache from XAMPP Control Panel</li>';
    echo '</ol>';
    
    echo '<h2>Troubleshooting</h2>';
    if (!$isAdmin) {
        echo '<p><strong>Your user is not admin.</strong> Contact your system administrator or check the database.</p>';
    } else {
        echo '<p><strong>You are admin.</strong> If you see 403, it is likely:</p>';
        echo '<ul>';
        echo '<li>Apache .htaccess not being processed (enable mod_rewrite in Apache config)</li>';
        echo '<li>Browser cache showing old version</li>';
        echo '<li>Apache needs restart</li>';
        echo '</ul>';
    }
    ?>
    
</body>
</html>
