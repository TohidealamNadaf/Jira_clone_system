<?php
require_once __DIR__ . '/bootstrap/autoload.php';

use App\Services\NotificationService;
use App\Core\Database;

try {
    echo "Triggering comment notification...\n";
    // Using issue 1, commenter 5, comment 10 (arbitrary values)
    NotificationService::dispatchCommentAdded(1, 5, 10);
    echo "Done.\n";
} catch (\Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}
