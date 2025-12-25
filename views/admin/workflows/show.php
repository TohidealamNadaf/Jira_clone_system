<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="wf-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <nav class="wf-breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-gear"></i> Administration
        </a>
        <span class="breadcrumb-sep">/</span>
        <a href="<?= url('/admin/workflows') ?>" class="breadcrumb-link">Workflows</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current"><?= e($workflow['name']) ?></span>
    </nav>

    <!-- Page Header with Icon -->
    <div class="wf-header">
        <div class="wf-header-left">
            <div class="wf-icon-box">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div class="wf-header-content">
                <h1 class="wf-title"><?= e($workflow['name']) ?></h1>
                <p class="wf-subtitle"><?= e($workflow['description'] ?: 'No description provided') ?></p>
                <div class="wf-meta">
                    <span class="wf-meta-item">
                        <i class="bi bi-hexagon-fill"></i>
                        <?= count($statuses) ?> Statuses
                    </span>
                    <span class="wf-meta-item">
                        <i class="bi bi-arrow-left-right"></i>
                        <?= count($transitions) ?> Transitions
                    </span>
                </div>
            </div>
        </div>
        <div class="wf-header-actions">
            <a href="<?= url('/admin/workflows') ?>" class="wf-action-btn wf-action-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <button class="wf-action-btn wf-action-primary" onclick="alert('Edit workflow feature coming soon!')">
                <i class="bi bi-pencil"></i>
                Edit Workflow
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="wf-content">
        <!-- Workflow Visualization Card -->
        <div class="wf-card wf-card-full">
            <div class="wf-card-header">
                <h2 class="wf-card-title">
                    <i class="bi bi-diagram-3"></i>
                    Workflow Visualization
                </h2>
                <p class="wf-card-subtitle">Visual representation of statuses and transitions</p>
            </div>
            <div class="wf-card-body">
                <div class="wf-visualizer">
                    <div class="wf-viz-header">
                        <i class="bi bi-diagram-3"></i>
                        <span>Workflow Diagram</span>
                    </div>
                    <p class="wf-viz-description">
                        This workflow contains <?= count($statuses) ?> statuses connected by <?= count($transitions) ?> transitions, 
                        defining the possible paths for issue progression.
                    </p>
                    <div class="wf-diagram">
                        <?php foreach ($statuses as $index => $status): ?>
                            <div class="wf-diagram-node" style="background-color: <?= e($status['color'] ?? '#DFE1E6') ?>15; border-color: <?= e($status['color'] ?? '#DFE1E6') ?>; color: <?= e($status['color'] ?? '#626F86') ?>">
                                <div class="wf-node-name"><?= e($status['name']) ?></div>
                                <?php if ($status['is_initial'] ?? false): ?>
                                    <div class="wf-node-badge">Start</div>
                                <?php endif; ?>
                            </div>
                            <?php if ($index < count($statuses) - 1): ?>
                                <div class="wf-diagram-arrow">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="wf-grid">
            <!-- Statuses Section -->
            <div class="wf-card">
                <div class="wf-card-header">
                    <h2 class="wf-card-title">
                        <i class="bi bi-circle-fill"></i>
                        Statuses
                    </h2>
                    <span class="wf-card-badge"><?= count($statuses) ?></span>
                </div>
                <div class="wf-card-body wf-table-body">
                    <table class="wf-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Category</th>
                                <th class="wf-text-center">Initial</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($statuses as $status): ?>
                                <tr>
                                    <td>
                                        <div class="wf-status-cell">
                                            <span class="wf-status-dot" style="background-color: <?= e($status['color'] ?? '#626F86') ?>"></span>
                                            <strong><?= e($status['name']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="wf-category-badge wf-cat-<?= e($status['category']) ?>">
                                            <?= e(ucfirst($status['category'])) ?>
                                        </span>
                                    </td>
                                    <td class="wf-text-center">
                                        <?php if ($status['is_initial'] ?? false): ?>
                                            <i class="bi bi-check-circle-fill wf-text-success"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Stats Section -->
            <div class="wf-card">
                <div class="wf-card-header">
                    <h2 class="wf-card-title">
                        <i class="bi bi-info-circle"></i>
                        Quick Stats
                    </h2>
                </div>
                <div class="wf-card-body">
                    <div class="wf-stats-grid">
                        <div class="wf-stat-item">
                            <div class="wf-stat-icon">
                                <i class="bi bi-circle"></i>
                            </div>
                            <div class="wf-stat-content">
                                <div class="wf-stat-value"><?= count($statuses) ?></div>
                                <div class="wf-stat-label">Total Statuses</div>
                            </div>
                        </div>
                        <div class="wf-stat-item">
                            <div class="wf-stat-icon">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                            <div class="wf-stat-content">
                                <div class="wf-stat-value"><?= count($transitions) ?></div>
                                <div class="wf-stat-label">Total Transitions</div>
                            </div>
                        </div>
                        <div class="wf-stat-item">
                            <div class="wf-stat-icon">
                                <i class="bi bi-play-circle"></i>
                            </div>
                            <div class="wf-stat-content">
                                <div class="wf-stat-value">
                                    <?php 
                                        $initialCount = 0;
                                        foreach ($statuses as $status) {
                                            if ($status['is_initial'] ?? false) $initialCount++;
                                        }
                                        echo $initialCount;
                                    ?>
                                </div>
                                <div class="wf-stat-label">Initial Status(es)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transitions Section -->
        <div class="wf-card wf-card-full">
            <div class="wf-card-header">
                <h2 class="wf-card-title">
                    <i class="bi bi-arrow-left-right"></i>
                    Transitions
                </h2>
                <span class="wf-card-badge"><?= count($transitions) ?></span>
            </div>
            <div class="wf-card-body wf-table-body">
                <?php if (empty($transitions)): ?>
                    <div class="wf-empty-state">
                        <i class="bi bi-arrow-left-right"></i>
                        <h4>No Transitions Defined</h4>
                        <p>This workflow doesn't have any transitions yet.</p>
                    </div>
                <?php else: ?>
                    <table class="wf-table">
                        <thead>
                            <tr>
                                <th>Transition</th>
                                <th>From Status</th>
                                <th></th>
                                <th>To Status</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transitions as $transition): ?>
                                <tr>
                                    <td>
                                        <strong class="wf-transition-name"><?= e($transition['name']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($transition['from_status_id']): ?>
                                            <span class="wf-badge wf-badge-outline"><?= e($transition['from_status_name']) ?></span>
                                        <?php else: ?>
                                            <span class="wf-badge wf-badge-any">
                                                <i class="bi bi-asterisk"></i> Any Status
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="wf-text-center">
                                        <i class="bi bi-arrow-right"></i>
                                    </td>
                                    <td>
                                        <span class="wf-badge wf-badge-outline"><?= e($transition['to_status_name']) ?></span>
                                    </td>
                                    <td class="wf-text-muted"><?= e($transition['description'] ?: '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --wf-primary: #8B1956;
        --wf-primary-dark: #6F123F;
        --wf-primary-light: #F0DCE5;
        --wf-dark: #161B22;
        --wf-gray: #626F86;
        --wf-light: #F7F8FA;
        --wf-border: #DFE1E6;
        --wf-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
        --wf-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
        --wf-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ============================================
       MAIN WRAPPER & LAYOUT
       ============================================ */

    .wf-page-wrapper {
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 80px);
        background: var(--wf-light);
    }

    .wf-breadcrumb {
        padding: 12px 32px;
        background: white;
        border-bottom: 1px solid var(--wf-border);
        font-size: 13px;
        display: flex;
        align-items: center;
    }

    .breadcrumb-link {
        color: var(--wf-primary);
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: var(--wf-transition);
    }

    .breadcrumb-link:hover {
        color: var(--wf-primary-dark);
        text-decoration: underline;
    }

    .breadcrumb-sep {
        color: var(--wf-gray);
        margin: 0 8px;
    }

    .breadcrumb-current {
        color: var(--wf-dark);
        font-weight: 600;
    }

    /* ============================================
       PAGE HEADER
       ============================================ */

    .wf-header {
        padding: 32px;
        background: white;
        border-bottom: 1px solid var(--wf-border);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 32px;
    }

    .wf-header-left {
        display: flex;
        gap: 24px;
        flex: 1;
    }

    .wf-icon-box {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--wf-primary-light) 0%, #F8E8F0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 32px;
        color: var(--wf-primary);
    }

    .wf-header-content {
        flex: 1;
    }

    .wf-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--wf-dark);
        margin: 0 0 8px 0;
        letter-spacing: -0.2px;
    }

    .wf-subtitle {
        font-size: 15px;
        color: var(--wf-gray);
        margin: 0 0 12px 0;
    }

    .wf-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .wf-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--wf-gray);
        font-weight: 500;
    }

    .wf-header-actions {
        display: flex;
        gap: 12px;
        flex-shrink: 0;
    }

    /* ============================================
       ACTION BUTTONS
       ============================================ */

    .wf-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 6px;
        border: 1px solid var(--wf-border);
        background: white;
        color: var(--wf-dark);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: var(--wf-transition);
        cursor: pointer;
        white-space: nowrap;
    }

    .wf-action-btn:hover {
        background: var(--wf-light);
        border-color: var(--wf-primary);
        color: var(--wf-primary);
    }

    .wf-action-secondary {
        border-color: var(--wf-border);
    }

    .wf-action-secondary:hover {
        background: var(--wf-light);
    }

    .wf-action-primary {
        background: var(--wf-primary);
        color: white;
        border: none;
    }

    .wf-action-primary:hover {
        background: var(--wf-primary-dark);
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.2);
        transform: translateY(-2px);
    }

    /* ============================================
       CONTENT AREA
       ============================================ */

    .wf-content {
        padding: 32px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .wf-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    /* ============================================
       CARDS
       ============================================ */

    .wf-card {
        background: white;
        border: 1px solid var(--wf-border);
        border-radius: 8px;
        box-shadow: var(--wf-shadow-sm);
        overflow: hidden;
        transition: var(--wf-transition);
    }

    .wf-card:hover {
        box-shadow: var(--wf-shadow-md);
    }

    .wf-card-full {
        grid-column: span 2;
    }

    .wf-card-header {
        padding: 20px;
        border-bottom: 1px solid var(--wf-border);
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .wf-card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--wf-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.1px;
    }

    .wf-card-title i {
        color: var(--wf-primary);
        font-size: 18px;
    }

    .wf-card-subtitle {
        font-size: 12px;
        color: var(--wf-gray);
        margin: 8px 0 0 0;
    }

    .wf-card-badge {
        display: inline-block;
        background: var(--wf-light);
        color: var(--wf-gray);
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .wf-card-body {
        padding: 20px;
    }

    .wf-table-body {
        padding: 0;
    }

    /* ============================================
       VISUALIZATION
       ============================================ */

    .wf-visualizer {
        background: #F4F5F7;
        border: 2px dashed var(--wf-border);
        border-radius: 8px;
        padding: 40px 24px;
        text-align: center;
    }

    .wf-viz-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 12px;
        color: var(--wf-dark);
    }

    .wf-viz-header i {
        color: var(--wf-primary);
        font-size: 20px;
    }

    .wf-viz-description {
        font-size: 13px;
        color: var(--wf-gray);
        margin-bottom: 24px;
    }

    .wf-diagram {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .wf-diagram-node {
        padding: 12px 16px;
        border: 2px solid;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        min-width: 80px;
        position: relative;
        text-align: center;
        transition: var(--wf-transition);
    }

    .wf-diagram-node:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .wf-node-name {
        font-weight: 600;
    }

    .wf-node-badge {
        font-size: 10px;
        background: white;
        padding: 2px 6px;
        border-radius: 3px;
        margin-top: 4px;
        opacity: 0.8;
    }

    .wf-diagram-arrow {
        color: var(--wf-gray);
        font-size: 16px;
        opacity: 0.5;
    }

    /* ============================================
       STATS
       ============================================ */

    .wf-stats-grid {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .wf-stat-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px;
        background: var(--wf-light);
        border-radius: 6px;
        transition: var(--wf-transition);
    }

    .wf-stat-item:hover {
        background: #ECEEF1;
    }

    .wf-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--wf-primary);
        font-size: 18px;
        flex-shrink: 0;
    }

    .wf-stat-content {
        flex: 1;
    }

    .wf-stat-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--wf-dark);
        margin-bottom: 2px;
    }

    .wf-stat-label {
        font-size: 12px;
        color: var(--wf-gray);
        font-weight: 500;
    }

    /* ============================================
       TABLES
       ============================================ */

    .wf-table {
        width: 100%;
        border-collapse: collapse;
    }

    .wf-table thead th {
        text-align: left;
        padding: 12px 20px;
        background: var(--wf-light);
        color: var(--wf-gray);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        border-bottom: 1px solid var(--wf-border);
    }

    .wf-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid var(--wf-border);
        vertical-align: middle;
        font-size: 14px;
    }

    .wf-table tbody tr:hover {
        background: var(--wf-light);
    }

    /* ============================================
       STATUS CELLS
       ============================================ */

    .wf-status-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }

    .wf-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .wf-category-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .wf-cat-todo {
        background: #EAEBEF;
        color: #42526E;
    }

    .wf-cat-inprogress {
        background: #DEEBFF;
        color: #0747A6;
    }

    .wf-cat-done {
        background: #E3FCEF;
        color: #006644;
    }

    /* ============================================
       BADGES
       ============================================ */

    .wf-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .wf-badge-outline {
        border: 1px solid var(--wf-border);
        color: var(--wf-dark);
        background: white;
    }

    .wf-badge-any {
        background: #FFF0B3;
        color: #825C00;
        border: 1px solid #FFE580;
    }

    /* ============================================
       TRANSITION CELLS
       ============================================ */

    .wf-transition-name {
        color: var(--wf-dark);
        font-weight: 600;
    }

    /* ============================================
       EMPTY STATE
       ============================================ */

    .wf-empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--wf-gray);
    }

    .wf-empty-state i {
        font-size: 48px;
        color: var(--wf-border);
        margin-bottom: 16px;
        display: block;
    }

    .wf-empty-state h4 {
        font-size: 16px;
        font-weight: 600;
        color: var(--wf-dark);
        margin: 0 0 8px 0;
    }

    .wf-empty-state p {
        font-size: 13px;
        color: var(--wf-gray);
        margin: 0;
    }

    /* ============================================
       UTILITIES
       ============================================ */

    .wf-text-center {
        text-align: center !important;
    }

    .wf-text-muted {
        color: var(--wf-gray);
        font-size: 13px;
    }

    .wf-text-success {
        color: #36B37E !important;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */

    @media (max-width: 1024px) {
        .wf-header {
            flex-direction: column;
            padding: 24px;
        }

        .wf-header-left {
            width: 100%;
        }

        .wf-header-actions {
            width: 100%;
        }

        .wf-action-btn {
            flex: 1;
            justify-content: center;
        }

        .wf-content {
            padding: 20px;
            gap: 20px;
        }

        .wf-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .wf-card-full {
            grid-column: span 1;
        }
    }

    @media (max-width: 768px) {
        .wf-breadcrumb {
            padding: 12px 16px;
            font-size: 12px;
            overflow-x: auto;
        }

        .wf-header {
            padding: 16px;
        }

        .wf-icon-box {
            width: 64px;
            height: 64px;
            font-size: 24px;
        }

        .wf-title {
            font-size: 24px;
        }

        .wf-meta {
            flex-direction: column;
            gap: 8px;
        }

        .wf-content {
            padding: 16px;
            gap: 16px;
        }

        .wf-card-header {
            padding: 16px;
            flex-direction: column;
            gap: 8px;
        }

        .wf-card-body {
            padding: 16px;
        }

        .wf-table thead th {
            padding: 10px 12px;
            font-size: 10px;
        }

        .wf-table tbody td {
            padding: 12px;
            font-size: 13px;
        }

        .wf-diagram {
            gap: 6px;
        }

        .wf-diagram-node {
            padding: 8px 12px;
            font-size: 11px;
            min-width: 70px;
        }

        .wf-stats-grid {
            gap: 12px;
        }

        .wf-stat-item {
            padding: 10px;
        }

        .wf-stat-value {
            font-size: 18px;
        }
    }

    @media (max-width: 480px) {
        .wf-header {
            padding: 12px;
            gap: 16px;
        }

        .wf-icon-box {
            width: 56px;
            height: 56px;
            font-size: 20px;
        }

        .wf-header-content {
            min-width: 0;
        }

        .wf-title {
            font-size: 20px;
        }

        .wf-subtitle {
            font-size: 13px;
        }

        .wf-header-actions {
            flex-direction: column;
            width: 100%;
            gap: 8px;
        }

        .wf-action-btn {
            width: 100%;
            font-size: 13px;
            padding: 8px 12px;
        }

        .wf-breadcrumb {
            padding: 8px 12px;
        }

        .breadcrumb-link,
        .breadcrumb-sep {
            display: none;
        }

        .breadcrumb-link:first-child {
            display: flex;
        }

        .wf-diagram {
            flex-direction: column;
        }

        .wf-diagram-arrow {
            transform: rotate(90deg);
        }

        .wf-table {
            font-size: 12px;
        }

        .wf-table thead {
            display: none;
        }

        .wf-table tbody td {
            display: block;
            text-align: right;
            padding-left: 50%;
            position: relative;
            border: none;
            border-bottom: 1px solid var(--wf-border);
        }

        .wf-table tbody td::before {
            content: attr(data-label);
            position: absolute;
            left: 12px;
            font-weight: 600;
            color: var(--wf-gray);
        }

        .wf-table tbody tr {
            display: block;
            margin-bottom: 12px;
            border: 1px solid var(--wf-border);
            border-radius: 4px;
            padding: 12px 0;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>