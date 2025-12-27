<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

echo "Setting up project_documents table...\n";

try {
    $sql = "
        CREATE TABLE IF NOT EXISTS project_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            filename VARCHAR(255) NOT NULL,
            original_filename VARCHAR(255) NOT NULL,
            mime_type VARCHAR(100),
            size BIGINT NOT NULL,
            path VARCHAR(255) NOT NULL,
            category VARCHAR(50) DEFAULT 'other',
            version VARCHAR(20) DEFAULT '1.0',
            is_public TINYINT(1) DEFAULT 1,
            download_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    Database::query($sql);
    echo "Table 'project_documents' created or already exists.\n";

} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
    exit(1);
}
