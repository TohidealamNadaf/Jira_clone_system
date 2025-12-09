<?php
/**
 * Reset Admin Password Script
 * 
 * Usage: php scripts/reset-admin-password.php [new-password]
 * 
 * If no password provided, defaults to: Admin@123
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

$newPassword = $argv[1] ?? 'Admin@123';

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║           ADMIN PASSWORD RESET                                  ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Find admin user
$admin = Database::selectOne(
    'SELECT id, email, first_name, last_name FROM users WHERE email = ?',
    ['admin@example.com']
);

if (!$admin) {
    echo "❌ Admin user (admin@example.com) not found!\n";
    echo "   Creating admin user...\n\n";
    
    $hash = password_hash($newPassword, PASSWORD_ARGON2ID);
    
    Database::insert('users', [
        'email' => 'admin@example.com',
        'password_hash' => $hash,
        'first_name' => 'System',
        'last_name' => 'Administrator',
        'is_admin' => 1,
        'is_active' => 1,
    ]);
    
    echo "✅ Admin user created!\n";
} else {
    // Update password
    $hash = password_hash($newPassword, PASSWORD_ARGON2ID);
    
    Database::update('users', 
        ['password_hash' => $hash], 
        'id = ?', 
        [$admin['id']]
    );
    
    echo "✅ Password updated for: {$admin['email']}\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════════\n";
echo "  Admin Credentials:\n";
echo "  Email:    admin@example.com\n";
echo "  Password: {$newPassword}\n";
echo "═══════════════════════════════════════════════════════════════════\n";
echo "\n⚠️  Change this password after logging in!\n\n";
