<?php
require_once __DIR__ . '/vendor/autoload.php';
$user = \App\Core\Database::selectOne("SELECT email FROM users LIMIT 1");
echo "Email: " . ($user['email'] ?? 'No user found');
