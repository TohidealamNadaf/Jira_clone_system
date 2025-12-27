<?php
require_once __DIR__ . '/../bootstrap/app.php';

header('Content-Type: text/plain');

echo "Testing upload permissions...\n";

$targetDir = __DIR__ . '/uploads/' . date('Y/m');
echo "Target Dir: $targetDir\n";

if (!is_dir($targetDir)) {
    echo "Creating directory...\n";
    if (mkdir($targetDir, 0755, true)) {
        echo "Directory created.\n";
    } else {
        echo "Failed to create directory.\n";
        exit;
    }
} else {
    echo "Directory exists.\n";
}

$testFile = $targetDir . '/test_write.txt';
if (file_put_contents($testFile, 'test')) {
    echo "Write success: $testFile\n";
    unlink($testFile);
} else {
    echo "Write failed.\n";
}

// Check Max Upload Size
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
