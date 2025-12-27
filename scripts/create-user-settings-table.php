<?php
/**
 * Create user_settings table migration
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Core\Database;

try {
    echo "Creating user_settings table...\n";

    // Check if table exists
    $tableExists = Database::selectValue(
        "SELECT COUNT(*) FROM information_schema.TABLES 
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_settings'",
        []
    );

    if ($tableExists) {
        echo "✓ user_settings table already exists\n";
        exit(0);
    }

    // Create the table
    $sql = "
    CREATE TABLE user_settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL UNIQUE,
        theme VARCHAR(50) DEFAULT 'light' COMMENT 'light, dark, auto',
        language VARCHAR(5) DEFAULT 'en' COMMENT 'Language preference',
        items_per_page INT DEFAULT 25 COMMENT 'Items to show per page',
        timezone VARCHAR(50) DEFAULT 'UTC' COMMENT 'User timezone',
        date_format VARCHAR(50) DEFAULT 'MM/DD/YYYY' COMMENT 'Date display format',
        auto_refresh TINYINT DEFAULT 1 COMMENT 'Auto-refresh notifications',
        compact_view TINYINT DEFAULT 0 COMMENT 'Use compact layout',
        show_profile TINYINT DEFAULT 1 COMMENT 'Show profile publicly',
        show_activity TINYINT DEFAULT 1 COMMENT 'Show activity in timelines',
        show_email TINYINT DEFAULT 0 COMMENT 'Show email publicly',
        high_contrast TINYINT DEFAULT 0 COMMENT 'Enable high contrast',
        reduce_motion TINYINT DEFAULT 0 COMMENT 'Reduce animations',
        large_text TINYINT DEFAULT 0 COMMENT 'Increase font size',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    Database::query($sql, []);
    echo "✓ user_settings table created successfully\n";

    // Seed default settings for existing users
    echo "Seeding default settings for existing users...\n";
    
    $users = Database::select("SELECT id FROM users WHERE is_active = 1", []);
    
    foreach ($users as $user) {
        $exists = Database::selectValue(
            "SELECT id FROM user_settings WHERE user_id = ?",
            [$user['id']]
        );
        
        if (!$exists) {
            Database::insert(
                "INSERT INTO user_settings (user_id, theme, language, items_per_page, timezone, date_format) 
                 VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $user['id'],
                    'light',
                    'en',
                    25,
                    'UTC',
                    'MM/DD/YYYY'
                ]
            );
        }
    }
    
    echo "✓ Default settings seeded for " . count($users) . " users\n";
    echo "✅ Migration completed successfully!\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
