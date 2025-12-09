<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}/boards") ?>">Boards</a></li>
            <li class="breadcrumb-item active">Create Board</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create New Board</h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('/boards') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <!-- Project (hidden) -->
                            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

                            <!-- Board Name -->
                            <div class="col-12">
                                <label class="form-label">Board Name</label>
                                <input type="text" class="form-control" name="name"
                                       value="<?= e(old('name')) ?>" required maxlength="100" autofocus>
                            </div>

                            <!-- Board Type -->
                            <div class="col-md-6">
                                <label class="form-label">Board Type</label>
                                <select class="form-select" name="type" required>
                                    <option value="scrum" <?= old('type') === 'scrum' ? 'selected' : '' ?>>Scrum Board</option>
                                    <option value="kanban" <?= old('type') === 'kanban' ? 'selected' : '' ?>>Kanban Board</option>
                                </select>
                                <div class="form-text">
                                    Scrum boards are for agile development with sprints. Kanban boards are for continuous flow.
                                </div>
                            </div>

                            <!-- Private Board -->
                            <div class="col-md-6">
                                <label class="form-label">Visibility</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_private" value="1"
                                           id="is_private" <?= old('is_private') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_private">
                                        Private Board
                                    </label>
                                </div>
                                <div class="form-text">
                                    Private boards are only visible to project members.
                                </div>
                            </div>

                            <!-- JQL Filter -->
                            <div class="col-12">
                                <label class="form-label">JQL Filter (Optional)</label>
                                <textarea class="form-control" name="filter_jql" rows="3"
                                          maxlength="2000" placeholder="project = <?= e($project['key']) ?> AND ..."><?= e(old('filter_jql')) ?></textarea>
                                <div class="form-text">
                                    Advanced filter using JQL (Jira Query Language). Leave empty to show all project issues.
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label">Description (Optional)</label>
                                <textarea class="form-control" name="description" rows="3"
                                          maxlength="500"><?= e(old('description')) ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= url("/projects/{$project['key']}/boards") ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Create Board
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Board Types</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-lightning text-primary me-2"></i>Scrum Board</h6>
                            <ul class="small text-muted">
                                <li>Organized around sprints</li>
                                <li>Shows sprint progress and velocity</li>
                                <li>Ideal for teams using agile methodology</li>
                                <li>Includes backlog management</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-kanban text-success me-2"></i>Kanban Board</h6>
                            <ul class="small text-muted">
                                <li>Continuous workflow visualization</li>
                                <li>No time-boxed iterations</li>
                                <li>Focus on work-in-progress limits</li>
                                <li>Suitable for support and maintenance teams</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>