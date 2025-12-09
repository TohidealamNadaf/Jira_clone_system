<?php
/**
 * Fix Missing Roles in Database
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

try {
    echo "Fixing missing roles...\n\n";
    
    // Check if project-admin role exists
    $projectAdminRole = Database::selectOne(
        "SELECT id FROM roles WHERE slug = 'project-admin'"
    );
    
    if (!$projectAdminRole) {
        echo "Creating 'project-admin' role...\n";
        Database::insert('roles', [
            'name' => 'Project Admin',
            'slug' => 'project-admin',
            'description' => 'Administrator for a specific project',
            'is_system' => 0,
        ]);
        echo "✓ Created project-admin role\n";
    } else {
        echo "✓ project-admin role already exists\n";
    }
    
    // Verify all required roles exist
    echo "\nVerifying all roles exist:\n";
    $requiredRoles = [
        'admin' => 'Administrator',
        'project-admin' => 'Project Admin',
        'project-manager' => 'Project Manager',
        'developer' => 'Developer',
        'qa-tester' => 'QA Tester',
        'viewer' => 'Viewer'
    ];
    
    $allRoles = Database::select("SELECT id, name, slug FROM roles");
    $existingRoles = array_column($allRoles, 'slug');
    
    foreach ($requiredRoles as $slug => $name) {
        if (in_array($slug, $existingRoles)) {
            echo "✓ $slug\n";
        } else {
            echo "✗ $slug (missing)\n";
        }
    }
    
    echo "\n✅ Role fix complete!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
