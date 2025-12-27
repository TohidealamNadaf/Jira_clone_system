<?php

declare(strict_types=1);

\App\Core\View::extends('layouts.app');

\App\Core\View::section('content');

// Budget Dashboard - Overview of all project budgets
// Shows budget status, remaining budget, alerts, and cost trends

$budgets = $budgets ?? [];

// Calculate total budget stats
$totalBudget = array_sum(array_map(fn($b) => $b['total_budget'] ?? 0, $budgets));
$totalCost = array_sum(array_map(fn($b) => $b['total_cost'] ?? 0, $budgets));
$remainingBudget = $totalBudget - $totalCost;
$overallPercent = $totalBudget > 0 ? ($totalCost / $totalBudget) * 100 : 0;

// Identify budgets with alerts
$criticalBudgets = array_filter($budgets, fn($b) => ($b['percentage_used'] ?? 0) > 100);
$warningBudgets = array_filter($budgets, fn($b) => ($b['percentage_used'] ?? 0) >= 80 && ($b['percentage_used'] ?? 0) <= 100);

?>

<style>
    /* ====================================== 
       CSS Variables - Enterprise Design System
       ====================================== */
    :root {
        --jira-blue: #8B1956;
        --jira-blue-dark: #6B0F44;
        --jira-blue-light: #F0DCE5;
        --color-warning: #E77817;
        --text-primary: #161B22;
        --text-secondary: #626F86;
        --bg-primary: #FFFFFF;
        --bg-secondary: #F7F8FA;
        --border-color: #DFE1E6;
        --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
        --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
        --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
        --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        --color-success: #216E4E;
        --color-error: #ED3C32;
        --color-info: #0055CC;
    }

    * {
        box-sizing: border-box;
    }

    /* ====================================== 
       Page Wrapper - Full Screen Layout
       ====================================== */
    .budget-wrapper {
        background-color: var(--bg-secondary);
        min-height: calc(100vh - 80px);
        display: flex;
        flex-direction: column;
        padding: 0;
        margin: 0;
    }

    /* ====================================== 
       Breadcrumb Navigation - Professional
       ====================================== */
    .bd-breadcrumb {
        padding: 12px 40px;
        background-color: var(--bg-primary);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        flex-wrap: wrap;
    }

    .breadcrumb-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        transition: color var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--text-secondary);
        margin: 0 4px;
        font-weight: 400;
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* ====================================== 
       Page Header - Professional Title Section
       ====================================== */
    .bd-header {
        background-color: var(--bg-primary);
        padding: 32px 40px;
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 32px;
    }

    .bd-header-left {
        flex: 1;
    }

    .bd-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        line-height: 1.3;
        letter-spacing: -0.5px;
    }

    .bd-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 8px 0 0 0;
        padding: 0;
        line-height: 1.5;
        font-weight: 400;
    }

    .bd-header-right {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    /* ====================================== 
       Main Content Area
       ====================================== */
    .bd-content {
        flex: 1;
        padding: 24px 40px;
        overflow-y: auto;
    }

    /* ====================================== 
       Buttons - Professional Styling
       ====================================== */
    .btn-bd {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 4px;
        cursor: pointer;
        border: none;
        transition: all var(--transition);
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-primary-bd {
        background-color: var(--jira-blue);
        color: var(--bg-primary);
    }

    .btn-primary-bd:hover {
        background-color: var(--jira-blue-dark);
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .btn-secondary-bd {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary-bd:hover {
        background-color: var(--bg-secondary);
        border-color: var(--jira-blue);
        color: var(--jira-blue);
    }

    /* ====================================== 
       Alert Banner - Status Notifications
       ====================================== */
    .bd-alert-banner {
        background: linear-gradient(135deg, #FFF0F5 0%, #FFE4E1 100%);
        border: 1px solid var(--border-color);
        border-left: 4px solid var(--color-error);
        border-radius: 4px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        box-shadow: var(--shadow-sm);
    }

    .bd-alert-icon {
        font-size: 24px;
        min-width: 40px;
        text-align: center;
        margin-top: 2px;
    }

    .bd-alert-content {
        flex: 1;
    }

    .bd-alert-title {
        margin: 0;
        font-size: 16px;
        color: var(--text-primary);
        font-weight: 600;
    }

    .bd-alert-message {
        margin: 4px 0 0 0;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .bd-alert-list {
        margin: 8px 0 0 0;
        padding-left: 20px;
        font-size: 13px;
        color: var(--text-secondary);
    }

    /* ====================================== 
       Metrics Grid - Statistics Cards
       ====================================== */
    .bd-metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .bd-metric-card {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 20px 24px;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .bd-metric-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
        border-color: var(--jira-blue);
    }

    .bd-metric-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .bd-metric-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        font-family: 'Monaco', 'Menlo', monospace;
    }

    .bd-metric-subtext {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 0;
    }

    .bd-metric-subtext.success {
        color: var(--color-success);
    }

    .bd-metric-subtext.error {
        color: var(--color-error);
    }

    /* ====================================== 
       Budget Cards Grid
       ====================================== */
    .bd-budget-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .bd-budget-card {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: all var(--transition);
    }

    .bd-budget-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
        border-color: var(--jira-blue);
    }

    .bd-budget-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        background-color: var(--bg-secondary);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .bd-budget-card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .bd-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .bd-status-ok {
        background-color: #D4EDDA;
        color: #155724;
    }

    .bd-status-warning {
        background-color: #FFF3CD;
        color: #856404;
    }

    .bd-status-critical {
        background-color: #F8D7DA;
        color: #721C24;
    }

    .bd-budget-card-body {
        padding: 20px 24px;
    }

    .bd-budget-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 16px;
    }

    .bd-budget-col {
        flex: 1;
    }

    .bd-budget-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 4px 0;
    }

    .bd-budget-amount {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        font-family: 'Monaco', 'Menlo', monospace;
    }

    .bd-progress-section {
        margin: 20px 0;
    }

    .bd-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .bd-progress-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .bd-progress-percent {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .bd-progress-bar {
        width: 100%;
        height: 8px;
        background-color: var(--bg-secondary);
        border-radius: 4px;
        overflow: hidden;
    }

    .bd-progress-fill {
        height: 100%;
        transition: width 0.3s ease;
        border-radius: 4px;
    }

    .bd-progress-ok {
        background-color: var(--color-success);
    }

    .bd-progress-warning {
        background-color: var(--color-warning);
    }

    .bd-progress-critical {
        background-color: var(--color-error);
    }

    .bd-budget-footer {
        padding: 16px 20px;
        border-top: 1px solid var(--border-color);
        background-color: var(--bg-secondary);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .bd-remaining-amount {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .bd-remaining-amount.success {
        color: var(--color-success);
    }

    .bd-remaining-amount.error {
        color: var(--color-error);
    }

    /* ====================================== 
       Empty State
       ====================================== */
    .bd-empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--text-secondary);
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
    }

    .bd-empty-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    .bd-empty-text {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 8px;
        color: var(--text-primary);
    }

    .bd-empty-hint {
        font-size: 13px;
        color: var(--text-secondary);
    }

    .bd-empty-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 600;
    }

    .bd-empty-link:hover {
        text-decoration: underline;
    }

    /* ====================================== 
       Help Section
       ====================================== */
    .bd-help-section {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 24px;
        box-shadow: var(--shadow-sm);
        margin-top: 32px;
    }

    .bd-help-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 16px 0;
    }

    .bd-help-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 12px;
    }

    .bd-help-list li {
        padding: 12px;
        background-color: var(--bg-secondary);
        border-left: 4px solid var(--jira-blue);
        border-radius: 2px;
        font-size: 13px;
        line-height: 1.6;
        color: var(--text-primary);
    }

    .bd-help-list strong {
        font-weight: 600;
        color: var(--text-primary);
    }

    /* ====================================== 
       Responsive Design
       ====================================== */
    @media (max-width: 1024px) {
        .bd-metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .bd-budget-grid {
            grid-template-columns: 1fr;
        }

        .bd-header {
            flex-direction: column;
            gap: 16px;
        }

        .bd-header-right {
            justify-content: flex-start;
        }
    }

    @media (max-width: 768px) {
        .bd-header,
        .bd-breadcrumb {
            padding: 16px 20px;
        }

        .bd-content {
            padding: 16px 20px;
        }

        .bd-title {
            font-size: 24px;
        }

        .bd-metrics-grid {
            grid-template-columns: 1fr;
        }

        .bd-budget-grid {
            grid-template-columns: 1fr;
        }

        .bd-budget-row {
            flex-direction: column;
            gap: 12px;
        }

        .bd-budget-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 480px) {
        .bd-metrics-grid {
            gap: 12px;
        }

        .bd-metric-card {
            padding: 16px;
        }

        .bd-metric-value {
            font-size: 22px;
        }

        .bd-header {
            padding: 16px;
        }

        .bd-content {
            padding: 12px 16px;
        }

        .bd-title {
            font-size: 20px;
        }

        .bd-subtitle {
            font-size: 13px;
        }

        .bd-budget-card-body {
            padding: 16px;
        }

        .bd-budget-card-header {
            padding: 12px 16px;
        }
    }
</style>

<div class="budget-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="bd-breadcrumb">
        <a href="<?= url('/dashboard') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/time-tracking') ?>" class="breadcrumb-link">
            <i class="bi bi-hourglass-split"></i> Time Tracking
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Budget Dashboard</span>
    </div>

    <!-- Page Header -->
    <div class="bd-header">
        <div class="bd-header-left">
            <h1 class="bd-title">💼 Project Budget Dashboard</h1>
            <p class="bd-subtitle">Monitor and manage project budgets across all active projects</p>
        </div>
        <div class="bd-header-right">
            <a href="<?= url('/time-tracking') ?>" class="btn-bd btn-secondary-bd">
                <i class="bi bi-hourglass-split"></i> Back to Tracking
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bd-content">
        <!-- Alert Banner for Critical Budgets -->
        <?php if (!empty($criticalBudgets)): ?>
        <div class="bd-alert-banner">
            <div class="bd-alert-icon">⚠️</div>
            <div class="bd-alert-content">
                <h4 class="bd-alert-title">Budget Alert: <?= count($criticalBudgets) ?> project(s) exceeded</h4>
                <p class="bd-alert-message">
                    The following projects have exceeded their budget allocation. Immediate action may be required.
                </p>
                <ul class="bd-alert-list">
                    <?php foreach (array_slice($criticalBudgets, 0, 3) as $budget): ?>
                    <li>
                        <strong><?= htmlspecialchars($budget['project_name']) ?></strong> 
                        - <?= number_format($budget['percentage_used'] ?? 0, 1) ?>% used
                    </li>
                    <?php endforeach; ?>
                    <?php if (count($criticalBudgets) > 3): ?>
                    <li>... and <?= count($criticalBudgets) - 3 ?> more</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Metrics Grid - Overall Budget Stats -->
        <div class="bd-metrics-grid">
            <!-- Total Budget -->
            <div class="bd-metric-card">
                <p class="bd-metric-label">Total Budget</p>
                <p class="bd-metric-value">$<?= number_format($totalBudget, 0) ?></p>
                <p class="bd-metric-subtext">Across all projects</p>
            </div>

            <!-- Total Cost -->
            <div class="bd-metric-card">
                <p class="bd-metric-label">Total Cost</p>
                <p class="bd-metric-value">$<?= number_format($totalCost, 0) ?></p>
                <p class="bd-metric-subtext">Time tracked and logged</p>
            </div>

            <!-- Remaining Budget -->
            <div class="bd-metric-card">
                <p class="bd-metric-label">Remaining Budget</p>
                <p class="bd-metric-value <?= $remainingBudget >= 0 ? 'success' : 'error' ?>">
                    $<?= number_format($remainingBudget, 0) ?>
                </p>
                <p class="bd-metric-subtext <?= $remainingBudget >= 0 ? 'success' : 'error' ?>">
                    <?= $remainingBudget >= 0 ? '✓ Healthy' : '✗ Exceeded' ?>
                </p>
            </div>

            <!-- Overall Usage -->
            <div class="bd-metric-card">
                <p class="bd-metric-label">Overall Usage</p>
                <p class="bd-metric-value"><?= number_format(min($overallPercent, 999), 1) ?>%</p>
                <p class="bd-metric-subtext">
                    <?php 
                    if ($overallPercent > 100) {
                        echo '🔴 Exceeded';
                    } elseif ($overallPercent >= 80) {
                        echo '🟡 Critical';
                    } else {
                        echo '🟢 Healthy';
                    }
                    ?>
                </p>
            </div>
        </div>

        <!-- Budget Cards Grid -->
        <?php if (empty($budgets)): ?>
        <div class="bd-empty-state">
            <div class="bd-empty-icon">📭</div>
            <p class="bd-empty-text">No Project Budgets Configured</p>
            <p class="bd-empty-hint">
                Start tracking time and configure project budgets to see cost analysis here.
                <a href="<?= url('/time-tracking') ?>" class="bd-empty-link">View Time Tracking →</a>
            </p>
        </div>
        <?php else: ?>
        <div class="bd-budget-grid">
            <?php 
            // Sort by usage percentage (descending) to show most critical first
            usort($budgets, fn($a, $b) => ($b['percentage_used'] ?? 0) <=> ($a['percentage_used'] ?? 0));
            foreach ($budgets as $budget): 
                $percent = $budget['percentage_used'] ?? 0;
                $remaining = ($budget['total_budget'] ?? 0) - ($budget['total_cost'] ?? 0);
                
                // Determine status
                if ($percent > 100) {
                    $statusClass = 'bd-status-critical';
                    $statusText = 'Exceeded';
                    $statusIcon = '🔴';
                } elseif ($percent >= 80) {
                    $statusClass = 'bd-status-warning';
                    $statusText = 'Critical';
                    $statusIcon = '🟡';
                } else {
                    $statusClass = 'bd-status-ok';
                    $statusText = 'Healthy';
                    $statusIcon = '🟢';
                }
                
                // Determine progress bar color
                if ($percent > 100) {
                    $progressClass = 'bd-progress-critical';
                } elseif ($percent >= 80) {
                    $progressClass = 'bd-progress-warning';
                } else {
                    $progressClass = 'bd-progress-ok';
                }
            ?>
            <div class="bd-budget-card">
                <div class="bd-budget-card-header">
                    <h4 class="bd-budget-card-title">
                        <?= htmlspecialchars($budget['project_name']) ?>
                    </h4>
                    <span class="bd-status-badge <?= $statusClass ?>">
                        <?= $statusIcon ?> <?= $statusText ?>
                    </span>
                </div>

                <div class="bd-budget-card-body">
                    <!-- Budget Row -->
                    <div class="bd-budget-row">
                        <div class="bd-budget-col">
                            <p class="bd-budget-label">Total Budget</p>
                            <p class="bd-budget-amount">
                                $<?= number_format($budget['total_budget'] ?? 0, 0) ?>
                            </p>
                        </div>
                        <div class="bd-budget-col">
                            <p class="bd-budget-label">Total Cost</p>
                            <p class="bd-budget-amount">
                                $<?= number_format($budget['total_cost'] ?? 0, 0) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Progress Section -->
                    <div class="bd-progress-section">
                        <div class="bd-progress-header">
                            <p class="bd-progress-label">Budget Usage</p>
                            <p class="bd-progress-percent"><?= number_format($percent, 1) ?>%</p>
                        </div>
                        <div class="bd-progress-bar">
                            <div class="bd-progress-fill <?= $progressClass ?>" 
                                 style="width: <?= min($percent, 100) ?>%"></div>
                        </div>
                    </div>

                    <!-- Remaining Budget Row -->
                    <div class="bd-budget-row">
                        <div class="bd-budget-col">
                            <p class="bd-budget-label">Remaining</p>
                            <p class="bd-budget-amount <?= $remaining >= 0 ? 'success' : 'error' ?>">
                                $<?= number_format($remaining, 0) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bd-budget-footer">
                    <span class="bd-remaining-amount <?= $remaining >= 0 ? 'success' : 'error' ?>">
                        <?= $remaining >= 0 ? '✓' : '✗' ?> 
                        <?= number_format(abs($percent - 100), 1) ?>% 
                        <?= $remaining >= 0 ? 'available' : 'over budget' ?>
                    </span>
                    <a href="<?= url('/time-tracking/project/' . ($budget['project_id'] ?? '#')) ?>" 
                       class="btn-bd btn-secondary-bd">
                        📊 View Report
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Help Section -->
        <div class="bd-help-section">
            <h4 class="bd-help-title">💡 Budget Status Guide</h4>
            <ul class="bd-help-list">
                <li><strong>🟢 Healthy (0-79%)</strong> - Budget is well within limits, no action needed</li>
                <li><strong>🟡 Critical (80-99%)</strong> - Budget approaching limit, monitor closely and consider adjustments</li>
                <li><strong>🔴 Exceeded (100%+)</strong> - Budget has been exceeded, immediate review and corrective action required</li>
                <li>Click <strong>"View Report"</strong> on any project to see detailed time tracking and cost breakdown</li>
                <li>Adjust team rates, reduce scope, or increase budget allocation to manage project costs</li>
            </ul>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
