<?php
// public/debug_session.php

require_once __DIR__ . '/../bootstrap/app.php';

// Manually start session for diagnostics
$session = $app->resolve(\App\Core\Session::class);
$session->start();

// Simulate App::run() CSRF generation
if (!\App\Core\Session::has('_csrf_token')) {
    \App\Core\Session::set('_csrf_token', bin2hex(random_bytes(32)));
    $generatedNow = true;
}

// Test Persistence
$count = \App\Core\Session::get('debug_count', 0) + 1;
\App\Core\Session::set('debug_count', $count);

echo "<h1>Session & CSRF Debug (Final)</h1>";

// 1. Session Persistence
echo "<h2>1. Persistence Test</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Page views in this session: <strong>$count</strong> (If this stays 1, sessions are broken)<br>";

// 2. Cookie config
$params = session_get_cookie_params();
echo "<h2>2. Cookie Configuration</h2>";
echo "Cookie Path: <strong>" . $params['path'] . "</strong> (Should be '/')<br>";
echo "Config session.path: " . config('session.path') . "<br>";

// 3. CSRF
echo "<h2>3. CSRF Token</h2>";
echo "Token: " . \App\Core\Session::get('_csrf_token') . "<br>";
if (isset($generatedNow)) {
    echo "<em>(Generated just now - first visit or session reset)</em><br>";
} else {
    echo "<em>(Persisted from previous request)</em><br>";
}

// 4. Check CSRF
echo "<h2>4. CSRF Token</h2>";
echo "Function csrf_token(): " . csrf_token() . "<br>";
echo "Session Key '_csrf_token': " . ($_SESSION['_csrf_token'] ?? 'NOT SET') . "<br>";

// 5. Check URL Generation
echo "<h2>5. URL Generation</h2>";
echo "url('/login'): " . url('/login') . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";

// 6. Test Form Action Matching
echo "<h2>6. POST Route Simulator</h2>";
$router = new \App\Core\Router(); // Just to see if we can instantiate
echo "Router instantiated.<br>";

echo "<hr>";
echo "<p>Refresh this page. If Session ID changes every time, cookies are <strong>NOT</strong> working/saving.</p>";
