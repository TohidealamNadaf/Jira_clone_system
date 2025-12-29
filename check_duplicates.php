<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "Checking for duplicate issues...\n";

$sql = "
    SELECT issue_key, COUNT(*) as count
    FROM issues
    GROUP BY issue_key
    HAVING count > 1
";

$duplicates = Database::select($sql);

if (empty($duplicates)) {
    echo "No duplicate issue keys found.\n";
} else {
    echo "Found duplicate issue keys:\n";
    foreach ($duplicates as $dup) {
        echo "Key: {$dup['issue_key']}, Count: {$dup['count']}\n";
    }
}

// Also check if any issue matches specific conditions
$sql2 = "SELECT id, issue_key, start_date, end_date, due_date FROM issues WHERE issue_key = 'CWAYS-12'";
$cways12 = Database::select($sql2);
echo "\nChecking CWAYS-12:\n";
print_r($cways12);
