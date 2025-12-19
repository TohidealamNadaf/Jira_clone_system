<?php
/**
 * Verify and seed database if needed
 */

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

// Check issue types
$issueTypesCount = (int) Database::selectValue("SELECT COUNT(*) FROM issue_types");
echo "Issue Types: $issueTypesCount\n";

if ($issueTypesCount == 0) {
    echo "\n❌ No issue types found. Seeding data...\n\n";
    
    // Insert issue types
    Database::insert('issue_types', [
        'name' => 'Epic',
        'description' => 'A large body of work that can be broken down into stories',
        'icon' => 'epic',
        'color' => '#904EE2',
        'is_subtask' => 0,
        'is_default' => 0,
        'sort_order' => 1,
    ]);
    
    Database::insert('issue_types', [
        'name' => 'Story',
        'description' => 'A user-facing feature or requirement',
        'icon' => 'story',
        'color' => '#63BA3C',
        'is_subtask' => 0,
        'is_default' => 1,
        'sort_order' => 2,
    ]);
    
    Database::insert('issue_types', [
        'name' => 'Task',
        'description' => 'A unit of work',
        'icon' => 'task',
        'color' => '#4BADE8',
        'is_subtask' => 0,
        'is_default' => 0,
        'sort_order' => 3,
    ]);
    
    Database::insert('issue_types', [
        'name' => 'Bug',
        'description' => 'A defect or issue to be fixed',
        'icon' => 'bug',
        'color' => '#E5493A',
        'is_subtask' => 0,
        'is_default' => 0,
        'sort_order' => 4,
    ]);
    
    Database::insert('issue_types', [
        'name' => 'Sub-task',
        'description' => 'A smaller piece of work within an issue',
        'icon' => 'subtask',
        'color' => '#4BADE8',
        'is_subtask' => 1,
        'is_default' => 0,
        'sort_order' => 5,
    ]);
    
    echo "✅ Issue types seeded successfully!\n";
}

// Check priorities
$prioritiesCount = (int) Database::selectValue("SELECT COUNT(*) FROM issue_priorities");
echo "Priorities: $prioritiesCount\n";

if ($prioritiesCount == 0) {
    echo "\n❌ No priorities found. Seeding data...\n\n";
    
    Database::insert('issue_priorities', [
        'name' => 'Highest',
        'description' => 'Critical priority - blocks release',
        'icon' => 'highest',
        'color' => '#FF5630',
        'sort_order' => 1,
        'is_default' => 0,
    ]);
    
    Database::insert('issue_priorities', [
        'name' => 'High',
        'description' => 'Major impact on functionality',
        'icon' => 'high',
        'color' => '#FF7452',
        'sort_order' => 2,
        'is_default' => 0,
    ]);
    
    Database::insert('issue_priorities', [
        'name' => 'Medium',
        'description' => 'Normal priority',
        'icon' => 'medium',
        'color' => '#FFAB00',
        'sort_order' => 3,
        'is_default' => 1,
    ]);
    
    Database::insert('issue_priorities', [
        'name' => 'Low',
        'description' => 'Minor issue',
        'icon' => 'low',
        'color' => '#0065FF',
        'sort_order' => 4,
        'is_default' => 0,
    ]);
    
    Database::insert('issue_priorities', [
        'name' => 'Lowest',
        'description' => 'Trivial issue',
        'icon' => 'lowest',
        'color' => '#2684FF',
        'sort_order' => 5,
        'is_default' => 0,
    ]);
    
    echo "✅ Priorities seeded successfully!\n";
}

// Check categories
$categoriesCount = (int) Database::selectValue("SELECT COUNT(*) FROM project_categories");
echo "Project Categories: $categoriesCount\n";

if ($categoriesCount == 0) {
    echo "\n❌ No project categories found. Seeding data...\n\n";
    
    Database::insert('project_categories', [
        'name' => 'Web Development',
        'description' => 'Web application projects',
    ]);
    
    Database::insert('project_categories', [
        'name' => 'Mobile Development',
        'description' => 'Mobile application projects',
    ]);
    
    Database::insert('project_categories', [
        'name' => 'Infrastructure',
        'description' => 'DevOps and infrastructure projects',
    ]);
    
    echo "✅ Project categories seeded successfully!\n";
}

// Check statuses
$statusesCount = (int) Database::selectValue("SELECT COUNT(*) FROM statuses");
echo "Statuses: $statusesCount\n";

if ($statusesCount == 0) {
    echo "\n❌ No statuses found. Seeding data...\n\n";
    
    $statuses = [
        ['Open', 'Issue is open and ready to be worked on', 'todo', '#42526E', 1],
        ['To Do', 'Issue is in the backlog', 'todo', '#42526E', 2],
        ['In Progress', 'Issue is being actively worked on', 'in_progress', '#0052CC', 3],
        ['In Review', 'Issue is being reviewed', 'in_progress', '#FF991F', 4],
        ['Testing', 'Issue is being tested', 'in_progress', '#36B37E', 5],
        ['Done', 'Issue is completed', 'done', '#00875A', 6],
        ['Closed', 'Issue is closed', 'done', '#6B778C', 7],
        ['Reopened', 'Issue was reopened', 'todo', '#FF5630', 8],
    ];
    
    foreach ($statuses as $status) {
        Database::insert('statuses', [
            'name' => $status[0],
            'description' => $status[1],
            'category' => $status[2],
            'color' => $status[3],
            'sort_order' => $status[4],
        ]);
    }
    
    echo "✅ Statuses seeded successfully!\n";
}

echo "\n✅ Database verification complete!\n";
