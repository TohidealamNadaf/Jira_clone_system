<?php
require_once __DIR__ . '/../bootstrap/app.php';
use App\Core\Database;

header('Content-Type: text/plain');

echo "Testing INSERT into issue_attachments...\n";

try {
    // 1. Get an issue ID
    $issue = Database::selectOne("SELECT id FROM issues ORDER BY id DESC LIMIT 1");
    if (!$issue) {
        die("No issues found to test with.\n");
    }
    $issueId = $issue['id'];
    echo "Using Issue ID: $issueId\n";

    // 2. Get a user ID
    $userId = 1; // Admin

    // 3. Try Insert
    $data = [
        'issue_id' => $issueId,
        'uploaded_by' => $userId,
        'filename' => 'test_insert.png',
        'original_name' => 'Original_Test.png',
        'mime_type' => 'image/png',
        'file_size' => 1234,
        'file_path' => 'uploads/test/test.png',
        'created_at' => date('Y-m-d H:i:s'),
    ];

    echo "Inserting data:\n";
    print_r($data);

    $newId = Database::insert('issue_attachments', $data);

    echo "Success! Inserted ID: $newId\n";

    // 4. Verify Select
    $row = Database::selectOne("SELECT * FROM issue_attachments WHERE id = ?", [$newId]);
    print_r($row);

} catch (Exception $e) {
    echo "INSERT FAILED: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString();
}
