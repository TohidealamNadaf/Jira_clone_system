<?php
$output = "";
$mysqli = new mysqli("localhost", "root", "", "cways_prod");

if ($mysqli->connect_errno) {
    $output .= "Failed to connect to MySQL: " . $mysqli->connect_error;
    file_put_contents('create_log.txt', $output);
    exit;
}

// 1. Get Project ID
$res = $mysqli->query("SELECT id FROM projects WHERE `key` = 'CWAYSMIS'");
$project = $res->fetch_assoc();
if (!$project) {
    $output .= "Project CWAYSMIS not found.";
    file_put_contents('create_log.txt', $output);
    exit;
}
$projectId = $project['id'];

// 2. Check if Kanban board already exists (to avoid duplicates if re-run)
$res = $mysqli->query("SELECT id FROM boards WHERE project_id = $projectId AND type = 'kanban' AND name = 'CWays MIS Kanban Board'");
if ($res->num_rows > 0) {
    $output .= "Kanban board already exists.\n";
    file_put_contents('create_log.txt', $output);
    exit;
}

// 3. Create Board
$stmt = $mysqli->prepare("INSERT INTO boards (project_id, name, type, is_private, created_at, updated_at) VALUES (?, ?, 'kanban', 0, NOW(), NOW())");
$boardName = "CWays MIS Kanban Board";
$stmt->bind_param("is", $projectId, $boardName);
$stmt->execute();
$boardId = $stmt->insert_id;
$output .= "Created Board ID: $boardId\n";

// 4. Get Statuses for Columns
$statuses = [];
$res = $mysqli->query("SELECT id, name, category FROM statuses ORDER BY sort_order ASC");
while ($row = $res->fetch_assoc()) {
    $statuses[] = $row;
}

// 5. Define Columns mapping
$columnDefinitions = [
    'todo' => ['name' => 'To Do', 'statuses' => []],
    'in_progress' => ['name' => 'In Progress', 'statuses' => []],
    'done' => ['name' => 'Done', 'statuses' => []],
];

foreach ($statuses as $status) {
    if (isset($columnDefinitions[$status['category']])) {
        $columnDefinitions[$status['category']]['statuses'][] = $status['id'];
    }
}

// 6. Insert Columns
$order = 0;
$stmtCol = $mysqli->prepare("INSERT INTO board_columns (board_id, name, status_ids, sort_order) VALUES (?, ?, ?, ?)");

foreach ($columnDefinitions as $colDef) {
    $statusJson = json_encode($colDef['statuses']);
    $colName = $colDef['name'];
    $stmtCol->bind_param("issi", $boardId, $colName, $statusJson, $order);
    $stmtCol->execute();
    $order++;
}

$output .= "Created default columns for Board $boardId.\n";
file_put_contents('create_log.txt', $output);

$mysqli->close();
