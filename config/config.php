<?php
/**
 * Main Configuration File
 * Copy this to config.local.php and modify for your environment
 */

declare(strict_types=1);

return [
    // Application
    'app' => [
        'name' => 'Jira Clone',
        'env' => 'development', // development, production
        'debug' => true,
        'url' => 'http://localhost:8080/jira_clone_system/public',
        'timezone' => 'UTC',
        'locale' => 'en',
        'key' => 'd62ba6fe4db129cdfbb444e1961575c7', // Change this!
    ],

    // Database
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'jiira_clonee_system',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    

    // Session
    'session' => [
        'name' => 'jira_session',
        'lifetime' => 7200, // 2 hours
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set true in production with HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ],

    // Mail
    'mail' => [
        'driver' => 'smtp', // mail, smtp
        'host' => 'localhost',
        'port' => 25,
        'username' => '',
        'password' => '',
        'encryption' => '', // tls, ssl, or empty
        'from_address' => 'noreply@example.com',
        'from_name' => 'Jira Clone',
    ],

    // JWT
    'jwt' => [
        'secret' => 'd62ba6fe4db129cdfbb444e1961575c7',
        'algorithm' => 'HS256',
        'ttl' => 3600, // 1 hour
        'refresh_ttl' => 604800, // 7 days
    ],

    // Upload
    'upload' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'path' => 'uploads',
        'allowed_types' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain', 'text/csv',
            'application/zip', 'application/x-rar-compressed',
        ],
    ],

    // Cache
    'cache' => [
        'driver' => 'file', // file, none
        'path' => __DIR__ . '/../storage/cache',
        'ttl' => 3600,
    ],

    // Logging
    'logging' => [
        'path' => __DIR__ . '/../storage/logs',
        'level' => 'debug', // debug, info, warning, error
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
