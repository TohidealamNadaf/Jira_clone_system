<?php

declare(strict_types=1);

require_once 'bootstrap/app.php';

echo "=== BUDGET API DEBUG ===\n\n";

// Test 1: Check if user is authenticated
echo "1. Checking authentication...\n";
$user = \App\Core\Session::user();
if ($user) {
    echo "   ✓ Session user found: {$user['email']}\n";
} else {
    echo "   ✗ No session user found\n";
}

// Test 2: Check API authentication
echo "\n2. Testing API middleware authentication...\n";
$request = new \App\Core\Request(['projectId' => '1']);

// Mock API request
$_SERVER['REQUEST_METHOD'] = 'PUT';
$_SERVER['CONTENT_TYPE'] = 'application/json';
file_put_contents('php://input', '{"budget": 5000.00, "currency": "USD"}');

// Test ApiMiddleware
$middleware = new \App\Middleware\ApiMiddleware();
try {
    $result = $middleware->handle($request, function($req) {
        echo "   ✓ API middleware passed\n";
        echo "   ✓ api_user in globals: " . (isset($GLOBALS['api_user']) ? 'YES' : 'NO') . "\n";
        if (isset($GLOBALS['api_user'])) {
            echo "   ✓ User: {$GLOBALS['api_user']['email']}\n";
        }
        return $req;
    });
} catch (Exception $e) {
    echo "   ✗ API middleware failed: {$e->getMessage()}\n";
}

// Test 3: Check controller directly
echo "\n3. Testing controller directly...\n";
try {
    $controller = new \App\Controllers\Api\ProjectBudgetApiController();

    // Set api_user
    if ($user) {
        $GLOBALS['api_user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
            'token_type' => 'session'
        ];
    }

    ob_start();
    $controller->updateBudget($request);
    $output = ob_get_clean();

    echo "   Controller output length: " . strlen($output) . "\n";
    echo "   First 200 chars: " . substr($output, 0, 200) . "\n";

    $json = json_decode($output, true);
    if ($json) {
        echo "   ✓ Valid JSON response\n";
        if (isset($json['success'])) {
            echo "   ✓ Success: {$json['success']}\n";
        }
        if (isset($json['error'])) {
            echo "   ✗ Error: {$json['error']}\n";
        }
    } else {
        echo "   ✗ Invalid JSON response\n";
    }

} catch (Exception $e) {
    echo "   ✗ Controller exception: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n";
}

// Test 4: Check database
echo "\n4. Testing database operations...\n";
try {
    $project = \App\Core\Database::selectOne("SELECT id, budget, budget_currency FROM projects WHERE id = ?", [1]);
    if ($project) {
        echo "   ✓ Project found: ID {$project['id']}, Budget: {$project['budget']}, Currency: {$project['budget_currency']}\n";
    } else {
        echo "   ✗ Project not found\n";
    }

    $rows = \App\Core\Database::update('projects', ['budget' => 9999.99, 'budget_currency' => 'EUR'], 'id = ?', [1]);
    echo "   ✓ Database update test: {$rows} rows affected\n";

} catch (Exception $e) {
    echo "   ✗ Database error: {$e->getMessage()}\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
