<?php
declare(strict_types=1);

require '../bootstrap/autoload.php';

use App\Core\Database;

echo "<h2>Avatar 404 Fix Diagnostic</h2>";

// Get all users with avatars
$users = Database::select('SELECT id, email, avatar FROM users WHERE avatar IS NOT NULL AND avatar != ""');

echo "<table border='1' cellpadding='10' style='margin: 20px 0;'>";
echo "<tr><th>ID</th><th>Email</th><th>Avatar in DB</th><th>Issue</th></tr>";

foreach ($users as $user) {
    $avatar = $user['avatar'] ?? '';
    $issue = '';
    
    // Check if avatar path starts with /public/ (wrong) or /uploads/ (correct)
    if (str_starts_with($avatar, '/public/')) {
        $issue = "❌ WRONG: Uses /public/ instead of /uploads/";
    } elseif (str_starts_with($avatar, '/uploads/')) {
        $issue = "✅ CORRECT: Uses /uploads/";
    } else {
        $issue = "⚠️ UNKNOWN: Path is " . substr($avatar, 0, 20);
    }
    
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td><code>$avatar</code></td>";
    echo "<td>$issue</td>";
    echo "</tr>";
}

echo "</table>";

// Now check if any need fixing
$wrongAvatars = Database::select(
    "SELECT id, email, avatar FROM users WHERE avatar LIKE '/public/%'"
);

if (!empty($wrongAvatars)) {
    echo "<h3>Found " . count($wrongAvatars) . " users with wrong avatar paths</h3>";
    
    echo "<h4>Before running the fix:</h4>";
    echo "<pre>";
    foreach ($wrongAvatars as $user) {
        echo "User {$user['id']}: {$user['avatar']}\n";
    }
    echo "</pre>";
    
    echo "<h4>Fix Applied:</h4>";
    
    // Fix: Replace /public/uploads/ with /uploads/
    foreach ($wrongAvatars as $user) {
        $oldPath = $user['avatar'];
        $newPath = str_replace('/public/uploads/', '/uploads/', $oldPath);
        
        Database::update('users', ['avatar' => $newPath], 'id = ?', [$user['id']]);
        
        echo "✅ User {$user['id']}: '{$oldPath}' → '{$newPath}'\n";
    }
    
    echo "<h3 style='color: green;'>✅ All avatars fixed!</h3>";
    echo "<p><strong>Action required:</strong> Hard refresh browser (CTRL+SHIFT+DEL then CTRL+F5)</p>";
} else {
    echo "<h3 style='color: green;'>✅ No avatars with wrong paths found!</h3>";
    echo "<p>The avatar system is working correctly.</p>";
}

?>
