<?php
/**
 * Test Budget API Response
 * Diagnoses JSON parsing errors by testing the actual API response
 */

// Simulate the API request
header('Content-Type: text/plain');

echo "=== BUDGET API RESPONSE TEST ===\n\n";

// Set up minimal application
define('DOCUMENT_ROOT', __DIR__ . '/public');
require_once __DIR__ . '/bootstrap/autoload.php';

echo "1. Testing validateApi response format...\n";

// Create a mock request object to test validateApi
$testData = [
    'budget' => '50000',
    'currency' => 'EUR'
];

// Simulate a Request object
$_POST = $testData;
$_SERVER['REQUEST_METHOD'] = 'PUT';
$_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';

echo "2. Test data: " . json_encode($testData) . "\n\n";

echo "3. Calling validateApi with valid data...\n";
try {
    $request = new \App\Core\Request();
    $validated = $request->validateApi([
        'budget' => 'required|numeric|minValue:0',
        'currency' => 'required|min:3|max:3'
    ]);
    echo "✓ Validation passed: " . json_encode($validated) . "\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n4. Testing JSON response headers...\n";

// Start output buffering to capture any unexpected output
ob_start();
$test_response = [
    'success' => true,
    'message' => 'Budget updated successfully',
    'budget' => [
        'total_budget' => 50000,
        'total_cost' => 30500,
        'currency' => 'EUR'
    ]
];

echo "Response that will be sent:\n";
echo json_encode($test_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";

$output = ob_get_clean();

echo "\n5. Checking for extra output...\n";
echo "Output length: " . strlen($output) . " bytes\n";
echo "Output (hex): " . bin2hex($output) . "\n";

// Check for BOM
if (substr($output, 0, 3) === "\xef\xbb\xbf") {
    echo "⚠️  WARNING: BOM detected at start!\n";
}

// Check for trailing whitespace
if (preg_match('/\s+$/', $output)) {
    echo "⚠️  WARNING: Trailing whitespace detected!\n";
}

echo "\n6. Checking files for BOM...\n";
$filesToCheck = [
    'src/Controllers/Api/ProjectBudgetApiController.php',
    'src/Helpers/functions.php',
    'src/Core/Request.php',
];

foreach ($filesToCheck as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $handle = fopen($path, 'rb');
        $bom = fread($handle, 3);
        fclose($handle);
        
        if ($bom === "\xef\xbb\xbf") {
            echo "⚠️  BOM found in: $file\n";
        } else {
            echo "✓ No BOM in: $file\n";
        }
    }
}

echo "\n7. API Response Format Check...\n";
echo "Expected JSON response format should be:\n";
$mockResponse = [
    'success' => true,
    'message' => 'Budget updated successfully',
    'budget' => ['total_budget' => 50000]
];
echo json_encode($mockResponse) . "\n";

echo "\nIf JSON parse error at position 181, check:\n";
echo "- Extra whitespace after json_encode()\n";
echo "- Extra output before/after json() call\n";
echo "- BOM in PHP files (especially those called by API)\n";
echo "- Character encoding (ensure UTF-8)\n";

?>
