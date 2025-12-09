<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <?php if ($project): ?>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active">Create Issue</li>
            <?php else: ?>
            <li class="breadcrumb-item active">Create Issue</li>
            <?php endif; ?>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create New Issue</h5>
                </div>
                <div class="card-body">
                    <form action="<?= url($project ? "/projects/{$project['key']}/issues" : '/issues') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <!-- Project (if not specified) -->
                            <?php if (!$project): ?>
                            <div class="col-12">
                                <label class="form-label">Project</label>
                                <select class="form-select" name="project_id" required id="project-select">
                                    <option value="">Select Project...</option>
                                    <?php foreach ($projects ?? [] as $proj): ?>
                                    <option value="<?= $proj['id'] ?>" data-key="<?= $proj['key'] ?>">
                                        <?= e($proj['name']) ?> (<?= e($proj['key']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php else: ?>
                            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                            <?php endif; ?>

                            <!-- Issue Type -->
                            <div class="col-md-6">
                                <label class="form-label">Issue Type</label>
                                <select class="form-select" name="issue_type_id" required id="issue-type-select">
                                    <option value="">Select Type...</option>
                                    <?php if ($project): ?>
                                    <?php foreach ($project['issue_types'] ?? [] as $type): ?>
                                    <option value="<?= $type['id'] ?>">
                                        <?= e($type['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority_id" id="priority-select">
                                    <option value="">Select Priority...</option>
                                    <?php foreach ($priorities ?? [] as $priority): ?>
                                    <option value="<?= $priority['id'] ?>" <?= $priority['is_default'] ? 'selected' : '' ?>>
                                        <?= e($priority['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Summary -->
                            <div class="col-12">
                                <label class="form-label">Summary</label>
                                <input type="text" class="form-control" name="summary"
                                       value="<?= e(old('summary')) ?>" required maxlength="500" autofocus>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control markdown-editor" name="description" rows="6"
                                          maxlength="50000"><?= e(old('description')) ?></textarea>
                            </div>

                            <!-- Assignee -->
                            <div class="col-md-6">
                                <label class="form-label">Assignee</label>
                                <select class="form-select" name="assignee_id" id="assignee-select">
                                    <option value="">Unassigned</option>
                                    <?php if ($project): ?>
                                    <?php foreach ($project['members'] ?? [] as $member): ?>
                                    <option value="<?= $member['id'] ?>">
                                        <?= e($member['display_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Labels -->
                            <div class="col-md-6">
                                <label class="form-label">Labels</label>
                                <select class="form-select" name="labels[]" multiple id="labels-select">
                                    <?php if ($project): ?>
                                    <?php foreach ($project['labels'] ?? [] as $label): ?>
                                    <option value="<?= $label['id'] ?>">
                                        <?= e($label['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Components -->
                            <div class="col-md-6">
                                <label class="form-label">Components</label>
                                <select class="form-select" name="components[]" multiple id="components-select">
                                    <?php if ($project): ?>
                                    <?php foreach ($project['components'] ?? [] as $component): ?>
                                    <option value="<?= $component['id'] ?>">
                                        <?= e($component['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Fix Versions -->
                            <div class="col-md-6">
                                <label class="form-label">Fix Versions</label>
                                <select class="form-select" name="fix_versions[]" multiple id="versions-select">
                                    <?php if ($project): ?>
                                    <?php foreach ($project['versions'] ?? [] as $version): ?>
                                    <option value="<?= $version['id'] ?>">
                                        <?= e($version['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Story Points -->
                            <div class="col-md-6">
                                <label class="form-label">Story Points</label>
                                <input type="number" class="form-control" name="story_points"
                                       value="<?= e(old('story_points')) ?>" min="0" max="999">
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" name="due_date"
                                       value="<?= e(old('due_date')) ?>">
                            </div>

                            <!-- Original Estimate -->
                            <div class="col-12">
                                <label class="form-label">Original Estimate (minutes)</label>
                                <input type="number" class="form-control" name="original_estimate"
                                       value="<?= e(old('original_estimate')) ?>" min="0">
                            </div>

                            <!-- Attachments -->
                            <div class="col-12">
                                <label class="form-label">Attachments</label>
                                <input type="file" class="form-control" name="attachments[]" multiple
                                       accept="image/*,.pdf,.doc,.docx,.txt,.zip,.rar">
                                <div class="form-text">Supported formats: Images, PDF, Word documents, Text files, Archives</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= url($project ? "/projects/{$project['key']}" : '/projects') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Create Issue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
<?php if (!$project): ?>
// Load project data when project is selected
document.getElementById('project-select').addEventListener('change', async function() {
    const projectId = this.value;
    if (!projectId) return;

    try {
        const response = await api.get(`/projects/${projectId}`);
        const project = response;

        // Update issue types
        const typeSelect = document.getElementById('issue-type-select');
        typeSelect.innerHTML = '<option value="">Select Type...</option>';
        if (project.issue_types) {
            project.issue_types.forEach(type => {
                typeSelect.innerHTML += `<option value="${type.id}">${type.name}</option>`;
            });
        }

        // Update assignees
        const assigneeSelect = document.getElementById('assignee-select');
        assigneeSelect.innerHTML = '<option value="">Unassigned</option>';
        if (project.members) {
            project.members.forEach(member => {
                assigneeSelect.innerHTML += `<option value="${member.id}">${member.display_name}</option>`;
            });
        }

        // Update labels
        const labelsSelect = document.getElementById('labels-select');
        labelsSelect.innerHTML = '';
        if (project.labels) {
            project.labels.forEach(label => {
                labelsSelect.innerHTML += `<option value="${label.id}">${label.name}</option>`;
            });
        }

        // Update components
        const componentsSelect = document.getElementById('components-select');
        componentsSelect.innerHTML = '';
        if (project.components) {
            project.components.forEach(component => {
                componentsSelect.innerHTML += `<option value="${component.id}">${component.name}</option>`;
            });
        }

        // Update versions
        const versionsSelect = document.getElementById('versions-select');
        versionsSelect.innerHTML = '';
        if (project.versions) {
            project.versions.forEach(version => {
                versionsSelect.innerHTML += `<option value="${version.id}">${version.name}</option>`;
            });
        }
    } catch (error) {
        console.error('Failed to load project data:', error);
    }
});
<?php endif; ?>
</script>

<?php \App\Core\View::endSection(); ?>