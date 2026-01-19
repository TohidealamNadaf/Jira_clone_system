<?php
file_put_contents('verify_status.txt', 'STARTING');
$mysqli = new mysqli("localhost", "root", "", "cways_prod");
$res = $mysqli->query("SELECT id FROM boards WHERE name = 'CWays MIS Kanban Board'");
if ($res->num_rows > 0) {
    file_put_contents('verify_status.txt', 'EXISTS');
} else {
    file_put_contents('verify_status.txt', 'MISSING');
}
$mysqli->close();
