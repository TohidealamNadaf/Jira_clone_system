<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid dashboard-container">
    <!-- Welcome Header -->
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg me-3">
                        <?php if ($user['avatar'] ?? null): ?>
                            <img src="<?= e($user['avatar']) ?>" class="rounded-circle" width="56" height="56" alt="">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 56px; height: 56px; font-size: 1.5rem;">
                                <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="mb-0">Good <?= date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening') ?>, <?= e($user['first_name'] ?? 'User') ?>!</h2>
                        <p class="text-muted mb-0">
                            <?php if ($stats['overdue'] > 0): ?>
                                <span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i><?= $stats['overdue'] ?> overdue</span> · 
                            <?php endif; ?>
                            <?= $stats['assigned_count'] ?> issues assigned · <?= $stats['in_progress'] ?> in progress
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickCreateModal">
                    <i class="bi bi-plus-lg me-1"></i> Create Issue
                </a>
                <a href="<?= url('/projects/create') ?>" class="btn btn-outline-primary ms-2">
                    <i class="bi bi-folder-plus me-1"></i> New Project
                </a>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-xl">
            <a href="<?= url('/search?assignee=currentUser()') ?>" class="card stat-card border-0 shadow-sm h-100 text-decoration-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stat-value"><?= e($stats['assigned_count']) ?></div>
                            <div class="stat-label">Assigned</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <a href="<?= url('/search?reporter=currentUser()') ?>" class="card stat-card border-0 shadow-sm h-100 text-decoration-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stat-value"><?= e($stats['reported_count']) ?></div>
                            <div class="stat-label">Reported</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <a href="<?= url('/search?assignee=currentUser()&status=In+Progress') ?>" class="card stat-card border-0 shadow-sm h-100 text-decoration-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-play-circle"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stat-value"><?= e($stats['in_progress']) ?></div>
                            <div class="stat-label">In Progress</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <a href="<?= url('/search?assignee=currentUser()&due=week') ?>" class="card stat-card border-0 shadow-sm h-100 text-decoration-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stat-value"><?= e($stats['due_soon']) ?></div>
                            <div class="stat-label">Due Soon</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <a href="<?= url('/search?assignee=currentUser()&due=overdue') ?>" class="card stat-card border-0 shadow-sm h-100 text-decoration-none <?= $stats['overdue'] > 0 ? 'stat-card-danger' : '' ?>">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stat-value"><?= e($stats['overdue']) ?></div>
                            <div class="stat-label">Overdue</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Left Column: Your Work -->
        <div class="col-xl-8">
            <!-- Your Work Card with Tabs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap">
                        <h5 class="mb-0"><i class="bi bi-kanban me-2"></i>Your Work</h5>
                        <a href="<?= url('/search?assignee=currentUser()') ?>" class="btn btn-sm btn-outline-primary flex-shrink-0">View All</a>
                    </div>
                    <ul class="nav nav-tabs card-header-tabs" id="yourWorkTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned-panel" type="button">
                                Assigned to Me <span class="badge bg-primary ms-1"><?= count($assignedIssues) ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reported-tab" data-bs-toggle="tab" data-bs-target="#reported-panel" type="button">
                                Reported <span class="badge bg-secondary ms-1"><?= count($reportedIssues) ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="watched-tab" data-bs-toggle="tab" data-bs-target="#watched-panel" type="button">
                                <i class="bi bi-eye me-1"></i>Watching <span class="badge bg-secondary ms-1"><?= count($watchedIssues) ?></span>
                            </button>
                        </li>
                    </ul>
                </div>
                
                <!-- Quick Filters -->
                <div class="card-body border-bottom py-2">
                    <div class="d-flex flex-wrap gap-2" id="filterButtonsContainer">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="dashboardFilterIssues('all', event);">
                            All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="dashboardFilterIssues('high-priority', event);">
                            <i class="bi bi-arrow-up text-danger"></i> High Priority
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="dashboardFilterIssues('due-soon', event);">
                            <i class="bi bi-clock text-warning"></i> Due Soon
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="dashboardFilterIssues('updated-today', event);">
                            <i class="bi bi-calendar-check text-success"></i> Updated Today
                        </button>
                    </div>
                    <script>
                    // Define filter function inline before buttons reference it
                    function dashboardFilterIssues(filterType, event) {
                        if (event) {
                            event.preventDefault();
                        }
                        
                        console.log('Filter clicked:', filterType);
                        
                        // Get all filter buttons in the container
                        var buttons = document.querySelectorAll('#filterButtonsContainer .btn-outline-secondary');
                        buttons.forEach(function(btn) {
                            btn.classList.remove('active');
                        });
                        
                        // Set active button - find the one that was clicked
                        if (event && event.currentTarget) {
                            event.currentTarget.classList.add('active');
                        }
                        
                        // Apply filter to all issue rows
                        var issueRows = document.querySelectorAll('.issue-row');
                        console.log('Total issue rows:', issueRows.length);
                        
                        var visibleCount = 0;
                        issueRows.forEach(function(row) {
                            var priority = row.getAttribute('data-priority') || '';
                            var due = row.getAttribute('data-due') || '';
                            var updatedToday = row.getAttribute('data-updated-today') === 'true';
                            
                            var visible = true;
                            switch(filterType) {
                                case 'high-priority':
                                    visible = (priority === 'high' || priority === 'highest' || priority === 'critical');
                                    break;
                                case 'due-soon':
                                    visible = (due === 'due-soon' || due === 'overdue');
                                    break;
                                case 'updated-today':
                                    visible = updatedToday;
                                    break;
                                default: // 'all'
                                    visible = true;
                            }
                            
                            if (visible) visibleCount++;
                            row.style.display = visible ? 'flex' : 'none';
                        });
                        
                        console.log('Visible rows after filter:', visibleCount);
                    }
                    </script>
                </div>
                
                <div class="card-body p-0">
                    <div class="tab-content">
                        <!-- Assigned Panel -->
                        <div class="tab-pane fade show active" id="assigned-panel" role="tabpanel">
                            <?php if (empty($assignedIssues)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p class="mb-0">No issues assigned to you</p>
                                <a href="#" class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#quickCreateModal">Create your first issue</a>
                            </div>
                            <?php else: ?>
                            <div class="issue-list">
                                <?php foreach ($assignedIssues as $issue): ?>
                                <?php
                                    $dueBucket = 'none';
                                    if (!empty($issue['due_date'])) {
                                        $dueTs = strtotime($issue['due_date']);
                                        $today = strtotime('today');
                                        $week = strtotime('+7 days', $today);
                                        if ($dueTs < $today) $dueBucket = 'overdue';
                                        elseif ($dueTs <= $week) $dueBucket = 'due-soon';
                                    }
                                    $updatedToday = date('Y-m-d', strtotime($issue['updated_at'])) === date('Y-m-d');
                                    $prioritySlug = strtolower(str_replace(' ', '-', $issue['priority_name']));
                                ?>
                                <a href="<?= url('/issue/' . $issue['issue_key']) ?>" 
                                   class="issue-row"
                                   data-priority="<?= e($prioritySlug) ?>"
                                   data-due="<?= e($dueBucket) ?>"
                                   data-updated-today="<?= $updatedToday ? 'true' : 'false' ?>">
                                    <div class="issue-type-icon" style="background-color: <?= e($issue['issue_type_color']) ?>">
                                        <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                                    </div>
                                    <div class="issue-key"><?= e($issue['issue_key']) ?></div>
                                    <div class="issue-summary"><?= e($issue['summary']) ?></div>
                                    <?php if ($dueBucket === 'overdue'): ?>
                                    <span class="badge bg-danger me-1"><i class="bi bi-exclamation-triangle"></i> Overdue</span>
                                    <?php elseif ($dueBucket === 'due-soon'): ?>
                                    <span class="badge bg-warning text-dark me-1"><i class="bi bi-clock"></i> Due soon</span>
                                    <?php endif; ?>
                                    <span class="issue-priority" style="background-color: <?= e($issue['priority_color']) ?>">
                                        <?= e($issue['priority_name']) ?>
                                    </span>
                                    <span class="issue-status" style="background-color: <?= e($issue['status_color']) ?>">
                                        <?= e($issue['status_name']) ?>
                                    </span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Reported Panel -->
                        <div class="tab-pane fade" id="reported-panel" role="tabpanel">
                            <?php if (empty($reportedIssues)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
                                <p class="mb-0">No issues reported by you</p>
                            </div>
                            <?php else: ?>
                            <div class="issue-list">
                                <?php foreach ($reportedIssues as $issue): ?>
                                <?php
                                    $dueBucket = 'none';
                                    if (!empty($issue['due_date'])) {
                                        $dueTs = strtotime($issue['due_date']);
                                        $today = strtotime('today');
                                        $week = strtotime('+7 days', $today);
                                        if ($dueTs < $today) $dueBucket = 'overdue';
                                        elseif ($dueTs <= $week) $dueBucket = 'due-soon';
                                    }
                                    $updatedToday = date('Y-m-d', strtotime($issue['updated_at'])) === date('Y-m-d');
                                    $prioritySlug = strtolower(str_replace(' ', '-', $issue['priority_name'] ?? ''));
                                ?>
                                <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="issue-row"
                                   data-priority="<?= e($prioritySlug) ?>"
                                   data-due="<?= e($dueBucket) ?>"
                                   data-updated-today="<?= $updatedToday ? 'true' : 'false' ?>">
                                    <div class="issue-type-icon" style="background-color: <?= e($issue['issue_type_color'] ?? '#0052CC') ?>">
                                        <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                                    </div>
                                    <div class="issue-key"><?= e($issue['issue_key']) ?></div>
                                    <div class="issue-summary"><?= e($issue['summary']) ?></div>
                                    <?php if ($issue['assignee_name']): ?>
                                    <span class="text-muted small me-2"><i class="bi bi-person"></i> <?= e($issue['assignee_name']) ?></span>
                                    <?php else: ?>
                                    <span class="text-muted small me-2">Unassigned</span>
                                    <?php endif; ?>
                                    <span class="issue-status" style="background-color: <?= e($issue['status_color']) ?>">
                                        <?= e($issue['status_name']) ?>
                                    </span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Watched Panel -->
                        <div class="tab-pane fade" id="watched-panel" role="tabpanel">
                            <?php if (empty($watchedIssues)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-eye fs-1 d-block mb-2"></i>
                                <p class="mb-0">You aren't watching any issues</p>
                                <small class="text-muted">Click the eye icon on any issue to start watching it</small>
                            </div>
                            <?php else: ?>
                            <div class="issue-list">
                                <?php foreach ($watchedIssues as $issue): ?>
                                <?php
                                    $dueBucket = 'none';
                                    if (!empty($issue['due_date'])) {
                                        $dueTs = strtotime($issue['due_date']);
                                        $today = strtotime('today');
                                        $week = strtotime('+7 days', $today);
                                        if ($dueTs < $today) $dueBucket = 'overdue';
                                        elseif ($dueTs <= $week) $dueBucket = 'due-soon';
                                    }
                                    $updatedToday = date('Y-m-d', strtotime($issue['updated_at'])) === date('Y-m-d');
                                    $prioritySlug = strtolower(str_replace(' ', '-', $issue['priority_name'] ?? ''));
                                ?>
                                <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="issue-row"
                                   data-priority="<?= e($prioritySlug) ?>"
                                   data-due="<?= e($dueBucket) ?>"
                                   data-updated-today="<?= $updatedToday ? 'true' : 'false' ?>">
                                    <div class="issue-type-icon" style="background-color: <?= e($issue['issue_type_color']) ?>">
                                        <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                                    </div>
                                    <div class="issue-key"><?= e($issue['issue_key']) ?></div>
                                    <div class="issue-summary"><?= e($issue['summary']) ?></div>
                                    <i class="bi bi-eye text-muted me-2"></i>
                                    <?php if ($issue['assignee_name']): ?>
                                    <span class="text-muted small me-2"><?= e($issue['assignee_name']) ?></span>
                                    <?php endif; ?>
                                    <span class="issue-status" style="background-color: <?= e($issue['status_color']) ?>">
                                        <?= e($issue['status_name']) ?>
                                    </span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Active Sprints -->
            <?php if (!empty($activeSprints)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Active Sprints</h5>
                </div>
                <div class="card-body p-0">
                    <div class="sprint-list">
                        <?php foreach ($activeSprints as $sprint): ?>
                        <div class="sprint-row">
                            <div class="sprint-header">
                                <div class="sprint-info">
                                    <a href="<?= url('/projects/' . $sprint['project_key'] . '/board') ?>" class="sprint-name"><?= e($sprint['name']) ?></a>
                                    <span class="sprint-project"><?= e($sprint['project_key']) ?> · <?= e($sprint['board_name']) ?></span>
                                </div>
                                <div class="sprint-meta">
                                    <span class="sprint-health sprint-health-<?= $sprint['health'] ?>">
                                        <?= e($sprint['health_label']) ?>
                                    </span>
                                    <span class="sprint-stats">
                                        <?= e($sprint['done_count']) ?>/<?= e($sprint['issue_count']) ?> issues done
                                    </span>
                                </div>
                            </div>
                            <div class="sprint-progress">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?= $sprint['progress_percent'] ?>%"></div>
                                </div>
                                <div class="sprint-progress-marker" style="left: <?= $sprint['ideal_percent'] ?>%"></div>
                            </div>
                            <div class="sprint-footer">
                                <span><?= $sprint['days_remaining'] ?> days remaining</span>
                                <span>Ends <?= format_date($sprint['end_date']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Right Column: Sidebar -->
        <div class="col-xl-4">
            <!-- Projects -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-folder me-2"></i>Projects</h5>
                    <a href="<?= url('/projects') ?>" class="btn btn-sm btn-outline-primary">All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($projects)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-folder fs-2 d-block mb-2"></i>
                        <p class="small mb-0">No projects yet</p>
                    </div>
                    <?php else: ?>
                    <div class="project-list">
                        <?php foreach ($projects as $project): ?>
                        <a href="<?= url('/projects/' . $project['key']) ?>" class="project-row">
                            <div class="project-avatar">
                                <?php if ($project['avatar']): ?>
                                <img src="<?= e($project['avatar']) ?>" class="rounded" width="32" height="32">
                                <?php else: ?>
                                <div class="project-avatar-placeholder">
                                    <?= strtoupper(substr($project['key'], 0, 2)) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="project-info">
                                <div class="project-name"><?= e($project['name']) ?></div>
                                <div class="project-key"><?= e($project['key']) ?></div>
                            </div>
                            <div class="project-stats">
                                <span class="badge bg-primary"><?= e($project['open_issues']) ?></span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Workload & Status Distribution -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>My Workload</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($workloadByProject)): ?>
                    <p class="text-muted small mb-0">No open issues assigned to you.</p>
                    <?php else: ?>
                    <?php $totalWork = array_sum(array_column($workloadByProject, 'issue_count')); ?>
                    <?php foreach ($workloadByProject as $row): ?>
                    <?php $percent = $totalWork > 0 ? round(($row['issue_count'] / $totalWork) * 100) : 0; ?>
                    <div class="workload-row">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <a href="<?= url('/projects/' . $row['key']) ?>" class="workload-project">
                                <strong><?= e($row['key']) ?></strong>
                                <span class="text-muted"><?= e($row['name']) ?></span>
                            </a>
                            <span class="text-muted small"><?= $row['issue_count'] ?></span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Status Distribution Pie -->
                    <?php if (!empty($statusDistribution)): ?>
                    <hr class="my-3">
                    <h6 class="mb-3">Status Distribution</h6>
                    <?php
                        $total = array_sum(array_column($statusDistribution, 'issue_count'));
                        $startAngle = 0;
                        $segments = [];
                        foreach ($statusDistribution as $row) {
                            $angle = $total > 0 ? ($row['issue_count'] / $total) * 360 : 0;
                            $endAngle = $startAngle + $angle;
                            $segments[] = sprintf('%s %sdeg %sdeg', $row['color'], $startAngle, $endAngle);
                            $startAngle = $endAngle;
                        }
                        $gradient = implode(', ', $segments);
                    ?>
                    <div class="d-flex align-items-center">
                        <div class="status-pie me-3" style="background: conic-gradient(<?= $gradient ?>);"></div>
                        <div class="flex-grow-1">
                            <?php foreach ($statusDistribution as $row): ?>
                            <div class="d-flex align-items-center mb-1 small">
                                <span class="status-dot me-2" style="background-color: <?= e($row['color']) ?>"></span>
                                <span class="me-auto"><?= e($row['name']) ?></span>
                                <span class="text-muted"><?= $row['issue_count'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Activity Stream</h5>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($recentActivity)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-activity fs-2 d-block mb-2"></i>
                        <p class="small mb-0">No recent activity</p>
                    </div>
                    <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recentActivity as $activity): ?>
                        <?php
                            $field = $activity['field'] ?? '';
                            $icon = 'bi-pencil-square';
                            $iconBg = 'bg-secondary';
                            if ($field === 'status') { $icon = 'bi-arrow-left-right'; $iconBg = 'bg-primary'; }
                            elseif ($field === 'assignee') { $icon = 'bi-person-check'; $iconBg = 'bg-info'; }
                            elseif ($field === 'comment') { $icon = 'bi-chat-dots'; $iconBg = 'bg-success'; }
                            elseif ($field === 'priority') { $icon = 'bi-exclamation-triangle'; $iconBg = 'bg-warning'; }
                            elseif ($field === 'created') { $icon = 'bi-plus-circle'; $iconBg = 'bg-success'; }
                        ?>
                        <div class="activity-row">
                            <div class="activity-icon <?= $iconBg ?> bg-opacity-10">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-text">
                                    <strong><?= e($activity['user_name'] ?? 'System') ?></strong>
                                    <?php if ($field === 'comment'): ?>
                                        commented on
                                    <?php elseif ($field === 'created'): ?>
                                        created
                                    <?php else: ?>
                                        updated <em><?= e($field) ?></em> on
                                    <?php endif; ?>
                                    <a href="<?= url('/issue/' . $activity['issue_key']) ?>"><?= e($activity['issue_key']) ?></a>
                                </div>
                                <?php if (!empty($activity['from_value']) || !empty($activity['to_value'])): ?>
                                <div class="activity-change">
                                    <span class="activity-from"><?= e($activity['from_value'] ?? '—') ?></span>
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    <span class="activity-to"><?= e($activity['to_value'] ?? '—') ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="activity-time"><?= time_ago($activity['created_at']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Create Modal Data -->
<script type="application/json" id="quickCreateProjectsData">
<?php 
$projectsForModal = [];
if (!empty($allProjectsForModal)) {
    $issueService = new \App\Services\IssueService();
    foreach ($allProjectsForModal as $project) {
        $fullProject = $issueService->getProjectWithDetails($project['id']);
        $projectsForModal[] = [
            'id' => $fullProject['id'],
            'key' => $fullProject['key'],
            'name' => $fullProject['name'],
            'issue_types' => $fullProject['issue_types'] ?? [],
        ];
    }
}
echo json_encode($projectsForModal);
?>
</script>

<?php \App\Core\View::endSection(); ?>
