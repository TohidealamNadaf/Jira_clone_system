<?php
require 'bootstrap/app.php';

$categories = \App\Core\Database::select('SELECT * FROM project_categories ORDER BY name ASC');
echo "Total categories: " . count($categories) . "\n\n";

if (empty($categories)) {
    echo "No categories found. Creating default categories...\n\n";
    
    $defaultCategories = [
        ['name' => 'Software', 'description' => 'Software development projects'],
        ['name' => 'Marketing', 'description' => 'Marketing and promotion projects'],
        ['name' => 'Operations', 'description' => 'Operations and infrastructure projects'],
        ['name' => 'HR', 'description' => 'Human resources projects'],
        ['name' => 'Finance', 'description' => 'Finance and accounting projects'],
    ];
    
    foreach ($defaultCategories as $cat) {
        \App\Core\Database::insert('project_categories', $cat);
        echo "✓ Created: " . $cat['name'] . "\n";
    }
    
    echo "\n";
    $categories = \App\Core\Database::select('SELECT * FROM project_categories ORDER BY name ASC');
}

echo "Available Categories:\n";
foreach ($categories as $cat) {
    echo "- ID: " . $cat['id'] . ", Name: " . $cat['name'] . "\n";
}
