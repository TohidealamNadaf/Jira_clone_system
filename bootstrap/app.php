<?php
/**
 * Application Bootstrap
 */

declare(strict_types=1);

// Load autoloader
require_once __DIR__ . '/autoload.php';

// Load helper functions
require_once BASE_PATH . '/src/Helpers/functions.php';

// Load configuration
$config = require BASE_PATH . '/config/config.php';

// Load local config overrides if exists
$localConfigPath = BASE_PATH . '/config/config.local.php';
if (file_exists($localConfigPath)) {
    $localConfig = require $localConfigPath;
    $config = array_replace_recursive($config, $localConfig);
}

// Set configuration globally
App\Core\Config::setAll($config);

// Set error handling based on environment
if (config('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Set timezone
date_default_timezone_set(config('app.timezone', 'UTC'));

// Set default encoding
mb_internal_encoding('UTF-8');

// FIX 8: Initialize logging directory for notifications
$logDir = storage_path('logs');
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

// Initialize the application
$app = App\Core\Application::getInstance();

return $app;
