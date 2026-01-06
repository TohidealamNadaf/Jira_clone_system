<?php
// Direct database check without framework
$mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system');

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT id, email, avatar FROM users WHERE avatar IS NOT NULL LIMIT 10");

echo "<h2>Avatar Paths in Database</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Email</th><th>Avatar Path</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td><code>" . htmlspecialchars($row['avatar']) . "</code></td>";
    echo "</tr>";
}

echo "</table>";

$mysqli->close();
?>
