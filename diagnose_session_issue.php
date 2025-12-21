<?php
/**
 * Diagnose Session Issue with Create Issue Modal
 * Run this script when logged in to see what's in the session
 */

session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Diagnostic</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f0f0f0; }
        .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #0052cc; }
        .ok { border-left-color: #28a745; }
        .warning { border-left-color: #ffc107; }
        .error { border-left-color: #dc3545; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; border-radius: 3px; }
        .code { background: #f0f0f0; padding: 2px 5px; border-radius: 2px; }
        h2 { margin-top: 0; color: #333; }
    </style>
</head>
<body>

<h1>🔍 Session Diagnostic Report</h1>

<div class="section ok">
    <h2>1️⃣ Session ID</h2>
    <pre>Session ID: <?php echo session_id(); ?></pre>
    <p>Session Name: <?php echo session_name(); ?></p>
    <p>Session Status: <?php echo session_status() === PHP_SESSION_ACTIVE ? '✅ ACTIVE' : '❌ INACTIVE'; ?></p>
</div>

<div class="section">
    <h2>2️⃣ Session Cookie Info</h2>
    <pre><?php
    $cookieParams = session_get_cookie_params();
    echo "Name: " . session_name() . "\n";
    echo "Path: " . $cookieParams['path'] . "\n";
    echo "Domain: " . ($cookieParams['domain'] ?: '(empty)') . "\n";
    echo "Secure: " . ($cookieParams['secure'] ? 'Yes' : 'No') . "\n";
    echo "HttpOnly: " . ($cookieParams['httponly'] ? 'Yes' : 'No') . "\n";
    echo "SameSite: " . ($cookieParams['samesite'] ?: 'Not set') . "\n";
    echo "Lifetime: " . $cookieParams['lifetime'] . " seconds\n";
    ?></pre>
</div>

<div class="section">
    <h2>3️⃣ $_SESSION Contents</h2>
    <?php if (empty($_SESSION)): ?>
        <p style="color: #dc3545;"><strong>❌ SESSION IS EMPTY!</strong></p>
        <p>This is why you're getting "User not authenticated"</p>
        <p>Even though you're logged in on the page, the session doesn't have user data.</p>
    <?php else: ?>
        <pre><?php var_dump($_SESSION); ?></pre>
    <?php endif; ?>
</div>

<div class="section">
    <h2>4️⃣ User Data Check</h2>
    <?php
    $user = $_SESSION['user'] ?? null;
    if ($user):
    ?>
        <p style="color: #28a745;"><strong>✅ User Found in Session</strong></p>
        <pre><?php var_dump($user); ?></pre>
        <p>User ID: <code><?php echo $user['id'] ?? 'Not set'; ?></code></p>
    <?php else: ?>
        <p style="color: #dc3545;"><strong>❌ NO USER DATA IN SESSION</strong></p>
        <p>This is the problem! The session exists but doesn't have user data.</p>
    <?php endif; ?>
</div>

<div class="section">
    <h2>5️⃣ Browser Cookies (HTTP Headers)</h2>
    <pre><?php
    echo "Cookies being sent to server:\n";
    echo implode("; ", array_map(function($k, $v) {
        return "$k=$v";
    }, array_keys($_COOKIE), array_values($_COOKIE)));
    ?>
    
<?php if (empty($_COOKIE)): ?>
❌ NO COOKIES SENT!
<?php else: ?>
✅ Cookies present: <?php echo count($_COOKIE); ?>
<?php endif; ?></pre>
</div>

<div class="section">
    <h2>6️⃣ Server Variables</h2>
    <pre><?php
    echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
    echo "CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set') . "\n";
    echo "HTTP_ACCEPT: " . ($_SERVER['HTTP_ACCEPT'] ?? 'Not set') . "\n";
    echo "HTTP_CONTENT_TYPE: " . ($_SERVER['HTTP_CONTENT_TYPE'] ?? 'Not set') . "\n";
    echo "HTTP_X_REQUESTED_WITH: " . ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? 'Not set') . "\n";
    echo "HTTP_X_CSRF_TOKEN: " . ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? 'Not set') . "\n";
    ?></pre>
</div>

<div class="section warning">
    <h2>❓ What This Means</h2>
    <?php
    $userInSession = isset($_SESSION['user']);
    $cookieExists = isset($_COOKIE[session_name()]);
    $sessionActive = session_status() === PHP_SESSION_ACTIVE;
    ?>
    
    <h3>Diagnosis:</h3>
    <ul>
        <li><strong>Session Active:</strong> <?php echo $sessionActive ? '✅ YES' : '❌ NO'; ?></li>
        <li><strong>User in Session:</strong> <?php echo $userInSession ? '✅ YES' : '❌ NO'; ?></li>
        <li><strong>Cookie Sent:</strong> <?php echo $cookieExists ? '✅ YES' : '❌ NO'; ?></li>
    </ul>

    <?php if (!$userInSession): ?>
        <h3>Problem Found:</h3>
        <p style="color: #dc3545;"><strong>The session doesn't have the user!</strong></p>
        
        <p><strong>Possible Causes:</strong></p>
        <ol>
            <li>User wasn't properly saved to session during login</li>
            <li>Session timeout - session expired</li>
            <li>Different session between page load and API call</li>
            <li>Session cookie not being sent with fetch() request</li>
        </ol>

        <p><strong>Solutions to Try:</strong></p>
        <ol>
            <li><strong>Log Out & Back In:</strong> 
                <ul>
                    <li>Click your user menu (top right)</li>
                    <li>Click "Logout"</li>
                    <li>Go to /login and log in again</li>
                    <li>Then try creating an issue</li>
                </ul>
            </li>
            <li><strong>Clear All Cookies:</strong>
                <ul>
                    <li>Press Ctrl+Shift+Delete</li>
                    <li>Select "All time"</li>
                    <li>Check "Cookies and other site data"</li>
                    <li>Click "Clear data"</li>
                    <li>Reload page and log in again</li>
                </ul>
            </li>
            <li><strong>Check Network Tab:</strong>
                <ul>
                    <li>Open F12 → Network tab</li>
                    <li>Try creating issue</li>
                    <li>Find POST to /issues/store</li>
                    <li>Click "Cookies" tab</li>
                    <li>Check if <?php echo session_name(); ?> cookie is being sent</li>
                </ul>
            </li>
        </ol>
    <?php endif; ?>
</div>

<div class="section">
    <h2>7️⃣ AJAX Session Issue Explanation</h2>
    <p>When you submit the form using fetch() (AJAX), the browser:</p>
    <ol>
        <li>Opens new HTTP connection (different from page load)</li>
        <li>By default, <strong>does NOT send cookies</strong> (unlike regular form submit)</li>
        <li>Server receives request without session cookie</li>
        <li>Session is empty (new session created)</li>
        <li>User data not found → 401 error</li>
    </ol>

    <p><strong>✅ FIX Applied:</strong> Added <code>credentials: 'include'</code> to fetch()</p>
    <p>This tells the browser: "Send cookies with this request"</p>

    <p><strong>Verify:</strong></p>
    <ol>
        <li>Open F12 → Network tab</li>
        <li>Click "Create" button and submit form</li>
        <li>Find POST to /issues/store</li>
        <li>Click on it and go to "Cookies" tab</li>
        <li>Should see <?php echo session_name(); ?> cookie being sent</li>
        <li>If missing → cookies not being sent → 401 error</li>
    </ol>
</div>

<div class="section">
    <h2>8️⃣ Quick Test</h2>
    <p>What to check after logging in and clearing cache:</p>
    <ul>
        <li>☐ Reload this diagnostic page</li>
        <li>☐ Check "User in Session": Should show ✅ YES</li>
        <li>☐ Check "User Data Check": Should show user info</li>
        <li>☐ Then try creating an issue</li>
    </ul>
</div>

<hr>
<p style="color: #666; font-size: 12px;">
    <strong>Note:</strong> This diagnostic page shows the session state when loaded.
    If you're logged in, $_SESSION['user'] should be populated.
    If empty, the login process didn't save user data to the session.
</p>

</body>
</html>
