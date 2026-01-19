<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('styles'); ?>
<style>
    /* ============================================
    ENTERPRISE COMPACT LIST DESIGN
    ============================================ */

    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-dark: #161B22 !important;
        --jira-gray: #626F86 !important;
        --jira-light: #F7F8FA !important;
        --jira-border: #DFE1E6 !important;
        --transition: 0.1s ease-in-out !important;
    }

    /* LAYOUT */
    .page-wrapper {
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 60px);
        background: var(--jira-light);
    }

    /* BREADCRUMB */
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        font-size: 13px;
    }

    .breadcrumb-link {
        color: var(--jira-gray);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue);
        text-decoration: underline;
    }

    .breadcrumb-current {
        color: var(--jira-dark);
        font-weight: 500;
    }

    /* HEADER */
    .page-header {
        padding: 24px 32px;
        background: #FFFFFF;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--jira-border);
    }

    .page-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--jira-dark);
        margin: 0 0 4px 0;
    }

    .header-description {
        font-size: 13px;
        color: var(--jira-gray);
        margin: 0;
    }

    .board-create-btn {
        background-color: #8B1956;
        /* Fallback */
        background-color: var(--jira-blue);
        color: #ffffff !important;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.1s;
        border: none;
        white-space: nowrap;
    }

    .board-create-btn:hover {
        background-color: #6F123F;
        /* Fallback */
        background-color: var(--jira-blue-dark);
        color: #ffffff !important;
        text-decoration: none;
    }

    /* CONTENT AREA */
    .page-content {
        display: flex;
        gap: 24px;
        padding: 24px 32px;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
        align-items: flex-start;
    }

    .content-left {
        flex: 1;
        min-width: 0;
    }

    .content-right {
        width: 260px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* COMPACT LIST TABLE */
    .boards-list-container {
        background: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 3px;
        /* overflow: hidden; - Removed to allow dropdowns to overflow */
    }

    .list-header {
        display: flex;
        padding: 10px 16px;
        background: var(--jira-light);
        border-bottom: 1px solid var(--jira-border);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--jira-gray);
        letter-spacing: 0.5px;
    }

    .list-row {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid var(--jira-border);
        background: #FFFFFF;
        transition: background 0.1s;
    }

    .list-row:last-child {
        border-bottom: none;
    }

    .list-row:hover {
        background: #fafbfc;
    }

    /* COLUMNS */
    .col-name {
        flex: 1;
        display: flex;
        align-items: center;
        min-width: 200px;
    }

    .col-type {
        width: 120px;
    }

    .col-stats {
        width: 80px;
        text-align: right;
        padding-right: 16px;
    }

    .col-actions {
        width: 60px;
        text-align: right;
    }

    /* CONTENT STYLES */
    .board-link-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: inherit;
        width: 100%;
    }

    .board-icon-small {
        width: 24px;
        height: 24px;
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .board-icon-small.scrum {
        background: rgba(139, 25, 86, 0.1);
        color: var(--jira-blue);
    }

    .board-icon-small.kanban {
        background: #E6FAEC;
        color: #2ea44f;
    }

    .board-details {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .board-name {
        font-size: 14px;
        font-weight: 500;
        color: var(--jira-blue);
    }

    .board-link-wrapper:hover .board-name {
        text-decoration: underline;
        color: var(--jira-blue-dark);
    }

    .board-desc-truncate {
        font-size: 12px;
        color: var(--jira-gray);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
    }

    .badge-type {
        display: inline-block;
        padding: 2px 8px;
        font-size: 11px;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-type.scrum {
        background: rgba(139, 25, 86, 0.1);
        color: var(--jira-blue);
    }

    .badge-type.kanban {
        background: #E6FAEC;
        color: #2ea44f;
    }

    .stat-pill {
        background: var(--jira-light);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        color: var(--jira-gray);
        font-weight: 600;
    }

    .action-icon-btn {
        background: transparent;
        border: none;
        color: var(--jira-gray);
        padding: 4px;
        border-radius: 3px;
        cursor: pointer;
    }

    .action-icon-btn:hover {
        background: var(--jira-light);
        color: var(--jira-dark);
    }

    /* SIDEBAR WIDGETS */
    .sidebar-widget {
        margin-bottom: 20px;
    }

    .widget-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--jira-gray);
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .project-brief {
        background: #FFFFFF;
        border: 1px solid var(--jira-border);
        padding: 12px;
        border-radius: 3px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .badge-key {
        background: var(--jira-light);
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        color: var(--jira-gray);
    }

    .widget-text {
        font-size: 12px;
        color: var(--jira-gray);
        line-height: 1.5;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .page-content {
            flex-direction: column;
            padding: 16px;
        }

        .content-right {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .col-stats,
        .col-type {
            display: none;
        }

        .board-desc-truncate {
            display: none;
        }
    }
</style>
<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper main-content-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-folder"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/projects/' . $project['key']) ?>" class="breadcrumb-link">
            <?= e($project['key']) ?>
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Boards</span>
    </div>

    <!-- Page Header Section -->
    <div class="page-header">
        <div class="header-left">
            <div class="header-info">
                <h1 class="page-title">Boards</h1>
                <p class="header-description">
                    All boards in the <strong><?= e($project['name']) ?></strong> project.
                </p>
            </div>
        </div>
        <div class="header-actions">
            <?php if (can('manage-boards', $project['id'])): ?>
                <a href="<?= url('/projects/' . $project['key'] . '/boards/create') ?>" class="board-create-btn">
                    <i class="bi bi-plus-lg"></i>
                    <span>Create Board</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="page-content">
        <!-- Left Column (Main Content) -->
        <div class="content-left">

            <?php if (empty($boards)): ?>
                <!-- Empty State -->
                <div class="empty-state-compact">
                    <i class="bi bi-kanban"></i>
                    <div>
                        <strong>No boards found.</strong>
                        <span class="text-muted">Create a board to get started.</span>
                    </div>
                </div>
            <?php else: ?>
                <!-- Boards List Table -->
                <div class="boards-list-container">
                    <div class="list-header">
                        <div class="col-name">Name</div>
                        <div class="col-type">Type</div>
                        <div class="col-stats">Issues</div>
                        <div class="col-actions">Actions</div>
                    </div>

                    <div class="list-body">
                        <?php foreach ($boards as $board): ?>
                            <div class="list-row">
                                <!-- Name Column -->
                                <div class="col-name">
                                    <a href="<?= url('/boards/' . $board['id']) ?>" class="board-link-wrapper">
                                        <div class="board-icon-small <?= $board['type'] ?>">
                                            <i
                                                class="bi bi-<?= $board['type'] === 'scrum' ? 'lightning-charge' : 'kanban' ?>"></i>
                                        </div>
                                        <div class="board-details">
                                            <span class="board-name"><?= e($board['name']) ?></span>
                                            <?php if (!empty($board['description'])): ?>
                                                <span class="board-desc-truncate"><?= e($board['description']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </div>

                                <!-- Type Column -->
                                <div class="col-type">
                                    <span class="badge-type <?= $board['type'] ?>">
                                        <?= ucfirst($board['type']) ?>
                                    </span>
                                </div>

                                <!-- Stats Column -->
                                <div class="col-stats">
                                    <span class="stat-pill" title="Total Issues">
                                        <?= $board['issue_count'] ?? 0 ?>
                                    </span>
                                </div>

                                <!-- Actions Column -->
                                <div class="col-actions">
                                    <div class="dropdown">
                                        <button class="action-icon-btn" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <a class="dropdown-item" href="<?= url('/boards/' . $board['id']) ?>">
                                                    <i class="bi bi-box-arrow-up-right me-2 text-muted"></i> Open Board
                                                </a>
                                            </li>
                                            <?php if (can('manage-boards', $project['id'])): ?>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="<?= url('/boards/' . $board['id'] . '/settings') ?>">
                                                        <i class="bi bi-gear me-2 text-muted"></i> Configure
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="<?= url('/boards/' . $board['id']) ?>" method="POST"
                                                        onsubmit="return confirm('Delete this board?')">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Right Column (Sidebar) -->
        <div class="content-right">
            <!-- Project Brief -->
            <div class="sidebar-widget">
                <h4 class="widget-title">Project</h4>
                <div class="project-brief">
                    <strong><?= e($project['name']) ?></strong>
                    <span class="badge-key"><?= e($project['key']) ?></span>
                </div>
            </div>

            <div class="sidebar-widget">
                <h4 class="widget-title">About Boards</h4>
                <p class="widget-text">
                    Boards visualize your workflow.
                    Use <strong>Kanban</strong> for continuous flow or <strong>Scrum</strong> for iterations.
                </p>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>