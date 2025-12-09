<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active">Sprints</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Sprints</h1>
            <p class="text-muted mb-0"><?= e($project['name']) ?></p>
        </div>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Sprints List -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($sprints)): ?>
            <div class="p-5 text-center text-muted">
                <i class="bi bi-lightning fs-1 d-block mb-3"></i>
                <p>No sprints created yet.</p>
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($sprints as $sprint): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <?= e($sprint['name']) ?>
                                <span class="badge bg-info float-end">
                                    <?= ucfirst(str_replace('_', ' ', $sprint['status'])) ?>
                                </span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-6">Status</dt>
                                <dd class="col-6"><?= ucfirst(str_replace('_', ' ', $sprint['status'])) ?></dd>
                                
                                <?php if ($sprint['start_date']): ?>
                                <dt class="col-6">Start Date</dt>
                                <dd class="col-6"><?= date('M j, Y', strtotime($sprint['start_date'])) ?></dd>
                                <?php endif; ?>
                                
                                <?php if ($sprint['end_date']): ?>
                                <dt class="col-6">End Date</dt>
                                <dd class="col-6"><?= date('M j, Y', strtotime($sprint['end_date'])) ?></dd>
                                <?php endif; ?>
                                
                                <?php if ($sprint['goal']): ?>
                                <dt class="col-6">Goal</dt>
                                <dd class="col-6"><?= e($sprint['goal']) ?></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
