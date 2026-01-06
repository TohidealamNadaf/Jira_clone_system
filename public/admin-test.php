<?php
/**
 * Direct Admin Test - Bypasses routing
 */
session_start();

// Check if session has user
$user = $_SESSION['user'] ?? null;

echo "<!DOCTYPE html>
<html>
<head>
    <title>Admin Test</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 40px auto; }
        .box { background: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #0052CC; }
        .ok { background: #e8f5e9; border-left-color: green; }
        .err { background: #ffebee; border-left-color: red; }
        code { background: white; padding: 2px 5px; }
    </style>
</head>
<body>
    <h1>Admin Access Test</h1>";

if (!$user) {
    echo '<div class="box err"><strong>Not Logged In</strong><br>Session empty. <a href="/jira_clone_system/public/login">Go to login</a></div>';
    echo '</body></html>';
    exit;
}

echo '<div class="box">';
echo '<strong>Session User Data:</strong><br>';
echo 'Email: ' . htmlspecialchars($user['email'] ?? 'Unknown') . '<br>';
echo 'ID: ' . htmlspecialchars($user['id'] ?? 'Unknown') . '<br>';
echo 'Display Name: ' . htmlspecialchars($user['display_name'] ?? 'Unknown') . '<br>';
echo 'is_admin: ' . ($user['is_admin'] ? '<strong>1 (YES)</strong>' : '0 (NO)') . '<br>';
echo 'role_slug: ' . htmlspecialchars($user['role_slug'] ?? 'Unknown') . '<br>';
echo '</div>';

$isAdmin = ($user['role_slug'] ?? '') === 'admin' || ($user['is_admin'] ?? false);

if ($isAdmin) {
    echo '<div class="box ok"><strong>✓ Should Pass AdminMiddleware</strong><br>';
    echo 'You should be able to access /admin/</div>';
} else {
    echo '<div class="box err"><strong>✗ Will FAIL AdminMiddleware</strong><br>';
    echo 'Missing: ' . (!($user['is_admin'] ?? false) ? 'is_admin=1' : '') . 
         (($user['role_slug'] ?? '') !== 'admin' && !($user['is_admin'] ?? false) ? ' and ' : '') .
         (($user['role_slug'] ?? '') !== 'admin' ? 'role_slug=admin' : '') . '</div>';
}

echo '<h2>Next Steps</h2>';
if (!$isAdmin) {
    echo '<ol>
    <li>Open phpMyAdmin: <code>http://localhost/phpmyadmin</code></li>
    <li>Run this SQL on the jira_clone_system database:
    <pre>UPDATE users SET is_admin=1, display_name="Admin User", first_name="Admin", last_name="User" WHERE email="admin@example.com";</pre>
    </li>
    <li>Log out and back in</li>
    <li>Try /admin/ again</li>
    </ol>';
} else {
    echo '<p>Try: <a href="/jira_clone_system/public/admin/">http://localhost:8080/jira_clone_system/public/admin/</a></p>';
}

echo '</body></html>';
?>
