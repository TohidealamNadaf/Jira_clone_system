<?php

declare(strict_types=1);

require_once 'bootstrap/app.php';

try {
    echo "Testing budget API fix...\n";

    // Simulate API authentication
    $GLOBALS['api_user'] = [
        'id' => 1,
        'email' => 'admin@example.com',
        'first_name' => 'Admin',
        'last_name' => 'User',
        'token_type' => 'session'
    ];

    // Create controller and test
    $controller = new App\Controllers\Api\ProjectBudgetApiController();

    // Mock request with route parameters
    $request = new App\Core\Request(['projectId' => '1']);

    // Test getBudget
    echo "Testing getBudget... ";
    ob_start();
    $controller->getBudget($request);
    $output = ob_get_clean();
    $data = json_decode($output, true);
    if ($data && isset($data['success'])) {
        echo "OK\n";
    } else {
        echo "FAILED: " . substr($output, 0, 100) . "\n";
    }

    // Test updateBudget
    echo "Testing updateBudget... ";
    $updateRequest = new App\Core\Request(['projectId' => '1']);

    // Mock JSON input
    $_SERVER['REQUEST_METHOD'] = 'PUT';
    $_SERVER['CONTENT_TYPE'] = 'application/json';
    file_put_contents('php://input', '{"budget": 7500.00, "currency": "USD"}');

    ob_start();
    $controller->updateBudget($updateRequest);
    $output = ob_get_clean();
    $data = json_decode($output, true);
    if ($data && isset($data['success'])) {
        echo "OK\n";
        echo "Budget updated successfully!\n";
    } else {
        echo "FAILED: " . substr($output, 0, 200) . "\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
