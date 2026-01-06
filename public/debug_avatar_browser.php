<!DOCTYPE html>
<html>
<head>
    <title>Avatar Browser Debug</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; background: #f9f9f9; }
        .avatar-test { margin: 10px 0; }
        img { border: 2px solid #007bff; margin: 5px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        .debug { background: #f0f0f0; padding: 10px; font-family: monospace; white-space: pre-wrap; }
        .reporter-avatar { width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #6c757d; color: white; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Avatar Browser Behavior Debug</h1>
    
    <div class="test-section">
        <h2>Test 1: Direct Avatar Function</h2>
        <?php
        require_once '../bootstrap/app.php';
        
        // Set proper server variables like in web context
        $_SERVER['HTTP_HOST'] = 'localhost:8080';
        $_SERVER['SCRIPT_NAME'] = '/jira_clone_system/public/index.php';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        
        $db = new \App\Core\Database();
        $user = $db->selectOne('SELECT avatar, display_name FROM users WHERE id = 1');
        
        echo '<div class="debug">';
        echo 'Raw avatar from DB: ' . ($user['avatar'] ?? 'NULL') . PHP_EOL;
        echo 'avatar() result: ' . avatar($user['avatar'] ?? null) . PHP_EOL;
        echo '</div>';
        ?>
        
        <div class="avatar-test">
            <h3>Direct PHP Generated Avatar:</h3>
            <img src="<?= e(avatar($user['avatar'] ?? null)) ?>" width="80" height="80" alt="Direct PHP">
        </div>
    </div>
    
    <div class="test-section">
        <h2>Test 2: JavaScript Data Attribute Simulation</h2>
        
        <!-- Simulate navbar button like in layouts/app.php -->
        <button class="navbar-action-btn user-btn" id="userMenu" title="User menu"
            data-user-name="<?= e($user['display_name']) ?>"
            data-user-avatar="<?= e(avatar($user['avatar'] ?? null)) ?>">
            <img src="<?= e(avatar($user['avatar'] ?? null)) ?>" alt="<?= e($user['display_name']) ?>" class="user-avatar">
            <i class="bi bi-chevron-down"></i>
        </button>
        
        <!-- Simulate reporter avatar element like in create modal -->
        <div class="reporter-avatar" id="reporterAvatar" style="margin-top: 10px;"></div>
        
        <div class="avatar-test">
            <h3>JavaScript Processed Avatar:</h3>
            <div id="jsResult">[JavaScript will update this]</div>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Test 3: URL Resolution Test</h2>
        <div class="debug">
            <h4>URL Generation Tests:</h4>
            avatar('avatar_1_1767008522.png'): <?= avatar('avatar_1_1767008522.png') ?><br>
            url('/uploads/avatars/avatar_1_1767008522.png'): <?= url('/uploads/avatars/avatar_1_1767008522.png') ?><br>
            <br>
            <h4>Manual Tests:</h4>
            <a href="<?= avatar($user['avatar'] ?? null) ?>" target="_blank">Test PHP Avatar URL</a><br>
            <a href="http://localhost:8080/uploads/avatars/avatar_1_1767008522.png" target="_blank" style="color: red;">Test Error URL (should 404)</a>
        </div>
    </div>
    
    <script>
        // Simulate the exact JavaScript from layouts/app.php
        console.log('🔍 Starting avatar debug...');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📝 DOM Content Loaded');
            
            // Extract user data from navbar button
            const userMenuBtn = document.getElementById('userMenu');
            const reporterAvatar = document.getElementById('reporterAvatar');
            const jsResult = document.getElementById('jsResult');
            
            if (userMenuBtn) {
                const userName = userMenuBtn.getAttribute('data-user-name') || 'User';
                const userAvatar = userMenuBtn.getAttribute('data-user-avatar') || '';
                
                console.log('📝 Extracted data:', { userName, userAvatar });
                
                // Get user initials from user name
                const initials = userName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
                
                // Update reporter name (simulated)
                //reporterName.textContent = userName;
                
                // Update reporter avatar
                if (userAvatar && userAvatar.includes('http')) {
                    // If avatar is a valid image URL, show it as background image
                    reporterAvatar.style.backgroundImage = `url(${userAvatar})`;
                    reporterAvatar.style.backgroundSize = 'cover';
                    reporterAvatar.style.backgroundPosition = 'center';
                    reporterAvatar.style.color = 'transparent'; // Hide text
                    reporterAvatar.innerHTML = ''; // Clear any content
                    
                    jsResult.innerHTML = '<span class="success">✅ Avatar set via background-image: ' + userAvatar + '</span>';
                } else if (initials) {
                    // Show initials if no valid avatar URL
                    reporterAvatar.textContent = initials;
                    reporterAvatar.style.backgroundImage = 'none';
                    reporterAvatar.style.color = 'white';
                    
                    jsResult.innerHTML = '<span class="error">❌ No avatar, showing initials: ' + initials + '</span>';
                } else {
                    jsResult.innerHTML = '<span class="error">❌ No avatar data available</span>';
                }
                
                console.log('🎯 Final result:', {
                    dataAttr: userAvatar,
                    hasHttp: userAvatar.includes('http'),
                    initials: initials,
                    finalStyle: reporterAvatar.style.backgroundImage
                });
            } else {
                jsResult.innerHTML = '<span class="error">❌ User menu button not found</span>';
            }
        });
        
        // Check for image load errors
        document.addEventListener('error', function(e) {
            if (e.target.tagName === 'IMG') {
                console.error('❌ Image failed to load:', e.target.src);
                e.target.style.border = '3px solid red';
                e.target.alt = 'FAILED: ' + e.target.src;
            }
        }, true);
        
        // Check for successful image loads
        document.addEventListener('load', function(e) {
            if (e.target.tagName === 'IMG') {
                console.log('✅ Image loaded successfully:', e.target.src);
                e.target.style.border = '3px solid green';
            }
        }, true);
    </script>
</body>
</html>