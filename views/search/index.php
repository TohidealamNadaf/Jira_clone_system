<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Search Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('/search') ?>" method="GET" id="searchForm">
                        <input type="hidden" name="q" value="<?= e($query ?? '') ?>">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Project</label>
                            <select name="project" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Projects</option>
                                <?php foreach ($projects ?? [] as $proj): ?>
                                <option value="<?= $proj['key'] ?>" <?= ($filters['project'] ?? '') === $proj['key'] ? 'selected' : '' ?>>
                                    <?= e($proj['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Type</label>
                            <?php foreach ($issueTypes ?? [] as $type): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="type[]" 
                                       value="<?= $type['id'] ?>" id="type-<?= $type['id'] ?>"
                                       <?= in_array($type['id'], $filters['type'] ?? []) ? 'checked' : '' ?>
                                       onchange="this.form.submit()">
                                <label class="form-check-label small" for="type-<?= $type['id'] ?>">
                                    <i class="bi bi-<?= e($type['icon']) ?>" style="color: <?= e($type['color']) ?>"></i>
                                    <?= e($type['name']) ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Status</label>
                            <?php foreach ($statuses ?? [] as $status): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status[]" 
                                       value="<?= $status['id'] ?>" id="status-<?= $status['id'] ?>"
                                       <?= in_array($status['id'], $filters['status'] ?? []) ? 'checked' : '' ?>
                                       onchange="this.form.submit()">
                                <label class="form-check-label small" for="status-<?= $status['id'] ?>">
                                    <span class="badge" style="background-color: <?= e($status['color']) ?>"><?= e($status['name']) ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Priority</label>
                            <?php foreach ($priorities ?? [] as $priority): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="priority[]" 
                                       value="<?= $priority['id'] ?>" id="priority-<?= $priority['id'] ?>"
                                       <?= in_array($priority['id'], $filters['priority'] ?? []) ? 'checked' : '' ?>
                                       onchange="this.form.submit()">
                                <label class="form-check-label small" for="priority-<?= $priority['id'] ?>">
                                    <span class="badge" style="background-color: <?= e($priority['color']) ?>"><?= e($priority['name']) ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Assignee</label>
                            <select name="assignee" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Anyone</option>
                                <option value="currentUser()" <?= ($filters['assignee'] ?? '') === 'currentUser()' ? 'selected' : '' ?>>
                                    Current User
                                </option>
                                <option value="unassigned" <?= ($filters['assignee'] ?? '') === 'unassigned' ? 'selected' : '' ?>>
                                    Unassigned
                                </option>
                                <?php foreach ($users ?? [] as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= ($filters['assignee'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                    <?= e($u['display_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Reporter</label>
                            <select name="reporter" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Anyone</option>
                                <option value="currentUser()" <?= ($filters['reporter'] ?? '') === 'currentUser()' ? 'selected' : '' ?>>
                                    Current User
                                </option>
                                <?php foreach ($users ?? [] as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= ($filters['reporter'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                    <?= e($u['display_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Created</label>
                            <select name="created" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Any time</option>
                                <option value="today" <?= ($filters['created'] ?? '') === 'today' ? 'selected' : '' ?>>Today</option>
                                <option value="week" <?= ($filters['created'] ?? '') === 'week' ? 'selected' : '' ?>>This week</option>
                                <option value="month" <?= ($filters['created'] ?? '') === 'month' ? 'selected' : '' ?>>This month</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="<?= url('/search') ?>" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                            <a href="<?= url('/search/advanced') ?>" class="btn btn-sm btn-outline-primary">Advanced Search</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Saved Filters -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Saved Filters</h6>
                    <button class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#saveFilterModal">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($savedFilters ?? [] as $filter): ?>
                    <a href="<?= url('/search?' . $filter['query']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span><?= e($filter['name']) ?></span>
                        <span class="badge bg-secondary"><?= $filter['result_count'] ?? 0 ?></span>
                    </a>
                    <?php endforeach; ?>
                    <?php if (empty($savedFilters)): ?>
                    <div class="list-group-item text-muted small">No saved filters</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <?php if ($query): ?>
                            Search results for "<?= e($query) ?>"
                            <?php else: ?>
                            All Issues
                            <?php endif; ?>
                        </h5>
                        <small class="text-muted"><?= $totalResults ?? 0 ?> results found</small>
                    </div>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" style="min-width: 160px;" id="sortSelect" onchange="changeSortOrder(this.value)">
                             <option value="updated_desc" <?= ($sort === 'updated_desc') ? 'selected' : '' ?>>Recently Updated</option>
                             <option value="created_desc" <?= ($sort === 'created_desc') ? 'selected' : '' ?>>Newest First</option>
                             <option value="created_asc" <?= ($sort === 'created_asc') ? 'selected' : '' ?>>Oldest First</option>
                             <option value="priority_desc" <?= ($sort === 'priority_desc') ? 'selected' : '' ?>>Priority</option>
                         </select>
                        <div class="btn-group" role="group">
                            <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'list'])) ?>" class="btn btn-sm btn-outline-secondary <?= ($view === 'list' || empty($view)) ? 'active' : '' ?>" title="List View">
                                 <i class="bi bi-list"></i>
                             </a>
                             <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'detail'])) ?>" class="btn btn-sm btn-outline-secondary <?= ($view === 'detail') ? 'active' : '' ?>" title="Card View">
                                 <i class="bi bi-grid-3x2-gap"></i>
                             </a>
                         </div>
                    </div>
                </div>
                
                <?php if (empty($issues)): ?>
                <div class="card-body text-center py-5">
                    <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                    <h5>No issues found</h5>
                    <p class="text-muted">Try adjusting your search or filters</p>
                </div>
                <?php elseif ($view === 'detail'): ?>
                <!-- Detail/Card View -->
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($issues as $issue): ?>
                        <div class="col-12">
                            <div class="card border hover-lift">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <span class="d-inline-flex align-items-center justify-content-center rounded" 
                                                  style="width: 40px; height: 40px; background-color: <?= e($issue['issue_type_color']) ?>20;">
                                                <i class="bi bi-<?= e($issue['issue_type_icon']) ?>" style="color: <?= e($issue['issue_type_color']) ?>; font-size: 1.25rem;"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="text-primary fw-bold text-decoration-none me-2">
                                                        <?= e($issue['issue_key']) ?>
                                                    </a>
                                                    <span class="badge" style="background-color: <?= e($issue['priority_color']) ?>">
                                                        <?= e($issue['priority_name']) ?>
                                                    </span>
                                                </div>
                                                <span class="badge" style="background-color: <?= e($issue['status_color']) ?>">
                                                    <?= e($issue['status_name']) ?>
                                                </span>
                                            </div>
                                            <h6 class="mb-2">
                                                <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="text-dark text-decoration-none">
                                                    <?= e($issue['summary']) ?>
                                                </a>
                                            </h6>
                                            <?php if (!empty($issue['description'])): ?>
                                            <p class="text-muted small mb-2 text-truncate-2">
                                                <?= e(mb_substr(strip_tags($issue['description']), 0, 200)) ?>...
                                            </p>
                                            <?php endif; ?>
                                            <div class="d-flex flex-wrap gap-3 small text-muted">
                                                <span>
                                                    <i class="bi bi-folder me-1"></i>
                                                    <?= e($issue['project_name'] ?? $issue['project_key']) ?>
                                                </span>
                                                <span>
                                                    <i class="bi bi-person me-1"></i>
                                                    <?= $issue['assignee_name'] ? e($issue['assignee_name']) : 'Unassigned' ?>
                                                </span>
                                                <span>
                                                    <i class="bi bi-clock me-1"></i>
                                                    Updated <?= time_ago($issue['updated_at']) ?>
                                                </span>
                                                <?php if (!empty($issue['due_date'])): ?>
                                                <span class="<?= strtotime($issue['due_date']) < time() ? 'text-danger' : '' ?>">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    Due <?= format_date($issue['due_date']) ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <!-- List/Table View -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Key</th>
                                <th>Summary</th>
                                <th>Assignee</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($issues as $issue): ?>
                            <tr>
                                <td>
                                    <span style="color: <?= e($issue['issue_type_color']) ?>">
                                        <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="fw-medium text-decoration-none">
                                        <?= e($issue['issue_key']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="text-dark text-decoration-none">
                                        <?= e($issue['summary']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($issue['assignee_name']): ?>
                                    <span class="small"><?= e($issue['assignee_name']) ?></span>
                                    <?php else: ?>
                                    <span class="text-muted small">Unassigned</span>
                                    <?php endif; ?>
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
                                <td class="text-muted small"><?= time_ago($issue['updated_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (($totalPages ?? 1) > 1): ?>
                <div class="card-footer bg-transparent">
                    <nav>
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                            <li class="page-item <?= $i === ($currentPage ?? 1) ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i, 'view' => $view ?? 'list'])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Save Filter Modal -->
<div class="modal fade" id="saveFilterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Save Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/filters') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="query" value="<?= e(http_build_query($_GET)) ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Filter Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
function changeSortOrder(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>
<?php \App\Core\View::endSection(); ?>
