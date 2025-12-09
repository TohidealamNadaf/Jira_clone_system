<?php
/**
 * Simple test to check projects in the modal
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select2 Dropdown Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        body { padding: 30px; background: #f5f5f5; }
        .card { margin: 20px 0; }
        .success { color: #155724; padding: 15px; background: #d4edda; margin: 20px 0; border-radius: 4px; }
        .error { color: #721c24; padding: 15px; background: #f8d7da; margin: 20px 0; border-radius: 4px; }
        .info { color: #0c5460; padding: 15px; background: #d1ecf1; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>Select2 Dropdown - Test</h3>
            </div>
            <div class="card-body">
                <div class="info">
                    <strong>Test Instructions:</strong>
                    <ol>
                        <li>Go to: <a href="/jira_clone_system/public/dashboard" target="_blank">Dashboard</a></li>
                        <li>Click <strong>"Create"</strong> button (top-right)</li>
                        <li>Look at the <strong>Project dropdown</strong></li>
                        <li>You should see projects list</li>
                        <li>Open <strong>Browser Console (F12)</strong> to check for errors</li>
                    </ol>
                </div>

                <h4 style="margin-top: 30px;">What to Check</h4>
                
                <table class="table">
                    <tr>
                        <th>Check Item</th>
                        <th>What to Look For</th>
                    </tr>
                    <tr>
                        <td>Dropdown Opens</td>
                        <td>Click project field → dropdown should appear</td>
                    </tr>
                    <tr>
                        <td>Projects Visible</td>
                        <td>Should see list of projects with names and keys</td>
                    </tr>
                    <tr>
                        <td>Scrolling Works</td>
                        <td>Use mouse wheel to scroll if many projects</td>
                    </tr>
                    <tr>
                        <td>Search Works</td>
                        <td>Type in dropdown to filter projects</td>
                    </tr>
                    <tr>
                        <td>Selection Works</td>
                        <td>Click a project → it gets selected</td>
                    </tr>
                </table>

                <h4 style="margin-top: 30px;">Browser Console Check (F12)</h4>
                <div class="info">
                    <strong>If projects don't show:</strong>
                    <ol>
                        <li>Press <code>F12</code> to open Developer Tools</li>
                        <li>Go to <strong>Console</strong> tab</li>
                        <li>Look for red error messages</li>
                        <li>Check what the error says</li>
                    </ol>
                </div>

                <h4 style="margin-top: 30px;">Network Check (F12)</h4>
                <div class="info">
                    <strong>To verify API is being called:</strong>
                    <ol>
                        <li>Press <code>F12</code> → Go to <strong>Network</strong> tab</li>
                        <li>Click "Create" button</li>
                        <li>Look for request to <code>/api/v1/projects</code></li>
                        <li>Click it and check the Response tab</li>
                        <li>Should see JSON with project data</li>
                    </ol>
                </div>

                <h4 style="margin-top: 30px;">What If Nothing Shows?</h4>
                <div class="error">
                    <strong>Possible Causes & Fixes:</strong>
                    <ul>
                        <li><strong>No projects in database:</strong>
                            <br>Run: <code>php scripts/verify-and-seed.php</code>
                            <br>This will create sample projects
                        </li>
                        <li><strong>JavaScript error:</strong>
                            <br>Check browser console (F12) for red errors
                            <br>Look for Select2 or jQuery errors
                        </li>
                        <li><strong>API not working:</strong>
                            <br>Go to: <code>/api/v1/projects</code>
                            <br>Should return JSON list of projects
                        </li>
                        <li><strong>Cache issue:</strong>
                            <br>Hard refresh: <code>Ctrl+F5</code>
                            <br>Clear browser cache
                        </li>
                    </ul>
                </div>

                <h4 style="margin-top: 30px;">Test Dropdown Below</h4>
                <div style="padding: 20px; background: white; border: 1px solid #ddd; border-radius: 4px;">
                    <label for="testSelect" class="form-label"><strong>Test Select2 Dropdown:</strong></label>
                    <select id="testSelect" class="form-select" style="max-width: 400px;">
                        <option value="">Select an option...</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                        <option value="4">Option 4</option>
                    </select>
                    <small class="form-text text-muted">This is a test to verify Select2 CSS/JS are loaded. Try scrolling and typing.</small>
                </div>

                <h4 style="margin-top: 30px;">Quick Links</h4>
                <div style="padding: 20px; background: #f9f9f9; border-radius: 4px;">
                    <a href="/jira_clone_system/public/dashboard" class="btn btn-primary">Go to Dashboard</a>
                    <a href="/jira_clone_system/public/api/v1/projects" class="btn btn-info">Check Projects API</a>
                    <a href="javascript:location.reload();" class="btn btn-secondary">Reload Page</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-info text-white">
                <h4>Summary</h4>
            </div>
            <div class="card-body">
                <p><strong>The Select2 dropdown should be working now with these fixes:</strong></p>
                <ul>
                    <li>✓ Select2 CSS loaded</li>
                    <li>✓ jQuery library loaded</li>
                    <li>✓ Select2 JavaScript loaded</li>
                    <li>✓ Initialization code added</li>
                    <li>✓ Project refresh trigger added</li>
                </ul>
                <p style="margin-top: 20px;">
                    <strong>Next Step:</strong> Go to <a href="/jira_clone_system/public/dashboard">Dashboard</a> 
                    and click "Create" to test the dropdown.
                </p>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Initialize Select2 for test dropdown
        $(document).ready(function() {
            $('#testSelect').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            
            console.log('✓ Select2 test dropdown initialized');
            console.log('✓ jQuery version: ' + jQuery.fn.jquery);
            console.log('✓ Select2 ready');
        });
    </script>
</body>
</html>
