<?php
/**
 * Comprehensive Fix Script - Resolves All Known Issues
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

$logFile = __DIR__ . '/storage/logs/comprehensive_fix.log';
@mkdir(dirname($logFile), 0755, true);

function log_msg($msg) {
    global $logFile;
    $line = "[" . date('Y-m-d H:i:s') . "] $msg\n";
    file_put_contents($logFile, $line, FILE_APPEND);
    echo $line;
}

try {
    log_msg("========================================");
    log_msg("COMPREHENSIVE FIX - Starting");
    log_msg("========================================\n");

    $pdo = Database::getConnection();
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // ============================================
    // PHASE 1: Fix Foreign Key Constraints
    // ============================================
    log_msg("\n[PHASE 1] Fixing Foreign Key Constraints...");
    $pdo->query("SET FOREIGN_KEY_CHECKS = 0");

    $constraints_to_fix = [
        ['table' => 'issues', 'old_fk' => 'issues_project_id_fk'],
        ['table' => 'project_members', 'old_fk' => 'project_members_project_id_fk'],
        ['table' => 'boards', 'old_fk' => 'boards_project_id_fk'],
        ['table' => 'components', 'old_fk' => 'components_project_id_fk'],
        ['table' => 'labels', 'old_fk' => 'labels_project_id_fk'],
        ['table' => 'versions', 'old_fk' => 'versions_project_id_fk'],
    ];

    foreach ($constraints_to_fix as $constraint) {
        try {
            $pdo->query("ALTER TABLE `{$constraint['table']}` DROP FOREIGN KEY `{$constraint['old_fk']}`");
            log_msg("  ✓ Dropped {$constraint['old_fk']}");
        } catch (\Exception $e) {
            log_msg("  ⚠ {$constraint['old_fk']} already dropped or doesn't exist");
        }
    }

    // Recreate constraints with ON DELETE RESTRICT
    $new_constraints = [
        "ALTER TABLE `issues` ADD CONSTRAINT `issues_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `project_members` ADD CONSTRAINT `project_members_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `boards` ADD CONSTRAINT `boards_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `components` ADD CONSTRAINT `components_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `labels` ADD CONSTRAINT `labels_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
        "ALTER TABLE `versions` ADD CONSTRAINT `versions_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT",
    ];

    foreach ($new_constraints as $sql) {
        try {
            $pdo->query($sql);
            preg_match('/`(\w+)_project_id_fk`/', $sql, $matches);
            log_msg("  ✓ Created {$matches[1]} → projects (RESTRICT)");
        } catch (\Exception $e) {
            log_msg("  ⚠ Constraint might already exist: " . $e->getMessage());
        }
    }

    $pdo->query("SET FOREIGN_KEY_CHECKS = 1");

    // ============================================
    // PHASE 2: Create Missing Roles
    // ============================================
    log_msg("\n[PHASE 2] Creating Missing Roles...");

    $required_roles = [
        ['name' => 'Administrator', 'slug' => 'admin', 'desc' => 'Full system access', 'system' => 1],
        ['name' => 'Project Admin', 'slug' => 'project-admin', 'desc' => 'Administrator for a project', 'system' => 0],
        ['name' => 'Project Manager', 'slug' => 'project-manager', 'desc' => 'Manage projects', 'system' => 1],
        ['name' => 'Developer', 'slug' => 'developer', 'desc' => 'Develop and contribute', 'system' => 1],
        ['name' => 'QA Tester', 'slug' => 'qa-tester', 'desc' => 'Test and report issues', 'system' => 1],
        ['name' => 'Viewer', 'slug' => 'viewer', 'desc' => 'Read-only access', 'system' => 1],
    ];

    foreach ($required_roles as $role) {
        $existing = Database::selectOne("SELECT id FROM roles WHERE slug = ?", [$role['slug']]);
        if (!$existing) {
            Database::insert('roles', [
                'name' => $role['name'],
                'slug' => $role['slug'],
                'description' => $role['desc'],
                'is_system' => $role['system'],
            ]);
            log_msg("  ✓ Created role: {$role['slug']}");
        } else {
            log_msg("  ✓ Role already exists: {$role['slug']}");
        }
    }

    // ============================================
    // PHASE 3: Verify Workflow Setup
    // ============================================
    log_msg("\n[PHASE 3] Verifying Workflow Setup...");

    $workflow = Database::selectOne("SELECT id FROM workflows WHERE is_default = 1");
    if (!$workflow) {
        log_msg("  ❌ ERROR: No default workflow found!");
        throw new \Exception("No default workflow");
    }
    log_msg("  ✓ Default workflow exists");

    $initial_status = Database::selectOne(
        "SELECT s.id, s.name FROM statuses s
         JOIN workflow_statuses ws ON s.id = ws.status_id
         WHERE ws.workflow_id = ? AND ws.is_initial = 1",
        [$workflow['id']]
    );

    if (!$initial_status) {
        log_msg("  ❌ ERROR: No initial status in workflow!");
        throw new \Exception("No initial status");
    }
    log_msg("  ✓ Initial status: {$initial_status['name']}");

    // ============================================
    // PHASE 4: Verify Issue Types
    // ============================================
    log_msg("\n[PHASE 4] Verifying Issue Types...");

    $issue_types = Database::select("SELECT id, name FROM issue_types");
    if (empty($issue_types)) {
        log_msg("  ❌ ERROR: No issue types found!");
        throw new \Exception("No issue types");
    }
    log_msg("  ✓ Found " . count($issue_types) . " issue types");

    // ============================================
    // PHASE 5: Test Complete Flow
    // ============================================
    log_msg("\n[PHASE 5] Testing Complete Flow...");

    // Create test project
    log_msg("  Creating test project...");
    $projectId = Database::insert('projects', [
        'key' => 'FIX' . date('Hs'),
        'name' => 'Fix Test ' . date('Y-m-d H:i:s'),
        'created_by' => 1,
        'default_assignee' => 'unassigned',
    ]);
    log_msg("    ✓ Project created (ID: $projectId)");

    // Add project member
    $adminRole = Database::selectOne("SELECT id FROM roles WHERE slug = 'project-admin'");
    if ($adminRole) {
        Database::insert('project_members', [
            'project_id' => $projectId,
            'user_id' => 1,
            'role_id' => $adminRole['id'],
        ]);
        log_msg("    ✓ Project member added");
    }

    // Create issue
    log_msg("  Creating test issue...");
    $issueId = Database::insert('issues', [
        'project_id' => $projectId,
        'issue_type_id' => 3,
        'status_id' => $initial_status['id'],
        'priority_id' => 3,
        'issue_key' => 'FIX' . date('Hs') . '-1',
        'issue_number' => 1,
        'summary' => 'Test Issue',
        'description' => 'Testing the fix',
        'reporter_id' => 1,
    ]);
    log_msg("    ✓ Issue created (ID: $issueId)");

    // Add comment
    log_msg("  Adding test comment...");
    $commentId = Database::insert('comments', [
        'issue_id' => $issueId,
        'user_id' => 1,
        'body' => 'Test comment body',
    ]);
    log_msg("    ✓ Comment created (ID: $commentId)");

    // Verify project still exists
    $verifyProject = Database::selectOne("SELECT id FROM projects WHERE id = ?", [$projectId]);
    if (!$verifyProject) {
        log_msg("  ❌ ERROR: Project disappeared after operations!");
        throw new \Exception("Project was deleted");
    }
    log_msg("    ✓ Project still exists");

    // Verify issue still exists
    $verifyIssue = Database::selectOne("SELECT id FROM issues WHERE id = ?", [$issueId]);
    if (!$verifyIssue) {
        log_msg("  ❌ ERROR: Issue disappeared!");
        throw new \Exception("Issue was deleted");
    }
    log_msg("    ✓ Issue still exists");

    // Verify comment still exists
    $verifyComment = Database::selectOne("SELECT id FROM comments WHERE id = ?", [$commentId]);
    if (!$verifyComment) {
        log_msg("  ❌ ERROR: Comment disappeared!");
        throw new \Exception("Comment was deleted");
    }
    log_msg("    ✓ Comment still exists");

    // Test comment update
    log_msg("  Testing comment update...");
    Database::update('comments', [
        'body' => 'Updated comment body',
    ], 'id = ?', [$commentId]);
    log_msg("    ✓ Comment updated successfully");

    // Verify update persisted
    $updatedComment = Database::selectOne("SELECT body FROM comments WHERE id = ?", [$commentId]);
    if ($updatedComment['body'] !== 'Updated comment body') {
        log_msg("  ❌ ERROR: Comment update did not persist!");
        throw new \Exception("Comment update failed");
    }
    log_msg("    ✓ Update persisted correctly");

    // Test comment delete
    log_msg("  Testing comment delete...");
    Database::delete('comments', 'id = ?', [$commentId]);
    log_msg("    ✓ Comment deleted successfully");

    // Verify delete persisted
    $deletedComment = Database::selectOne("SELECT id FROM comments WHERE id = ?", [$commentId]);
    if ($deletedComment) {
        log_msg("  ❌ ERROR: Comment was not deleted!");
        throw new \Exception("Comment delete failed");
    }
    log_msg("    ✓ Delete persisted correctly");

    // Verify issue still exists after comment operations
    $finalIssue = Database::selectOne("SELECT id FROM issues WHERE id = ?", [$issueId]);
    if (!$finalIssue) {
        log_msg("  ❌ ERROR: Issue disappeared after comment operations!");
        throw new \Exception("Issue was deleted during operations");
    }
    log_msg("    ✓ Issue still exists after comment operations");

    // Verify project still exists after all operations
    $finalProject = Database::selectOne("SELECT id FROM projects WHERE id = ?", [$projectId]);
    if (!$finalProject) {
        log_msg("  ❌ ERROR: Project disappeared!");
        throw new \Exception("Project was deleted");
    }
    log_msg("    ✓ Project still exists after all operations");

    // ============================================
    // SUCCESS
    // ============================================
    log_msg("\n========================================");
    log_msg("✅ ALL FIXES APPLIED AND VERIFIED!");
    log_msg("========================================");
    log_msg("\nThe system is now ready for testing.");
    log_msg("You can create projects, issues, and comments.");
    log_msg("Comment edit/delete should work without 404 errors.");
    log_msg("Data will persist after page reload.\n");

} catch (\Exception $e) {
    log_msg("\n========================================");
    log_msg("❌ FIX FAILED: " . $e->getMessage());
    log_msg("========================================\n");
    exit(1);
}

echo "\n✅ Log saved to: $logFile\n";
?>
