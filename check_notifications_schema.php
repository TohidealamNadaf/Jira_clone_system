<?php
$pdo = new PDO('mysql:host=localhost;dbname=jira_clone', 'root', '');

echo "=== NOTIFICATIONS TABLE SCHEMA ===\n\n";

$result = $pdo->query('DESCRIBE notifications;');
$columns = $result->fetchAll(PDO::FETCH_ASSOC);

echo "Current columns in notifications table:\n";
foreach ($columns as $col) {
    echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
}

echo "\n=== EXPECTED COLUMNS ===\n";
$expected = ['id', 'user_id', 'type', 'title', 'message', 'action_url', 'actor_user_id', 'related_issue_id', 'related_project_id', 'priority', 'is_read', 'read_at', 'created_at'];
echo implode(", ", $expected) . "\n";

echo "\n=== MISSING COLUMNS ===\n";
$actual = array_column($columns, 'Field');
$missing = array_diff($expected, $actual);
if ($missing) {
    foreach ($missing as $col) {
        echo "  ✗ MISSING: $col\n";
    }
} else {
    echo "  ✓ All expected columns present\n";
}

echo "\n=== EXTRA COLUMNS ===\n";
$extra = array_diff($actual, $expected);
if ($extra) {
    foreach ($extra as $col) {
        echo "  ? EXTRA: $col\n";
    }
} else {
    echo "  ✓ No extra columns\n";
}
