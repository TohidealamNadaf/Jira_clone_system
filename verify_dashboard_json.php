<?php
require_once __DIR__ . '/bootstrap/autoload.php';

// Mock a Request object if needed, or just call the controller method
// Since AdminController::index takes a Request, let's mock it.
use App\Core\Request;
use App\Controllers\AdminController;

$_SERVER['REQUEST_URI'] = '/admin';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_SERVER['HTTP_ACCEPT'] = 'application/json';

$request = new Request();
$controller = new AdminController();

try {
    // Capture output
    ob_start();
    $result = $controller->index($request);
    $output = ob_get_clean();

    echo "--- JSON RESPONSE START ---\n";
    echo $output;
    echo "\n--- JSON RESPONSE END ---\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
