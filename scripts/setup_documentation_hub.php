<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

try {
    echo "Creating project_documents table...\n";
    
    // Read and execute migration
    $migrationSql = file_get_contents(__DIR__ . '/../database/migrations/003_create_project_documents_table.sql');
    
    // Split SQL statements and execute
    $statements = array_filter(array_map('trim', explode(';', $migrationSql)));
    
    foreach ($statements as $sql) {
        if (!empty($sql)) {
            Database::query($sql);
        }
    }
    
    echo "✅ Project documents table created successfully!\n";
    echo "📁 Upload directory: " . __DIR__ . '/../public/uploads/documents/' . "\n";
    
    // Create uploads directory if it doesn't exist
    $uploadDir = __DIR__ . '/../public/uploads/documents/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        echo "📁 Created uploads directory: $uploadDir\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🚀 Documentation Hub is ready!\n";
echo "📖 Navigate to: /projects/{PROJECT_KEY}/documentation\n";