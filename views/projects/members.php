<?php \App\Core\View:: extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper">
    <!-- 1. Breadcrumb Navigation -->
    <div class="breadcrumb">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-folder"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Team Members</span>
    </div>

    <!-- 2. Page Header -->
    <div class="page-header">
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
                <h1 class="page-title">Team Members</h1>
                <p class="page-subtitle">Manage access and roles for <?= e($project['name']) ?></p>
            </div>
        </div>
        <div class="header-actions">
            <?php if (can('manage-members', $project['id'])): ?>
                <button class="action-button primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-person-plus-fill"></i> Add Member
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- 3. Main Content Area -->
    <div class="page-content">
        <!-- LEFT COLUMN -->
        <div class="content-left">

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= count($members) ?></div>
                    <div class="stat-label">Total Members</div>
                    <i class="bi bi-people stat-icon"></i>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        <?= count(array_filter($members, fn($m) => ($m['role'] ?? '') === 'developer')) ?>
                    </div>
                    <div class="stat-label">Developers</div>
                    <i class="bi bi-code-slash stat-icon"></i>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        <?= count(array_filter($members, fn($m) => ($m['role'] ?? '') === 'qa')) ?>
                    </div>
                    <div class="stat-label">QA Testers</div>
                    <i class="bi bi-bug stat-icon"></i>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        <?= count(array_filter($members, fn($m) => ($m['role'] ?? '') === 'project_lead')) ?>
                    </div>
                    <div class="stat-label">Leads</div>
                    <i class="bi bi-star stat-icon"></i>
                </div>
            </div>

            <!-- Controls Bar -->
            <div class="controls-bar">
                <div class="search-filter-group">
                    <div class="search-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="memberSearch" placeholder="Search members..." class="search-input">
                    </div>
                    <div class="filter-wrapper">
                        <i class="bi bi-funnel filter-icon"></i>
                        <select id="roleFilter" class="filter-select">
                            <option value="">All Roles</option>
                            <option value="administrator">Administrator</option>
                            <option value="project_lead">Project Lead</option>
                            <option value="developer">Developer</option>
                            <option value="qa">QA</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                </div>
                <div class="view-toggle-group">
                    <button class="view-btn active" id="btnGridView" title="Grid View">
                        <i class="bi bi-grid-fill"></i>
                    </button>
                    <button class="view-btn" id="btnListView" title="List View">
                        <i class="bi bi-list-ul"></i>
                    </button>
                </div>
            </div>

            <!-- Members Content -->
            <div class="members-container">
                <?php if (empty($members)): ?>
                    <div class="empty-state">
                        <i class="bi bi-people empty-icon"></i>
                        <h3>No Members Found</h3>
                        <p>This project has no members yet.</p>
                    </div>
                <?php else: ?>

                    <!-- GRID VIEW -->
                    <div id="gridView" class="members-grid">
                        <?php foreach ($members as $member): ?>
                            <div class="member-card member-item"
                                data-member-name="<?= strtolower(e($member['display_name'])) ?>"
                                data-member-email="<?= strtolower(e($member['email'])) ?>"
                                data-member-role="<?= e($member['role']) ?>" data-member-role-id="<?= e($member['role_id']) ?>">

                                <div class="card-options">
                                    <?php if (can('manage-members', $project['id'])): ?>
                                        <div class="dropdown">
                                            <button class="btn-icon" id="dropdownBtn<?= $member['user_id'] ?>"
                                                data-bs-toggle="dropdown" aria-expanded="false" type="button">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <!-- <ul class="dropdown-menu" aria-labelledby="dropdownBtn<?= $member['user_id'] ?>"> -->
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownBtn<?= $member['user_id'] ?>">

                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#changeRoleModal"
                                                        data-member-id="<?= e($member['user_id']) ?>"
                                                        data-member-name="<?= e($member['display_name']) ?>"
                                                        data-member-role="<?= e($member['role']) ?>"
                                                        data-member-role-id="<?= e($member['role_id']) ?>"
                                                        onclick="setupChangeRole(this); return false;">
                                                        Change Role
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#removeMemberModal"
                                                        data-member-id="<?= e($member['user_id']) ?>"
                                                        data-member-name="<?= e($member['display_name']) ?>"
                                                        onclick="setupRemoveMember(this); return false;">
                                                        Remove
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="member-avatar-large">
                                    <?php if ($url = avatar($member['avatar'] ?? null, $member['display_name'] ?? 'U')): ?>
                                        <img src="<?= e($url) ?>" alt="Avatar">
                                    <?php else: ?>
                                        <div class="avatar-fallback">
                                            <?= avatarInitials($member['display_name'], $member['email']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="member-info">
                                    <h3 class="member-name">
                                        <?= e($member['display_name']) ?>
                                        <?php if ($member['user_id'] === $project['lead_id']): ?>
                                            <i class="bi bi-star-fill text-warning ms-1" title="Project Lead"></i>
                                        <?php endif; ?>
                                    </h3>
                                    <p class="member-email"><?= e($member['email']) ?></p>

                                    <?php
                                    $role = $member['role'] ?? 'viewer';
                                    $roleClass = match ($role) {
                                        'administrator' => 'role-admin',
                                        'project_lead' => 'role-lead',
                                        'developer' => 'role-dev',
                                        'qa' => 'role-qa',
                                        default => 'role-viewer'
                                    };
                                    ?>
                                    <span class="role-badge <?= $roleClass ?>">
                                        <?= ucwords(str_replace('_', ' ', $role)) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- LIST VIEW -->
                    <div id="listView" class="members-list" style="display: none;">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th class="sortable" data-sort="name">Member <i
                                            class="bi bi-arrow-down-up sort-icon"></i></th>
                                    <th class="sortable" data-sort="role">Role <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th>Stats</th>
                                    <th>Joined</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                    <tr class="member-item" data-member-name="<?= strtolower(e($member['display_name'])) ?>"
                                        data-member-email="<?= strtolower(e($member['email'])) ?>"
                                        data-member-role="<?= e($member['role']) ?>"
                                        data-member-role-id="<?= e($member['role_id']) ?>">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="member-avatar-small">
                                                    <?php if ($url = avatar($member['avatar'] ?? null, $member['display_name'] ?? 'U')): ?>
                                                        <img src="<?= e($url) ?>" alt="Avatar">
                                                    <?php else: ?>
                                                        <div class="avatar-fallback">
                                                            <?= avatarInitials($member['display_name'], $member['email']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= e($member['display_name']) ?></div>
                                                    <div class="small text-muted"><?= e($member['email']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="role-badge <?= match ($member['role'] ?? '') {
                                                'administrator' => 'role-admin',
                                                'project_lead' => 'role-lead',
                                                'developer' => 'role-dev',
                                                'qa' => 'role-qa',
                                                default => 'role-viewer'
                                            } ?>">
                                                <?= ucwords(str_replace('_', ' ', $member['role'] ?? 'Viewer')) ?>
                                            </span>
                                        </td>
                                        <td><?= (int) ($member['assigned_issues_count'] ?? 0) ?> Issues</td>
                                        <td><?= isset($member['created_at']) ? date('M d, Y', strtotime($member['created_at'])) : '—' ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if (can('manage-members', $project['id'])): ?>
                                                <div class="dropdown">
                                                    <button class="btn-icon" id="dropdownBtnList<?= $member['user_id'] ?>"
                                                        data-bs-toggle="dropdown" aria-expanded="false" type="button"><i
                                                            class="bi bi-three-dots"></i></button>
                                                    <ul class="dropdown-menu"
                                                        aria-labelledby="dropdownBtnList<?= $member['user_id'] ?>">
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#changeRoleModal"
                                                                data-member-id="<?= e($member['user_id']) ?>"
                                                                data-member-name="<?= e($member['display_name']) ?>"
                                                                data-member-role="<?= e($member['role']) ?>"
                                                                data-member-role-id="<?= e($member['role_id']) ?>"
                                                                onclick="setupChangeRole(this); return false;">Change Role</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#removeMemberModal"
                                                                data-member-id="<?= e($member['user_id']) ?>"
                                                                data-member-name="<?= e($member['display_name']) ?>"
                                                                onclick="setupRemoveMember(this); return false;">Remove</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT COLUMN (SIDEBAR) -->
        <div class="content-right">
            <!-- Project Details Card -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h3 class="card-title">Project Details</h3>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <span class="detail-label">Key</span>
                        <span class="detail-value badge-key"><?= e($project['key']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Team Size</span>
                        <span class="detail-value"><?= count($members) ?> Members</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Lead</span>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <?php
                            $lead = null;
                            foreach ($members as $m) {
                                if ($m['user_id'] === $project['lead_id']) {
                                    $lead = $m;
                                    break;
                                }
                            }
                            ?>
                            <?php if ($lead): ?>
                                <div class="member-avatar-small" style="width: 24px; height: 24px; font-size: 10px;">
                                    <?= avatarInitials($lead['display_name'], $lead['email']) ?>
                                </div>
                                <span class="detail-value"><?= e($lead['display_name']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">Unassigned</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h3 class="card-title">Quick Links</h3>
                </div>
                <div class="card-body p-0">
                    <a href="<?= url("/projects/{$project['key']}/settings") ?>" class="sidebar-link">
                        <span>Project Settings</span>
                        <i class="bi bi-gear"></i>
                    </a>
                    <a href="<?= url("/projects/{$project['key']}/board") ?>" class="sidebar-link">
                        <span>Kanban Board</span>
                        <i class="bi bi-kanban"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals (Add, Change Role, Remove) -->
<?php \App\Core\View::endSection(); ?>
<?php \App\Core\View::section('modals'); ?>

<!-- Redesigned Add Member Modal -->
<style>
    /* ============================================
       GLOBAL MODAL RULES - HIDE ALL BY DEFAULT
       ============================================ */
    /* Hide all modals by default */
    .modal {
        display: none !important;
        visibility: hidden !important;
    }

    /* Show only the active modal */
    .modal.show {
        display: block !important;
        visibility: visible !important;
    }

    /* When modal is open, prevent body scroll and hide content below modal */
    body.modal-open,
    body:has(.modal.show) {
        overflow: hidden !important;
        /* Prevent scrolling when modal open */
    }

    /* ============================================
       MODAL BACKDROP FIX - ENSURES MODAL BLOCKS ALL INTERACTIONS
       ============================================ */
    /* Ensure backdrop covers entire viewport and blocks all interactions */
    #addMemberModal.modal {
        z-index: 2050 !important;
    }

    #addMemberModal.modal.show {
        display: block !important;
        visibility: visible !important;
    }

    #addMemberModal .modal-backdrop {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
        z-index: 2040 !important;
        pointer-events: auto !important;
        /* Ensure backdrop blocks mouse events */
    }

    #addMemberModal .modal-backdrop.show {
        opacity: 1 !important;
        display: block !important;
    }

    /* Add Member Modal - Standard Design matching Create Issue */
    #addMemberModal.modal.show {
        overflow: hidden !important;
        /* Prevent body scroll when modal open */
    }

    /* Fixed Modal Dialog - Rely on Bootstrap's structure but enforce clean resets */
    #addMemberModal .modal-dialog {
        max-width: 500px;
        margin: 1.75rem auto;
        z-index: 2050 !important;
        position: relative;
        /* Remove custom flex/height that might cause stretching ghosts */
        background-color: transparent !important;
        box-shadow: none !important;
        border: none !important;
        pointer-events: none;
        /* Standard Bootstrap */
    }

    #addMemberModal .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        height: auto !important;
        background-color: #ffffff !important;
        z-index: 2051 !important;
        overflow: hidden;
        pointer-events: auto;
        /* Re-enable clicks */
    }

    #addMemberModal .modal-header {
        padding: 16px 24px;
        border-bottom: 1px solid #e5e7eb;
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    /* Form Layout Fix */
    #addMemberForm {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        margin: 0;
        padding: 0;
        /* Align with modal-content */
        background-color: transparent !important;
        /* Prevent double-background */
    }

    #addMemberModal .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    #addMemberModal .modal-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1 1 auto;
        min-height: 0;
        background-color: #ffffff;
    }

    #addMemberModal .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    #addMemberModal .form-select {
        font-size: 14px;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        width: 100%;
        background-color: #fff;
        /* Ensure white bg */
    }

    #addMemberModal .form-select:focus {
        border-color: #8b1956;
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
        outline: none;
    }

    #addMemberModal .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e5e7eb;
        background-color: #f9fafb;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-shrink: 0;
    }

    #addMemberModal .btn {
        font-size: 14px;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 6px;
    }

    /* Fix dropdown z-index issues */
    .dropdown-menu {
        z-index: 1055 !important;
    }
</style>

<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invite New Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= url("/projects/{$project['key']}/members") ?>" method="POST" id="addMemberForm">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="form-label">Select User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Choose user...</option>
                            <?php foreach ($availableUsers as $user): ?>
                                <option value="<?= e($user['id']) ?>" data-is-admin="<?= e($user['is_admin']) ?>"
                                    data-global-role-id="<?= e($user['global_role_id'] ?? '') ?>">
                                    <?= e($user['display_name'] ?? $user['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Role</label>
                        <select name="role_id" id="addMemberRole" class="form-select" required>
                            <?php foreach ($availableRoles as $role): ?>
                                <option value="<?= e($role['id']) ?>" data-role-slug="<?= e($role['slug']) ?>">
                                    <?= e($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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

<!-- Change Role Modal Styles -->
<style>
    /* Ensure backdrop covers entire viewport for changeRoleModal */
    #changeRoleModal.modal {
        z-index: 2050 !important;
        display: none !important;
        visibility: hidden !important;
    }

    #changeRoleModal.modal.show {
        display: block !important;
        visibility: visible !important;
    }

    #changeRoleModal.modal.show {
        overflow: hidden !important;
        /* Prevent body scroll when modal open */
    }

    #changeRoleModal .modal-backdrop {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
        z-index: 2040 !important;
        pointer-events: auto !important;
        /* Ensure backdrop blocks mouse events */
    }

    #changeRoleModal .modal-backdrop.show {
        opacity: 1 !important;
        display: block !important;
    }

    #changeRoleModal .modal-dialog {
        z-index: 2050 !important;
        position: relative;
        background-color: transparent !important;
        box-shadow: none !important;
        border: none !important;
        pointer-events: none;
    }

    #changeRoleModal .modal-content {
        background-color: #ffffff !important;
        z-index: 2051 !important;
        height: auto !important;
        overflow: hidden;
        pointer-events: auto;
    }

    #changeRoleModal .modal-body {
        background-color: #ffffff;
        overflow-y: auto;
    }
</style>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="changeRoleForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    <p class="mb-4">Updating role for <strong id="roleMemberName"></strong></p>
                    <div class="mb-4">
                        <label class="form-label">New Role</label>
                        <select id="newRole" name="role_id" class="form-select" required>
                            <?php foreach ($availableRoles as $role): ?>
                                <option value="<?= e($role['id']) ?>"><?= e($role['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Remove Member Modal Styles -->
<style>
    /* Ensure backdrop covers entire viewport for removeMemberModal */
    #removeMemberModal.modal {
        z-index: 2050 !important;
        display: none !important;
        visibility: hidden !important;
    }

    #removeMemberModal.modal.show {
        display: block !important;
        visibility: visible !important;
    }

    #removeMemberModal.modal.show {
        overflow: hidden !important;
        /* Prevent body scroll when modal open */
    }

    #removeMemberModal .modal-backdrop {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
        z-index: 2040 !important;
        pointer-events: auto !important;
        /* Ensure backdrop blocks mouse events */
    }

    #removeMemberModal .modal-backdrop.show {
        opacity: 1 !important;
        display: block !important;
    }

    #removeMemberModal .modal-dialog {
        z-index: 2050 !important;
        position: relative;
        background-color: transparent !important;
        box-shadow: none !important;
        border: none !important;
        pointer-events: none;
        margin-top: 20px !important;
    }

    #removeMemberModal .modal-content {
        background-color: #ffffff !important;
        z-index: 2051 !important;
        height: auto !important;
        overflow: hidden;
        pointer-events: auto;
    }

    #removeMemberModal .modal-body {
        background-color: #ffffff;
        overflow-y: auto;
    }
</style>

<!-- Remove Member Modal -->
<div class="modal fade" id="removeMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove <strong id="removeMemberName"></strong>? This cannot be undone.</p>
                <form method="POST" id="removeMemberForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Remove Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
   ENTERPRISE JIRA DESIGN SYSTEM
   ============================================ */

    :root {
        --jira-blue: #8B1956;
        /* Plum Primary */
        --jira-blue-dark: #6F123F;
        /* Dark Plum Hover */
        --jira-dark: #172B4D;
        /* Text Main */
        --jira-gray: #6B778C;
        /* Text Muted */
        --jira-light: #F4F5F7;
        /* Background Page */
        --jira-white: #FFFFFF;
        /* Background Card */
        --jira-border: #DFE1E6;
        /* Borders */
        --jira-hover: #FAFBFC;
        /* Hover bg */
        --shadow-sm: 0 1px 2px rgba(9, 30, 66, 0.05);
        --shadow-md: 0 4px 8px rgba(9, 30, 66, 0.08);
    }

    body {
        background-color: var(--jira-light);
        color: var(--jira-dark);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    /* 1. Breadcrumb */
    .breadcrumb {
        background: var(--jira-white);
        padding: 10px 20px;
        /* Reduced from 16px 32px */
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        /* Reduced from 14px */
        margin: 0;
    }

    .breadcrumb-link {
        color: var(--jira-gray);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue);
    }

    .breadcrumb-separator {
        color: var(--jira-gray);
        opacity: 0.6;
    }

    .breadcrumb-current {
        font-weight: 500;
        color: var(--jira-dark);
    }

    /* 2. Header */
    .page-header {
        background: var(--jira-white);
        padding: 24px;
        /* Reduced from 32px */
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .header-left {
        display: flex;
        gap: 16px;
        /* Reduced from 24px */
        align-items: center;
    }

    .header-avatar {
        width: 48px;
        height: 48px;
        /* Reduced from 64px */
        border-radius: 6px;
        /* Reduced radius */
        overflow: hidden;
        background: var(--jira-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        /* Reduced font */
        font-weight: bold;
    }

    .header-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .page-title {
        margin: 0 0 4px;
        font-size: 20px;
        /* Reduced from 24px */
        font-weight: 600;
        color: var(--jira-dark);
    }

    .page-subtitle {
        margin: 0;
        color: var(--jira-gray);
        font-size: 13px;
        /* Reduced from 14px */
    }

    .action-button {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        color: var(--jira-dark);
        padding: 6px 12px;
        /* Reduced padding */
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        /* Reduced font */
    }

    .action-button:hover {
        background: var(--jira-hover);
        box-shadow: var(--shadow-sm);
    }

    .action-button.primary {
        background: var(--jira-blue);
        color: white;
        border-color: var(--jira-blue);
    }

    .action-button.primary:hover {
        background: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
    }

    /* 3. Main Content */
    .page-content {
        display: flex;
        padding: 24px;
        /* Reduced from 32px */
        gap: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }

    .content-left {
        flex: 1;
        min-width: 0;
    }

    .content-right {
        width: 260px;
        /* Slightly reduced width */
        flex-shrink: 0;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        /* Reduced min-width */
        gap: 12px;
        /* Reduced gap */
        margin-bottom: 24px;
        /* Reduced margin */
    }

    .stat-card {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        padding: 16px;
        /* Reduced from 20px */
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
        border-color: #B3D4FF;
    }

    .stat-value {
        font-size: 20px;
        /* Reduced from 24px */
        font-weight: 700;
        color: var(--jira-dark);
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 12px;
        /* Reduced from 13px */
        font-weight: 600;
        color: var(--jira-gray);
        text-transform: uppercase;
    }

    .stat-icon {
        position: absolute;
        top: 16px;
        right: 16px;
        font-size: 20px;
        /* Reduced size */
        color: var(--jira-border);
    }

    /* Controls */
    .controls-bar {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        padding: 12px;
        /* Reduced from 16px */
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        /* Reduced margin */
        gap: 12px;
    }

    .search-filter-group {
        display: flex;
        gap: 10px;
        flex: 1;
    }

    .search-wrapper,
    .filter-wrapper {
        position: relative;
    }

    .search-wrapper {
        flex: 1;
        max-width: 280px;
    }

    .search-input,
    .filter-select {
        width: 100%;
        padding: 6px 10px 6px 32px;
        /* Reduced padding */
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        background: var(--jira-hover);
        color: var(--jira-dark);
        font-size: 13px;
        /* Reduced font */
        transition: all 0.2s;
    }

    .search-input:focus,
    .filter-select:focus {
        background: white;
        border-color: var(--jira-blue);
        outline: none;
        box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
    }

    .search-icon,
    .filter-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--jira-gray);
        pointer-events: none;
        font-size: 12px;
    }

    .view-btn {
        background: none;
        border: 1px solid var(--jira-border);
        padding: 6px;
        border-radius: 4px;
        color: var(--jira-gray);
        cursor: pointer;
        width: 32px;
        height: 32px;
        /* Reduced size */
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .view-btn.active {
        background: var(--jira-blue);
        border-color: var(--jira-blue);
        color: white;
    }

    /* Members Grid */
    .members-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        /* Reduced min-width */
        gap: 16px;
        /* Reduced gap */
    }

    .member-card {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        padding: 16px;
        /* Reduced from 24px */
        text-align: center;
        position: relative;
        transition: all 0.2s;
    }

    .member-card:hover {
        /* removed transform to fix z-index stacking context for dropdowns */
        box-shadow: var(--shadow-md);
        border-color: var(--jira-blue);
        z-index: 2;
        /* Bring to front on hover */
    }

    .member-avatar-large {
        width: 56px;
        height: 56px;
        /* Reduced from 80px */
        border-radius: 50%;
        margin: 0 auto 12px;
        background: var(--jira-light);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        font-size: 20px;
        font-weight: bold;
        color: var(--jira-gray);
    }

    .member-avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .member-name {
        font-size: 14px;
        /* Reduced from 16px */
        font-weight: 600;
        color: var(--jira-dark);
        margin: 0 0 4px;
    }

    .member-email {
        font-size: 12px;
        /* Reduced from 13px */
        color: var(--jira-gray);
        margin-bottom: 10px;
    }

    .role-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        background: var(--jira-hover);
        color: var(--jira-dark);
    }

    /* Role Colors */
    .role-admin {
        background: #EAE6FF;
        color: #403294;
    }

    .role-lead {
        background: #FFF0B3;
        color: #172B4D;
    }

    .role-dev {
        background: #DEEBFF;
        color: #0747A6;
    }

    .role-qa {
        background: #E3FCEF;
        color: #006644;
    }

    .card-options {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 100;
        /* Standard z-index */
    }

    .btn-icon {
        background: none;
        border: none;
        color: var(--jira-gray);
        cursor: pointer;
        font-size: 16px;
        padding: 6px;
        border-radius: 4px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        /* Standard size */
        min-width: 32px;
    }

    .btn-icon:hover {
        background: var(--jira-hover);
        color: var(--jira-blue);
    }

    /* Removed custom .dropdown and .dropdown-menu overrides to let Bootstrap handle it */

    /* List View */
    .members-list {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        /* overflow: hidden; REMOVED to allow dropdowns to show */
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table th {
        background: var(--jira-hover);
        padding: 10px 16px;
        /* Reduced padding */
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: var(--jira-gray);
        text-transform: uppercase;
        border-bottom: 1px solid var(--jira-border);
        cursor: pointer;
    }

    .modern-table td {
        padding: 10px 16px;
        /* Reduced padding */
        border-bottom: 1px solid var(--jira-border);
        color: var(--jira-dark);
        font-size: 13px;
        /* Reduced font */
    }

    .member-avatar-small {
        width: 28px;
        height: 28px;
        /* Reduced from 32px */
        border-radius: 50%;
        background: var(--jira-light);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: bold;
        color: var(--jira-gray);
    }

    .member-avatar-small img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Sidebar */
    .sidebar-card {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .card-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--jira-border);
    }

    .card-title {
        font-size: 13px;
        font-weight: 700;
        margin: 0;
        color: var(--jira-dark);
    }

    .card-body {
        padding: 16px;
    }

    .detail-item {
        margin-bottom: 12px;
    }

    .detail-item:last-child {
        margin-bottom: 0;
    }

    .detail-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .detail-value {
        font-size: 13px;
        font-weight: 500;
        color: var(--jira-dark);
    }

    .badge-key {
        background: var(--jira-hover);
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 12px;
    }

    .sidebar-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        text-decoration: none;
        color: var(--jira-dark);
        border-bottom: 1px solid var(--jira-border);
        transition: background 0.2s;
        font-size: 13px;
    }

    .sidebar-link:hover {
        background: var(--jira-hover);
        color: var(--jira-blue);
    }

    .sidebar-link:last-child {
        border-bottom: none;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .page-content {
            flex-direction: column;
        }

        .content-right {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 16px;
        }

        .controls-bar {
            flex-direction: column;
            align-items: stretch;
        }
    }

    /* Modals */
    .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
    }

    .modal-header {
        border-bottom: 1px solid var(--jira-border);
        padding: 20px;
    }

    .modal-title {
        font-weight: 600;
        font-size: 18px;
    }

    .modal-body {
        padding: 24px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--jira-gray);
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .form-select,
    .form-control {
        border-radius: 4px;
        border: 1px solid var(--jira-border);
        padding: 10px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. View Toggles
        const gridViewBtn = document.getElementById('btnGridView');
        const listViewBtn = document.getElementById('btnListView');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');

        function setView(view) {
            if (view === 'grid') {
                gridView.style.display = 'grid';
                listView.style.display = 'none';
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
            } else {
                gridView.style.display = 'none';
                listView.style.display = 'block';
                gridViewBtn.classList.remove('active');
                listViewBtn.classList.add('active');
            }
            localStorage.setItem('membersViewMode', view);
        }

        gridViewBtn.addEventListener('click', () => setView('grid'));
        listViewBtn.addEventListener('click', () => setView('list'));

        // Load preference
        const savedView = localStorage.getItem('membersViewMode') || 'grid';
        setView(savedView);

        // 2. Search & Filter
        const searchInput = document.getElementById('memberSearch');
        const roleFilter = document.getElementById('roleFilter');
        const memberItems = document.querySelectorAll('.member-item');

        function filterMembers() {
            const term = searchInput.value.toLowerCase().trim();
            const role = roleFilter.value;

            memberItems.forEach(item => {
                const name = item.dataset.memberName;
                const email = item.dataset.memberEmail;
                const itemRole = item.dataset.memberRole;

                const matchesSearch = name.includes(term) || email.includes(term);
                const matchesRole = !role || itemRole === role;

                if (matchesSearch && matchesRole) {
                    // If it's a table row, removing display:none is enough (it reverts to table-row)
                    // For div, it reverts to block/grid item
                    if (item.tagName === 'TR') item.style.display = '';
                    else item.style.display = 'block'; // Grid handles layout really
                } else {
                    item.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterMembers);
        roleFilter.addEventListener('change', filterMembers);

        // 3. Sorting (List View)
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', () => {
                const sortType = header.dataset.sort;
                const tbody = document.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const isAsc = !header.classList.contains('asc');

                // Reset icons/classes
                document.querySelectorAll('.sortable').forEach(h => {
                    h.classList.remove('asc', 'desc');
                    h.querySelector('.sort-icon').className = 'bi bi-arrow-down-up sort-icon';
                });

                header.classList.add(isAsc ? 'asc' : 'desc');
                header.querySelector('.sort-icon').className = isAsc ? 'bi bi-arrow-up-short sort-icon' : 'bi bi-arrow-down-short sort-icon';

                rows.sort((a, b) => {
                    let valA, valB;
                    if (sortType === 'name') {
                        valA = a.dataset.memberName;
                        valB = b.dataset.memberName;
                    } else if (sortType === 'role') {
                        valA = a.dataset.memberRole;
                        valB = b.dataset.memberRole;
                    } else if (sortType === 'joined') {
                        valA = parseInt(a.dataset.joined);
                        valB = parseInt(b.dataset.joined);
                    }

                    if (valA < valB) return isAsc ? -1 : 1;
                    if (valA > valB) return isAsc ? 1 : -1;
                    return 0;
                });

                rows.forEach(row => tbody.appendChild(row));
            });
        });
    });

    // Role Auto-Detection Logic
    const userSelect = document.querySelector('select[name="user_id"]');
    const roleSelect = document.getElementById('addMemberRole');

    if (userSelect && roleSelect) {
        userSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const isAdmin = selectedOption.dataset.isAdmin == '1';

            // Logic: If Admin -> Administrator, else -> Developer (default)
            // Iterate options to find matching role
            let targetSlug = isAdmin ? 'administrator' : 'developer';

            for (let i = 0; i < roleSelect.options.length; i++) {
                if (roleSelect.options[i].dataset.roleSlug === targetSlug) {
                    roleSelect.selectedIndex = i;
                    break;
                }
            }
        });
    }

    // Modal Helpers
    function setupChangeRole(el) {
        document.getElementById('roleMemberName').textContent = el.dataset.memberName;
        document.getElementById('newRole').value = el.dataset.memberRoleId;
        // Dynamically set action
        const baseAction = document.getElementById('addMemberForm').action;
        document.getElementById('changeRoleForm').action = baseAction + '/' + el.dataset.memberId;
    }

    function setupRemoveMember(el) {
        document.getElementById('removeMemberName').textContent = el.dataset.memberName;
        const baseAction = document.getElementById('addMemberForm').action;
        document.getElementById('removeMemberForm').action = baseAction + '/' + el.dataset.memberId;
    }

    // Automate Role Selection in Add Member Modal
    document.querySelector('#addMemberModal select[name="user_id"]')?.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const globalRoleId = selectedOption.getAttribute('data-global-role-id');
        const roleSelect = document.getElementById('addMemberRole');

        if (globalRoleId && roleSelect) {
            // Find the option with the matching role_id
            for (let i = 0; i < roleSelect.options.length; i++) {
                if (roleSelect.options[i].value === globalRoleId) {
                    roleSelect.selectedIndex = i;
                    break;
                }
            }
        }
    });
</script>

<style>
    /* 
    FIX: Use :has() and :focus-within to elevate z-index when dropdown is active.
    IMPORTANT: z-index values must stay BELOW modal backdrop (2040) to prevent cards from appearing above modal.
    This solves the Grid Stacking Context issue where later cards cover earlier dropdowns.
    */
    .member-card:focus-within,
    .member-card:hover,
    .member-card:has(.dropdown-menu.show) {
        z-index: 10 !important;
        /* Reduced from 100 to stay below modal backdrop (2040) */
        border-color: var(--jira-blue) !important;
    }

    /* Ensure list view rows also behave */
    .member-item:hover,
    .member-item:focus-within,
    .member-item:has(.dropdown-menu.show) {
        z-index: 10;
        /* Reduced from 100 to stay below modal backdrop (2040) */
        position: relative;
    }

    /* Ensure dropdown menu isn't clipped and has proper positioning */
    .dropdown-menu {
        border: 1px solid var(--jira-border);
        box-shadow: var(--shadow-md);
        z-index: 1055 !important;
        /* Higher than cards (10) but below modal (2040) */
        margin-top: 4px;

        /* FIX: Transparent dropdown on mobile */
        background-color: var(--jira-white) !important;
        opacity: 1 !important;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }

    /* Mobile-specific hardening */
    @media (max-width: 768px) {
        .dropdown-menu {
            background-color: #ffffff !important;
            border: 1px solid var(--jira-border);
            box-shadow: 0 8px 24px rgba(9, 30, 66, 0.15);
            z-index: 1055 !important;
            /* Consistent with desktop, below modal */
        }
    }

    /* Prevent background bleeding - but allow modal to properly overlay */
    .member-card,
    .members-grid,
    .members-list {
        /* Removed: isolation: isolate; - This was creating stacking context that blocked modal */
    }



    /* Force dropdown to be fully opaque and readable */
    .dropdown-menu .dropdown-item {
        background-color: transparent;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: var(--jira-hover);
    }
</style>


<?php \App\Core\View::endSection(); ?>