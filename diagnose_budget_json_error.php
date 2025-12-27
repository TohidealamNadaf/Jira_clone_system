<?php
/**
 * Diagnostic Script for Budget JSON Error
 * Tests the API endpoint directly and shows what's being returned
 */

declare(strict_types=1);

require __DIR__ . '/bootstrap/autoload.php';

$projectId = 1;

echo "=== BUDGET API DIAGNOSTIC ===\n\n";
echo "Project ID: {$projectId}\n";
echo "Endpoint: PUT /api/v1/projects/{$projectId}/budget\n\n";

// Test 1: Check if projects table has budget columns
echo "TEST 1: Check database columns\n";
echo str_repeat("-", 50) . "\n";
try {
    $columns = \App\Core\Database::select(
        "SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'projects' AND TABLE_SCHEMA = DATABASE()
         AND COLUMN_NAME LIKE '%budget%'"
    );
    if (empty($columns)) {
        echo "❌ ERROR: No budget columns found in projects table!\n";
        echo "   Expected columns: budget, budget_currency\n";
    } else {
        echo "✓ Budget columns found:\n";
        foreach ($columns as $col) {
            echo "  - {$col['COLUMN_NAME']}: {$col['COLUMN_TYPE']}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check project exists
echo "TEST 2: Check if project exists\n";
echo str_repeat("-", 50) . "\n";
try {
    $project = \App\Core\Database::selectOne(
        "SELECT id, name, budget, budget_currency FROM projects WHERE id = ?",
        [$projectId]
    );
    if ($project) {
        echo "✓ Project found:\n";
        echo "  - ID: {$project['id']}\n";
        echo "  - Name: {$project['name']}\n";
        echo "  - Budget: " . ($project['budget'] ?? 'NULL') . "\n";
        echo "  - Currency: " . ($project['budget_currency'] ?? 'NULL') . "\n";
    } else {
        echo "❌ Project not found!\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Check issue_time_logs table
echo "TEST 3: Check issue_time_logs table structure\n";
echo str_repeat("-", 50) . "\n";
try {
    $columns = \App\Core\Database::select(
        "SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'issue_time_logs' AND TABLE_SCHEMA = DATABASE()
         LIMIT 10"
    );
    if (empty($columns)) {
        echo "⚠ WARNING: issue_time_logs table not found or empty\n";
    } else {
        echo "✓ issue_time_logs table structure:\n";
        foreach ($columns as $col) {
            echo "  - {$col['COLUMN_NAME']}: {$col['COLUMN_TYPE']}\n";
        }
    }
} catch (Exception $e) {
    echo "⚠ WARNING: Could not check table: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Test getBudgetStatus directly
echo "TEST 4: Test getBudgetStatus() method\n";
echo str_repeat("-", 50) . "\n";
try {
    $projectService = new \App\Services\ProjectService();
    $budgetStatus = $projectService->getBudgetStatus($projectId);
    echo "✓ getBudgetStatus returned:\n";
    echo "  " . json_encode($budgetStatus, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Check if there are whitespace/BOM issues in controller files
echo "TEST 5: Check for PHP output buffering issues\n";
echo str_repeat("-", 50) . "\n";
try {
    $controllerFile = __DIR__ . '/src/Controllers/Api/ProjectBudgetApiController.php';
    $content = file_get_contents($controllerFile);
    
    // Check for BOM
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        echo "❌ ERROR: File has UTF-8 BOM!\n";
    }
    
    // Check for trailing whitespace after closing tag
    if (preg_match('/\?>\s+$/', $content)) {
        echo "❌ ERROR: Whitespace found after closing PHP tag\n";
    } else {
        echo "✓ No BOM or trailing whitespace detected\n";
    }
    
} catch (Exception $e) {
    echo "⚠ WARNING: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Check Core\Controller json() method
echo "TEST 6: Verify Controller::json() method\n";
echo str_repeat("-", 50) . "\n";
try {
    $reflectionMethod = new ReflectionMethod(\App\Core\Controller::class, 'json');
    echo "✓ json() method found\n";
    echo "  Location: " . $reflectionMethod->getFileName() . ":" . $reflectionMethod->getStartLine() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 7: Test actual API response simulation
echo "TEST 7: Simulate API response\n";
echo str_repeat("-", 50) . "\n";
try {
    // Set up request context
    $_SERVER['REQUEST_METHOD'] = 'PUT';
    $_SERVER['CONTENT_TYPE'] = 'application/json';
    $_POST = [];
    $_GET = [];
    
    // Create a mock request
    $requestData = [
        'budget' => 5000,
        'currency' => 'USD'
    ];
    
    // Simulate what happens in the controller
    $projectService = new \App\Services\ProjectService();
    $project = $projectService->getProjectById($projectId);
    
    if (!$project) {
        echo "❌ Project not found\n";
    } else {
        echo "✓ Simulating response:\n";
        $budgetStatus = $projectService->getBudgetStatus($projectId);
        $response = [
            'success' => true,
            'message' => 'Budget updated successfully',
            'budget' => $budgetStatus
        ];
        
        echo "  Response type: " . gettype($response) . "\n";
        echo "  Response length: " . strlen(json_encode($response)) . "\n";
        echo "  Response (first 500 chars): " . substr(json_encode($response), 0, 500) . "\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n\n=== END DIAGNOSTIC ===\n";
?>
