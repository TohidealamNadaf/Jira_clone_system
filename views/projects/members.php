<?php \App\Core\View:: extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<div class="members-page-wrapper">

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-section">
        <div class="breadcrumb-container">
            <a href="<?= url('/projects') ?>" class="breadcrumb-link">
                <i class="bi bi-diagram-3"></i>Projects
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
                <?= e($project['name']) ?>
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Members</span>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header-section">
        <div class="header-left">
            <div class="header-avatar">
                <?php
                $projectAvatar = avatar($project['avatar'] ?? null, $project['name'] ?? 'Project');
                if ($projectAvatar): ?>
                    <img src="<?= e($projectAvatar) ?>" alt="<?= e($project['name']) ?>"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="avatar-fallback" style="display:none;">
                        <?= avatarInitials($project['name'] ?? 'P', $project['key'] ?? '') ?>
                    </div>
                <?php else: ?>
                    <div class="avatar-fallback">
                        <?= avatarInitials($project['name'] ?? 'P', $project['key'] ?? '') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="header-info">
                <h1 class="header-title">Project Members</h1>
                <p class="header-meta">
                    <span class="meta-item"><?= e($project['key']) ?></span>
                    <span class="meta-separator">•</span>
                    <span class="meta-item">
                        <?= count($members ?? []) ?> member<?= count($members ?? []) !== 1 ? 's' : '' ?>
                    </span>
                </p>
                <p class="header-description">Manage team access and assign roles within this project</p>
            </div>
        </div>

        <div class="header-actions">
            <?php if (can('manage-members', $project['id'])): ?>
                <button class="action-button" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-person-plus"></i>Add Member
                </button>
            <?php endif; ?>

            <a href="<?= url("/projects/{$project['key']}") ?>" class="action-button">
                <i class="bi bi-arrow-left"></i>Back to Project
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="page-content-section">

        <div class="content-main">
            <div class="members-card">
                <div class="card-header">
                    <h2 class="card-title">Team Members</h2>
                    <span class="card-count"><?= count($members ?? []) ?></span>
                </div>

                <div class="card-body">
                    <?php if (empty($members)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">👥</div>
                            <h3 class="empty-title">No members found</h3>
                            <?php if (can('manage-members', $project['id'])): ?>
                                <button class="btn-empty-cta" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                    <i class="bi bi-person-plus"></i>Add Member
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="members-list">
                            <?php foreach ($members as $member): ?>
                                <div class="member-row">

                                    <!-- Left -->
                                    <div class="member-cell-left">
                                        <div class="member-avatar-wrapper">
                                            <?php
                                            $avatarUrl = avatar($member['avatar'] ?? null, $member['display_name'] ?? 'User');
                                            if ($avatarUrl): ?>
                                                <img src="<?= e($avatarUrl) ?>" class="member-avatar"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="member-avatar-fallback" style="display:none;">
                                                    <?= avatarInitials($member['display_name'], $member['email']) ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="member-avatar-fallback">
                                                    <?= avatarInitials($member['display_name'], $member['email']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="member-info">
                                            <div class="member-name">
                                                <?= e($member['display_name']) ?>
                                                <?php if ($member['user_id'] === $project['lead_id']): ?>
                                                    <span class="lead-badge">Lead</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="member-email"><?= e($member['email']) ?></div>
                                        </div>
                                    </div>

                                    <!-- Role -->
                                    <div class="member-cell-center">
                                        <?php
                                        $role = $member['role'] ?? 'viewer';
                                        ?>
                                        <span class="role-badge"><?= ucwords(str_replace('_', ' ', $role)) ?></span>
                                    </div>

                                    <!-- Right -->
                                    <div class="member-cell-right">
                                        <div class="member-meta">
                                            <div class="meta-item">
                                                <span class="meta-label">Joined</span>
                                                <?php
                                                $dateSource = $member['joined_at'] ?? $member['created_at'] ?? null;
                                                ?>
                                                <span class="meta-value">
                                                    <?= $dateSource ? date('M j, Y', strtotime($dateSource)) : '—' ?>
                                                </span>
                                            </div>

                                            <div class="meta-item">
                                                <span class="meta-label">Issues</span>
                                                <a href="<?= url("/projects/{$project['key']}/issues?assignee={$member['user_id']}") ?>"
                                                    class="meta-link">
                                                    <?= (int) ($member['assigned_issues_count'] ?? 0) ?>
                                                </a>
                                            </div>
                                        </div>

                                        <?php if (can('manage-members', $project['id'])): ?>
                                            <div class="member-actions dropdown">
                                                <button class="action-menu-btn"
                                                    id="memberMenu<?= e($member['user_id']) ?>-<?= e($project['id']) ?>"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#changeRoleModal"
                                                            data-member-id="<?= e($member['user_id']) ?>"
                                                            data-member-name="<?= e($member['display_name']) ?>"
                                                            data-member-role="<?= e($member['role']) ?>">
                                                            <i class="bi bi-person-check"></i>Change Role
                                                        </a>
                                                    </li>

                                                    <?php if ($member['user_id'] !== $project['lead_id']): ?>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form method="POST"
                                                                action="<?= url("/projects/{$project['key']}/members/{$member['user_id']}") ?>"
                                                                onsubmit="return confirm('Remove member?')">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="_method" value="DELETE">
                                                                <button class="dropdown-item text-danger">
                                                                    <i class="bi bi-person-dash"></i>Remove
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form class="modal-content" method="POST" action="<?= url("/projects/{$project['key']}/members") ?>">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title">Add Member</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select class="form-input" name="user_id" required>
                    <option value="">Select user</option>
                    <?php foreach ($availableUsers ?? [] as $user): ?>
                        <option value="<?= e($user['id']) ?>">
                            <?= e($user['display_name']) ?> (<?= e($user['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <select class="form-input mt-2" name="role" required>
                    <option value="developer">Developer</option>
                    <option value="viewer">Viewer</option>
                    <option value="project_manager">Project Manager</option>
                    <option value="qa_tester">QA Tester</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary-modal" data-bs-dismiss="modal">Cancel</button>
                <button class="btn-primary-modal">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form id="changeRoleForm" class="modal-content" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div class="modal-header">
                <h5 class="modal-title">Change Role</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><strong id="memberName"></strong></p>
                <select class="form-input" name="role" id="newRole">
                    <option value="developer">Developer</option>
                    <option value="viewer">Viewer</option>
                    <option value="project_manager">Project Manager</option>
                    <option value="qa_tester">QA Tester</option>
                </select>
            </div>

            <div class="modal-footer">
                <button class="btn-secondary-modal" data-bs-dismiss="modal">Cancel</button>
                <button class="btn-primary-modal">Update</button>
            </div>
        </form>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
    document.getElementById('changeRoleModal')?.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn) return;

        const id = btn.dataset.memberId;
        if (!id) return;

        document.getElementById('memberName').textContent = btn.dataset.memberName || '';
        document.getElementById('newRole').value = btn.dataset.memberRole || 'viewer';
        document.getElementById('changeRoleForm').action =
            '<?= url("/projects/{$project['key']}/members/") ?>' + id;
    });
</script>
<?php \App\Core\View::endSection(); ?>