<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active">Members</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Project Members</h1>
        <?php if (can('manage-members', $project['id'])): ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
            <i class="bi bi-person-plus me-1"></i> Add Member
        </button>
        <?php endif; ?>
    </div>

    <!-- Member Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0"><?= count($members ?? []) ?></h3>
                    <small class="text-muted">Total Members</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0"><?= count(array_filter($members ?? [], fn($m) => ($m['role'] ?? '') === 'admin')) ?></h3>
                    <small class="text-muted">Administrators</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0"><?= count(array_filter($members ?? [], fn($m) => ($m['role'] ?? '') === 'developer')) ?></h3>
                    <small class="text-muted">Developers</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0"><?= count(array_filter($members ?? [], fn($m) => ($m['role'] ?? '') === 'viewer')) ?></h3>
                    <small class="text-muted">Viewers</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" 
                               value="<?= e($filters['search'] ?? '') ?>" placeholder="Search members...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="role">
                        <option value="">All Roles</option>
                        <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                        <option value="developer" <?= ($filters['role'] ?? '') === 'developer' ? 'selected' : '' ?>>Developer</option>
                        <option value="viewer" <?= ($filters['role'] ?? '') === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Members List -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Member</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Issues Assigned</th>
                        <?php if (can('manage-members', $project['id'])): ?>
                        <th class="text-end">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No members found</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($members as $member): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= e($member['avatar'] ?? '/images/default-avatar.png') ?>" 
                                     class="rounded-circle me-2" width="40" height="40">
                                <div>
                                    <div class="fw-medium">
                                        <?= e($member['display_name']) ?>
                                        <?php if ($member['id'] === $project['lead_id']): ?>
                                        <span class="badge bg-primary ms-1">Lead</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">@<?= e($member['username']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= e($member['email']) ?></td>
                        <td>
                            <?php
                            $roleColors = [
                                'admin' => 'danger',
                                'developer' => 'primary',
                                'viewer' => 'secondary'
                            ];
                            $roleLabels = [
                                'admin' => 'Administrator',
                                'developer' => 'Developer',
                                'viewer' => 'Viewer'
                            ];
                            ?>
                            <span class="badge bg-<?= $roleColors[$member['role'] ?? 'viewer'] ?>">
                                <?= $roleLabels[$member['role'] ?? 'viewer'] ?>
                            </span>
                        </td>
                        <td class="text-muted"><?= date('M j, Y', strtotime($member['joined_at'] ?? $member['created_at'])) ?></td>
                        <td>
                            <a href="<?= url("/projects/{$project['key']}/issues?assignee={$member['id']}") ?>">
                                <?= e($member['assigned_issues_count'] ?? 0) ?> issues
                            </a>
                        </td>
                        <?php if (can('manage-members', $project['id'])): ?>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changeRoleModal"
                                           data-member-id="<?= e($member['id']) ?>" data-member-name="<?= e($member['display_name']) ?>"
                                           data-member-role="<?= e($member['role']) ?>">
                                            <i class="bi bi-person-badge me-2"></i> Change Role
                                        </a>
                                    </li>
                                    <?php if ($member['id'] !== $project['lead_id']): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="<?= url("/projects/{$project['key']}/members/{$member['id']}") ?>" method="POST"
                                              onsubmit="return confirm('Remove this member from the project?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-person-dash me-2"></i> Remove
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url("/projects/{$project['key']}/members") ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">User</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Select user...</option>
                            <?php foreach ($availableUsers ?? [] as $u): ?>
                            <option value="<?= e($u['id']) ?>"><?= e($u['display_name']) ?> (<?= e($u['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="developer">Developer</option>
                            <option value="admin">Administrator</option>
                            <option value="viewer">Viewer</option>
                        </select>
                        <div class="form-text">
                            <strong>Administrator:</strong> Full access including settings<br>
                            <strong>Developer:</strong> Can create and edit issues<br>
                            <strong>Viewer:</strong> Read-only access
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="changeRoleForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <p>Change role for <strong id="memberName"></strong></p>
                    <div class="mb-3">
                        <label for="newRole" class="form-label">New Role</label>
                        <select class="form-select" id="newRole" name="role" required>
                            <option value="admin">Administrator</option>
                            <option value="developer">Developer</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
document.getElementById('changeRoleModal')?.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const memberId = button.getAttribute('data-member-id');
    const memberName = button.getAttribute('data-member-name');
    const memberRole = button.getAttribute('data-member-role');
    
    document.getElementById('memberName').textContent = memberName;
    document.getElementById('newRole').value = memberRole;
    document.getElementById('changeRoleForm').action = '<?= url("/projects/{$project['key']}/members/") ?>' + memberId;
});
</script>
<?php \App\Core\View::endSection(); ?>
