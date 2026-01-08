<?php
$logFile = __DIR__ . '/verify_log.txt';
file_put_contents($logFile, "Starting verification...\n");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=cways_mis", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    file_put_contents($logFile, "Tables found: " . count($tables) . "\n", FILE_APPEND);

    if (count($tables) > 0) {
        $users = $pdo->query("SELECT count(*) FROM users")->fetchColumn();
        file_put_contents($logFile, "Users count: $users\n", FILE_APPEND);

        $issues = $pdo->query("SELECT count(*) FROM issues")->fetchColumn();
        file_put_contents($logFile, "Issues count: $issues\n", FILE_APPEND);

        // Check admin
        $admin = $pdo->query("SELECT email, is_admin FROM users WHERE id=1")->fetch();
        file_put_contents($logFile, "Admin: " . print_r($admin, true) . "\n", FILE_APPEND);
    }

} catch (Exception $e) {
    file_put_contents($logFile, "Error: " . $e->getMessage() . "\n", FILE_APPEND);
}
