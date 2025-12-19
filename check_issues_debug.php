<?php
declare(strict_types=1);

// Minimal database check
require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;

// Check if issues table has data
$count = Database::selectValue("SELECT COUNT(*) FROM issues");
echo "Total issues in database: $count\n";

// Check by project
$byProject = Database::select("SELECT project_id, COUNT(*) as count FROM issues GROUP BY project_id");
echo "\nIssues by project:\n";
foreach ($byProject as $row) {
    echo "  Project {$row['project_id']}: {$row['count']} issues\n";
}

// Check project 1 specifically
$project1Issues = Database::select("SELECT id, `key`, summary FROM issues WHERE project_id = 1 LIMIT 5");
echo "\nFirst 5 issues in Project 1:\n";
if (empty($project1Issues)) {
    echo "  NO ISSUES FOUND\n";
} else {
    foreach ($project1Issues as $issue) {
        echo "  - {$issue['key']}: {$issue['summary']}\n";
    }
}

// Check if the issues dropdown query works
$test = Database::select("
    SELECT i.id, i.`key`, i.summary, p.id as project_id
    FROM issues i
    JOIN projects p ON i.project_id = p.id
    WHERE p.id = 1
    ORDER BY i.`key` ASC
    LIMIT 10
");

echo "\nDirect SQL test for Project 1:\n";
if (empty($test)) {
    echo "  NO RESULTS\n";
} else {
    echo "  Found " . count($test) . " issues\n";
    foreach ($test as $issue) {
        echo "    - {$issue['key']}: {$issue['summary']}\n";
    }
}
?>
