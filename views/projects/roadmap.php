<?php \App\Core\View:: extends('layouts.app'); ?>

<?php
/**
 * Helper function to calculate the width of a Gantt bar based on item dates and timeline range
 */
function calculateBarWidth($item, $timeline)
{
    $startDate = new DateTime($item['start_date']);
    $endDate = new DateTime($item['end_date']);
    $timelineStart = new DateTime($timeline['start_date']);
    $timelineEnd = new DateTime($timeline['end_date']);

    // Calculate total days in timeline
    $totalDays = $timelineStart->diff($timelineEnd)->days;
    if ($totalDays === 0) {
        $totalDays = 1;
    }

    // Calculate item duration in days
    $itemDays = $startDate->diff($endDate)->days;
    if ($itemDays === 0) {
        $itemDays = 1;
    }

    // Calculate percentage width
    $percentage = ($itemDays / $totalDays) * 100;

    // Cap at 100%
    return min($percentage, 100);
}
?>

<?php \App\Core\View::section('content'); ?>

<style>
    /* ============================================
       ROADMAP PAGE - COMPACT DESIGN
       ============================================ */

    :root {
        --jira-blue: #8B1956;
        --jira-blue-dark: #6F123F;
        --jira-dark: #161B22;
        --jira-gray: #626F86;
        --jira-light: #F7F8FA;
        --jira-border: #DFE1E6;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-dialog {
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.16);
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: var(--jira-dark);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--jira-gray);
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: var(--jira-dark);
    }

    .modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }

    .modal-footer {
        padding: 16px 20px;
        border-top: 1px solid var(--jira-border);
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--jira-dark);
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        font-size: 13px;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-help {
        font-size: 11px;
        color: var(--jira-gray);
        margin-top: 4px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .required {
        color: #EF4444;
    }

    /* Modal Error */
    .modal-error {
        display: none;
        padding: 12px;
        background: #FEE2E2;
        border: 1px solid #FECACA;
        border-radius: 4px;
        color: #7F1D1D;
        font-size: 12px;
        margin-bottom: 12px;
    }

    .modal-error.show {
        display: block;
    }

    /* Buttons */
    .btn-cancel,
    .btn-submit {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel {
        background: white;
        border: 1px solid var(--jira-border);
        color: var(--jira-dark);
    }

    .btn-cancel:hover {
        background: var(--jira-light);
        border-color: #B6C2CF;
    }

    .btn-submit {
        background: var(--jira-blue);
        color: white;
    }

    .btn-submit:hover:not(:disabled) {
        background: var(--jira-blue-dark);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    @media (max-width: 480px) {
        .modal-dialog {
            width: 95%;
            max-height: calc(100vh - 40px);
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .page-wrapper {
        background-color: var(--jira-light);
        min-height: calc(100vh - 80px);
        padding: 0;
    }

    /* Breadcrumb */
    .breadcrumb-nav {
        background: white;
        border-bottom: 1px solid var(--jira-border);
        padding: 10px 20px;
        font-size: 12px;
        display: flex;
        gap: 8px;
    }

    .breadcrumb-nav a {
        color: var(--jira-blue);
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb-nav a:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-sep {
        color: var(--jira-gray);
    }

    /* Header */
    .page-header {
        background: white;
        border-bottom: 1px solid var(--jira-border);
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title {
        flex: 1;
    }

    .header-title h1 {
        font-size: 22px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
    }

    .header-title p {
        font-size: 12px;
        color: var(--jira-gray);
        margin: 4px 0 0 0;
    }

    .header-actions {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 8px 12px;
        background: white;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        color: var(--jira-dark);
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-action:hover {
        background: var(--jira-light);
        border-color: #B6C2CF;
    }

    .btn-action.primary {
        background: var(--jira-blue);
        color: white;
        border-color: var(--jira-blue);
    }

    .btn-action.primary:hover {
        background: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
    }

    /* Main Container */
    .page-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 16px 20px;
    }

    /* Metrics Row */
    .metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .metric-box {
        background: white;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        padding: 12px;
        border-left: 3px solid var(--jira-blue);
    }

    .metric-box:hover {
        border-color: #B6C2CF;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .metric-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--jira-gray);
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .metric-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--jira-dark);
    }

    .metric-sub {
        font-size: 11px;
        color: var(--jira-gray);
        margin-top: 2px;
    }

    /* Alert */
    .alert {
        background: #FEE2E2;
        border: 1px solid #FECACA;
        border-radius: 4px;
        padding: 12px;
        margin-bottom: 16px;
        font-size: 12px;
        color: #7F1D1D;
    }

    .alert strong {
        display: block;
        margin-bottom: 2px;
    }

    /* Filters */
    .filters-bar {
        background: white;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        padding: 12px;
        margin-bottom: 16px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 8px;
    }

    .filters-bar select,
    .filters-bar button {
        padding: 6px 10px;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        font-size: 12px;
        background: white;
        color: var(--jira-dark);
        cursor: pointer;
        transition: all 0.2s;
    }

    .filters-bar select:focus,
    .filters-bar button:focus {
        outline: none;
        border-color: var(--jira-blue);
    }

    .filters-bar button:hover {
        background: var(--jira-light);
    }

    /* Gantt Table */
    .gantt-wrapper {
        background: white;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        overflow: hidden;
    }

    .gantt-header {
        background: var(--jira-light);
        padding: 12px;
        border-bottom: 1px solid var(--jira-border);
        font-size: 12px;
        font-weight: 600;
        color: var(--jira-dark);
    }

    .gantt-container {
        overflow-x: auto;
        max-height: 500px;
        overflow-y: auto;
    }

    .gantt-row {
        display: flex;
        border-bottom: 1px solid var(--jira-border);
        height: 50px;
        align-items: center;
        transition: background-color 0.2s;
    }

    .gantt-row:hover {
        background-color: var(--jira-light);
    }

    .gantt-row:last-child {
        border-bottom: none;
    }

    .gantt-info {
        flex: 0 0 250px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 12px;
        border-right: 1px solid var(--jira-border);
        min-width: 250px;
    }

    .gantt-icon {
        width: 24px;
        height: 24px;
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        color: white;
        flex-shrink: 0;
    }

    .gantt-icon.epic {
        background: #6366F1;
    }

    .gantt-icon.feature {
        background: var(--jira-blue);
    }

    .gantt-icon.milestone {
        background: #E77817;
    }

    .gantt-title {
        font-size: 12px;
        font-weight: 500;
        color: var(--jira-dark);
        cursor: pointer;
        transition: color 0.2s;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .gantt-title:hover {
        color: var(--jira-blue);
        text-decoration: underline;
    }

    .gantt-meta {
        font-size: 11px;
        color: var(--jira-gray);
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .gantt-timeline {
        flex: 1;
        display: flex;
        align-items: center;
        padding: 0 12px;
        min-width: 300px;
        position: relative;
    }

    .gantt-bar {
        height: 28px;
        border-radius: 3px;
        position: relative;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 0 4px;
    }

    .gantt-bar:hover {
        transform: scaleY(1.2);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .gantt-bar.planned {
        background: #9CA3AF;
    }

    .gantt-bar.in_progress {
        background: #3B82F6;
    }

    .gantt-bar.on_track {
        background: #10B981;
    }

    .gantt-bar.at_risk {
        background: #F59E0B;
    }

    .gantt-bar.delayed {
        background: #EF4444;
    }

    .gantt-bar.completed {
        background: #6B7280;
    }

    .gantt-progress {
        position: absolute;
        height: 100%;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px 0 0 3px;
        transition: width 0.3s ease;
    }

    .gantt-status {
        flex: 0 0 110px;
        padding: 0 12px;
        text-align: center;
        border-left: 1px solid var(--jira-border);
    }

    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.planned {
        background: #E5E7EB;
        color: #374151;
    }

    .status-badge.in_progress {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .status-badge.on_track {
        background: #DCFCE7;
        color: #065F46;
    }

    .status-badge.at_risk {
        background: #FEF3C7;
        color: #92400E;
    }

    .status-badge.delayed {
        background: #FEE2E2;
        color: #7F1D1D;
    }

    .status-badge.completed {
        background: #F3F4F6;
        color: #374151;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--jira-gray);
    }

    .empty-state-icon {
        font-size: 40px;
        margin-bottom: 8px;
    }

    .empty-state-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--jira-dark);
        margin: 0 0 4px 0;
    }

    .empty-state-text {
        font-size: 12px;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .header-actions {
            width: 100%;
        }

        .metrics-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .filters-bar {
            grid-template-columns: 1fr;
        }

        .gantt-info {
            flex: 0 0 200px;
            min-width: 200px;
        }

        .gantt-title {
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .page-header {
            padding: 12px;
        }

        .page-container {
            padding: 12px;
        }

        .header-title h1 {
            font-size: 18px;
        }

        .metrics-row {
            grid-template-columns: 1fr;
        }

        .gantt-info {
            flex: 0 0 150px;
            min-width: 150px;
        }

        .gantt-meta {
            display: none;
        }

        .filters-bar {
            grid-template-columns: 1fr;
        }
    }

    /* ============================================
       MODAL STYLES
       ============================================ */

    .modal-overlay {
        display: none !important;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
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
</style>

<div class="page-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-nav">
        <a href="<?= url('/dashboard') ?>">Dashboard</a>
        <span class="breadcrumb-sep">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>"><?= htmlspecialchars($project['name']) ?></a>
        <span class="breadcrumb-sep">/</span>
        <span>Roadmap</span>
    </div>

    <!-- Header -->
    <div class="page-header">
        <div class="header-title">
            <h1>Project Roadmap</h1>
            <p>Strategic timeline and milestone tracking</p>
        </div>
        <div class="header-actions">
            <?php if (can('issues.create', $project['id'])): ?>
                <button class="btn-action primary" onclick="showCreateItemModal()">
                    <i class="bi bi-plus-lg"></i> Add Item
                </button>
            <?php endif; ?>
            <a href="<?= url('/roadmap?project=' . $project['key']) ?>" class="btn-action"
                style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="bi bi-globe"></i> Global Roadmap
            </a>
            <button class="btn-action" onclick="exportRoadmap()">
                <i class="bi bi-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Content -->
    <div class="page-container">
        <!-- Metrics -->
        <div class="metrics-row">
            <div class="metric-box">
                <div class="metric-label">Total Items</div>
                <div class="metric-value"><?= $summary['total_items'] ?></div>
                <div class="metric-sub"><?= $summary['at_risk_count'] ?> at risk</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Progress</div>
                <div class="metric-value"><?= $summary['average_progress'] ?>%</div>
                <div class="metric-sub"><?= $summary['completed_issues'] ?>/<?= $summary['total_issues'] ?> done</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Est. Hours</div>
                <div class="metric-value"><?= number_format($summary['total_estimated_hours'], 0) ?></div>
                <div class="metric-sub"><?= number_format($summary['total_actual_hours'], 0) ?> logged</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Completion</div>
                <div class="metric-value"><?= $summary['issue_completion_rate'] ?>%</div>
                <div class="metric-sub">Done / Total</div>
            </div>
        </div>

        <!-- Alert -->
        <?php if ($summary['at_risk_count'] > 0): ?>
            <div class="alert">
                <strong>⚠️ <?= $summary['at_risk_count'] ?> items at risk</strong>
                Check status and dependencies to keep the project on track
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filters-bar">
            <select onchange="applyFilters()">
                <option value="">All Status</option>
                <option value="planned" <?= $filters['status'] === 'planned' ? 'selected' : '' ?>>Planned</option>
                <option value="in_progress" <?= $filters['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress
                </option>
                <option value="on_track" <?= $filters['status'] === 'on_track' ? 'selected' : '' ?>>On Track</option>
                <option value="at_risk" <?= $filters['status'] === 'at_risk' ? 'selected' : '' ?>>At Risk</option>
                <option value="delayed" <?= $filters['status'] === 'delayed' ? 'selected' : '' ?>>Delayed</option>
                <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <select onchange="applyFilters()">
                <option value="">All Types</option>
                <option value="epic" <?= $filters['type'] === 'epic' ? 'selected' : '' ?>>Epic</option>
                <option value="feature" <?= $filters['type'] === 'feature' ? 'selected' : '' ?>>Feature</option>
                <option value="milestone" <?= $filters['type'] === 'milestone' ? 'selected' : '' ?>>Milestone</option>
            </select>
            <select onchange="applyFilters()">
                <option value="">All Owners</option>
                <?php foreach ($projectMembers as $member): ?>
                    <option value="<?= htmlspecialchars($member['user_id']) ?>" <?= $filters['owner_id'] == $member['user_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($member['user_name'] ?? 'Unknown') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn-action" onclick="clearFilters()">Clear</button>
        </div>

        <!-- Gantt Table -->
        <div class="gantt-wrapper">
            <div class="gantt-header">📊 Timeline</div>
            <?php if (!empty($roadmapItems)): ?>
                <div class="gantt-container">
                    <?php foreach ($roadmapItems as $item): ?>
                        <div class="gantt-row">
                            <div class="gantt-info">
                                <div class="gantt-icon <?= htmlspecialchars($item['type']) ?>">
                                    <?= htmlspecialchars(strtoupper(substr($item['type'], 0, 1))) ?>
                                </div>
                                <div>
                                    <div class="gantt-title" onclick="showItemDetail(<?= intval($item['id']) ?>)">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </div>
                                    <div class="gantt-meta">
                                        <?= date('M d', strtotime($item['start_date'])) ?> -
                                        <?= date('M d', strtotime($item['end_date'])) ?> •
                                        <?= intval($item['progress_percentage'] ?? $item['progress']['percentage'] ?? 0) ?>%
                                    </div>
                                </div>
                            </div>

                            <div class="gantt-timeline">
                                <div class="gantt-bar <?= htmlspecialchars($item['status']) ?>"
                                    style="width: <?= calculateBarWidth($item, $timeline) ?>%"
                                    title="<?= htmlspecialchars($item['title']) ?>"
                                    onclick="showItemDetail(<?= intval($item['id']) ?>)">
                                    <div class="gantt-progress"
                                        style="width: <?= intval($item['progress_percentage'] ?? $item['progress']['percentage'] ?? 0) ?>%">
                                    </div>
                                    <span style="position: relative; z-index: 2;">
                                        <?= date('M d', strtotime($item['start_date'])) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="gantt-status">
                                <span class="status-badge <?= htmlspecialchars($item['status']) ?>">
                                    <?= str_replace('_', ' ', htmlspecialchars($item['status'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <div class="empty-state-title">No roadmap items</div>
                    <div class="empty-state-text">Create your first roadmap item to get started</div>
                </div>
            <?php endif; ?>
        </div>
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
                <input type="hidden" id="project_id" value="<?= intval($project['id']) ?>">

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

<script>


    function showCreateItemModal() {
        console.log('[ROADMAP MODAL] Opening modal');

        const modal = document.getElementById('createModal');
        const errorDiv = document.getElementById('modalError');

        if (!modal) {
            console.error('[ROADMAP MODAL] Modal element not found!');
            return;
        }

        // Clear error
        errorDiv.classList.remove('show');
        errorDiv.textContent = '';

        // Reset form fields
        document.getElementById('item_title').value = '';
        document.getElementById('item_description').value = '';
        document.getElementById('item_type').value = '';
        document.getElementById('item_status').value = '';
        document.getElementById('item_progress').value = '0';

        // Set default dates to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('item_start_date').value = today;
        document.getElementById('item_end_date').value = today;

        // Show modal
        modal.classList.add('active');
        console.log('[ROADMAP MODAL] Modal opened, active class added');
        console.log('[ROADMAP MODAL] Modal display:', window.getComputedStyle(modal).display);
    }

    function closeCreateModal() {
        console.log('[ROADMAP MODAL] Closing modal');
        const modal = document.getElementById('createModal');
        modal.classList.remove('active');
        console.log('[ROADMAP MODAL] Modal closed, active class removed');
    }

    function submitCreateItem(event) {
        console.log('[ROADMAP MODAL] submitCreateItem() called');

        const errorDiv = document.getElementById('modalError');
        const submitBtn = event ? event.target : document.querySelector('.btn-submit');

        // Get form values
        const title = document.getElementById('item_title').value.trim();
        const description = document.getElementById('item_description').value.trim();
        const type = document.getElementById('item_type').value;
        const status = document.getElementById('item_status').value;
        const startDate = document.getElementById('item_start_date').value;
        const endDate = document.getElementById('item_end_date').value;
        const progress = document.getElementById('item_progress').value;
        const csrfToken = document.getElementById('csrf_token').value;
        const projectId = document.getElementById('project_id').value;

        console.log('[ROADMAP MODAL] Form values:', { title, type, status, startDate, endDate });

        // Validate required fields
        if (!title) {
            console.log('[ROADMAP MODAL] Validation failed: Title is empty');
            errorDiv.textContent = 'Title is required';
            errorDiv.classList.add('show');
            return;
        }

        if (!type) {
            console.log('[ROADMAP MODAL] Validation failed: Type is empty');
            errorDiv.textContent = 'Type is required';
            errorDiv.classList.add('show');
            return;
        }

        if (!status) {
            console.log('[ROADMAP MODAL] Validation failed: Status is empty');
            errorDiv.textContent = 'Status is required';
            errorDiv.classList.add('show');
            return;
        }

        if (!startDate) {
            console.log('[ROADMAP MODAL] Validation failed: Start date is empty');
            errorDiv.textContent = 'Start date is required';
            errorDiv.classList.add('show');
            return;
        }

        if (!endDate) {
            console.log('[ROADMAP MODAL] Validation failed: End date is empty');
            errorDiv.textContent = 'End date is required';
            errorDiv.classList.add('show');
            return;
        }

        // Validate dates
        const start = new Date(startDate);
        const end = new Date(endDate);

        if (start > end) {
            console.log('[ROADMAP MODAL] Validation failed: Start date after end date');
            errorDiv.textContent = 'Start date must be before end date';
            errorDiv.classList.add('show');
            return;
        }

        // Validate progress
        const prog = parseInt(progress);
        if (prog < 0 || prog > 100) {
            console.log('[ROADMAP MODAL] Validation failed: Progress out of range');
            errorDiv.textContent = 'Progress must be between 0 and 100';
            errorDiv.classList.add('show');
            return;
        }

        console.log('[ROADMAP MODAL] All validations passed, submitting...');
        console.log('[ROADMAP MODAL] Sending POST to:', '<?= url("/projects/{$project['key']}/roadmap") ?>');

        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';
        }

        // Send to API - use correct endpoint format
        console.log('[ROADMAP MODAL] Submitting to URL:', '<?= url("/projects/{$project['key']}/roadmap") ?>');
        console.log('[ROADMAP MODAL] Data being sent:', JSON.stringify({
            project_id: parseInt(projectId),
            title: title,
            description: description,
            type: type,
            status: status,
            start_date: startDate,
            end_date: endDate,
            progress: prog
        }));

        fetch('<?= url("/projects/{$project['key']}/roadmap") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                project_id: parseInt(projectId),
                title: title,
                description: description,
                type: type,
                status: status,
                start_date: startDate,
                end_date: endDate,
                progress: prog
            })
        })
            .then(response => {
                console.log('[ROADMAP MODAL] Response status:', response.status);
                console.log('[ROADMAP MODAL] Response headers:', response.headers.get('content-type'));

                // Check content type
                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');

                // If JSON response, parse it
                if (isJson) {
                    return response.json().then(data => ({
                        ...data,
                        _status: response.status,
                        _isJson: true
                    }));
                }

                // If not JSON (redirect, etc.), just return status
                if (response.status === 201 || response.status === 302 || response.status === 200) {
                    console.log('[ROADMAP MODAL] HTTP ' + response.status + ' - Success!');
                    return { success: true, _status: response.status, _isJson: false };
                }

                // Error response
                return response.text().then(text => ({
                    error: 'Server error: ' + text.substring(0, 200),
                    _status: response.status,
                    _isJson: false
                }));
            })
            .then(result => {
                console.log('[ROADMAP MODAL] Response result:', result);

                // Check if request was successful
                const isSuccess = result && (result.success || result.status === 'success' || result._status === 201 || result._status === 302 || result._status === 200);

                if (isSuccess) {
                    console.log('[ROADMAP MODAL] ✅ Success! Closing modal and reloading...');
                    console.log('[ROADMAP MODAL] Result data:', result);

                    // Close modal and reload page
                    closeCreateModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    // Error occurred
                    const errorMsg = result?.message || result?.error || 'Failed to create roadmap item';
                    console.log('[ROADMAP MODAL] ❌ Error:', errorMsg);
                    console.log('[ROADMAP MODAL] Full result:', result);

                    errorDiv.textContent = errorMsg;
                    errorDiv.classList.add('show');

                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Create Item';
                    }
                }
            })
            .catch(error => {
                console.error('[ROADMAP MODAL] Fetch error:', error);
                errorDiv.textContent = 'Error: ' + error.message + '. This feature requires API endpoint implementation.';
                errorDiv.classList.add('show');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Item';
                }
            });
    }

    // Setup modal event listeners when page loads
    document.addEventListener('DOMContentLoaded', function () {
        console.log('[ROADMAP MODAL] DOMContentLoaded fired, setting up modal listeners');

        const modalOverlay = document.getElementById('createModal');
        const modalDialog = document.querySelector('.modal-dialog');

        if (!modalOverlay) {
            console.error('[ROADMAP MODAL] Modal overlay not found!');
            return;
        }

        if (!modalDialog) {
            console.error('[ROADMAP MODAL] Modal dialog not found!');
            return;
        }

        console.log('[ROADMAP MODAL] Modal elements found, attaching listeners');

        // Close modal when clicking on overlay background (not the dialog itself)
        // Use BUBBLING phase (false) not CAPTURE phase to allow onclick handlers to fire
        modalOverlay.addEventListener('click', function (event) {
            console.log('[ROADMAP MODAL] Click detected on overlay, target:', event.target);

            // Only close if clicking directly on the overlay background
            if (event.target === modalOverlay) {
                console.log('[ROADMAP MODAL] Closing because overlay was clicked');
                closeCreateModal();
            }
        }, false); // FIXED: Use bubbling phase to allow onclick handlers to execute

        console.log('[ROADMAP MODAL] Event listeners successfully attached');
    });

    function showItemDetail(itemId) {
        // Check permission - passed from PHP
        const canEdit = <?= json_encode(can('issues.edit', $project['id'])) ?>;

        fetch(`<?= url("/api/v1/roadmap/{$project['id']}/items/") ?>${itemId}`)
            .then(r => r.json())
            .then(d => {
                console.log('Item:', d.item);
                if (canEdit) {
                    // Logic to open edit modal would go here
                    // For now, we will just log as the edit modal isn't fully implemented in the provided code
                    console.log('User can edit item ' + itemId);
                } else {
                    console.log('Read-only view for item ' + itemId);
                    // Optionally disable inputs or show read-only modal
                }
            })
            .catch(e => console.error('Error:', e));
    }

    function exportRoadmap() {
        alert('Export functionality coming soon');
    }

    function applyFilters() {
        const params = new URLSearchParams();
        window.location.search = params.toString();
    }

    function clearFilters() {
        window.location = window.location.pathname;
    }
</script>

<?php \App\Core\View::endSection(); ?>