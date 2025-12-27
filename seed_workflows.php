<?php
require_once 'bootstrap/app.php';
use App\Core\Database;

echo "Seeding workflows and workflow statuses...\n";

// Seed workflows
Database::insert('workflows', [
    'name' => 'Standard Workflow',
    'description' => 'Default workflow for most projects',
    'is_active' => 1,
    'is_default' => 1,
]);

Database::insert('workflows', [
    'name' => 'Agile Workflow',
    'description' => 'Workflow optimized for agile teams',
    'is_active' => 1,
    'is_default' => 0,
]);

Database::insert('workflows', [
    'name' => 'Kanban Workflow',
    'description' => 'Simplified workflow for Kanban boards',
    'is_active' => 1,
    'is_default' => 0,
]);

echo "✅ Workflows seeded\n";

// Seed workflow statuses for Standard Workflow (ID: 1)
$standardWorkflowStatuses = [
    [1, 1, 1, 0, 0],     // Open (initial)
    [1, 2, 0, 100, 0],   // To Do
    [1, 3, 0, 200, 0],   // In Progress
    [1, 4, 0, 300, 0],   // In Review
    [1, 5, 0, 400, 0],   // Testing
    [1, 6, 0, 500, 0],   // Done
    [1, 7, 0, 600, 0],   // Closed
];

foreach ($standardWorkflowStatuses as $ws) {
    Database::insert('workflow_statuses', [
        'workflow_id' => $ws[0],
        'status_id' => $ws[1],
        'is_initial' => $ws[2],
        'x_position' => $ws[3],
        'y_position' => $ws[4],
    ]);
}

echo "✅ Workflow statuses seeded\n";
echo "Workflow seeding completed successfully!\n";
?>





