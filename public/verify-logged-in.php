<?php
session_start();

echo "<h1>Login Status Check</h1>";

if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✓ You are LOGGED IN</p>";
    echo "<p>User ID: " . htmlspecialchars($_SESSION['user_id']) . "</p>";
    echo "<hr>";
    echo "<p><a href='/jira_clone_system/public/time-tracking' style='font-size: 18px; color: blue;'>👉 Try accessing Time Tracking Dashboard now</a></p>";
} else {
    echo "<p style='color: red; font-size: 18px; font-weight: bold;'>✗ You are NOT LOGGED IN</p>";
    echo "<p>You need to login first before accessing time tracking.</p>";
    echo "<hr>";
    echo "<p><a href='/jira_clone_system/public/login' style='font-size: 18px; color: blue;'>👉 Go to Login Page</a></p>";
}

echo "<hr>";
echo "<p><a href='/jira_clone_system/public/dashboard'>Back to Dashboard</a></p>";
?>
