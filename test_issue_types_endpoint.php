<?php
/**
 * Test issue types endpoint
 */

session_start();
$_SESSION['user'] = ['id' => 1, 'email' => 'test@example.com'];

include 'bootstrap/app.php';

try {
    $db = new \App\Core\Database();
    $types = $db->select("SELECT id, name, icon, color FROM issue_types ORDER BY sort_order ASC, name ASC");
    
    echo "✅ Issue Types Found: " . count($types) . "\n";
    echo json_encode($types, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
