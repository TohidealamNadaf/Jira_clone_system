<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="app-base-path" content="<?= e(basePath()) ?>">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?= e($title ?? 'Dashboard') ?> - <?= e(config('app.name')) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <!-- Quill Rich Text Editor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet" />
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <!-- Design Consistency CSS - Ensures all users see identical layout -->
    <link href="<?= asset('css/design-consistency.css') ?>" rel="stylesheet">
    <!-- Real-Time Notifications CSS -->
    <link href="<?= asset('css/realtime-notifications.css') ?>" rel="stylesheet">
    <!-- Time Tracking CSS -->
    <link rel="stylesheet" href="<?= url('/assets/css/floating-timer.css') ?>">
    <!-- Time Tracking Dashboard CSS -->
    <link rel="stylesheet" href="<?= url('/assets/css/time-tracking.css') ?>">

    <!-- Apply theme colors from settings -->
    <?php
    $primaryColor = \App\Core\Database::selectValue("SELECT value FROM settings WHERE `key` = 'primary_color'") ?? '#8B1956';
    $defaultTheme = \App\Core\Database::selectValue("SELECT value FROM settings WHERE `key` = 'default_theme'") ?? 'light';
    ?>
    <style>
        /* Prevent scrollbar layout shift */
        html {
            scrollbar-gutter: stable;
        }

        :root {
            --jira-blue:
                <?= e($primaryColor) ?>
            ;
            --jira-blue-dark: #6F123F;
            --jira-blue-light: #E77817;
            --jira-blue-lighter: #f0dce5;
            --primary-main:
                <?= e($primaryColor) ?>
            ;
            --primary-hover: #6F123F;
            --primary-light: #E77817;
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

        /* Links - INCLUDING breadcrumb and sidebar links */
        a {
            color: var(--jira-blue);
        }

        a.btn,
        a.nav-link,
        a.dropdown-item,
        a.list-group-item {
            color: inherit;
        }

        a:hover {
            color: var(--jira-blue-dark);
        }

        a.btn:hover,
        a.nav-link:hover,
        a.dropdown-item:hover,
        a.list-group-item:hover {
            color: inherit;
        }

        /* Explicit breadcrumb link styling */
        a.breadcrumb-link,
        .breadcrumb-nav a,
        .security-nav-item,
        a.security-nav-item,
        .profile-nav-item {
            color: var(--jira-blue) !important;
        }

        a.breadcrumb-link:hover,
        .breadcrumb-nav a:hover,
        .security-nav-item:hover,
        a.security-nav-item:hover,
        .profile-nav-item:hover {
            color: var(--jira-blue-dark) !important;
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

        /* ========================================
           SELECT2 CUSTOM THEME OVERRIDE
           ======================================== */

        /* Select2 container and borders */
        .select2-container--bootstrap-5 .select2-selection {
            border-color: var(--border-color) !important;
            background-color: white !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--jira-blue) !important;
            box-shadow: 0 0 0 0.2rem var(--jira-blue-lighter) !important;
        }

        /* Select2 focused state */
        .select2-container--bootstrap-5 .select2-selection.select2-selection--single:focus,
        .select2-container--bootstrap-5 .select2-selection.select2-selection--multiple:focus {
            border-color: var(--jira-blue) !important;
        }

        /* Select2 dropdown arrow */
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            color: var(--jira-blue) !important;
        }

        /* Select2 dropdown results */
        .select2-container--bootstrap-5 .select2-results__option--highlighted,
        .select2-container--bootstrap-5 .select2-results__option[aria-selected='true'] {
            background-color: var(--jira-blue) !important;
            color: white !important;
        }

        /* Select2 dropdown hover */
        .select2-container--bootstrap-5 .select2-results__option:hover {
            background-color: var(--jira-blue) !important;
            color: white !important;
        }

        /* Select2 selected option in dropdown */
        .select2-container--bootstrap-5 .select2-results__option[aria-selected='true']:hover {
            background-color: var(--jira-blue-dark) !important;
        }

        /* Select2 dropdown menu styling */
        .select2-dropdown {
            border-color: var(--jira-blue-lighter) !important;
            box-shadow: 0 4px 12px rgba(139, 25, 86, 0.15) !important;
        }

        /* Select2 search input */
        .select2-container--bootstrap-5 .select2-search__field {
            border-color: var(--border-color) !important;
        }

        .select2-container--bootstrap-5 .select2-search__field:focus {
            border-color: var(--jira-blue) !important;
            box-shadow: 0 0 0 0.2rem var(--jira-blue-lighter) !important;
        }

        /* Select2 selection placeholder */
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
            color: var(--text-muted) !important;
        }

        /* Select2 selected value */
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: var(--text-primary) !important;
        }

        /* Select2 open indicator (arrow) */
        .select2-container--bootstrap-5.select2-container--open .select2-selection--single .select2-selection__arrow {
            color: var(--jira-blue) !important;
        }

        /* Select2 disabled state */
        .select2-container--bootstrap-5.select2-container--disabled .select2-selection {
            background-color: #F7F8FA !important;
            color: var(--text-muted) !important;
        }

        /* Select2 tags/pills */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: var(--jira-blue-lighter) !important;
            border-color: var(--jira-blue) !important;
            color: var(--jira-blue) !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: var(--jira-blue) !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: var(--jira-blue-dark) !important;
        }

        /* Select2 clear button */
        .select2-container--bootstrap-5 .select2-selection__clear {
            color: var(--jira-blue) !important;
        }

        .select2-container--bootstrap-5 .select2-selection__clear:hover {
            color: var(--jira-blue-dark) !important;
        }

        /* Select2 loading indicator */
        .select2-container--bootstrap-5.select2-container--loading .select2-selection {
            border-color: var(--jira-blue) !important;
        }

        /* Fix for nested dropdown styling */
        .select2-container--bootstrap-5 .select2-dropdown.select2-dropdown--below {
            box-shadow: 0 4px 12px rgba(139, 25, 86, 0.15) !important;
        }

        .select2-container--bootstrap-5 .select2-dropdown.select2-dropdown--above {
            box-shadow: 0 -4px 12px rgba(139, 25, 86, 0.15) !important;
        }

        /* Smooth transitions */
        .select2-container--bootstrap-5 .select2-selection,
        .select2-search__field,
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            transition: border-color var(--transition-fast), box-shadow var(--transition-fast) !important;
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

                    <!-- Planning -->
                    <div class="nav-dropdown">
                        <button class="nav-dropdown-btn">
                            <i class="bi bi-calendar-event"></i>
                            <span>Planning</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-panel">
                            <a href="<?= url('/calendar') ?>" class="dropdown-item">
                                <i class="bi bi-calendar3"></i>
                                <div class="item-content">
                                    <div class="item-title">Calendar</div>
                                    <div class="item-desc">Track issue dates</div>
                                </div>
                            </a>
                            <a href="<?= url('/roadmap') ?>" class="dropdown-item">
                                <i class="bi bi-diagram-3"></i>
                                <div class="item-content">
                                    <div class="item-title">Roadmap</div>
                                    <div class="item-desc">Plan releases</div>
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
                <button class="navbar-action-btn create-btn" data-bs-toggle="modal" data-bs-target="#quickCreateModal"
                    title="Create issue">
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
                    <button class="navbar-action-btn user-btn" id="userMenu" title="User menu"
                        data-user-name="<?= e($user['display_name'] ?? ($user['first_name'] . ' ' . $user['last_name']) ?? 'User') ?>"
                        data-user-avatar="<?= e($user['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['display_name'] ?? $user['first_name'] ?? 'User')) ?>">
                        <img src="<?= e($user['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['display_name'] ?? $user['first_name'] ?? 'User')) ?>"
                            alt="<?= e($user['display_name'] ?? $user['first_name'] ?? 'User') ?>" class="user-avatar">
                        <i class="bi bi-chevron-down d-none d-md-inline"></i>
                    </button>
                    <div class="dropdown-panel user-panel">
                        <div class="panel-header user-header">
                            <img src="<?= e($user['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['display_name'] ?? $user['first_name'] ?? 'User')) ?>"
                                alt="<?= e($user['display_name'] ?? $user['first_name'] ?? 'User') ?>"
                                class="user-avatar-large">
                            <div>
                                <div class="user-name">
                                    <?= e($user['display_name'] ?? ($user['first_name'] . ' ' . $user['last_name']) ?? 'User') ?>
                                </div>
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
            color: var(--jira-blue, #8B1956);
            font-weight: 600;
            font-size: 16px;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.2s ease;
            line-height: 1;
        }

        .navbar-brand:hover {
            color: var(--jira-blue-dark, #6F123F);
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
            background-color: var(--jira-blue-lighter, #f0dce5);
            color: var(--jira-blue, #8B1956);
        }

        .nav-dropdown-btn:focus,
        .nav-link-simple:focus {
            background-color: var(--jira-blue-lighter, #f0dce5);
            color: var(--jira-blue, #8B1956);
        }

        .nav-dropdown-btn i:last-child {
            font-size: 12px;
            margin-left: 4px;
        }

        /* Dropdown Panel */
        .dropdown-panel {
            position: absolute;
            top: calc(100% - 8px);
            left: 0;
            background: #FFFFFF;
            border: 1px solid #DFE1E6;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(9, 30, 66, 0.15);
            min-width: 280px;
            padding-top: 8px;
            display: none;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease, visibility 0.15s ease;
            visibility: hidden;
        }

        .nav-dropdown:hover .dropdown-panel,
        .navbar-action-dropdown:hover .dropdown-panel {
            display: flex;
            pointer-events: auto;
            opacity: 1;
            visibility: visible;
        }

        /* Close the gap to prevent dropdown from disappearing on mouse move */
        .nav-dropdown:hover .dropdown-panel::before,
        .navbar-action-dropdown:hover .dropdown-panel::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 0;
            right: 0;
            height: 8px;
            background: transparent;
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
            color: var(--jira-blue, #8B1956);
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
            background-color: var(--jira-blue-lighter, #f0dce5);
            color: var(--jira-blue, #8B1956);
        }

        .dropdown-item:focus {
            background-color: var(--jira-blue-lighter, #f0dce5);
            color: var(--jira-blue, #8B1956);
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
            color: var(--jira-blue, #8B1956);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .panel-footer:hover {
            background-color: #F7F8FA;
            color: var(--jira-blue-dark, #6F123F);
        }

        .panel-footer:focus {
            background-color: #F7F8FA;
            color: var(--jira-blue-dark, #6F123F);
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
            border-color: var(--jira-blue, #8B1956);
            box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
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
            background: var(--jira-blue, #8B1956) !important;
            color: #FFFFFF !important;
            font-weight: 600 !important;
            font-size: 14px !important;
        }

        .create-btn:hover {
            background: var(--jira-blue-dark, #6F123F) !important;
            color: #FFFFFF !important;
        }

        .create-btn:focus {
            background: var(--jira-blue-dark, #6F123F) !important;
            color: #FFFFFF !important;
        }

        .create-btn span {
            color: #FFFFFF !important;
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
        // Load notifications on bell cl                ick
        document.getElementById('notificationBell').addEventListener('click', function (e) {
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

    <!-- Main Content - Consistent for All Users -->
    <main class="p-0" id="mainContent" style="background: transparent; min-height: calc(100vh - 200px); padding: 0;">
        <div style="width: 100%;">
            <?= \App\Core\View::yield('content') ?>
        </div>
    </main>

    <!-- DEBUG: Test Console Logging -->
    <script>
        console.log('🟡 DEBUG: Main content area script executed');
    </script>

    <!-- Footer -->
    <footer class="bg-light border-top mt-auto" style="padding: 0.5rem 0;">
        <div class="container-fluid">
            <div
                style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #626F86; padding: 0 1rem;">
                <span>&copy; <?= date('Y') ?> <?= e(config('app.name')) ?>. All rights reserved.</span>
                <span>Version 1.0.0</span>
            </div>
        </div>
    </footer>

    <!-- Quick Create Modal - Jira-Like Design -->
    <div class="modal fade" id="quickCreateModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 700px;">
            <div class="modal-content quick-create-modal-jira">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Create Issue</h5>
                    <small class="text-muted ms-2" id="initialStatusText">This is the initial status upon
                        creation</small>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form id="quickCreateForm" class="quick-create-form-jira">
                        <!-- Project & Issue Type (Compact) - MOVED TO TOP -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                                <select class="form-select jira-input" name="project_id" required
                                    id="quickCreateProject">
                                    <option value="">Loading projects...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Work Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select jira-input" name="issue_type_id" required
                                    id="quickCreateIssueType">
                                    <option value="">Select a project first...</option>
                                </select>
                            </div>
                        </div>

                        <!-- Summary Field (Required) -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Summary <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg jira-input" name="summary" required
                                placeholder="What needs to be done?" maxlength="500" id="quickCreateSummary">
                            <div class="invalid-feedback d-block mt-1" id="summaryError"></div>
                            <small class="form-text text-muted d-block mt-1">
                                <span id="summaryChar">0</span>/500 characters
                            </small>
                        </div>

                        <!-- Description Field with Quill Rich Text Editor + Inline Attachments -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Description</label>
                            <div style="border: 1px solid #DFE1E6; border-radius: 4px; overflow: hidden;">
                                <!-- Quill Editor Container -->
                                <div id="quickCreateDescriptionEditor" style="background: white; min-height: 200px;">
                                </div>
                                <!-- Inline Attachments Section Below Editor -->
                                <div id="descriptionAttachmentsContainer"
                                    style="display: none; border-top: 1px solid #DFE1E6; background-color: #F7F8FA; padding: 12px;">
                                    <div
                                        style="font-weight: 500; font-size: 13px; color: #161B22; margin-bottom: 12px; padding-left: 8px;">
                                        Attached files:</div>
                                    <div id="descriptionAttachmentsList"
                                        style="display: flex; flex-direction: column; gap: 8px;"></div>
                                </div>
                                <!-- Hidden file input for description attachments -->
                                <input type="file" id="descriptionAttachmentInput" multiple style="display: none;"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.zip,.mov,.mp4,.webm,.webp">
                            </div>
                            <!-- Hidden textarea to store content -->
                            <textarea name="description" id="quickCreateDescription" style="display:none;"></textarea>
                            <small class="form-text text-muted d-block mt-2">
                                <span id="descChar">0</span>/5000 characters
                            </small>
                        </div>

                        <!-- Reporter Field (Auto-filled, Read-only) - With Avatar -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Reporter</label>
                            <div class="reporter-field-wrapper"
                                style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #DFE1E6; border-radius: 4px; background-color: #F7F8FA;">
                                <div id="quickCreateReporterAvatar"
                                    style="width: 44px; height: 44px; border-radius: 50%; background-color: #8B1956; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px; flex-shrink: 0;">
                                    C
                                </div>
                                <div style="flex: 1;">
                                    <div id="quickCreateReporterName"
                                        style="font-weight: 500; color: #161B22; font-size: 14px;">Current User</div>
                                    <small class="form-text text-muted d-block" style="margin-top: 2px;">You are
                                        automatically set as the reporter</small>
                                </div>
                            </div>
                        </div>

                        <!-- Assignee Field -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Assignee</label>
                            <div class="assignee-wrapper">
                                <select class="form-select form-select-lg jira-input" name="assignee_id"
                                    id="quickCreateAssignee">
                                    <option value="">Automatic</option>
                                </select>
                                <a href="#" class="assignee-link ms-2" id="assignToMeLink">Assign to me</a>
                            </div>
                        </div>

                        <!-- Status Field -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select jira-input" name="status_id" id="quickCreateStatus">
                                <option value="">Default</option>
                            </select>
                        </div>

                        <!-- Sprint Field -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Sprint</label>
                            <select class="form-select jira-input" name="sprint_id" id="quickCreateSprint">
                                <option value="">None (Backlog)</option>
                            </select>
                        </div>

                        <!-- Labels Field -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Labels</label>
                            <select class="form-select jira-input" name="labels" id="quickCreateLabels"
                                placeholder="Select labels..." multiple>
                                <option value="">No labels</option>
                            </select>
                            <small class="form-text text-muted d-block mt-1">Hold Ctrl/Cmd to select multiple</small>
                        </div>

                        <!-- Start Date Field -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" class="form-control jira-input" name="start_date"
                                id="quickCreateStartDate">
                        </div>

                        <!-- Due Date Field -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Due Date</label>
                            <input type="date" class="form-control jira-input" name="due_date" id="quickCreateDueDate">
                        </div>

                        <!-- Attachments Field -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Attachments</label>
                            <div class="attachment-drop-zone" id="quickCreateAttachmentZone" style="
                                 border: 2px dashed #DFE1E6;
                                 border-radius: 8px;
                                 padding: 20px;
                                 text-align: center;
                                 cursor: pointer;
                                 transition: all 0.2s ease;
                                 background-color: #F7F8FA;
                             ">
                                <i class="bi bi-cloud-arrow-up"
                                    style="font-size: 2rem; color: var(--jira-blue); margin-bottom: 8px;"></i>
                                <div style="font-weight: 500; color: #161B22; margin-bottom: 4px;">Drop files here to
                                    upload</div>
                                <div style="font-size: 13px; color: #626F86;">Or click to select files (max 10MB per
                                    file)</div>
                                <input type="file" id="quickCreateAttachmentInput" multiple style="display: none;"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.zip">
                            </div>
                            <div id="quickCreateAttachmentList" style="margin-top: 12px;"></div>
                        </div>

                        <!-- Create Another Checkbox -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="create_another"
                                    id="createAnotherCheck">
                                <label class="form-check-label" for="createAnotherCheck">
                                    Create another
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-lg" onclick="submitQuickCreate()"
                        id="quickCreateBtn">
                        <i class="bi bi-plus-lg me-1"></i> Create
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Create Modal Styles -->
    <style>
        /* Modal Content */
        .quick-create-modal-jira .modal-content {
            border-radius: 8px;
            border: 1px solid #DFE1E6;
            box-shadow: 0 10px 40px rgba(9, 30, 66, 0.25);
        }

        .quick-create-modal-jira .modal-header {
            background-color: #FFFFFF;
            padding: 20px;
            border-bottom: 1px solid #DFE1E6;
        }

        .quick-create-modal-jira .modal-body {
            padding: 20px;
            background-color: #FAFBFC;
        }

        .quick-create-modal-jira .modal-footer {
            padding: 16px 20px;
            border-top: 1px solid #DFE1E6;
        }

        /* Form Controls */
        .jira-input {
            border: 1px solid #DFE1E6;
            border-radius: 3px;
            padding: 8px 12px;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .jira-input:focus {
            border-color: var(--jira-blue);
            box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
            outline: none;
        }

        .jira-textarea {
            border: 1px solid #DFE1E6;
            border-radius: 3px;
            padding: 12px;
            font-size: 14px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            resize: vertical;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .jira-textarea:focus {
            border-color: var(--jira-blue);
            box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
            outline: none;
        }

        /* Rich Text Editor Toolbar */
        .jira-editor-toolbar {
            border: 1px solid #DFE1E6;
            border-bottom: none;
            border-radius: 3px 3px 0 0;
            background-color: #F7F8FA;
            padding: 8px;
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .editor-buttons {
            display: flex;
            gap: 4px;
            align-items: center;
            flex-wrap: wrap;
        }

        .editor-btn {
            background: none;
            border: none;
            color: #626F86;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 3px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: background-color 0.15s, color 0.15s;
        }

        .editor-btn:hover {
            background-color: #DEEBFF;
            color: var(--jira-blue);
        }

        .editor-btn:active {
            background-color: #B3D4FF;
        }

        .dropdown-arrow {
            font-size: 10px;
            margin-left: 2px;
        }

        .editor-divider {
            width: 1px;
            height: 20px;
            background-color: #DFE1E6;
            margin: 0 4px;
        }

        /* Assignee */
        .assignee-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .assignee-link {
            color: var(--jira-blue);
            text-decoration: none;
            font-size: 14px;
            white-space: nowrap;
        }

        .assignee-link:hover {
            color: var(--jira-blue-dark);
            text-decoration: underline;
        }

        /* Form Labels */
        .form-label {
            font-size: 14px;
            color: #161B22;
            margin-bottom: 8px;
            font-weight: 600;
        }

        /* Character Counters */
        #summaryChar,
        #descChar {
            color: #626F86;
            font-weight: 500;
        }

        /* Error Messages */
        .invalid-feedback {
            color: #AE2A19;
            font-size: 13px;
            margin-top: 4px;
        }

        .invalid-feedback.d-block {
            display: block !important;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .quick-create-modal-jira .modal-dialog {
                max-width: 100% !important;
                margin: 8px;
            }

            .quick-create-modal-jira .modal-body {
                max-height: calc(100vh - 200px) !important;
            }

            .editor-buttons {
                flex-wrap: wrap;
            }

            .editor-divider {
                display: none;
            }
        }

        /* Button Styles */
        .modal-footer .btn {
            padding: 8px 20px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 14px;
        }

        .modal-footer .btn-light {
            background-color: #FAFBFC;
            border-color: #DFE1E6;
            color: #161B22;
        }

        .modal-footer .btn-light:hover {
            background-color: #FFFFFF;
            border-color: #B6C2CF;
        }

        .modal-footer .btn-primary {
            background-color: var(--jira-blue);
            border-color: var(--jira-blue);
            color: white;
        }

        .modal-footer .btn-primary:hover {
            background-color: var(--jira-blue-dark);
            border-color: var(--jira-blue-dark);
        }
    </style>

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

    <!-- DEBUG: Before App JS -->
    <script>
        console.log('[BEFORE] App JS section');
    </script>

    <!-- Minimal Test Script -->
    <script>
        console.log('[MINIMAL-TEST] Minimal script works');
    </script>

    <!-- App JS -->
    <script>
        console.log('[START] App JS script section started');
    </script>

    <!-- Test Script 2 -->
    <script>
        console.log('[TEST] This is a test');
    </script>

    <!-- Test Script 3 -->
    <script>
        console.log('[TRY-BLOCK] About to enter try block');
    </script>

    <!-- Test Script 4 -->
    <script>
        console.log('[NAVBAR-LOG] About to setup navbar');

        // API URLs - Use PHP-generated URLs to handle any deployment path
        // MOVED TO GLOBAL SCOPE (outside try-catch) so submitQuickCreate can access them
        const API_QUICK_CREATE_URL = '<?= url('/projects/quick-create-list') ?>';
        const API_USERS_ACTIVE_URL = '<?= url('/users/active') ?>';
        const API_ISSUE_CREATE_TEMPLATE = '<?= url('/projects/{projectKey}/issues') ?>';
        const APP_BASE_PATH = '<?= url('') ?>';
        const ISSUE_VIEW_TEMPLATE = '<?= url('/issue/{issueKey}') ?>';

        console.log('📱 Global API URLs initialized:', { APP_BASE_PATH, ISSUE_VIEW_TEMPLATE });

        // CSRF Token - moved to global scope for submitQuickCreate access
         const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
         const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';
         console.log('🔐 CSRF Token initialized:', csrfToken ? 'present' : 'missing');

         // ✅ CRITICAL: Global projectsMap for quick create form submission
          // This must be global so submitQuickCreate() can access it as a fallback
          window.projectsMap = {};
          console.log('📦 projectsMap initialized as window.projectsMap');

        try {
            console.log('[NAVBAR] Setting up navbar click handlers');
            // Close navbar when clicking navigation links
            const navLinks = document.querySelectorAll('.navbar-collapse .dropdown-item, .navbar-collapse .nav-link:not([data-bs-toggle])');
            navLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    const navDropdown = this.closest('.nav-dropdown');

                    if (navDropdown) {
                        return;
                    }

                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        const collapseButton = document.querySelector('.navbar-toggler');
                        collapseButton.click();
                    }
                });
            });

            console.log('[MODAL-CLOSE] Setting up modal close handlers');
            // Close navbar when modal opens
            const modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
            modalTriggers.forEach(function (trigger) {
                trigger.addEventListener('click', function () {
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        const collapseButton = document.querySelector('.navbar-toggler');
                        collapseButton.click();
                    }
                });
            });

            // NOTE: csrfToken is now defined in global scope (line ~1606) for access by submitQuickCreate

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

            console.log('[TOOLTIPS] Initializing tooltips');
            // Tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });

            console.log('[INIT-SELECT2] Defining initializeSelect2 function');
            // Plain HTML select - no Select2 needed for quick modal
            function initializeSelect2() {
                try {
                    console.log('Quick modal using plain HTML selects - no Select2 initialization needed');
                } catch (error) {
                    console.error('❌ Error in select initialization:', error);
                }
            }

            // Initialize reporter field with current user's name and avatar
            function initializeReporterField() {
                const reporterAvatar = document.getElementById('quickCreateReporterAvatar');
                const reporterName = document.getElementById('quickCreateReporterName');

                if (!reporterAvatar || !reporterName) return;

                try {
                    // Get current user data from navbar button
                    const userMenuBtn = document.getElementById('userMenu');
                    if (!userMenuBtn) {
                        console.warn('⚠️ User menu button not found');
                        return;
                    }

                    // Extract user data from navbar button data attributes
                    const userName = userMenuBtn.getAttribute('data-user-name') || 'User';
                    const userAvatar = userMenuBtn.getAttribute('data-user-avatar') || '';

                    // Get user initials from user name
                    const initials = userName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);

                    console.log('📝 Reporter Field initialized:', { userName, userAvatar, initials });

                    // Update reporter name
                    reporterName.textContent = userName;

                    // Update reporter avatar
                    if (userAvatar && userAvatar.includes('http')) {
                        // If avatar is a valid image URL, show it as background image
                        reporterAvatar.style.backgroundImage = `url(${userAvatar})`;
                        reporterAvatar.style.backgroundSize = 'cover';
                        reporterAvatar.style.backgroundPosition = 'center';
                        reporterAvatar.style.color = 'transparent'; // Hide text
                        reporterAvatar.innerHTML = ''; // Clear any content
                    } else if (initials) {
                        // Show initials if no valid avatar URL
                        reporterAvatar.textContent = initials;
                        reporterAvatar.style.backgroundImage = 'none';
                        reporterAvatar.style.color = 'white';
                    }
                } catch (error) {
                    console.error('❌ Failed to initialize reporter field:', error);
                }
            }

            // Initialize quick create form elements
            function initializeQuickCreateModal() {
                console.log('✅ Initializing Quick Create Modal form elements...');

                // Initialize reporter field
                initializeReporterField();

                // Character counter for summary
                const summaryInput = document.getElementById('quickCreateSummary');
                if (summaryInput) {
                    summaryInput.addEventListener('input', function () {
                        const counter = document.getElementById('summaryChar');
                        if (counter) counter.textContent = this.value.length;
                    });
                }

                // Character counter for description
                const descInput = document.getElementById('quickCreateDescription');
                if (descInput) {
                    descInput.addEventListener('input', function () {
                        const counter = document.getElementById('descChar');
                        if (counter) counter.textContent = this.value.length;
                    });
                }

                // Assign to me link handler
                const assignLink = document.getElementById('assignToMeLink');
                if (assignLink) {
                    assignLink.addEventListener('click', function (e) {
                        e.preventDefault();
                        const assigneeSelect = document.getElementById('quickCreateAssignee');
                        if (!assigneeSelect) return;

                        console.log('[ASSIGN-ME] Looking for current user option...');
                        console.log('[ASSIGN-ME] All assignee options:');
                        for (let i = 0; i < assigneeSelect.options.length; i++) {
                            const opt = assigneeSelect.options[i];
                            console.log(`  [${i}] value="${opt.value}" text="${opt.text}" data-is-current="${opt.dataset.isCurrent}"`);
                        }

                        const currentUserOption = assigneeSelect.querySelector('option[data-is-current="true"]');
                        if (currentUserOption) {
                            assigneeSelect.value = currentUserOption.value;
                            
                            // Trigger change event with vanilla JavaScript
                            const changeEvent = new Event('change', { bubbles: true });
                            assigneeSelect.dispatchEvent(changeEvent);
                            
                            console.log('[ASSIGN-ME] ✅ Assigned to current user:', currentUserOption.textContent);
                        } else {
                            console.warn('[ASSIGN-ME] ⚠️ Current user option not found in dropdown');
                            console.warn('[ASSIGN-ME] Check if current user is active and in the assignees API response');
                            
                            // Fallback: Try to find option with "(me)" in text
                            const meOption = Array.from(assigneeSelect.options).find(opt => opt.textContent.includes('(me)'));
                            if (meOption) {
                                console.log('[ASSIGN-ME] ✅ Found option with (me) text:', meOption.textContent);
                                assigneeSelect.value = meOption.value;
                                const changeEvent = new Event('change', { bubbles: true });
                                assigneeSelect.dispatchEvent(changeEvent);
                            }
                        }
                    });
                }

                console.log('✅ Quick Create Modal initialization complete');
            }

            // Rich text editor button actions
            document.querySelectorAll('.editor-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const action = this.dataset.action;
                    const textarea = document.getElementById('quickCreateDescription');
                    if (!textarea) return;
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    const selectedText = text.substring(start, end);
                    let insertText = '';

                    switch (action) {
                        case 'bold':
                            insertText = `**${selectedText || 'bold text'}**`;
                            break;
                        case 'italic':
                            insertText = `_${selectedText || 'italic text'}_`;
                            break;
                        case 'unorderedList':
                            insertText = `\n• ${selectedText || 'list item'}\n`;
                            break;
                        case 'orderedList':
                            insertText = `\n1. ${selectedText || 'list item'}\n`;
                            break;
                        case 'checkbox':
                            insertText = `\n☐ ${selectedText || 'task'}\n`;
                            break;
                        case 'link':
                            insertText = `[${selectedText || 'link text'}](url)`;
                            break;
                        case 'mention':
                            insertText = `@`;
                            break;
                        case 'emoji':
                            insertText = `:`;
                            break;
                        case 'code':
                            insertText = `\`${selectedText || 'code'}\``;
                            break;
                        case 'attachment':
                            alert('Attachment feature would open file picker');
                            return;
                        case 'table':
                            insertText = `\n| Header 1 | Header 2 |\n|----------|----------|\n| Cell 1   | Cell 2   |\n`;
                            break;
                        case 'format':
                            alert('Text formatting options: **bold**, _italic_, \`code\`, etc.');
                            return;
                    }

                    if (insertText && textarea) {
                        textarea.value = text.substring(0, start) + insertText + text.substring(end);
                        textarea.selectionStart = start + insertText.length;
                        textarea.selectionEnd = start + insertText.length;
                        textarea.focus();

                        const descChar = document.getElementById('descChar');
                        if (descChar) descChar.textContent = textarea.value.length;
                    }
                });
            });

            // Load projects on modal open - always fetch fresh from endpoint (works on all pages)
            let projectsLoading = false;

            // NOTE: API URL constants are now defined in global scope (line ~1598-1602)
            // so they are accessible to the global submitQuickCreate function

            // Deferred initialization to avoid conflicts with page-specific forms
            function attachQuickCreateModalListeners() {
                console.log('🔴🔴🔴 attachQuickCreateModalListeners CALLED 🔴🔴🔴');
                console.log('📱 attachQuickCreateModalListeners called');
                console.log('API_QUICK_CREATE_URL:', API_QUICK_CREATE_URL);

                // Ensure modal exists before trying to attach event listener
                const quickCreateModal = document.getElementById('quickCreateModal');
                console.log('Looking for quickCreateModal element...');
                console.log('quickCreateModal element:', quickCreateModal);

                if (!quickCreateModal) {
                    console.log('ℹ️ Quick Create Modal not on this page (normal for create/edit pages)');
                    console.log('Available elements in DOM:', document.querySelectorAll('.modal').length, 'modals found');
                    return;
                }

                console.log('✅ Quick Create Modal found, initializing...');

                // Initialize form controls
                console.log('🔧 Calling initializeQuickCreateModal()');
                initializeQuickCreateModal();
                console.log('✅ initializeQuickCreateModal() complete');

                quickCreateModal.addEventListener('show.bs.modal', async function () {
                    console.log('[MODAL-OPEN] Modal "show.bs.modal" event fired');

                    // Re-initialize reporter field on modal open
                    initializeReporterField();

                    // Initialize Select2 if not already done (using vanilla JS instead of jQuery)
                    const projectSelectElement = document.getElementById('quickCreateProject');
                    if (projectSelectElement && !projectSelectElement.classList.contains('select2-hidden-accessible')) {
                        console.log('Initializing Select2...');
                        initializeSelect2();
                    } else {
                        console.log('Select2 already initialized or element not found');
                    }

                    const projectSelect = document.getElementById('quickCreateProject');
                    const assigneeSelect = document.getElementById('quickCreateAssignee');

                    if (!projectSelect) {
                        console.error('❌ Project select element not found!');
                        return;
                    }

                    if (!assigneeSelect) {
                        console.error('❌ Assignee select element not found!');
                        return;
                    }

                    // Always reload projects and assignees to ensure fresh data on all pages
                    projectsLoading = true;
                    console.log('🔄 Loading projects and assignees for quick create modal...');
                    console.log('📡 Fetching from:', API_QUICK_CREATE_URL);

                    try {
                        let projects = [];
                        let assignees = [];

                        console.log('[LOAD-PROJECTS] Starting project and assignee loading...');

                        // Use PHP-generated URLs that handle deployment paths correctly
                        const quickCreateUrl = API_QUICK_CREATE_URL;
                        const assigneesUrl = API_USERS_ACTIVE_URL;

                        console.log('[API-URL] API_QUICK_CREATE_URL:', API_QUICK_CREATE_URL);
                        console.log('📡 API_USERS_ACTIVE_URL:', API_USERS_ACTIVE_URL);
                        console.log('🌍 APP_BASE_PATH:', APP_BASE_PATH);

                        // Fetch projects
                        console.log('🌐 Starting projects fetch...');
                        const projectsResponse = await fetch(quickCreateUrl, {
                            method: 'GET',
                            credentials: 'include',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken || '',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        }).catch(err => {
                            console.error('❌ Fetch error (projects):', err);
                            throw err;
                        });

                        console.log('📊 Projects response received, status:', projectsResponse.status);

                        // Fetch assignees
                        console.log('🌐 Starting assignees fetch...');
                        const assigneesResponse = await fetch(assigneesUrl, {
                            method: 'GET',
                            credentials: 'include',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken || '',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        }).catch(err => {
                            console.error('❌ Fetch error (assignees):', err);
                            throw err;
                        });

                        console.log('📊 Assignees response received, status:', assigneesResponse.status);

                        if (!projectsResponse.ok) {
                            const errorText = await projectsResponse.text();
                            console.error('❌ Projects API Error:', projectsResponse.status, errorText);
                            throw new Error(`Projects API HTTP ${projectsResponse.status}: ${projectsResponse.statusText}`);
                        }

                        const projectData = await projectsResponse.json();
                        console.log('✅ Projects data received:', projectData);
                        console.log('   Total items:', projectData.items?.length || 0);

                        // Handle paginated response
                        if (projectData.items && Array.isArray(projectData.items)) {
                            projects = projectData.items;
                            console.log('Got', projects.length, 'projects from items array');
                        } else if (Array.isArray(projectData)) {
                            projects = projectData;
                            console.log('Got', projects.length, 'projects directly');
                        } else {
                            console.warn('Unexpected projects response format:', projectData);
                            projects = [];
                        }

                        // Handle assignees response
                        if (assigneesResponse.ok) {
                            const assigneesData = await assigneesResponse.json();
                            if (Array.isArray(assigneesData)) {
                                assignees = assigneesData;
                            } else if (assigneesData.items && Array.isArray(assigneesData.items)) {
                                assignees = assigneesData.items;
                            }
                            console.log('Got', assignees.length, 'assignees');
                        } else {
                            console.warn('Could not load assignees:', assigneesResponse.status);
                        }

                        // Populate projects select
                        console.log('🔨 Clearing project dropdown and adding options...');
                        projectSelect.innerHTML = '<option value="">Select a project...</option>';

                        if (projects.length > 0) {
                            console.log('✅ Adding', projects.length, 'projects to dropdown');
                            projects.forEach((project, idx) => {
                                // Add to dropdown
                                const option = document.createElement('option');
                                option.value = project.id;
                                option.dataset.projectKey = project.key;
                                const displayText = `${project.name} (${project.key})`;
                                option.textContent = displayText;
                                option.title = displayText;
                                projectSelect.appendChild(option);

                                console.log(`   [${idx + 1}] Added project: ${displayText}`);

                                // Add to projectsMap for issue type lookup
                                window.projectsMap[project.id] = {
                                    id: project.id,
                                    key: project.key,
                                    name: project.name,
                                    displayText: displayText,
                                    issue_types: project.issue_types || []
                                };
                            });
                            console.log('✅ All projects added to dropdown');
                        } else {
                            console.error('❌ No projects returned from API');
                            projectSelect.innerHTML = '<option value="">No projects available</option>';
                        }

                        console.log('✅ window.projectsMap now contains', Object.keys(window.projectsMap).length, 'projects');

                        // Populate assignees select
                        assigneeSelect.innerHTML = '<option value="">Automatic</option>';

                        if (assignees.length > 0) {
                             const currentUserId = parseInt('<?= e($user['id'] ?? '0') ?>') || 0;
                             console.log('[ASSIGNEES] Current user ID:', currentUserId);
                             let foundCurrentUser = false;
                             
                             assignees.forEach(user => {
                                 const option = document.createElement('option');
                                 option.value = user.id;
                                 // Compare as numbers for accuracy
                                 const isCurrentUser = parseInt(user.id) === currentUserId;
                                 
                                 // Only set attribute when true (cleaner and matches querySelector)
                                 if (isCurrentUser) {
                                     option.dataset.isCurrent = 'true';
                                     foundCurrentUser = true;
                                     console.log('[ASSIGNEES] ✅ Found current user:', user.name, '(ID:', user.id, ')');
                                 }
                                 
                                 option.textContent = user.name + (isCurrentUser ? ' (me)' : '');
                                 assigneeSelect.appendChild(option);
                             });
                             
                             console.log('[ASSIGNEES] Added', assignees.length, 'assignees to dropdown');
                             if (!foundCurrentUser) {
                                 console.warn('[ASSIGNEES] ⚠️ Current user not found in active assignees list (ID:', currentUserId, ')');
                                 console.warn('[ASSIGNEES] ℹ️  This is OK - current user might not be marked as active, or is admin');
                                 console.warn('[ASSIGNEES] ℹ️  The "Assign to me" link will use fallback (me) matching');
                             }
                         } else {
                             console.warn('[ASSIGNEES] No assignees returned from API');
                         }

                        // Projects dropdown is now populated with plain HTML options
                        // No Select2 needed - plain HTML select works better for this use case
                        console.log('✅ Projects dropdown populated with', projectSelect.options.length - 1, 'options');

                        // Assignee dropdown is now populated with plain HTML options
                        console.log('✅ Assignee dropdown populated with', assigneeSelect.options.length - 1, 'options');

                        // Setup project change handler after first load
                        console.log('🔧 Setting up project change handler');
                        setupProjectChangeHandler();

                        // Reset the selection to empty
                        console.log('🔄 Resetting project selection to empty');
                        projectSelect.value = '';
                        
                        // Trigger change event with vanilla JavaScript
                        const changeEvent = new Event('change', { bubbles: true });
                        projectSelect.dispatchEvent(changeEvent);

                        console.log('✅ Projects and assignees loaded and Select2 re-initialized');
                        console.log('📊 Final state:');
                        console.log('   - Projects in dropdown:', projectSelect.options.length - 1);
                        console.log('   - Assignees in dropdown:', assigneeSelect.options.length - 1);
                        console.log('   - Projects in window.projectsMap:', Object.keys(window.projectsMap).length);

                    } catch (error) {
                        console.error('❌ Failed to load projects/assignees:', error);
                        console.error('   Error message:', error.message);
                        console.error('   Error stack:', error.stack);
                        projectSelect.innerHTML = '<option value="">Error: ' + (error.message || 'Unknown error') + '</option>';
                        
                        // Trigger change event with vanilla JavaScript
                        const changeEvent = new Event('change', { bubbles: true });
                        projectSelect.dispatchEvent(changeEvent);
                    } finally {
                        projectsLoading = false;
                        console.log('✅ Loading complete, projectsLoading =', projectsLoading);
                    }
                });
            }



            // Flag to track if change handler is initialized
            let projectChangeHandlerInitialized = false;

            // Setup project change handler (called from attachQuickCreateModalListeners after first load)
            function setupProjectChangeHandler() {
                console.log('📌 Attaching project change event handler');

                // Plain vanilla JavaScript change handler (like create issue page)
                const projectSelect = document.getElementById('quickCreateProject');
                const issueTypeSelect = document.getElementById('quickCreateIssueType');
                const assigneeSelect = document.getElementById('quickCreateAssignee');
                const statusSelect = document.getElementById('quickCreateStatus');
                const sprintSelect = document.getElementById('quickCreateSprint');
                const labelsSelect = document.getElementById('quickCreateLabels');

                if (!projectSelect) {
                    console.error('❌ Project select not found');
                    return;
                }

                projectSelect.addEventListener('change', async function () {
                     const projectId = this.value;
                     console.log('🎯 Project changed to:', projectId);
                     console.log('   Selected text:', this.options[this.selectedIndex].text);

                     if (!projectId) {
                         // ✅ CRITICAL FIX: Don't clear assignees - keep full team roster available
                         // This preserves the ability to use "Assign to me" even without a project selected
                         issueTypeSelect.innerHTML = '<option value="">Select a project first...</option>';
                         // ✅ REMOVED: assigneeSelect.innerHTML = '<option value="">Automatic</option>';
                         // Now keep existing assignees instead of clearing them
                         statusSelect.innerHTML = '<option value="">Default</option>';
                         sprintSelect.innerHTML = '<option value="">None (Backlog)</option>';
                         labelsSelect.innerHTML = '<option value="">No labels</option>';
                         return;
                     }

                    // First try projectsMap
                     let issueTypes = [];
                     const project = window.projectsMap[projectId];

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

                            // Build API URL using deployment-aware path
                            const apiUrl = APP_BASE_PATH + '/projects/' + projectKey;

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
                    issueTypeSelect.innerHTML = '<option value="">Select an issue type...</option>';

                    if (issueTypes.length > 0) {
                        console.log('Adding', issueTypes.length, 'issue types to dropdown');
                        issueTypes.forEach(type => {
                            const option = document.createElement('option');
                            option.value = type.id;
                            option.textContent = type.name;
                            issueTypeSelect.appendChild(option);
                        });
                        console.log('✅ Issue types added:', issueTypes.length);
                    } else {
                        issueTypeSelect.innerHTML = '<option value="">No issue types available</option>';
                        console.warn('No issue types found for project:', projectId);
                    }

                    console.log('✅ Issue type dropdown updated:', issueTypes.length, 'types available');

                    // Fetch and populate statuses, sprints, and labels
                    try {
                        const projectKey = document.querySelector('#quickCreateProject option:checked')?.dataset?.projectKey;
                        if (!projectKey) {
                            throw new Error('Project key not found');
                        }

                        const apiUrl = APP_BASE_PATH + '/projects/' + projectKey;

                        const response = await fetch(apiUrl, {
                            method: 'GET',
                            credentials: 'include',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        if (response.ok) {
                            const projectData = await response.json();

                            // Populate statuses
                            if (projectData.statuses && Array.isArray(projectData.statuses)) {
                                statusSelect.innerHTML = '<option value="">Default</option>';
                                projectData.statuses.forEach(status => {
                                    const option = document.createElement('option');
                                    option.value = status.id;
                                    option.textContent = status.name;
                                    statusSelect.appendChild(option);
                                });
                                console.log('✅ Statuses populated:', projectData.statuses.length);
                            }

                            // Populate sprints
                            if (projectData.sprints && Array.isArray(projectData.sprints)) {
                                sprintSelect.innerHTML = '<option value="">None (Backlog)</option>';
                                projectData.sprints.forEach(sprint => {
                                    const option = document.createElement('option');
                                    option.value = sprint.id;
                                    option.textContent = sprint.name;
                                    sprintSelect.appendChild(option);
                                });
                                console.log('✅ Sprints populated:', projectData.sprints.length);
                            }

                            // Populate labels (if available in project data)
                            if (projectData.labels && Array.isArray(projectData.labels)) {
                                labelsSelect.innerHTML = '<option value="">No labels</option>';
                                projectData.labels.forEach(label => {
                                    const option = document.createElement('option');
                                    option.value = label.id;
                                    option.textContent = label.name;
                                    labelsSelect.appendChild(option);
                                });
                                console.log('✅ Labels populated:', projectData.labels.length);
                            } else {
                                console.log('ℹ️ No labels available for this project');
                            }
                        } else {
                            console.warn('⚠️ Could not fetch project data for statuses/sprints/labels');
                        }
                    } catch (error) {
                        console.error('❌ Failed to populate statuses/sprints/labels:', error);
                    }
                });
            }

            // Attachment handling for quick create modal
            const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
            const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip'];
            const attachmentInput = document.getElementById('quickCreateAttachmentInput');
            const attachmentZone = document.getElementById('quickCreateAttachmentZone');
            const attachmentList = document.getElementById('quickCreateAttachmentList');
            const selectedFiles = new Map();

            // Only setup attachment handlers if modal elements exist
            if (attachmentInput && attachmentZone && attachmentList) {
                // Click to select files
                attachmentZone.addEventListener('click', () => {
                    attachmentInput.click();
                });

                // Handle file selection
                attachmentInput.addEventListener('change', (e) => {
                    handleFiles(e.target.files);
                });

                // Drag and drop
                attachmentZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    attachmentZone.classList.add('dragover');
                });

                attachmentZone.addEventListener('dragleave', () => {
                    attachmentZone.classList.remove('dragover');
                });

                attachmentZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    attachmentZone.classList.remove('dragover');
                    handleFiles(e.dataTransfer.files);
                });

                function handleFiles(files) {
                    for (let file of files) {
                        // Validate file size
                        if (file.size > MAX_FILE_SIZE) {
                            alert(`File "${file.name}" exceeds 10MB limit`);
                            continue;
                        }

                        // Validate file type
                        const ext = file.name.split('.').pop().toLowerCase();
                        if (!ALLOWED_EXTENSIONS.includes(ext)) {
                            alert(`File type ".${ext}" not allowed`);
                            continue;
                        }

                        // Add to selected files
                        const fileId = 'file_' + Math.random().toString(36).substr(2, 9);
                        selectedFiles.set(fileId, file);
                        addAttachmentItem(fileId, file);
                    }
                }

                function addAttachmentItem(fileId, file) {
                    const listItem = document.createElement('div');
                    listItem.id = fileId;
                    listItem.className = 'attachment-item';

                    const ext = file.name.split('.').pop().toLowerCase();
                    let iconClass = 'bi-file';

                    // Determine icon based on file type
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                        iconClass = 'bi-image';
                    } else if (['pdf'].includes(ext)) {
                        iconClass = 'bi-file-pdf';
                    } else if (['doc', 'docx'].includes(ext)) {
                        iconClass = 'bi-file-word';
                    } else if (['xls', 'xlsx'].includes(ext)) {
                        iconClass = 'bi-file-earmark-spreadsheet';
                    } else if (['ppt', 'pptx'].includes(ext)) {
                        iconClass = 'bi-file-presentation';
                    } else if (['zip'].includes(ext)) {
                        iconClass = 'bi-file-zip';
                    }

                    listItem.innerHTML = `
                    <div class="attachment-item-info">
                        <div class="attachment-item-icon">
                            <i class="bi ${iconClass}"></i>
                        </div>
                        <div class="attachment-item-details">
                            <span class="attachment-item-name" title="${file.name}">${file.name}</span>
                            <span class="attachment-item-size">${formatFileSize(file.size)}</span>
                        </div>
                    </div>
                    <div class="attachment-item-status">
                        <button type="button" class="attachment-remove-btn" aria-label="Remove file">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;

                    // Remove button handler
                    listItem.querySelector('.attachment-remove-btn').addEventListener('click', (e) => {
                        e.preventDefault();
                        listItem.remove();
                        selectedFiles.delete(fileId);

                        // Clear input if no files
                        if (selectedFiles.size === 0) {
                            attachmentInput.value = '';
                        }
                    });

                    attachmentList.appendChild(listItem);
                }

                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
                }
            }

            console.log('[BOTTOM] Reached bottom of App JS script');

            // Deferred initialization of quick create modal to avoid conflicts with page-specific forms
            console.log('[READY-STATE] Script bottom: document.readyState =', document.readyState);

            if (document.readyState === 'loading') {
                console.log('[DOM-LOADING] DOM still loading, attaching DOMContentLoaded listener');
                document.addEventListener('DOMContentLoaded', function () {
                    console.log('[DOM-READY] DOMContentLoaded fired, calling attachQuickCreateModalListeners in 100ms');
                    setTimeout(attachQuickCreateModalListeners, 100);
                });
            } else {
                console.log('[DOM-READY] DOM already loaded, calling attachQuickCreateModalListeners in 100ms');
                setTimeout(attachQuickCreateModalListeners, 100);
            }

            // Quick Create function moved to global scope below (outside try-catch)
        } catch (error) {
            console.error('❌ FATAL ERROR in App JS main script block:', error);
            console.error('Stack:', error.stack);
        }
    </script>

    <!-- Global submitQuickCreate function (outside try-catch for onclick access) -->
    <script>
        // Make submitQuickCreate globally accessible for onclick handler
        window.submitQuickCreate = async function submitQuickCreate() {
            console.log('[SUBMIT] submitQuickCreate() called');
            const form = document.getElementById('quickCreateForm');
            const summaryField = document.getElementById('quickCreateSummary');

            // Validate summary
            if (!summaryField.value.trim()) {
                console.log('[SUBMIT] Summary is empty');
                document.getElementById('summaryError').textContent = 'Summary is required';
                summaryField.classList.add('is-invalid');
                return;
            }

            // Clear previous error
            document.getElementById('summaryError').textContent = '';
            summaryField.classList.remove('is-invalid');

            // Check required fields
            const projectSelect = document.getElementById('quickCreateProject');
            const issueTypeSelect = document.getElementById('quickCreateIssueType');

            console.log('[SUBMIT] Project value:', projectSelect.value);
            console.log('[SUBMIT] Issue Type value:', issueTypeSelect.value);
            console.log('[SUBMIT] Summary value:', summaryField.value);

            if (!projectSelect.value) {
                console.log('[SUBMIT] Project not selected');
                projectSelect.classList.add('is-invalid');
                alert('Please select a project');
                return;
            }

            if (!issueTypeSelect.value) {
                console.log('[SUBMIT] Issue type not selected');
                issueTypeSelect.classList.add('is-invalid');
                alert('Please select an issue type');
                return;
            }

            if (!form.reportValidity()) {
                console.log('[SUBMIT] Form validation failed');
                return;
            }

            console.log('[SUBMIT] All validations passed, proceeding with submission');

            const formData = new FormData(form);
            const btn = document.getElementById('quickCreateBtn');
            const originalText = btn.innerHTML;
            const createAnother = form.querySelector('[name="create_another"]').checked;

            try {
                console.log('[SUBMIT] Starting API request...');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating...';

                // ✅ FIX: Keep FormData as-is (don't convert to JSON - File objects can't be JSON serialized)
                // Remove the 'create_another' checkbox from FormData
                const formDataToSend = new FormData(form);
                formDataToSend.delete('create_another');

                // ✅ CRITICAL FIX: Add description attachments to FormData
                // Files from Quill editor are stored in descriptionAttachments map
                if (typeof descriptionAttachments !== 'undefined' && descriptionAttachments.size > 0) {
                    console.log('[SUBMIT] Adding description attachments:', descriptionAttachments.size);
                    for (const [fileId, file] of descriptionAttachments) {
                        // Add each file with the 'attachments' field name to match quick-create attachments
                        formDataToSend.append('attachments', file);
                        console.log(`[SUBMIT]   - Added: ${file.name} (${file.size} bytes)`);
                    }
                }

                // Log form data entries for debugging
                console.log('[SUBMIT] FormData entries:');
                for (const [key, value] of formDataToSend.entries()) {
                    if (value instanceof File) {
                        console.log(`  ${key}: File(${value.name}, ${value.size} bytes, ${value.type})`);
                    } else {
                        console.log(`  ${key}: ${value}`);
                    }
                }

                // Get project key from selected project
                const projectSelect = document.getElementById('quickCreateProject');
                const selectedProjectId = projectSelect.value;
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                let projectKey = selectedOption ? selectedOption.dataset.projectKey : null;
                
                console.log('[SUBMIT] Project ID:', selectedProjectId);
                console.log('[SUBMIT] Project Key from dataset:', projectKey);
                console.log('[SUBMIT] Selected option:', selectedOption);
                console.log('[SUBMIT] Selected option dataset:', selectedOption ? selectedOption.dataset : 'null');

                // ✅ CRITICAL FIX: Fallback to projectsMap if dataset not available
                if (!projectKey && selectedProjectId && window.projectsMap[selectedProjectId]) {
                    projectKey = window.projectsMap[selectedProjectId].key;
                    console.log('[SUBMIT] Using projectKey from window.projectsMap:', projectKey);
                } else if (!projectKey && selectedOption && selectedOption.text) {
                    // ✅ EMERGENCY FALLBACK: Extract key from option text "Name (KEY)"
                    const match = selectedOption.text.match(/\(([A-Z0-9]+)\)$/);
                    if (match) {
                        projectKey = match[1];
                        console.log('[SUBMIT] ✅ Extracted projectKey from option text:', projectKey);
                    }
                }

                if (!projectKey) {
                    console.error('[SUBMIT] ✗ Could not determine project key');
                    console.error('[SUBMIT] Selected project ID:', selectedProjectId);
                    console.error('[SUBMIT] Selected project option:', projectSelect.options[projectSelect.selectedIndex]);
                    console.error('[SUBMIT] window.projectsMap keys:', Object.keys(window.projectsMap));
                    console.error('[SUBMIT] window.projectsMap:', window.projectsMap);
                    console.error('[SUBMIT] All dropdown options:');
                    for (let i = 0; i < projectSelect.options.length; i++) {
                        console.error(`   [${i}] value="${projectSelect.options[i].value}" text="${projectSelect.options[i].text}" key="${projectSelect.options[i].dataset.projectKey}"`);
                    }
                    throw new Error('Could not determine project key. Please select a project and try again.');
                }

                // Create issue using web endpoint (uses session auth)
                 // ✅ FIX: Remove trailing slash from APP_BASE_PATH to avoid double slashes
                 const basePath = APP_BASE_PATH.endsWith('/') ? APP_BASE_PATH.slice(0, -1) : APP_BASE_PATH;
                 const webUrl = basePath + '/projects/' + projectKey + '/issues';
                 console.log('[SUBMIT] ✅ URL computed:', webUrl);
                 console.log('[SUBMIT]    basePath:', basePath);
                 console.log('[SUBMIT]    projectKey:', projectKey);
                 console.log('[SUBMIT] Posting to URL:', webUrl);

                // ✅ FIX: Send as FormData to preserve File objects
                // Don't set Content-Type header - let browser set it with proper boundary
                const response = await fetch(webUrl, {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        // ✅ REMOVED: 'Content-Type': 'application/json' 
                        // Browser will set: Content-Type: multipart/form-data; boundary=...
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formDataToSend,  // ✅ CHANGED: Use FormData instead of JSON.stringify()
                });

                console.log('[SUBMIT] ✓ Response received - status:', response.status);
                console.log('[SUBMIT] Response content-type:', response.headers.get('content-type'));
                console.log('[SUBMIT] Response URL (after redirects):', response.url);

                const responseText = await response.text();
                console.log('[SUBMIT] Raw response text length:', responseText.length);
                if (responseText.length < 500) {
                    console.log('[SUBMIT] Full response text:', responseText);
                } else {
                    console.log('[SUBMIT] Response text (first 500 chars):', responseText.substring(0, 500));
                }

                if (!response.ok) {
                    console.error('[SUBMIT] ✗ Error response:', responseText);
                    try {
                        const errorData = JSON.parse(responseText);
                        throw new Error(errorData.error || `HTTP ${response.status}`);
                    } catch (e) {
                        throw new Error(`HTTP ${response.status}: ${responseText.substring(0, 200)}`);
                    }
                }

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('[SUBMIT] ✗ Failed to parse response as JSON');
                    console.error('[SUBMIT] Parse error:', parseError.message);
                    console.error('[SUBMIT] Response text (first 500 chars):', responseText.substring(0, 500));
                    throw new Error('Invalid server response: ' + parseError.message);
                }

                console.log('[SUBMIT] ✓ Issue creation response received');
                 
                 // ✅ DEBUG: Log the actual structure
                 console.log('[SUBMIT] Response keys:', Object.keys(result));
                 console.log('[SUBMIT] Full response structure:', {
                     hasSuccess: 'success' in result,
                     hasIssueKey: 'issue_key' in result,
                     hasIssue: 'issue' in result,
                     hasError: 'error' in result,
                     hasItems: 'items' in result,
                     issueKeyValue: result.issue_key || 'undefined',
                     issueObjKeys: result.issue ? Object.keys(result.issue).slice(0, 5) : 'no issue object',
                     responseKeys: Object.keys(result).slice(0, 10)
                 });
                 
                 // ✅ DETECTION: Check if this is a projects list response (wrong endpoint)
                 if (result.items && result.total !== undefined && result.current_page !== undefined) {
                     console.error('[SUBMIT] ❌ WRONG ENDPOINT! Got projects list instead of issue response');
                     console.error('[SUBMIT] This means the form posted to /api/v1/projects instead of /projects/{KEY}/issues');
                     console.error('[SUBMIT] DEBUG INFO:');
                     console.error('[SUBMIT]    selectedProjectId:', selectedProjectId);
                     console.error('[SUBMIT]    projectKey:', projectKey);
                     console.error('[SUBMIT]    webUrl:', webUrl);
                     console.error('[SUBMIT]    projectsMap:', projectsMap);
                     throw new Error('Form posted to wrong endpoint. Project key not properly extracted. This is a bug in the modal initialization.');
                 }

                // ✅ FIX: Extract issue key from multiple possible locations
                // Tries root level first, then nested in issue object
                const issueKey = result.issue_key ||
                    (result.issue && result.issue.issue_key) ||
                    (result.data && result.data.issue_key);

                if (!issueKey) {
                    console.error('[SUBMIT] ✗ Issue key not found in response');
                    console.error('[SUBMIT] Full response object:', JSON.stringify(result, null, 2));
                    if (result.issue) {
                        console.error('[SUBMIT] Issue object keys:', Object.keys(result.issue));
                        console.error('[SUBMIT] First 500 chars of issue object:', JSON.stringify(result.issue).substring(0, 500));
                    }
                    // Check if this is actually a success despite key extraction issue
                    if (result.success === true && result.issue && result.issue.issue_key) {
                        console.log('[SUBMIT] ✓ Found issue_key in result.issue.issue_key');
                    }
                }

                if (issueKey) {
                    console.log('[SUBMIT] ✓ Issue key extracted:', issueKey);
                    if (createAnother) {
                        // Reset form for creating another issue
                        form.reset();
                        document.getElementById('summaryChar').textContent = '0';
                        document.getElementById('descChar').textContent = '0';
                        // Clear description attachments
                        if (typeof descriptionAttachments !== 'undefined') {
                            descriptionAttachments.clear();
                            const container = document.getElementById('descriptionAttachmentsContainer');
                            if (container) {
                                container.style.display = 'none';
                                const fileList = document.getElementById('descriptionAttachmentsList');
                                if (fileList) {
                                    fileList.innerHTML = '';
                                }
                            }
                        }
                        // Clear Quill editor
                        if (typeof quillEditor !== 'undefined' && quillEditor) {
                            quillEditor.setContents([]);
                        }
                        document.getElementById('quickCreateSummary').focus();
                        btn.disabled = false;
                        btn.innerHTML = originalText;

                        // Show success message
                        const successMsg = document.createElement('div');
                        successMsg.className = 'alert alert-success alert-dismissible fade show mt-2';
                        successMsg.innerHTML = `Issue <strong>${issueKey}</strong> created successfully!<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                        form.parentElement.insertBefore(successMsg, form);

                        // Auto-dismiss after 3 seconds
                        setTimeout(() => {
                            successMsg.remove();
                        }, 3000);
                    } else {
                        // Redirect to the new issue (preserves current host/IP)
                        // Use deployment-aware URL template
                        const issueUrl = ISSUE_VIEW_TEMPLATE.replace('{issueKey}', issueKey);
                        console.log('[SUBMIT] ✓ Redirecting to:', issueUrl);
                        window.location.href = issueUrl;
                    }
                } else if (result.error) {
                    throw new Error(result.error);
                } else if (result.success === true) {
                    // ✅ FALLBACK: If success is true, try harder to extract the key
                    console.warn('[SUBMIT] ⚠️ Success is true but key not extracted in standard locations');
                    // This shouldn't happen with current API, but gives us a safety net
                    throw new Error('Issue was created but response format was unexpected. Check browser console for full response structure (F12).');
                } else {
                    console.error('[SUBMIT] ✗ Unexpected response structure:', result);
                    throw new Error('Issue created but key extraction failed. Check browser console (F12) for diagnostic details.');
                }
            } catch (error) {
                console.error('Error creating issue:', error);
                alert('Error creating issue: ' + error.message);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        };
    </script>

    <!-- Quill Rich Text Editor JS -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>

    <!-- Quill Editor Initialization -->
    <script>
        let quillEditor = null;
        let descriptionAttachments = new Map(); // Store attached files for description

        function initializeQuillEditor() {
            try {
                if (quillEditor) {
                    console.log('ℹ️ Quill editor already initialized');
                    return;
                }

                const editorDiv = document.getElementById('quickCreateDescriptionEditor');
                if (!editorDiv) {
                    console.log('ℹ️ Quill editor container not found (may not be on this page)');
                    return;
                }

                // Initialize Quill with custom toolbar including attachment button
                quillEditor = new Quill('#quickCreateDescriptionEditor', {
                    theme: 'snow',
                    modules: {
                        toolbar: {
                            container: [
                                [{ 'header': [1, 2, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                ['blockquote', 'code-block'],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                ['link', 'image'],
                                ['attach-file'], // Custom attachment button
                                ['clean']
                            ],
                            handlers: {
                                'attach-file': function () {
                                    document.getElementById('descriptionAttachmentInput').click();
                                }
                            }
                        }
                    },
                    placeholder: 'Type description here...',
                    bounds: '#quickCreateDescriptionEditor'
                });

                // Add custom styling to the attach button
                const attachBtn = document.querySelector('.ql-toolbar .ql-attach-file');
                if (attachBtn) {
                    attachBtn.innerHTML = '<i class="bi bi-paperclip" style="font-size: 16px;"></i>';
                    attachBtn.setAttribute('title', 'Attach files');
                }

                // Sync Quill content to hidden textarea
                quillEditor.on('text-change', function () {
                    const content = quillEditor.root.innerHTML;
                    document.getElementById('quickCreateDescription').value = content;

                    // Update character count (approximate)
                    const textLength = quillEditor.getText().length - 1; // -1 for trailing newline
                    document.getElementById('descChar').textContent = Math.max(0, textLength);
                });

                // Setup attachment file input listener
                setupDescriptionAttachmentHandlers();

                console.log('✅ Quill editor initialized successfully with attachment support');
            } catch (error) {
                console.error('❌ Error initializing Quill:', error);
            }
        }

        function setupDescriptionAttachmentHandlers() {
            const fileInput = document.getElementById('descriptionAttachmentInput');

            // Handle file selection
            fileInput.addEventListener('change', function () {
                const files = Array.from(this.files);
                files.forEach(file => {
                    addDescriptionAttachment(file);
                });
                this.value = ''; // Reset input
            });

            // Handle drag and drop on description editor
            const editorDiv = document.getElementById('quickCreateDescriptionEditor');
            const editorArea = editorDiv.closest('.mb-4');

            if (editorArea) {
                editorArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    editorDiv.style.backgroundColor = '#F0DCE5';
                });

                editorArea.addEventListener('dragleave', () => {
                    editorDiv.style.backgroundColor = 'white';
                });

                editorArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    editorDiv.style.backgroundColor = 'white';
                    const files = Array.from(e.dataTransfer.files);
                    files.forEach(file => {
                        addDescriptionAttachment(file);
                    });
                });
            }
        }

        function addDescriptionAttachment(file) {
            // Validate file size (10MB max)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
                return;
            }

            // Validate file type
            const allowedTypes = ['application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/zip',
                'video/mp4', 'video/webm', 'video/quicktime'];

            if (!allowedTypes.includes(file.type)) {
                alert(`File type not allowed: ${file.type}`);
                return;
            }

            // Generate unique ID for this file
            const fileId = 'desc-attach-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

            // Store file
            descriptionAttachments.set(fileId, file);

            // Show container if hidden
            const container = document.getElementById('descriptionAttachmentsContainer');
            container.style.display = 'block';

            // Create file item element
            const fileList = document.getElementById('descriptionAttachmentsList');
            const fileItem = createDescriptionFileItem(fileId, file);
            fileList.appendChild(fileItem);
        }

        function createDescriptionFileItem(fileId, file) {
            const div = document.createElement('div');
            div.id = fileId;
            div.style.cssText = `
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px;
                background: white;
                border: 1px solid #DFE1E6;
                border-radius: 4px;
                transition: all 0.2s ease;
            `;

            // File icon based on type
            const icon = getFileIcon(file.name);
            const fileSize = formatFileSize(file.size);

            div.innerHTML = `
                <div style="font-size: 20px; color: #8B1956; min-width: 24px; text-align: center;">
                    ${icon}
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 500; font-size: 13px; color: #161B22; word-break: break-word; white-space: normal;">
                        ${escapeHtml(file.name)}
                    </div>
                    <div style="font-size: 12px; color: #626F86; margin-top: 2px;">
                        ${fileSize}
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-ghost-danger" style="
                    border: none;
                    background: none;
                    color: #ae2a19;
                    cursor: pointer;
                    padding: 4px 8px;
                    font-size: 18px;
                    line-height: 1;
                    transition: color 0.2s;
                " title="Remove file" onclick="removeDescriptionAttachment('${fileId}')">
                    ×
                </button>
            `;

            // Hover effect
            div.addEventListener('mouseenter', () => {
                div.style.backgroundColor = '#DEEBFF';
                div.style.borderColor = '#8B1956';
            });
            div.addEventListener('mouseleave', () => {
                div.style.backgroundColor = 'white';
                div.style.borderColor = '#DFE1E6';
            });

            return div;
        }

        function removeDescriptionAttachment(fileId) {
            descriptionAttachments.delete(fileId);
            const fileItem = document.getElementById(fileId);
            if (fileItem) {
                fileItem.remove();
            }

            // Hide container if empty
            if (descriptionAttachments.size === 0) {
                document.getElementById('descriptionAttachmentsContainer').style.display = 'none';
            }
        }

        function getFileIcon(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const icons = {
                'pdf': '📄',
                'doc': '📝', 'docx': '📝',
                'xls': '📊', 'xlsx': '📊',
                'ppt': '📽️', 'pptx': '📽️',
                'txt': '📄',
                'jpg': '🖼️', 'jpeg': '🖼️', 'png': '🖼️', 'gif': '🖼️', 'webp': '🖼️',
                'zip': '📦',
                'mp4': '🎬', 'webm': '🎬', 'mov': '🎬'
            };
            return icons[ext] || '📎';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        }

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

        // Initialize Quill when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeQuillEditor);
        } else {
            initializeQuillEditor();
        }

        // Re-initialize when modal opens
        document.addEventListener('show.bs.modal', function (e) {
            if (e.target.id === 'quickCreateModal' && quillEditor) {
                console.log('📝 Modal opened, Quill editor ready');
            }
        });
    </script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <!-- Real-Time Notifications (Server-Sent Events) -->
    <script src="<?= asset('js/realtime-notifications.js') ?>"></script>

    <script src="<?= asset('js/app.js') ?>"></script>

    <!-- Time Tracking Widget -->
    <script src="<?= url('/assets/js/floating-timer.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            FloatingTimer.init({ syncInterval: 5000, debug: false });
        });
    </script>

    <?= \App\Core\View::yield('scripts') ?>
</body>

</html>