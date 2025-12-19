<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="board-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav">
        <a href="<?= url('/dashboard') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="breadcrumb-sep">/</span>
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">Projects</a>
        <span class="breadcrumb-sep">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Board</span>
    </nav>

    <!-- Project Navigation Tabs -->
    <div class="project-nav-tabs">
        <a href="<?= url("/projects/{$project['key']}/board") ?>" class="nav-tab active">
            <i class="bi bi-kanban"></i>
            <span>Board</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/issues") ?>" class="nav-tab">
            <i class="bi bi-list-ul"></i>
            <span>Issues</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/backlog") ?>" class="nav-tab">
            <i class="bi bi-inbox"></i>
            <span>Backlog</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/sprints") ?>" class="nav-tab">
            <i class="bi bi-lightning-charge"></i>
            <span>Sprints</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/reports") ?>" class="nav-tab">
            <i class="bi bi-bar-chart"></i>
            <span>Reports</span>
        </a>
        <a href="<?= url("/time-tracking/project/{$project['id']}") ?>" class="nav-tab">
            <i class="bi bi-hourglass-split"></i>
            <span>Time Tracking</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/calendar") ?>" class="nav-tab">
            <i class="bi bi-calendar-event"></i>
            <span>Calendar</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/roadmap") ?>" class="nav-tab">
            <i class="bi bi-signpost-2"></i>
            <span>Roadmap</span>
        </a>
    </div>

    <!-- Board Toolbar -->
    <div class="board-toolbar">
        <div class="toolbar-left">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="boardSearch" placeholder="Search board" class="search-input">
            </div>
            <button class="toolbar-btn" id="filterBtn" title="Filter issues">
                <i class="bi bi-funnel"></i>
                <span>Filter</span>
            </button>
        </div>
        <div class="toolbar-right">
            <button class="toolbar-btn" id="groupBtn" title="Group by">
                <i class="bi bi-collection"></i>
                <span>Group</span>
            </button>
            <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="toolbar-btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                <span>Create</span>
            </a>
            <button class="toolbar-btn menu-btn" title="More options">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
        </div>
    </div>

    <!-- Main Board Content -->
    <div class="board-container">
        <!-- Kanban Board -->
        <div class="kanban-board">
            <?php foreach ($statuses as $status): ?>
                <?php 
                    $statusIssues = array_filter($issues, function($issue) use ($status) {
                        return $issue['status_id'] == $status['id'];
                    });
                    $statusIssues = array_values($statusIssues);
                ?>
                <div class="kanban-column" data-status-id="<?= e($status['id']) ?>" data-status-name="<?= e($status['name']) ?>">
                    <!-- Column Header -->
                    <div class="column-header">
                        <div class="column-title-group">
                            <h3 class="column-title"><?= e($status['name']) ?></h3>
                            <span class="column-badge"><?= count($statusIssues) ?></span>
                        </div>
                    </div>

                    <!-- Column Content (Droppable) -->
                    <div class="column-cards board-column" data-status-id="<?= e($status['id']) ?>">
                        <?php if (empty($statusIssues)): ?>
                            <!-- Empty State -->
                            <div class="empty-state">
                                <i class="empty-icon bi bi-inbox"></i>
                                <p class="empty-text">No issues</p>
                            </div>
                        <?php else: ?>
                            <!-- Issue Cards -->
                            <?php foreach ($statusIssues as $issue): ?>
                            <div class="issue-card"
                                 draggable="true"
                                 data-issue-id="<?= e($issue['id']) ?>"
                                 data-issue-key="<?= e($issue['issue_key']) ?>"
                                 data-status-id="<?= e($status['id']) ?>"
                                 data-priority="<?= e($issue['priority_name'] ?? 'Medium') ?>"
                                 data-assignee="<?= e($issue['assignee_name'] ?? 'Unassigned') ?>"
                                 role="button"
                                 tabindex="0">
                                
                                <!-- Card Priority Bar (Left) -->
                                <div class="card-priority-bar" style="background-color: <?= e($issue['priority_color'] ?? '#DFE1E6') ?>"></div>

                                <!-- Card Body -->
                                <div class="card-body">
                                    <!-- Issue Key -->
                                    <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="card-key">
                                        <?= e($issue['issue_key']) ?>
                                    </a>

                                    <!-- Issue Summary -->
                                    <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="card-summary">
                                        <?= e($issue['summary']) ?>
                                    </a>

                                    <!-- Card Footer: Type Badge + Due Date + Assignee -->
                                    <div class="card-footer">
                                        <div class="footer-left">
                                            <span class="issue-type-label" 
                                                  style="background-color: <?= e($issue['issue_type_color'] ?? '#8B1956') ?>;"
                                                  title="<?= e($issue['issue_type_name'] ?? 'Task') ?>">
                                                <i class="bi bi-<?= e($issue['issue_type_icon'] ?? 'list-check') ?>"></i>
                                                <span><?= e($issue['issue_type_name'] ?? 'Task') ?></span>
                                            </span>
                                            <?php if (!empty($issue['due_date'])): ?>
                                            <span class="due-date" title="Due: <?= e($issue['due_date']) ?>">
                                                <i class="bi bi-calendar"></i>
                                                <?= date('M d', strtotime($issue['due_date'])) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="footer-right">
                                            <?php if ($issue['assignee_name']): ?>
                                                <?php if (!empty($issue['assignee_avatar']) && is_file($_SERVER['DOCUMENT_ROOT'] . $issue['assignee_avatar'])): ?>
                                                <img src="<?= e($issue['assignee_avatar']) ?>" 
                                                     alt="<?= e($issue['assignee_name']) ?>"
                                                     class="avatar-sm"
                                                     title="<?= e($issue['assignee_name']) ?>">
                                                <?php else: ?>
                                                <div class="avatar-sm avatar-initials" 
                                                     title="<?= e($issue['assignee_name']) ?>">
                                                    <?= e(strtoupper(substr($issue['assignee_name'], 0, 1))) ?>
                                                </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                            <div class="avatar-sm unassigned" title="Unassigned"></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Add Card Button -->
                    <button class="add-card-btn" data-status-id="<?= e($status['id']) ?>">
                        <i class="bi bi-plus"></i> Add issue
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
/* ============================================
   KANBAN BOARD - ENTERPRISE REDESIGN
   Compact & Clean - All Content Visible
   ============================================ */

:root {
    --jira-blue: #8B1956 !important;
    --jira-blue-dark: #6F123F !important;
    --jira-dark: #161B22 !important;
    --jira-gray: #626F86 !important;
    --jira-light: #F7F8FA !important;
    --jira-border: #DFE1E6 !important;
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
}

* { box-sizing: border-box; }

.board-page-wrapper {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 60px);
    background: var(--jira-light);
    overflow: hidden;
}

/* ============================================
   BREADCRUMB
   ============================================ */

.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 12px 20px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    font-size: 12px;
    flex-shrink: 0;
    overflow-x: auto;
    white-space: nowrap;
    min-height: 30px;
}

.breadcrumb-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: var(--jira-blue);
    text-decoration: none;
    font-weight: 500;
    transition: all var(--transition);
}

.breadcrumb-link:hover {
    color: var(--jira-blue-dark);
}

.breadcrumb-sep {
    color: var(--jira-gray);
    opacity: 0.5;
    margin: 0 2px;
}

.breadcrumb-current {
    color: var(--jira-dark);
    font-weight: 500;
}

/* ============================================
   BOARD TOOLBAR
   ============================================ */

.board-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    box-shadow: var(--shadow-sm);
    flex-shrink: 0;
    gap: 12px;
}

.toolbar-left,
.toolbar-right {
    display: flex;
    align-items: center;
    gap: 8px;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    background: var(--jira-light);
    border: 1px solid var(--jira-border);
    border-radius: 4px;
    height: 32px;
}

.search-input {
    border: none;
    background: transparent;
    font-size: 12px;
    color: var(--jira-dark);
    width: 140px;
    outline: none;
}

.search-input::placeholder {
    color: var(--jira-gray);
}

.search-box i {
    color: var(--jira-gray);
    font-size: 13px;
}

.toolbar-btn {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: #FFFFFF;
    border: 1px solid var(--jira-border);
    border-radius: 4px;
    color: var(--jira-dark);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition);
    white-space: nowrap;
}

.toolbar-btn:hover {
    border-color: #B6C2CF;
    background: var(--jira-light);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.toolbar-btn.btn-primary {
    background: var(--jira-blue);
    border-color: var(--jira-blue);
    color: #FFFFFF;
}

.toolbar-btn.btn-primary:hover {
    background: var(--jira-blue-dark);
    border-color: var(--jira-blue-dark);
}

.toolbar-btn i {
    font-size: 13px;
}

.toolbar-btn span {
    display: inline;
}

.menu-btn {
    padding: 6px 8px !important;
}

.menu-btn span {
    display: none;
}

/* ============================================
    BOARD MENU DROPDOWN
    ============================================ */

.board-menu-dropdown {
    position: fixed;
    background: #FFFFFF;
    border: 1px solid var(--jira-border);
    border-radius: 4px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    min-width: 200px;
    z-index: 1000;
    animation: slideDown 0.15s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.board-menu-dropdown .menu-item {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 8px 12px;
    background: transparent;
    border: none;
    color: var(--jira-dark);
    font-size: 13px;
    cursor: pointer;
    transition: all var(--transition);
    text-align: left;
    white-space: nowrap;
}

.board-menu-dropdown .menu-item:hover {
    background: var(--jira-light);
    color: var(--jira-blue);
}

.board-menu-dropdown .menu-item i {
    font-size: 13px;
    color: var(--jira-gray);
}

.board-menu-dropdown .menu-item:hover i {
    color: var(--jira-blue);
}

/* ============================================
    BOARD CONTAINER & KANBAN
    ============================================ */

.board-container {
    flex: 1;
    overflow: hidden;
    display: flex;
    padding: 12px 20px;
    background: var(--jira-light);
    min-height: 0;
    min-width: 0;
}

.kanban-board {
    display: flex;
    gap: 12px;
    flex: 1;
    overflow-x: auto;
    overflow-y: hidden;
    padding-bottom: 4px;
    min-width: 0;
    align-items: flex-start;
}

.kanban-board::-webkit-scrollbar {
    height: 6px;
}

.kanban-board::-webkit-scrollbar-track {
    background: transparent;
}

.kanban-board::-webkit-scrollbar-thumb {
    background: #D0D7DE;
    border-radius: 3px;
}

.kanban-board::-webkit-scrollbar-thumb:hover {
    background: #B0B7BE;
}

/* ============================================
   KANBAN COLUMN
   ============================================ */

.kanban-column {
    display: flex;
    flex-direction: column;
    flex: 0 0 340px;
    background: #F7F8FA;
    border-radius: 6px;
    border: 1px solid var(--jira-border);
    overflow: hidden;
    transition: all var(--transition);
    max-height: 100%;
    min-height: 0;
}

.kanban-column:hover {
    border-color: #B6C2CF;
}

.column-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    flex-shrink: 0;
}

.column-title-group {
    display: flex;
    align-items: center;
    gap: 6px;
}

.column-title {
    margin: 0;
    font-size: 13px;
    font-weight: 700;
    color: var(--jira-dark);
    letter-spacing: -0.2px;
}

.column-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 6px;
    background: #DFE1E6;
    color: var(--jira-dark);
    font-size: 11px;
    font-weight: 600;
    border-radius: 12px;
}

/* ============================================
    COLUMN CONTENT
    ============================================ */

.column-cards {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 8px;
    gap: 8px;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    transition: all var(--transition);
    min-height: 0;
    scroll-behavior: smooth;
}

.column-cards::-webkit-scrollbar {
    width: 6px;
}

.column-cards::-webkit-scrollbar-track {
    background: transparent;
}

.column-cards::-webkit-scrollbar-thumb {
    background: #D0D7DE;
    border-radius: 3px;
}

.column-cards::-webkit-scrollbar-thumb:hover {
    background: #B0B7BE;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    gap: 8px;
    color: var(--jira-gray);
    text-align: center;
    font-size: 12px;
    min-height: 100px;
}

.empty-icon {
    font-size: 24px;
    opacity: 0.4;
}

.empty-text {
    margin: 0;
    font-size: 12px;
    opacity: 0.7;
}

/* ============================================
    ISSUE CARD - Clean & Compact
    All Content Visible
    ============================================ */

.issue-card {
    background: #FFFFFF;
    border: 1px solid var(--jira-border);
    border-radius: 4px;
    padding: 0;
    cursor: grab;
    transition: all var(--transition);
    overflow: hidden;
    position: relative;
    display: flex;
    flex-direction: row;
    align-items: stretch;
    user-select: none;
    flex-shrink: 0;
    min-height: 88px;
    max-height: 120px;
}

.issue-card:hover {
    border-color: #B6C2CF;
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.issue-card.dragging {
    opacity: 0.5;
    cursor: grabbing;
    box-shadow: var(--shadow-md);
}

/* Priority bar on left */
.card-priority-bar {
    width: 4px;
    height: auto;
    flex-shrink: 0;
    transition: all var(--transition);
}

.issue-card:hover .card-priority-bar {
    width: 5px;
}

/* Main card body */
.card-body {
    padding: 10px 11px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
    min-width: 0;
    justify-content: space-between;
}

/* Issue key - first line */
.card-key {
    font-size: 12px;
    font-weight: 700;
    color: var(--jira-blue);
    text-decoration: none;
    transition: all var(--transition);
    line-height: 1;
    display: block;
    margin: 0;
}

.card-key:hover {
    color: var(--jira-blue-dark);
}

/* Issue summary - truncated to 2 lines max */
.card-summary {
    font-size: 13px;
    font-weight: 500;
    color: var(--jira-dark);
    text-decoration: none;
    line-height: 1.3;
    transition: all var(--transition);
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    min-height: 26px;
    max-height: 32px;
}

.card-summary:hover {
    color: var(--jira-blue);
}

/* Card footer - all visible, compact */
.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 6px;
    padding-top: 7px;
    border-top: 1px solid var(--jira-border);
    min-height: 28px;
}

/* Footer left section: type label + due date */
.footer-left {
    display: flex;
    align-items: center;
    gap: 5px;
    min-width: 0;
    flex: 1;
}

/* Issue type label with icon */
.issue-type-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 3px 7px;
    border-radius: 3px;
    color: #FFFFFF;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
    transition: all var(--transition);
}

.issue-type-label i {
    font-size: 11px;
}

.issue-type-label span {
    display: inline;
}

.issue-type-label:hover {
    transform: scale(1.05);
}

/* Due date */
.due-date {
    display: flex;
    align-items: center;
    gap: 3px;
    color: var(--jira-gray);
    white-space: nowrap;
    font-size: 11px;
    transition: all var(--transition);
    flex-shrink: 0;
}

.due-date i {
    font-size: 11px;
}

.due-date:hover {
    color: var(--jira-dark);
}

/* Footer right: assignee avatar */
.footer-right {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    flex-shrink: 0;
}

/* Avatar styling */
.avatar-sm {
    width: 24px;
    height: 24px;
    border-radius: 3px;
    object-fit: cover;
    border: 1px solid var(--jira-border);
    transition: all var(--transition);
    flex-shrink: 0;
}

.avatar-sm:hover {
    transform: scale(1.15);
    box-shadow: var(--shadow-md);
}

.avatar-sm.avatar-initials {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--jira-blue), var(--jira-blue-dark));
    color: #FFFFFF;
    font-size: 10px;
    font-weight: 700;
}

.avatar-sm.unassigned {
    background: var(--jira-light);
    border-color: var(--jira-border);
}

/* ============================================
   ADD CARD BUTTON
   ============================================ */

.add-card-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 8px;
    margin: 0 8px 8px;
    background: transparent;
    border: 1px dashed var(--jira-border);
    border-radius: 4px;
    color: var(--jira-gray);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition);
    flex-shrink: 0;
}

.add-card-btn:hover {
    border-color: var(--jira-blue);
    color: var(--jira-blue);
    background: rgba(139, 25, 86, 0.05);
}

/* ============================================
   DRAG & DROP STATES
   ============================================ */

.column-cards.drag-over {
    background: rgba(139, 25, 86, 0.08);
    border-color: var(--jira-blue);
}

@keyframes cardSlideIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.issue-card {
    animation: cardSlideIn 0.2s ease-out;
}

/* ============================================
   RESPONSIVE DESIGN
   ============================================ */

@media (max-width: 1024px) {
    .toolbar-btn span {
        display: none;
    }

    .search-input {
        width: 120px;
    }

    .kanban-column {
        flex: 0 0 320px;
    }
}

@media (max-width: 768px) {
    .board-page-wrapper {
        height: calc(100vh - 56px);
    }

    .board-toolbar {
        flex-wrap: wrap;
        padding: 8px 12px;
    }

    .toolbar-left {
        flex: 1;
        min-width: 0;
    }

    .toolbar-right {
        flex: 1;
    }

    .search-box {
        flex: 1;
        min-width: 100px;
    }

    .search-input {
        width: 100%;
    }

    .board-container {
        padding: 8px 12px;
    }

    .kanban-board {
        gap: 8px;
    }

    .kanban-column {
        flex: 0 0 300px;
    }

    .column-header {
        padding: 10px;
    }

    .column-title {
        font-size: 12px;
    }

    .card-body {
        padding: 10px 11px;
        gap: 6px;
    }

    .card-key {
        font-size: 11px;
    }

    .card-summary {
        font-size: 12px;
    }

    .card-footer {
        font-size: 10px;
        padding-top: 6px;
        min-height: 26px;
    }

    .issue-type-label {
        font-size: 10px;
        padding: 3px 6px;
        gap: 3px;
    }

    .issue-type-label i {
        font-size: 10px;
    }

    .avatar-sm {
        width: 22px;
        height: 22px;
    }
}

/* ============================================
    MENU DROPDOWNS - Group & More Options
    ============================================ */

.board-menu-dropdown {
    position: fixed;
    background: #FFFFFF;
    border: 1px solid var(--jira-border);
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    min-width: 180px;
    overflow: hidden;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 10px 12px;
    border: none;
    background: transparent;
    color: var(--jira-dark);
    font-size: 13px;
    cursor: pointer;
    transition: all var(--transition);
    text-align: left;
    white-space: nowrap;
}

.menu-item:hover {
    background: var(--jira-light);
    color: var(--jira-blue);
}

.menu-item i {
    font-size: 14px;
    flex-shrink: 0;
}

.board-menu-dropdown hr {
    border: none;
    border-top: 1px solid var(--jira-border);
    margin: 0;
}

@media (max-width: 480px) {
    .breadcrumb-nav {
        padding: 6px 12px;
        font-size: 10px;
        gap: 2px;
    }

    .breadcrumb-sep {
        margin: 0 1px;
    }

    .board-toolbar {
        padding: 6px;
        gap: 4px;
    }

    .toolbar-btn {
        padding: 4px 8px;
        font-size: 11px;
    }

    .search-box {
        height: 28px;
        padding: 4px 6px;
    }

    .search-input {
        font-size: 11px;
        width: 80px;
    }

    .board-container {
        padding: 6px;
    }

    .kanban-board {
        gap: 6px;
    }

    .kanban-column {
        flex: 0 0 280px;
    }

    .column-header {
        padding: 8px;
    }

    .column-title {
        font-size: 11px;
    }

    .column-badge {
        min-width: 20px;
        height: 20px;
        font-size: 10px;
    }

    .card-body {
        padding: 9px 10px;
        gap: 5px;
    }

    .card-key {
        font-size: 10px;
    }

    .card-summary {
        font-size: 11px;
        min-height: 24px;
    }

    .card-footer {
        font-size: 9px;
        padding-top: 5px;
        min-height: 24px;
        gap: 4px;
    }

    .issue-type-label {
        font-size: 9px;
        padding: 2px 5px;
        gap: 2px;
    }

    .issue-type-label i {
        font-size: 9px;
    }

    .avatar-sm {
        width: 20px;
        height: 20px;
    }

    .add-card-btn {
        padding: 6px;
        margin: 0 6px 6px;
        font-size: 11px;
    }
}
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
/**
 * ENHANCED KANBAN BOARD - DRAG & DROP v6
 * Production-Ready with Optimized UX
 * Features:
 *  - Smooth drag & drop with enhanced visual feedback
 *  - Real-time API synchronization
 *  - Optimistic UI updates with rollback
 *  - Comprehensive error handling
 *  - Search functionality
 *  - Performance optimized
 */

const PROJECT_KEY = '<?= e($project['key']) ?>';
let draggedCard = null;
let originalColumn = null;
let originalParent = null;
let dragStartTime = 0;
let statusesData = {}; // Cache statuses for grouping

/**
 * Initialize board on DOM ready
 */
function initializeBoard() {
    console.log('[📊 BOARD] Initialization started');
    
    const columns = document.querySelectorAll('.board-column');
    const cards = document.querySelectorAll('.issue-card');
    
    console.log(`✓ Found ${columns.length} columns and ${cards.length} cards`);
    
    if (columns.length === 0) {
        console.warn('[BOARD] Retrying initialization...');
        setTimeout(initializeBoard, 500);
        return;
    }
    
    // Cache statuses for grouping
    document.querySelectorAll('.kanban-column').forEach(col => {
        const statusId = col.dataset.statusId;
        const statusName = col.dataset.statusName;
        statusesData[statusId] = statusName;
    });

    setupDragAndDrop();
    setupSearch();
    setupAddCardButtons();
    setupGroupButton();
    setupMoreMenu();
    
    console.log('✓ Board ready for drag & drop');
}

/**
 * Setup drag and drop functionality
 */
function setupDragAndDrop() {
    document.querySelectorAll('.issue-card').forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });

    document.querySelectorAll('.column-cards').forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('dragleave', handleDragLeave);
        column.addEventListener('drop', handleDrop);
    });
}

/**
 * Handle drag start
 */
function handleDragStart(e) {
    draggedCard = this;
    originalColumn = this.closest('.kanban-column');
    originalParent = this.parentElement;
    dragStartTime = Date.now();
    
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);
    
    console.log(`✓ Drag started: ${this.dataset.issueKey}`);
    
    // Dim other columns
    document.querySelectorAll('.kanban-column').forEach(col => {
        if (col !== originalColumn) {
            col.style.opacity = '0.6';
        }
    });
}

/**
 * Handle drag end
 */
function handleDragEnd(e) {
    this.classList.remove('dragging');
    
    // Reset column appearance
    document.querySelectorAll('.kanban-column').forEach(col => {
        col.style.opacity = '1';
        col.querySelector('.column-cards')?.classList.remove('drag-over');
    });
    
    console.log(`✓ Drag ended after ${Date.now() - dragStartTime}ms`);
}

/**
 * Handle drag over
 */
function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    this.classList.add('drag-over');
}

/**
 * Handle drag leave
 */
function handleDragLeave(e) {
    if (e.target === this) {
        this.classList.remove('drag-over');
    }
}

/**
 * Handle drop - core functionality
 */
async function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    
    this.classList.remove('drag-over');

    if (!draggedCard) return;

    const targetColumn = this.closest('.kanban-column');
    if (!targetColumn) return;

    const targetStatusId = targetColumn.dataset.statusId;
    const currentStatusId = originalColumn.dataset.statusId;
    const issueKey = draggedCard.dataset.issueKey;

    if (targetStatusId === currentStatusId) {
        console.log('[DROP] Card already in column');
        return;
    }

    const originalHTML = draggedCard.cloneNode(true);
    const cardElement = draggedCard;

    // Optimistic update - move card immediately
    this.appendChild(cardElement);
    updateColumnCounts();
    
    console.log(`📡 API Call: Moving ${issueKey} to status ${targetStatusId}`);

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const response = await fetch(`<?= url('/api/v1/issues/') ?>${issueKey}/transitions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken || '',
            },
            body: JSON.stringify({ status_id: parseInt(targetStatusId) }),
            credentials: 'include'
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Unknown error');
        }

        console.log(`📦 API Response: ✓ ${issueKey} saved to status ${targetColumn.dataset.statusName}`);
        showToast(`✓ ${issueKey} moved to ${targetColumn.dataset.statusName}`, 'success');
    } catch (error) {
        console.error('[DROP] Error:', error.message);
        // Rollback on error
        originalParent.appendChild(cardElement);
        updateColumnCounts();
        showToast(`✗ Failed: ${error.message}`, 'error');
    }

    draggedCard = null;
}

/**
 * Update column counts
 */
function updateColumnCounts() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const boardColumn = column.querySelector('.board-column');
        const count = boardColumn.querySelectorAll('.issue-card').length;
        const badge = column.querySelector('.column-badge');
        
        if (badge) {
            badge.textContent = count;
        }

        const emptyState = boardColumn.querySelector('.empty-state');
        if (emptyState) {
            emptyState.style.display = count === 0 ? 'flex' : 'none';
        }
    });
}

/**
 * Setup search functionality
 */
function setupSearch() {
    const searchInput = document.getElementById('boardSearch');
    if (!searchInput) return;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        
        document.querySelectorAll('.issue-card').forEach(card => {
            const key = card.dataset.issueKey.toLowerCase();
            const summary = card.querySelector('.card-summary')?.textContent.toLowerCase();
            
            const matches = key.includes(query) || (summary && summary.includes(query));
            card.style.display = matches ? '' : 'none';
        });
    });
}

/**
 * Setup add card buttons
 */
function setupAddCardButtons() {
    document.querySelectorAll('.add-card-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            window.location.href = '<?= url("/projects/{$project['key']}/issues/create") ?>';
        });
    });
}

/**
 * Setup Group button
 */
function setupGroupButton() {
    const groupBtn = document.getElementById('groupBtn');
    if (!groupBtn) return;

    groupBtn.addEventListener('click', () => {
        showGroupMenu();
    });
}

/**
 * Show group menu
 */
function showGroupMenu() {
    const groupBtn = document.getElementById('groupBtn');
    const groupMenu = document.createElement('div');
    groupMenu.className = 'board-menu-dropdown';
    groupMenu.innerHTML = `
        <button class="menu-item" onclick="groupBy('status')">
            <i class="bi bi-columns-gap"></i>
            <span>Group by Status</span>
        </button>
        <button class="menu-item" onclick="groupBy('assignee')">
            <i class="bi bi-person"></i>
            <span>Group by Assignee</span>
        </button>
        <button class="menu-item" onclick="groupBy('priority')">
            <i class="bi bi-exclamation-circle"></i>
            <span>Group by Priority</span>
        </button>
        <hr style="margin: 4px 0;">
        <button class="menu-item" onclick="groupBy('none')">
            <i class="bi bi-x-circle"></i>
            <span>Clear Grouping</span>
        </button>
    `;
    
    const existingMenu = document.querySelector('.board-menu-dropdown');
    if (existingMenu) {
        existingMenu.remove();
    }
    
    document.body.appendChild(groupMenu);
    
    // Position menu below the button
    const rect = groupBtn.getBoundingClientRect();
    groupMenu.style.top = (rect.bottom + 4) + 'px';
    groupMenu.style.left = rect.left + 'px';
    
    // Close menu when clicking outside
    setTimeout(() => {
        document.addEventListener('click', closeGroupMenu);
    }, 100);
}

/**
 * Close group menu
 */
function closeGroupMenu(e) {
    const menu = document.querySelector('.board-menu-dropdown');
    const groupBtn = document.getElementById('groupBtn');
    
    if (menu && !menu.contains(e.target) && !groupBtn.contains(e.target)) {
        menu.remove();
        document.removeEventListener('click', closeGroupMenu);
    }
}

/**
 * Group by field - Reorganizes the board
 */
function groupBy(field) {
    console.log(`[GROUP] Grouping by: ${field}`);
    
    const board = document.querySelector('.kanban-board');
    if (!board) return;
    
    // IMPORTANT: Collect issues BEFORE clearing the board
    // Get all issue cards
    const allIssues = Array.from(document.querySelectorAll('.issue-card')).map(card => ({
        id: card.dataset.issueId,
        key: card.dataset.issueKey,
        statusId: card.dataset.statusId,
        html: card.cloneNode(true),
        summary: card.querySelector('.card-summary')?.textContent || '',
        type: card.querySelector('.issue-type-label span')?.textContent || '',
        priority: card.dataset.priority || 'Medium',
        assignee: card.dataset.assignee || 'Unassigned'
    }));
    
    console.log(`[GROUP] Collected ${allIssues.length} issues`);
    
    // NOW remove existing columns
    board.innerHTML = '';
    
    let groups = {};
    let groupLabels = {};
    
    if (field === 'status') {
        // Group by status (original view) - Use cached status data
        Object.keys(statusesData).forEach(statusId => {
            groups[statusId] = [];
            groupLabels[statusId] = statusesData[statusId];
        });
        
        allIssues.forEach(issue => {
            if (groups[issue.statusId]) {
                groups[issue.statusId].push(issue);
            }
        });
        
    } else if (field === 'assignee') {
        // Group by assignee
        allIssues.forEach(issue => {
            const assignee = issue.assignee;
            if (!groups[assignee]) {
                groups[assignee] = [];
                groupLabels[assignee] = assignee;
            }
            groups[assignee].push(issue);
        });
        
    } else if (field === 'priority') {
        // Group by priority - Keep order and include all priorities
        const priorityOrder = ['Urgent', 'High', 'Medium', 'Low'];
        
        // First, collect all unique priorities from issues
        const issuePriorities = new Set();
        allIssues.forEach(issue => {
            issuePriorities.add(issue.priority || 'Medium');
        });
        
        // Create groups in priority order
        priorityOrder.forEach(p => {
            if (issuePriorities.has(p)) {
                groups[p] = [];
                groupLabels[p] = p;
            }
        });
        
        // Add any priorities not in standard list
        issuePriorities.forEach(p => {
            if (!groups[p]) {
                groups[p] = [];
                groupLabels[p] = p;
            }
        });
        
        // Sort issues into priority groups
        allIssues.forEach(issue => {
            const issuePriority = issue.priority || 'Medium';
            if (groups[issuePriority]) {
                groups[issuePriority].push(issue);
            }
        });
        
    } else {
        // Clear grouping - go back to status view
        window.location.reload();
        return;
    }
    
    // Render grouped columns in correct order
    const groupKeys = Object.keys(groups);
    
    // Maintain order for priorities
    if (field === 'priority') {
        const priorityOrder = ['Urgent', 'High', 'Medium', 'Low'];
        groupKeys.sort((a, b) => {
            const aIndex = priorityOrder.indexOf(a);
            const bIndex = priorityOrder.indexOf(b);
            if (aIndex === -1 && bIndex === -1) return 0;
            if (aIndex === -1) return 1;
            if (bIndex === -1) return -1;
            return aIndex - bIndex;
        });
    } else if (field === 'status') {
        // Maintain original status order using cached data
        groupKeys.sort((a, b) => {
            const aIndex = Object.keys(statusesData).indexOf(a);
            const bIndex = Object.keys(statusesData).indexOf(b);
            return aIndex - bIndex;
        });
    } else if (field === 'assignee') {
        // Sort alphabetically
        groupKeys.sort();
    }
    
    groupKeys.forEach(groupKey => {
        const groupIssues = groups[groupKey];
        const groupLabel = groupLabels[groupKey];
        
        console.log(`[GROUP] Rendering group: ${groupLabel} with ${groupIssues.length} issues`);
        
        // Create column structure step by step
        const columnDiv = document.createElement('div');
        columnDiv.className = 'kanban-column';
        columnDiv.dataset.groupKey = groupKey;
        
        // Create header
        const headerDiv = document.createElement('div');
        headerDiv.className = 'column-header';
        headerDiv.innerHTML = `
            <div class="column-title-group">
                <h3 class="column-title">${escapeHtml(groupLabel)}</h3>
                <span class="column-badge">${groupIssues.length}</span>
            </div>
        `;
        columnDiv.appendChild(headerDiv);
        
        // Create content area
        const contentDiv = document.createElement('div');
        contentDiv.className = 'column-cards board-column';
        contentDiv.dataset.groupKey = groupKey;
        
        // Add empty state or issues
        if (groupIssues.length === 0) {
            contentDiv.innerHTML = '<div class="empty-state"><i class="empty-icon bi bi-inbox"></i><p class="empty-text">No issues</p></div>';
        } else {
            // Add each issue card
            groupIssues.forEach(issue => {
                contentDiv.appendChild(issue.html);
            });
        }
        
        columnDiv.appendChild(contentDiv);
        board.appendChild(columnDiv);
    });
    
    // Re-initialize drag and drop
    setupDragAndDrop();
    
    // Update button text to show current grouping
    const groupBtn = document.getElementById('groupBtn');
    if (groupBtn) {
        const fieldNames = {
            'status': 'Status',
            'assignee': 'Assignee',
            'priority': 'Priority'
        };
        groupBtn.dataset.currentGroup = field;
        groupBtn.title = `Grouped by ${fieldNames[field] || 'Status'}`;
    }
    
    showToast(`✓ Grouped by ${field.charAt(0).toUpperCase() + field.slice(1)}`, 'success');
    document.querySelector('.board-menu-dropdown')?.remove();
    console.log(`✓ Board regrouped by ${field}`);
}

/**
 * Escape HTML special characters
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Setup More menu button
 */
function setupMoreMenu() {
    const menuBtn = document.querySelector('.menu-btn');
    if (!menuBtn) return;

    menuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        showMoreMenu(menuBtn);
    });
}

/**
 * Show more options menu
 */
function showMoreMenu(btn) {
    const moreMenu = document.createElement('div');
    moreMenu.className = 'board-menu-dropdown';
    moreMenu.innerHTML = `
        <button class="menu-item" onclick="exportBoard()">
            <i class="bi bi-download"></i>
            <span>Export Board</span>
        </button>
        <button class="menu-item" onclick="printBoard()">
            <i class="bi bi-printer"></i>
            <span>Print</span>
        </button>
        <hr style="margin: 4px 0;">
        <button class="menu-item" onclick="boardSettings()">
            <i class="bi bi-gear"></i>
            <span>Board Settings</span>
        </button>
    `;
    
    const existingMenu = document.querySelector('.board-menu-dropdown');
    if (existingMenu) {
        existingMenu.remove();
    }
    
    document.body.appendChild(moreMenu);
    
    // Position menu below the button
    const rect = btn.getBoundingClientRect();
    moreMenu.style.top = (rect.bottom + 4) + 'px';
    moreMenu.style.right = (window.innerWidth - rect.right) + 'px';
    moreMenu.style.left = 'auto';
    
    // Close menu when clicking outside
    setTimeout(() => {
        document.addEventListener('click', closeMoreMenu);
    }, 100);
}

/**
 * Close more menu
 */
function closeMoreMenu(e) {
    const menu = document.querySelector('.board-menu-dropdown');
    const menuBtn = document.querySelector('.menu-btn');
    
    if (menu && !menu.contains(e.target) && !menuBtn.contains(e.target)) {
        menu.remove();
        document.removeEventListener('click', closeMoreMenu);
    }
}

/**
 * Export board data
 */
function exportBoard() {
    console.log('[EXPORT] Exporting board data...');
    
    // Show export format menu
    const exportMenu = document.createElement('div');
    exportMenu.className = 'export-format-menu';
    exportMenu.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border: 1px solid #DFE1E6;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        z-index: 10000;
        padding: 20px;
        min-width: 320px;
    `;
    
    exportMenu.innerHTML = `
        <h4 style="margin: 0 0 16px; color: #161B22; font-size: 14px; font-weight: 600;">Export Board As</h4>
        <button onclick="exportAsCSV()" style="width: 100%; padding: 10px; margin-bottom: 8px; border: 1px solid #DFE1E6; border-radius: 4px; background: #FFFFFF; cursor: pointer; text-align: left; font-size: 13px;">
            <i class="bi bi-filetype-csv"></i> CSV (Excel/Spreadsheet)
        </button>
        <button onclick="exportAsJSON()" style="width: 100%; padding: 10px; margin-bottom: 8px; border: 1px solid #DFE1E6; border-radius: 4px; background: #FFFFFF; cursor: pointer; text-align: left; font-size: 13px;">
            <i class="bi bi-filetype-json"></i> JSON (Data Format)
        </button>
        <button onclick="closeExportMenu()" style="width: 100%; padding: 10px; border: 1px solid #DFE1E6; border-radius: 4px; background: #FFFFFF; cursor: pointer; text-align: left; font-size: 13px;">
            <i class="bi bi-x"></i> Cancel
        </button>
    `;
    
    document.body.appendChild(exportMenu);
    document.querySelector('.board-menu-dropdown')?.remove();
}

/**
 * Close export menu
 */
function closeExportMenu() {
    document.querySelector('.export-format-menu')?.remove();
}

/**
 * Export board as CSV
 */
function exportAsCSV() {
    console.log('[EXPORT] Exporting as CSV...');
    
    // Collect all issues from the board
    const issues = [];
    document.querySelectorAll('.issue-card').forEach(card => {
        const issue = {
            'Key': card.dataset.issueKey || '',
            'Summary': card.querySelector('.card-summary')?.textContent || '',
            'Type': card.querySelector('.issue-type-label span')?.textContent || '',
            'Status': card.closest('.kanban-column')?.dataset.statusName || '',
            'Date': card.querySelector('.due-date')?.textContent?.trim() || ''
        };
        issues.push(issue);
    });
    
    // Create CSV header
    const headers = Object.keys(issues[0] || {});
    let csv = headers.map(h => `"${h}"`).join(',') + '\n';
    
    // Add rows
    issues.forEach(issue => {
        const row = headers.map(h => {
            const value = issue[h] || '';
            return `"${value.toString().replace(/"/g, '""')}"`;
        }).join(',');
        csv += row + '\n';
    });
    
    // Download CSV file
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `board_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    closeExportMenu();
    showToast('✓ Board exported as CSV', 'success');
}

/**
 * Export board as JSON
 */
function exportAsJSON() {
    console.log('[EXPORT] Exporting as JSON...');
    
    // Collect all issues from the board
    const issues = [];
    const statuses = {};
    
    document.querySelectorAll('.kanban-column').forEach(column => {
        const statusName = column.dataset.statusName || '';
        const statusIssues = [];
        
        column.querySelectorAll('.issue-card').forEach(card => {
            const issue = {
                key: card.dataset.issueKey || '',
                summary: card.querySelector('.card-summary')?.textContent || '',
                type: card.querySelector('.issue-type-label span')?.textContent || '',
                dueDate: card.querySelector('.due-date')?.textContent?.trim() || ''
            };
            statusIssues.push(issue);
            issues.push(issue);
        });
        
        if (statusName) {
            statuses[statusName] = statusIssues;
        }
    });
    
    // Create JSON structure
    const boardData = {
        project: PROJECT_KEY,
        exportDate: new Date().toISOString(),
        totalIssues: issues.length,
        statuses: statuses,
        allIssues: issues
    };
    
    // Download JSON file
    const blob = new Blob([JSON.stringify(boardData, null, 2)], { type: 'application/json;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `board_${new Date().toISOString().split('T')[0]}.json`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    closeExportMenu();
    showToast('✓ Board exported as JSON', 'success');
}

/**
 * Print board
 */
function printBoard() {
    console.log('[PRINT] Printing board...');
    
    // Hide the toolbar and show print-specific styles
    const stylesheet = document.createElement('style');
    stylesheet.textContent = `
        @media print {
            * {
                margin: 0;
                padding: 0;
            }
            
            body {
                background: white;
            }
            
            .board-page-wrapper {
                height: auto;
            }
            
            .breadcrumb-nav,
            .board-toolbar,
            .search-box,
            .toolbar-btn,
            .add-card-btn,
            .menu-btn,
            #groupBtn {
                display: none !important;
            }
            
            .board-container {
                padding: 20px;
                overflow: visible;
                flex: none;
            }
            
            .kanban-board {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
                overflow: visible;
            }
            
            .kanban-column {
                page-break-inside: avoid;
                break-inside: avoid;
                flex: 0 0 auto;
                border: 2px solid #333;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .column-header {
                background: #f5f5f5;
                border-bottom: 2px solid #333;
                padding: 12px;
                font-weight: bold;
                page-break-inside: avoid;
            }
            
            .column-cards {
                overflow: visible;
                display: block;
            }
            
            .issue-card {
                page-break-inside: avoid;
                break-inside: avoid;
                margin-bottom: 12px;
                border: 1px solid #ccc;
                padding: 8px;
                background: white;
                display: block;
                flex-direction: column;
            }
            
            .card-priority-bar {
                display: none;
            }
            
            .card-body {
                padding: 0;
            }
            
            .card-key {
                font-weight: bold;
                color: #000;
                margin-bottom: 4px;
            }
            
            .card-summary {
                font-size: 12px;
                margin-bottom: 4px;
                color: #333;
            }
            
            .card-footer {
                border-top: 1px solid #ddd;
                padding-top: 4px;
                margin-top: 4px;
                font-size: 10px;
            }
            
            .issue-type-label {
                display: inline-block;
                background: #e0e0e0 !important;
                color: #000 !important;
                padding: 2px 4px;
                margin-right: 4px;
            }
            
            .due-date,
            .avatar-sm,
            .footer-right {
                display: inline-block;
                margin-right: 8px;
            }
            
            .empty-state {
                display: none;
            }
            
            /* Add title before printing */
            .board-container::before {
                content: "Board: <?= e($project['name']) ?>";
                display: block;
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 20px;
                text-decoration: underline;
            }
        }
    `;
    
    document.head.appendChild(stylesheet);
    
    // Trigger print dialog
    window.print();
    
    // Clean up after print
    setTimeout(() => {
        document.head.removeChild(stylesheet);
    }, 100);
    
    document.querySelector('.board-menu-dropdown')?.remove();
    showToast('✓ Print dialog opened', 'success');
}

/**
 * Board settings
 */
function boardSettings() {
    console.log('[SETTINGS] Opening board settings...');
    showToast('Board settings feature coming soon', 'info');
    document.querySelector('.board-menu-dropdown')?.remove();
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const icon = type === 'success' ? '✓' : '✗';
    console.log(`[${type.toUpperCase()}] ${message}`);
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeBoard);
} else {
    setTimeout(initializeBoard, 100);
}
</script>
<?php \App\Core\View::endSection(); ?>
