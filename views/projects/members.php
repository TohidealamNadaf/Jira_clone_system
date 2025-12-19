<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="members-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="members-breadcrumb-section">
        <div class="members-breadcrumb">
            <a href="<?= url('/projects') ?>" class="breadcrumb-link">
                <i class="bi bi-diagram-3"></i> Projects
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
                <?= e($project['name']) ?>
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Members</span>
        </div>
    </div>

    <!-- Page Header -->
    <div class="members-page-header">
        <div class="members-header-left">
            <h1 class="members-page-title">Project Members <span class="members-page-subtitle">— Manage team access and roles</span></h1>
        </div>
        <?php if (can('manage-members', $project['id'])): ?>
        <button type="button" class="btn-primary-action" data-bs-toggle="modal" data-bs-target="#addMemberModal">
            <i class="bi bi-person-plus"></i> Add Member
        </button>
        <?php endif; ?>
    </div>

    <!-- Main Content Container -->
    <div class="members-content-container">
        <!-- Sidebar -->
        <div class="members-sidebar">
            <div class="members-sidebar-content">
                <!-- Stats Section -->
                <div class="members-stats-section">
                    <h4 class="stats-title">Team Overview</h4>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?= count($members ?? []) ?></div>
                            <div class="stat-label">Total</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= count(array_filter($members ?? [], fn($m) => ($m['role'] ?? '') === 'admin')) ?></div>
                            <div class="stat-label">Admins</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= count(array_filter($members ?? [], fn($m) => ($m['role'] ?? '') === 'developer')) ?></div>
                            <div class="stat-label">Devs</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= count(array_filter($members ?? [], fn($m) => ($m['role'] ?? '') === 'viewer')) ?></div>
                            <div class="stat-label">Viewers</div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="members-filter-section">
                    <h4 class="filter-title">Filter</h4>
                    <form method="GET" class="filter-form" id="memberFilterForm">
                        <div class="form-field">
                            <label class="form-field-label">Search Members</label>
                            <input type="text" class="form-field-input" name="search" 
                                   value="<?= e($filters['search'] ?? '') ?>" 
                                   placeholder="Name or email..." 
                                   onchange="document.getElementById('memberFilterForm').submit()">
                        </div>
                        <div class="form-field">
                            <label class="form-field-label">By Role</label>
                            <select class="form-field-select" name="role" onchange="document.getElementById('memberFilterForm').submit()">
                                <option value="">All Roles</option>
                                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                                <option value="developer" <?= ($filters['role'] ?? '') === 'developer' ? 'selected' : '' ?>>Developer</option>
                                <option value="viewer" <?= ($filters['role'] ?? '') === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                            </select>
                        </div>
                        <?php if (($filters['search'] ?? '') || ($filters['role'] ?? '')): ?>
                        <div class="form-field">
                            <a href="<?= url("/projects/{$project['key']}/members") ?>" class="clear-filters-link">
                                <i class="bi bi-x-circle"></i> Clear Filters
                            </a>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="members-main-content">
            <!-- Members List -->
            <?php if (empty($members)): ?>
            <div class="members-empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h3 class="empty-state-title">No members found</h3>
                <p class="empty-state-description">
                    <?php if (($filters['search'] ?? '') || ($filters['role'] ?? '')): ?>
                        No members match your filter criteria. Try adjusting your search or role filter.
                    <?php else: ?>
                        Add your first team member to get started.
                    <?php endif; ?>
                </p>
                <?php if (can('manage-members', $project['id'])): ?>
                <button type="button" class="btn-primary-action" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-person-plus"></i> Add First Member
                </button>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="members-list">
                <?php foreach ($members as $member): ?>
                <div class="member-card">
                    <!-- Member Header -->
                    <div class="member-card-header">
                        <div class="member-avatar-wrapper">
                             <?php if ($member['avatar'] ?? null): ?>
                                 <img src="<?= e($member['avatar']) ?>" class="member-avatar" alt="<?= e($member['display_name']) ?>">
                             <?php else: ?>
                                 <div class="member-avatar-placeholder">
                                     <?= strtoupper(substr($member['first_name'] ?? 'U', 0, 1)) ?>
                                 </div>
                             <?php endif; ?>
                             <?php if ($member['user_id'] === $project['lead_id']): ?>
                             <div class="member-lead-badge">Lead</div>
                             <?php endif; ?>
                         </div>
                         <div class="member-info">
                             <h3 class="member-name">
                                 <?= e($member['display_name']) ?>
                             </h3>
                             <p class="member-username">@<?= e($member['email']) ?></p>
                         </div>
                        <?php if (can('manage-members', $project['id'])): ?>
                        <div class="member-actions">
                            <button class="member-action-btn" data-bs-toggle="dropdown" title="More options">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changeRoleModal"
                                       data-member-id="<?= e($member['user_id']) ?>" 
                                       data-member-name="<?= e($member['display_name']) ?>"
                                       data-member-role="<?= e($member['role']) ?>">
                                        <i class="bi bi-person-badge me-2"></i> Change Role
                                    </a>
                                </li>
                                <?php if ($member['user_id'] !== $project['lead_id']): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="<?= url("/projects/{$project['key']}/members/{$member['user_id']}") ?>" method="POST" 
                                          style="display: contents;"
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
                        <?php endif; ?>
                    </div>

                    <!-- Member Details -->
                    <div class="member-card-body">
                        <div class="member-detail-row">
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?= e($member['email']) ?></span>
                        </div>
                        <div class="member-detail-row">
                            <span class="detail-label">Role</span>
                            <span class="detail-value">
                                <?php
                                $roleColors = [
                                    'administrator' => '#AE2A19',
                                    'developer' => '#8B1956',
                                    'project_manager' => '#0052CC',
                                    'qa_tester' => '#7B2CBF',
                                    'viewer' => '#626F86',
                                    'product_owner' => '#FB8500',
                                    'project-admin' => '#AE2A19',
                                    'project-member' => '#8B1956',
                                    'project-viewer' => '#626F86'
                                ];
                                $roleLabels = [
                                    'administrator' => 'Administrator',
                                    'developer' => 'Developer',
                                    'project_manager' => 'Project Manager',
                                    'qa_tester' => 'QA Tester',
                                    'viewer' => 'Viewer',
                                    'product_owner' => 'Product Owner',
                                    'project-admin' => 'Project Admin',
                                    'project-member' => 'Project Member',
                                    'project-viewer' => 'Project Viewer'
                                ];
                                $role = $member['role'] ?? 'viewer';
                                $color = $roleColors[$role] ?? '#626F86';
                                $label = $roleLabels[$role] ?? ucwords(str_replace('_', ' ', $role));
                                ?>
                                <span class="role-badge" style="background-color: <?= $color ?>20; color: <?= $color ?>; border: 1px solid <?= $color ?>40;">
                                    <?= $label ?>
                                </span>
                            </span>
                        </div>
                        <div class="member-detail-row">
                            <span class="detail-label">Joined</span>
                            <span class="detail-value"><?= date('M j, Y', strtotime($member['joined_at'] ?? $member['created_at'])) ?></span>
                        </div>
                        <div class="member-detail-row">
                            <span class="detail-label">Issues Assigned</span>
                            <span class="detail-value">
                                <a href="<?= url("/projects/{$project['key']}/issues?assignee={$member['user_id']}") ?>" class="issues-link">
                                    <?= e($member['assigned_issues_count'] ?? 0) ?> issues
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url("/projects/{$project['key']}/members") ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select User</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Choose a user...</option>
                            <?php foreach ($availableUsers ?? [] as $u): ?>
                            <option value="<?= e($u['id']) ?>"><?= e($u['display_name']) ?> (<?= e($u['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                         <label for="role_id" class="form-label">Role</label>
                         <select class="form-select" id="role_id" name="role_id" required>
                             <option value="">Select a role...</option>
                             <?php foreach ($availableRoles ?? [] as $role): ?>
                             <option value="<?= e($role['id']) ?>"><?= e($role['name']) ?></option>
                             <?php endforeach; ?>
                         </select>
                         <div class="form-text mt-2">
                             Choose a project role for this team member.
                         </div>
                     </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="changeRoleForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <p>Change role for <strong id="memberName"></strong></p>
                    <div class="mb-3">
                         <label for="newRole" class="form-label">New Role</label>
                         <select class="form-select" id="newRole" name="role_id" required>
                             <option value="">Select a role...</option>
                             <?php foreach ($availableRoles ?? [] as $role): ?>
                             <option value="<?= e($role['id']) ?>"><?= e($role['name']) ?></option>
                             <?php endforeach; ?>
                         </select>
                         <div class="form-text mt-2">
                             Choose a new role for this team member.
                         </div>
                     </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --jira-blue: #8B1956;
    --jira-blue-dark: #6F123F;
    --jira-light-blue: #f0dce5;
    --text-primary: #161B22;
    --text-secondary: #626F86;
    --text-muted: #97A0AF;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F8FA;
    --bg-tertiary: #ECEDF0;
    --border-color: #DFE1E6;
    --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
    --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Page Wrapper */
.members-page-wrapper {
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 80px);
    background-color: var(--bg-secondary);
}

/* Breadcrumb Section */
.members-breadcrumb-section {
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    padding: 0 32px;
    height: 48px;
    display: flex;
    align-items: center;
    box-shadow: var(--shadow-sm);
}

.members-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
}

.breadcrumb-link {
    color: var(--jira-blue) !important;
    text-decoration: none !important;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: color var(--transition);
}

.breadcrumb-link:hover {
    color: var(--jira-blue-dark) !important;
    text-decoration: none !important;
}

.breadcrumb-separator {
    color: var(--text-muted);
}

.breadcrumb-current {
    color: var(--text-primary);
    font-weight: 600;
}

/* Page Header */
.members-page-header {
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    padding: 24px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    box-shadow: var(--shadow-sm);
}

.members-header-left {
    flex: 1;
}

.members-page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    letter-spacing: -0.3px;
}

.members-page-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
    font-weight: 400;
    margin-left: 8px;
}

/* Action Button */
.btn-primary-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background-color: var(--jira-blue);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition);
    white-space: nowrap;
}

.btn-primary-action:hover {
    background-color: var(--jira-blue-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-primary-action:active {
    transform: translateY(0);
}

/* Content Container */
.members-content-container {
    display: flex;
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 48px;
    gap: 32px;
    flex: 1;
    width: 100%;
}

/* Sidebar */
.members-sidebar {
    flex-shrink: 0;
    width: 280px;
}

.members-sidebar-content {
    position: sticky;
    top: 24px;
}

/* Stats Section */
.members-stats-section {
    background-color: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    box-shadow: var(--shadow-md);
    margin-bottom: 20px;
}

.stats-title {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-secondary);
    margin: 0 0 16px 0;
    letter-spacing: 0.5px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.stat-item {
    text-align: center;
    padding: 8px;
}

.stat-value {
    display: block;
    font-size: 24px;
    font-weight: 700;
    color: var(--jira-blue);
    margin-bottom: 4px;
}

.stat-label {
    display: block;
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 500;
}

/* Filter Section */
.members-filter-section {
    background-color: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    box-shadow: var(--shadow-md);
}

.filter-title {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-secondary);
    margin: 0 0 16px 0;
    letter-spacing: 0.5px;
}

.filter-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.form-field {
    display: flex;
    flex-direction: column;
}

.form-field-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 6px;
}

.form-field-input,
.form-field-select {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 13px;
    color: var(--text-primary);
    background-color: var(--bg-primary);
    transition: border-color var(--transition);
}

.form-field-input:focus,
.form-field-select:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.1);
}

.clear-filters-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--jira-blue);
    text-decoration: none;
    font-size: 13px;
    transition: color var(--transition);
}

.clear-filters-link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

/* Main Content */
.members-main-content {
    flex: 1;
    min-width: 0;
}

/* Members List */
.members-list {
    display: grid;
    gap: 16px;
}

/* Member Card */
.member-card {
    background-color: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all var(--transition);
}

.member-card:hover {
    border-color: var(--jira-blue);
    box-shadow: var(--shadow-lg);
}

/* Member Card Header */
.member-card-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    border-bottom: 1px solid var(--border-color);
}

.member-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}

.member-avatar,
.member-avatar-placeholder {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: 2px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
}

.member-avatar {
    object-fit: cover;
}

.member-avatar-placeholder {
    background: linear-gradient(135deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
    color: white;
}

.member-lead-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    background-color: var(--jira-blue);
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 12px;
    border: 2px solid var(--bg-primary);
    white-space: nowrap;
}

.member-info {
    flex: 1;
}

.member-name {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 4px 0;
}

.member-username {
    font-size: 12px;
    color: var(--text-secondary);
    margin: 0;
}

/* Member Actions */
.member-actions {
    display: flex;
    gap: 8px;
}

.member-action-btn {
    width: 32px;
    height: 32px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background-color: var(--bg-secondary);
    color: var(--text-secondary);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition);
}

.member-action-btn:hover {
    background-color: var(--bg-tertiary);
    color: var(--text-primary);
    border-color: var(--jira-blue);
}

/* Member Card Body */
.member-card-body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.member-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.detail-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 13px;
    color: var(--text-primary);
    font-weight: 500;
}

.role-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.issues-link {
    color: var(--jira-blue);
    text-decoration: none;
    transition: color var(--transition);
}

.issues-link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

/* Empty State */
.members-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 40px;
    text-align: center;
    background-color: var(--bg-primary);
    border: 1px dashed var(--border-color);
    border-radius: 8px;
}

.empty-state-icon {
    font-size: 48px;
    color: var(--text-muted);
    margin-bottom: 16px;
}

.empty-state-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 8px 0;
}

.empty-state-description {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0 0 24px 0;
    max-width: 400px;
}

/* Modal Overrides */
.modal-header {
    border-bottom: 1px solid var(--border-color);
    padding: 20px 24px;
}

.modal-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 12px 24px;
    border-top: 1px solid var(--border-color);
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.form-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.form-select,
.form-control {
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--text-primary);
}

.form-select:focus,
.form-control:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.1);
}

.form-text {
    font-size: 12px;
    color: var(--text-secondary);
    line-height: 1.6;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition);
    white-space: nowrap;
}

.btn-primary {
    background-color: var(--jira-blue);
    color: white;
}

.btn-primary:hover {
    background-color: var(--jira-blue-dark);
    transform: translateY(-1px);
}

.btn-outline-secondary {
    border: 1px solid var(--border-color);
    background-color: transparent;
    color: var(--text-primary);
}

.btn-outline-secondary:hover {
    background-color: var(--bg-secondary);
    border-color: var(--text-secondary);
}

.dropdown-menu {
    border: 1px solid var(--border-color);
    border-radius: 6px;
    box-shadow: var(--shadow-lg);
}

.dropdown-item {
    padding: 8px 16px;
    font-size: 13px;
    color: var(--text-primary);
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: var(--bg-secondary);
}

.dropdown-item.text-danger {
    color: #AE2A19;
}

.dropdown-divider {
    margin: 4px 0;
    border-color: var(--border-color);
}

/* Responsive Design */
@media (max-width: 1199px) {
    .members-content-container {
        gap: 28px;
        padding: 28px 40px;
    }

    .members-sidebar {
        width: 260px;
    }
}

@media (max-width: 991px) {
    .members-content-container {
        flex-direction: column;
        gap: 28px;
        padding: 24px 32px;
    }

    .members-sidebar {
        width: 100%;
    }

    .members-sidebar-content {
        position: static;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .members-page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .btn-primary-action {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .members-page-header {
        padding: 20px 24px;
    }

    .members-page-title {
        font-size: 24px;
    }

    .members-content-container {
        padding: 20px 24px;
        gap: 20px;
    }

    .members-sidebar-content {
        grid-template-columns: 1fr;
    }

    .member-card-header {
        flex-wrap: wrap;
        padding: 12px;
    }

    .member-detail-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .detail-label {
        margin-bottom: 4px;
    }
}

@media (max-width: 480px) {
    .members-page-wrapper {
        min-height: calc(100vh - 56px);
    }

    .members-breadcrumb-section {
        padding: 0 16px;
    }

    .members-breadcrumb {
        font-size: 12px;
    }

    .members-page-header {
        padding: 16px;
    }

    .members-page-title {
        font-size: 20px;
    }

    .members-page-subtitle {
        font-size: 12px;
        display: block;
        margin-left: 0;
        margin-top: 4px;
    }

    .members-content-container {
        padding: 16px;
        gap: 16px;
    }

    .members-sidebar {
        width: 100%;
    }

    .members-sidebar-content {
        grid-template-columns: 1fr;
    }

    .member-card-body {
        padding: 12px;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .member-avatar,
    .member-avatar-placeholder {
        width: 48px;
        height: 48px;
        font-size: 18px;
    }

    .members-empty-state {
        padding: 40px 24px;
    }

    .empty-state-icon {
        font-size: 40px;
    }

    .empty-state-title {
        font-size: 16px;
    }

    .empty-state-description {
        font-size: 13px;
    }
}
</style>

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
