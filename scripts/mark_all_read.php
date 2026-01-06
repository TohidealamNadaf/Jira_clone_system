<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

Database::query('UPDATE notifications SET read_at = NOW() WHERE read_at IS NULL');
echo "All notifications marked as read\n";

$count = Database::selectValue('SELECT COUNT(*) FROM notifications WHERE read_at IS NULL');
echo "Unread count: $count\n";
