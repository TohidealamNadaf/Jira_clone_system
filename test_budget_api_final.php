<?php
/**
 * Budget API Diagnostic Tool
 * Tests the budget save endpoint directly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate a budget save request
header('Content-Type: application/json');

try {
    require_once 'bootstrap/autoload.php';

    $projectId = 1;
    $budgetAmount = 50000;
    $currency = 'EUR';

    echo json_encode([
        'test' => 'Budget API Diagnostic',
        'timestamp' => date('Y-m-d H:i:s'),
        'tests' => []
    ], JSON_PRETTY_PRINT) . "\n";

    // Test 1: Check if ProjectBudgetApiController exists
    echo "TEST 1: Controller exists\n";
    if (class_exists('App\Controllers\Api\ProjectBudgetApiController')) {
        echo "✓ ProjectBudgetApiController found\n";
    } else {
        echo "✗ ProjectBudgetApiController NOT FOUND\n";
    }

    // Test 2: Check ProjectService
    echo "\nTEST 2: ProjectService methods\n";
    $projectService = new \App\Services\ProjectService();
    
    if (method_exists($projectService, 'setProjectBudget')) {
        echo "✓ setProjectBudget method exists\n";
    } else {
        echo "✗ setProjectBudget method NOT FOUND\n";
    }

    if (method_exists($projectService, 'getBudgetStatus')) {
        echo "✓ getBudgetStatus method exists\n";
    } else {
        echo "✗ getBudgetStatus method NOT FOUND\n";
    }

    // Test 3: Try to get project
    echo "\nTEST 3: Get project #1\n";
    $project = $projectService->getProjectById($projectId);
    if ($project) {
        echo "✓ Project found: " . $project['name'] . "\n";
    } else {
        echo "✗ Project NOT FOUND\n";
    }

    // Test 4: Try to set budget
    echo "\nTEST 4: Set budget programmatically\n";
    try {
        $result = $projectService->setProjectBudget($projectId, $budgetAmount, $currency);
        if ($result) {
            echo "✓ Budget set successfully\n";
        } else {
            echo "✗ setProjectBudget returned false\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }

    // Test 5: Get budget status
    echo "\nTEST 5: Get budget status\n";
    try {
        $budgetStatus = $projectService->getBudgetStatus($projectId);
        echo "✓ Budget status retrieved:\n";
        echo json_encode($budgetStatus, JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }

    // Test 6: Check Request class
    echo "\nTEST 6: Request class methods\n";
    $requestClass = new ReflectionClass('\App\Core\Request');
    
    if ($requestClass->hasMethod('json')) {
        echo "✓ Request::json() exists\n";
    } else {
        echo "✗ Request::json() NOT FOUND\n";
    }

    if ($requestClass->hasMethod('validateApi')) {
        echo "✓ Request::validateApi() exists\n";
    } else {
        echo "✗ Request::validateApi() NOT FOUND\n";
    }

    // Test 7: Test JSON response
    echo "\nTEST 7: Test JSON response method\n";
    try {
        $controller = new \App\Controllers\Api\ProjectBudgetApiController();
        // Try to call json method through reflection
        $reflection = new ReflectionMethod($controller, 'json');
        echo "✓ Controller::json() method exists\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }

    echo "\n✓ All diagnostic tests completed\n";

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Diagnostic error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT) . "\n";
}
?>
