<?php
$mysqli = new mysqli("localhost", "root", "", "cways_prod");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$result = $mysqli->query("
    SELECT b.id, b.name, b.type, p.key as project_key 
    FROM boards b 
    JOIN projects p ON b.project_id = p.id
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    $result->free();
} else {
    echo "Error: " . $mysqli->error;
}

$mysqli->close();
