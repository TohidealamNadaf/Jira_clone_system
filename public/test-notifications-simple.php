<?php
/**
 * Simple Notifications Test - No Login Required
 * Just checks if the database schema is correct
 */

echo "<h2>Notifications Schema Verification</h2>\n";
echo "<pre>\n";

try {
    // Direct database connection
    $mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system');
    
    if ($mysqli->connect_error) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }
    
    echo "✓ Database connected\n\n";
    
    // Check if notifications table exists
    echo "1. Checking notifications table exists...\n";
    $result = $mysqli->query("SHOW TABLES LIKE 'notifications'");
    if ($result->num_rows === 0) {
        throw new Exception('notifications table does not exist!');
    }
    echo "   ✓ Table exists\n\n";
    
    // Check columns
    echo "2. Verifying all required columns exist:\n";
    $result = $mysqli->query('DESC notifications');
    
    $requiredColumns = [
        'id', 'user_id', 'type', 'title', 'message', 'action_url', 
        'actor_user_id', 'related_issue_id', 'related_project_id', 
        'priority', 'is_read', 'read_at', 'created_at'
    ];
    
    $foundColumns = [];
    while ($row = $result->fetch_assoc()) {
        $foundColumns[] = $row['Field'];
        echo "   ✓ {$row['Field']}: {$row['Type']}\n";
    }
    
    echo "\n3. Checking for missing columns...\n";
    $missing = array_diff($requiredColumns, $foundColumns);
    if (empty($missing)) {
        echo "   ✓ All required columns present\n\n";
    } else {
        throw new Exception('Missing columns: ' . implode(', ', $missing));
    }
    
    // Check indexes
    echo "4. Verifying indexes:\n";
    $result = $mysqli->query('SHOW INDEX FROM notifications');
    $indexCount = 0;
    while ($row = $result->fetch_assoc()) {
        if ($row['Key_name'] !== 'PRIMARY') {
            echo "   ✓ {$row['Key_name']} on {$row['Column_name']}\n";
            $indexCount++;
        }
    }
    echo "   Total indexes: " . ($indexCount + 1) . "\n\n";
    
    // Try a test query
    echo "5. Testing a query...\n";
    $result = $mysqli->query('SELECT COUNT(*) as count FROM notifications');
    $row = $result->fetch_assoc();
    echo "   ✓ Query works - Total notifications: {$row['count']}\n\n";
    
    $mysqli->close();
    
    echo "═════════════════════════════════════════════════\n";
    echo "✅ ALL TESTS PASSED - Notifications system is ready!\n";
    echo "═════════════════════════════════════════════════\n\n";
    echo "Next steps:\n";
    echo "1. Log in to your account\n";
    echo "2. Visit: http://localhost:8080/jira_clone_system/public/notifications\n";
    echo "3. Or run: http://localhost:8080/jira_clone_system/public/test-notifications-page.php\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "</pre>\n";
