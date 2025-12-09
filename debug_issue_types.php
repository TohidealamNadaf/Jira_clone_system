<?php
require 'bootstrap/autoload.php';

$db = new \App\Core\Database();
$types = $db->select('SELECT id, name, icon, color FROM issue_types ORDER BY sort_order ASC');

echo "Issue Types in Database: " . count($types) . "\n";
echo json_encode($types, JSON_PRETTY_PRINT);
