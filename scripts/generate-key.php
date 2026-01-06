<?php
/**
 * Generate Secure Application Keys
 * 
 * This script generates cryptographically secure keys for:
 * - app.key (application encryption key)
 * - jwt.secret (JWT token signing secret)
 * 
 * Usage: php scripts/generate-key.php
 */

declare(strict_types=1);

// Generate 32-character hex key (128 bits of entropy)
$key = bin2hex(random_bytes(16));
$jwtSecret = bin2hex(random_bytes(16));

echo "═══════════════════════════════════════════════════════════════\n";
echo "  Secure Key Generation\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📋 Copy these values to config/config.php:\n\n";

echo "1. APP KEY (for config['app']['key']):\n";
echo "   \033[32m{$key}\033[0m\n\n";

echo "2. JWT SECRET (for config['jwt']['secret']):\n";
echo "   \033[32m{$jwtSecret}\033[0m\n\n";

echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📝 Update config/config.php:\n";
echo "   'app' => [\n";
echo "       'key' => '{$key}',\n";
echo "   ],\n";
echo "   'jwt' => [\n";
echo "       'secret' => '{$jwtSecret}',\n";
echo "   ],\n\n";

echo "✅ Keys generated successfully!\n";
echo "⚠️  Keep these keys secure. Share only in secure channels.\n";
echo "⚠️  Never commit keys to version control.\n";
echo "⚠️  Use environment variables in production:\n";
echo "   - APP_KEY (environment variable)\n";
echo "   - JWT_SECRET (environment variable)\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
?>
