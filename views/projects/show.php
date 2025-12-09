<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item active"><?= e($project['name']) ?></li>
        </ol>
    </nav>

    <!-- Project Header -->
    <div class="d-flex align-items-start justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <?php if ($project['avatar'] ?? null): ?>
            <img src="<?= e($project['avatar']) ?>" class="rounded me-3" width="64" height="64" alt="">
            <?php else: ?>
            <div class="rounded me-3 d-flex align-items-center justify-content-center bg-primary text-white" 
                 style="width: 64px; height: 64px; font-size: 1.5rem;">
                <?= strtoupper(substr($project['key'], 0, 2)) ?>
            </div>
            <?php endif; ?>
            <div>
                <h1 class="h3 mb-1"><?= e($project['name']) ?></h1>
                <div class="text-muted">
                    <span class="badge bg-secondary me-2"><?= e($project['key']) ?></span>
                    <?php if ($project['category_name'] ?? null): ?>
                    <span class="me-2"><?= e($project['category_name']) ?></span>
                    <?php endif; ?>
                    <?php if ($project['is_archived'] ?? false): ?>
                    <span class="badge bg-warning">Archived</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="btn-group">
            <a href="<?= url("/projects/{$project['key']}/board") ?>" class="btn btn-outline-primary">
                <i class="bi bi-kanban me-1"></i> Board
            </a>
            <a href="<?= url("/projects/{$project['key']}/issues") ?>" class="btn btn-outline-primary">
                <i class="bi bi-list-ul me-1"></i> Issues
            </a>
            <?php if (can('edit-project', $project['id'])): ?>
            <a href="<?= url("/projects/{$project['key']}/settings") ?>" class="btn btn-outline-secondary">
                <i class="bi bi-gear me-1"></i> Settings
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($project['description'] ?? null): ?>
    <p class="lead text-muted mb-4"><?= e($project['description']) ?></p>
    <?php endif; ?>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-auto">
                            <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Create Issue
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="<?= url("/projects/{$project['key']}/backlog") ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-inbox me-1"></i> Backlog
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="<?= url("/projects/{$project['key']}/sprints") ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-lightning me-1"></i> Sprints
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="<?= url("/projects/{$project['key']}/reports") ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-bar-chart me-1"></i> Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h2 class="mb-0"><?= e($stats['total_issues'] ?? 0) ?></h2>
                            <small class="text-muted">Total Issues</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h2 class="mb-0 text-primary"><?= e($stats['open_issues'] ?? 0) ?></h2>
                            <small class="text-muted">Open</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h2 class="mb-0 text-warning"><?= e($stats['in_progress'] ?? 0) ?></h2>
                            <small class="text-muted">In Progress</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h2 class="mb-0 text-success"><?= e($stats['done_issues'] ?? 0) ?></h2>
                            <small class="text-muted">Done</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Issues -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Issues</h5>
                    <a href="<?= url("/projects/{$project['key']}/issues") ?>" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Key</th>
                                <th>Summary</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Assignee</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentIssues)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No issues yet. <a href="<?= url("/projects/{$project['key']}/issues/create") ?>">Create the first one</a>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($recentIssues as $issue): ?>
                            <tr>
                                <td>
                                    <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="text-decoration-none fw-medium">
                                        <?= e($issue['issue_key']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="text-decoration-none text-dark">
                                        <?= e(substr($issue['summary'], 0, 50)) ?><?= strlen($issue['summary']) > 50 ? '...' : '' ?>
                                    </a>
                                </td>
                                <td>
                                    <i class="bi bi-<?= e($issue['type']['icon'] ?? 'circle') ?> text-<?= e($issue['type']['color'] ?? 'secondary') ?>"></i>
                                    <?= e($issue['type']['name'] ?? 'Task') ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= e($issue['status']['color'] ?? 'secondary') ?>">
                                        <?= e($issue['status']['name'] ?? 'Open') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($issue['assignee'] ?? null): ?>
                                    <img src="<?= e($issue['assignee']['avatar'] ?? '/images/default-avatar.png') ?>" 
                                         class="rounded-circle" width="24" height="24" 
                                         title="<?= e($issue['assignee']['display_name']) ?>" data-bs-toggle="tooltip">
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small"><?= time_ago($issue['updated_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activity</h5>
                    <span class="badge bg-light text-dark"><?= count($activities) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($activities)): ?>
                    <div class="p-3 text-muted text-center">
                        <i class="bi bi-inbox fs-5 d-block mb-2"></i>
                        <p class="mb-0">No recent activity</p>
                    </div>
                    <?php else: ?>
                    <div class="activity-timeline" style="max-height: 400px; overflow-y: auto;">
                        <?php foreach (array_slice($activities, 0, 8) as $index => $activity): ?>
                        <div class="activity-item px-3 py-2 <?= $index !== count(array_slice($activities, 0, 8)) - 1 ? 'border-bottom' : '' ?>" style="border-bottom: 1px solid #e9ecef;">
                            <div class="d-flex gap-2" style="align-items: flex-start;">
                                <img src="<?= e($activity['user']['avatar'] ?? '/images/default-avatar.png') ?>" 
                                     class="rounded-circle flex-shrink-0" width="28" height="28" style="margin-top: 2px;">
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-600 text-dark" style="font-size: 0.95rem;"><?= e($activity['user']['display_name']) ?></span>
                                        <span class="text-muted" style="font-size: 0.85rem;">
                                            <?php 
                                            $description = $activity['description'];
                                            // Get action type for styling
                                            $actionType = substr($activity['action'], 0, strpos($activity['action'], '_') ?: 0) ?: $activity['action'];
                                            
                                            // Get action verb
                                            if (strpos($activity['action'], 'created') !== false) {
                                                $icon = '<i class="bi bi-plus-circle text-success"></i>';
                                                $verb = 'created';
                                            } elseif (strpos($activity['action'], 'updated') !== false) {
                                                $icon = '<i class="bi bi-pencil text-info"></i>';
                                                $verb = 'updated';
                                            } elseif (strpos($activity['action'], 'deleted') !== false) {
                                                $icon = '<i class="bi bi-trash text-danger"></i>';
                                                $verb = 'deleted';
                                            } elseif (strpos($activity['action'], 'assigned') !== false) {
                                                $icon = '<i class="bi bi-person-check text-primary"></i>';
                                                $verb = 'assigned';
                                            } elseif (strpos($activity['action'], 'comment') !== false) {
                                                $icon = '<i class="bi bi-chat text-warning"></i>';
                                                $verb = 'commented';
                                            } else {
                                                $icon = '<i class="bi bi-arrow-repeat text-secondary"></i>';
                                                $verb = 'updated';
                                            }
                                            
                                            echo $icon . ' ' . $verb;
                                            if ($activity['issue']) {
                                                $issueKey = $activity['issue']['key'];
                                                echo " <a href='" . url("/issue/{$issueKey}") . "' class='text-decoration-none fw-600'>{$issueKey}</a>";
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.8rem; margin-top: 2px;">
                                        <?= time_ago($activity['created_at']) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($activities) > 8): ?>
                    <div class="border-top p-2 text-center">
                        <a href="<?= url("/projects/{$project['key']}/activity") ?>" class="btn btn-sm btn-link text-primary">
                            View All Activities →
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Project Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Project Details</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Lead</dt>
                        <dd class="col-7">
                            <?php if ($project['lead'] ?? null): ?>
                            <div class="d-flex align-items-center">
                                <img src="<?= e($project['lead']['avatar'] ?? '/images/default-avatar.png') ?>" 
                                     class="rounded-circle me-2" width="24" height="24">
                                <?= e($project['lead']['display_name']) ?>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">Unassigned</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-5 text-muted">Type</dt>
                        <dd class="col-7"><?= e(ucfirst($project['project_type'] ?? 'Software')) ?></dd>

                        <dt class="col-5 text-muted">Created</dt>
                        <dd class="col-7"><?= date('M j, Y', strtotime($project['created_at'])) ?></dd>

                        <?php if ($project['url'] ?? null): ?>
                        <dt class="col-5 text-muted">URL</dt>
                        <dd class="col-7">
                            <a href="<?= e($project['url']) ?>" target="_blank" class="text-truncate d-block">
                                <?= e($project['url']) ?>
                            </a>
                        </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Team Members -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Team Members</h6>
                    <?php if (can('manage-members', $project['id'])): ?>
                    <a href="<?= url("/projects/{$project['key']}/members") ?>" class="btn btn-sm btn-link">Manage</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($members)): ?>
                    <p class="text-muted mb-0">No team members</p>
                    <?php else: ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach (array_slice($members, 0, 8) as $member): ?>
                        <img src="<?= e($member['avatar'] ?? '/images/default-avatar.png') ?>" 
                             class="rounded-circle" width="36" height="36" 
                             title="<?= e($member['display_name']) ?>" data-bs-toggle="tooltip">
                        <?php endforeach; ?>
                        <?php if (count($members) > 8): ?>
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                             style="width: 36px; height: 36px; font-size: 0.75rem;">
                            +<?= count($members) - 8 ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Active Sprint -->
            <?php if ($activeSprint ?? null): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Active Sprint</h6>
                </div>
                <div class="card-body">
                    <h6><?= e($activeSprint['name']) ?></h6>
                    <div class="progress mb-2" style="height: 8px;">
                        <?php $progress = $activeSprint['total_issues'] > 0 ? ($activeSprint['done_issues'] / $activeSprint['total_issues']) * 100 : 0; ?>
                        <div class="progress-bar bg-success" style="width: <?= $progress ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span><?= e($activeSprint['done_issues']) ?>/<?= e($activeSprint['total_issues']) ?> issues done</span>
                        <span><?= e($activeSprint['remaining_days']) ?> days left</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Links -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Links</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url("/projects/{$project['key']}/issues?status=open") ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Open Issues
                        <span class="badge bg-primary rounded-pill"><?= e($stats['open_issues'] ?? 0) ?></span>
                    </a>
                    <a href="<?= url("/projects/{$project['key']}/issues?assignee=me") ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        My Issues
                        <span class="badge bg-secondary rounded-pill"><?= e($stats['my_issues'] ?? 0) ?></span>
                    </a>
                    <a href="<?= url("/projects/{$project['key']}/issues?priority=highest") ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        High Priority
                        <span class="badge bg-danger rounded-pill"><?= e($stats['high_priority'] ?? 0) ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
