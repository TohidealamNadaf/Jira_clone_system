<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Dashboard') ?> - <?= e(config('app.name')) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    
    <!-- Apply theme colors from settings -->
    <?php 
        $primaryColor = \App\Core\Database::selectValue("SELECT value FROM settings WHERE `key` = 'primary_color'") ?? '#0052CC';
        $defaultTheme = \App\Core\Database::selectValue("SELECT value FROM settings WHERE `key` = 'default_theme'") ?? 'light';
    ?>
    <style>
        :root {
            --jira-blue: <?= e($primaryColor) ?>;
            --jira-blue-dark: color-mix(in srgb, <?= e($primaryColor) ?> 80%, black);
            --jira-blue-light: color-mix(in srgb, <?= e($primaryColor) ?> 80%, white);
            --jira-blue-lighter: color-mix(in srgb, <?= e($primaryColor) ?> 15%, white);
        }
        
        /* Primary backgrounds */
        .bg-primary,
        .navbar.bg-primary {
            background-color: var(--jira-blue) !important;
        }
        
        /* Primary buttons */
        .btn-primary {
            background-color: var(--jira-blue) !important;
            border-color: var(--jira-blue) !important;
        }
        
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--jira-blue-dark) !important;
            border-color: var(--jira-blue-dark) !important;
        }
        
        .btn-outline-primary {
            color: var(--jira-blue) !important;
            border-color: var(--jira-blue) !important;
        }
        
        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background-color: var(--jira-blue) !important;
            border-color: var(--jira-blue) !important;
            color: white !important;
        }
        
        /* Links */
        a:not(.btn):not(.nav-link):not(.dropdown-item):not(.list-group-item) {
            color: var(--jira-blue);
        }
        
        a:not(.btn):not(.nav-link):not(.dropdown-item):not(.list-group-item):hover {
            color: var(--jira-blue-dark);
        }
        
        /* Text primary */
        .text-primary {
            color: var(--jira-blue) !important;
        }
        
        /* Border primary */
        .border-primary {
            border-color: var(--jira-blue) !important;
        }
        
        /* Focus states */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--jira-blue);
            box-shadow: 0 0 0 0.2rem var(--jira-blue-lighter);
        }
        
        /* Checkbox and switch */
        .form-check-input:checked {
            background-color: var(--jira-blue);
            border-color: var(--jira-blue);
        }
        
        /* Progress bar */
        .progress-bar {
            background-color: var(--jira-blue);
        }
        
        /* Pagination */
        .page-link.active,
        .page-item.active .page-link {
            background-color: var(--jira-blue);
            border-color: var(--jira-blue);
        }
        
        .page-link {
            color: var(--jira-blue);
        }
        
        /* Nav tabs and pills */
        .nav-link.active,
        .nav-pills .nav-link.active {
            background-color: var(--jira-blue);
        }
        
        .nav-tabs .nav-link.active {
            border-bottom-color: var(--jira-blue);
            color: var(--jira-blue);
            background-color: transparent;
        }
        
        /* Badges */
        .badge.bg-primary {
            background-color: var(--jira-blue) !important;
        }
        
        /* Dropdown active items */
        .dropdown-item.active,
        .dropdown-item:active {
            background-color: var(--jira-blue);
        }
    </style>
    
    <?php \App\Core\View::yield('styles') ?>
</head>
<body>
    <!-- Navigation - Enterprise Jira-Like Design -->
    <nav class="navbar-enterprise sticky-top" style="z-index: 2000;">
        <div class="navbar-container">
            <!-- Left Section: Brand & Primary Menu -->
            <div class="navbar-left">
                <!-- Brand -->
                <a class="navbar-brand" href="<?= url('/') ?>">
                    <i class="bi bi-kanban"></i>
                    <span class="brand-text"><?= e(config('app.name')) ?></span>
                </a>
                
                <!-- Primary Navigation -->
                <div class="navbar-menu">
                    <!-- Projects -->
                    <div class="nav-dropdown">
                        <button class="nav-dropdown-btn">
                            <i class="bi bi-folder"></i>
                            <span>Projects</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-panel">
                            <a href="<?= url('/projects') ?>" class="dropdown-item">
                                <i class="bi bi-collection"></i>
                                <div class="item-content">
                                    <div class="item-title">View All Projects</div>
                                    <div class="item-desc">Browse all projects</div>
                                </div>
                            </a>
                            <?php if (can('create-projects')): ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?= url('/projects/create') ?>" class="dropdown-item">
                                <i class="bi bi-plus-circle"></i>
                                <div class="item-content">
                                    <div class="item-title">Create Project</div>
                                    <div class="item-desc">Start a new project</div>
                                </div>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Issues -->
                    <div class="nav-dropdown">
                        <button class="nav-dropdown-btn">
                            <i class="bi bi-list-task"></i>
                            <span>Issues</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-panel">
                            <a href="<?= url('/search?assignee=currentUser()') ?>" class="dropdown-item">
                                <i class="bi bi-person-check"></i>
                                <div class="item-content">
                                    <div class="item-title">Assigned to Me</div>
                                    <div class="item-desc">Your tasks</div>
                                </div>
                            </a>
                            <a href="<?= url('/search?reporter=currentUser()') ?>" class="dropdown-item">
                                <i class="bi bi-person-fill"></i>
                                <div class="item-content">
                                    <div class="item-title">Reported by Me</div>
                                    <div class="item-desc">Your reports</div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= url('/search') ?>" class="dropdown-item">
                                <i class="bi bi-search"></i>
                                <div class="item-content">
                                    <div class="item-title">Search Issues</div>
                                    <div class="item-desc">Find issues</div>
                                </div>
                            </a>
                            <a href="<?= url('/filters') ?>" class="dropdown-item">
                                <i class="bi bi-funnel"></i>
                                <div class="item-content">
                                    <div class="item-title">Saved Filters</div>
                                    <div class="item-desc">Your filters</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Reports -->
                    <div class="nav-dropdown">
                        <button class="nav-dropdown-btn">
                            <i class="bi bi-bar-chart"></i>
                            <span>Reports</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-panel">
                            <a href="<?= url('/reports') ?>" class="dropdown-item">
                                <i class="bi bi-graph-up"></i>
                                <div class="item-content">
                                    <div class="item-title">All Reports</div>
                                    <div class="item-desc">View analytics</div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= url('/reports/burndown') ?>" class="dropdown-item">
                                <i class="bi bi-graph-down"></i>
                                <div class="item-content">
                                    <div class="item-title">Burndown</div>
                                    <div class="item-desc">Sprint progress</div>
                                </div>
                            </a>
                            <a href="<?= url('/reports/velocity') ?>" class="dropdown-item">
                                <i class="bi bi-speedometer2"></i>
                                <div class="item-content">
                                    <div class="item-title">Velocity</div>
                                    <div class="item-desc">Team metrics</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Admin -->
                    <?php if ($user['is_admin'] ?? false): ?>
                    <a href="<?= url('/admin') ?>" class="nav-link-simple">
                        <i class="bi bi-gear"></i>
                        <span>Admin</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Section: Actions -->
            <div class="navbar-right">
                <!-- Search Box -->
                <form class="search-box d-none d-lg-flex" action="<?= url('/search') ?>" method="GET">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Search issues..." autocomplete="off">
                </form>
                
                <!-- Quick Create Button -->
                <button class="navbar-action-btn create-btn" data-bs-toggle="modal" data-bs-target="#quickCreateModal" title="Create issue">
                    <i class="bi bi-plus-lg"></i>
                    <span class="d-none d-md-inline">Create</span>
                </button>
                
                <!-- Notifications -->
                <div class="navbar-action-dropdown">
                    <button class="navbar-action-btn notification-btn" id="notificationBell" title="Notifications">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge" id="unreadBadge" style="display: none;">0</span>
                    </button>
                    <div class="dropdown-panel notification-panel" id="notificationDropdown">
                        <div class="panel-header">
                            <i class="bi bi-bell-fill"></i>
                            <span>Notifications</span>
                        </div>
                        <div class="panel-content" id="notificationList">
                            <div class="text-center text-muted">Loading...</div>
                        </div>
                        <a href="<?= url('/notifications') ?>" class="panel-footer">View All</a>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="navbar-action-dropdown">
                    <button class="navbar-action-btn user-btn" id="userMenu" title="User menu">
                        <img src="<?= e($user['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['name'] ?? 'User')) ?>" 
                             alt="<?= e($user['name'] ?? 'User') ?>" class="user-avatar">
                        <i class="bi bi-chevron-down d-none d-md-inline"></i>
                    </button>
                    <div class="dropdown-panel user-panel">
                        <div class="panel-header user-header">
                            <img src="<?= e($user['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['name'] ?? 'User')) ?>" 
                                 alt="<?= e($user['name'] ?? 'User') ?>" class="user-avatar-large">
                            <div>
                                <div class="user-name"><?= e($user['name'] ?? 'User') ?></div>
                                <div class="user-email"><?= e($user['email'] ?? '') ?></div>
                            </div>
                        </div>
                        <a href="<?= url('/profile') ?>" class="dropdown-item">
                            <i class="bi bi-person-circle"></i>
                            <span>Profile</span>
                        </a>
                        <a href="<?= url('/profile/settings') ?>" class="dropdown-item">
                            <i class="bi bi-gear"></i>
                            <span>Settings</span>
                        </a>
                        <a href="<?= url('/profile/notifications') ?>" class="dropdown-item">
                            <i class="bi bi-bell"></i>
                            <span>Notification Preferences</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="<?= url('/logout') ?>" method="POST">
                            <?= csrf_field() ?>
                            <button type="submit" class="dropdown-item danger">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle d-lg-none" id="mobileMenuToggle" title="Menu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Navbar Styles - Enterprise Design -->
    <style>
    /* Navbar Container */
    .navbar-enterprise {
        position: relative;
        background: #FFFFFF;
        border-bottom: 1px solid #DFE1E6;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13);
        height: 60px;
        display: flex;
        align-items: center;
        padding: 0;
        margin: 0;
    }
    
    .navbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        height: 100%;
        padding: 0 20px;
        gap: 20px;
    }
    
    /* Left Section */
    .navbar-left {
        display: flex;
        align-items: center;
        gap: 32px;
        flex: 1;
        min-width: 0;
    }
    
    /* Brand */
    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: #0052CC;
        font-weight: 600;
        font-size: 16px;
        white-space: nowrap;
        flex-shrink: 0;
        transition: all 0.2s ease;
        line-height: 1;
    }
    
    .navbar-brand:hover {
        color: #003DA5;
    }
    
    .navbar-brand i {
        font-size: 22px;
        display: flex;
        align-items: center;
    }
    
    .brand-text {
        display: none;
    }
    
    @media (min-width: 992px) {
        .brand-text {
            display: inline;
        }
    }
    
    /* Navigation Menu */
    .navbar-menu {
        display: none;
        flex-direction: row;
        gap: 8px;
        align-items: center;
        flex: 1;
        min-width: 0;
    }
    
    @media (min-width: 992px) {
        .navbar-menu {
            display: flex;
        }
    }
    
    /* Nav Dropdown */
    .nav-dropdown {
        position: relative;
    }
    
    .nav-dropdown-btn,
    .nav-link-simple {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: none;
        border: none;
        color: #626F86;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.2s ease;
        white-space: nowrap;
        text-decoration: none;
    }
    
    .nav-dropdown-btn {
        justify-content: space-between;
        width: 100%;
    }
    
    .nav-dropdown-btn:hover,
    .nav-link-simple:hover {
        background-color: #F7F8FA;
        color: #0052CC;
    }
    
    .nav-dropdown-btn i:last-child {
        font-size: 12px;
        margin-left: 4px;
    }
    
    /* Dropdown Panel */
    .dropdown-panel {
        position: absolute;
        top: 100%;
        left: 0;
        background: #FFFFFF;
        border: 1px solid #DFE1E6;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(9, 30, 66, 0.15);
        min-width: 280px;
        margin-top: 8px;
        display: none;
        flex-direction: column;
        z-index: 1000;
        overflow: hidden;
    }
    
    .nav-dropdown:hover .dropdown-panel,
    .navbar-action-dropdown:hover .dropdown-panel {
        display: flex;
    }
    
    .panel-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: #F7F8FA;
        border-bottom: 1px solid #DFE1E6;
        font-size: 13px;
        font-weight: 600;
        color: #161B22;
    }
    
    .panel-header i {
        color: #0052CC;
        font-size: 16px;
    }
    
    /* Dropdown Items */
    .dropdown-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 16px;
        color: #161B22;
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        width: 100%;
        text-align: left;
    }
    
    .dropdown-item:hover {
        background-color: #F7F8FA;
        color: #0052CC;
    }
    
    .dropdown-item i {
        flex-shrink: 0;
        width: 20px;
        font-size: 16px;
    }
    
    .item-content {
        flex: 1;
        min-width: 0;
    }
    
    .item-title {
        font-weight: 500;
        margin-bottom: 2px;
    }
    
    .item-desc {
        font-size: 12px;
        color: #626F86;
    }
    
    .dropdown-divider {
        height: 1px;
        background: #DFE1E6;
        margin: 4px 0;
    }
    
    .panel-footer {
        display: block;
        padding: 10px 16px;
        text-align: center;
        border-top: 1px solid #DFE1E6;
        color: #0052CC;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .panel-footer:hover {
        background-color: #F7F8FA;
    }
    
    /* Right Section */
    .navbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    
    /* Search Box */
    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #F7F8FA;
        border: 1px solid #DFE1E6;
        border-radius: 6px;
        padding: 0 12px;
        height: 36px;
        flex: 0 1 200px;
    }
    
    .search-box i {
        color: #626F86;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .search-box input {
        background: none;
        border: none;
        outline: none;
        color: #161B22;
        font-size: 13px;
        flex: 1;
        min-width: 0;
    }
    
    .search-box input::placeholder {
        color: #626F86;
    }
    
    .search-box:focus-within {
        background: #FFFFFF;
        border-color: #0052CC;
        box-shadow: 0 0 0 2px rgba(0, 82, 204, 0.1);
    }
    
    /* Action Buttons */
    .navbar-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 12px;
        background: none;
        border: none;
        color: #626F86;
        cursor: pointer;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
        white-space: nowrap;
        height: 36px;
    }
    
    .navbar-action-btn:hover {
        background-color: #F7F8FA;
        color: #161B22;
    }
    
    .navbar-action-btn i {
        font-size: 16px;
    }
    
    .create-btn {
        background: #0052CC;
        color: #FFFFFF;
    }
    
    .create-btn:hover {
        background: #003DA5;
        color: #FFFFFF;
    }
    
    /* Notification Badge */
    .notification-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 4px;
        background: #ED3C32;
        color: #FFFFFF;
        border-radius: 9px;
        font-size: 10px;
        font-weight: 600;
    }
    
    /* Notification Dropdown */
    .navbar-action-dropdown {
        position: relative;
    }
    
    .notification-panel {
        right: 0 !important;
        left: auto !important;
        min-width: 320px;
    }
    
    .panel-content {
        padding: 0;
        max-height: 360px;
        overflow-y: auto;
        flex: 1;
    }
    
    .panel-content .dropdown-item {
        border-bottom: 1px solid #F1F2F4;
    }
    
    .panel-content .dropdown-item:last-child {
        border-bottom: none;
    }
    
    /* User Panel */
    .user-panel {
        right: 0 !important;
        left: auto !important;
        min-width: 260px;
    }
    
    .user-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #FFFFFF;
    }
    
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    
    .user-avatar-large {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    
    .user-name {
        font-weight: 600;
        color: #161B22;
        font-size: 14px;
    }
    
    .user-email {
        font-size: 12px;
        color: #626F86;
    }
    
    .dropdown-item.danger:hover {
        background: #FFECEB;
        color: #AE2A19;
    }
    
    /* Mobile Menu Toggle */
    .mobile-menu-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: none;
        border: none;
        color: #626F86;
        cursor: pointer;
        font-size: 20px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .mobile-menu-toggle:hover {
        background-color: #F7F8FA;
        color: #161B22;
    }
    
    /* Responsive */
    @media (max-width: 991px) {
        .navbar-container {
            padding: 0 16px;
            gap: 12px;
        }
        
        .navbar-left {
            gap: 16px;
        }
        
        .search-box {
            display: none;
        }
        
        .brand-text {
            display: none;
        }
    }
    </style>
    
    <!-- Notification JavaScript -->
    <script>
    // Load notifications on bell click
    document.getElementById('notificationBell').addEventListener('click', function(e) {
        if (this.getAttribute('aria-expanded') === 'true') {
            loadNotifications();
        }
    });

    function loadNotifications() {
        const appUrl = '<?= url("/") ?>';
        fetch(appUrl + 'api/v1/notifications?limit=5', {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            const unreadBadge = document.getElementById('unreadBadge');
            const notificationList = document.getElementById('notificationList');
            
            // Update badge
            if (data.unread_count > 0) {
                unreadBadge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                unreadBadge.style.display = 'inline-block';
            } else {
                unreadBadge.style.display = 'none';
            }
            
            // Update notification list
            if (!data.data || data.data.length === 0) {
                notificationList.innerHTML = '<div class="px-3 py-3 text-center text-muted"><small>No notifications</small></div>';
                return;
            }
            
            notificationList.innerHTML = data.data.map(n => `
                <a href="${n.action_url || '#'}" class="dropdown-item d-flex align-items-start gap-2 py-2" style="text-decoration: none;">
                    <div style="flex: 1; border-left: 3px solid ${n.is_read ? 'transparent' : 'var(--jira-blue)'}; padding-left: 8px;">
                        <div class="small fw-semibold text-dark">${escapeHtml(n.title)}</div>
                        <div class="text-muted" style="font-size: 12px;">
                            ${n.message ? escapeHtml(n.message).substring(0, 60) + '...' : ''}
                        </div>
                        <div class="text-muted" style="font-size: 11px; margin-top: 4px;">
                            ${formatTime(n.created_at)}
                        </div>
                    </div>
                    ${!n.is_read ? '<span class="badge bg-primary ms-2">New</span>' : ''}
                </a>
            `).join('');
        })
        .catch(err => {
            console.error('Error loading notifications:', err);
            document.getElementById('notificationList').innerHTML = '<div class="px-3 py-3 text-center text-danger"><small>Error loading notifications</small></div>';
        });
    }

    // Initial load
    loadNotifications();
    
    // Refresh every 30 seconds
    setInterval(loadNotifications, 30000);

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);

        if (minutes < 1) return 'Just now';
        if (minutes < 60) return minutes + 'm ago';
        if (hours < 24) return hours + 'h ago';
        if (days < 7) return days + 'd ago';
        
        return date.toLocaleDateString();
    }
    </script>
    
    <!-- Flash Messages -->
    <?php foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $type => $class): ?>
    <?php if ($message = $flash[$type] ?? null): ?>
    <div class="alert alert-<?= $class ?> alert-dismissible fade show m-3" role="alert">
        <?= e($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
    
    <!-- Main Content -->
    <main class="py-4" id="mainContent">
        <?= \App\Core\View::yield('content') ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-light border-top mt-auto" style="padding: 0.5rem 0;">
        <div class="container-fluid">
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #626F86; padding: 0 1rem;">
                <span>&copy; <?= date('Y') ?> <?= e(config('app.name')) ?>. All rights reserved.</span>
                <span>Version 1.0.0</span>
            </div>
        </div>
    </footer>
    
    <!-- Quick Create Modal -->
    <div class="modal fade" id="quickCreateModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content quick-create-modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Issue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickCreateForm" class="quick-create-form">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" name="project_id" required id="quickCreateProject">
                                <option value="">Loading projects...</option>
                            </select>
                            <small class="form-text text-muted d-block mt-1">Select a project to create issue in</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Issue Type <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" name="issue_type_id" required id="quickCreateIssueType">
                                <option value="">Select a project first...</option>
                            </select>
                            <small class="form-text text-muted d-block mt-1">Select the type of issue</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Summary <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" name="summary" required placeholder="Brief description of the issue" maxlength="500">
                            <small class="form-text text-muted d-block mt-1">Maximum 500 characters</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-lg" onclick="submitQuickCreate()" id="quickCreateBtn">
                        <i class="bi bi-plus-lg me-1"></i> Create
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Backdrop Overlay -->
    <div id="modalBackdrop"></div>
    
    <!-- Embedded projects data for quick create modal -->
    <script type="application/json" id="quickCreateProjectsData">
    []
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- App JS -->
    <script>
        // Close navbar when clicking navigation links
        document.querySelectorAll('.navbar-collapse .dropdown-item, .navbar-collapse .nav-link:not([data-bs-toggle])').forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    const collapseButton = document.querySelector('.navbar-toggler');
                    collapseButton.click();
                }
            });
        });

        // Close navbar when modal opens
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const collapseButton = document.querySelector('.navbar-toggler');
                    collapseButton.click();
                }
            });
        });

        // CSRF Token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // AJAX helper
        async function api(url, options = {}) {
            const defaults = {
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            };
            
            const config = { ...defaults, ...options };
            if (options.headers) {
                config.headers = { ...defaults.headers, ...options.headers };
            }
            
            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            return response.json();
        }
        
        // Tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
        
        // Initialize Select2 for select elements
        function initializeSelect2() {
            // Initialize Project select with Select2
            $('#quickCreateProject').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select a project...',
                allowClear: false,
                dropdownParent: $('#quickCreateModal'),
                maximumResultsForSearch: Infinity,
                language: {
                    noResults: function() {
                        return 'No projects found';
                    }
                }
            });
            
            // Initialize Issue Type select with Select2
            $('#quickCreateIssueType').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select an issue type...',
                allowClear: false,
                dropdownParent: $('#quickCreateModal'),
                language: {
                    noResults: function() {
                        return 'No issue types found';
                    }
                }
            });
        }

        // Load projects on modal open - always fetch fresh from endpoint (works on all pages)
        let projectsLoading = false;
        
        document.getElementById('quickCreateModal').addEventListener('show.bs.modal', async function() {
            // Initialize Select2 if not already done
            if (!$('#quickCreateProject').hasClass('select2-hidden-accessible')) {
                initializeSelect2();
            }
            
            const projectSelect = document.getElementById('quickCreateProject');
            
            // Always reload projects to ensure fresh data on all pages
            projectsLoading = true;
            console.log('Loading projects for quick create modal...');
            
            try {
                let projects = [];
                
                // Always fetch from dedicated endpoint (works on all pages)
                const quickCreateUrl = document.location.pathname.includes('/jira_clone_system/public')
                    ? '/jira_clone_system/public/projects/quick-create-list'
                    : '/projects/quick-create-list';
                
                console.log('Fetching from:', quickCreateUrl);
                
                const response = await fetch(quickCreateUrl, {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('Data received:', data);
                
                // Handle paginated response
                if (data.items && Array.isArray(data.items)) {
                    projects = data.items;
                    console.log('Got', projects.length, 'projects from items array');
                } else if (Array.isArray(data)) {
                    projects = data;
                    console.log('Got', projects.length, 'projects directly');
                } else {
                    console.warn('Unexpected response format:', data);
                    projects = [];
                }
                
                // Populate select with projects and update projectsMap
                projectSelect.innerHTML = '<option value="">Select Project...</option>';
                
                if (projects.length > 0) {
                    console.log('Adding', projects.length, 'projects to dropdown');
                    projects.forEach(project => {
                        console.log('Project:', project.name, 'Issue types count:', project.issue_types ? project.issue_types.length : 0);
                        
                        // Add to dropdown
                        const option = document.createElement('option');
                        option.value = project.id;
                        option.dataset.projectKey = project.key;
                        option.textContent = `${project.name} (${project.key})`;
                        projectSelect.appendChild(option);
                        
                        // Add to projectsMap for issue type lookup
                        projectsMap[project.id] = {
                            id: project.id,
                            key: project.key,
                            name: project.name,
                            issue_types: project.issue_types || []
                        };
                        console.log('Added to projectsMap[' + project.id + ']:', projectsMap[project.id]);
                    });
                } else {
                    console.warn('No projects returned');
                    projectSelect.innerHTML = '<option value="">No projects available</option>';
                }
                
                // Refresh Select2 to show options
                $('#quickCreateProject').trigger('change');
                console.log('Projects loaded successfully');
                
            } catch (error) {
                console.error('Failed to load projects:', error);
                projectSelect.innerHTML = '<option value="">Error loading projects</option>';
                $('#quickCreateProject').trigger('change');
            } finally {
                projectsLoading = false;
            }
        });
        
        // Store project details fetched from API (populated when modal opens)
        let projectsMap = {};
        
        // Load issue types when project changes (using Select2 change event)
        $('#quickCreateProject').on('change', async function() {
            const projectId = this.value;
            const issueTypeSelect = document.getElementById('quickCreateIssueType');
            
            console.log('🔄 Project changed to:', projectId);
            console.log('🗺️ ProjectsMap keys:', Object.keys(projectsMap));
            console.log('📍 Project data:', projectsMap[projectId]);
            
            if (!projectId) {
                $(issueTypeSelect).html('<option value="">Select a project first...</option>');
                $('#quickCreateIssueType').val(null).trigger('change');
                return;
            }
            
            // First try projectsMap
            let issueTypes = [];
            const project = projectsMap[projectId];
            
            console.log('Project from map:', project);
            console.log('Issue types from map:', project ? project.issue_types : 'no project');
            
            if (project && project.issue_types && Array.isArray(project.issue_types) && project.issue_types.length > 0) {
                console.log('✓ Using issue types from projectsMap, count:', project.issue_types.length);
                issueTypes = project.issue_types;
            } else {
                // If not in map or empty, fetch from API
                console.log('📡 Fetching issue types from API...');
                try {
                    const projectKey = document.querySelector('#quickCreateProject option:checked')?.dataset?.projectKey;
                    if (!projectKey) {
                        throw new Error('Project key not found');
                    }
                    
                    const apiUrl = document.location.pathname.includes('/jira_clone_system/public')
                        ? `/jira_clone_system/public/projects/${projectKey}`
                        : `/projects/${projectKey}`;
                    
                    console.log('Fetching from:', apiUrl);
                    
                    const response = await fetch(apiUrl, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Error:', errorText);
                        throw new Error(`HTTP ${response.status}`);
                    }
                    
                    const projectData = await response.json();
                    console.log('📦 API Response:', projectData);
                    
                    issueTypes = projectData.issue_types || [];
                    console.log('✓ Fetched', issueTypes.length, 'issue types from API');
                    
                    if (issueTypes.length === 0) {
                        console.warn('⚠️ No issue types returned from API for project:', projectKey);
                    }
                } catch (error) {
                    console.error('❌ Failed to fetch issue types:', error);
                    issueTypes = [];
                }
            }
            
            // Clear and populate issue type options
            issueTypeSelect.innerHTML = '<option value="">Select issue type...</option>';
            
            if (issueTypes.length > 0) {
                console.log('Adding', issueTypes.length, 'issue types to dropdown');
                issueTypes.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.id;
                    option.textContent = type.name;
                    issueTypeSelect.appendChild(option);
                });
                console.log('✓ Issue types added to DOM');
            } else {
                issueTypeSelect.innerHTML = '<option value="">No issue types available</option>';
                console.warn('No issue types found for project:', projectId);
            }
            
            // Destroy and reinitialize Select2 to refresh options
            $('#quickCreateIssueType').select2('destroy');
            $('#quickCreateIssueType').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select an issue type...',
                allowClear: false,
                dropdownParent: $('#quickCreateModal'),
                language: {
                    noResults: function() {
                        return 'No issue types found';
                    }
                }
            });
            $('#quickCreateIssueType').val(null).trigger('change');
            console.log('✓ Select2 reinitialized for issue types');
        });
        
        // Quick Create
        async function submitQuickCreate() {
            const form = document.getElementById('quickCreateForm');
            
            if (!form.reportValidity()) {
                return;
            }
            
            const formData = new FormData(form);
            const btn = document.getElementById('quickCreateBtn');
            const originalText = btn.innerHTML;
            
            try {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating...';
                
                const data = Object.fromEntries(formData);
                console.log('Creating issue with data:', data);
                
                // Get project key from selected project
                const projectSelect = document.getElementById('quickCreateProject');
                const projectKey = projectSelect.options[projectSelect.selectedIndex].dataset.projectKey;
                
                // Create issue using web endpoint (uses session auth)
                const webUrl = document.location.pathname.includes('/jira_clone_system/public')
                    ? `/jira_clone_system/public/projects/${projectKey}/issues`
                    : `/projects/${projectKey}/issues`;
                
                const response = await fetch(webUrl, {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(data),
                });
                
                console.log('Create response status:', response.status);
                console.log('Create response content-type:', response.headers.get('content-type'));
                
                const responseText = await response.text();
                console.log('Raw response text:', responseText);
                
                if (!response.ok) {
                    console.error('Error response body:', responseText);
                    try {
                        const errorData = JSON.parse(responseText);
                        throw new Error(errorData.error || `HTTP ${response.status}`);
                    } catch (e) {
                        throw new Error(`HTTP ${response.status}: ${responseText.substring(0, 200)}`);
                    }
                }
                
                const result = JSON.parse(responseText);
                console.log('Issue created:', result);
                
                // Get issue key from response - could be at result.issue_key or result.issue.issue_key
                const issueKey = result.issue_key || (result.issue && result.issue.issue_key);
                
                if (issueKey) {
                    // Redirect to the new issue
                    const issueUrl = document.location.pathname.includes('/jira_clone_system/public')
                        ? `/jira_clone_system/public/issue/${issueKey}`
                        : `/issue/${issueKey}`;
                    console.log('Redirecting to:', issueUrl);
                    window.location.href = issueUrl;
                } else if (result.error) {
                    throw new Error(result.error);
                } else {
                    console.error('Unexpected response structure:', result);
                    throw new Error('Issue created but no key returned');
                }
            } catch (error) {
                console.error('Error creating issue:', error);
                alert('Error creating issue: ' + error.message);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    </script>
    
    <script src="<?= asset('js/app.js') ?>"></script>
    
    <?= \App\Core\View::yield('scripts') ?>
</body>
</html>
