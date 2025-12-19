<?php
require '../bootstrap/autoload.php';

try {
    $db = new \App\Core\Database();
    
    echo "=== PROJECTS ===\n";
    $projects = $db->select('SELECT id, key, name FROM projects LIMIT 10');
    if (empty($projects)) {
        echo "No projects found!\n";
    } else {
        foreach($projects as $p) {
            echo "- {$p['key']}: {$p['name']}\n";
        }
    }
    
    echo "\n=== BOARDS ===\n";
    $boards = $db->select('SELECT id, project_id, name FROM boards LIMIT 10');
    if (empty($boards)) {
        echo "No boards found!\n";
    } else {
        foreach($boards as $b) {
            echo "- Board {$b['id']}: {$b['name']} (Project: {$b['project_id']})\n";
        }
    }
    
    echo "\n=== SPRINTS ===\n";
    $sprints = $db->select('SELECT id, board_id, name, status FROM sprints LIMIT 10');
    if (empty($sprints)) {
        echo "No sprints found!\n";
    } else {
        foreach($sprints as $s) {
            echo "- Sprint {$s['id']}: {$s['name']} ({$s['status']}) in Board {$s['board_id']}\n";
        }
    }
    
    echo "\n=== QUICK LINKS ===\n";
    if (!empty($projects)) {
        $first = $projects[0];
        echo "Try this URL:\n";
        echo "http://localhost/jira_clone_system/public/projects/{$first['key']}/sprints\n";
    } else {
        echo "No projects found. Create one first at:\n";
        echo "http://localhost/jira_clone_system/public/dashboard\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString();
}
?>
