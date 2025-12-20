<?php

declare(strict_types=1);

require_once 'bootstrap/app.php';

try {
    echo "Testing database connection... ";
    $pdo = App\Core\Database::getConnection();
    echo "OK\n";

    echo "Testing projects table... ";
    $result = App\Core\Database::selectOne('SELECT COUNT(*) as count FROM projects');
    echo "Found " . $result['count'] . " projects\n";

    echo "Testing update operation... ";
    $rows = App\Core\Database::update('projects', ['budget' => 1234.56, 'budget_currency' => 'USD'], 'id = ?', [1]);
    echo "Updated " . $rows . " rows\n";

    echo "Testing getBudgetStatus... ";
    $projectService = new App\Services\ProjectService();
    $status = $projectService->getBudgetStatus(1);
    echo "Status: " . json_encode($status) . "\n";

    echo "Testing API endpoint simulation... ";
    $controller = new App\Controllers\Api\ProjectBudgetApiController();

    // Simulate a request
    $request = new App\Core\Request();
    $_SERVER['REQUEST_METHOD'] = 'PUT';
    $_SERVER['REQUEST_URI'] = '/api/v1/projects/1/budget';
    $_SERVER['CONTENT_TYPE'] = 'application/json';
    $request->setParam('projectId', '1');

    // Mock JSON input
    $jsonInput = '{"budget": 5000.00, "currency": "USD"}';
    $request->setJson(json_decode($jsonInput, true));

    // Mock session
    App\Core\Session::set('user', ['id' => 1, 'email' => 'admin@example.com']);

    // Call the method
    ob_start();
    $controller->updateBudget($request);
    $output = ob_get_clean();
    echo "API Response: " . $output . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
