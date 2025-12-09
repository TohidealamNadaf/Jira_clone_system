<?php
$pdo = new PDO('mysql:host=localhost', 'root', '');
$pdo->exec('USE jira_clone');
$cols = $pdo->query('DESCRIBE notifications')->fetchAll(PDO::FETCH_ASSOC);
echo "Columns in notifications table:\n";
foreach ($cols as $col) {
    echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
}
echo "\nTotal: " . count($cols) . " columns\n";
