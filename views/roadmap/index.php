<?php
/**
 * Global Roadmap View - Enterprise Edition
 * Adheres to strict Jira Clone Design System
 */

declare(strict_types=1);

\App\Core\View:: extends('layouts.app');
\App\Core\View::section('content');

// Helper to calculate days between dates
$dateDiff = function ($start, $end) {
    $d1 = new DateTime($start);
    $d2 = new DateTime($end);
    return $d2->diff($d1)->days;
};

// Timeline Logic
$timelineStart = new DateTime($roadmapData['timeline']['start_date'] ?? 'now');
$timelineEnd = new DateTime($roadmapData['timeline']['end_date'] ?? '+3 months');
$timelineEnd->modify('+1 month');

$totalDays = $timelineStart->diff($timelineEnd)->days + 1;
$dayWidth = 20; // Standardized pixel width
$totalWidth = $totalDays * $dayWidth;

// Generate Month Headers
$months = [];
$period = new DatePeriod($timelineStart, new DateInterval('P1M'), $timelineEnd);
foreach ($period as $dt) {
    $months[] = [
        'name' => $dt->format('M Y'),
        'days' => (int) $dt->format('t'),
        'width' => (int) $dt->format('t') * $dayWidth
    ];
}
?>

<div class="page-wrapper">
    <!-- 1. Breadcrumb Navigation -->
    <div class="breadcrumb-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?= url('/') ?>" class="breadcrumb-link">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Roadmap</li>
            </ol>
        </nav>
    </div>

    <!-- 2. Page Header Section -->
    <div class="page-header">
        <div class="header-left">
            <div class="header-info">
                <h1 class="page-title">Roadmap</h1>
                <p class="page-meta">
                    <?php if ($selectedProject): ?>
                        <span class="badge-key"><?= e($selectedProject['key']) ?></span>
                        <span class="meta-text"><?= e($selectedProject['name']) ?></span>
                    <?php else: ?>
                        <span class="meta-text">Select a project to view timeline</span>
                    <?php endif; ?>
                </p>
                <p class="page-description">
                    Visualize epics, features, and milestones over time. Track progress and dependencies across your
                    project.
                </p>
            </div>
        </div>
        <div class="header-right">
            <div class="header-actions">
                <?php if ($selectedProject): ?>
                    <a href="<?= url("/projects/{$selectedProject['key']}/roadmap") ?>" class="action-button">
                        <i class="bi bi-box-arrow-up-right"></i> Project View
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 3. Quick Actions Bar -->
    <div class="quick-actions-bar">
        <div class="action-group-left">
            <!-- Project Selector -->
            <form action="<?= url('/roadmap') ?>" method="GET" class="project-selector-form d-inline-block">
                <select name="project_id" class="form-select form-select-sm" onchange="this.form.submit()"
                    style="width: 200px;">
                    <option value="">Select Project</option>
                    <?php foreach ($projects as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($selectedProject['id'] ?? null) == $p['id'] ? 'selected' : '' ?>>
                            <?= e($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($selectedProject): ?>
                <button class="btn btn-primary btn-sm ms-2" onclick="showCreateItemModal()">
                    <i class="bi bi-plus-lg"></i> Create Item
                </button>
            <?php endif; ?>
        </div>

        <?php if ($selectedProject): ?>
            <div class="action-group-right">
                <div class="search-wrapper">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="roadmapSearch" placeholder="Search items..."
                        class="form-control form-control-sm search-input">
                </div>
                <select id="statusFilter" class="form-select form-select-sm ms-2" style="width: 140px;">
                    <option value="">All Statuses</option>
                    <option value="planned">Planned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        <?php endif; ?>
    </div>

    <!-- 4. Main Content Area -->
    <div class="page-content">
        <?php if (!$selectedProject): ?>
            <div class="empty-state-card">
                <div class="empty-content">
                    <div class="empty-icon">📊</div>
                    <h3>No Project Selected</h3>
                    <p>Please select a project from the quick actions bar above to load the roadmap.</p>
                </div>
            </div>
        <?php else: ?>

            <div class="content-full">
                <!-- Legend (Inline for this view) -->
                <div class="legend-bar">
                    <span class="legend-label">Legend:</span>
                    <span class="legend-item"><span class="dot epic"></span> Epic</span>
                    <span class="legend-item"><span class="dot feature"></span> Feature</span>
                    <span class="legend-item"><span class="dot milestone"></span> Milestone</span>
                </div>

                <!-- Gantt Chart Card -->
                <div class="gantt-card">
                    <div class="gantt-container">
                        <!-- Sidebar -->
                        <div class="gantt-sidebar">
                            <div class="sidebar-header">
                                <div class="col-name">Item</div>
                                <div class="col-status">Status</div>
                            </div>
                            <div class="sidebar-content">
                                <?php foreach (($roadmapData['items'] ?? []) as $item): ?>
                                    <div class="sidebar-row" data-id="<?= $item['id'] ?>">
                                        <div class="col-name">
                                            <span class="item-type type-<?= $item['type'] ?>"></span>
                                            <span class="text-truncate"
                                                title="<?= e($item['title']) ?>"><?= e($item['title']) ?></span>
                                        </div>
                                        <div class="col-status">
                                            <span
                                                class="badge bg-<?= $item['status'] ?>"><?= str_replace('_', ' ', $item['status']) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($roadmapData['items'])): ?>
                                    <div class="p-3 text-muted text-center small">No items found</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="gantt-timeline-wrapper">
                            <div class="gantt-timeline" style="width: <?= $totalWidth ?>px">
                                <div class="timeline-header">
                                    <?php foreach ($months as $month): ?>
                                        <div class="month-block" style="width: <?= $month['width'] ?>px">
                                            <?= $month['name'] ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="timeline-body">
                                    <div class="grid-lines">
                                        <?php
                                        $currentLeft = 0;
                                        foreach ($months as $month):
                                            $currentLeft += $month['width'];
                                            ?>
                                            <div class="grid-line" style="left: <?= $currentLeft ?>px"></div>
                                        <?php endforeach; ?>

                                        <?php
                                        $todayDiff = (new DateTime())->diff($timelineStart)->days;
                                        $todayLeft = ($todayDiff * $dayWidth);
                                        if ($todayLeft >= 0 && $todayLeft <= $totalWidth):
                                            ?>
                                            <div class="today-line" style="left: <?= $todayLeft ?>px" title="Today"></div>
                                        <?php endif; ?>
                                    </div>

                                    <?php foreach (($roadmapData['items'] ?? []) as $item):
                                        $start = new DateTime($item['start_date']);
                                        $end = new DateTime($item['end_date']);
                                        if ($end < $timelineStart || $start > $timelineEnd)
                                            continue;

                                        $visualStartRaw = $start->getTimestamp() - $timelineStart->getTimestamp();
                                        $visualStartDays = floor($visualStartRaw / (60 * 60 * 24));
                                        $left = max(0, $visualStartDays * $dayWidth);
                                        $durationDays = $end->diff($start)->days + 1;
                                        $width = $durationDays * $dayWidth;
                                        ?>
                                        <div class="timeline-row">
                                            <div class="gantt-bar type-<?= $item['type'] ?>"
                                                style="left: <?= $left ?>px; width: <?= $width ?>px;" data-bs-toggle="tooltip"
                                                title="<?= e($item['title']) ?> (<?= $item['progress_percentage'] ?>%)">
                                                <div class="bar-progress" style="width: <?= $item['progress_percentage'] ?>%">
                                                </div>
                                                <div class="bar-label"><?= e($item['title']) ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for Creating Roadmap Item -->
<div class="modal-overlay" id="createModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h2>Add Roadmap Item</h2>
            <button class="modal-close" type="button" onclick="closeCreateModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-error" id="modalError"></div>
            <div id="createItemForm">
                <input type="hidden" id="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" id="project_id" value="<?= intval($selectedProject['id'] ?? 0) ?>">
                <input type="hidden" id="project_key" value="<?= e($selectedProject['key'] ?? '') ?>">

                <!-- Title -->
                <div class="form-group">
                    <label for="item_title">
                        Title <span class="required">*</span>
                    </label>
                    <input type="text" id="item_title" placeholder="Enter roadmap item title" maxlength="200">
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="item_description">Description</label>
                    <textarea id="item_description" placeholder="Optional description or notes"></textarea>
                </div>

                <!-- Type and Status -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="item_type">
                            Type <span class="required">*</span>
                        </label>
                        <select id="item_type">
                            <option value="">Select Type</option>
                            <option value="epic">Epic</option>
                            <option value="feature">Feature</option>
                            <option value="milestone">Milestone</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item_status">
                            Status <span class="required">*</span>
                        </label>
                        <select id="item_status">
                            <option value="">Select Status</option>
                            <option value="planned">Planned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="on_track">On Track</option>
                            <option value="at_risk">At Risk</option>
                            <option value="delayed">Delayed</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>

                <!-- Dates -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="item_start_date">
                            Start Date <span class="required">*</span>
                        </label>
                        <input type="date" id="item_start_date">
                    </div>
                    <div class="form-group">
                        <label for="item_end_date">
                            End Date <span class="required">*</span>
                        </label>
                        <input type="date" id="item_end_date">
                    </div>
                </div>

                <!-- Progress -->
                <div class="form-group">
                    <label for="item_progress">
                        Progress (%) <span class="required">*</span>
                    </label>
                    <input type="number" id="item_progress" min="0" max="100" value="0">
                    <div class="form-help">Enter a value between 0 and 100</div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" type="button" onclick="closeCreateModal()">Cancel</button>
            <button class="btn-submit" type="button" onclick="submitCreateItem(event)">Create Item</button>
        </div>
    </div>
</div>

<style>
    /* ============================================
   PAGE TITLE - ENTERPRISE DESIGN
   ============================================ */

    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-dark: #161B22 !important;
        --jira-gray: #626F86 !important;
        --jira-light: #F7F8FA !important;
        --jira-border: #DFE1E6 !important;

        /* Gantt Specific */
        --gantt-header-height: 40px;
        --gantt-row-height: 36px;
        --gantt-bg: #ffffff;
        --gantt-border: #dfe1e6;
        --gantt-hover: #f4f5f7;
    }

    /* ============================================
       MODAL STYLES (MATCHING PROJECT ROADMAP)
       ============================================ */
    .modal-overlay {
        display: none !important;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 5000;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex !important;
    }

    .modal-dialog {
        background: white;
        border-radius: 6px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        pointer-events: auto;
        margin-top: 25px !important;
    }

    .modal-header {
        padding: 16px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--jira-dark);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: var(--jira-gray);
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: var(--jira-dark);
    }

    .modal-body {
        padding: 16px;
    }

    .modal-footer {
        padding: 12px 16px;
        border-top: 1px solid var(--jira-border);
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--jira-dark);
        margin-bottom: 4px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        font-size: 13px;
        color: var(--jira-dark);
        background: white;
        font-family: inherit;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .form-row .form-group {
        margin-bottom: 0;
    }

    .form-group .required {
        color: #EF4444;
    }

    .modal-footer button {
        padding: 8px 14px;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-submit {
        background: var(--jira-blue);
        color: white;
    }

    .btn-submit:hover {
        background: var(--jira-blue-dark);
    }

    .btn-cancel {
        background: white;
        color: var(--jira-dark);
        border: 1px solid var(--jira-border);
    }

    .btn-cancel:hover {
        background: var(--jira-light);
    }

    .modal-error {
        display: none;
        background: #FEE2E2;
        border: 1px solid #FECACA;
        color: #7F1D1D;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 12px;
        font-size: 12px;
    }

    .modal-error.show {
        display: block;
    }

    .form-help {
        font-size: 11px;
        color: var(--jira-gray);
        margin-top: 2px;
    }

    body {
        background-color: var(--jira-light);
    }

    .page-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* 1. Breadcrumb */
    .breadcrumb-container {
        background: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        padding: 8px 24px;
    }

    .breadcrumb-link {
        color: var(--jira-gray);
        text-decoration: none;
        font-size: 12px;
        transition: color 0.1s;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue);
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        font-weight: 500;
        color: var(--jira-dark);
        font-size: 12px;
    }

    /* 2. Page Header */
    .page-header {
        background: #FFFFFF;
        padding: 20px 24px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .header-left {
        display: flex;
        gap: 24px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 600;
        color: var(--jira-dark);
        margin: 0 0 4px 0;
        line-height: 1.2;
    }

    .page-meta {
        font-size: 12px;
        color: var(--jira-gray);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .badge-key {
        background: #DFE1E6;
        color: #42526E;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
    }

    .page-description {
        font-size: 13px;
        color: var(--jira-gray);
        margin: 0;
        max-width: 600px;
        line-height: 1.4;
    }

    .action-button {
        background: #FFFFFF;
        border: 1px solid var(--jira-border);
        color: var(--jira-dark);
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .action-button:hover {
        background: var(--jira-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    /* 3. Quick Actions */
    .quick-actions-bar {
        background: #FFFFFF;
        padding: 12px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--jira-border);
    }

    .action-group-left,
    .action-group-right {
        display: flex;
        align-items: center;
    }

    .btn-primary {
        background-color: var(--jira-blue);
        border-color: var(--jira-blue);
    }

    .btn-primary:hover {
        background-color: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
    }

    .search-wrapper {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--jira-gray);
        font-size: 12px;
    }

    .search-input {
        padding-left: 30px;
        width: 200px;
    }

    /* 4. Main Content */
    .page-content {
        padding: 24px;
        background: var(--jira-light);
        flex: 1;
    }

    .empty-state-card {
        background: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        padding: 64px;
        text-align: center;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    /* Gantt Card Container */
    .gantt-card {
        background: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .gantt-container {
        display: flex;
        min-height: 500px;
        /* Force height */
        max-height: calc(100vh - 400px);
        /* Responsive height limit */
    }

    .legend-bar {
        margin-bottom: 16px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 16px;
        color: var(--jira-gray);
    }

    .legend-label {
        font-weight: 600;
    }

    /* Gantt Sidebar & Timeline Re-used Styles (Standardized) */
    .gantt-sidebar {
        width: 280px;
        /* Fixed sidebar width from spec */
        border-right: 1px solid var(--gantt-border);
        background: #fff;
        display: flex;
        flex-direction: column;
        z-index: 2;
    }

    /* Jira-Style Table Layout */
    .sidebar-header {
        height: var(--gantt-header-height);
        display: flex;
        align-items: center;
        border-bottom: 2px solid #dfe1e6;
        background: #f4f5f7;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
        font-size: 11px;
        font-weight: 700;
        color: #6b778c;
        /* Jira header gray */
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sidebar-row {
        height: var(--gantt-row-height);
        display: flex;
        align-items: center;
        border-bottom: 1px solid #dfe1e6;
        background-color: #ffffff;
        font-size: 14px;
        /* Standard row text size */
        color: #172b4d;
        cursor: pointer;
        transition: background-color 0.1s ease;
    }

    .sidebar-row:hover {
        background-color: #ebecf0;
        /* Jira hover gray */
    }

    /* Column Specifics */
    .col-name {
        flex: 1;
        /* Takes remaining space */
        padding: 0 16px;
        display: flex;
        align-items: center;
        overflow: hidden;
        border-right: 1px solid #dfe1e6;
        font-weight: 500;
    }

    .col-status {
        width: 120px;
        /* Fixed status width */
        padding: 0 16px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        /* Left align badges in Jira */
        flex-shrink: 0;
    }

    /* Timeline */
    .gantt-timeline-wrapper {
        flex: 1;
        overflow: auto;
        position: relative;
        background: #fff;
    }

    .gantt-timeline {
        position: relative;
        min-width: 100%;
    }

    .timeline-header {
        height: var(--gantt-header-height);
        display: flex;
        border-bottom: 1px solid var(--gantt-border);
        background: #f4f5f7;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .month-block {
        border-right: 1px solid #dfe1e6;
        display: flex;
        align-items: center;
        padding-left: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #5e6c84;
    }

    .timeline-body {
        position: relative;
        min-height: 100%;
    }

    .grid-lines {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .grid-line {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 1px;
        background: #f4f5f7;
        border-right: 1px dashed #dfe1e6;
    }

    .today-line {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #ff5630;
        z-index: 5;
    }

    .timeline-row {
        height: var(--gantt-row-height);
        border-bottom: 1px solid #f4f5f7;
        position: relative;
        display: flex;
        align-items: center;
    }

    .timeline-row:hover {
        background: rgba(9, 30, 66, 0.02);
    }

    .gantt-bar {
        position: absolute;
        height: 16px;
        border-radius: 2px;
        display: flex;
        align-items: center;
        padding: 0 6px;
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        transition: transform 0.1s, box-shadow 0.1s;
    }

    .gantt-bar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        z-index: 20;
    }

    .gantt-bar.type-epic {
        background: #8777d9;
    }

    .gantt-bar.type-feature {
        background: #4bade8;
    }

    .gantt-bar.type-milestone {
        background: #fdbd3e;
        color: #172b4d;
        width: 20px !important;
        border-radius: 50%;
        padding: 0;
        justify-content: center;
    }

    .gantt-bar.type-milestone .bar-label,
    .bar-progress {
        display: none;
    }

    .bar-progress {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 4px 0 0 4px;
    }

    .bar-label {
        position: relative;
        z-index: 1;
        text-shadow: 0 0 2px rgba(0, 0, 0, 0.3);
    }

    /* Dots */
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 2px;
        display: inline-block;
        margin-right: 4px;
    }

    .dot.epic {
        background: #8777d9;
    }

    .dot.feature {
        background: #4bade8;
    }

    .dot.milestone {
        background: #fdbd3e;
        border-radius: 50%;
    }

    /* Responsive Constants */
    @media (max-width: 1024px) {
        .page-header {
            flex-direction: column;
            gap: 16px;
            padding: 20px;
        }

        .header-left {
            flex-direction: column;
            gap: 12px;
        }

        .page-content {
            padding: 20px;
        }

        .gantt-sidebar {
            width: 200px;
        }
    }

    @media (max-width: 768px) {
        .breadcrumb-container {
            padding: 12px 16px;
        }

        .page-header {
            padding: 16px;
        }

        .page-title {
            font-size: 24px;
        }

        .quick-actions-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            padding: 16px;
        }

        .action-group-right {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .search-input {
            width: 100%;
        }

        #statusFilter {
            width: 100%;
            margin: 0 !important;
        }

        .page-content {
            padding: 16px;
        }

        .gantt-sidebar {
            width: 140px;
        }

        .col-status {
            display: none;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sync scrolling
        const sidebar = document.querySelector('.sidebar-content');
        const timeline = document.querySelector('.gantt-timeline-wrapper');
        if (sidebar && timeline) {
            sidebar.style.overflowY = 'auto';
            timeline.style.overflowY = 'auto';
            sidebar.style.scrollbarWidth = 'none';

            let isSyncingSidebar = false;
            let isSyncingTimeline = false;

            sidebar.addEventListener('scroll', function () {
                if (!isSyncingSidebar) {
                    isSyncingTimeline = true;
                    timeline.scrollTop = this.scrollTop;
                }
                isSyncingSidebar = false;
            });

            timeline.addEventListener('scroll', function () {
                if (!isSyncingTimeline) {
                    isSyncingSidebar = true;
                    sidebar.scrollTop = this.scrollTop;
                }
                isSyncingTimeline = false;
            });
        }

        // Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
    // Custom Modal Functions matching Project Roadmap
    function showCreateItemModal() {
        const modal = document.getElementById('createModal');
        const errorDiv = document.getElementById('modalError');

        if (!modal) return;

        // Clear error & reset form
        errorDiv.classList.remove('show');
        errorDiv.textContent = '';

        // Reset inputs
        const inputs = ['item_title', 'item_description', 'item_type', 'item_status'];
        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });

        const progressEl = document.getElementById('item_progress');
        if (progressEl) progressEl.value = '0';

        // Default dates to today
        const today = new Date().toISOString().split('T')[0];
        const startEl = document.getElementById('item_start_date');
        const endEl = document.getElementById('item_end_date');

        if (startEl) startEl.value = today;
        if (endEl) endEl.value = today;

        modal.classList.add('active');
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        if (modal) modal.classList.remove('active');
    }

    function submitCreateItem(event) {
        const errorDiv = document.getElementById('modalError');
        const submitBtn = event ? event.target : document.querySelector('.btn-submit');
        const csrfToken = document.getElementById('csrf_token').value;
        const projectId = document.getElementById('project_id').value;

        if (!projectId || projectId === '0') {
            errorDiv.textContent = 'Please select a project first.';
            errorDiv.classList.add('show');
            return;
        }

        const title = document.getElementById('item_title').value.trim();
        const description = document.getElementById('item_description').value.trim();
        const type = document.getElementById('item_type').value;
        const status = document.getElementById('item_status').value;
        const startDate = document.getElementById('item_start_date').value;
        const endDate = document.getElementById('item_end_date').value;
        const progress = parseInt(document.getElementById('item_progress').value);

        // Validation
        if (!title || !type || !status || !startDate || !endDate) {
            errorDiv.textContent = 'Please fill in all required fields.';
            errorDiv.classList.add('show');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            errorDiv.textContent = 'Start date must be before end date.';
            errorDiv.classList.add('show');
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';
        }

        // Use PHP generated URL for the current project context
        fetch('<?= url("/projects/" . ($selectedProject['key'] ?? '') . "/roadmap") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                project_id: projectId,
                title, description, type, status, start_date: startDate, end_date: endDate, progress
            })
        })
            .then(response => {
                if (response.ok || response.status === 201) return response.json().catch(() => ({ success: true }));
                return response.text().then(text => Promise.reject(text));
            })
            .then(data => {
                closeCreateModal();
                window.location.reload();
            })
            .catch(err => {
                console.error(err);
                errorDiv.textContent = 'Error creating item: ' + (typeof err === 'string' ? err : err.message || 'Unknown error');
                errorDiv.classList.add('show');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Item';
                }
            });
    }

    // Modal Overlay Click Listener
    document.addEventListener('DOMContentLoaded', function () {
        const modalOverlay = document.getElementById('createModal');
        if (modalOverlay) {
            modalOverlay.addEventListener('click', function (e) {
                if (e.target === modalOverlay) closeCreateModal();
            });
        }
    });
</script>

<?php \App\Core\View::endSection(); ?>