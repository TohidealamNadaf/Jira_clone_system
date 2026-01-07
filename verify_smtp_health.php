<?php
require_once __DIR__ . '/bootstrap/app.php';
use App\Helpers\SystemHealth;

$status = SystemHealth::getMailerStatus();
print_r($status);
