<?php
/**
 * Check Admin Login Status - Uses framework properly
 */

require '../bootstrap/autoload.php';

use App\Core\Session;
use App\Core\Database;

// Initialize the app
$app = require '../bootstrap/app.php';

// Start session through framework
$session = new Session();
$session->start();

// Get user from session
$user = Session::user();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Admin Login Check</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 40px auto; padding: 20px; }
        .box { background: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #0052CC; border-radius: 4px; }
        .ok { background: #e8f5e9; border-left-color: green; color: green; }
        .err { background: #ffebee; border-left-color: red; color: red; }
        .info { background: #e3f2fd; border-left-color: #0052CC; color: #0052CC; }
        code { background: white; padding: 2px 6px; font-family: monospace; }
        strong { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Admin Login Check</h1>";

if (!$user) {
    echo '<div class="box err"><strong>❌ Not Logged In</strong><br>Session is empty. You must log in first.</div>';
    echo '<p><a href="/jira_clone_system/public/login">Go to Login</a></p>';
    echo '</body></html>';
    exit;
}

echo '<div class="box info"><strong>✓ User Session Found</strong></div>';

echo '<div class="box">
    <strong>Session Data:</strong><br>
    ID: ' . htmlspecialchars($user['id'] ?? 'N/A') . '<br>
    Email: ' . htmlspecialchars($user['email'] ?? 'N/A') . '<br>
    Name: ' . htmlspecialchars($user['display_name'] ?? 'N/A') . '<br>
    is_admin: ' . ($user['is_admin'] ? '<strong style="color: green;">1 (YES)</strong>' : '<strong style="color: red;">0 (NO)</strong>') . '<br>
    role_slug: ' . htmlspecialchars($user['role_slug'] ?? 'N/A') . '<br>
</div>';

$isAdmin = ($user['role_slug'] ?? '') === 'admin' || ($user['is_admin'] ?? false);

if ($isAdmin) {
    echo '<div class="box ok"><strong>✓ Admin Status Confirmed</strong><br>
    You should be able to access /admin/ now.</div>';
    echo '<p><a href="/jira_clone_system/public/admin/">Try Admin Panel</a></p>';
} else {
    echo '<div class="box err"><strong>❌ NOT an Admin</strong><br>
    Your account is not configured as admin.</div>';
    echo '<p><strong>Missing:</strong><br>';
    if (!($user['is_admin'] ?? false)) echo '- is_admin flag not set<br>';
    if (($user['role_slug'] ?? '') !== 'admin') echo '- role_slug is not "admin"<br>';
    echo '</p>';
    
    echo '<p><strong>Fix:</strong> Run SQL in phpMyAdmin on database "jiira_clonee_system":</p>';
    echo '<code style="display: block; background: white; padding: 10px; border: 1px solid #ccc; margin: 10px 0; overflow-x: auto;">
UPDATE users SET is_admin=1, display_name="Admin User", first_name="Admin", last_name="User" WHERE email="admin@example.com";
    </code>';
}

echo '</body></html>';
?>
