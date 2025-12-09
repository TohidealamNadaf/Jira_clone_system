<?php
// Quick syntax check
try {
    require_once 'bootstrap/autoload.php';
    require_once 'src/Controllers/AdminController.php';
    echo "✓ AdminController syntax is valid\n";
    echo "✓ All methods loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
