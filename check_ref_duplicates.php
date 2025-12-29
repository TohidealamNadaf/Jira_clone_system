<?php
require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "Checking for duplicates in referenced tables...\n";

$tables = ['projects', 'statuses', 'issue_priorities', 'issue_types', 'users'];

foreach ($tables as $table) {
    echo "Checking {$table}...\n";
    $sql = "SELECT id, COUNT(*) as count FROM {$table} GROUP BY id HAVING count > 1";
    $dups = Database::select($sql);
    if (!empty($dups)) {
        echo "FAIL: Duplicates found in {$table} (by ID)!\n";
    } else {
        echo "OK: No duplicates in {$table} (by ID).\n";
    }
}

// Check if any issue references non-unique names?
// The join is on ID, so names don't matter for duplication unless we join on name.
// We join on ID.

echo "Done.\n";
