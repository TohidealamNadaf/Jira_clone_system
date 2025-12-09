<?php
/**
 * Diagnostic Script - Identify why projects are disappearing
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

try {
    echo "=== JIRA Clone System Diagnostics ===\n\n";
    
    // Check 1: Verify roles exist
    echo "1. Checking Roles Table:\n";
    $roles = Database::select("SELECT id, name, slug FROM roles ORDER BY name");
    if (empty($roles)) {
        echo "   ❌ ERROR: No roles found in database!\n";
        echo "   This is why project member assignment fails.\n";
    } else {
        echo "   ✓ Found " . count($roles) . " roles:\n";
        foreach ($roles as $role) {
            echo "      - {$role['name']} ({$role['slug']})\n";
        }
    }
    
    // Check 2: Verify project-admin role exists
    echo "\n2. Checking for 'project-admin' role:\n";
    $projectAdminRole = Database::selectOne(
        "SELECT id FROM roles WHERE slug = 'project-admin' LIMIT 1"
    );
    if (!$projectAdminRole) {
        echo "   ❌ ERROR: 'project-admin' role does not exist!\n";
        echo "   This causes project creation to fail silently.\n";
    } else {
        echo "   ✓ project-admin role exists with ID: " . $projectAdminRole['id'] . "\n";
    }
    
    // Check 3: Count existing projects
    echo "\n3. Checking existing projects:\n";
    $projectCount = Database::selectValue("SELECT COUNT(*) FROM projects");
    echo "   Total projects in database: $projectCount\n";
    
    if ($projectCount > 0) {
        $projects = Database::select("SELECT id, key, name FROM projects LIMIT 10");
        foreach ($projects as $project) {
            echo "   - [{$project['key']}] {$project['name']} (ID: {$project['id']})\n";
        }
    }
    
    // Check 4: Check foreign key constraints
    echo "\n4. Checking Foreign Key Constraints:\n";
    $pdo = Database::getConnection();
    $constraints = $pdo->query("
        SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, DELETE_RULE
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE() 
        AND REFERENCED_TABLE_NAME = 'projects'
        ORDER BY TABLE_NAME
    ")->fetchAll(\PDO::FETCH_ASSOC);
    
    if (empty($constraints)) {
        echo "   No constraints found referencing projects table\n";
    } else {
        foreach ($constraints as $c) {
            $status = ($c['DELETE_RULE'] === 'CASCADE') ? '❌ CASCADE' : '✓ ' . $c['DELETE_RULE'];
            echo "   $status - {$c['TABLE_NAME']}.{$c['COLUMN_NAME']} -> projects\n";
        }
    }
    
    // Check 5: Look for recent database errors
    echo "\n5. Checking Database Tables:\n";
    
    // Check issues table
    $issueCount = Database::selectValue("SELECT COUNT(*) FROM issues");
    echo "   - issues: $issueCount rows\n";
    
    // Check comments table
    $commentCount = Database::selectValue("SELECT COUNT(*) FROM comments");
    echo "   - comments: $commentCount rows\n";
    
    // Check project_members table
    $memberCount = Database::selectValue("SELECT COUNT(*) FROM project_members");
    echo "   - project_members: $memberCount rows\n";
    
    echo "\n✅ Diagnostics complete.\n";
    
} catch (\Exception $e) {
    echo "❌ Diagnostic Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
