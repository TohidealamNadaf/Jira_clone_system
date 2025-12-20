<?php

declare(strict_types=1);

require_once 'bootstrap/app.php';

echo "Testing budget API fix...\n\n";

// Simulate logged-in user
\App\Core\Session::set('user', [
    'id' => 1,
    'email' => 'admin@example.com',
    'first_name' => 'Admin',
    'last_name' => 'User',
    'is_active' => true
]);

// Test ApiMiddleware with session auth
$request = new \App\Core\Request(['projectId' => '1']);
$middleware = new \App\Middleware\ApiMiddleware();

try {
    $result = $middleware->handle($request, function($req) {
        echo "✓ ApiMiddleware passed with session auth\n";
        return $req;
    });
} catch (Exception $e) {
    echo "✗ ApiMiddleware failed: {$e->getMessage()}\n";
    exit(1);
}

// Test controller
if (isset($GLOBALS['api_user'])) {
    echo "✓ User authenticated via session: {$GLOBALS['api_user']['email']}\n";

    $controller = new \App\Controllers\Api\ProjectBudgetApiController();

    // Mock JSON input
    $_SERVER['REQUEST_METHOD'] = 'PUT';
    $_SERVER['CONTENT_TYPE'] = 'application/json';
    file_put_contents('php://input', '{"budget": 12345.67, "currency": "USD"}');

    ob_start();
    $controller->updateBudget($request);
    $output = ob_get_clean();

    $json = json_decode($output, true);
    if ($json && isset($json['success'])) {
        echo "✓ Budget update successful\n";
        echo "Response: " . json_encode($json, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "✗ Budget update failed\n";
        echo "Output: $output\n";
    }
} else {
    echo "✗ No api_user in globals\n";
}

echo "\nTest complete.\n";
