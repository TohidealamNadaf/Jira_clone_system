<?php
// Direct MySQL test - no framework
echo "=== DIRECT MYSQL CONNECTION TEST ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=localhost;port=3306;dbname=jiira_clonee_system;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );
    
    echo "✓ Connected to MySQL successfully!\n";
    echo "Database: jiira_clonee_system\n";
    
    // Test query
    $result = $pdo->query('SELECT COUNT(*) as count FROM projects');
    $row = $result->fetch(PDO::FETCH_ASSOC);
    
    echo "✓ Projects in database: " . $row['count'] . "\n\n";
    
    // List projects
    echo "=== PROJECTS LIST ===\n";
    $projects = $pdo->query('SELECT id, `key`, name FROM projects LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($projects)) {
        echo "No projects found. Create one first.\n";
    } else {
        foreach ($projects as $p) {
            echo "- {$p['key']}: {$p['name']}\n";
        }
    }
    
    // Get sprints
    echo "\n=== SPRINTS LIST ===\n";
    $sprints = $pdo->query('SELECT id, name, status FROM sprints LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($sprints)) {
        echo "No sprints found.\n";
    } else {
        foreach ($sprints as $s) {
            echo "- {$s['name']}: {$s['status']}\n";
        }
    }
    
    // Sprint URL
    if (!empty($projects)) {
        echo "\n=== TRY THIS URL ===\n";
        echo "http://localhost/jira_clone_system/public/projects/{$projects[0]['key']}/sprints\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    echo "\nDebug info:\n";
    echo "- Host: localhost\n";
    echo "- Port: 3306\n";
    echo "- Database: jiira_clonee_system\n";
    echo "- User: root\n";
    echo "- Charset: utf8mb4\n";
}
?>
