<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active">Issues</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Issues</h1>
            <p class="text-muted mb-0">Project: <?= e($project['name']) ?> (<?= e($project['key']) ?>)</p>
        </div>
        <?php if (can('issues.create', $project['id'])): ?>
        <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Issue
        </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url("/projects/{$project['key']}/issues") ?>" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               value="<?= e($filters['search'] ?? '') ?>" placeholder="Search issues...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="issue_type_id">
                        <option value="">All Types</option>
                        <?php foreach ($issueTypes ?? [] as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= ($filters['issue_type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
                            <?= e($type['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status_id">
                        <option value="">All Statuses</option>
                        <?php foreach ($statuses ?? [] as $status): ?>
                        <option value="<?= $status['id'] ?>" <?= ($filters['status_id'] ?? '') == $status['id'] ? 'selected' : '' ?>>
                            <?= e($status['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="priority_id">
                        <option value="">All Priorities</option>
                        <?php foreach ($priorities ?? [] as $priority): ?>
                        <option value="<?= $priority['id'] ?>" <?= ($filters['priority_id'] ?? '') == $priority['id'] ? 'selected' : '' ?>>
                            <?= e($priority['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="assignee_id">
                        <option value="">All Assignees</option>
                        <option value="unassigned" <?= ($filters['assignee_id'] ?? '') === 'unassigned' ? 'selected' : '' ?>>Unassigned</option>
                        <?php foreach ($projectMembers ?? [] as $member): ?>
                        <option value="<?= $member['id'] ?>" <?= ($filters['assignee_id'] ?? '') == $member['id'] ? 'selected' : '' ?>>
                            <?= e($member['display_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Issues Table -->
    <?php if (empty($issues['data'])): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
            <h5>No issues found</h5>
            <p class="text-muted mb-4">No issues match your current filters.</p>
            <?php if (can('issues.create', $project['id'])): ?>
            <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Create First Issue
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Key</th>
                            <th>Summary</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Assignee</th>
                            <th>Reporter</th>
                            <th>Created</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($issues['data'] as $issue): ?>
                        <tr class="cursor-pointer" onclick="window.location.href='<?= url("/issue/{$issue['issue_key']}") ?>'">
                            <td>
                                <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="text-decoration-none fw-medium">
                                    <?= e($issue['issue_key']) ?>
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge me-2" style="background-color: <?= e($issue['issue_type_color']) ?>">
                                        <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                                    </span>
                                    <span class="text-truncate" style="max-width: 300px;" title="<?= e($issue['summary']) ?>">
                                        <?= e($issue['summary']) ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background-color: <?= e($issue['issue_type_color']) ?>">
                                    <?= e($issue['issue_type_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background-color: <?= e($issue['status_color']) ?>">
                                    <?= e($issue['status_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background-color: <?= e($issue['priority_color']) ?>">
                                    <?= e($issue['priority_name']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($issue['assignee_name'] ?? null): ?>
                                <div class="d-flex align-items-center">
                                    <?php if ($issue['assignee_avatar'] ?? null): ?>
                                    <img src="<?= e($issue['assignee_avatar']) ?>" class="rounded-circle me-2" width="24" height="24">
                                    <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                         style="width: 24px; height: 24px; font-size: 10px;">
                                        <?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                                    </div>
                                    <?php endif; ?>
                                    <span class="small"><?= e($issue['assignee_name']) ?></span>
                                </div>
                                <?php else: ?>
                                <span class="text-muted small">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($issue['reporter_name'] ?? null): ?>
                                <div class="d-flex align-items-center">
                                    <?php if ($issue['reporter_avatar'] ?? null): ?>
                                    <img src="<?= e($issue['reporter_avatar']) ?>" class="rounded-circle me-2" width="24" height="24">
                                    <?php else: ?>
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                                         style="width: 24px; height: 24px; font-size: 10px;">
                                        <?= strtoupper(substr($issue['reporter_name'], 0, 1)) ?>
                                    </div>
                                    <?php endif; ?>
                                    <span class="small"><?= e($issue['reporter_name']) ?></span>
                                </div>
                                <?php else: ?>
                                <span class="text-muted small">Unknown</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?= time_ago($issue['created_at']) ?>
                            </td>
                            <td class="small text-muted">
                                <?= time_ago($issue['updated_at']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($issues['total_pages'] > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $issues['current_page'] <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= url("/projects/{$project['key']}/issues?" . http_build_query(array_merge($filters ?? [], ['page' => $issues['current_page'] - 1]))) ?>">
                    Previous
                </a>
            </li>
            <?php for ($i = 1; $i <= $issues['total_pages']; $i++): ?>
            <li class="page-item <?= $issues['current_page'] == $i ? 'active' : '' ?>">
                <a class="page-link" href="<?= url("/projects/{$project['key']}/issues?" . http_build_query(array_merge($filters ?? [], ['page' => $i]))) ?>">
                    <?= $i ?>
                </a>
            </li>
            <?php endfor; ?>
            <li class="page-item <?= $issues['current_page'] >= $issues['total_pages'] ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= url("/projects/{$project['key']}/issues?" . http_build_query(array_merge($filters ?? [], ['page' => $issues['current_page'] + 1]))) ?>">
                    Next
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php \App\Core\View::endSection(); ?>