<?php
/**
 * Create Baramati Project
 * Run: php create_baramati.php
 */

require_once __DIR__ . '/bootstrap/app.php';

try {
    echo "Creating Baramati Project...\n\n";
    
    // Check if project key already exists
    $existing = \App\Core\Database::selectOne(
        "SELECT id FROM projects WHERE `key` = ?",
        ['BARA']
    );
    
    if ($existing) {
        echo "Project with key 'BARA' already exists\n";
        exit(0);
    }
    
    // Insert project (without workflow_id)
    $projectId = \App\Core\Database::insert('projects', [
        'name' => 'Baramati Project',
        'key' => 'BARA',
        'description' => 'Baramati Project',
        'lead_id' => 1,
        'default_assignee' => 'unassigned',
        'issue_count' => 0,
        'is_archived' => 0,
    ]);
    
    if (!$projectId) {
        echo "Failed to create project\n";
        exit(1);
    }
    
    echo "✅ Baramati Project created!\n\n";
    echo "Project Details:\n";
    echo "  Name: Baramati Project\n";
    echo "  Key: BARA\n";
    echo "  ID: {$projectId}\n";
    echo "  Lead: User ID 1\n\n";
    
    // Add default project member
    $existing_member = \App\Core\Database::selectOne(
        "SELECT 1 FROM project_members WHERE project_id = ? AND user_id = ?",
        [$projectId, 1]
    );
    
    if (!$existing_member) {
        \App\Core\Database::insert('project_members', [
            'project_id' => $projectId,
            'user_id' => 1,
            'role' => 'lead',
        ]);
    }
    
    echo "Access at: http://localhost/jira_clone_system/public/projects/BARA\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
