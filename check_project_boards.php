<?php
require 'bootstrap/autoload.php';

use App\Core\Database;

$db = new Database();

echo "=== PROJECTS AND THEIR BOARDS ===\n\n";

$projects = $db->select('
    SELECT p.id, p.`key`, p.name, COUNT(b.id) as board_count 
    FROM projects p 
    LEFT JOIN boards b ON p.id = b.project_id 
    GROUP BY p.id 
    ORDER BY p.name
');

foreach ($projects as $proj) {
    echo "ID: {$proj['id']}, Key: {$proj['key']}, Name: {$proj['name']}, Boards: {$proj['board_count']}\n";
    
    $boards = $db->select(
        'SELECT id, name, type FROM boards WHERE project_id = ? ORDER BY id',
        [$proj['id']]
    );
    
    if ($boards) {
        foreach ($boards as $board) {
            echo "  -> Board {$board['id']}: {$board['name']} ({$board['type']})\n";
        }
    } else {
        echo "  -> NO BOARDS\n";
    }
    echo "\n";
}
