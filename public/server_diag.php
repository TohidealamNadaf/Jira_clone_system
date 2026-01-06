<?php
// public/server_diag.php
// Access this via browser: http://localhost:8081/server_diag.php

echo "<h1>Server Diagnostics</h1>";

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";

$keys = ['REQUEST_URI', 'SCRIPT_NAME', 'PHP_SELF', 'DOCUMENT_ROOT', 'HTTP_HOST'];
foreach ($keys as $key) {
    echo "<tr><td>{$key}</td><td>" . ($_SERVER[$key] ?? '<em>not set</em>') . "</td></tr>";
}
echo "</table>";

echo "<h2>Computed Base Path Test</h2>";
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$basePath = dirname($scriptName);
$basePath = str_replace('\\', '/', $basePath);
$basePath = rtrim($basePath, '/');
echo "<p>Computed basePath from SCRIPT_NAME: <strong>{$basePath}</strong></p>";

echo "<h2>Comparison</h2>";
echo "<p>Does REQUEST_URI start with basePath? " . (str_starts_with($_SERVER['REQUEST_URI'], $basePath) ? 'YES' : 'NO') . "</p>";

if (!str_starts_with($_SERVER['REQUEST_URI'], $basePath)) {
    echo "<p><strong>Mismatch Warning:</strong> This is why the router failed. Case sensitivity or path differences.</p>";
}
