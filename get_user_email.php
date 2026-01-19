<?php
require_once __DIR__ . '/vendor/autoload.php';
$user = \App\Core\Database::selectOne("SELECT id, email FROM users LIMIT 1");
if ($user) {
    echo $user['email'];
} else {
    echo "No user found";
}
