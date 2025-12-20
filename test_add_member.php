<?php
require_once 'bootstrap/app.php';
use App\Controllers\ProjectController;
use App\Core\Request;

// Simulate a POST request to add a member
$_POST = [
    'user_id' => 2, // John Smith
    'role_id' => 2, // Developer
    '_token' => 'test-token' // Mock CSRF token
];

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/projects/CWAYS/members';

// Create a mock request
$request = new Request();

// Mock the route parameters
$request = $request->withParam('key', 'CWAYS');

// Create controller and call method
$controller = new ProjectController();
try {
    $result = $controller->addMember($request);
    echo "Result: " . json_encode($result) . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}
?>


