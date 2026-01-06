<?php
/**
 * Fix Avatar Paths in Database
 * 
 * This script fixes any avatar paths that were stored incorrectly as /public/avatars/
 * instead of /uploads/avatars/
 */

// Direct database connection (bypasses config system which may not be available)
try {
    $mysqli = new mysqli('localhost', 'root', '', 'jiira_clonee_system');
    
    if ($mysqli->connect_error) {
        die('<div style="background: #f8d7da; color: #721c24; padding: 20px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;">'
            . '<strong>Database Connection Error:</strong> ' . htmlspecialchars($mysqli->connect_error)
            . '<br><br>Please ensure XAMPP MySQL is running.'
            . '</div>');
    }
} catch (Exception $e) {
    die('<div style="background: #f8d7da; color: #721c24; padding: 20px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;">'
        . '<strong>Connection Error:</strong> ' . htmlspecialchars($e->getMessage())
        . '</div>');
}

echo "<h2 style='color: #8B1956;'>🔧 Avatar Database Path Fix</h2>";

// Check for wrong avatar paths
$result = $mysqli->query("SELECT id, email, avatar FROM users WHERE avatar IS NOT NULL AND avatar LIKE '%/public/avatars/%'");

$wrongAvatars = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $wrongAvatars[] = $row;
    }
}

if (empty($wrongAvatars)) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin: 20px 0;'>";
    echo "<h4 style='color: #155724; margin: 0;'>✅ No incorrect avatar paths found</h4>";
    echo "<p style='margin: 10px 0 0 0; color: #155724;'>Your avatar database is correctly configured.</p>";
    echo "</div>";
    exit;
}

echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px; margin: 20px 0;'>";
echo "<h4 style='color: #856404;'>⚠️ Found " . count($wrongAvatars) . " users with incorrect avatar paths</h4>";
echo "<p style='margin: 0; color: #856404;'>These paths will be corrected automatically.</p>";
echo "</div>";

echo "<h3 style='margin-top: 30px;'>Affected Users:</h3>";
echo "<table style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "  <th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>ID</th>";
echo "  <th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Email</th>";
echo "  <th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Current Path</th>";
echo "  <th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Fixed Path</th>";
echo "</tr>";

foreach ($wrongAvatars as $user) {
    $oldPath = $user['avatar'];
    $newPath = str_replace('/public/avatars/', '/uploads/avatars/', $oldPath);
    
    echo "<tr>";
    echo "  <td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($user['id']) . "</td>";
    echo "  <td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($user['email']) . "</td>";
    echo "  <td style='border: 1px solid #dee2e6; padding: 12px;'><code style='background: #f8f9fa; padding: 4px 8px; border-radius: 3px;'>" . htmlspecialchars($oldPath) . "</code></td>";
    echo "  <td style='border: 1px solid #dee2e6; padding: 12px;'><code style='background: #d4edda; padding: 4px 8px; border-radius: 3px;'>" . htmlspecialchars($newPath) . "</code></td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3 style='margin-top: 30px;'>Applying Fix...</h3>";

// Update all wrong avatar paths
$updated = 0;
foreach ($wrongAvatars as $user) {
    $newPath = str_replace('/public/avatars/', '/uploads/avatars/', $user['avatar']);
    
    $stmt = $mysqli->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $newPath, $user['id']);
        if ($stmt->execute()) {
            $updated++;
        }
        $stmt->close();
    }
}

echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin: 20px 0;'>";
echo "<h4 style='color: #155724; margin: 0;'>✅ Fix Applied Successfully!</h4>";
echo "<p style='margin: 10px 0 0 0; color: #155724;'>";
echo "Updated <strong>$updated user(s)</strong> with correct avatar paths.<br>";
echo "Avatar paths now use <code>/uploads/avatars/</code> instead of <code>/public/avatars/</code>";
echo "</p>";
echo "</div>";

echo "<h3 style='margin-top: 30px;'>Next Steps:</h3>";
echo "<ol>";
echo "  <li><strong>Clear Cache:</strong> Press <code>CTRL + SHIFT + DEL</code></li>";
echo "  <li><strong>Clear Cookies:</strong> Select 'All time' and check 'Cookies and other site data'</li>";
echo "  <li><strong>Hard Refresh:</strong> Press <code>CTRL + F5</code></li>";
echo "  <li><strong>Check Results:</strong> Navigate to any page with avatars and verify they load</li>";
echo "  <li><strong>Verify Fix:</strong> Open DevTools (F12), go to Network tab, and confirm no 404 errors for avatars</li>";
echo "</ol>";

echo "<div style='background: #e7f3ff; border-left: 4px solid #0052cc; padding: 15px; margin-top: 30px;'>";
echo "<p style='margin: 0; color: #0052cc;'>";
echo "💡 <strong>Tip:</strong> The avatar() helper function in <code>src/Helpers/functions.php</code> has been updated to automatically ";
echo "handle any remaining /public/avatars/ paths as a fallback. This ensures compatibility even if some paths are manually set incorrectly.";
echo "</p>";
echo "</div>";

// Close database connection
$mysqli->close();

?>
