<?php
require_once __DIR__ . '/../bootstrap/app.php';
use App\Core\Database;

header('Content-Type: text/plain');

try {
    echo "Checking issue_attachments table...\n";
    $rows = Database::select("SELECT * FROM issue_attachments ORDER BY id DESC LIMIT 5");
    if (empty($rows)) {
        echo "Table is EMPTY (or no recent rows).\n";
    } else {
        echo "Found " . count($rows) . " rows:\n";
        print_r($rows);
    }

    echo "\nChecking users table (for id 1)...\n";
    $user = Database::selectOne("SELECT * FROM users WHERE id = 1");
    print_r($user);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
