<?php
/**
 * Debug authentication and user ID
 */
require 'bootstrap/autoload.php';

use App\Core\Session;

echo "=== Debugging Authentication ===\n\n";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check session data
echo "Session Status:\n";
echo "- Session ID: " . session_id() . "\n";
echo "- Session Status: " . session_status() . "\n";
echo "- Session Data: " . json_encode($_SESSION, JSON_PRETTY_PRINT) . "\n\n";

// Check user via Session
echo "User from Session:\n";
$user = Session::get('_user');
if ($user) {
    echo "- User found: " . json_encode($user, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "- ✗ No user in session\n";
}

echo "\n";

// Check user via helper function
echo "User via helper function:\n";
$authUser = auth();
if ($authUser) {
    echo "- auth() returned: " . json_encode($authUser, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "- ✗ auth() returned null\n";
}

echo "\n";

// Check user ID
echo "User ID:\n";
$userId = user_id();
if ($userId) {
    echo "- user_id() returned: $userId\n";
} else {
    echo "- ✗ user_id() returned null\n";
}

echo "\n";

// If no user, try to manually check
echo "Database check for users:\n";
$users = \App\Core\Database::select("SELECT id, email, display_name FROM users LIMIT 3", []);
echo "- Found " . count($users) . " users:\n";
foreach ($users as $u) {
    echo "  • ID: {$u['id']}, Email: {$u['email']}, Name: {$u['display_name']}\n";
}
