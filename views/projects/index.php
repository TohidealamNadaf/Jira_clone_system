<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active">Projects</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Projects</h1>
        <?php if (can('create-projects')): ?>
        <a href="<?= url('/projects/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Project
        </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('/projects') ?>" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" 
                               value="<?= e($filters['search'] ?? '') ?>" placeholder="Search projects...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories ?? [] as $category): ?>
                        <option value="<?= e($category['id']) ?>" <?= ($filters['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                            <?= e($category['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="archived" <?= ($filters['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Projects Grid -->
    <?php if (empty($projects['items'] ?? [])): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        No projects found. <?php if (can('create-projects')): ?>
        <a href="<?= url('/projects/create') ?>">Create your first project</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($projects['items'] as $project): ?>
        <div class="col">
            <div class="card h-100 project-card">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <?php if ($project['avatar'] ?? null): ?>
                        <img src="<?= e($project['avatar']) ?>" class="rounded me-3" width="48" height="48" alt="">
                        <?php else: ?>
                        <div class="rounded me-3 d-flex align-items-center justify-content-center bg-primary text-white" 
                             style="width: 48px; height: 48px; font-size: 1.25rem;">
                            <?= strtoupper(substr($project['key'], 0, 2)) ?>
                        </div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">
                                <a href="<?= url("/projects/{$project['key']}") ?>" class="text-decoration-none">
                                    <?= e($project['name']) ?>
                                </a>
                            </h5>
                            <span class="badge bg-secondary"><?= e($project['key']) ?></span>
                            <?php if ($project['is_archived'] ?? false): ?>
                            <span class="badge bg-warning">Archived</span>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= url("/projects/{$project['key']}") ?>">
                                    <i class="bi bi-eye me-2"></i> View
                                </a></li>
                                <li><a class="dropdown-item" href="<?= url("/projects/{$project['key']}/board") ?>">
                                    <i class="bi bi-kanban me-2"></i> Board
                                </a></li>
                                <?php if (can('edit-project', $project['id'])): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= url("/projects/{$project['key']}/settings") ?>">
                                    <i class="bi bi-gear me-2"></i> Settings
                                </a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <?php if ($project['description'] ?? null): ?>
                    <p class="card-text text-muted small mb-3">
                        <?= e(substr($project['description'], 0, 100)) ?><?= strlen($project['description']) > 100 ? '...' : '' ?>
                    </p>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between text-muted small">
                        <span><i class="bi bi-list-task me-1"></i> <?= e($project['issue_count'] ?? 0) ?> issues</span>
                        <span><i class="bi bi-people me-1"></i> <?= e($project['member_count'] ?? 0) ?> members</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="text-muted small me-2">Lead:</span>
                            <?php if ($project['lead'] ?? null): ?>
                            <img src="<?= e($project['lead']['avatar'] ?? '/images/default-avatar.png') ?>" 
                                 class="rounded-circle" width="24" height="24" 
                                 title="<?= e($project['lead']['display_name']) ?>"
                                 data-bs-toggle="tooltip">
                            <?php else: ?>
                            <span class="text-muted small">Unassigned</span>
                            <?php endif; ?>
                        </div>
                        <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus"></i> New Issue
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($projects['last_page']) && $projects['last_page'] > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $projects['current_page'] <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= url('/projects?' . http_build_query(array_merge($filters ?? [], ['page' => $projects['current_page'] - 1]))) ?>">
                    Previous
                </a>
            </li>
            <?php for ($i = 1; $i <= $projects['last_page']; $i++): ?>
            <li class="page-item <?= $projects['current_page'] == $i ? 'active' : '' ?>">
                <a class="page-link" href="<?= url('/projects?' . http_build_query(array_merge($filters ?? [], ['page' => $i]))) ?>">
                    <?= $i ?>
                </a>
            </li>
            <?php endfor; ?>
            <li class="page-item <?= $projects['current_page'] >= $projects['last_page'] ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= url('/projects?' . http_build_query(array_merge($filters ?? [], ['page' => $projects['current_page'] + 1]))) ?>">
                    Next
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php \App\Core\View::endSection(); ?>
