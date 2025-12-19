<?php
/**
 * Debug Session State
 */

echo "<h2>Session Debug Information</h2>\n";
echo "<pre>\n";

// Check session status
echo "1. Session Status:\n";
echo "   - Status: " . session_status() . "\n";
echo "   - PHP_SESSION_DISABLED: " . PHP_SESSION_DISABLED . "\n";
echo "   - PHP_SESSION_NONE: " . PHP_SESSION_NONE . "\n";
echo "   - PHP_SESSION_ACTIVE: " . PHP_SESSION_ACTIVE . "\n\n";

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    echo "2. Starting session...\n";
    session_start();
    echo "   - Session started\n\n";
} else {
    echo "2. Session already active\n\n";
}

// Check session contents
echo "3. Session Contents (\$_SESSION):\n";
if (empty($_SESSION)) {
    echo "   - EMPTY (no session data)\n";
} else {
    foreach ($_SESSION as $key => $value) {
        if (is_array($value)) {
            echo "   - $key: Array with " . count($value) . " items\n";
            foreach ($value as $k => $v) {
                if (!is_array($v) && !is_object($v)) {
                    echo "     - $k: " . substr((string)$v, 0, 50) . "\n";
                }
            }
        } else {
            echo "   - $key: " . substr((string)$value, 0, 50) . "\n";
        }
    }
}
echo "\n";

// Check cookies
echo "4. Session Cookies (\$_COOKIE):\n";
if (empty($_COOKIE)) {
    echo "   - EMPTY (no cookies)\n";
} else {
    foreach ($_COOKIE as $key => $value) {
        echo "   - $key: " . substr((string)$value, 0, 50) . "\n";
    }
}
echo "\n";

// Check session ID
echo "5. Session ID:\n";
echo "   - session_id(): " . session_id() . "\n";
echo "   - Length: " . strlen(session_id()) . "\n\n";

// Check _user directly from session
echo "6. Checking _user in session:\n";
if (isset($_SESSION['_user'])) {
    echo "   ✓ _user key EXISTS in \$_SESSION\n";
    $user = $_SESSION['_user'];
    echo "   - ID: {$user['id']}\n";
    echo "   - Email: {$user['email']}\n";
    echo "   - Name: {$user['first_name']} {$user['last_name']}\n";
} else {
    echo "   ✗ _user key DOES NOT exist in \$_SESSION\n";
}

echo "\n";
echo "═════════════════════════════════════════════════\n";
echo "DIAGNOSIS:\n";
if (session_status() !== PHP_SESSION_ACTIVE) {
    echo "❌ Session is not active - cookies not being sent\n";
} else if (empty($_SESSION)) {
    echo "❌ Session is active but empty - you may not be logged in\n";
    echo "   Try logging out and logging back in\n";
} else if (!isset($_SESSION['_user'])) {
    echo "❌ Session has data but _user key missing\n";
    echo "   Check how your login system stores user data\n";
} else {
    echo "✅ Session looks good! User should be logged in\n";
}
echo "═════════════════════════════════════════════════\n";

echo "</pre>\n";
