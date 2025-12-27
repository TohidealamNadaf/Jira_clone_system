<?php

declare(strict_types=1);

// Test the budget API endpoint directly
require_once 'bootstrap/app.php';

echo "=== DEBUGGING BUDGET API ERROR ===\n\n";

// Step 1: Check if project exists
echo "1. Checking project...\n";
$project = \App\Core\Database::selectOne("SELECT id, name, budget, budget_currency FROM projects WHERE id = ?", [1]);
if ($project) {
    echo "   ✓ Project found: {$project['name']} (ID: {$project['id']})\n";
    echo "   Budget: {$project['budget']}, Currency: {$project['budget_currency']}\n";
} else {
    echo "   ✗ Project not found\n";
    exit(1);
}

// Step 2: Test database operations directly
echo "\n2. Testing database operations...\n";
try {
    $rows = \App\Core\Database::update('projects', ['budget' => 999.99, 'budget_currency' => 'EUR'], 'id = ?', [1]);
    echo "   ✓ Update successful: {$rows} rows affected\n";

    $updated = \App\Core\Database::selectOne("SELECT budget, budget_currency FROM projects WHERE id = ?", [1]);
    echo "   ✓ Updated values: {$updated['budget']} {$updated['budget_currency']}\n";
} catch (Exception $e) {
    echo "   ✗ Database error: {$e->getMessage()}\n";
    exit(1);
}

// Step 3: Test service methods
echo "\n3. Testing service methods...\n";
$projectService = new \App\Services\ProjectService();

try {
    $budgetStatus = $projectService->getBudgetStatus(1);
    echo "   ✓ getBudgetStatus: " . json_encode($budgetStatus) . "\n";
} catch (Exception $e) {
    echo "   ✗ getBudgetStatus error: {$e->getMessage()}\n";
}

try {
    $result = $projectService->setProjectBudget(1, 1234.56, 'USD');
    echo "   ✓ setProjectBudget: " . ($result ? 'true' : 'false') . "\n";
} catch (Exception $e) {
    echo "   ✗ setProjectBudget error: {$e->getMessage()}\n";
}

// Step 4: Simulate API call
echo "\n4. Simulating API call...\n";

try {
    // Set up authentication
    $GLOBALS['api_user'] = [
        'id' => 1,
        'email' => 'admin@example.com',
        'first_name' => 'Admin',
        'last_name' => 'User',
        'token_type' => 'session'
    ];

    // Create fresh request object
    $request = new \App\Core\Request(['projectId' => '1']);

    // Force fresh JSON parsing by resetting the cache
    $reflection = new ReflectionClass($request);
    $property = $reflection->getProperty('json');
    $property->setAccessible(true);
    $property->setValue($request, null);

    // Mock JSON input
    $_SERVER['REQUEST_METHOD'] = 'PUT';
    $_SERVER['CONTENT_TYPE'] = 'application/json';

    // Clear and set input stream
    $input = '{"budget": 5678.90, "currency": "USD"}';
    file_put_contents('php://input', $input);
    echo "   Mock JSON input: $input\n";

    // Test JSON parsing directly
    $parsed = json_decode($input, true);
    echo "   Parsed JSON: " . json_encode($parsed) . "\n";
    echo "   Budget field: " . ($parsed['budget'] ?? 'NOT FOUND') . "\n";
    echo "   Currency field: " . ($parsed['currency'] ?? 'NOT FOUND') . "\n";

    // Create controller
    $controller = new \App\Controllers\Api\ProjectBudgetApiController();

    echo "   Calling updateBudget...\n";
    ob_start();
    $controller->updateBudget($request);
    $output = ob_get_clean();

    echo "   Output length: " . strlen($output) . "\n";
    echo "   First 500 chars: " . substr($output, 0, 500) . "\n";

    $json = json_decode($output, true);
    if ($json) {
        echo "   ✓ Valid JSON response\n";
        echo "   Response: " . json_encode($json, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "   ✗ Invalid JSON response\n";
        echo "   Raw output: $output\n";
    }

} catch (Exception $e) {
    echo "   ✗ API call exception: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n";
    echo "   Trace:\n{$e->getTraceAsString()}\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
