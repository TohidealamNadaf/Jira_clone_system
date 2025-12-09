<?php
// Debug script to test notification preferences update

require 'bootstrap/autoload.php';

use App\Core\Database;

// Test the insertOrUpdate query
$table = 'notification_preferences';
$data = [
    'user_id' => 1,
    'event_type' => 'issue_created',
    'in_app' => 1,
    'email' => 1,
    'push' => 0,
];
$uniqueKeys = ['user_id', 'event_type'];

// Build the SQL manually to see what's generated
$columns = array_keys($data);
$quotedColumns = array_map(fn($col) => "`$col`", $columns);
$placeholders = array_map(fn($col) => ":$col", $columns);

$updateClauses = [];
foreach ($columns as $col) {
    if (!in_array($col, $uniqueKeys)) {
        $updateClauses[] = "`$col` = :{$col}";
    }
}

$sql = sprintf(
    'INSERT INTO `%s` (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
    $table,
    implode(', ', $quotedColumns),
    implode(', ', $placeholders),
    implode(', ', $updateClauses)
);

echo "=== DEBUG: Notification Preferences Insert ===\n\n";
echo "Table: $table\n";
echo "Data: " . json_encode($data) . "\n";
echo "Unique Keys: " . json_encode($uniqueKeys) . "\n\n";

echo "Generated SQL:\n$sql\n\n";

echo "Columns: " . json_encode($columns) . "\n";
echo "Placeholders: " . json_encode($placeholders) . "\n";
echo "Update Clauses: " . json_encode($updateClauses) . "\n\n";

echo "Parameters passed to execute:\n";
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Try to execute
try {
    $result = Database::insertOrUpdate($table, $data, $uniqueKeys);
    echo "✓ Query executed successfully!\n";
    echo "Result: " . ($result ? 'true' : 'false') . "\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
