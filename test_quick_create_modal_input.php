<?php
/**
 * Test Quick Create Modal Input Fields
 * 
 * Diagnose why summary field is not accepting input
 */

declare(strict_types=1);

// Check if input field exists and is properly formed
$diagnostics = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => []
];

$diagnostics['tests'][] = [
    'name' => 'Summary Field in HTML',
    'check' => 'Is the quickCreateSummary input field properly defined?',
    'expected' => 'Input field with id="quickCreateSummary" and no disabled/readonly attributes',
    'html_snippet' => '<input type="text" ... id="quickCreateSummary" name="summary" ... maxlength="500">',
    'status' => 'NEEDS MANUAL CHECK'
];

$diagnostics['tests'][] = [
    'name' => 'Form Structure',
    'check' => 'Is the form properly opened before the input field?',
    'expected' => '<form id="quickCreateForm"> before <input id="quickCreateSummary">',
    'note' => 'Check views/layouts/app.php line 1187 (form open) and line 1210 (input)',
    'status' => 'NEEDS MANUAL CHECK'
];

$diagnostics['tests'][] = [
    'name' => 'Character Counter Script',
    'check' => 'Is the character counter attached to summary field?',
    'expected' => 'JavaScript addEventListener on #quickCreateSummary for "input" event',
    'location' => 'views/layouts/app.php lines 1758-1764',
    'status' => 'NEEDS MANUAL CHECK'
];

$diagnostics['tests'][] = [
    'name' => 'Modal Initialization',
    'check' => 'Is initializeQuickCreateModal() being called?',
    'expected' => 'Function initializeQuickCreateModal() called in attachQuickCreateModalListeners()',
    'location' => 'views/layouts/app.php lines 1888',
    'status' => 'NEEDS MANUAL CHECK'
];

$diagnostics['tests'][] = [
    'name' => 'CSS Issues',
    'check' => 'Is the input field disabled by CSS?',
    'expected' => 'No CSS rule setting display: none or pointer-events: none on #quickCreateSummary',
    'check_for' => [
        'display: none',
        'visibility: hidden',
        'pointer-events: none',
        'user-select: none',
        'opacity: 0'
    ],
    'status' => 'NEEDS MANUAL CHECK'
];

$diagnostics['possible_causes'] = [
    'Cause 1: Form not properly structured' => 'The input is outside the <form> tag - FIX: Move input inside form tag',
    'Cause 2: Character counter blocking input' => 'JavaScript addEventListener is preventing default - FIX: Remove e.preventDefault() if present',
    'Cause 3: CSS disabling input' => 'CSS rule hiding or disabling the field - FIX: Check public/assets/css/app.css for #quickCreateSummary rules',
    'Cause 4: Modal not initializing' => 'attachQuickCreateModalListeners() not being called - FIX: Check line 2200+ for function call',
    'Cause 5: Field is disabled attribute' => 'HTML has disabled or readonly attribute - FIX: Remove disabled/readonly from HTML',
];

$diagnostics['steps_to_diagnose'] = [
    '1. Open Browser DevTools (F12)',
    '2. Go to Console tab',
    '3. Type: document.getElementById("quickCreateSummary").disabled',
    '4. Should return: false (if true, field is disabled)',
    '5. Type: document.getElementById("quickCreateSummary").readOnly',
    '6. Should return: false (if true, field is read-only)',
    '7. Type: document.getElementById("quickCreateSummary").getAttribute("disabled")',
    '8. Should return: null (if not null, field has disabled attribute)',
    '9. Type: document.getElementById("quickCreateSummary").style.display',
    '10. Should return: "" (empty, field is visible)',
];

$diagnostics['steps_to_fix'] = [
    'Step 1: Check HTML structure',
    '  - Open views/layouts/app.php',
    '  - Find: <form id="quickCreateForm"> (line 1187)',
    '  - Find: <input id="quickCreateSummary"> (line 1210)',
    '  - Verify input is INSIDE the form tags',
    '',
    'Step 2: Remove any disabled/readonly attributes',
    '  - If input has disabled or readonly, REMOVE THEM',
    '',
    'Step 3: Check CSS for #quickCreateSummary',
    '  - Search in public/assets/css/app.css for: #quickCreateSummary',
    '  - Remove any CSS that hides or disables the field',
    '',
    'Step 4: Check JavaScript initialization',
    '  - Open Console (F12)',
    '  - Look for "[MODAL-OPEN]" messages when modal opens',
    '  - Look for "✅ initializeQuickCreateModal() complete" message',
    '  - If not present, initialization is not running',
    '',
    'Step 5: Test in browser console',
    '  - Open modal (click Create button)',
    '  - Open DevTools Console (F12 → Console)',
    '  - Type: document.getElementById("quickCreateSummary").focus()',
    '  - Try typing - if it works, there\'s a JavaScript issue',
    '  - If it still doesn\'t work, there\'s an HTML/CSS issue',
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Quick Create Modal - Input Field Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; border-bottom: 2px solid #0052CC; padding-bottom: 10px; }
        h2 { color: #0052CC; margin-top: 20px; }
        .test { margin: 15px 0; padding: 12px; background: #f9f9f9; border-left: 4px solid #ddd; }
        .test.pass { border-left-color: #28a745; background: #f0f7f4; }
        .test.fail { border-left-color: #dc3545; background: #f8f5f6; }
        .test.info { border-left-color: #0052CC; background: #f0f4ff; }
        strong { color: #333; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        ul { margin: 10px 0; padding-left: 20px; }
        li { margin: 5px 0; }
        .step { background: #fffacd; margin: 10px 0; padding: 10px; border-radius: 4px; border-left: 3px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Quick Create Modal - Summary Field Diagnostic</h1>
        <p><strong>Error:</strong> User reports "in summary field nothing is getting typed"</p>
        
        <h2>📋 Test Results</h2>
        
        <?php foreach ($diagnostics['tests'] as $test): ?>
            <div class="test info">
                <strong>Test:</strong> <?= htmlspecialchars($test['name']) ?><br>
                <strong>Check:</strong> <?= htmlspecialchars($test['check']) ?><br>
                <strong>Expected:</strong> <?= htmlspecialchars($test['expected']) ?><br>
                <strong>Location:</strong> <?= htmlspecialchars($test['location'] ?? 'See details') ?><br>
                <strong>Status:</strong> <span style="color: #ff6600;">⚠️ <?= htmlspecialchars($test['status']) ?></span>
            </div>
        <?php endforeach; ?>

        <h2>🔴 Possible Causes</h2>
        <?php foreach ($diagnostics['possible_causes'] as $cause => $fix): ?>
            <div class="test fail">
                <strong>Cause:</strong> <?= htmlspecialchars($cause) ?><br>
                <strong>Fix:</strong> <?= htmlspecialchars($fix) ?>
            </div>
        <?php endforeach; ?>

        <h2>🔧 Diagnostic Steps (Use Browser Console)</h2>
        <ol>
            <?php foreach ($diagnostics['steps_to_diagnose'] as $step): ?>
                <li><code><?= htmlspecialchars($step) ?></code></li>
            <?php endforeach; ?>
        </ol>

        <h2>✅ Fix Steps</h2>
        <?php foreach ($diagnostics['steps_to_fix'] as $step): ?>
            <?php if (trim($step) === ''): ?>
                <br>
            <?php elseif (strpos($step, 'Step') === 0): ?>
                <div class="step"><strong><?= htmlspecialchars($step) ?></strong></div>
            <?php else: ?>
                <div style="margin-left: 20px; font-family: monospace;"><?= htmlspecialchars($step) ?></div>
            <?php endif; ?>
        <?php endforeach; ?>

        <h2>📝 Quick Fixes to Try</h2>
        <ol>
            <li><strong>Clear browser cache:</strong> CTRL+SHIFT+DEL → Clear all → Close browser → Reopen</li>
            <li><strong>Hard refresh:</strong> CTRL+F5 (or CTRL+SHIFT+R)</li>
            <li><strong>Open DevTools:</strong> F12 → Console tab</li>
            <li><strong>Test form input:</strong>
                <ul>
                    <li>Open quick create modal (click Create in navbar)</li>
                    <li>Check console for "[MODAL-OPEN]" and "✅ initializeQuickCreateModal() complete"</li>
                    <li>Try clicking in Summary field and typing</li>
                    <li>If still not working, check console for JavaScript errors (red text)</li>
                </ul>
            </li>
            <li><strong>If JavaScript error found:</strong> Share the error message from console</li>
            <li><strong>Check HTML:</strong> Right-click on Summary field → Inspect → Check for disabled/readonly attributes</li>
        </ol>

        <h2>📊 Diagnostic Summary</h2>
        <div class="test info">
            <p>The quick create modal summary field is not accepting user input. This could be due to:</p>
            <ul>
                <li>Field is disabled via JavaScript (most likely)</li>
                <li>Field has HTML disabled or readonly attribute (unlikely)</li>
                <li>CSS hiding or blocking the field (unlikely)</li>
                <li>Initialization script not running (likely)</li>
            </ul>
            <p><strong>Next Step:</strong> Follow diagnostic steps above using browser console (F12)</p>
        </div>

        <h2>🎯 Root Cause (Most Likely)</h2>
        <div class="test fail">
            <strong>Problem:</strong> The <code>initializeQuickCreateModal()</code> function is attaching event listeners that may be blocking input or the form initialization is not complete when modal opens.<br><br>
            
            <strong>Solution:</strong> 
            <ul>
                <li>Verify <code>attachQuickCreateModalListeners()</code> is being called (check for function call around line 2200+)</li>
                <li>Check that character counter event listener is not calling <code>preventDefault()</code></li>
                <li>Verify form initialization completes before modal show event fires</li>
            </ul>
        </div>

        <hr style="margin: 20px 0;">
        <p><small>Generated: <?= htmlspecialchars($diagnostics['timestamp']) ?></small></p>
    </div>
</body>
</html>
