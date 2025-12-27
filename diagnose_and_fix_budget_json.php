<?php
/**
 * Diagnose and Fix Budget JSON Parse Error
 * 
 * This script helps diagnose the "Unexpected non-whitespace character after JSON at position 181" error
 * and provides fixes for common causes.
 */

header('Content-Type: text/html; charset=utf-8');

echo <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>Budget JSON Debug Tool</title>
    <style>
        body { font-family: monospace; background: #f5f5f5; margin: 20px; }
        .section { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .ok { color: green; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; }
        h2 { border-bottom: 2px solid #333; padding-bottom: 10px; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; border-radius: 3px; }
        .hex-dump { font-size: 11px; }
    </style>
</head>
<body>
    <h1>Budget JSON Parse Error - Diagnostic Tool</h1>
    
HTML;

// Check for trailing whitespace in critical files
echo '<div class="section"><h2>1. Checking for Trailing Whitespace in PHP Files</h2>';

$filesToCheck = [
    'src/Helpers/functions.php',
    'src/Controllers/Api/ProjectBudgetApiController.php',
    'src/Core/Request.php',
    'src/Core/Controller.php',
    'bootstrap/autoload.php',
];

$issues = [];

foreach ($filesToCheck as $filePath) {
    $fullPath = __DIR__ . '/' . $filePath;
    if (!file_exists($fullPath)) {
        echo "<p class='warning'>⚠️  File not found: $filePath</p>";
        continue;
    }
    
    $content = file_get_contents($fullPath);
    $lastChar = substr($content, -1);
    $last10 = substr($content, -10);
    
    echo "<p><strong>$filePath</strong></p>";
    echo "<pre class='hex-dump'>Last 10 bytes (hex): " . bin2hex($last10) . "</pre>";
    
    // Check for common issues
    if ($lastChar === "\n") {
        echo "<p class='warning'>⚠️  WARNING: File ends with newline</p>";
        $issues[$filePath][] = "Trailing newline";
    } elseif ($lastChar === " " || $lastChar === "\t") {
        echo "<p class='warning'>⚠️  WARNING: File ends with whitespace</p>";
        $issues[$filePath][] = "Trailing whitespace";
    } else {
        echo "<p class='ok'>✓ File ends with: " . bin2hex($lastChar) . "</p>";
    }
    
    // Check for closing PHP tag
    if (strpos($content, '?>') !== false) {
        $closingPos = strrpos($content, '?>');
        if ($closingPos < strlen($content) - 2) {
            echo "<p class='warning'>⚠️  WARNING: Content after ?> closing tag</p>";
            $issues[$filePath][] = "Content after closing tag";
        } else {
            echo "<p class='warning'>⚠️  File has closing ?> tag (not recommended for classes)</p>";
            $issues[$filePath][] = "Has closing tag";
        }
    }
    
    echo '<hr>';
}

// Check for BOM
echo '</div><div class="section"><h2>2. Checking for BOM (Byte Order Mark)</h2>';

foreach ($filesToCheck as $filePath) {
    $fullPath = __DIR__ . '/' . $filePath;
    if (!file_exists($fullPath)) continue;
    
    $handle = fopen($fullPath, 'rb');
    $bom = fread($handle, 3);
    fclose($handle);
    
    echo "<p><strong>$filePath</strong></p>";
    
    if ($bom === "\xef\xbb\xbf") {
        echo "<p class='error'>❌ BOM DETECTED (UTF-8 BOM)</p>";
        echo "<p>Hex: " . bin2hex($bom) . "</p>";
        $issues[$filePath][] = "BOM detected";
    } else {
        echo "<p class='ok'>✓ No BOM</p>";
    }
}

// Simulate API response
echo '</div><div class="section"><h2>3. Simulating API Response</h2>';

$testResponse = [
    'success' => true,
    'message' => 'Budget updated successfully',
    'budget' => [
        'total_budget' => 50000,
        'total_cost' => 30550,
        'remaining' => 19450,
        'currency' => 'EUR'
    ]
];

$jsonString = json_encode($testResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
echo "<p><strong>JSON Response:</strong></p>";
echo "<pre>$jsonString</pre>";
echo "<p>Length: " . strlen($jsonString) . " characters</p>";
echo "<p>Position 181 would be at character: <strong>" . (strlen($jsonString) > 180 ? substr($jsonString, 180, 10) : '(beyond response length)') . "</strong></p>";

// Character analysis
echo '</div><div class="section"><h2>4. Character Analysis</h2>';
echo "<p>If your response is ~180 characters, position 181 error means there's something AFTER the valid JSON.</p>";
echo "<p>Common causes:</p>";
echo "<ul>";
echo "<li>Trailing newline after json() exit</li>";
echo "<li>Extra spaces or tabs</li>";
echo "<li>PHP closing tag with content after</li>";
echo "<li>Output buffering capturing extra content</li>";
echo "<li>BOM (Byte Order Mark) at file start</li>";
echo "</ul>";

// Summary
echo '</div><div class="section"><h2>5. Issues Found</h2>';

if (empty($issues)) {
    echo "<p class='ok'>✓ No obvious issues detected</p>";
} else {
    echo "<p class='error'>Issues found:</p>";
    foreach ($issues as $file => $fileIssues) {
        echo "<p><strong>$file:</strong></p>";
        echo "<ul>";
        foreach ($fileIssues as $issue) {
            echo "<li>$issue</li>";
        }
        echo "</ul>";
    }
}

// Recommendations
echo '</div><div class="section"><h2>6. Recommendations</h2>';
echo <<<'REC'
<ol>
    <li><strong>Remove Trailing Whitespace</strong>
        <ul>
            <li>Edit each file and go to the end</li>
            <li>Delete any empty lines after the final closing brace</li>
            <li>Save the file</li>
        </ul>
    </li>
    <li><strong>Remove Closing PHP Tags</strong>
        <ul>
            <li>Delete any <code>?></code> closing tags at end of class/namespace files</li>
            <li>Files should end with <code>}</code> only</li>
        </ul>
    </li>
    <li><strong>Verify File Encoding</strong>
        <ul>
            <li>Open in VS Code</li>
            <li>Check bottom-right corner for encoding (should be UTF-8)</li>
            <li>If UTF-8 with BOM, change to UTF-8 without BOM</li>
        </ul>
    </li>
    <li><strong>Test the Fix</strong>
        <ul>
            <li>Clear browser cache (CTRL+SHIFT+DEL)</li>
            <li>Hard refresh (CTRL+F5)</li>
            <li>Open DevTools (F12) → Network tab</li>
            <li>Try to save budget again</li>
            <li>Check the API response in Network tab</li>
        </ul>
    </li>
</ol>
REC;

// Direct API Test
echo '</div><div class="section"><h2>7. Direct API Test</h2>';
echo <<<'TEST'
<p>To test the API directly, you can use curl:</p>
<pre>
curl -X PUT http://localhost:8081/jira_clone_system/public/api/v1/projects/1/budget \
  -H "Content-Type: application/json" \
  -d '{"budget": 50000, "currency": "EUR"}'
</pre>
<p>This will show exactly what the API returns, including any extra characters.</p>
TEST;

echo '</div></body></html>';
?>
