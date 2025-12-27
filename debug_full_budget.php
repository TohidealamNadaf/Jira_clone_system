<?php

declare(strict_types=1);

require_once 'bootstrap/app.php';

echo "=== COMPREHENSIVE BUDGET API DEBUG ===\n\n";

// Test 1: Check current session
echo "1. SESSION STATUS:\n";
$user = \App\Core\Session::user();
if ($user) {
    echo "   ✓ Session user: {$user['email']} (ID: {$user['id']})\n";
    echo "   ✓ Session data: " . json_encode($user) . "\n";
} else {
    echo "   ✗ No session user found\n";
}

// Test 2: Check database
echo "\n2. DATABASE CHECK:\n";
try {
    $project = \App\Core\Database::selectOne("SELECT id, name, budget, budget_currency FROM projects WHERE id = ?", [1]);
    if ($project) {
        echo "   ✓ Project found: {$project['name']} (ID: {$project['id']})\n";
        echo "   ✓ Current budget: {$project['budget']} {$project['budget_currency']}\n";
    } else {
        echo "   ✗ Project with ID 1 not found\n";
    }

    // Test database update
    $rows = \App\Core\Database::update('projects', ['budget' => 99999.99], 'id = ?', [1]);
    echo "   ✓ Database update test: {$rows} row(s) affected\n";

    // Restore original
    \App\Core\Database::update('projects', ['budget' => $project['budget'] ?? 0], 'id = ?', [1]);

} catch (Exception $e) {
    echo "   ✗ Database error: {$e->getMessage()}\n";
}

// Test 3: Simulate exact API call
echo "\n3. API SIMULATION:\n";

// Mock the request exactly like the browser would send
$_SERVER['REQUEST_METHOD'] = 'PUT';
$_SERVER['CONTENT_TYPE'] = 'application/json';
$_SERVER['HTTP_X_CSRF_TOKEN'] = 'test-token'; // Mock CSRF token

// Mock JSON payload
$jsonPayload = '{"budget": 75000.00, "currency": "USD"}';
file_put_contents('php://input', $jsonPayload);

echo "   Mock request: PUT /api/v1/projects/1/budget\n";
echo "   Content-Type: application/json\n";
echo "   Payload: {$jsonPayload}\n";

// Create request
$request = new \App\Core\Request(['projectId' => '1']);

// Test ApiMiddleware directly
echo "\n4. API MIDDLEWARE TEST:\n";
$middleware = new \App\Middleware\ApiMiddleware();

try {
    // Start output buffering to capture middleware output
    ob_start();
    $result = $middleware->handle($request, function($req) use ($jsonPayload) {
        echo "   ✓ Middleware passed - proceeding to controller\n";

        // Test controller
        $controller = new \App\Controllers\Api\ProjectBudgetApiController();

        // Check if api_user is set
        if (!isset($GLOBALS['api_user'])) {
            echo "   ✗ No api_user in globals - authentication failed\n";
            return $req;
        }

        echo "   ✓ api_user set: {$GLOBALS['api_user']['email']}\n";

        // Call controller method
        ob_start();
        $controller->updateBudget($req);
        $controllerOutput = ob_get_clean();

        echo "   Controller output: " . substr($controllerOutput, 0, 200) . "\n";

        $json = json_decode($controllerOutput, true);
        if ($json) {
            echo "   ✓ Valid JSON response\n";
            if (isset($json['success'])) {
                echo "   ✓ Success: " . json_encode($json) . "\n";
            } elseif (isset($json['error'])) {
                echo "   ✗ Error: {$json['error']}\n";
            }
        } else {
            echo "   ✗ Invalid JSON: {$controllerOutput}\n";
        }

        return $req;
    });
    $middlewareOutput = ob_get_clean();

    if (!empty($middlewareOutput)) {
        echo "   Middleware output: {$middlewareOutput}\n";
        $middlewareJson = json_decode($middlewareOutput, true);
        if ($middlewareJson && isset($middlewareJson['error'])) {
            echo "   ✗ Middleware authentication failed: {$middlewareJson['error']}\n";
        }
    }

} catch (Exception $e) {
    echo "   ✗ Exception: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n";
}

// Test 4: Check CSRF validation
echo "\n5. CSRF CHECK:\n";
$csrfToken = \App\Core\Session::get('csrf_token');
if ($csrfToken) {
    echo "   ✓ CSRF token in session: " . substr($csrfToken, 0, 10) . "...\n";
} else {
    echo "   ✗ No CSRF token in session\n";
}

// Test 5: Check Request::json() method
echo "\n6. REQUEST JSON PARSING:\n";
try {
    $testRequest = new \App\Core\Request(['projectId' => '1']);
    file_put_contents('php://input', '{"budget": 50000.00, "currency": "EUR"}');
    $jsonData = $testRequest->json();
    echo "   ✓ Request::json() parsed: " . json_encode($jsonData) . "\n";
} catch (Exception $e) {
    echo "   ✗ Request::json() failed: {$e->getMessage()}\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
echo "\nIf the error persists, the issue might be:\n";
echo "1. Browser cache not cleared\n";
echo "2. User not logged in when testing\n";
echo "3. CSRF token mismatch\n";
echo "4. Network/firewall blocking the request\n";
echo "5. Apache/Nginx configuration issues\n";
echo "\nTry:\n";
echo "1. Hard refresh: CTRL+F5\n";
echo "2. Check browser Network tab for the actual request\n";
echo "3. Check browser Console for JavaScript errors\n";
echo "4. Try logging out and back in\n";
