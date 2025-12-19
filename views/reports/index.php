<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="reports-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="<?= url('/dashboard') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Reports</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Reports</h1>
            <p class="page-subtitle">Analyze your team's progress and performance across projects</p>
        </div>
        <div class="header-right">
            <div class="filter-group">
                <label class="filter-label">Project</label>
                <select class="filter-select" id="projectFilter">
                    <option value="">All Projects</option>
                    <?php foreach ($projects ?? [] as $proj): ?>
                    <option value="<?= $proj['id'] ?>" <?= ($selectedProject ?? 0) == $proj['id'] ? 'selected' : '' ?>>
                        <?= e($proj['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="content-area">
        <!-- Quick Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Issues</div>
                <div class="stat-value" id="totalIssues"><?= e($stats['total_issues'] ?? 0) ?></div>
                <div class="stat-icon">📋</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Completed</div>
                <div class="stat-value stat-value-success" id="completedIssues"><?= e($stats['completed_issues'] ?? 0) ?></div>
                <div class="stat-icon">✓</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">In Progress</div>
                <div class="stat-value stat-value-warning" id="inProgress"><?= e($stats['in_progress'] ?? 0) ?></div>
                <div class="stat-icon">⚙️</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Avg. Velocity</div>
                <div class="stat-value stat-value-primary" id="avgVelocity"><?= e($stats['avg_velocity'] ?? 0) ?></div>
                <div class="stat-icon">📈</div>
            </div>
        </div>

        <!-- Report Categories Grid -->
        <div class="reports-grid">
            <!-- Agile Reports -->
            <div class="report-category">
                <div class="category-header">
                    <h3 class="category-title">⚡ Agile Reports</h3>
                    <p class="category-description">Sprint and velocity metrics</p>
                </div>
                <div class="report-list">
                    <?php if (!empty($activeSprints)): ?>
                    <a href="<?= url('/reports/burndown/' . $activeSprints[0]['id']) ?>" class="report-item">
                        <div class="report-item-icon">📊</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Burndown Chart</h4>
                            <p class="report-item-desc">Track remaining work in a sprint</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>
                    <?php else: ?>
                    <div class="report-item report-item-disabled">
                        <div class="report-item-icon">📊</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Burndown Chart</h4>
                            <p class="report-item-desc">No active sprints</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($boards)): ?>
                    <a href="<?= url('/reports/velocity/' . $boards[0]['id']) ?>" class="report-item">
                        <div class="report-item-icon">📈</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Velocity Chart</h4>
                            <p class="report-item-desc">Measure team velocity over sprints</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>
                    <?php else: ?>
                    <div class="report-item report-item-disabled">
                        <div class="report-item-icon">📈</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Velocity Chart</h4>
                            <p class="report-item-desc">No boards available</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <a href="<?= url('/reports/sprint') ?>" class="report-item">
                        <div class="report-item-icon">🎯</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Sprint Report</h4>
                            <p class="report-item-desc">Sprint completion and scope change</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>

                    <a href="<?= url('/reports/cumulative-flow') ?>" class="report-item">
                        <div class="report-item-icon">📐</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Cumulative Flow Diagram</h4>
                            <p class="report-item-desc">Visualize work in progress over time</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>
                </div>
            </div>

            <!-- Issue Reports -->
            <div class="report-category">
                <div class="category-header">
                    <h3 class="category-title">🔍 Issue Reports</h3>
                    <p class="category-description">Issue trends and analysis</p>
                </div>
                <div class="report-list">
                    <a href="<?= url('/reports/created-vs-resolved') ?>" class="report-item">
                        <div class="report-item-icon">↔️</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Created vs Resolved</h4>
                            <p class="report-item-desc">Compare issue creation and resolution rates</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>

                    <a href="<?= url('/reports/resolution-time') ?>" class="report-item">
                        <div class="report-item-icon">⏱️</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Resolution Time</h4>
                            <p class="report-item-desc">Average time to resolve issues</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>

                    <a href="<?= url('/reports/workload') ?>" class="report-item">
                        <div class="report-item-icon">👥</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Workload Distribution</h4>
                            <p class="report-item-desc">Issues assigned per team member</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>

                    <a href="<?= url('/reports/priority-breakdown') ?>" class="report-item">
                        <div class="report-item-icon">⚠️</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Priority Breakdown</h4>
                            <p class="report-item-desc">Issues by priority level</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>
                </div>
            </div>

            <!-- Time Tracking Reports -->
            <div class="report-category">
                <div class="category-header">
                    <h3 class="category-title">⏰ Time Tracking</h3>
                    <p class="category-description">Time tracking and estimates</p>
                </div>
                <div class="report-list">
                    <a href="<?= url('/reports/time-logged') ?>" class="report-item">
                        <div class="report-item-icon">⏲️</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Time Logged</h4>
                            <p class="report-item-desc">Total time logged by team</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>

                    <a href="<?= url('/reports/time-estimate-accuracy') ?>" class="report-item">
                        <div class="report-item-icon">🎯</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Estimate Accuracy</h4>
                            <p class="report-item-desc">Compare estimates vs actual time</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>
                </div>
            </div>

            <!-- Version Reports -->
            <div class="report-category">
                <div class="category-header">
                    <h3 class="category-title">🏷️ Version Reports</h3>
                    <p class="category-description">Release planning and tracking</p>
                </div>
                <div class="report-list">
                    <a href="<?= url('/reports/version-progress') ?>" class="report-item">
                        <div class="report-item-icon">📊</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Version Progress</h4>
                            <p class="report-item-desc">Track progress toward releases</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>

                    <a href="<?= url('/reports/release-burndown') ?>" class="report-item">
                        <div class="report-item-icon">📉</div>
                        <div class="report-item-content">
                            <h4 class="report-item-title">Release Burndown</h4>
                            <p class="report-item-desc">Work remaining for a release</p>
                        </div>
                        <i class="bi bi-chevron-right report-item-arrow"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<style>
:root {
    /* Plum Theme - Enterprise Design System */
    --jira-blue: #8B1956;
    --jira-blue-dark: #6F123F;
    --jira-blue-light: rgba(139, 25, 86, 0.08);
    --jira-blue-lighter: #f0dce5;
    --text-primary: #161B22;
    --text-secondary: #626F86;
    --text-tertiary: #97A0AF;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F8FA;
    --border-color: #DFE1E6;
    --color-success: #216E4E;
    --color-warning: #E77817;
    --color-info: #216E4E;
    --color-error: #ED3C32;
    --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
    --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --shadow-xl: 0 10px 40px rgba(0, 0, 0, 0.12);
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --radius-sm: 4px;
    --radius-md: 6px;
    --radius-lg: 8px;
}

/* Page Wrapper */
.reports-page-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: var(--bg-secondary);
}

/* Breadcrumb Navigation */
/* Breadcrumb Navigation */
.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 32px;
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    font-size: 13px;
    color: var(--text-secondary);
    box-shadow: var(--shadow-sm);
}

.breadcrumb-link {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--jira-blue);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition);
    cursor: pointer;
}

.breadcrumb-link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

.breadcrumb-link i {
    font-size: 14px;
}

.breadcrumb-separator {
    color: var(--border-color);
    font-weight: 400;
}

.breadcrumb-current {
    color: var(--text-secondary);
    font-weight: 600;
}

/* Page Header - Professional Enterprise Design */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    padding: 24px 32px;
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    flex-wrap: wrap;
    position: relative;
}

.page-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--jira-blue) 0%, transparent 100%);
    opacity: 0;
    transition: opacity var(--transition);
}

.header-left {
    flex: 1;
    min-width: 300px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 8px 0;
    line-height: 1.2;
    letter-spacing: -0.3px;
}

.page-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
    line-height: 1.5;
    font-weight: 400;
}

.header-right {
    display: flex;
    align-items: flex-end;
    gap: 16px;
    justify-content: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
}

.filter-select {
    width: 240px;
    height: 36px;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    background-color: var(--bg-primary);
    font-size: 13px;
    color: var(--text-primary);
    cursor: pointer;
    transition: all var(--transition);
    appearance: none;
    background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23626F86%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3e%3cpolyline points=%226 9 12 15 18 9%22%3e%3c/polyline%3e%3c/svg%3e');
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 18px;
    padding-right: 32px;
}

.filter-select:hover {
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
    background-color: var(--jira-blue-lighter);
}

.filter-select:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.15);
    background-color: var(--bg-primary);
}

/* Content Area */
.content-area {
    flex: 1;
    padding: 32px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 1.5rem;
}

.stat-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 20px;
    background-color: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-left: 3px solid var(--jira-blue);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
    transition: all var(--transition);
    overflow: hidden;
    cursor: pointer;
}

.stat-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-3px);
    border-left-color: var(--jira-blue-dark);
}

.stat-card::before {
    position: absolute;
    top: -10px;
    right: -10px;
    font-size: 56px;
    opacity: 0.06;
    pointer-events: none;
    transition: opacity var(--transition);
}

.stat-card:hover::before {
    opacity: 0.12;
}

.stat-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
}

.stat-value-success {
    color: var(--color-success);
}

.stat-value-warning {
    color: var(--color-warning);
}

.stat-value-primary {
    color: var(--jira-blue);
}

.stat-icon {
    position: absolute;
    bottom: -5px;
    right: -5px;
    font-size: 48px;
    opacity: 0.08;
    pointer-events: none;
    transition: opacity var(--transition);
}

.stat-card:hover .stat-icon {
    opacity: 0.15;
}

/* Reports Grid */
.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
}

.report-category {
    background-color: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: all var(--transition-slow);
    position: relative;
}

.report-category::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--jira-blue) 0%, transparent 100%);
    opacity: 0;
    transition: opacity var(--transition);
}

.report-category:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.report-category:hover::before {
    opacity: 1;
}

.category-header {
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
    background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(139, 25, 86, 0.02) 100%);
}

.category-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 6px 0;
    line-height: 1.3;
    letter-spacing: -0.2px;
}

.category-description {
    font-size: 12px;
    color: var(--text-secondary);
    margin: 0;
    line-height: 1.4;
    font-weight: 400;
}

.report-list {
    display: flex;
    flex-direction: column;
    padding: 0;
}

/* Report Item */
.report-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    background-color: var(--bg-primary);
    color: var(--text-primary);
    text-decoration: none;
    transition: all var(--transition);
    cursor: pointer;
    position: relative;
}

.report-item::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, var(--border-color) 50%, transparent 100%);
}

.report-item:last-child {
    border-bottom: none;
}

.report-item:last-child::after {
    display: none;
}

.report-item:hover {
    background-color: var(--jira-blue-light);
    padding-left: 24px;
}

.report-item.report-item-disabled {
    opacity: 0.5;
    cursor: default;
    background-color: var(--bg-primary);
}

.report-item.report-item-disabled:hover {
    background-color: var(--bg-primary);
    padding-left: 20px;
}

.report-item-icon {
    font-size: 24px;
    flex-shrink: 0;
    text-align: center;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition);
}

.report-item:hover .report-item-icon {
    font-size: 28px;
}

.report-item-content {
    flex: 1;
    min-width: 0;
}

.report-item-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 4px 0;
    line-height: 1.3;
    letter-spacing: -0.1px;
}

.report-item-desc {
    font-size: 12px;
    color: var(--text-secondary);
    margin: 0;
    line-height: 1.4;
    font-weight: 400;
}

.report-item:hover .report-item-title {
    color: var(--jira-blue-dark);
}

.report-item-arrow {
    font-size: 16px;
    color: var(--text-secondary);
    flex-shrink: 0;
    transition: all var(--transition);
    transform: translateX(0);
}

.report-item:hover .report-item-arrow {
    color: var(--jira-blue);
    transform: translateX(4px);
}

/* Responsive Design */
/* Responsive Design - Tablet & Below */
@media (max-width: 1024px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }

    .header-right {
        justify-content: flex-start;
        flex-direction: row;
        flex-wrap: wrap;
    }

    .content-area {
        padding: 24px 20px;
    }

    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .reports-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .report-item:hover {
        padding-left: 22px;
    }

    .filter-select {
        width: 100%;
        max-width: 240px;
    }
}

/* Responsive Design - Mobile */
@media (max-width: 768px) {
    .breadcrumb-nav {
        padding: 8px 16px;
        font-size: 12px;
        flex-wrap: wrap;
    }

    .page-header {
        padding: 16px;
        gap: 12px;
    }

    .page-title {
        font-size: 24px;
        margin-bottom: 4px;
    }

    .page-subtitle {
        font-size: 13px;
    }

    .filter-group {
        width: 100%;
    }

    .filter-select {
        width: 100%;
        max-width: 100%;
        height: 40px;
    }

    .content-area {
        padding: 16px;
    }

    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 24px;
    }

    .stat-card {
        padding: 16px;
        border-radius: var(--radius-md);
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-value {
        font-size: 24px;
    }

    .stat-label {
        font-size: 10px;
    }

    .reports-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .report-item {
        padding: 14px 16px;
        min-height: 44px;
    }

    .report-item:hover {
        padding-left: 20px;
        background-color: var(--jira-blue-light);
    }

    .category-header {
        padding: 16px;
    }

    .category-title {
        font-size: 14px;
    }

    .category-description {
        font-size: 11px;
    }

    .report-item-icon {
        font-size: 20px;
        width: 36px;
        height: 36px;
    }

    .report-item:hover .report-item-icon {
        font-size: 22px;
    }

    .report-item-title {
        font-size: 12px;
        font-weight: 600;
    }

    .report-item-desc {
        font-size: 11px;
    }

    .report-item-arrow {
        font-size: 18px;
    }
}

/* Responsive Design - Small Mobile */
@media (max-width: 480px) {
    .page-title {
        font-size: 20px;
        margin-bottom: 4px;
    }

    .page-subtitle {
        font-size: 12px;
    }

    .breadcrumb-nav {
        padding: 8px 12px;
        font-size: 11px;
    }

    .page-header {
        padding: 12px;
    }

    .header-right {
        width: 100%;
    }

    .filter-group {
        width: 100%;
    }

    .filter-select {
        height: 36px;
    }

    .content-area {
        padding: 12px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 20px;
    }

    .stat-card {
        padding: 12px;
        border-radius: var(--radius-sm);
    }

    .stat-value {
        font-size: 20px;
    }

    .stat-label {
        font-size: 9px;
    }

    .report-item {
        gap: 12px;
        padding: 12px;
        min-height: 40px;
    }

    .report-item:hover {
        padding-left: 16px;
    }

    .report-item-icon {
        font-size: 18px;
        width: 32px;
        height: 32px;
        flex-shrink: 0;
    }

    .report-item-arrow {
        font-size: 16px;
    }

    .category-header {
        padding: 12px;
    }

    .category-title {
        font-size: 13px;
        margin-bottom: 2px;
    }

    .category-description {
        font-size: 10px;
    }

    .reports-grid {
        gap: 12px;
    }
}
</style>

<script>
document.getElementById('projectFilter').addEventListener('change', function() {
    const url = new URL(window.location);
    if (this.value) {
        url.searchParams.set('project_id', this.value);
    } else {
        url.searchParams.delete('project_id');
    }
    window.location = url;
});

// Real-time stats update
function updateStats() {
    fetch('<?= url('/reports/stats') ?>')
        .then(response => response.json())
        .then(data => {
            updateElement('totalIssues', data.total_issues);
            updateElement('completedIssues', data.completed_issues);
            updateElement('inProgress', data.in_progress);
            updateElement('avgVelocity', data.avg_velocity);
        })
        .catch(error => console.error('Error fetching stats:', error));
}

function updateElement(id, newValue) {
    const element = document.getElementById(id);
    if (element && element.textContent !== String(newValue)) {
        element.textContent = newValue;
        element.style.transition = 'opacity 0.3s ease';
        element.style.opacity = '0.7';
        setTimeout(() => {
            element.style.opacity = '1';
        }, 100);
    }
}

// Update stats every 5 seconds
setInterval(updateStats, 5000);
</script>
<?php \App\Core\View::endSection(); ?>
