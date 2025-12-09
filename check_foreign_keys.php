<?php
/**
 * Check actual foreign keys in database
 * Run: php check_foreign_keys.php
 */

require_once __DIR__ . '/bootstrap/app.php';

try {
    $pdo = \App\Core\Database::getConnection();
    
    echo "=== CHECKING FOREIGN KEYS IN DATABASE ===\n\n";
    
    // Get all foreign keys for all tables
    $sql = "SELECT 
                TABLE_NAME,
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = 'jiira_clonee_system' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
            ORDER BY TABLE_NAME, CONSTRAINT_NAME";
    
    $stmt = $pdo->query($sql);
    $keys = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    if (empty($keys)) {
        echo "No foreign keys found\n";
        exit(0);
    }
    
    $current_table = null;
    foreach ($keys as $key) {
        if ($current_table !== $key['TABLE_NAME']) {
            $current_table = $key['TABLE_NAME'];
            echo "\nTable: {$key['TABLE_NAME']}\n";
            echo str_repeat("-", 80) . "\n";
        }
        
        echo "  Constraint: {$key['CONSTRAINT_NAME']}\n";
        echo "    Column: {$key['COLUMN_NAME']}\n";
        echo "    References: {$key['REFERENCED_TABLE_NAME']}.{$key['REFERENCED_COLUMN_NAME']}\n\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
