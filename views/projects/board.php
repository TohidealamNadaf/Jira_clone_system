<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="jira-board-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="board-breadcrumb">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link active">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Board</span>
    </div>

    <!-- Board Header -->
    <div class="board-header">
        <div class="board-header-left">
            <h1 class="board-title"><?= e($project['name']) ?></h1>
            <p class="board-subtitle">Kanban Board</p>
        </div>
        <div class="board-header-right">
            <button class="btn btn-sm btn-outline-secondary board-filter-btn">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-sm btn-primary board-create-btn">
                <i class="bi bi-plus-lg me-1"></i> Create Issue
            </a>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="jira-board">
        <?php foreach ($statuses as $status): ?>
        <?php 
            $statusIssues = array_filter($issues, function($issue) use ($status) {
                return $issue['status_id'] == $status['id'];
            });
        ?>
        <div class="board-column-container">
            <!-- Column Header -->
            <div class="board-column-header">
                <div class="column-title-section">
                    <h3 class="column-title"><?= e($status['name']) ?></h3>
                    <span class="column-count"><?= count($statusIssues) ?></span>
                </div>
                <button class="column-menu-btn" title="Column actions">
                    <i class="bi bi-three-dots"></i>
                </button>
            </div>

            <!-- Column Content -->
            <div class="board-column board-column-content" 
                 data-status-id="<?= e($status['id']) ?>"
                 data-status-name="<?= e($status['name']) ?>">
                
                <?php if (empty($statusIssues)): ?>
                <div class="empty-column-state">
                    <div class="empty-state-icon">
                        📋
                    </div>
                    <p class="empty-state-text">No issues</p>
                </div>
                <?php else: ?>
                    <?php foreach ($statusIssues as $issue): ?>
                    <div class="issue-card board-card" 
                         draggable="true"
                         data-issue-id="<?= e($issue['id']) ?>"
                         data-issue-key="<?= e($issue['issue_key']) ?>"
                         data-current-status="<?= e($status['id']) ?>">
                         
                        <!-- Priority Bar (left edge) -->
                        <div class="card-priority-indicator" 
                             style="background-color: <?= e($issue['priority_color']) ?>"
                             title="Priority: <?= e($issue['priority_name'] ?? 'None') ?>"></div>

                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Top Row: Issue Key + Issue Type Badge + Priority Badge -->
                            <div class="card-top-row">
                                <div class="card-left">
                                    <span class="issue-key"><?= e($issue['issue_key']) ?></span>
                                </div>
                                <div class="card-badges">
                                    <!-- Issue Type Badge -->
                                    <span class="issue-type-badge" 
                                          style="background-color: <?= e($issue['issue_type_color'] ?? '#F7F8FA') ?>; color: #FFFFFF;"
                                          title="<?= e($issue['issue_type_name'] ?? 'Task') ?>">
                                        <i class="bi bi-<?= e($issue['issue_type_icon'] ?? 'list-check') ?>"></i>
                                        <span class="badge-label"><?= e(strtoupper(substr($issue['issue_type_name'] ?? 'Task', 0, 1))) ?></span>
                                    </span>
                                    
                                    <!-- Priority Badge -->
                                    <?php if (!empty($issue['priority_name']) && $issue['priority_name'] !== 'None'): ?>
                                    <span class="priority-badge" style="background-color: <?= e($issue['priority_color']) ?>">
                                        <?= e(substr($issue['priority_name'], 0, 1)) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Summary -->
                            <a href="<?= url("/issue/{$issue['issue_key']}") ?>" class="issue-summary">
                                <?= e($issue['summary']) ?>
                            </a>

                            <!-- Bottom Row: Assignee + Issue Type Label -->
                            <div class="card-bottom-section">
                                <div class="card-bottom-row">
                                    <div class="issue-assignee">
                                        <?php if ($issue['assignee_name']): ?>
                                            <?php if ($issue['assignee_avatar'] && file_exists('public' . $issue['assignee_avatar'])): ?>
                                            <img src="<?= e($issue['assignee_avatar']) ?>" 
                                                 alt="<?= e($issue['assignee_name']) ?>"
                                                 class="assignee-avatar"
                                                 title="<?= e($issue['assignee_name']) ?>">
                                            <?php else: ?>
                                            <div class="assignee-avatar assignee-initials" title="<?= e($issue['assignee_name']) ?>">
                                                <?= e(substr($issue['assignee_name'], 0, 1)) ?>
                                            </div>
                                            <?php endif; ?>
                                            <span class="assignee-name"><?= e($issue['assignee_name']) ?></span>
                                        <?php else: ?>
                                        <div class="assignee-avatar assignee-unassigned" title="Unassigned">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <span class="assignee-name">Unassigned</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- Issue Type Label -->
                                <span class="issue-type-label">
                                    <?= e($issue['issue_type_name'] ?? 'Task') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Add Card Button -->
            <button class="add-card-btn" data-status-id="<?= e($status['id']) ?>" title="Add card to this column">
                <i class="bi bi-plus me-1"></i> Add card
            </button>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
/* ============================================
   JIRA BOARD REDESIGN - REAL JIRA STYLING
   ============================================ */

:root {
    --jira-blue: #0052CC;
    --jira-dark: #161B22;
    --jira-gray: #626F86;
    --jira-light: #F7F8FA;
    --jira-border: #DFE1E6;
}

.jira-board-wrapper {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #F7F8FA;
    padding: 0;
    overflow: hidden;
    margin-top: -1.5rem;
}

/* Breadcrumb Navigation */
.board-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 32px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    font-size: 13px;
    flex-shrink: 0;
}

.breadcrumb-link {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--jira-blue);
    text-decoration: none;
    transition: all 0.2s ease;
    font-weight: 500;
}

.breadcrumb-link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

.breadcrumb-link.active {
    color: var(--text-primary);
    font-weight: 600;
    cursor: default;
}

.breadcrumb-link.active:hover {
    text-decoration: none;
}

.breadcrumb-separator {
    color: var(--jira-gray);
    font-weight: 300;
}

.breadcrumb-current {
    color: var(--text-primary);
    font-weight: 500;
}

/* Board Header */
.board-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 32px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--jira-border);
    box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13);
    flex-shrink: 0;
}

.board-header-left {
    flex: 1;
}

.board-title {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    color: var(--jira-dark);
    letter-spacing: -0.3px;
}

.board-subtitle {
    margin: 6px 0 0 0;
    font-size: 14px;
    color: var(--jira-gray);
    font-weight: 400;
}

.board-header-right {
    display: flex;
    gap: 12px;
    align-items: center;
}

/* Board Container */
.jira-board {
    display: flex;
    gap: 24px;
    padding: 24px 32px;
    overflow-x: auto;
    overflow-y: hidden;
    flex: 1;
    align-items: flex-start;
    min-width: 0;
}

/* Column Container */
.board-column-container {
    flex: 0 0 360px;
    display: flex;
    flex-direction: column;
    background: #F7F8FA;
    border-radius: 3px;
    overflow: visible;
    min-height: 0;
    width: 360px;
}

/* Column Header */
.board-column-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #F7F8FA;
    flex-shrink: 0;
    margin-bottom: 8px;
}

.column-title-section {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}

.column-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--jira-dark);
    text-transform: none;
    letter-spacing: -0.2px;
}

.column-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    padding: 0 8px;
    background-color: #ECEDF0;
    color: #57606A;
    font-size: 13px;
    font-weight: 600;
    border-radius: 4px;
}

.column-menu-btn {
    background: none;
    border: none;
    padding: 6px;
    color: var(--jira-gray);
    cursor: pointer;
    font-size: 18px;
    transition: all 0.2s ease;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.column-menu-btn:hover {
    background-color: #EBECF0;
    color: var(--jira-dark);
}

/* Column Content */
.board-column {
    flex: 1;
    overflow-y: auto;
    overflow-x: visible;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 0;
    padding: 0 8px;
}

.board-column::-webkit-scrollbar {
    width: 8px;
}

.board-column::-webkit-scrollbar-track {
    background: transparent;
}

.board-column::-webkit-scrollbar-thumb {
    background: #DFE1E6;
    border-radius: 4px;
}

.board-column::-webkit-scrollbar-thumb:hover {
    background: #B6C2CF;
}

/* Empty Column State */
.empty-column-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    color: var(--jira-gray);
    text-align: center;
}

.empty-state-icon {
    font-size: 56px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state-text {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
    color: var(--jira-gray);
}

/* Issue Card - Jira Style */
.issue-card {
    background: #FFFFFF;
    border: 1px solid #EBECF0;
    border-radius: 6px;
    padding: 14px;
    cursor: grab;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: visible;
    display: flex;
    flex-direction: column;
    gap: 10px;
    box-shadow: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    min-height: auto;
    width: 100%;
    box-sizing: border-box;
}

.issue-card:hover {
    box-shadow: 0 8px 16px rgba(9, 30, 66, 0.15), 0 0 1px rgba(9, 30, 66, 0.13);
    border-color: #B6C2CF;
    background: #FAFBFC;
    transform: translateY(-3px);
}

.issue-card:active {
    cursor: grabbing;
}

.issue-card.dragging {
    opacity: 0.6;
    box-shadow: 0 12px 24px rgba(9, 30, 66, 0.25);
    transform: rotate(3deg);
}

/* Card Priority Indicator */
.card-priority-indicator {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    border-radius: 3px 0 0 3px;
}

/* Card Content Container */
.card-content {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
    min-width: 0;
    width: 100%;
    padding-left: 4px;
}

/* Card Top Row */
.card-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    min-width: 0;
}

.card-left {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
    flex: 1;
}

.issue-key {
    font-size: 12px;
    font-weight: 600;
    color: var(--jira-gray);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    flex-shrink: 0;
    white-space: nowrap;
}

/* Card Badges Container */
.card-badges {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

/* Issue Type Badge */
.issue-type-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex-shrink: 0;
    white-space: nowrap;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
}

.issue-type-badge i {
    font-size: 12px;
    display: flex;
    align-items: center;
}

.issue-type-badge .badge-label {
    display: inline;
}

.issue-type-badge:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
    transform: scale(1.05);
}

.priority-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 4px;
    color: #FFFFFF;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
    transition: all 0.15s ease;
}

.priority-badge:hover {
    transform: scale(1.1);
}

/* Issue Summary */
.issue-summary {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: var(--jira-dark);
    text-decoration: none;
    line-height: 1.43;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    transition: color 0.15s ease;
    margin: 0;
    padding: 2px 0;
    min-width: 0;
    white-space: normal;
}

.issue-summary:hover {
    color: var(--jira-blue);
    text-decoration: underline;
}

/* Card Bottom Section */
.card-bottom-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px solid #F1F2F4;
    gap: 8px;
}

/* Card Bottom Row */
.card-bottom-row {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

/* Issue Type Label */
.issue-type-label {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    color: var(--jira-gray);
    text-transform: capitalize;
    white-space: nowrap;
    padding: 3px 8px;
    background-color: #F7F8FA;
    border-radius: 3px;
    border: 1px solid #DFE1E6;
    flex-shrink: 0;
}

/* Assignee */
.issue-assignee {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 0;
}

.assignee-avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    color: #FFFFFF;
    border: 1px solid #FFFFFF;
    box-shadow: 0 1px 2px rgba(9, 30, 66, 0.13);
    object-fit: cover;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    flex-shrink: 0;
}

.assignee-avatar:hover {
    transform: scale(1.15);
    box-shadow: 0 2px 6px rgba(9, 30, 66, 0.2);
}

.assignee-initials {
    background: linear-gradient(135deg, #0052CC, #2684FF);
}

.assignee-unassigned {
    background-color: #DFE1E6;
    color: #626F86;
    font-size: 13px;
}

.assignee-name {
    font-size: 12px;
    color: var(--jira-gray);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}

/* Add Card Button */
.add-card-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px;
    margin-top: 8px;
    background: transparent;
    border: 1px dashed #DFE1E6;
    color: var(--jira-gray);
    border-radius: 3px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.15s ease;
}

.add-card-btn:hover {
    background-color: #DEEBFF;
    border-color: var(--jira-blue);
    color: var(--jira-blue);
}

/* Board Buttons */
.board-create-btn,
.board-filter-btn {
    padding: 7px 14px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 3px;
    transition: all 0.15s ease;
}

.board-create-btn {
    background-color: var(--jira-blue);
    border-color: var(--jira-blue);
    color: #FFFFFF;
}

.board-create-btn:hover {
    background-color: #003DA5;
    border-color: #003DA5;
}

.board-filter-btn {
    background-color: #FFFFFF;
    border: 1px solid var(--jira-border);
    color: var(--jira-dark);
}

.board-filter-btn:hover {
    background-color: #F7F8FA;
    border-color: #B6C2CF;
}

/* Drag Over Effect */
.board-column.drag-over {
    background-color: #DEEBFF;
    border-radius: 3px;
}

/* Responsive Design */
@media (max-width: 1400px) {
    .board-column-container {
        flex: 0 0 330px;
    }
}

@media (max-width: 1024px) {
    .board-header {
        padding: 20px 24px;
    }

    .jira-board {
        gap: 20px;
        padding: 20px 24px;
    }

    .board-column-container {
        flex: 0 0 300px;
    }

    .board-title {
        font-size: 24px;
    }
}

@media (max-width: 768px) {
    .board-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 16px 20px;
    }

    .board-header-right {
        width: 100%;
        justify-content: space-between;
    }

    .jira-board {
        gap: 16px;
        padding: 16px 20px;
    }

    .board-column-container {
        flex: 0 0 280px;
    }

    .issue-card {
        padding: 10px 10px;
    }

    .issue-summary {
        font-size: 13px;
    }
}

/* Loading State */
.board-column.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Animation */
@keyframes slideIn {
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
    animation: slideIn 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
// JIRA Board Drag & Drop Implementation
const projectKey = '<?= e($project['key']) ?>';
let draggedCard = null;
let originalColumn = null;

function initDragAndDrop() {
    console.log('🎯 Initializing drag-and-drop for board');
    
    // Card drag handlers
    document.querySelectorAll('.issue-card').forEach(card => {
        card.addEventListener('dragstart', (e) => {
            draggedCard = card;
            originalColumn = card.closest('.board-column');
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', card.innerHTML);
            console.log('✓ Drag started for', card.dataset.issueKey);
        });

        card.addEventListener('dragend', (e) => {
            card.classList.remove('dragging');
            document.querySelectorAll('.board-column').forEach(col => {
                col.classList.remove('drag-over');
            });
            console.log('✓ Drag ended');
        });
    });

    // Column drag handlers
    document.querySelectorAll('.board-column').forEach(column => {
        column.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            column.classList.add('drag-over');
        });

        column.addEventListener('dragleave', (e) => {
            if (e.target === column) {
                column.classList.remove('drag-over');
            }
        });

        column.addEventListener('drop', async (e) => {
            e.preventDefault();
            column.classList.remove('drag-over');

            if (!draggedCard) {
                console.log('✗ No card being dragged');
                return;
            }

            const statusId = column.dataset.statusId;
            const currentStatusId = originalColumn.dataset.statusId;

            // Don't move to same status
            if (statusId === currentStatusId) {
                console.log('ℹ Issue already in status', statusId);
                return;
            }

            const issueKey = draggedCard.dataset.issueKey;
            const originalHTML = draggedCard.cloneNode(true);

            // Optimistic UI update
            column.appendChild(draggedCard);
            updateColumnCounts();
            console.log('✓ Moved card to column', statusId);

            // Send to server
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const apiUrl = '<?= url("/api/v1/issues/") ?>' + issueKey + '/transitions';
                
                console.log('📡 API Call:', {
                    url: apiUrl,
                    method: 'POST',
                    body: { status_id: parseInt(statusId) }
                });

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken || ''
                    },
                    body: JSON.stringify({
                        status_id: parseInt(statusId)
                    })
                });

                const data = await response.json();
                console.log('📦 API Response:', data);

                if (!response.ok || !data.success) {
                    console.error('✗ Transition failed:', data);
                    alert('Failed to move issue: ' + (data.error || 'Unknown error'));
                    originalColumn.appendChild(draggedCard);
                    updateColumnCounts();
                } else {
                    console.log('✓ Issue transitioned successfully');
                    updateColumnCounts();
                }
            } catch (error) {
                console.error('✗ Error moving issue:', error);
                alert('Error moving issue: ' + error.message);
                originalColumn.appendChild(draggedCard);
                updateColumnCounts();
            }
        });
    });
}

function updateColumnCounts() {
    document.querySelectorAll('.board-column').forEach(column => {
        const count = column.querySelectorAll('.issue-card').length;
        const countBadge = column.closest('.board-column-container')?.querySelector('.column-count');
        
        if (countBadge) {
            countBadge.textContent = count;
        }

        // Show/hide empty state
        const emptyState = column.querySelector('.empty-column-state');
        if (emptyState) {
            if (count > 0) {
                emptyState.style.display = 'none';
            } else {
                emptyState.style.display = 'flex';
            }
        }
    });
}

// Initialize
function startDragAndDrop() {
    const cards = document.querySelectorAll('.issue-card');
    const columns = document.querySelectorAll('.board-column');
    
    console.log('📊 Board status:', {
        cards: cards.length,
        columns: columns.length,
        projectKey: projectKey,
        ready: cards.length > 0 && columns.length > 0
    });
    
    if (columns.length === 0) {
        console.warn('⚠ Board columns not found, retrying in 500ms...');
        setTimeout(startDragAndDrop, 500);
        return;
    }
    
    initDragAndDrop();
}

// Add Card Button Handler
function initAddCardButtons() {
    console.log('📝 Initializing add card buttons');
    
    document.querySelectorAll('.add-card-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const statusId = btn.dataset.statusId;
            
            // Get status name from the column
            const column = btn.closest('.board-column-container');
            const statusName = column?.querySelector('.column-title')?.textContent || 'Unknown';
            
            console.log('✓ Add card clicked for status:', statusName, '(ID:', statusId, ')');
            
            // Navigate to create issue page with status pre-selected
            const createUrl = '<?= url("/projects/{$project['key']}/issues/create") ?>';
            window.location.href = createUrl;
        });
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        startDragAndDrop();
        initAddCardButtons();
    });
} else {
    setTimeout(() => {
        startDragAndDrop();
        initAddCardButtons();
    }, 100);
}
</script>
<?php \App\Core\View::endSection(); ?>
