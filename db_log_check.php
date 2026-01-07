<?php
require_once __DIR__ . '/bootstrap/app.php';
$status = App\Helpers\SystemHealth::getDatabaseStatus();
file_put_contents('db_check.txt', print_r($status, true));
