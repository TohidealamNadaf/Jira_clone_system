<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="backlog-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-section">
        <div class="breadcrumb">
            <a href="<?= url('/') ?>" class="breadcrumb-link">
                <i class="bi bi-house-door"></i> Home
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url('/projects') ?>" class="breadcrumb-link">Projects</a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
                <?= e($project['name']) ?>
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Backlog</span>
        </div>
    </div>

    <!-- Page Header -->
    <div class="page-header-section">
        <div class="header-content">
            <div>
                <h1 class="page-title">Backlog</h1>
                <p class="header-subtitle">
                    Project: <span class="project-key"><?= e($project['key']) ?></span> &bull;
                    <span class="project-name"><?= e($project['name']) ?></span>
                </p>
            </div>
            <?php if (can('issues.create', $project['id'])): ?>
                <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Create Issue
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="filters-card">
        <form method="GET" action="<?= url("/projects/{$project['key']}/backlog") ?>" class="filters-form">
            <div class="filter-row">
                <div class="filter-group">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" class="filter-input" name="search" value="<?= e($filters['search'] ?? '') ?>"
                            placeholder="Search backlog items...">
                    </div>
                </div>

                <div class="filter-group">
                    <select class="filter-select" name="status_id">
                        <option value="">All Statuses</option>
                        <?php foreach ($statuses ?? [] as $status): ?>
                            <option value="<?= $status['id'] ?>" <?= ($filters['status_id'] ?? '') == $status['id'] ? 'selected' : '' ?>>
                                <?= e($status['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <select class="filter-select" name="priority_id">
                        <option value="">All Priorities</option>
                        <?php foreach ($priorities ?? [] as $priority): ?>
                            <option value="<?= $priority['id'] ?>" <?= ($filters['priority_id'] ?? '') == $priority['id'] ? 'selected' : '' ?>>
                                <?= e($priority['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <select class="filter-select" name="assignee_id">
                        <option value="">All Assignees</option>
                        <option value="unassigned" <?= ($filters['assignee_id'] ?? '') === 'unassigned' ? 'selected' : '' ?>>Unassigned</option>
                        <?php foreach ($projectMembers ?? [] as $member): ?>
                            <option value="<?= $member['id'] ?>" <?= ($filters['assignee_id'] ?? '') == $member['id'] ? 'selected' : '' ?>>
                                <?= e($member['display_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-filter">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Backlog Content -->
    <?php if (empty($backlogIssues)): ?>
        <!-- Empty State -->
        <div class="empty-state-card">
            <div class="empty-state-content">
                <i class="bi bi-inbox empty-state-icon"></i>
                <h3 class="empty-state-title">No backlog items found</h3>
                <p class="empty-state-description">No items match your current filters.</p>
                <?php if (can('issues.create', $project['id'])): ?>
                    <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Create First Issue
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Backlog Table -->
        <div class="backlog-table-container">
            <table class="backlog-table">
                <thead>
                    <tr class="table-header-row">
                        <th class="col-key">Key</th>
                        <th class="col-summary">Summary</th>
                        <th class="col-type">Type</th>
                        <th class="col-status">Status</th>
                        <th class="col-priority">Priority</th>
                        <th class="col-assignee">Assignee</th>
                        <th class="col-created">Created</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backlogIssues as $issue): ?>
                        <tr class="table-body-row" onclick="window.location.href='<?= url("/issue/{$issue['issue_key']}") ?>'">
                            <td class="col-key">
                                <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="issue-key-link">
                                    <?= e($issue['issue_key']) ?>
                                </a>
                            </td>
                            <td class="col-summary">
                                <div class="summary-cell">
                                    <span class="type-icon" style="background-color: <?= e($issue['issue_type_color']) ?>">
                                        <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                                    </span>
                                    <span class="summary-text" title="<?= e($issue['summary']) ?>">
                                        <?= e($issue['summary']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="col-type">
                                <span class="badge-type"
                                    style="background-color: <?= e($issue['issue_type_color']) ?>20; color: <?= e($issue['issue_type_color']) ?>;">
                                    <?= e($issue['issue_type_name']) ?>
                                </span>
                            </td>
                            <td class="col-status">
                                <span class="badge-status"
                                    style="background-color: <?= e($issue['status_color']) ?>; color: white !important;">
                                    <?= e($issue['status_name']) ?>
                                </span>
                            </td>
                            <td class="col-priority">
                                <span class="badge-priority"
                                    style="background-color: <?= e($issue['priority_color']) ?>20; color: <?= e($issue['priority_color']) ?>;">
                                    <?= e($issue['priority_name']) ?>
                                </span>
                            </td>
                            <td class="col-assignee">
                                <?php if ($issue['assignee_name'] ?? null): ?>
                                    <div class="assignee-cell">
                                        <?php if ($issue['assignee_avatar'] ?? null): ?>
                                            <img src="<?= e(avatar($issue['assignee_avatar'])) ?>" class="avatar-image"
                                                alt="<?= e($issue['assignee_name']) ?>" title="<?= e($issue['assignee_name']) ?>">
                                        <?php else: ?>
                                            <div class="avatar-initial">
                                                <?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <span class="assignee-name"><?= e($issue['assignee_name']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-unassigned">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="col-created">
                                <span class="date-text">
                                    <?= time_ago($issue['created_at']) ?>
                                </span>
                            </td>
                            <td class="col-actions">
                                <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="action-link" title="View Issue">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
    /* ========================================
   Root Variables & Base Styles
   ======================================== */
    :root {
        /* Brand Colors */
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-blue-light: rgba(139, 25, 86, 0.1) !important;

        /* Text Colors */
        --text-primary: #161B22 !important;
        --text-secondary: #57606A !important;
        --text-tertiary: #738496 !important;
        --text-muted: #97A0AF !important;
        --text-white: #FFFFFF !important;

        /* Background Colors */
        --bg-primary: #FFFFFF !important;
        --bg-secondary: #F7F8FA !important;
        --bg-tertiary: #ECEDF0 !important;

        /* Borders & Dividers */
        --border-color: #DFE1E6 !important;
        --border-light: #EBECF0 !important;

        /* Transitions */
        --transition: 200ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ========================================
   Main Page Wrapper
   ======================================== */
    .backlog-page-wrapper {
        background: var(--bg-secondary);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ========================================
   Breadcrumb Navigation
   ======================================== */
    .breadcrumb-section {
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        padding: 8px 20px;
        flex-shrink: 0;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        padding: 0;
        font-size: 13px;
    }

    .breadcrumb-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
        transition: all var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--text-secondary);
        margin: 0 2px;
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* ========================================
   Page Header
   ======================================== */
    .page-header-section {
        background: var(--bg-primary);
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .header-content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 4px 0;
        letter-spacing: -0.3px;
    }

    .header-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    .project-key {
        font-weight: 600;
        color: var(--text-primary);
    }

    .project-name {
        color: var(--text-secondary);
    }

    .btn-primary {
        background: var(--jira-blue);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: var(--jira-blue-dark);
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.3);
        transform: translateY(-2px);
    }

    /* ========================================
   Filters Card
   ======================================== */
    .filters-card {
        background: var(--bg-primary);
        margin: 16px 20px;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .filters-form {
        display: flex;
        flex-direction: column;
    }

    .filter-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 150px;
    }

    /* Search Input */
    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        font-size: 14px;
    }

    .filter-input {
        width: 100%;
        padding: 8px 12px 8px 36px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 14px;
        color: var(--text-primary);
        background: var(--bg-primary);
        transition: all var(--transition);
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.15);
    }

    .filter-input::placeholder {
        color: var(--text-muted);
    }

    /* Select Dropdowns */
    .filter-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 14px;
        color: var(--text-primary);
        background: var(--bg-primary);
        cursor: pointer;
        transition: all var(--transition);
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23626F86' stroke-width='2'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 20px;
        padding-right: 32px;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.15);
    }

    .filter-select option {
        color: var(--text-primary);
        background: var(--bg-primary);
    }

    /* Filter Button */
    .btn-filter {
        padding: 8px 16px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        transition: all var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .btn-filter:hover {
        background: var(--bg-tertiary);
        border-color: #B6C2CF;
    }

    /* ========================================
   Empty State
   ======================================== */
    .empty-state-card {
        background: var(--bg-primary);
        margin: 16px 20px;
        padding: 40px 24px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        text-align: center;
    }

    .empty-state-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }

    .empty-state-icon {
        font-size: 48px;
        color: var(--text-muted);
    }

    .empty-state-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .empty-state-description {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0 0 12px 0;
    }

    /* ========================================
   Backlog Table
   ======================================== */
    .backlog-table-container {
        background: var(--bg-primary);
        margin: 16px 20px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .backlog-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .table-header-row {
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .table-header-row th {
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
    }

    .table-body-row {
        border-bottom: 1px solid var(--border-color);
        transition: all var(--transition);
        cursor: pointer;
    }

    .table-body-row:hover {
        background: var(--bg-secondary);
    }

    .backlog-table td {
        padding: 12px;
        font-size: 13px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    /* Column Sizing */
    .col-key {
        width: 80px;
    }

    .col-summary {
        width: auto;
    }

    .col-type {
        width: 100px;
    }

    .col-status {
        width: 100px;
    }

    .col-priority {
        width: 100px;
    }

    .col-assignee {
        width: 120px;
    }

    .col-created {
        width: 100px;
    }

    .col-actions {
        width: 60px;
    }

    /* Key Link */
    .issue-key-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 600;
        transition: color var(--transition);
    }

    .issue-key-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    /* Summary Cell */
    .summary-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .type-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 4px;
        color: white;
        font-size: 14px;
        flex-shrink: 0;
    }

    .summary-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 400px;
    }

    /* Badges */
    .badge-type,
    .badge-status,
    .badge-priority {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    /* Assignee */
    .assignee-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .avatar-image {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .avatar-initial {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--jira-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .assignee-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 13px;
    }

    .text-unassigned {
        color: var(--text-muted);
        font-size: 13px;
        font-style: italic;
    }

    /* Date Text */
    .date-text {
        color: var(--text-secondary);
        font-size: 13px;
        white-space: nowrap;
    }

    /* Actions */
    .action-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-size: 16px;
        display: inline-block;
        padding: 4px;
        transition: all var(--transition);
    }

    .action-link:hover {
        color: var(--jira-blue-dark);
        transform: scale(1.15);
    }

    /* ========================================
   Responsive Design
   ======================================== */

    /* Tablet (576px - 1024px) */
    @media (max-width: 1024px) {
        .header-content {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary {
            width: fit-content;
        }

        .filter-row {
            gap: 8px;
        }

        .filter-group {
            min-width: 120px;
        }

        .col-summary {
            width: auto;
        }

        .col-type {
            width: 80px;
        }

        .col-status {
            width: 80px;
        }

        .col-priority {
            width: 80px;
        }

        .col-assignee {
            width: 100px;
        }

        .col-created {
            width: 90px;
        }

        .backlog-table td {
            padding: 12px;
            font-size: 13px;
        }

        .table-header-row th {
            padding: 10px 12px;
            font-size: 11px;
        }
    }

    /* Mobile (< 576px) */
    @media (max-width: 576px) {

        .breadcrumb-section,
        .page-header-section,
        .filters-card,
        .empty-state-card,
        .backlog-table-container {
            margin: 8px 12px;
            padding: 12px;
            border-radius: 6px;
        }

        .page-title {
            font-size: 20px;
        }

        .header-subtitle {
            font-size: 12px;
        }

        .breadcrumb {
            font-size: 12px;
            gap: 4px;
        }

        .filter-row {
            flex-direction: column;
            gap: 12px;
        }

        .filter-group {
            width: 100%;
            min-width: unset;
        }

        .btn-filter {
            width: 100%;
            justify-content: center;
        }

        /* Hide columns on mobile */
        .col-type,
        .col-created,
        .col-actions,
        .table-header-row th:nth-child(n+3),
        .table-body-row td:nth-child(n+3) {
            display: none;
        }

        .backlog-table-container {
            overflow-x: auto;
        }

        .backlog-table {
            min-width: 300px;
        }

        .col-key {
            width: 60px;
        }

        .col-summary {
            width: auto;
        }

        .col-status {
            width: 80px;
        }

        .col-priority {
            width: 80px;
        }

        .col-assignee {
            width: 100px;
        }

        .backlog-table td {
            padding: 12px 8px;
            font-size: 12px;
        }

        .table-header-row th {
            padding: 8px;
            font-size: 10px;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>