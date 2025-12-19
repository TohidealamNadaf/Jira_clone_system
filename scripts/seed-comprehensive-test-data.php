<?php
declare(strict_types=1);

/**
 * Comprehensive Test Data Seeder
 * Creates multiple projects, issues, and test data to fully test the system
 * 
 * Usage: php scripts/seed-comprehensive-test-data.php
 */

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Core\Database;

class ComprehensiveTestDataSeeder
{
    private Database $db;
    private array $users = [];
    private array $projects = [];
    private array $issueTypes = [];
    private array $statuses = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        echo "🌱 Comprehensive Test Data Seeder Started\n";
        echo "==========================================\n\n";
    }

    public function run(): void
    {
        try {
            $this->loadUsers();
            $this->loadIssueTypes();
            $this->loadStatuses();
            $this->createProjects();
            $this->createIssuesForEachProject();
            $this->linkSomeIssues();
            $this->addComments();
            $this->addWorkLogs();
            
            echo "\n✅ Test data seeding completed successfully!\n";
            echo "==========================================\n";
            $this->displaySummary();
        } catch (\Exception $e) {
            echo "\n❌ Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function loadUsers(): void
    {
        echo "📋 Loading users...\n";
        $users = $this->db->select("SELECT id, display_name, email FROM users LIMIT 10");
        
        foreach ($users as $user) {
            $this->users[$user['id']] = $user;
        }
        
        echo "✅ Loaded " . count($this->users) . " users\n\n";
    }

    private function loadIssueTypes(): void
    {
        echo "📋 Loading issue types...\n";
        $types = $this->db->select("SELECT id, name FROM issue_types ORDER BY name");
        
        foreach ($types as $type) {
            $this->issueTypes[$type['name']] = $type['id'];
        }
        
        echo "✅ Loaded " . count($this->issueTypes) . " issue types\n\n";
    }

    private function loadStatuses(): void
    {
        echo "📋 Loading statuses...\n";
        $statuses = $this->db->select("SELECT id, name FROM statuses ORDER BY name");
        
        foreach ($statuses as $status) {
            $this->statuses[$status['name']] = $status['id'];
        }
        
        echo "✅ Loaded " . count($this->statuses) . " statuses\n\n";
    }

    private function createProjects(): void
    {
        echo "🚀 Creating test projects...\n";

        $projectsData = [
            [
                'name' => 'E-Commerce Platform',
                'key' => 'ECOM',
                'description' => 'Main e-commerce platform project for online retail business',
                'category' => 'Product'
            ],
            [
                'name' => 'Mobile App',
                'key' => 'MOB',
                'description' => 'iOS and Android mobile application development',
                'category' => 'Product'
            ],
            [
                'name' => 'Backend API',
                'key' => 'API',
                'description' => 'RESTful API development and maintenance',
                'category' => 'Product'
            ],
            [
                'name' => 'DevOps Infrastructure',
                'key' => 'DEVOPS',
                'description' => 'Cloud infrastructure, CI/CD pipelines, and deployment',
                'category' => 'Infrastructure'
            ],
            [
                'name' => 'QA & Testing',
                'key' => 'QA',
                'description' => 'Quality assurance and automated testing framework',
                'category' => 'QA'
            ],
        ];

        foreach ($projectsData as $data) {
            $exists = $this->db->selectOne(
                "SELECT id FROM projects WHERE `key` = ?",
                [$data['key']]
            );

            if ($exists) {
                echo "   ⚠️ Project {$data['key']} already exists, skipping...\n";
                $this->projects[$data['key']] = $exists['id'];
                continue;
            }

            $this->db->insert('projects', [
                'name' => $data['name'],
                'key' => $data['key'],
                'description' => $data['description'],
                'category_id' => 1,
                'lead_id' => array_key_first($this->users),
                'created_by' => array_key_first($this->users),
                'created_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ]);

            $projectId = $this->db->lastInsertId();
            $this->projects[$data['key']] = $projectId;
            
            echo "   ✅ Created project: {$data['key']} - {$data['name']}\n";
        }

        echo "\n";
    }

    private function createIssuesForEachProject(): void
    {
        echo "📝 Creating test issues for each project...\n\n";

        $priorities = ['Low', 'Medium', 'High', 'Urgent'];
        $issueNum = 1;

        foreach ($this->projects as $projectKey => $projectId) {
            echo "   📂 Creating issues for project: {$projectKey}\n";

            // Get random users for assignment
            $userIds = array_keys($this->users);
            $userCount = count($userIds);

            // Create various issues with different statuses, priorities, and due dates
            $issuesData = [
                // Overdue issues (past due dates)
                [
                    'summary' => 'Fix critical bug in checkout process',
                    'description' => '<p><strong>Critical issue:</strong> Users cannot complete purchases</p><p>The checkout process is <strong>broken</strong> and needs <em>immediate</em> attention.</p>',
                    'type' => 'Bug',
                    'status' => 'Open',
                    'priority' => 'Urgent',
                    'daysFromNow' => -5,  // 5 days overdue
                ],
                [
                    'summary' => 'Update user authentication system',
                    'description' => '<p>Implement OAuth2.0 authentication</p><ul><li>Support Google login</li><li>Support GitHub login</li><li>Add two-factor authentication</li></ul>',
                    'type' => 'Feature',
                    'status' => 'In Progress',
                    'priority' => 'High',
                    'daysFromNow' => -2,  // 2 days overdue
                ],
                [
                    'summary' => 'Performance optimization for search',
                    'description' => '<p>Optimize search queries to reduce response time below 100ms</p><pre><code>SELECT * FROM products WHERE name LIKE ?</code></pre>',
                    'type' => 'Task',
                    'status' => 'Open',
                    'priority' => 'High',
                    'daysFromNow' => -3,  // 3 days overdue
                ],

                // Due soon (today to 3 days)
                [
                    'summary' => 'Implement dark mode theme',
                    'description' => '<p>Add dark mode toggle to user settings</p><blockquote>This is a frequently requested feature from users</blockquote>',
                    'type' => 'Feature',
                    'status' => 'Open',
                    'priority' => 'Medium',
                    'daysFromNow' => 0,  // Due today
                ],
                [
                    'summary' => 'Database migration for schema v2',
                    'description' => '<p>Migrate existing data to new schema</p><ol><li>Create new tables</li><li>Migrate data</li><li>Validate migration</li><li>Drop old tables</li></ol>',
                    'type' => 'Task',
                    'status' => 'In Progress',
                    'priority' => 'High',
                    'daysFromNow' => 1,  // Due tomorrow
                ],
                [
                    'summary' => 'Write API documentation',
                    'description' => '<p>Complete <strong>API documentation</strong> for all endpoints</p>',
                    'type' => 'Task',
                    'status' => 'Open',
                    'priority' => 'Medium',
                    'daysFromNow' => 3,  // Due in 3 days
                ],

                // Future issues (1-2 weeks)
                [
                    'summary' => 'Implement push notifications',
                    'description' => '<p>Add push notification support for mobile app</p><ul><li>Firebase Cloud Messaging</li><li>Local notifications</li><li>Notification center UI</li></ul>',
                    'type' => 'Feature',
                    'status' => 'Open',
                    'priority' => 'Medium',
                    'daysFromNow' => 7,  // Due in 1 week
                ],
                [
                    'summary' => 'Refactor API authentication middleware',
                    'description' => '<p>Improve JWT token handling and refresh logic</p>',
                    'type' => 'Improvement',
                    'status' => 'Open',
                    'priority' => 'Low',
                    'daysFromNow' => 10,  // Due in 10 days
                ],
                [
                    'summary' => 'Add multi-language support',
                    'description' => '<p>Implement internationalization (i18n) for Spanish and French</p>',
                    'type' => 'Feature',
                    'status' => 'Open',
                    'priority' => 'Low',
                    'daysFromNow' => 14,  // Due in 2 weeks
                ],

                // Completed issues
                [
                    'summary' => 'Fix responsive design on mobile',
                    'description' => '<p>CSS breakpoints are broken on screens < 768px</p>',
                    'type' => 'Bug',
                    'status' => 'Done',
                    'priority' => 'High',
                    'daysFromNow' => -10,  // Completed
                ],
                [
                    'summary' => 'Review and optimize database indexes',
                    'description' => '<p>Analyze slow queries and add missing indexes</p>',
                    'type' => 'Task',
                    'status' => 'Done',
                    'priority' => 'Medium',
                    'daysFromNow' => -7,  // Completed
                ],
            ];

            foreach ($issuesData as $data) {
                $assigneeId = $userIds[rand(0, $userCount - 1)];
                $typeId = $this->issueTypes[$data['type']] ?? 1;
                $statusId = $this->statuses[$data['status']] ?? 1;

                // Calculate due date
                $dueDate = date('Y-m-d', strtotime("+{$data['daysFromNow']} days"));

                // Calculate start date (one week before due date)
                $startDate = date('Y-m-d', strtotime($dueDate . ' -7 days'));

                $this->db->insert('issues', [
                    'project_id' => $projectId,
                    'issue_key' => "{$projectKey}-{$issueNum}",
                    'summary' => $data['summary'],
                    'description' => $data['description'],
                    'issue_type_id' => $typeId,
                    'status_id' => $statusId,
                    'priority_id' => 4 - array_search($data['priority'], $priorities, true),
                    'assignee_id' => $assigneeId,
                    'reporter_id' => $userIds[rand(0, $userCount - 1)],
                    'due_date' => $dueDate,
                    'start_date' => $startDate,
                    'end_date' => $data['status'] === 'Done' ? date('Y-m-d') : null,
                    'story_points' => rand(1, 13),
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $issueNum++;
                echo "      ✅ {$projectKey}-{$issueNum-1}: {$data['summary']}\n";
            }

            echo "\n";
        }

        echo "✅ Created " . ($issueNum - 1) . " test issues\n\n";
    }

    private function linkSomeIssues(): void
    {
        echo "🔗 Linking related issues...\n";

        // Get some issues to link
        $issues = $this->db->select("SELECT id, issue_key FROM issues LIMIT 5");

        if (count($issues) >= 2) {
            for ($i = 0; $i < count($issues) - 1; $i++) {
                $this->db->insert('issue_links', [
                    'from_issue_id' => $issues[$i]['id'],
                    'to_issue_id' => $issues[$i + 1]['id'],
                    'link_type' => 'relates_to',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                echo "   ✅ Linked {$issues[$i]['issue_key']} → {$issues[$i+1]['issue_key']}\n";
            }
        }

        echo "\n";
    }

    private function addComments(): void
    {
        echo "💬 Adding comments to issues...\n";

        // Get some issues
        $issues = $this->db->select("SELECT id, issue_key FROM issues LIMIT 10");
        $userIds = array_keys($this->users);

        $comments = [
            'This needs to be reviewed before deployment.',
            'Great progress on this! Keep up the good work.',
            'I think we should discuss this in the next standup.',
            'This is blocking several other tasks.',
            'Ready for testing in staging environment.',
            'Can we prioritize this? It\'s causing issues for users.',
            'Waiting for design review before implementation.',
            'All tests passing, ready for production.',
        ];

        foreach ($issues as $issue) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $comment = $comments[array_rand($comments)];
                
                $this->db->insert('comments', [
                    'issue_id' => $issue['id'],
                    'user_id' => $userIds[rand(0, count($userIds) - 1)],
                    'comment' => $comment,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 7) . ' days')),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                echo "   ✅ Added comment to {$issue['issue_key']}\n";
            }
        }

        echo "\n";
    }

    private function addWorkLogs(): void
    {
        echo "⏱️ Adding work logs...\n";

        // Get issues with "In Progress" or "Done" status
        $issues = $this->db->select(
            "SELECT i.id, i.issue_key, i.assignee_id 
             FROM issues i 
             JOIN statuses s ON i.status_id = s.id 
             WHERE s.name IN ('In Progress', 'Done') 
             LIMIT 8"
        );

        $userIds = array_keys($this->users);

        foreach ($issues as $issue) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $this->db->insert('worklogs', [
                    'issue_id' => $issue['id'],
                    'user_id' => $issue['assignee_id'] ?? $userIds[0],
                    'hours_logged' => rand(1, 8),
                    'description' => 'Working on the task',
                    'logged_date' => date('Y-m-d', strtotime('-' . rand(1, 7) . ' days')),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                echo "   ✅ Added work log to {$issue['issue_key']}\n";
            }
        }

        echo "\n";
    }

    private function displaySummary(): void
    {
        echo "\n📊 SUMMARY:\n";
        echo "==========================================\n";

        $projectCount = $this->db->selectValue("SELECT COUNT(*) FROM projects");
        $issueCount = $this->db->selectValue("SELECT COUNT(*) FROM issues");
        $overdueCount = $this->db->selectValue("SELECT COUNT(*) FROM issues WHERE due_date < CURDATE() AND status_id != (SELECT id FROM statuses WHERE name = 'Done')");
        $dueCount = $this->db->selectValue("SELECT COUNT(*) FROM issues WHERE due_date = CURDATE()");
        $inProgressCount = $this->db->selectValue("SELECT COUNT(*) FROM issues WHERE status_id = (SELECT id FROM statuses WHERE name = 'In Progress')");
        $openCount = $this->db->selectValue("SELECT COUNT(*) FROM issues WHERE status_id = (SELECT id FROM statuses WHERE name = 'Open')");
        $doneCount = $this->db->selectValue("SELECT COUNT(*) FROM issues WHERE status_id = (SELECT id FROM statuses WHERE name = 'Done')");
        $commentCount = $this->db->selectValue("SELECT COUNT(*) FROM comments");
        $worklogCount = $this->db->selectValue("SELECT COUNT(*) FROM worklogs");

        echo "\n📁 Projects Created: $projectCount\n";
        echo "📋 Total Issues: $issueCount\n";
        echo "   🔴 Open: $openCount\n";
        echo "   🟡 In Progress: $inProgressCount\n";
        echo "   ✅ Done: $doneCount\n";
        echo "\n⏰ Due Dates:\n";
        echo "   🚨 Overdue: $overdueCount\n";
        echo "   ⚡ Due Today: $dueCount\n";
        echo "\n💬 Comments: $commentCount\n";
        echo "⏱️  Work Logs: $worklogCount\n";
        echo "\n==========================================\n";

        echo "\n🎯 Test Scenarios:\n";
        echo "   ✅ Overdue issues - Check dashboard for overdue tasks\n";
        echo "   ✅ Due soon - View issues due in next 3 days\n";
        echo "   ✅ Future planning - See long-term roadmap\n";
        echo "   ✅ Progress tracking - View in-progress work\n";
        echo "   ✅ Completed items - Check done items\n";
        echo "   ✅ Various priorities - Filter by priority\n";
        echo "   ✅ Different assignees - See team workload\n";
        echo "   ✅ Comments & discussions - Check collaboration\n";
        echo "   ✅ Time tracking - View logged hours\n";
        echo "   ✅ Issue linking - See related issues\n";
        echo "\n==========================================\n";
    }
}

// Run the seeder
$seeder = new ComprehensiveTestDataSeeder();
$seeder->run();
