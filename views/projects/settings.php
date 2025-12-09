<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Project Settings</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#details" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="bi bi-info-circle me-2"></i> Details
                    </a>
                    <a href="#access" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-shield-lock me-2"></i> Access
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-bell me-2"></i> Notifications
                    </a>
                    <a href="#issue-types" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-list-task me-2"></i> Issue Types
                    </a>
                    <a href="#workflows" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-diagram-3 me-2"></i> Workflows
                    </a>
                    <a href="#fields" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-input-cursor-text me-2"></i> Fields
                    </a>
                    <a href="#integrations" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-plug me-2"></i> Integrations
                    </a>
                    <?php if (can('delete-project', $project['id'])): ?>
                    <a href="#danger" class="list-group-item list-group-item-action text-danger" data-bs-toggle="list">
                        <i class="bi bi-exclamation-triangle me-2"></i> Danger Zone
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Details Tab -->
                <div class="tab-pane fade show active" id="details">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Project Details</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= url("/projects/{$project['key']}/settings") ?>" method="POST" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="PUT">

                                <div class="row mb-4">
                                    <div class="col-md-3 text-center">
                                        <?php if ($project['avatar'] ?? null): ?>
                                        <img src="<?= e($project['avatar']) ?>" class="rounded mb-2" width="96" height="96" alt="">
                                        <?php else: ?>
                                        <div class="rounded mb-2 mx-auto d-flex align-items-center justify-content-center bg-primary text-white" 
                                             style="width: 96px; height: 96px; font-size: 2rem;">
                                            <?= strtoupper(substr($project['key'], 0, 2)) ?>
                                        </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control form-control-sm" name="avatar" accept="image/*">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Project Name</label>
                                            <input type="text" class="form-control <?= has_error('name') ? 'is-invalid' : '' ?>" 
                                                   id="name" name="name" value="<?= e($project['name']) ?>" required>
                                            <?php if (has_error('name')): ?>
                                            <div class="invalid-feedback"><?= e(error('name')) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-3">
                                            <label for="key" class="form-label">Project Key</label>
                                            <input type="text" class="form-control" id="key" value="<?= e($project['key']) ?>" disabled>
                                            <div class="form-text">Project key cannot be changed</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?= e($project['description']) ?></textarea>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select" id="category_id" name="category_id">
                                            <option value="">None</option>
                                            <?php foreach ($categories ?? [] as $category): ?>
                                            <option value="<?= e($category['id']) ?>" <?= $project['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                                <?= e($category['name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lead_id" class="form-label">Project Lead</label>
                                        <select class="form-select" id="lead_id" name="lead_id">
                                            <option value="">Unassigned</option>
                                            <?php foreach ($users ?? [] as $u): ?>
                                            <option value="<?= e($u['id']) ?>" <?= $project['lead_id'] == $u['id'] ? 'selected' : '' ?>>
                                                <?= e($u['display_name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="url" class="form-label">Project URL</label>
                                    <input type="url" class="form-control" id="url" name="url" value="<?= e($project['url'] ?? '') ?>"
                                           placeholder="https://example.com/project">
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Access Tab -->
                <div class="tab-pane fade" id="access">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Access Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= url("/projects/{$project['key']}/settings/access") ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="PUT">

                                <div class="mb-4">
                                    <h6>Project Visibility</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="is_private" id="public" value="0"
                                               <?= !$project['is_private'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="public">
                                            <i class="bi bi-globe me-1"></i> Public
                                            <div class="text-muted small">Anyone in the organization can view this project</div>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_private" id="private" value="1"
                                               <?= $project['is_private'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="private">
                                            <i class="bi bi-lock me-1"></i> Private
                                            <div class="text-muted small">Only project members can view this project</div>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>Default Assignee</h6>
                                    <select class="form-select" name="default_assignee">
                                        <option value="unassigned" <?= ($project['default_assignee'] ?? '') === 'unassigned' ? 'selected' : '' ?>>Unassigned</option>
                                        <option value="project_lead" <?= ($project['default_assignee'] ?? '') === 'project_lead' ? 'selected' : '' ?>>Project Lead</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="notifications">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Notification Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= url("/projects/{$project['key']}/settings/notifications") ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="PUT">

                                <div class="mb-4">
                                    <h6>Email Notifications</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notify_assignee" id="notify_assignee" value="1"
                                               <?= $project['settings']['notify_assignee'] ?? true ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="notify_assignee">Notify assignee on issue assignment</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notify_reporter" id="notify_reporter" value="1"
                                               <?= $project['settings']['notify_reporter'] ?? true ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="notify_reporter">Notify reporter on issue updates</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notify_watchers" id="notify_watchers" value="1"
                                               <?= $project['settings']['notify_watchers'] ?? true ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="notify_watchers">Notify watchers on issue updates</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Issue Types Tab -->
                <div class="tab-pane fade" id="issue-types">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Issue Types</h5>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addIssueTypeModal">
                                <i class="bi bi-plus-lg me-1"></i> Add Issue Type
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php foreach ($issueTypes ?? [] as $type): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-<?= e($type['icon']) ?> text-<?= e($type['color']) ?> me-2 fs-5"></i>
                                        <div>
                                            <strong><?= e($type['name']) ?></strong>
                                            <div class="text-muted small"><?= e($type['description'] ?? '') ?></div>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Workflows Tab -->
                <div class="tab-pane fade" id="workflows">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Workflows</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Configure issue workflows and status transitions.</p>
                            <a href="<?= url("/projects/{$project['key']}/workflows") ?>" class="btn btn-outline-primary">
                                Manage Workflows
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Fields Tab -->
                <div class="tab-pane fade" id="fields">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Custom Fields</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Add custom fields to capture additional information on issues.</p>
                            <a href="<?= url("/projects/{$project['key']}/fields") ?>" class="btn btn-outline-primary">
                                Manage Fields
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Integrations Tab -->
                <div class="tab-pane fade" id="integrations">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Integrations</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="bi bi-github fs-1 me-3"></i>
                                            <div>
                                                <h6 class="mb-1">GitHub</h6>
                                                <p class="text-muted small mb-0">Link repositories and track commits</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="bi bi-slack fs-1 me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Slack</h6>
                                                <p class="text-muted small mb-0">Send notifications to channels</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone Tab -->
                <?php if (can('delete-project', $project['id'])): ?>
                <div class="tab-pane fade" id="danger">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">Danger Zone</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6>Archive Project</h6>
                                <p class="text-muted small">Archived projects are read-only and hidden from the main project list.</p>
                                <?php if ($project['is_archived'] ?? false): ?>
                                <form action="<?= url("/projects/{$project['key']}/unarchive") ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-success">
                                        <i class="bi bi-archive me-1"></i> Unarchive Project
                                    </button>
                                </form>
                                <?php else: ?>
                                <form action="<?= url("/projects/{$project['key']}/archive") ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-warning" 
                                            onclick="return confirm('Are you sure you want to archive this project?')">
                                        <i class="bi bi-archive me-1"></i> Archive Project
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <div>
                                <h6>Delete Project</h6>
                                <p class="text-muted small">Once deleted, all project data including issues, comments, and attachments will be permanently removed.</p>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProjectModal">
                                    <i class="bi bi-trash me-1"></i> Delete Project
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Project Modal -->
<div class="modal fade" id="deleteProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone. All data will be permanently deleted.
                </div>
                <p>To confirm, type <strong><?= e($project['key']) ?></strong> below:</p>
                <form action="<?= url("/projects/{$project['key']}") ?>" method="POST" id="deleteProjectForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="text" class="form-control" id="confirmProjectKey" placeholder="Project key" required>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteProjectForm" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    Delete Project
                </button>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
document.getElementById('confirmProjectKey')?.addEventListener('input', function() {
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = this.value !== '<?= e($project['key']) ?>';
});
</script>
<?php \App\Core\View::endSection(); ?>
