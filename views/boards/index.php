<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('/projects/' . $project['key']) ?>"><?= e($project['key']) ?></a></li>
                    <li class="breadcrumb-item active">Boards</li>
                </ol>
            </nav>
            <h2 class="mb-0">Boards</h2>
        </div>
        <?php if (can('manage-boards', $project['id'])): ?>
        <a href="<?= url('/projects/' . $project['key'] . '/boards/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Board
        </a>
        <?php endif; ?>
    </div>

    <?php if (empty($boards)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-kanban fs-1 text-muted d-block mb-3"></i>
            <h5>No boards yet</h5>
            <p class="text-muted mb-4">Create your first board to visualize your project's workflow.</p>
            <?php if (can('manage-boards', $project['id'])): ?>
            <a href="<?= url('/projects/' . $project['key'] . '/boards/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Create Board
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($boards as $board): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="bg-<?= $board['type'] === 'scrum' ? 'primary' : 'success' ?> bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-<?= $board['type'] === 'scrum' ? 'lightning' : 'kanban' ?> text-<?= $board['type'] === 'scrum' ? 'primary' : 'success' ?> fs-4"></i>
                        </div>
                        <?php if (can('manage-boards', $project['id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= url('/projects/' . $project['key'] . '/boards/' . $board['id'] . '/settings') ?>">
                                    <i class="bi bi-gear me-2"></i> Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="<?= url('/projects/' . $project['key'] . '/boards/' . $board['id']) ?>" method="POST" 
                                          onsubmit="return confirm('Delete this board?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="card-title mb-1"><?= e($board['name']) ?></h5>
                    <p class="text-muted small mb-3">
                        <span class="badge bg-secondary"><?= ucfirst($board['type']) ?></span>
                        <?= e($board['description'] ?? '') ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                        <span><i class="bi bi-columns-gap me-1"></i> <?= count($board['columns'] ?? []) ?> columns</span>
                        <span><i class="bi bi-list-task me-1"></i> <?= $board['issue_count'] ?? 0 ?> issues</span>
                    </div>
                    <a href="<?= url('/projects/' . $project['key'] . '/boards/' . $board['id']) ?>" class="btn btn-outline-primary w-100">
                        Open Board
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php \App\Core\View::endSection(); ?>
