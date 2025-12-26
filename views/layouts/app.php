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

    <!-- TinyMCE (Community Edition via CDNJS) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

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

    // Get current user for navbar
    $user = \App\Core\Session::user();
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

        /* TinyMCE in Bootstrap Modal Fix */
        .tox-tinymce {
            z-index: 2000;
            /* Ensure editor is above modal */
        }

        .tox-tinymce-aux {
            z-index: 3000 !important;
            /* Ensure dialogs/tooltips are above everything */
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

                <!-- Create Button -->
                <button class="navbar-action-btn create-btn" id="openCreateIssueModal" title="Create Issue">
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
                        data-user-avatar="<?= e(!empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['display_name'] ?? $user['first_name'] ?? 'User')) ?>">
                        <img src="<?= e(!empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['display_name'] ?? $user['first_name'] ?? 'User')) ?>"
                            alt="<?= e($user['display_name'] ?? $user['first_name'] ?? 'User') ?>" class="user-avatar">
                        <i class="bi bi-chevron-down d-none d-md-inline"></i>
                    </button>
                    <div class="dropdown-panel user-panel">
                        <div class="panel-header user-header">
                            <img src="<?= e(!empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['display_name'] ?? $user['first_name'] ?? 'User')) ?>"
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





    <!-- Modal Backdrop Overlay -->
    <div id="modalBackdrop"></div>

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

            // Quick Create Modal initialization removed (feature disabled)
        } catch (error) {
            console.error('❌ FATAL ERROR in App JS main script block:', error);
            console.error('Stack:', error.stack);
        }
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

    <!-- Create Issue Modal Component -->
    <?php include_once __DIR__ . '/../components/create-issue-modal.php'; ?>

    <!-- Create Issue Modal JavaScript -->
    <script src="<?= url('/assets/js/create-issue-modal.js') ?>"></script>
</body>

</html>