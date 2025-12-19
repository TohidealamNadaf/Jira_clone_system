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
            <li class="breadcrumb-item"><a href="<?= url("/boards/{$board['id']}") ?>"><?= e($board['name']) ?></a></li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Board Settings</h5>
                </div>
                <div class="card-body">
                    <form action="<?= url("/boards/{$board['id']}") ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <div class="row g-3">
                            <!-- Board Name -->
                            <div class="col-12">
                                <label class="form-label">Board Name</label>
                                <input type="text" class="form-control" name="name"
                                       value="<?= e(old('name', $board['name'])) ?>" required maxlength="100">
                            </div>

                            <!-- JQL Filter -->
                            <div class="col-12">
                                <label class="form-label">JQL Filter</label>
                                <textarea class="form-control" name="filter_jql" rows="3"
                                          maxlength="2000"><?= e(old('filter_jql', $board['filter_jql'])) ?></textarea>
                                <div class="form-text">
                                    Advanced filter using JQL (Jira Query Language). Leave empty to show all project issues.
                                </div>
                            </div>

                            <!-- Private Board -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_private" value="1"
                                           id="is_private" <?= (old('is_private', $board['is_private'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_private">
                                        Private Board
                                    </label>
                                </div>
                                <div class="form-text">
                                    Private boards are only visible to project members.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= url("/boards/{$board['id']}") ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back to Board
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Update Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card mt-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</h6>
                </div>
                <div class="card-body">
                    <h6>Delete Board</h6>
                    <p class="text-muted mb-3">
                        Once you delete this board, there is no going back. This will permanently delete the board
                        and all associated data. Issues will remain in the project but will no longer be visible on this board.
                    </p>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-1"></i> Delete Board
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Board Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Type:</strong>
                        <span class="badge bg-secondary ms-1">
                            <?= ucfirst($board['type']) ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Project:</strong>
                        <a href="<?= url("/projects/{$project['key']}") ?>" class="ms-1">
                            <?= e($project['name']) ?> (<?= e($project['key']) ?>)
                        </a>
                    </div>
                    <div class="mb-3">
                        <strong>Visibility:</strong>
                        <span class="ms-1">
                            <?= $board['is_private'] ? 'Private' : 'Public' ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Created:</strong>
                        <span class="ms-1"><?= format_date($board['created_at']) ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Owner:</strong>
                        <span class="ms-1">
                            <?php if ($board['owner']): ?>
                                <?= e($board['owner']['display_name']) ?>
                            <?php else: ?>
                                System
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Board</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this board? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Deleting this board will:
                    <ul class="mb-0 mt-2">
                        <li>Remove all board configurations</li>
                        <li>Remove sprint associations (sprints will remain)</li>
                        <li>Issues will no longer be visible on this board</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="<?= url("/boards/{$board['id']}") ?>" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete Board
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>