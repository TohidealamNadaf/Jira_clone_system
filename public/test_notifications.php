<?php
// Simple standalone test - no framework bootstrap
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$dbname = 'jiira_clonee_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

echo "<h2>Notification Debug</h2>";

// Get unread notifications for user 1 (admin)
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = 1 AND read_at IS NULL ORDER BY created_at DESC LIMIT 10");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Unread notifications for user 1: " . count($notifications) . "</p>";

if (count($notifications) > 0) {
    echo "<pre>";
    print_r($notifications);
    echo "</pre>";
} else {
    echo "<p style='color:green;'>✓ No unread notifications - dropdown should show 'No new notifications'</p>";
}

// Show all notifications
$stmt = $pdo->prepare("SELECT id, user_id, type, read_at, created_at FROM notifications ORDER BY id DESC");
$stmt->execute();
$all = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>All notifications in database:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>User ID</th><th>Type</th><th>Read At</th><th>Created At</th></tr>";
foreach ($all as $n) {
    $readStatus = $n['read_at'] ? $n['read_at'] : '<span style="color:red">UNREAD</span>';
    echo "<tr><td>{$n['id']}</td><td>{$n['user_id']}</td><td>{$n['type']}</td><td>$readStatus</td><td>{$n['created_at']}</td></tr>";
}
echo "</table>";
