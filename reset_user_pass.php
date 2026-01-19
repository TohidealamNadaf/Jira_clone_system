<?php
require_once __DIR__ . '/vendor/autoload.php';
$user = \App\Core\Database::selectOne("SELECT id, email FROM users LIMIT 1");
if ($user) {
    // Update password to 'password' (hashed)
    $hashed = password_hash('password', PASSWORD_DEFAULT);
    \App\Core\Database::statement("UPDATE users SET password = ? WHERE id = ?", [$hashed, $user['id']]);
    echo "User: " . $user['email'] . "\nPassword set to: password";
} else {
    echo "No user found";
}
