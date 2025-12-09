<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active">Backlog</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Backlog</h1>
            <p class="text-muted mb-0"><?= e($project['name']) ?></p>
        </div>
        <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Issue
        </a>
    </div>

    <!-- Backlog Issues -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Backlog Items (<?= count($backlogIssues) ?>)</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($backlogIssues)): ?>
            <div class="p-5 text-center text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p>No backlog items yet.</p>
                <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-sm btn-primary">
                    Create First Issue
                </a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Key</th>
                            <th>Summary</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Assignee</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backlogIssues as $issue): ?>
                        <tr>
                            <td>
                                <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="text-decoration-none fw-medium">
                                    <?= e($issue['issue_key']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="text-decoration-none text-dark">
                                    <?= e(substr($issue['summary'], 0, 60)) ?><?= strlen($issue['summary']) > 60 ? '...' : '' ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge" style="background-color: <?= e($issue['issue_type_color']) ?>">
                                    <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                                    <?= e($issue['issue_type_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background-color: <?= e($issue['status_color']) ?>">
                                    <?= e($issue['status_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background-color: <?= e($issue['priority_color']) ?>">
                                    <?= e($issue['priority_name']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($issue['assignee_name']): ?>
                                <div class="d-flex align-items-center">
                                    <img src="<?= e($issue['assignee_avatar'] ?? '/images/default-avatar.png') ?>" 
                                         class="rounded-circle" width="24" height="24" title="<?= e($issue['assignee_name']) ?>">
                                    <span class="ms-2 small"><?= e($issue['assignee_name']) ?></span>
                                </div>
                                <?php else: ?>
                                <span class="text-muted small">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
