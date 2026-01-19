<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('styles'); ?>
<style>
    /* ============================================
    BREADCRUMB NAVIGATION
    ============================================ */

    .project-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 32px;
        background: #FFFFFF;
        border-bottom: 1px solid #DFE1E6;
        font-size: 13px;
        flex-shrink: 0;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #8B1956;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .breadcrumb-link:hover {
        color: #6F123F;
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: #626F86;
        font-weight: 300;
    }

    .breadcrumb-current {
        color: #161B22;
        font-weight: 600;
    }

    /* ============================================
    KANBAN/SCRUM BOARD - ENTERPRISE REDESIGN
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

    * {
        box-sizing: border-box;
    }

    .board-page-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 60px);
        background: var(--jira-light);
        overflow: hidden;
    }

    /* TOOLBAR */
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
        text-decoration: none;
    }

    .toolbar-btn:hover {
        border-color: #B6C2CF;
        background: var(--jira-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        color: var(--jira-dark);
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

    .menu-btn {
        padding: 6px 8px !important;
    }

    /* BOARD MENU DROPDOWN */
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

    /* BOARD CONTAINER */
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

    /* COLUMN */
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
    }

    .column-cards.drag-over {
        background: #E6EFFC;
    }

    /* ISSUE CARD */
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

    .card-priority-bar {
        width: 4px;
        flex-shrink: 0;
        transition: all var(--transition);
    }

    .issue-card:hover .card-priority-bar {
        width: 5px;
    }

    .card-body {
        padding: 10px 11px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        min-width: 0;
        justify-content: space-between;
    }

    .card-key {
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-blue);
        text-decoration: none;
    }

    .card-summary {
        font-size: 13px;
        font-weight: 500;
        color: var(--jira-dark);
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
        padding-top: 7px;
        border-top: 1px solid var(--jira-border);
        min-height: 28px;
    }

    .footer-left {
        display: flex;
        align-items: center;
        gap: 5px;
        flex: 1;
        min-width: 0;
    }

    .footer-right {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-shrink: 0;
    }

    .issue-type-label {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 6px;
        border-radius: 3px;
        color: #FFFFFF;
        font-size: 10px;
        font-weight: 600;
    }

    .avatar-sm {
        width: 24px;
        height: 24px;
        border-radius: 3px;
        object-fit: cover;
        border: 1px solid var(--jira-border);
    }

    .avatar-initials {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--jira-blue), var(--jira-blue-dark));
        color: #FFFFFF;
        font-size: 10px;
        font-weight: 700;
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
</style>
<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('content'); ?>

<div class="board-page-wrapper">
    <!-- Breadcrumb -->
    <div class="project-breadcrumb">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/projects/' . $project['key']) ?>" class="breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= e($board['name']) ?></span>
    </div>

    <!-- Toolbar -->
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

            <!-- Scrum Sprint Dropdown -->
            <?php if ($board['type'] === 'scrum' && !empty($sprints)): ?>
                <div class="dropdown d-inline-block">
                    <button class="toolbar-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-lightning-charge"></i>
                        <span><?= !empty($activeSprint) ? e($activeSprint['name']) : 'Select Sprint' ?></span>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($sprints as $sprint): ?>
                            <li>
                                <a class="dropdown-item <?= ($sprint['id'] === ($activeSprint['id'] ?? null)) ? 'active' : '' ?>"
                                    href="?sprint_id=<?= $sprint['id'] ?>">
                                    <?= e($sprint['name']) ?>
                                    <small class="text-muted ms-2">(<?= e($sprint['status']) ?>)</small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= url('/projects/' . $project['key'] . '/backlog') ?>">
                                <i class="bi bi-list-ul me-2"></i> Backlog
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="toolbar-right">
            <a href="<?= url('/projects/' . $project['key'] . '/boards') ?>" class="toolbar-btn">
                <i class="bi bi-layout-three-columns"></i>
                <span>Boards</span>
            </a>

            <button class="toolbar-btn" id="groupBtn" title="Group by">
                <i class="bi bi-collection"></i>
                <span>Group</span>
            </button>

            <?php if ($board['type'] === 'scrum' && !empty($activeSprint) && can('manage-sprints', $project['id'])): ?>
                <button class="toolbar-btn btn-primary" data-bs-toggle="modal" data-bs-target="#completeSprintModal">
                    Complete Sprint
                </button>
            <?php endif; ?>

            <button class="toolbar-btn menu-btn" title="More options">
                <i class="bi bi-three-dots-vertical"></i>
            </button>

            <?php if (can('manage-boards', $project['id'])): ?>
                <a href="<?= url('/boards/' . $board['id'] . '/settings') ?>" class="toolbar-btn" title="Board Settings">
                    <i class="bi bi-gear"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Board Content -->
    <div class="board-container" id="boardContainer">
        <div class="kanban-board">
            <?php foreach ($columns as $column): ?>
                <?php
                // Parse status_ids to get the target status for this column
                $statusIds = json_decode($column['status_ids'] ?? '[]', true);
                $targetStatusId = $statusIds[0] ?? '';
                ?>
                <div class="kanban-column" data-id="<?= $column['id'] ?>"
                    data-status-id="<?= $targetStatusId // Use actual status ID for grouping and logic ?>"
                    data-status-name="<?= e($column['name']) ?>">

                    <!-- Column Header -->
                    <div class="column-header" style="border-top: 3px solid <?= e($column['color'] ?? '#8B1956') ?>">
                        <div class="column-title-group">
                            <h3 class="column-title"><?= e($column['name']) ?></h3>
                            <span class="column-badge"><?= count($column['issues'] ?? []) ?></span>
                        </div>
                    </div>

                    <!-- Column Cards -->
                    <div class="column-cards board-column" data-column-id="<?= $column['id'] ?>"
                        data-status-id="<?= $targetStatusId ?>">
                        <?php if (empty($column['issues'])): ?>
                            <div class="empty-state">
                                <i class="empty-icon bi bi-inbox"></i>
                                <p class="empty-text">No issues</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($column['issues'] ?? [] as $issue): ?>
                                <div class="issue-card" draggable="true" data-issue-id="<?= $issue['id'] ?>"
                                    data-issue-key="<?= e($issue['issue_key']) ?>" data-status-id="<?= $issue['status_id'] ?>"
                                    data-priority="<?= e($issue['priority_name'] ?? 'Medium') ?>"
                                    data-assignee="<?= e($issue['assignee_name'] ?? 'Unassigned') ?>">

                                    <!-- Priority Bar -->
                                    <div class="card-priority-bar"
                                        style="background-color: <?= e($issue['priority_color'] ?? '#DFE1E6') ?>"></div>

                                    <!-- Card Content -->
                                    <div class="card-body">
                                        <div>
                                            <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="card-key">
                                                <?= e($issue['issue_key']) ?>
                                            </a>
                                            <div class="card-summary" title="<?= e($issue['summary']) ?>">
                                                <?= e($issue['summary']) ?>
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            <div class="footer-left">
                                                <span class="issue-type-label"
                                                    style="background-color: <?= e($issue['issue_type_color'] ?? '#8B1956') ?>">
                                                    <i class="bi bi-<?= e($issue['issue_type_icon'] ?? 'circle') ?>"></i>
                                                    <span><?= e($issue['issue_type_name'] ?? '') ?></span>
                                                </span>
                                                <?php if (!empty($issue['due_date'])): ?>
                                                    <span class="due-date ms-2" title="Due: <?= e($issue['due_date']) ?>">
                                                        <i class="bi bi-calendar"></i>
                                                        <?= date('M d', strtotime($issue['due_date'])) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($issue['story_points']): ?>
                                                    <span
                                                        class="badge bg-light text-dark border ms-2"><?= $issue['story_points'] ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="footer-right">
                                                <?php if (!empty($issue['assignee_avatar'])): ?>
                                                    <img src="<?= e(avatar($issue['assignee_avatar'])) ?>" class="avatar-sm"
                                                        title="<?= e($issue['assignee_name']) ?>">
                                                <?php elseif (!empty($issue['assignee_name'])): ?>
                                                    <div class="avatar-sm avatar-initials" title="<?= e($issue['assignee_name']) ?>">
                                                        <?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="avatar-sm" style="background: #f4f5f7;" title="Unassigned"></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Complete Sprint Modal -->
<?php if ($board['type'] === 'scrum' && can('manage-sprints', $project['id']) && !empty($activeSprint)): ?>
    <div class="modal fade" id="completeSprintModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Complete Sprint: <?= e($activeSprint['name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= url('/projects/' . $project['key'] . '/sprints/' . $activeSprint['id'] . '/complete') ?>"
                    method="POST">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <?php
                        $incompleteCount = 0;
                        foreach ($columns as $col) {
                            if (strtolower($col['name']) !== 'done') {
                                $incompleteCount += count($col['issues'] ?? []);
                            }
                        }
                        ?>

                        <?php if ($incompleteCount > 0): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?= $incompleteCount ?> issues are incomplete.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Move incomplete issues to:</label>
                                <select name="move_to" class="form-select">
                                    <option value="backlog">Backlog</option>
                                    <?php foreach ($sprints as $sprint): ?>
                                        <?php if ($sprint['id'] !== $activeSprint['id'] && $sprint['status'] !== 'completed'): ?>
                                            <option value="<?= $sprint['id'] ?>"><?= e($sprint['name']) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <p>All issues are complete. Good job!</p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Complete Sprint</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const boardContainer = document.getElementById('boardContainer');
        const searchInput = document.getElementById('boardSearch');
        const groupBtn = document.getElementById('groupBtn');
        let draggedCard = null;
        let statusesData = {};

        // Initialize Data
        document.querySelectorAll('.kanban-column').forEach(col => {
            const statusId = col.dataset.statusId;
            const statusName = col.dataset.statusName;
            statusesData[statusId] = statusName;
        });

        // Search Functionality
        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.issue-card').forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(term) ? 'flex' : 'none';
                });
            });
        }

        // Setup Group By Button
        if (groupBtn) {
            groupBtn.addEventListener('click', showGroupMenu);
        }

        function showGroupMenu() {
            const existingMenu = document.querySelector('.board-menu-dropdown');
            if (existingMenu) { existingMenu.remove(); return; }

            const groupMenu = document.createElement('div');
            groupMenu.className = 'board-menu-dropdown';
            groupMenu.innerHTML = `
                <button class="menu-item" onclick="groupBy('assignee')">
                    <i class="bi bi-person"></i>
                    <span>Group by Assignee</span>
                </button>
                <button class="menu-item" onclick="groupBy('priority')">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>Group by Priority</span>
                </button>
                <hr style="margin: 4px 0;">
                <button class="menu-item" onclick="window.location.reload()">
                    <i class="bi bi-x-circle"></i>
                    <span>Clear Grouping</span>
                </button>
            `;
            document.body.appendChild(groupMenu);

            const rect = groupBtn.getBoundingClientRect();
            groupMenu.style.top = (rect.bottom + 4) + 'px';
            groupMenu.style.left = rect.left + 'px';

            setTimeout(() => { document.addEventListener('click', closeGroupMenu) }, 100);
        }

        function closeGroupMenu(e) {
            const menu = document.querySelector('.board-menu-dropdown');
            if (menu && !menu.contains(e.target) && !groupBtn.contains(e.target)) {
                menu.remove();
                document.removeEventListener('click', closeGroupMenu);
            }
        }

        // Global groupBy function for onclick
        window.groupBy = function (field) {
            console.log('Grouping by', field);
            const board = document.querySelector('.kanban-board');

            // Collect issues
            const allIssues = Array.from(document.querySelectorAll('.issue-card')).map(card => ({
                html: card.cloneNode(true),
                assignee: card.dataset.assignee || 'Unassigned',
                priority: card.dataset.priority || 'Medium',
                statusId: card.dataset.statusId
            }));

            let groups = {};

            if (field === 'assignee') {
                allIssues.forEach(issue => {
                    const key = issue.assignee;
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(issue);
                });
            } else if (field === 'priority') {
                const priorityOrder = ['Urgent', 'High', 'Medium', 'Low'];
                allIssues.forEach(issue => {
                    const key = issue.priority;
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(issue);
                });
                // Ensure order could be handled, for now just simple dict
            }

            // Clear and Render
            board.innerHTML = '';

            Object.keys(groups).forEach(key => {
                const columnDiv = document.createElement('div');
                columnDiv.className = 'kanban-column';

                const issuesHtml = groups[key].map(i => i.html.outerHTML).join('');

                columnDiv.innerHTML = `
                    <div class="column-header" style="border-top: 3px solid #8B1956">
                        <div class="column-title-group">
                            <h3 class="column-title">${key}</h3>
                            <span class="column-badge">${groups[key].length}</span>
                        </div>
                    </div>
                    <div class="column-cards board-column">
                        ${issuesHtml}
                    </div>
                 `;
                board.appendChild(columnDiv);
            });

            document.querySelector('.board-menu-dropdown')?.remove();
            // Re-init drag events if needed (skipped for grouping view as per original)
        };

        // Update column badge count
        function updateCount(columnElement, change) {
            const badge = columnElement.querySelector('.column-badge');
            if (badge) {
                let current = parseInt(badge.textContent) || 0;
                badge.textContent = Math.max(0, current + change);
            }
        }

        // Drag & Drop
        setupDragAndDrop();

        let startColumn = null;

        function setupDragAndDrop() {
            boardContainer.addEventListener('dragstart', function (e) {
                const card = e.target.closest('.issue-card');
                if (card) {
                    draggedCard = card;
                    startColumn = card.closest('.kanban-column');
                    card.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', card.dataset.issueId);
                }
            });

            boardContainer.addEventListener('dragend', function (e) {
                const card = e.target.closest('.issue-card');
                if (card) {
                    card.classList.remove('dragging');
                    document.querySelectorAll('.column-cards').forEach(col => col.classList.remove('drag-over'));
                    draggedCard = null;
                    startColumn = null;
                }
            });

            boardContainer.addEventListener('dragover', function (e) {
                e.preventDefault();
                const column = e.target.closest('.column-cards');
                if (column) {
                    e.dataTransfer.dropEffect = 'move';
                    column.classList.add('drag-over');
                }
            });

            boardContainer.addEventListener('dragleave', function (e) {
                const column = e.target.closest('.column-cards');
                if (column && !column.contains(e.relatedTarget)) {
                    column.classList.remove('drag-over');
                }
            });

            boardContainer.addEventListener('drop', async function (e) {
                e.preventDefault();
                const column = e.target.closest('.column-cards');

                if (column && draggedCard) {
                    column.classList.remove('drag-over');
                    const issueKey = draggedCard.dataset.issueKey;
                    const newStatusId = column.dataset.statusId;

                    if (!newStatusId) {
                        console.error('Target column has no status ID');
                        return;
                    }

                    // Remove empty state if present
                    const emptyState = column.querySelector('.empty-state');
                    if (emptyState) {
                        emptyState.remove();
                    }

                    // Optimistic UI Update
                    column.appendChild(draggedCard);

                    // Update Counts
                    const targetKanbanColumn = column.closest('.kanban-column');
                    if (startColumn && targetKanbanColumn && startColumn !== targetKanbanColumn) {
                        updateCount(startColumn, -1);
                        updateCount(targetKanbanColumn, 1);
                    }

                    // Backend Update
                    try {
                        const response = await fetch(`<?= url('/api/v1/issues/') ?>${issueKey}/transitions`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ status_id: newStatusId })
                        });

                        const result = await response.json();

                        if (!response.ok || result.error) {
                            throw new Error(result.error || 'Failed to update status');
                        }
                        console.log('Status updated successfully');
                    } catch (error) {
                        console.error('Move failed:', error);
                        alert('Failed to move issue: ' + error.message);
                        window.location.reload(); // Revert changes
                    }
                }
            });
        }
    });

    /**
     * Escape HTML special characters
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
<?php \App\Core\View::endSection(); ?>