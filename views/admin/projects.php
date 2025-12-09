<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                    <li class="breadcrumb-item active">Projects</li>
                </ol>
            </nav>
            <h2 class="mb-0">Projects Management</h2>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= url('/admin/projects') ?>" method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search by project name or key..." 
                           value="<?= e($search ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="<?= url('/admin/projects') ?>" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Project Name</th>
                        <th>Key</th>
                        <th>Lead</th>
                        <th>Issues</th>
                        <th>Members</th>
                        <th>Created</th>
                        <th width="100">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3"></i>
                                <p class="mt-2">No projects found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td>
                                    <a href="<?= url('/projects/' . $project['key']) ?>" class="text-decoration-none fw-500">
                                        <?= e($project['name']) ?>
                                    </a>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1"><?= e($project['key']) ?></code>
                                </td>
                                <td>
                                    <?php if ($project['lead_id']): ?>
                                        <?php 
                                            $lead = \App\Core\Database::selectOne(
                                                "SELECT display_name FROM users WHERE id = ?",
                                                [$project['lead_id']]
                                            );
                                        ?>
                                        <?= e($lead['display_name'] ?? 'N/A') ?>
                                    <?php else: ?>
                                        <span class="text-muted">Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= $project['issue_count'] ?? 0 ?> issues
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= $project['member_count'] ?? 0 ?> members
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= format_date($project['created_at']) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($project['is_archived']): ?>
                                        <span class="badge bg-danger">Archived</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['last_page'] > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($pagination['current_page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('/admin/projects?page=1' . ($search ? '&search=' . urlencode($search) : '')) ?>">First</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('/admin/projects?page=' . ($pagination['current_page'] - 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php 
                $start = max(1, $pagination['current_page'] - 2);
                $end = min($pagination['last_page'], $pagination['current_page'] + 2);
                
                for ($i = $start; $i <= $end; $i++): 
                ?>
                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('/admin/projects?page=' . $i . ($search ? '&search=' . urlencode($search) : '')) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('/admin/projects?page=' . ($pagination['current_page'] + 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">Next</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('/admin/projects?page=' . $pagination['last_page'] . ($search ? '&search=' . urlencode($search) : '')) ?>">Last</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php \App\Core\View::endsection(); ?>
