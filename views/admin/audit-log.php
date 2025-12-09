<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                    <li class="breadcrumb-item active">Audit Log</li>
                </ol>
            </nav>
            <h2 class="mb-0">Audit Log</h2>
        </div>
        <button class="btn btn-outline-secondary" onclick="exportLog()">
            <i class="bi bi-download me-1"></i> Export
        </button>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= url('/admin/audit-log') ?>" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Action Type</label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        <option value="login" <?= ($filters['action'] ?? '') === 'login' ? 'selected' : '' ?>>Login</option>
                        <option value="logout" <?= ($filters['action'] ?? '') === 'logout' ? 'selected' : '' ?>>Logout</option>
                        <option value="create" <?= ($filters['action'] ?? '') === 'create' ? 'selected' : '' ?>>Create</option>
                        <option value="update" <?= ($filters['action'] ?? '') === 'update' ? 'selected' : '' ?>>Update</option>
                        <option value="delete" <?= ($filters['action'] ?? '') === 'delete' ? 'selected' : '' ?>>Delete</option>
                        <option value="permission" <?= ($filters['action'] ?? '') === 'permission' ? 'selected' : '' ?>>Permission Change</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Entity Type</label>
                    <select name="entity" class="form-select form-select-sm">
                        <option value="">All Entities</option>
                        <option value="user" <?= ($filters['entity'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="project" <?= ($filters['entity'] ?? '') === 'project' ? 'selected' : '' ?>>Project</option>
                        <option value="issue" <?= ($filters['entity'] ?? '') === 'issue' ? 'selected' : '' ?>>Issue</option>
                        <option value="role" <?= ($filters['entity'] ?? '') === 'role' ? 'selected' : '' ?>>Role</option>
                        <option value="settings" <?= ($filters['entity'] ?? '') === 'settings' ? 'selected' : '' ?>>Settings</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        <?php foreach ($users ?? [] as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= e($u['display_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date Range</label>
                    <select name="date_range" class="form-select form-select-sm">
                        <option value="">All Time</option>
                        <option value="today" <?= ($filters['date_range'] ?? '') === 'today' ? 'selected' : '' ?>>Today</option>
                        <option value="week" <?= ($filters['date_range'] ?? '') === 'week' ? 'selected' : '' ?>>This Week</option>
                        <option value="month" <?= ($filters['date_range'] ?? '') === 'month' ? 'selected' : '' ?>>This Month</option>
                        <option value="quarter" <?= ($filters['date_range'] ?? '') === 'quarter' ? 'selected' : '' ?>>Last 3 Months</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="<?= url('/admin/audit-log') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <span><?= $totalEntries ?? 0 ?> entries found</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries ?? [] as $entry): ?>
                    <tr>
                        <td>
                            <span class="small"><?= format_datetime($entry['created_at']) ?></span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                     style="width: 28px; height: 28px; font-size: 0.7rem;">
                                    <?= strtoupper(substr($entry['user_name'] ?? 'S', 0, 1)) ?>
                                </div>
                                <span class="small"><?= e($entry['user_name'] ?? 'System') ?></span>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $actionColors = [
                                'login' => 'success',
                                'logout' => 'secondary',
                                'create' => 'primary',
                                'update' => 'info',
                                'delete' => 'danger',
                                'permission' => 'warning',
                            ];
                            $color = $actionColors[$entry['action']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $color ?>"><?= ucfirst($entry['action']) ?></span>
                        </td>
                        <td>
                            <span class="small text-muted"><?= ucfirst($entry['entity_type'] ?? '') ?></span>
                            <?php if ($entry['entity_id'] ?? null): ?>
                            <span class="small">#<?= $entry['entity_id'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="small"><?= e($entry['description'] ?? '') ?></span>
                        </td>
                        <td>
                            <code class="small"><?= e($entry['ip_address'] ?? '-') ?></code>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-light" data-bs-toggle="modal" 
                                    data-bs-target="#detailModal" onclick="showDetails(<?= htmlspecialchars(json_encode($entry)) ?>)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($entries)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-journal-text fs-1 d-block mb-2"></i>
                            No audit log entries found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (($totalPages ?? 1) > 1): ?>
        <div class="card-footer bg-transparent">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>">
                            &laquo; Previous
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>">
                            Next &raquo;
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0" id="detailContent">
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
function showDetails(entry) {
    const content = document.getElementById('detailContent');
    
    let html = `
        <dt class="col-sm-3">Timestamp</dt>
        <dd class="col-sm-9">${entry.created_at}</dd>
        
        <dt class="col-sm-3">User</dt>
        <dd class="col-sm-9">${entry.user_name || 'System'} (ID: ${entry.user_id || '-'})</dd>
        
        <dt class="col-sm-3">Action</dt>
        <dd class="col-sm-9">${entry.action}</dd>
        
        <dt class="col-sm-3">Entity Type</dt>
        <dd class="col-sm-9">${entry.entity_type || '-'}</dd>
        
        <dt class="col-sm-3">Entity ID</dt>
        <dd class="col-sm-9">${entry.entity_id || '-'}</dd>
        
        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">${entry.description || '-'}</dd>
        
        <dt class="col-sm-3">IP Address</dt>
        <dd class="col-sm-9"><code>${entry.ip_address || '-'}</code></dd>
        
        <dt class="col-sm-3">User Agent</dt>
        <dd class="col-sm-9"><small class="text-muted">${entry.user_agent || '-'}</small></dd>
    `;
    
    if (entry.old_values || entry.new_values) {
        html += `
            <dt class="col-sm-3">Changes</dt>
            <dd class="col-sm-9">
                <div class="row">
                    <div class="col-6">
                        <strong>Old Values:</strong>
                        <pre class="small bg-light p-2 rounded">${JSON.stringify(entry.old_values || {}, null, 2)}</pre>
                    </div>
                    <div class="col-6">
                        <strong>New Values:</strong>
                        <pre class="small bg-light p-2 rounded">${JSON.stringify(entry.new_values || {}, null, 2)}</pre>
                    </div>
                </div>
            </dd>
        `;
    }
    
    content.innerHTML = html;
}

function exportLog() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = `${window.location.pathname}?${params.toString()}`;
}
</script>
<?php \App\Core\View::endSection(); ?>
