<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Administration</h2>
            <p class="text-muted mb-0">Manage users, roles, and system settings</p>
        </div>
    </div>

    <!-- System Stats -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-people text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= e($stats['total_users'] ?? 0) ?></h3>
                            <span class="text-muted">Total Users</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-folder text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= e($stats['total_projects'] ?? 0) ?></h3>
                            <span class="text-muted">Projects</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-list-task text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= e($stats['total_issues'] ?? 0) ?></h3>
                            <span class="text-muted">Total Issues</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-hdd text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= e($stats['storage_used'] ?? '0 MB') ?></h3>
                            <span class="text-muted">Storage Used</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Admin Menu -->
        <div class="col-lg-8">
            <div class="row g-4">
                <!-- User Management -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="bi bi-people-fill text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">User Management</h5>
                                    <small class="text-muted">Manage user accounts</small>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="<?= url('/admin/users') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> View All Users
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/admin/users/create') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> Create User
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= url('/admin/users?status=inactive') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> Inactive Users
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Role Management -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="bi bi-shield-check text-success fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">Roles & Permissions</h5>
                                    <small class="text-muted">Configure access control</small>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="<?= url('/admin/roles') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> Manage Roles
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/admin/permissions') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> Permission Schemes
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= url('/admin/global-permissions') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> Global Permissions
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Project Settings -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="bi bi-folder-fill text-info fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">Projects</h5>
                                    <small class="text-muted">Project administration</small>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="<?= url('/admin/projects') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> All Projects
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/admin/project-categories') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> Project Categories
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= url('/admin/issue-types') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> Issue Types
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="bi bi-gear-fill text-warning fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">System</h5>
                                    <small class="text-muted">System configuration</small>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="<?= url('/admin/settings') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> General Settings
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/admin/audit-log') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> Audit Log
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= url('/admin/system-info') ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-right me-2"></i> System Information
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Admin Activity</h5>
                    <a href="<?= url('/admin/audit-log') ?>" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($recentActivity)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-activity fs-1 d-block mb-2"></i>
                        No recent activity
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentActivity ?? [] as $activity): ?>
                        <div class="list-group-item">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-2">
                                    <?php 
                                    $action = $activity['action'] ?? '';
                                    if (str_contains($action, 'create')): ?>
                                    <i class="bi bi-plus-circle text-success"></i>
                                    <?php elseif (str_contains($action, 'delete')): ?>
                                    <i class="bi bi-trash text-danger"></i>
                                    <?php elseif (str_contains($action, 'update')): ?>
                                    <i class="bi bi-pencil text-warning"></i>
                                    <?php elseif (str_contains($action, 'login')): ?>
                                    <i class="bi bi-box-arrow-in-right text-info"></i>
                                    <?php else: ?>
                                    <i class="bi bi-activity text-primary"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small"><?= e(ucwords(str_replace('_', ' ', $action))) ?> <?= e($activity['entity_type'] ?? '') ?></div>
                                    <div class="text-muted smaller">
                                        <?= e($activity['user_name'] ?? 'System') ?> • <?= time_ago($activity['created_at']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent">
            <h5 class="mb-0"><i class="bi bi-heart-pulse me-2"></i>System Health</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-database fs-4 text-success me-3"></i>
                        <div>
                            <div class="fw-medium">Database</div>
                            <small class="text-success">Connected</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-envelope fs-4 text-success me-3"></i>
                        <div>
                            <div class="fw-medium">Email Service</div>
                            <small class="text-success">Operational</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-hdd fs-4 text-warning me-3"></i>
                        <div>
                            <div class="fw-medium">Disk Space</div>
                            <small class="text-warning"><?= e($stats['disk_usage'] ?? '75%') ?> used</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock fs-4 text-success me-3"></i>
                        <div>
                            <div class="fw-medium">Background Jobs</div>
                            <small class="text-success">Running</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification System Health (FIX 8) -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent">
            <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Notification System Health</h5>
        </div>
        <div class="card-body">
            <?php 
            $errorStats = \App\Helpers\NotificationLogger::getErrorStats();
            $isOperational = \App\Helpers\NotificationLogger::isLogOperational();
            ?>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle fs-4 <?= $isOperational ? 'text-success' : 'text-danger' ?> me-3"></i>
                        <div>
                            <div class="fw-medium">Status</div>
                            <small class="text-<?= $isOperational ? 'success' : 'danger' ?>">
                                <?= $isOperational ? 'Operational' : 'Issues Detected' ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-circle fs-4 <?= $errorStats['total_errors'] > 0 ? 'text-danger' : 'text-success' ?> me-3"></i>
                        <div>
                            <div class="fw-medium">Errors (24h)</div>
                            <small class="text-<?= $errorStats['total_errors'] > 0 ? 'danger' : 'success' ?>">
                                <?= e($errorStats['total_errors'] ?? 0) ?> errors
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-arrow-repeat fs-4 text-info me-3"></i>
                        <div>
                            <div class="fw-medium">Retries</div>
                            <small class="text-info"><?= e($errorStats['retry_count'] ?? 0) ?> queued</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-text fs-4 text-muted me-3"></i>
                        <div>
                            <div class="fw-medium">Log Size</div>
                            <small class="text-muted"><?= \App\Helpers\NotificationLogger::getLogFileSizeFormatted() ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($errorStats['recent_errors'])): ?>
            <div class="mt-3 pt-3 border-top">
                <h6 class="mb-2">Recent Errors:</h6>
                <div style="max-height: 200px; overflow-y: auto;">
                    <ul class="list-unstyled mb-0 small">
                        <?php foreach (array_slice($errorStats['recent_errors'], -5) as $error): ?>
                        <li class="text-danger mb-1">
                            <i class="bi bi-x-circle me-2"></i><?= e(substr($error, 0, 80)) ?>...
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
