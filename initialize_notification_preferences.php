<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

echo "Initializing notification preferences for all users...\n\n";

try {
    // Get all users
    $users = Database::select('SELECT id FROM users', []);
    
    if (empty($users)) {
        echo "No users found.\n";
        exit(0);
    }
    
    $eventTypes = [
        'issue_created',
        'issue_assigned',
        'issue_commented',
        'issue_status_changed',
        'issue_mentioned',
        'issue_watched',
        'project_created',
        'project_member_added',
        'comment_reply',
    ];
    
    $created = 0;
    $skipped = 0;
    
    foreach ($users as $user) {
        $userId = $user['id'];
        
        foreach ($eventTypes as $eventType) {
            // Check if preference already exists
            $existing = Database::selectOne(
                'SELECT id FROM notification_preferences WHERE user_id = ? AND event_type = ?',
                [$userId, $eventType]
            );
            
            if ($existing) {
                $skipped++;
                continue;
            }
            
            // Set defaults: in_app and email enabled, push disabled
            Database::insert('notification_preferences', [
                'user_id' => $userId,
                'event_type' => $eventType,
                'in_app' => 1,
                'email' => 1,
                'push' => 0,
            ]);
            
            $created++;
        }
    }
    
    echo "✅ Initialization complete!\n\n";
    echo "Statistics:\n";
    echo "  - Users: " . count($users) . "\n";
    echo "  - Preferences created: $created\n";
    echo "  - Preferences skipped (already exist): $skipped\n\n";
    
    echo "Default preferences set:\n";
    echo "  ✓ In-app notifications: ENABLED\n";
    echo "  ✓ Email notifications: ENABLED\n";
    echo "  ✓ Push notifications: DISABLED\n\n";
    
    echo "Users can customize these at: /profile/notifications\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
