<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$issue['project_key']}") ?>"><?= e($issue['project_name']) ?></a></li>
            <li class="breadcrumb-item"><a href="<?= url("/issue/{$issue['issue_key']}") ?>"><?= e($issue['issue_key']) ?></a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Issue: <?= e($issue['issue_key']) ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?= url("/issue/{$issue['issue_key']}") ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <div class="row g-3">
                            <!-- Issue Type -->
                            <div class="col-md-6">
                                <label class="form-label">Issue Type</label>
                                <select class="form-select" name="issue_type_id" required>
                                    <option value="">Select Type...</option>
                                    <?php foreach ($issueTypes ?? [] as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= $issue['issue_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                        <?= e($type['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority_id">
                                    <option value="">Select Priority...</option>
                                    <?php foreach ($priorities ?? [] as $priority): ?>
                                    <option value="<?= $priority['id'] ?>" <?= $issue['priority_id'] == $priority['id'] ? 'selected' : '' ?>>
                                        <?= e($priority['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Summary -->
                            <div class="col-12">
                                <label class="form-label">Summary</label>
                                <input type="text" class="form-control" name="summary"
                                       value="<?= e(old('summary', $issue['summary'])) ?>" required maxlength="500">
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control markdown-editor" name="description" rows="6"
                                          maxlength="50000"><?= e(old('description', $issue['description'])) ?></textarea>
                            </div>

                            <!-- Assignee -->
                            <div class="col-md-6">
                                <label class="form-label">Assignee</label>
                                <select class="form-select" name="assignee_id">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($projectMembers ?? [] as $member): ?>
                                    <option value="<?= $member['id'] ?>" <?= $issue['assignee_id'] == $member['id'] ? 'selected' : '' ?>>
                                        <?= e($member['display_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Labels -->
                            <div class="col-md-6">
                                <label class="form-label">Labels</label>
                                <select class="form-select" name="labels[]" multiple>
                                    <?php foreach ($labels ?? [] as $label): ?>
                                    <option value="<?= $label['id'] ?>"
                                            <?= in_array($label['id'], array_column($issue['labels'] ?? [], 'id')) ? 'selected' : '' ?>>
                                        <?= e($label['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Story Points -->
                            <div class="col-md-6">
                                <label class="form-label">Story Points</label>
                                <input type="number" class="form-control" name="story_points"
                                       value="<?= e(old('story_points', $issue['story_points'])) ?>" min="0" max="999">
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" name="due_date"
                                       value="<?= e(old('due_date', $issue['due_date'])) ?>">
                            </div>

                            <!-- Original Estimate -->
                            <div class="col-md-6">
                                <label class="form-label">Original Estimate (minutes)</label>
                                <input type="number" class="form-control" name="original_estimate"
                                       value="<?= e(old('original_estimate', $issue['original_estimate'])) ?>" min="0">
                            </div>

                            <!-- Remaining Estimate -->
                            <div class="col-md-6">
                                <label class="form-label">Remaining Estimate (minutes)</label>
                                <input type="number" class="form-control" name="remaining_estimate"
                                       value="<?= e(old('remaining_estimate', $issue['remaining_estimate'])) ?>" min="0">
                            </div>

                            <!-- Environment -->
                            <div class="col-12">
                                <label class="form-label">Environment</label>
                                <textarea class="form-control" name="environment" rows="3"
                                          maxlength="10000"><?= e(old('environment', $issue['environment'])) ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Update Issue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>