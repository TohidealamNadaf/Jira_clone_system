<?php
/**
 * Debug Script - Track Project Creation Step by Step
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

$logFile = __DIR__ . '/storage/logs/project_creation_debug.log';
@mkdir(dirname($logFile), 0755, true);

function debugLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $msg = "[$timestamp] $message\n";
    file_put_contents($logFile, $msg, FILE_APPEND);
    echo $msg;
}

try {
    debugLog("=== PROJECT CREATION DEBUG ===");
    
    // 1. Check database connection
    debugLog("1. Checking database connection...");
    $pdo = Database::getConnection();
    $pdo->query("SELECT 1");
    debugLog("   ✓ Database connected");
    
    // 2. Check if roles exist
    debugLog("2. Checking roles...");
    $adminRole = Database::selectOne("SELECT id FROM roles WHERE slug = 'admin'");
    $projectAdminRole = Database::selectOne("SELECT id FROM roles WHERE slug = 'project-admin'");
    debugLog("   - admin role: " . ($adminRole ? "ID {$adminRole['id']}" : "NOT FOUND"));
    debugLog("   - project-admin role: " . ($projectAdminRole ? "ID {$projectAdminRole['id']}" : "NOT FOUND"));
    
    // 3. Check initial project count
    debugLog("3. Checking initial project count...");
    $initialCount = Database::selectValue("SELECT COUNT(*) FROM projects");
    debugLog("   Projects before: $initialCount");
    
    // 4. Try to create a test project
    debugLog("4. Attempting to create test project...");
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
    try {
        $result = Database::transaction(function () {
            debugLog("   - Starting transaction...");
            
            debugLog("   - Inserting project...");
            $projectId = Database::insert('projects', [
                'key' => 'DBG' . date('Hs'),
                'name' => 'Debug Test Project ' . date('Y-m-d H:i:s'),
                'description' => 'Debug test',
                'created_by' => 1,
                'default_assignee' => 'unassigned',
            ]);
            debugLog("   - Project inserted with ID: $projectId");
            
            // Verify project was created
            $project = Database::selectOne("SELECT id FROM projects WHERE id = ?", [$projectId]);
            if (!$project) {
                debugLog("   - ERROR: Project not found immediately after insert!");
                throw new \RuntimeException("Project creation failed");
            }
            debugLog("   - ✓ Project verified in database");
            
            // Try to add project member
            debugLog("   - Looking for project-admin role...");
            $defaultRole = Database::selectOne(
                "SELECT id FROM roles WHERE slug = 'project-admin' LIMIT 1"
            );
            
            if (!$defaultRole) {
                debugLog("   - WARNING: project-admin role not found, skipping member assignment");
            } else {
                debugLog("   - Found project-admin role ID: {$defaultRole['id']}");
                debugLog("   - Inserting project member...");
                Database::insert('project_members', [
                    'project_id' => $projectId,
                    'user_id' => 1,
                    'role_id' => $defaultRole['id'],
                ]);
                debugLog("   - ✓ Project member inserted");
            }
            
            debugLog("   - Transaction ending successfully...");
            return $projectId;
        });
        
        debugLog("5. Transaction completed successfully!");
        debugLog("   Created project ID: $result");
        
        // 6. Verify project persists
        debugLog("6. Verifying project persistence...");
        $finalProject = Database::selectOne("SELECT id, key, name FROM projects WHERE id = ?", [$result]);
        if ($finalProject) {
            debugLog("   ✓ Project still exists!");
            debugLog("   - Key: {$finalProject['key']}");
            debugLog("   - Name: {$finalProject['name']}");
        } else {
            debugLog("   ❌ ERROR: Project disappeared!");
        }
        
        // 7. Check project count
        debugLog("7. Checking final project count...");
        $finalCount = Database::selectValue("SELECT COUNT(*) FROM projects");
        debugLog("   Projects after: $finalCount (was $initialCount, difference: " . ($finalCount - $initialCount) . ")");
        
    } catch (\Exception $e) {
        debugLog("❌ Transaction failed: " . $e->getMessage());
        debugLog("   Stack: " . $e->getTraceAsString());
    }
    
    debugLog("=== DEBUG COMPLETE ===\n");
    
    echo "\nDebug log saved to: $logFile\n";
    
} catch (\Exception $e) {
    debugLog("❌ Fatal Error: " . $e->getMessage());
    exit(1);
}
?>
