<?php
/**
 * Test Roadmap Item Submission
 * Tests if the POST request to /projects/{key}/roadmap works
 */

declare(strict_types=1);

// Start session and load bootstrap
session_start();
require_once __DIR__ . '/bootstrap/autoload.php';

// Set up a test POST request
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Roadmap Submission</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #8b1956; }
        button { background: #8b1956; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #6f123f; }
        #response { background: #e8f4f8; padding: 15px; border-radius: 4px; margin-top: 20px; white-space: pre-wrap; font-family: monospace; }
        .error { color: #d32f2f; }
        .success { color: #388e3c; }
        .info { color: #1976d2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Roadmap Submission</h1>
        
        <div class="section">
            <h3>Test 1: Simple POST Request</h3>
            <p>This will test a basic POST request to create a roadmap item</p>
            <button onclick="testSimplePost()">Run Test 1</button>
        </div>

        <div class="section">
            <h3>Test 2: Check Request Handler</h3>
            <p>Check if the controller correctly detects JSON requests</p>
            <button onclick="testRequestDetection()">Run Test 2</button>
        </div>

        <div class="section">
            <h3>Test 3: Validation Check</h3>
            <p>Test validation of required fields</p>
            <button onclick="testValidation()">Run Test 3</button>
        </div>

        <div id="response"></div>
    </div>

    <script>
        const PROJECT_KEY = 'CWAYS';  // Match the project key in your system
        const CSRF_TOKEN = '<?= csrf_token() ?? "missing" ?>';
        
        function log(title, content) {
            const responseDiv = document.getElementById('response');
            const lines = responseDiv.textContent ? responseDiv.textContent + '\n\n' : '';
            responseDiv.textContent = lines + `[${new Date().toLocaleTimeString()}] ${title}\n${JSON.stringify(content, null, 2)}`;
            responseDiv.scrollTop = responseDiv.scrollHeight;
        }

        async function testSimplePost() {
            log('Test 1: Simple POST Request', 'Starting...');
            
            const payload = {
                title: 'Test Roadmap Item ' + Date.now(),
                description: 'Testing roadmap submission',
                type: 'feature',
                status: 'planned',
                start_date: new Date().toISOString().split('T')[0],
                end_date: new Date(Date.now() + 86400000).toISOString().split('T')[0],
                progress: 0
            };

            try {
                const response = await fetch(`<?= url('/projects/') ?>${PROJECT_KEY}/roadmap`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': CSRF_TOKEN
                    },
                    body: JSON.stringify(payload)
                });

                log('Response Status', { status: response.status, statusText: response.statusText });

                // Try to parse as JSON
                const contentType = response.headers.get('content-type');
                let result;
                
                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    result = await response.text();
                }

                log('Response Body', result);

                if (response.ok || response.status === 302) {
                    log('✅ SUCCESS', 'Request submitted successfully. Reload page to see new item.');
                } else {
                    log('❌ ERROR', 'Request failed with status ' + response.status);
                }
            } catch (error) {
                log('❌ FETCH ERROR', error.message);
            }
        }

        async function testRequestDetection() {
            log('Test 2: Request Detection', 'Testing how the server detects JSON requests...');
            
            const simplePost = await fetch(`<?= url('/projects/') ?>${PROJECT_KEY}/roadmap`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': CSRF_TOKEN
                },
                body: JSON.stringify({ test: true })
            });

            log('JSON Content-Type Request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                status: simplePost.status
            });
        }

        async function testValidation() {
            log('Test 3: Validation', 'Testing field validation...');

            const tests = [
                { name: 'Missing title', data: { type: 'feature', status: 'planned', start_date: '2025-01-01', end_date: '2025-01-02' } },
                { name: 'Missing type', data: { title: 'Test', status: 'planned', start_date: '2025-01-01', end_date: '2025-01-02' } },
                { name: 'Invalid progress', data: { title: 'Test', type: 'feature', status: 'planned', start_date: '2025-01-01', end_date: '2025-01-02', progress: 150 } }
            ];

            for (const test of tests) {
                const response = await fetch(`<?= url('/projects/') ?>${PROJECT_KEY}/roadmap`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': CSRF_TOKEN
                    },
                    body: JSON.stringify(test.data)
                });

                const contentType = response.headers.get('content-type');
                let body = contentType?.includes('json') ? await response.json() : await response.text();

                log(`${test.name} (Status: ${response.status})`, body);
            }
        }
    </script>
</body>
</html>
