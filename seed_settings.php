<?php
require 'bootstrap/autoload.php';

$defaultSettings = [
    ['key' => 'app_name', 'value' => 'Jira Clone'],
    ['key' => 'app_url', 'value' => 'http://localhost:8080/jira_clone_system/public/'],
    ['key' => 'default_timezone', 'value' => 'UTC'],
    ['key' => 'default_language', 'value' => 'en'],
    ['key' => 'date_format', 'value' => 'Y-m-d'],
    ['key' => 'primary_color', 'value' => '#0d6efd'],
    ['key' => 'default_theme', 'value' => 'light'],
    ['key' => 'mail_driver', 'value' => 'log'],
    ['key' => 'mail_from_name', 'value' => 'Jira Clone'],
    ['key' => 'session_timeout', 'value' => '120'],
    ['key' => 'password_min_length', 'value' => '8'],
    ['key' => 'password_require_special', 'value' => '1'],
    ['key' => 'max_login_attempts', 'value' => '5'],
    ['key' => 'lockout_duration', 'value' => '15'],
    ['key' => 'notify_issue_assigned', 'value' => '1'],
    ['key' => 'notify_issue_updated', 'value' => '1'],
    ['key' => 'notify_comment_added', 'value' => '1'],
    ['key' => 'notify_mentioned', 'value' => '1'],
];

try {
    $db = \App\Core\Database::class;
    
    // Clear existing
    \App\Core\Database::delete('settings', '1 = 1');
    
    // Insert defaults
    foreach ($defaultSettings as $setting) {
        \App\Core\Database::insert('settings', $setting);
    }
    
    echo "Settings seeded successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
