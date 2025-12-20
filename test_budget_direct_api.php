<?php
/**
 * Direct Budget API Test
 * Simulates the exact PUT request to /api/v1/projects/{projectId}/budget
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to stdout, log them instead

try {
    require_once 'bootstrap/autoload.php';
    
    // Start output buffering to catch any stray output
    ob_start();
    
    // Create a mock Request object with the budget data
    $mockRequest = new class {
        private $data = [
            'budget' => 50000,
            'currency' => 'EUR'
        ];
        private $params = ['projectId' => 1];
        private $headers = [
            'content-type' => 'application/json',
            'x-csrf-token' => 'test'
        ];
        
        public function param(string $key, $default = null) {
            return $this->params[$key] ?? $default;
        }
        
        public function json() {
            return $this->data;
        }
        
        public function header(string $key, $default = null) {
            return $this->headers[strtolower($key)] ?? $default;
        }
        
        public function all() {
            return $this->data;
        }
    };
    
    // Simulate user session
    $sessionMock = ['id' => 1, 'name' => 'Admin User'];
    
    // Create controller instance
    $controller = new \App\Controllers\Api\ProjectBudgetApiController();
    
    // Mock Session::user() - we need to do this carefully
    // For testing, we'll call the method directly with our mock request
    
    // Get the method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('updateBudget');
    $method->setAccessible(true);
    
    // Call it
    $method->invoke($controller, $mockRequest);
    
    // Get any output that was buffered
    $output = ob_get_clean();
    
    if (!empty($output)) {
        http_response_code(200);
        echo $output;
    } else {
        http_response_code(500);
        echo json_encode([
            'error' => 'No response from controller',
            'debug' => 'updateBudget method did not produce output'
        ]);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'Test failed',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} 
?>
