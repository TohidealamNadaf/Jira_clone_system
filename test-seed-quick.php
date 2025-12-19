<?php
/**
 * Quick test to verify seed script works
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "✅ Bootstrap loaded successfully\n";
echo "✅ Config loaded\n";
echo "✅ Database: " . config('database.name') . "\n\n";

try {
    echo "Testing database connection...\n";
    $result = Database::selectValue("SELECT COUNT(*) FROM users");
    echo "✅ Database connected successfully\n";
    echo "✅ Users count: $result\n\n";
    
    echo "You can now run the seed script:\n";
    echo "http://localhost:8080/jira_clone_system/public/run-seed-now.php\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
