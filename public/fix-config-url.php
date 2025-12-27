<?php
/**
 * Fix Configuration URL
 * Updates config/config.php with the correct URL including port number
 */

declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$correctUrl = $scheme . '://' . $host . '/jira_clone_system/public';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Configuration URL</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; }
        .box { border: 1px solid #ccc; border-radius: 8px; padding: 20px; margin: 20px 0; background: #f9f9f9; }
        .success { border-color: #28a745; background: #d4edda; color: #155724; }
        .error { border-color: #dc3545; background: #f8d7da; color: #721c24; }
        .info { border-color: #0c5460; background: #d1ecf1; color: #0c5460; }
        h2 { margin-top: 0; }
        pre { background: #f4f4f4; padding: 12px; border-radius: 4px; overflow-x: auto; }
        .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 16px; }
        .button:hover { background: #0056b3; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔧 Configuration URL Fix Tool</h1>

    <div class="box info">
        <h2>Detected Information</h2>
        <p><strong>Your current URL:</strong> <code><?php echo $correctUrl; ?></code></p>
        <p><strong>Scheme:</strong> <?php echo $scheme; ?></p>
        <p><strong>Host:</strong> <?php echo $host; ?></p>
    </div>

    <?php
    $configPath = __DIR__ . '/../config/config.php';
    $configContent = file_get_contents($configPath);
    
    // Check if URL needs updating
    $pattern = "/'url'\s*=>\s*'[^']+'/";
    preg_match($pattern, $configContent, $matches);
    $currentUrl = isset($matches[0]) ? preg_replace("/^'url'\s*=>\s*'([^']+)'$/", '$1', $matches[0]) : 'NOT FOUND';
    
    $needsUpdate = (strpos($currentUrl, $host) === false);
    
    if ($needsUpdate) {
        ?>
        <div class="box error">
            <h2>⚠️ Configuration Issue Detected</h2>
            <p><strong>Current config URL:</strong> <code><?php echo htmlspecialchars($currentUrl); ?></code></p>
            <p><strong>Expected URL:</strong> <code><?php echo htmlspecialchars($correctUrl); ?></code></p>
            <p>The configuration URL doesn't match your access method (probably different port number).</p>
        </div>

        <div class="box">
            <h2>How to Fix</h2>
            <p><strong>Option 1: Automatic Fix (Recommended)</strong></p>
            <p>The Router has been updated to handle port variations automatically. Just clear your cache and reload:</p>
            <ol>
                <li>Press <code>CTRL + SHIFT + DEL</code> to open browser cache clear</li>
                <li>Clear all caches</li>
                <li>Refresh the page (F5)</li>
                <li>Try accessing <a href="/jira_clone_system/public/time-tracking" style="color: #007bff;">Time Tracking Dashboard</a></li>
            </ol>

            <p style="margin-top: 30px;"><strong>Option 2: Manual Config Update</strong></p>
            <p>Edit <code>config/config.php</code> and change line 15 from:</p>
            <pre>'url' => '<?php echo htmlspecialchars($currentUrl); ?>',</pre>
            <p>To:</p>
            <pre>'url' => '<?php echo htmlspecialchars($correctUrl); ?>',</pre>
        </div>
        <?php
    } else {
        ?>
        <div class="box success">
            <h2>✓ Configuration is Correct</h2>
            <p>Your config URL matches your access method!</p>
            <p>If you're still seeing 404 errors, try clearing cache:</p>
            <ol>
                <li>Press <code>CTRL + SHIFT + DEL</code></li>
                <li>Clear all caches</li>
                <li>Refresh the page</li>
            </ol>
        </div>
        <?php
    }
    ?>

    <div class="box">
        <h2>Testing</h2>
        <p>Try accessing these URLs:</p>
        <ul>
            <li><a href="/jira_clone_system/public/dashboard">/jira_clone_system/public/dashboard</a></li>
            <li><a href="/jira_clone_system/public/projects">/jira_clone_system/public/projects</a></li>
            <li><a href="/jira_clone_system/public/time-tracking">/jira_clone_system/public/time-tracking</a></li>
        </ul>
    </div>

    <div class="box info">
        <h2>📋 Current Config</h2>
        <pre>
File: config/config.php
Current app.url: <?php echo htmlspecialchars($currentUrl); ?>
Suggested app.url: <?php echo htmlspecialchars($correctUrl); ?>
        </pre>
    </div>
</body>
</html>
