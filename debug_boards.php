<?php
require_once 'bootstrap/app.php';

use App\Core\Database;

$boards = Database::select("SELECT b.id, b.name, b.type, p.key as project_key FROM boards b JOIN projects p ON b.project_id = p.id");
print_r($boards);
