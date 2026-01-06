<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

echo "Creating sample notifications...\n";

try {
    // Add sample notifications for user 1 (admin)
    Database::insert('notifications', [
        'user_id' => 1,
        'type' => 'issue_assigned',
        'notifiable_type' => 'issue',
        'notifiable_id' => 1,
        'data' => json_encode(['message' => 'You have been assigned to issue BP-1']),
    ]);
    echo "Added notification 1\n";

    Database::insert('notifications', [
        'user_id' => 1,
        'type' => 'comment_added',
        'notifiable_type' => 'issue',
        'notifiable_id' => 2,
        'data' => json_encode(['message' => 'John Smith commented on BP-2']),
    ]);
    echo "Added notification 2\n";

    Database::insert('notifications', [
        'user_id' => 1,
        'type' => 'issue_updated',
        'notifiable_type' => 'issue',
        'notifiable_id' => 3,
        'data' => json_encode(['message' => 'Issue BP-3 status changed to In Progress']),
    ]);
    echo "Added notification 3\n";

    // Also add for user 7 (vostro631@gmail.com)
    Database::insert('notifications', [
        'user_id' => 7,
        'type' => 'issue_assigned',
        'notifiable_type' => 'issue',
        'notifiable_id' => 1,
        'data' => json_encode(['message' => 'You have been assigned to a new issue']),
    ]);
    echo "Added notification for user 7\n";

    echo "\n✅ Sample notifications created successfully!\n";

    // Verify
    $count = Database::selectValue("SELECT COUNT(*) FROM notifications");
    echo "Total notifications in database: $count\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
