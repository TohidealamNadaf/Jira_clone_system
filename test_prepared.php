<?php
$pdo = new PDO('mysql:host=localhost', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('USE jira_clone');

echo "Test 1: Regular execute\n";
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([1]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Result: " . $result['count'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTest 2: With PDO::EMULATE_PREPARES = false\n";
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([1]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Result: " . $result['count'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone\n";
