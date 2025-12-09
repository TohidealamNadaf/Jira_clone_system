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
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top" style="z-index: 2000;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?= url('/') ?>">
                <i class="bi bi-kanban me-2"></i>
                <?= e(config('app.name')) ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-folder me-1"></i> Projects
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= url('/projects') ?>">View All Projects</a></li>
                            <?php if (can('create-projects')): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('/projects/create') ?>">Create Project</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-list-task me-1"></i> Issues
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= url('/search?assignee=currentUser()') ?>">Assigned to Me</a></li>
                            <li><a class="dropdown-item" href="<?= url('/search?reporter=currentUser()') ?>">Reported by Me</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('/search') ?>">Search Issues</a></li>
                            <li><a class="dropdown-item" href="<?= url('/filters') ?>">Saved Filters</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-bar-chart me-1"></i> Reports
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= url('/reports') ?>">All Reports</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('/reports?type=burndown') ?>">Burndown Chart</a></li>
                            <li><a class="dropdown-item" href="<?= url('/reports?type=velocity') ?>">Velocity Chart</a></li>
                        </ul>
                    </li>
                    
                    <?php if ($user['is_admin'] ?? false): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/admin') ?>">
                            <i class="bi bi-gear me-1"></i> Admin
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Search -->
                <form class="d-flex me-3" action="<?= url('/search') ?>" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" placeholder="Search issues..." 
                               style="min-width: 200px;">
                        <button class="btn btn-outline-light" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Quick Create -->
                <a href="#" class="btn btn-light me-3" data-bs-toggle="modal" data-bs-target="#quickCreateModal">
                    <i class="bi bi-plus-lg"></i> Create
                </a>
                
                <!-- Notifications Bell Icon -->
                <div class="dropdown me-3">
                    <a class="nav-link text-white position-relative" href="#" id="notificationBell" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="unreadBadge" style="display: none;">
                            0
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 360px; max-height: 450px; overflow-y: auto;" id="notificationDropdown">
                        <h6 class="dropdown-header">
                            <i class="bi bi-bell-fill"></i> Notifications
                        </h6>
                        <div id="notificationList" style="max-height: 350px; overflow-y: auto;">
                            <div class="px-3 py-3 text-center text-muted">
                                <small>Loading notifications...</small>
                            </div>
                        </div>
                        <hr class="dropdown-divider my-0">
                        <a class="dropdown-item text-center py-2" href="<?= url('/notifications') ?>" style="font-size: 13px;">
                            View All Notifications
                        </a>
                    </div>
                </div>

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
                            <a href="${n.action_url || '#'}" class="dropdown-item d-flex align-items-start py-2 ${n.is_read ? '' : 'bg-light'}" style="text-decoration: none; border-left: 3px solid ${n.is_read ? 'transparent' : '#0052cc'};">
                                <div style="flex: 1;">
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
                
                <!-- User Menu -->
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" data-bs-toggle="dropdown">
                        <?php if ($user['avatar'] ?? null): ?>
                        <img src="<?= e($user['avatar']) ?>" class="rounded-circle me-2" width="32" height="32" alt="">
                        <?php else: ?>
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                             style="width: 32px; height: 32px;">
                            <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                        <span><?= e($user['display_name'] ?? 'User') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= url('/profile') ?>"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="<?= url('/profile/tokens') ?>"><i class="bi bi-key me-2"></i> API Tokens</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="<?= url('/logout') ?>" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
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
    <main class="py-4">
        <?= \App\Core\View::yield('content') ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-light border-top py-3 mt-auto">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 text-muted small">
                    &copy; <?= date('Y') ?> <?= e(config('app.name')) ?>. All rights reserved.
                </div>
                <div class="col-md-6 text-md-end text-muted small">
                    Version 1.0.0
                </div>
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
