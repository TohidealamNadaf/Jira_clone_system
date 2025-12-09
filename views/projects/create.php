<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item active">Create Project</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-folder-plus me-2"></i>Create New Project</h4>
                </div>
                <div class="card-body">
                    <form action="<?= url('/projects') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= has_error('name') ? 'is-invalid' : '' ?>" 
                                       id="name" name="name" value="<?= e(old('name')) ?>" required
                                       placeholder="Enter project name">
                                <?php if (has_error('name')): ?>
                                <div class="invalid-feedback"><?= e(error('name')) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="key" class="form-label">Project Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= has_error('key') ? 'is-invalid' : '' ?>" 
                                       id="key" name="key" value="<?= e(old('key')) ?>" required
                                       pattern="[A-Z0-9]+" maxlength="10" style="text-transform: uppercase;"
                                       placeholder="e.g., PROJ">
                                <?php if (has_error('key')): ?>
                                <div class="invalid-feedback"><?= e(error('key')) ?></div>
                                <?php else: ?>
                                <div class="form-text">2-10 uppercase letters/numbers</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control <?= has_error('description') ? 'is-invalid' : '' ?>" 
                                      id="description" name="description" rows="3"
                                      placeholder="Describe the project's purpose and goals"><?= e(old('description')) ?></textarea>
                            <?php if (has_error('description')): ?>
                            <div class="invalid-feedback"><?= e(error('description')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select <?= has_error('category_id') ? 'is-invalid' : '' ?>" 
                                        id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories ?? [] as $category): ?>
                                    <option value="<?= e($category['id']) ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                        <?= e($category['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (has_error('category_id')): ?>
                                <div class="invalid-feedback"><?= e(error('category_id')) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="lead_id" class="form-label">Project Lead</label>
                                <select class="form-select <?= has_error('lead_id') ? 'is-invalid' : '' ?>" 
                                        id="lead_id" name="lead_id">
                                    <option value="">Select Lead</option>
                                    <?php foreach ($users ?? [] as $u): ?>
                                    <option value="<?= e($u['id']) ?>" <?= old('lead_id') == $u['id'] ? 'selected' : '' ?>>
                                        <?= e($u['display_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (has_error('lead_id')): ?>
                                <div class="invalid-feedback"><?= e(error('lead_id')) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Project Avatar</label>
                            <input type="file" class="form-control <?= has_error('avatar') ? 'is-invalid' : '' ?>" 
                                   id="avatar" name="avatar" accept="image/*">
                            <?php if (has_error('avatar')): ?>
                            <div class="invalid-feedback"><?= e(error('avatar')) ?></div>
                            <?php else: ?>
                            <div class="form-text">Optional. Recommended size: 48x48 pixels</div>
                            <?php endif; ?>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="project_type" class="form-label">Project Type</label>
                                <select class="form-select <?= has_error('project_type') ? 'is-invalid' : '' ?>" 
                                        id="project_type" name="project_type">
                                    <option value="software" <?= old('project_type') === 'software' ? 'selected' : '' ?>>Software</option>
                                    <option value="business" <?= old('project_type') === 'business' ? 'selected' : '' ?>>Business</option>
                                    <option value="service_desk" <?= old('project_type') === 'service_desk' ? 'selected' : '' ?>>Service Desk</option>
                                </select>
                                <?php if (has_error('project_type')): ?>
                                <div class="invalid-feedback"><?= e(error('project_type')) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="default_assignee" class="form-label">Default Assignee</label>
                                <select class="form-select <?= has_error('default_assignee') ? 'is-invalid' : '' ?>" 
                                        id="default_assignee" name="default_assignee">
                                    <option value="unassigned" <?= old('default_assignee') === 'unassigned' ? 'selected' : '' ?>>Unassigned</option>
                                    <option value="project_lead" <?= old('default_assignee') === 'project_lead' ? 'selected' : '' ?>>Project Lead</option>
                                </select>
                                <?php if (has_error('default_assignee')): ?>
                                <div class="invalid-feedback"><?= e(error('default_assignee')) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Access Settings</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_private" name="is_private" value="1"
                                       <?= old('is_private') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_private">
                                    Private Project
                                </label>
                                <div class="form-text">Only project members can view and access this project</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Create Project
                            </button>
                            <a href="<?= url('/projects') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
document.getElementById('name').addEventListener('input', function() {
    const keyField = document.getElementById('key');
    if (!keyField.dataset.userEdited) {
        const key = this.value
            .toUpperCase()
            .replace(/[^A-Z0-9]/g, '')
            .substring(0, 10);
        keyField.value = key;
    }
});

document.getElementById('key').addEventListener('input', function() {
    this.dataset.userEdited = 'true';
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});
</script>
<?php \App\Core\View::endSection(); ?>
