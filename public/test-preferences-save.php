<?php
/**
 * Test Page: Verify Notification Preferences Are Actually Saving
 * Access: http://localhost:8080/jira_clone_system/public/test-preferences-save.php
 */

require '../bootstrap/autoload.php';

use App\Core\Database;
use App\Services\NotificationService;

// Get current user ID (simulate logged in user)
$userId = 2;

// Get current preferences before
$prefsBefore = NotificationService::getPreferences($userId);

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Test Notification Preferences Saving</title>";
echo "<style>";
echo "body { font-family: Arial; max-width: 1000px; margin: 40px auto; padding: 20px; }";
echo "h1 { color: #0052cc; }";
echo ".section { margin: 30px 0; padding: 20px; background: #f6f8fa; border-radius: 8px; border-left: 4px solid #0052cc; }";
echo ".success { background: #dffcf0; border-left-color: #34d399; color: #216e4e; }";
echo ".error { background: #ffeceb; border-left-color: #f85149; color: #da3633; }";
echo "table { width: 100%; border-collapse: collapse; margin: 10px 0; }";
echo "th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }";
echo "th { background: #0052cc; color: white; }";
echo "tr:hover { background: #f0f7ff; }";
echo ".bool { font-weight: bold; }";
echo ".true { color: green; }";
echo ".false { color: red; }";
echo "code { background: #eaeef2; padding: 2px 6px; border-radius: 3px; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔍 Notification Preferences - Real-Time Verification</h1>";

echo "<div class='section'>";
echo "<h2>📊 Current Preferences in Database</h2>";
echo "<p>User ID: <strong>$userId</strong></p>";
echo "<p>These are the preferences currently saved in the database:</p>";

if (!empty($prefsBefore)) {
    echo "<table>";
    echo "<tr>";
    echo "<th>Event Type</th>";
    echo "<th>In-App</th>";
    echo "<th>Email</th>";
    echo "<th>Push</th>";
    echo "<th>Last Updated</th>";
    echo "</tr>";
    
    foreach ($prefsBefore as $pref) {
        $inApp = $pref['in_app'] ? '<span class="bool true">✓ Yes</span>' : '<span class="bool false">✗ No</span>';
        $email = $pref['email'] ? '<span class="bool true">✓ Yes</span>' : '<span class="bool false">✗ No</span>';
        $push = $pref['push'] ? '<span class="bool true">✓ Yes</span>' : '<span class="bool false">✗ No</span>';
        
        // Try to get updated_at from database
        $updated = Database::selectOne(
            "SELECT updated_at FROM notification_preferences WHERE user_id = ? AND event_type = ?",
            [$userId, $pref['event_type']]
        );
        $updatedAt = $updated ? $updated['updated_at'] : 'N/A';
        
        echo "<tr>";
        echo "<td><code>{$pref['event_type']}</code></td>";
        echo "<td>$inApp</td>";
        echo "<td>$email</td>";
        echo "<td>$push</td>";
        echo "<td>$updatedAt</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<div class='error'>";
    echo "<strong>⚠️ No preferences found!</strong>";
    echo "<p>User preferences may not be initialized. Try saving preferences from the settings page.</p>";
    echo "</div>";
}

echo "</div>";

echo "<div class='section success'>";
echo "<h2>✅ How to Test If It's Really Saving</h2>";
echo "<ol>";
echo "<li>";
echo "<strong>Go to: <code>/profile/notifications</code></strong><br>";
echo "Navigate to the notification preferences page";
echo "</li>";
echo "<li>";
echo "<strong>Make a change</strong><br>";
echo "For example: Uncheck 'Email' for 'Issue Created'";
echo "</li>";
echo "<li>";
echo "<strong>Click 'Save Preferences'</strong><br>";
echo "You should see a green success message";
echo "</li>";
echo "<li>";
echo "<strong>Come back to this page (F5 refresh)</strong><br>";
echo "Check if the table above shows your changes with a recent 'Last Updated' time";
echo "</li>";
echo "<li>";
echo "<strong>✅ If changes appear here = IT'S REALLY SAVING!</strong><br>";
echo "❌ If changes don't appear = There's a problem";
echo "</li>";
echo "</ol>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>🔧 Direct Test</h2>";
echo "<p>Let's test saving a preference right now:</p>";

try {
    // Test update
    $testResult = NotificationService::updatePreference(
        userId: $userId,
        eventType: 'issue_created',
        inApp: true,
        email: false,
        push: true
    );
    
    // Get updated preferences
    $prefsAfter = NotificationService::getPreferences($userId);
    $testPref = null;
    foreach ($prefsAfter as $pref) {
        if ($pref['event_type'] === 'issue_created') {
            $testPref = $pref;
            break;
        }
    }
    
    if ($testPref && $testPref['in_app'] == 1 && $testPref['email'] == 0 && $testPref['push'] == 1) {
        echo "<div class='success'>";
        echo "<h3>✅ Test PASSED - Preferences ARE saving correctly!</h3>";
        echo "<p>We just saved a test preference:</p>";
        echo "<ul>";
        echo "<li>Event Type: <code>issue_created</code></li>";
        echo "<li>In-App: <code>1 (true)</code></li>";
        echo "<li>Email: <code>0 (false)</code></li>";
        echo "<li>Push: <code>1 (true)</code></li>";
        echo "</ul>";
        echo "<p>And it was saved to the database successfully!</p>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ Test FAILED - Something is wrong</h3>";
        echo "<p>Expected values to be saved but they weren't found in database.</p>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Test ERROR</h3>";
    echo "<p><strong>Error:</strong> " . e($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>📈 Statistics</h2>";
echo "<p>Total preferences saved: <strong>" . count($prefsBefore) . " / 9</strong></p>";
echo "<p>Expected: 9 (one for each event type)</p>";

$count = Database::selectValue("SELECT COUNT(*) FROM notification_preferences WHERE user_id = ?", [$userId]);
echo "<p>Database count: <strong>$count</strong></p>";

// Get total time
$time = round((microtime(true) - APP_START) * 1000, 2);
echo "<p>Page load time: <strong>{$time}ms</strong></p>";
echo "</div>";

echo "</body>";
echo "</html>";
