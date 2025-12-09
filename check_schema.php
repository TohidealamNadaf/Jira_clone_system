<?php
require 'bootstrap/app.php';

$schema = \App\Core\Database::select("DESCRIBE issues");
echo "Issues table columns:\n";
foreach ($schema as $col) {
    echo "- {$col['Field']} ({$col['Type']})\n";
}
