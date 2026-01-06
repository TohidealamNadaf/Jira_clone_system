<?php
require_once '../bootstrap/app.php';

// Create a test page that shows user info like navbar would
session_start();

// Set proper server variables like in web context
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['SCRIPT_NAME'] = '/jira_clone_system/public/index.php';
$_SERVER['REQUEST_SCHEME'] = 'http';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<h2>Please log in first</h2>';
    echo '<p><a href="login">Go to login</a></p>';
    exit;
}

// Load user from database
$db = new \App\Core\Database();
$user = $db->selectOne('SELECT id, display_name, avatar FROM users WHERE id = ?', [$_SESSION['user_id']]);

if (!$user) {
    echo '<h2>User not found</h2>';
    exit;
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Avatar Debug Page</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; background: #f9f9f9; }
        .avatar-test { margin: 10px 0; }
        img { border: 2px solid #007bff; margin: 5px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        .code { background: #f0f0f0; padding: 10px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>Avatar System Debug</h1>
    
    <div class="debug-section">
        <h2>Session Data</h2>
        <div class="code">
        Session user_id: ' . ($_SESSION['user_id'] ?? 'not set') . '<br>
        Session user_name: ' . ($_SESSION['user_name'] ?? 'not set') . '<br>
        User from DB: ' . ($user['display_name'] ?? 'not found') . '<br>
        Avatar from DB: ' . ($user['avatar'] ?? 'NULL') . '<br>
        </div>
    </div>
    
    <div class="debug-section">
        <h2>avatar() Function Test</h2>
        <div class="code">
        $avatarPath = ' . ($user['avatar'] ?? 'NULL') . ';<br>
        $avatarUrl = avatar($avatarPath);<br>
        Result: ' . avatar($user['avatar'] ?? null) . '<br>
        </div>
        
        <div class="avatar-test">
            <h3>Generated HTML (like in layouts/app.php):</h3>
            <div class="code">
            &lt;img src="' . htmlspecialchars(avatar($user['avatar'] ?? null)) . '" alt="' . htmlspecialchars($user['display_name']) . '"&gt;<br>
            </div>
            
            <h3>Rendered Result:</h3>
            <img src="' . htmlspecialchars(avatar($user['avatar'] ?? null)) . '" alt="' . htmlspecialchars($user['display_name']) . '" width="80" height="80" onerror="this.style.borderColor=\'red\'; this.alt=\'FAILED: \' + this.src;">
        </div>
    </div>
    
    <div class="debug-section">
        <h2>Manual URL Test</h2>
        <p>Test these URLs directly:</p>
        <div class="code">
        <a href="' . avatar($user['avatar'] ?? null) . '" target="_blank">Full Avatar URL</a><br>
        <a href="' . url('/uploads/avatars/avatar_1_1767008522.png') . '" target="_blank">Direct URL Test</a><br>
        <a href="http://localhost:8080/uploads/avatars/avatar_1_1767008522.png" target="_blank" style="color: red;">Error URL (should 404)</a>
        </div>
    </div>
    
    <div class="debug-section">
        <h2>Environment</h2>
        <div class="code">
        SCRIPT_NAME: ' . $_SERVER['SCRIPT_NAME'] . '<br>
        REQUEST_URI: ' . $_SERVER['REQUEST_URI'] . '<br>
        HTTP_HOST: ' . $_SERVER['HTTP_HOST'] . '<br>
        url() base: ' . dirname($_SERVER['SCRIPT_NAME']) . '<br>
        </div>
    </div>
</body>
</html>';
?>