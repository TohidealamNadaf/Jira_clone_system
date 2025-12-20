<?php
/**
 * Debug Budget 500 Error
 * Find and test the exact error
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'bootstrap/autoload.php';

// Simulate the budget save request
echo "=== DEBUGGING BUDGET 500 ERROR ===\n\n";

try {
    // Test 1: Check if ProjectBudgetApiController exists
    echo "[1] Checking ProjectBudgetApiController...\n";
    $controllerClass = 'App\Controllers\Api\ProjectBudgetApiController';
    if (class_exists($controllerClass)) {
        echo "✓ Controller class exists\n";
    } else {
        echo "✗ Controller class NOT FOUND\n";
    }

    // Test 2: Check if ProjectService exists and has required methods
    echo "\n[2] Checking ProjectService...\n";
    $serviceClass = 'App\Services\ProjectService';
    if (class_exists($serviceClass)) {
        echo "✓ Service class exists\n";
        $reflectionClass = new ReflectionClass($serviceClass);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $methodNames = array_map(fn($m) => $m->getName(), $methods);
        
        if (in_array('setProjectBudget', $methodNames)) {
            echo "✓ setProjectBudget method exists\n";
        } else {
            echo "✗ setProjectBudget method NOT FOUND\n";
            echo "  Available methods:\n";
            foreach ($methodNames as $name) {
                if (strpos($name, 'budget') !== false || strpos($name, 'Budget') !== false) {
                    echo "  - $name\n";
                }
            }
        }
        
        if (in_array('getBudgetStatus', $methodNames)) {
            echo "✓ getBudgetStatus method exists\n";
        } else {
            echo "✗ getBudgetStatus method NOT FOUND\n";
        }
    } else {
        echo "✗ Service class NOT FOUND\n";
    }

    // Test 3: Check database tables
    echo "\n[3] Checking database tables...\n";
    $database = new \App\Core\Database();
    
    // Check for project budgets table
    try {
        $result = $database->query("SELECT 1 FROM project_budgets LIMIT 1");
        echo "✓ project_budgets table exists\n";
    } catch (Exception $e) {
        echo "✗ project_budgets table ERROR: " . $e->getMessage() . "\n";
    }

    // List all tables
    echo "\n[4] Available tables:\n";
    $tables = $database->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'jiira_clonee_system' ORDER BY TABLE_NAME");
    foreach ($tables as $table) {
        $tableName = $table['TABLE_NAME'] ?? $table[0] ?? 'unknown';
        if (strpos(strtolower($tableName), 'budget') !== false || strpos(strtolower($tableName), 'project') !== false) {
            echo "  - $tableName\n";
        }
    }

    // Test 4: Try to get a project and its budget
    echo "\n[5] Testing budget retrieval for project #1...\n";
    $projectService = new \App\Services\ProjectService();
    $project = $projectService->getProjectById(1);
    if ($project) {
        echo "✓ Project #1 found: " . $project['name'] . "\n";
        
        try {
            $budgetStatus = $projectService->getBudgetStatus(1);
            echo "✓ Budget status retrieved\n";
            echo "  Budget data: " . json_encode($budgetStatus, JSON_PRETTY_PRINT) . "\n";
        } catch (Exception $e) {
            echo "✗ getBudgetStatus error: " . $e->getMessage() . "\n";
            echo "  at: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    } else {
        echo "✗ Project #1 not found\n";
    }

    // Test 5: Check Request::validateApi method
    echo "\n[6] Checking Request::validateApi method...\n";
    $reflectionClass = new ReflectionClass('\App\Core\Request');
    if ($reflectionClass->hasMethod('validateApi')) {
        echo "✓ validateApi method exists in Request\n";
    } else {
        echo "✗ validateApi method NOT FOUND\n";
        echo "  Available methods:\n";
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'validate') !== false) {
                echo "  - " . $method->getName() . "\n";
            }
        }
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
?>
