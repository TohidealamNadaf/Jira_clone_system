<?php
/**
 * Test Budget/Settings Validation
 * Verify that the currency validation works correctly
 */

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Request;
use App\Core\Validator;

try {
    echo "=== Testing Currency Validation ===\n\n";
    
    // Test 1: Valid 3-letter currency codes
    echo "Test 1: Valid currency codes\n";
    $testCases = [
        'USD' => true,  // Should pass
        'EUR' => true,  // Should pass
        'GBP' => true,  // Should pass
        'INR' => true,  // Should pass
        'US' => false,   // Should fail (2 chars)
        'USDA' => false, // Should fail (4 chars)
        'U' => false,    // Should fail (1 char)
    ];
    
    foreach ($testCases as $currency => $shouldPass) {
        $validator = new \App\Core\Validator(
            ['currency' => $currency],
            ['currency' => 'required|min:3|max:3']
        );
        
        $valid = $validator->validate();
        $result = $valid ? '✓ PASS' : '✗ FAIL';
        $expected = $shouldPass ? 'PASS' : 'FAIL';
        $match = ($valid === $shouldPass) ? '✓' : '✗';
        
        echo "  $match Currency: '$currency' → $result (expected: $expected)\n";
        if (!$valid && $validator->errors()) {
            foreach ($validator->errors() as $field => $errors) {
                foreach ($errors as $error) {
                    echo "    Error: $error\n";
                }
            }
        }
    }
    
    // Test 2: Full settings validation like in UserController
    echo "\nTest 2: Full settings validation\n";
    $settingsData = [
        'annual_package' => '1000000',
        'rate_currency' => 'INR',
        'theme' => 'dark'
    ];
    
    $validator = new \App\Core\Validator($settingsData, [
        'annual_package' => 'nullable|numeric|min:0',
        'rate_currency' => 'nullable|in:USD,EUR,GBP,INR,AUD,CAD,SGD,JPY',
        'theme' => 'nullable|in:light,dark,auto'
    ]);
    
    if ($validator->validate()) {
        echo "  ✓ Settings validation PASSED\n";
        echo "  Validated data:\n";
        foreach ($validator->validated() as $key => $value) {
            echo "    - $key: $value\n";
        }
    } else {
        echo "  ✗ Settings validation FAILED\n";
        foreach ($validator->errors() as $field => $errors) {
            foreach ($errors as $error) {
                echo "    Error: $error\n";
            }
        }
    }
    
    echo "\n✓ All validation tests completed!\n";
    
} catch (\Exception $e) {
    echo "✗ Error: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}\n";
    echo "Line: {$e->getLine()}\n";
    exit(1);
}
