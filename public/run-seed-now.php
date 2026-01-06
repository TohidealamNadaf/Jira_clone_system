<?php
/**
 * Direct Test Data Insertion
 * This script directly inserts test data into the database
 */

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

try {
    echo "<h1>🌱 Seeding Comprehensive Test Data</h1>";
    echo "<pre style='background: #f5f5f5; padding: 20px; border-radius: 8px; overflow-x: auto;'>";
    
    // Get user IDs
    $admin = Database::selectOne("SELECT id FROM users WHERE is_admin = 1 LIMIT 1");
    $user1 = Database::selectOne("SELECT id FROM users WHERE is_admin = 0 LIMIT 1");
    $user2 = Database::selectOne("SELECT id FROM users WHERE is_admin = 0 LIMIT 1 OFFSET 1");
    $user3 = Database::selectOne("SELECT id FROM users WHERE is_admin = 0 LIMIT 1 OFFSET 2");
    
    $admin_id = $admin['id'] ?? 1;
    $user1_id = $user1['id'] ?? 2;
    $user2_id = $user2['id'] ?? 3;
    $user3_id = $user3['id'] ?? 4;
    
    echo "✅ Admin ID: $admin_id\n";
    echo "✅ User 1 ID: $user1_id\n";
    echo "✅ User 2 ID: $user2_id\n";
    echo "✅ User 3 ID: $user3_id\n\n";
    
    // Get issue type IDs
    $bug = Database::selectOne("SELECT id FROM issue_types WHERE name = 'Bug' LIMIT 1");
    $feature = Database::selectOne("SELECT id FROM issue_types WHERE name = 'Feature' LIMIT 1");
    $task = Database::selectOne("SELECT id FROM issue_types WHERE name = 'Task' LIMIT 1");
    $improvement = Database::selectOne("SELECT id FROM issue_types WHERE name = 'Improvement' LIMIT 1");
    
    $bug_id = $bug['id'] ?? 1;
    $feature_id = $feature['id'] ?? 2;
    $task_id = $task['id'] ?? 3;
    $improvement_id = $improvement['id'] ?? 4;
    
    // Get status IDs
    $open = Database::selectOne("SELECT id FROM statuses WHERE name = 'Open' LIMIT 1");
    $in_progress = Database::selectOne("SELECT id FROM statuses WHERE name = 'In Progress' LIMIT 1");
    $done = Database::selectOne("SELECT id FROM statuses WHERE name = 'Done' LIMIT 1");
    
    $open_id = $open['id'] ?? 1;
    $in_progress_id = $in_progress['id'] ?? 2;
    $done_id = $done['id'] ?? 3;
    
    echo "📋 Issue Types and Statuses loaded\n\n";
    
    // ====== CREATE PROJECTS ======
    echo "🚀 Creating 5 test projects...\n";
    
    $projects = [
        'ECOM' => 'E-Commerce Platform',
        'MOB' => 'Mobile App',
        'API' => 'Backend API',
        'DEVOPS' => 'DevOps Infrastructure',
        'QA' => 'QA & Testing'
    ];
    
    $project_ids = [];
    
    foreach ($projects as $key => $name) {
        $exists = Database::selectOne("SELECT id FROM projects WHERE `key` = ?", [$key]);
        if ($exists) {
            echo "   ⚠️  Project $key already exists\n";
            $project_ids[$key] = $exists['id'];
        } else {
            Database::insert('projects', [
                'name' => $name,
                'key' => $key,
                'description' => "Test project for $name",
                'lead_id' => $admin_id,
                'created_by' => $admin_id,
                'created_at' => date('Y-m-d H:i:s'),
                'is_archived' => 0
            ]);
            $project_ids[$key] = Database::getConnection()->lastInsertId();
            echo "   ✅ Created $key - $name\n";
        }
    }
    
    echo "\n";
    
    // ====== CREATE ISSUES ======
    echo "📝 Creating 50+ test issues...\n\n";
    
    $issue_counter = 1;
    $all_issues = [];
    
    // ECOM Issues
    echo "   📂 ECOM Project Issues:\n";
    $ecom_issues = [
        ['ECOM-1', 'Fix critical bug in checkout process', '<p><strong>Critical issue:</strong> Users cannot complete purchases</p><p>The checkout process is <strong>broken</strong></p>', $bug_id, $open_id, 4, -5],
        ['ECOM-2', 'Update user authentication system', '<p>Implement OAuth2.0 authentication</p><ul><li>Google login</li><li>GitHub login</li></ul>', $feature_id, $in_progress_id, 3, -2],
        ['ECOM-3', 'Performance optimization for search', '<p>Optimize queries to reduce response time</p><pre><code>SELECT * FROM products</code></pre>', $task_id, $open_id, 3, -3],
        ['ECOM-4', 'Implement dark mode theme', '<p>Add dark mode to settings</p>', $feature_id, $open_id, 2, 0],
        ['ECOM-5', 'Database migration for schema v2', '<p>Migrate existing data</p><ol><li>Create tables</li><li>Migrate</li></ol>', $task_id, $in_progress_id, 3, 1],
        ['ECOM-6', 'Write API documentation', '<p>Complete API docs</p>', $task_id, $open_id, 2, 3],
        ['ECOM-7', 'Implement push notifications', '<p>Add push support</p>', $feature_id, $open_id, 2, 7],
        ['ECOM-8', 'Refactor authentication middleware', '<p>Improve JWT handling</p>', $improvement_id, $open_id, 1, 10],
        ['ECOM-9', 'Fix responsive design on mobile', '<p>Fix CSS breakpoints</p>', $bug_id, $done_id, 3, -10],
        ['ECOM-10', 'Review and optimize database indexes', '<p>Analyze and optimize</p>', $task_id, $done_id, 2, -7],
    ];
    
    foreach ($ecom_issues as $idx => $data) {
        $dueDate = date('Y-m-d', strtotime("+{$data[6]} days"));
        $startDate = date('Y-m-d', strtotime($dueDate . ' -7 days'));
        
        $exists = Database::selectOne("SELECT id FROM issues WHERE issue_key = ?", [$data[0]]);
        if ($exists) {
            echo "      ⚠️  {$data[0]} already exists\n";
            $all_issues[] = $exists['id'];
        } else {
            Database::insert('issues', [
                'project_id' => $project_ids['ECOM'],
                'issue_key' => $data[0],
                'issue_number' => $idx + 1,
                'summary' => $data[1],
                'description' => $data[2],
                'issue_type_id' => $data[3],
                'status_id' => $data[4],
                'priority_id' => $data[5],
                'assignee_id' => $user1_id,
                'reporter_id' => $admin_id,
                'due_date' => $dueDate,
                'story_points' => rand(1, 13),
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            $issue_id = Database::getConnection()->lastInsertId();
            $all_issues[] = $issue_id;
            echo "      ✅ {$data[0]}: {$data[1]}\n";
        }
    }
    
    echo "\n   📂 MOB Project Issues:\n";
    $mob_issues = [
        ['MOB-1', 'Fix crash on app startup', '<p><strong>Critical:</strong> App crashes on iOS 14+</p>', $bug_id, $open_id, 4, -4],
        ['MOB-2', 'Implement offline mode', '<p>Allow offline usage</p>', $feature_id, $in_progress_id, 3, -1],
        ['MOB-3', 'Update UI to new design', '<p>New design system</p>', $task_id, $open_id, 3, 2],
        ['MOB-4', 'Add biometric authentication', '<p>Face/Touch ID support</p>', $feature_id, $open_id, 2, 14],
        ['MOB-5', 'Fix memory leaks', '<p>Memory usage increasing</p>', $bug_id, $done_id, 3, -8],
    ];
    
    foreach ($mob_issues as $idx => $data) {
        $dueDate = date('Y-m-d', strtotime("+{$data[6]} days"));
        $startDate = date('Y-m-d', strtotime($dueDate . ' -7 days'));
        
        $exists = Database::selectOne("SELECT id FROM issues WHERE issue_key = ?", [$data[0]]);
        if ($exists) {
            echo "      ⚠️  {$data[0]} already exists\n";
            $all_issues[] = $exists['id'];
        } else {
            Database::insert('issues', [
                'project_id' => $project_ids['MOB'],
                'issue_key' => $data[0],
                'issue_number' => $idx + 1,
                'summary' => $data[1],
                'description' => $data[2],
                'issue_type_id' => $data[3],
                'status_id' => $data[4],
                'priority_id' => $data[5],
                'assignee_id' => $user2_id,
                'reporter_id' => $admin_id,
                'due_date' => $dueDate,
                'story_points' => rand(1, 13),
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            $issue_id = Database::getConnection()->lastInsertId();
            $all_issues[] = $issue_id;
            echo "      ✅ {$data[0]}: {$data[1]}\n";
        }
    }
    
    echo "\n   📂 API Project Issues:\n";
    $api_issues = [
        ['API-1', 'Rate limiting not working', '<p>Users exceed limits</p>', $bug_id, $open_id, 3, -3],
        ['API-2', 'Implement GraphQL endpoint', '<p>Add GraphQL support</p>', $feature_id, $open_id, 2, 5],
        ['API-3', 'Add webhook support', '<p>Event subscriptions</p>', $feature_id, $done_id, 2, -6],
        ['API-4', 'Improve response time', '<p>Response < 100ms</p>', $improvement_id, $open_id, 2, 21],
    ];
    
    foreach ($api_issues as $idx => $data) {
        $dueDate = date('Y-m-d', strtotime("+{$data[6]} days"));
        $startDate = date('Y-m-d', strtotime($dueDate . ' -7 days'));
        
        $exists = Database::selectOne("SELECT id FROM issues WHERE issue_key = ?", [$data[0]]);
        if ($exists) {
            echo "      ⚠️  {$data[0]} already exists\n";
            $all_issues[] = $exists['id'];
        } else {
            Database::insert('issues', [
                'project_id' => $project_ids['API'],
                'issue_key' => $data[0],
                'issue_number' => $idx + 1,
                'summary' => $data[1],
                'description' => $data[2],
                'issue_type_id' => $data[3],
                'status_id' => $data[4],
                'priority_id' => $data[5],
                'assignee_id' => $user3_id,
                'reporter_id' => $admin_id,
                'due_date' => $dueDate,
                'story_points' => rand(1, 13),
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            $issue_id = Database::getConnection()->lastInsertId();
            $all_issues[] = $issue_id;
            echo "      ✅ {$data[0]}: {$data[1]}\n";
        }
    }
    
    echo "\n   📂 DEVOPS Project Issues:\n";
    $devops_issues = [
        ['DEVOPS-1', 'Upgrade Kubernetes cluster', '<p>Update to latest</p>', $task_id, $in_progress_id, 3, 0],
        ['DEVOPS-2', 'Set up monitoring/alerting', '<p>Prometheus + Grafana</p>', $task_id, $open_id, 3, 4],
        ['DEVOPS-3', 'Improve CI/CD pipeline', '<p>Reduce build time</p>', $improvement_id, $open_id, 2, 12],
    ];
    
    foreach ($devops_issues as $idx => $data) {
        $dueDate = date('Y-m-d', strtotime("+{$data[6]} days"));
        $startDate = date('Y-m-d', strtotime($dueDate . ' -7 days'));
        
        $exists = Database::selectOne("SELECT id FROM issues WHERE issue_key = ?", [$data[0]]);
        if ($exists) {
            echo "      ⚠️  {$data[0]} already exists\n";
            $all_issues[] = $exists['id'];
        } else {
            Database::insert('issues', [
                'project_id' => $project_ids['DEVOPS'],
                'issue_key' => $data[0],
                'issue_number' => $idx + 1,
                'summary' => $data[1],
                'description' => $data[2],
                'issue_type_id' => $data[3],
                'status_id' => $data[4],
                'priority_id' => $data[5],
                'assignee_id' => $user1_id,
                'reporter_id' => $admin_id,
                'due_date' => $dueDate,
                'story_points' => rand(1, 13),
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            $issue_id = Database::getConnection()->lastInsertId();
            $all_issues[] = $issue_id;
            echo "      ✅ {$data[0]}: {$data[1]}\n";
        }
    }
    
    echo "\n   📂 QA Project Issues:\n";
    $qa_issues = [
        ['QA-1', 'Create automated test suite', '<p>100% coverage</p>', $task_id, $in_progress_id, 3, 7],
        ['QA-2', 'Test authentication on all devices', '<p>Test all browsers</p>', $task_id, $open_id, 3, 2],
        ['QA-3', 'Update test documentation', '<p>Document all tests</p>', $task_id, $done_id, 1, -5],
    ];
    
    foreach ($qa_issues as $idx => $data) {
        $dueDate = date('Y-m-d', strtotime("+{$data[6]} days"));
        $startDate = date('Y-m-d', strtotime($dueDate . ' -7 days'));
        
        $exists = Database::selectOne("SELECT id FROM issues WHERE issue_key = ?", [$data[0]]);
        if ($exists) {
            echo "      ⚠️  {$data[0]} already exists\n";
            $all_issues[] = $exists['id'];
        } else {
            Database::insert('issues', [
            'project_id' => $project_ids['QA'],
            'issue_key' => $data[0],
            'issue_number' => $idx + 1,
            'summary' => $data[1],
            'description' => $data[2],
            'issue_type_id' => $data[3],
            'status_id' => $data[4],
            'priority_id' => $data[5],
            'assignee_id' => $user2_id,
            'reporter_id' => $admin_id,
            'due_date' => $dueDate,
            'story_points' => rand(1, 13),
            'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $issue_id = Database::getConnection()->lastInsertId();
        $all_issues[] = $issue_id;
        echo "      ✅ {$data[0]}: {$data[1]}\n";
        }
        }
        
        echo "\n";
    
    // ====== ADD COMMENTS ======
    echo "💬 Adding comments to issues...\n";
    $comments = [
        'This needs to be reviewed before deployment.',
        'Great progress on this! Keep up the good work.',
        'I think we should discuss this in the next standup.',
        'This is blocking several other tasks.',
        'Ready for testing in staging environment.',
        'Can we prioritize this? It is causing issues for users.',
        'Waiting for design review before implementation.',
        'All tests passing, ready for production.',
    ];
    
    $comment_count = 0;
    foreach ($all_issues as $issue_id) {
        if (rand(1, 100) > 40) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                Database::insert('comments', [
                    'issue_id' => $issue_id,
                    'user_id' => [$user1_id, $user2_id, $user3_id][rand(0, 2)],
                    'body' => $comments[array_rand($comments)],
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 7) . ' days')),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $comment_count++;
            }
        }
    }
    echo "   ✅ Added $comment_count comments\n\n";
    
    // ====== ADD WORK LOGS ======
     echo "⏱️  Adding work logs...\n";
     $worklog_count = 0;
     foreach ($all_issues as $issue_id) {
         if (rand(1, 100) > 50) {
             for ($i = 0; $i < rand(1, 3); $i++) {
                 $hours = rand(1, 8);
                 Database::insert('worklogs', [
                     'issue_id' => $issue_id,
                     'user_id' => [$user1_id, $user2_id, $user3_id][rand(0, 2)],
                     'time_spent' => $hours * 3600, // Convert hours to seconds
                     'description' => 'Working on the task',
                     'started_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 7) . ' days')),
                     'created_at' => date('Y-m-d H:i:s'),
                     'updated_at' => date('Y-m-d H:i:s'),
                 ]);
                 $worklog_count++;
             }
         }
     }
     echo "   ✅ Added $worklog_count work logs\n\n";
    
    // ====== STATISTICS ======
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 SEEDING COMPLETE - SUMMARY\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $projectCount = Database::selectValue("SELECT COUNT(*) FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')");
    $issueCount = Database::selectValue("SELECT COUNT(*) FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA'))");
    $overdueCount = Database::selectValue("SELECT COUNT(*) FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')) AND due_date < CURDATE() AND status_id != (SELECT id FROM statuses WHERE name = 'Done' LIMIT 1)");
    $openCount = Database::selectValue("SELECT COUNT(*) FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')) AND status_id = (SELECT id FROM statuses WHERE name = 'Open' LIMIT 1)");
    $inProgressCount = Database::selectValue("SELECT COUNT(*) FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')) AND status_id = (SELECT id FROM statuses WHERE name = 'In Progress' LIMIT 1)");
    $doneCount = Database::selectValue("SELECT COUNT(*) FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')) AND status_id = (SELECT id FROM statuses WHERE name = 'Done' LIMIT 1)");
    
    echo "✅ Projects Created: $projectCount\n";
    echo "✅ Total Issues: $issueCount\n";
    echo "   • Open: $openCount\n";
    echo "   • In Progress: $inProgressCount\n";
    echo "   • Done: $doneCount\n";
    echo "✅ Overdue Issues: $overdueCount\n";
    echo "✅ Comments Added: $comment_count\n";
    echo "✅ Work Logs Added: $worklog_count\n\n";
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "🎯 TEST SCENARIOS AVAILABLE:\n";
    echo "   ✅ Overdue issues (5, 3, 2 days ago)\n";
    echo "   ✅ Due soon (today, tomorrow, 3 days)\n";
    echo "   ✅ Future planning (7-21 days ahead)\n";
    echo "   ✅ Completed issues\n";
    echo "   ✅ Various priorities (Urgent, High, Medium, Low)\n";
    echo "   ✅ Different assignees\n";
    echo "   ✅ Formatted descriptions (bold, lists, code)\n";
    echo "   ✅ Comments and discussions\n";
    echo "   ✅ Time tracking and work logs\n\n";
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "<br><br><a href='/' style='display: inline-block; padding: 12px 24px; background: #8B1956; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;'>✅ Go to Dashboard to View Data</a>";
    echo "</pre>";
    
} catch (\Exception $e) {
    echo "<h1>❌ Error</h1>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
    exit(1);
}
?>
