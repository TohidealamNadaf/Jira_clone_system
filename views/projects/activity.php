<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active">Activity</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Project Activity</h1>
            <p class="text-muted mb-0"><?= e($project['name']) ?> - All activities</p>
        </div>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Project
        </a>
    </div>

    <!-- Activity Timeline -->
    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($activities)): ?>
            <div class="p-5 text-center text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p>No activities recorded yet.</p>
            </div>
            <?php else: ?>
            <div class="activity-feed">
                <?php foreach ($activities as $activity): ?>
                <div class="activity-entry p-3 border-bottom" style="display: flex; gap: 12px;">
                    <img src="<?= e($activity['user']['avatar'] ?? '/images/default-avatar.png') ?>" 
                         class="rounded-circle flex-shrink-0" width="36" height="36">
                    
                    <div class="flex-grow-1">
                        <div class="mb-1">
                            <span class="fw-600"><?= e($activity['user']['display_name']) ?></span>
                            
                            <!-- Activity icon and verb -->
                            <span class="text-muted ms-2" style="font-size: 0.95rem;">
                                <?php 
                                if (strpos($activity['action'], 'created') !== false) {
                                    echo '<i class="bi bi-plus-circle text-success"></i> created';
                                } elseif (strpos($activity['action'], 'updated') !== false) {
                                    echo '<i class="bi bi-pencil text-info"></i> updated';
                                } elseif (strpos($activity['action'], 'deleted') !== false) {
                                    echo '<i class="bi bi-trash text-danger"></i> deleted';
                                } elseif (strpos($activity['action'], 'assigned') !== false) {
                                    echo '<i class="bi bi-person-check text-primary"></i> assigned';
                                } elseif (strpos($activity['action'], 'transitioned') !== false) {
                                    echo '<i class="bi bi-arrow-repeat text-warning"></i> moved';
                                } elseif (strpos($activity['action'], 'comment') !== false) {
                                    echo '<i class="bi bi-chat text-warning"></i> commented on';
                                } else {
                                    echo '<i class="bi bi-arrow-repeat text-secondary"></i> updated';
                                }
                                ?>
                            </span>
                            
                            <!-- Issue link if applicable -->
                            <?php if ($activity['issue']): ?>
                            <a href="<?= url("/issue/{$activity['issue']['key']}") ?>" 
                               class="text-decoration-none fw-600 ms-1">
                                <?= e($activity['issue']['key']) ?>
                            </a>
                            <span class="text-muted ms-2" style="font-size: 0.9rem;">
                                - <?= e(substr($activity['issue']['summary'], 0, 50)) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <small class="text-muted">
                            <i class="bi bi-clock"></i> <?= time_ago($activity['created_at']) ?>
                        </small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination would go here if needed -->
            <?php endif; ?>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
