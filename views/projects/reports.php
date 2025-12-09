<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Project Reports</h1>
            <p class="text-muted mb-0"><?= e($project['name']) ?></p>
        </div>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0"><?= $stats['total_issues'] ?></h2>
                    <small class="text-muted">Total Issues</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 text-success"><?= $stats['resolved_issues'] ?></h2>
                    <small class="text-muted">Resolved</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 text-warning"><?= $stats['open_issues'] ?></h2>
                    <small class="text-muted">Open</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolution Rate -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Resolution Rate</h5>
        </div>
        <div class="card-body">
            <?php 
            $resolutionRate = $stats['total_issues'] > 0 
                ? ($stats['resolved_issues'] / $stats['total_issues']) * 100 
                : 0;
            ?>
            <div class="progress" style="height: 30px;">
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: <?= $resolutionRate ?>%;" 
                     aria-valuenow="<?= $resolutionRate ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <?= round($resolutionRate, 1) ?>%
                </div>
            </div>
            <p class="mt-3 text-muted">
                <?= $stats['resolved_issues'] ?> of <?= $stats['total_issues'] ?> issues resolved
            </p>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
