<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

$pdo = Database::getConnection();

// Check if comments table exists
$tables = $pdo->query("SHOW TABLES LIKE 'comments'")->fetchAll(\PDO::FETCH_ASSOC);
echo "Comments table exists: " . (count($tables) > 0 ? "YES" : "NO") . "\n";

if (count($tables) > 0) {
    // Get columns
    $columns = $pdo->query("DESCRIBE comments")->fetchAll(\PDO::FETCH_ASSOC);
    echo "\nColumns in comments table:\n";
    foreach ($columns as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
    // Count records
    $count = $pdo->query("SELECT COUNT(*) as cnt FROM comments")->fetch(\PDO::FETCH_ASSOC);
    echo "\nTotal comments: " . $count['cnt'] . "\n";
}
?>
