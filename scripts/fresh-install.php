<?php
/**
 * Fresh Installation Script
 * 
 * This script sets up a fresh database with all required tables and seed data.
 * 
 * Usage: php scripts/fresh-install.php
 */

declare(strict_types=1);

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║           JIRA CLONE - FRESH INSTALLATION                       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Check if config exists
$basePath = dirname(__DIR__);
$configFile = $basePath . '/config/config.php';
$localConfigFile = $basePath . '/config/config.local.php';

if (!file_exists($configFile)) {
    echo "❌ Configuration file not found: config/config.php\n";
    exit(1);
}

// Load config
$config = require $configFile;
if (file_exists($localConfigFile)) {
    $localConfig = require $localConfigFile;
    $config = array_replace_recursive($config, $localConfig);
}

$dbConfig = $config['database'];

echo "Database: {$dbConfig['name']}\n";
echo "Host: {$dbConfig['host']}:{$dbConfig['port']}\n";
echo "User: {$dbConfig['username']}\n\n";

// Connect to MySQL (without database)
try {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;charset=%s',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['charset']
    );
    
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "✅ Connected to MySQL server\n";
} catch (PDOException $e) {
    echo "❌ Failed to connect: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 1: Create database if not exists
echo "\n📦 Step 1: Creating database...\n";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['name']}` 
                DEFAULT CHARACTER SET utf8mb4 
                COLLATE utf8mb4_unicode_ci");
    echo "   ✅ Database created or already exists\n";
} catch (PDOException $e) {
    echo "   ❌ Failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Select database
$pdo->exec("USE `{$dbConfig['name']}`");

// Step 2: Run schema
echo "\n📋 Step 2: Running schema.sql...\n";
$schemaFile = $basePath . '/database/schema.sql';
if (file_exists($schemaFile)) {
    $sql = file_get_contents($schemaFile);
    
    // Split by statement (simple approach)
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Remove comments and execute
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($s) { return !empty($s) && strpos($s, '--') !== 0; }
    );
    
    $count = 0;
    foreach ($statements as $stmt) {
        if (empty(trim($stmt))) continue;
        if (stripos($stmt, 'CREATE DATABASE') !== false) continue;
        if (stripos($stmt, 'USE `') !== false) continue;
        
        try {
            $pdo->exec($stmt);
            $count++;
        } catch (PDOException $e) {
            // Ignore duplicate table/key errors
            if (strpos($e->getMessage(), '1050') === false && 
                strpos($e->getMessage(), '1061') === false &&
                strpos($e->getMessage(), '1060') === false) {
                echo "   ⚠️ Warning: " . $e->getMessage() . "\n";
            }
        }
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "   ✅ Executed {$count} statements\n";
} else {
    echo "   ❌ schema.sql not found\n";
    exit(1);
}

// Step 3: Run seed data
echo "\n🌱 Step 3: Running seed.sql...\n";
$seedFile = $basePath . '/database/seed.sql';
if (file_exists($seedFile)) {
    $sql = file_get_contents($seedFile);
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($s) { return !empty($s) && strpos($s, '--') !== 0; }
    );
    
    $count = 0;
    foreach ($statements as $stmt) {
        if (empty(trim($stmt))) continue;
        
        try {
            $pdo->exec($stmt);
            $count++;
        } catch (PDOException $e) {
            // Ignore duplicate entry errors
            if (strpos($e->getMessage(), '1062') === false) {
                echo "   ⚠️ Warning: " . $e->getMessage() . "\n";
            }
        }
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "   ✅ Executed {$count} statements\n";
} else {
    echo "   ⚠️ seed.sql not found, skipping\n";
}

// Step 4: Create admin user
echo "\n👤 Step 4: Creating admin user...\n";
$adminEmail = 'admin@example.com';
$adminPassword = 'Admin@123';
$passwordHash = password_hash($adminPassword, PASSWORD_ARGON2ID);

try {
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update password
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->execute([$passwordHash, $adminEmail]);
        echo "   ✅ Admin password reset\n";
    } else {
        // Create admin
        $stmt = $pdo->prepare(
            "INSERT INTO users (email, password_hash, first_name, last_name, is_admin, is_active) 
             VALUES (?, ?, 'System', 'Administrator', 1, 1)"
        );
        $stmt->execute([$adminEmail, $passwordHash]);
        echo "   ✅ Admin user created\n";
    }
} catch (PDOException $e) {
    echo "   ⚠️ Warning: " . $e->getMessage() . "\n";
}

// Step 5: Create sample users if they don't exist
echo "\n👥 Step 5: Checking sample users...\n";
$sampleUsers = [
    ['john.smith@example.com', 'John', 'Smith'],
    ['jane.doe@example.com', 'Jane', 'Doe'],
    ['mike.wilson@example.com', 'Mike', 'Wilson'],
    ['sarah.jones@example.com', 'Sarah', 'Jones'],
    ['david.brown@example.com', 'David', 'Brown'],
];

$userCount = 0;
foreach ($sampleUsers as $user) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$user[0]]);
        if (!$stmt->fetch()) {
            $hash = password_hash('Password123', PASSWORD_ARGON2ID);
            $stmt = $pdo->prepare(
                "INSERT INTO users (email, password_hash, first_name, last_name, is_admin, is_active) 
                 VALUES (?, ?, ?, ?, 0, 1)"
            );
            $stmt->execute([$user[0], $hash, $user[1], $user[2]]);
            $userCount++;
        }
    } catch (PDOException $e) {
        // Ignore
    }
}
echo "   ✅ {$userCount} sample users created (password: Password123)\n";

// Step 6: Run migrations
echo "\n🔄 Step 6: Running migrations...\n";
$migrationsDir = $basePath . '/database/migrations';
if (is_dir($migrationsDir)) {
    $files = glob($migrationsDir . '/*.sql');
    sort($files);
    
    $migrationCount = 0;
    foreach ($files as $file) {
        try {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
            $migrationCount++;
        } catch (PDOException $e) {
            // Ignore duplicate errors
        }
    }
    echo "   ✅ {$migrationCount} migrations processed\n";
} else {
    echo "   ⚠️ No migrations directory\n";
}

// Step 7: Initialize notification preferences
echo "\n🔔 Step 7: Initializing notification preferences...\n";
try {
    $stmt = $pdo->query("SELECT id FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $eventTypes = [
        'issue_created', 'issue_assigned', 'issue_commented', 
        'issue_status_changed', 'issue_mentioned', 'issue_watched',
        'project_created', 'project_member_added', 'comment_reply'
    ];
    
    $prefCount = 0;
    foreach ($users as $userId) {
        foreach ($eventTypes as $eventType) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT IGNORE INTO notification_preferences 
                     (user_id, event_type, in_app, email, push) 
                     VALUES (?, ?, 1, 1, 0)"
                );
                $stmt->execute([$userId, $eventType]);
                $prefCount++;
            } catch (PDOException $e) {
                // Ignore duplicates
            }
        }
    }
    echo "   ✅ Notification preferences initialized\n";
} catch (PDOException $e) {
    echo "   ⚠️ Warning: " . $e->getMessage() . "\n";
}

// Final summary
echo "\n";
echo "═══════════════════════════════════════════════════════════════════\n";
echo "  ✅ INSTALLATION COMPLETE!\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

echo "  Admin Credentials:\n";
echo "  ─────────────────────\n";
echo "  Email:    admin@example.com\n";
echo "  Password: Admin@123\n\n";

echo "  Access URL:\n";
echo "  ─────────────────────\n";
echo "  http://localhost:8080/jira_clone_system/public/\n\n";

echo "  ⚠️  Remember to:\n";
echo "  • Change the admin password after login\n";
echo "  • Configure email settings for notifications\n";
echo "  • Set debug=false in production\n\n";

// Verify installation
echo "  📊 Verification:\n";
echo "  ─────────────────────\n";

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "  Tables: " . count($tables) . "\n";

$users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
echo "  Users: {$users}\n";

$projects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
echo "  Projects: {$projects}\n";

$issues = $pdo->query("SELECT COUNT(*) FROM issues")->fetchColumn();
echo "  Issues: {$issues}\n";

echo "\n";
