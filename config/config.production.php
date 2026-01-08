<?php
/**
 * Production Configuration Template
 * 
 * Copy to config.php or use environment variables
 * DO NOT commit this to version control with real credentials
 */

declare(strict_types=1);

// Load environment variables from .env file if present
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
} else {
    $env = [];
}

// Helper function for env vars
function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

return [
    // Application
    'app' => [
        'name' => 'CWays MIS',
        'env' => env('APP_ENV', 'production'),
        'debug' => env('APP_DEBUG', false),
        'url' => env('APP_URL', 'https://jira.yourdomain.com'),
        'timezone' => 'UTC',
        'locale' => 'en',
        'key' => env('APP_KEY', ''), // REQUIRED: Long random 64-char string
    ],

    // Database
    'database' => [
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', 3306),
        'name' => env('DB_NAME', 'cways_prod'),
        'username' => env('DB_USER', 'jira_user'),
        'password' => env('DB_PASS', ''), // REQUIRED: Strong password
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    // Session
    'session' => [
        'name' => 'jira_session',
        'lifetime' => 7200, // 2 hours
        'path' => '/',
        'domain' => env('SESSION_DOMAIN', ''),
        'secure' => env('SESSION_SECURE', true), // HTTPS only in production
        'httponly' => true,
        'samesite' => 'Lax',
    ],

    // Mail - Email Delivery (Phase 2)
    'mail' => [
        'driver' => env('MAIL_DRIVER', 'smtp'),
        'host' => env('MAIL_HOST', 'smtp.sendgrid.net'),
        'port' => env('MAIL_PORT', 587),
        'username' => env('MAIL_USERNAME', 'apikey'),
        'password' => env('MAIL_PASSWORD', ''), // SendGrid API key or SMTP password
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'from_address' => env('MAIL_FROM_ADDRESS', 'notifications@yourdomain.com'),
        'from_name' => env('MAIL_FROM_NAME', 'CWays MIS'),
    ],

    // JWT Authentication
    'jwt' => [
        'secret' => env('JWT_SECRET', ''), // REQUIRED: Long random 64-char string
        'algorithm' => 'HS256',
        'ttl' => 3600, // 1 hour
        'refresh_ttl' => 604800, // 7 days
    ],

    // File Upload
    'upload' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'path' => env('UPLOAD_PATH', 'uploads'),
        'allowed_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
            'application/zip',
            'application/x-rar-compressed',
        ],
    ],

    // Cache
    'cache' => [
        'driver' => env('CACHE_DRIVER', 'file'),
        'path' => __DIR__ . '/../storage/cache',
        'ttl' => 3600,
    ],

    // Logging
    'logging' => [
        'path' => env('LOG_PATH', __DIR__ . '/../storage/logs'),
        'level' => env('LOG_LEVEL', 'warning'), // debug, info, warning, error
    ],

    // Pagination
    'pagination' => [
        'per_page' => 25,
        'max_per_page' => 100,
    ],

    // Security
    'security' => [
        'password_min_length' => 8,
        'password_require_uppercase' => true,
        'password_require_number' => true,
        'password_require_special' => false,
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
        'csrf_token_lifetime' => 7200,
    ],
];
