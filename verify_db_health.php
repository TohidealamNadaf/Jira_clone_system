<?php
require_once __DIR__ . '/bootstrap/app.php';
use App\Helpers\SystemHealth;

$dbStatus = SystemHealth::getDatabaseStatus();
echo "Database Status:\n";
print_r($dbStatus);
