<?php
require_once 'bootstrap/app.php';
use App\Core\Database;

echo "Seeding permissions and role permissions...\n";

// Permissions from seed.sql
$permissions = [
    ['View Issues', 'view_issues', 'View project issues', 'issues'],
    ['Create Issue', 'create_issue', 'Create new issues', 'issues'],
    ['Edit Issue', 'edit_issue', 'Edit existing issues', 'issues'],
    ['Delete Issue', 'delete_issue', 'Delete issues', 'issues'],
    ['View Projects', 'view_projects', 'View projects', 'projects'],
    ['Create Project', 'create_project', 'Create new projects', 'projects'],
    ['Edit Project', 'edit_project', 'Edit project settings', 'projects'],
    ['Manage Board', 'manage_board', 'Manage project boards', 'projects'],
    ['Manage Members', 'manage_members', 'Manage project members', 'projects'],
    ['Manage Sprints', 'manage_sprints', 'Create and manage sprints', 'sprints'],
    ['View Reports', 'view_reports', 'View project reports', 'reports'],
    ['Manage Users', 'manage_users', 'Manage user accounts', 'admin'],
    ['Manage Roles', 'manage_roles', 'Manage roles and permissions', 'admin'],
];

foreach ($permissions as $perm) {
    Database::insert('permissions', [
        'name' => $perm[0],
        'slug' => $perm[1],
        'description' => $perm[2],
        'category' => $perm[3],
    ]);
}

echo "Permissions seeded: " . count($permissions) . "\n";

// Role permissions
// Administrator: All permissions (role_id = 1)
for ($i = 1; $i <= count($permissions); $i++) {
    Database::insert('role_permissions', [
        'role_id' => 1,
        'permission_id' => $i,
    ]);
}

// Developer: Issue management + project viewing (role_id = 2)
$devPerms = [1, 2, 3, 5, 7, 8, 10]; // view_issues, create_issue, edit_issue, view_projects, edit_project, manage_board, view_reports
foreach ($devPerms as $permId) {
    Database::insert('role_permissions', [
        'role_id' => 2,
        'permission_id' => $permId,
    ]);
}

// Project Manager: Full project management + issues (role_id = 3)
for ($i = 1; $i <= count($permissions); $i++) {
    Database::insert('role_permissions', [
        'role_id' => 3,
        'permission_id' => $i,
    ]);
}

// QA Tester: Issue management + testing focused (role_id = 4)
$qaPerms = [1, 2, 3, 5, 7, 8, 10]; // view_issues, create_issue, edit_issue, view_projects, edit_project, manage_board, view_reports
foreach ($qaPerms as $permId) {
    Database::insert('role_permissions', [
        'role_id' => 4,
        'permission_id' => $permId,
    ]);
}

// Viewer: Read-only access (role_id = 5)
$viewerPerms = [1, 5, 10]; // view_issues, view_projects, view_reports
foreach ($viewerPerms as $permId) {
    Database::insert('role_permissions', [
        'role_id' => 5,
        'permission_id' => $permId,
    ]);
}

// Product Owner: Similar to Project Manager but focused on requirements (role_id = 6)
$poPerms = [1, 2, 3, 5, 7, 8, 9, 10]; // view_issues, create_issue, edit_issue, view_projects, edit_project, manage_board, manage_members, view_reports
foreach ($poPerms as $permId) {
    Database::insert('role_permissions', [
        'role_id' => 6,
        'permission_id' => $permId,
    ]);
}

echo "Role permissions seeded successfully!\n";
?>

