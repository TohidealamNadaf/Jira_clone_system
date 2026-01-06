<?php
// public/debug_auth.php

require_once __DIR__ . '/../bootstrap/app.php';

// Start Session
$app->resolve(\App\Core\Session::class)->start();

echo "<h1>Authentication Debugger</h1>";

// 1. Session & CSRF
echo "<h2>1. Session/CSRF</h2>";
echo "Session ID: " . session_id() . "<br>";
$sessionToken = \App\Core\Session::get('_csrf_token');
echo "Session CSRF Token: " . ($sessionToken ? substr($sessionToken, 0, 10) . '...' : 'NOT SET') . "<br>";

// 2. User Lookup
echo "<h2>2. User Lookup</h2>";
$email = 'admin@example.com';
$user = \App\Core\Database::selectOne("SELECT * FROM users WHERE email = ?", [$email]);

if ($user) {
    echo "User Found: ID " . $user['id'] . "<br>";
    echo "Is Active: " . $user['is_active'] . "<br>";

    // 3. Password Verify
    echo "<h2>3. Password Verification</h2>";
    $testPassword = 'Admin@123';
    $hash = $user['password_hash'];
    $verify = password_verify($testPassword, $hash);

    echo "Testing password '$testPassword': " . ($verify ? "<strong style='color:green'>MATCH</strong>" : "<strong style='color:red'>MISMATCH</strong>") . "<br>";

    if (!$verify) {
        echo "Hash in DB: " . substr($hash, 0, 20) . "...<br>";
        echo "Note: If mismatch, password seeding might be wrong.<br>";
    }
} else {
    echo "<strong style='color:red'>User '$email' NOT FOUND in database.</strong><br>";
}
