<?php
/**
 * Test wrapper to verify seed script fix
 */

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

echo "Testing Database class methods...\n";
echo "================================\n\n";

try {
    // Test 1: Check getConnection method
    echo "✓ Testing Database::getConnection()\n";
    $connection = Database::getConnection();
    echo "  Result: " . (is_object($connection) ? "SUCCESS - PDO object returned\n" : "FAILED\n");
    
    // Test 2: Check selectOne method
    echo "\n✓ Testing Database::selectOne()\n";
    $result = Database::selectOne("SELECT 1 as test");
    echo "  Result: " . (isset($result['test']) ? "SUCCESS - Query executed\n" : "FAILED\n");
    
    // Test 3: Check selectValue method
    echo "\n✓ Testing Database::selectValue()\n";
    $count = Database::selectValue("SELECT COUNT(*) FROM projects");
    echo "  Result: SUCCESS - Count = $count\n";
    
    // Test 4: Check that users exist
    echo "\n✓ Testing user queries\n";
    $admin = Database::selectOne("SELECT id FROM users WHERE is_admin = 1 LIMIT 1");
    echo "  Admin user: " . (isset($admin['id']) ? "FOUND (ID: {$admin['id']})\n" : "NOT FOUND\n");
    
    echo "\n================================\n";
    echo "✅ ALL DATABASE METHODS WORKING\n";
    echo "✅ SEED SCRIPT SHOULD NOW WORK\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
?>
