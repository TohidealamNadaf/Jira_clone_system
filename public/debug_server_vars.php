<?php
echo '<h1>Server Variables Debug</h1>';
echo '<div style="background: #f0f0f0; padding: 10px; font-family: monospace;">';
echo 'SCRIPT_NAME: "' . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . '"' . PHP_EOL;
echo 'dirname(SCRIPT_NAME): "' . dirname($_SERVER['SCRIPT_NAME'] ?? 'not set') . '"' . PHP_EOL;
echo 'HTTP_HOST: "' . ($_SERVER['HTTP_HOST'] ?? 'not set') . '"' . PHP_EOL;
echo 'REQUEST_SCHEME: "' . ($_SERVER['REQUEST_SCHEME'] ?? 'not set') . '"' . PHP_EOL;
echo 'HTTPS: "' . ($_SERVER['HTTPS'] ?? 'not set') . '"' . PHP_EOL;
echo 'SERVER_PORT: "' . ($_SERVER['SERVER_PORT'] ?? 'not set') . '"' . PHP_EOL;
echo '</div>';

// Build URL step by step like url() function
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$basePath = dirname($scriptName);
$basePath = str_replace('\\', '/', $basePath);
$basePath = rtrim($basePath, '/');

echo '<h2>URL Generation Steps</h2>';
echo '<div style="background: #f0f0f0; padding: 10px; font-family: monospace;">';
echo 'scheme: ' . $scheme . PHP_EOL;
echo 'host: ' . $host . PHP_EOL;
echo 'scriptName: ' . $scriptName . PHP_EOL;
echo 'basePath: ' . $basePath . PHP_EOL;
echo 'Final baseUrl: ' . $scheme . '://' . $host . $basePath . PHP_EOL;
echo '</div>';
?>